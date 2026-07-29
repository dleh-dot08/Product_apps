<x-app-layout>
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 text-navy fw-bold">Master Kendaraan</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createVehicleModal">
                + Tambah Kendaraan
            </button>
        </div>
        
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        
        <div class="card panel-card shadow-sm border-0 rounded-4">
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Plat Nomor</th>
                            <th>Nama Kendaraan</th>
                            <th>Harga BBM / Liter</th>
                            <th>KM / Liter</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicles as $index => $v)
                        <tr>
                            <td class="ps-4">{{ $index + 1 }}</td>
                            <td class="fw-bold">{{ $v->plate_number }}</td>
                            <td>{{ $v->name }}</td>
                            <td>Rp {{ number_format($v->fuel_price_per_liter, 2, ',', '.') }}</td>
                            <td>{{ number_format($v->km_per_liter, 2, ',', '.') }} KM</td>
                            <td>
                                @if($v->active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editVehicleModal{{ $v->id }}">Edit</button>
                                @if($v->active)
                                <form action="{{ route('vehicles.destroy', $v->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan kendaraan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        
                        <!-- Edit Modal -->
                        <div class="modal fade" id="editVehicleModal{{ $v->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('vehicles.update', $v->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header border-bottom-0">
                                            <h5 class="modal-title fw-bold">Edit Kendaraan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-start bg-light">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-muted small">Plat Nomor</label>
                                                <input type="text" name="plate_number" class="form-control" value="{{ $v->plate_number }}" required style="text-transform: uppercase;">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-muted small">Nama Kendaraan</label>
                                                <input type="text" name="name" class="form-control" value="{{ $v->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-muted small">Harga BBM / Liter (Rp)</label>
                                                <input type="number" step="0.01" min="1" name="fuel_price_per_liter" class="form-control" value="{{ $v->fuel_price_per_liter }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-muted small">KM / Liter</label>
                                                <input type="number" step="0.01" min="0.1" name="km_per_liter" class="form-control" value="{{ $v->km_per_liter }}" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data kendaraan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createVehicleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('vehicles.store') }}" method="POST">
                @csrf
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-bottom-0">
                        <h5 class="modal-title fw-bold">Tambah Kendaraan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body bg-light">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Plat Nomor</label>
                            <input type="text" name="plate_number" class="form-control" required style="text-transform: uppercase;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Nama Kendaraan</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Harga BBM / Liter (Rp)</label>
                            <input type="number" step="0.01" min="1" name="fuel_price_per_liter" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">KM / Liter</label>
                            <input type="number" step="0.01" min="0.1" name="km_per_liter" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
