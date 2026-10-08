<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ListTugasExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tasks;

    public function __construct($tasks)
    {
        $this->tasks = $tasks;
    }

    public function collection()
    {
        return collect($this->tasks);
    }

    public function headings(): array
    {
        return [
            'No',
            'Tipe',
            'Nomor SO / PO',
            'DO Easy',
            'Jumlah Barang',
            'Lokasi Awal',
            'Lokasi Tujuan',
            'Driver',
            'Tanggal Penugasan',
            'Status',
        ];
    }

    public function map($task): array
    {
        static $no = 1;
        $itemsCount = 0;
        if ($task->task_type === 'pickup') {
            $itemsCount = $task->items ? $task->items->count() : 0;
            if ($itemsCount === 0 && !empty($task->item_description)) {
                if (\Illuminate\Support\Str::startsWith($task->item_description, '{')) {
                    $json = json_decode($task->item_description, true);
                    $itemsCount = count($json['items'] ?? []);
                }
                if ($itemsCount === 0) $itemsCount = 1;
            }
        } else {
            if ($task->salesOrder) {
                $itemsCount = $task->salesOrder->items ? $task->salesOrder->items->count() : 0;
                if ($itemsCount === 0 && is_array($task->salesOrder->source_data)) {
                    $itemsCount = count($task->salesOrder->source_data['items'] ?? []);
                    if ($itemsCount === 0 && isset($task->salesOrder->source_data['deskripsi_barang'])) {
                        $itemsCount = 1;
                    }
                }
                if ($itemsCount === 0) $itemsCount = 1;
            }
        }

        return [
            $no++,
            strtoupper($task->task_type),
            $task->task_type === 'pickup' ? $task->reference_number : ($task->salesOrder->so_number ?? '-'),
            $task->do_easy ?? '-',
            $itemsCount,
            $task->pickup_name ?? '-',
            $task->task_type === 'pickup' ? ($task->destination_name ?? '-') : ($task->salesOrder->customer_name ?? '-'),
            $task->driver ? ($task->driver->full_name ?? $task->driver->name) : '-',
            $task->assigned_at ? \Carbon\Carbon::parse($task->assigned_at)->format('Y-m-d H:i') : '-',
            str_replace('_', ' ', strtoupper($task->status)),
        ];
    }
}
