<?php

namespace App\Services;

use RuntimeException;

class NatsPublisher
{
    public function publish(string $subject, string $payload): void
    {
        $url = (string) config('services.nats.url');
        if ($url === '') {
            throw new RuntimeException('NATS_URL belum dikonfigurasi.');
        }

        $parts = parse_url($url);
        $host = $parts['host'] ?? null;
        $port = $parts['port'] ?? 4222;
        if (! $host) {
            throw new RuntimeException('Format NATS_URL tidak valid.');
        }

        $timeout = (float) config('services.nats.connect_timeout', 5);
        $socket = @stream_socket_client(
            "tcp://{$host}:{$port}",
            $errorCode,
            $errorMessage,
            $timeout,
            STREAM_CLIENT_CONNECT
        );

        if (! $socket) {
            throw new RuntimeException("Gagal terhubung ke NATS: {$errorMessage} ({$errorCode})");
        }

        try {
            stream_set_timeout($socket, (int) config('services.nats.timeout', 10));
            $this->readUntilInfo($socket);
            $connect = json_encode([
                'verbose' => false,
                'pedantic' => false,
                'lang' => 'php',
                'version' => '1.0',
                'protocol' => 1,
                'echo' => true,
            ], JSON_THROW_ON_ERROR);

            $this->write($socket, "CONNECT {$connect}\r\n");
            $this->write($socket, "PUB {$subject} ".strlen($payload)."\r\n{$payload}\r\n");
            $this->write($socket, "PING\r\n");

            $response = $this->readUntilPong($socket);
            if (! str_contains($response, '+PONG') && ! str_contains($response, '+OK')) {
                throw new RuntimeException('NATS tidak mengonfirmasi publish.');
            }
        } finally {
            fclose($socket);
        }
    }

    private function readUntilInfo($socket): string
    {
        $buffer = '';
        while (! str_contains($buffer, "\r\n")) {
            $chunk = fread($socket, 4096);
            if ($chunk === false || $chunk === '') {
                throw new RuntimeException('NATS tidak mengirim INFO.');
            }
            $buffer .= $chunk;
        }

        if (! str_starts_with($buffer, 'INFO ')) {
            throw new RuntimeException('Response awal NATS tidak valid.');
        }

        return $buffer;
    }

    private function readUntilPong($socket): string
    {
        $buffer = '';
        while (! str_contains($buffer, '+PONG')) {
            $chunk = fread($socket, 4096);
            if ($chunk === false || $chunk === '') {
                throw new RuntimeException('NATS tidak mengonfirmasi publish.');
            }
            $buffer .= $chunk;
        }

        return $buffer;
    }

    private function write($socket, string $data): void
    {
        $written = fwrite($socket, $data);
        if ($written === false || $written !== strlen($data)) {
            throw new RuntimeException('Gagal menulis ke koneksi NATS.');
        }
    }
}
