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
                        <p class="text-muted">{{ $trip->date->format('d M Y') }}</p>
                        <hr>
                        <strong><i class="fas fa-truck mr-1"></i> Armada</strong>
                        <p class="text-muted">{{ $trip->vehicle->plate_number ?? '-' }} ({{ $trip->vehicle->name ?? '-' }})</p>
                        <hr>
                        <strong><i class="fas fa-user mr-1"></i> Driver</strong>
                        <p class="text-muted">{{ $trip->driver->full_name ?? '-' }}</p>
                        <hr>
                        <strong><i class="fas fa-road mr-1"></i> Jarak Tempuh</strong>
                        <p class="text-muted">{{ number_format($trip->distance_km, 2) }} KM</p>
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
                                    <td class="text-right">Rp {{ number_format($trip->fuel_cost, 2, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Biaya Manpower</td>
                                    <td class="text-right">Rp {{ number_format($trip->manpower_cost, 2, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Biaya Tol</td>
                                    <td class="text-right">Rp {{ number_format($trip->toll_cost, 2, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Biaya Parkir</td>
                                    <td class="text-right">Rp {{ number_format($trip->parking_cost, 2, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Biaya Lainnya</td>
                                    <td class="text-right">Rp {{ number_format($trip->other_cost, 2, ',', '.') }}</td>
                                </tr>
                                <tr class="bg-light">
                                    <th>Total Biaya</th>
                                    <th class="text-right">Rp {{ number_format($trip->total_cost, 2, ',', '.') }}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tabel Prorata HPP -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="card-title">Prorata HPP per Barang</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Nama Barang/PO</th>
                                    <th>Tipe</th>
                                    <th>Qty</th>
                                    <th>Nilai Barang</th>
                                    <th>HPP per Baris</th>
                                    <th>HPP / Qty</th>
                                    <th>% Biaya</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($prorataDetails as $item)
                                <tr>
                                    <td>{{ $item['item_name'] }}</td>
                                    <td>
                                        @if($item['type'] == 'delivery')
                                            <span class="badge badge-info">Kirim</span>
                                        @else
                                            <span class="badge badge-warning">Ambil</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($item['quantity'], 2) }}</td>
                                    <td>Rp {{ number_format($item['goods_value'], 2, ',', '.') }}</td>
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
                                    <td colspan="7" class="text-center">Tidak ada barang dalam ritase ini.</td>
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
