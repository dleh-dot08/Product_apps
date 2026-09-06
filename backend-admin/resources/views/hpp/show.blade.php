<x-app-layout>
    <x-slot name="header">
        Detail HPP Ritase
    </x-slot>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Informasi Trip -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Ritase</h3>
                    </div>
                    <div class="card-body">
                        <strong><i class="fas fa-calendar mr-1"></i> Tanggal</strong>
                        <p class="text-muted">{{ $shift->work_date->format('d M Y') }}</p>
                        <hr>
                        <strong><i class="fas fa-truck mr-1"></i> Armada</strong>
                        <p class="text-muted">{{ $shift->vehicle->plate_number ?? '-' }} ({{ $shift->vehicle->name ?? '-' }})</p>
                        <hr>
                        <strong><i class="fas fa-user mr-1"></i> Driver</strong>
                        <p class="text-muted">{{ $shift->driver->full_name ?? $shift->driver->name ?? '-' }}</p>
                        <hr>
                        <strong><i class="fas fa-road mr-1"></i> Jarak Tempuh</strong>
                        <p class="text-muted">
                            @if($shift->start_odometer && $shift->end_odometer)
                                {{ number_format(max(0, $shift->end_odometer - $shift->start_odometer), 2) }} KM
                            @else
                                Belum ada data
                            @endif
                        </p>
                        <hr>
                        <strong><i class="fas fa-clock mr-1"></i> Durasi Kerja</strong>
                        <p class="text-muted">
                            @if($shift->check_in_at && $shift->check_out_at)
                                @php
                                    $durationMinutes = $shift->check_in_at->diffInMinutes($shift->check_out_at);
                                    $hours = floor($durationMinutes / 60);
                                    $minutes = $durationMinutes % 60;
                                @endphp
                                {{ $hours }} Jam {{ $minutes }} Menit
                            @else
                                Belum ada data
                            @endif
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Rincian Biaya Operasional</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td>Biaya BBM</td>
                                    <td class="text-right">Rp {{ number_format($prorataDetails['costs']['fuel'], 2, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Biaya Manpower</td>
                                    <td class="text-right">Rp {{ number_format($prorataDetails['costs']['manpower'], 2, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Biaya Tol</td>
                                    <td class="text-right">Rp {{ number_format($prorataDetails['costs']['toll'], 2, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Biaya Parkir</td>
                                    <td class="text-right">Rp {{ number_format($prorataDetails['costs']['parking'], 2, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Biaya Lainnya</td>
                                    <td class="text-right">Rp {{ number_format($prorataDetails['costs']['other'], 2, ',', '.') }}</td>
                                </tr>
                                <tr class="bg-light">
                                    <th>Total Biaya</th>
                                    <th class="text-right">Rp {{ number_format($prorataDetails['costs']['total'], 2, ',', '.') }}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tabel Prorata HPP -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary" style="display: flex; justify-content: space-between; align-items: center;">
                        <h3 class="card-title" style="margin: 0;">Alokasi HPP per Barang</h3>
                        <div class="card-tools" style="float: right;">
                            <span class="badge" style="background-color: rgba(255,255,255,0.2); color: white;">
                                Skenario: {{ $prorataDetails['is_prorata'] ? 'Prorata Nilai' : 'Bagi Rata (Flat)' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Kode / Deskripsi</th>
                                    <th>Qty</th>
                                    <th>Nilai Barang</th>
                                    <th>HPP per Baris</th>
                                    <th>HPP / Qty</th>
                                    <th>% Biaya</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($prorataDetails['allocations'] as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item['reference_number'] }}</strong><br>
                                        <small>{{ $item['item_description'] }}</small>
                                    </td>
                                    <td>{{ number_format($item['quantity'], 2) }} {{ $item['unit'] }}</td>
                                    <td>Rp {{ number_format($item['line_total'], 2, ',', '.') }}</td>
                                    <td><strong>Rp {{ number_format($item['hpp_per_baris'], 2, ',', '.') }}</strong></td>
                                    <td>Rp {{ number_format($item['hpp_per_qty'], 2, ',', '.') }}</td>
                                    <td>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar bg-success" style="width: {{ $item['percentage'] }}%"></div>
                                        </div>
                                        <small>{{ number_format($item['percentage'], 2) }}%</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada barang dalam ritase ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('hpp.index') }}" class="btn btn-default">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
