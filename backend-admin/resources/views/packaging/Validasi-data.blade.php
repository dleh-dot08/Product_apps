<div class="modal-header border-bottom-0 pb-0">
    <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-database text-primary me-2"></i>Data Validasi Master</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body pt-3">
    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs pkg-custom-tabs mb-4" id="validasiTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold" id="material-tab" data-bs-toggle="tab" data-bs-target="#material" type="button" role="tab" aria-controls="material" aria-selected="true">
                <i class="fa-solid fa-cubes-stacked me-2 text-warning"></i>Data Material
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="nails-tab" data-bs-toggle="tab" data-bs-target="#nails" type="button" role="tab" aria-controls="nails" aria-selected="false">
                <i class="fa-solid fa-hammer me-2 text-info"></i>Data Fastener / Nails
            </button>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="validasiTabsContent">
        <!-- Tab: Data Material -->
        <div class="tab-pane fade show active" id="material" role="tabpanel" aria-labelledby="material-tab">
            <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                <table class="table table-hover align-middle table-sm border">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Code</th>
                            <th>Component</th>
                            <th>Size (Thk × W × L)</th>
                            <th>Wood Type</th>
                            <th class="text-end">Unit Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($materials ?? [] as $index => $mat)
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-secondary border">{{ $mat->code }}</span></td>
                                <td class="fw-medium">{{ $mat->component }}</td>
                                <td>{{ $mat->thickness }} × {{ $mat->width }} × {{ $mat->length }} mm</td>
                                <td>{{ $mat->material_type }}</td>
                                <td class="text-end fw-bold text-success">Rp {{ number_format($mat->unit_price, 0, ',', '.') }}<span class="text-muted fw-normal ms-1">/ {{ $mat->unit }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Data material tidak tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab: Data Fastener / Nails -->
        <div class="tab-pane fade" id="nails" role="tabpanel" aria-labelledby="nails-tab">
            <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                <table class="table table-hover align-middle table-sm border">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Weight Range (kg)</th>
                            <th>Thickness Range (mm)</th>
                            <th>Size Nails</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nails ?? [] as $index => $nail)
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                                <td>{{ $nail->from }} - {{ $nail->to }} kg</td>
                                <td>{{ $nail->thk_from }} - {{ $nail->thk_to }} mm</td>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary border px-2">{{ $nail->size_nails }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Data fastener/nails tidak tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer bg-light border-top-0 rounded-bottom-4">
    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Tutup</button>
</div>

<style>
    .pkg-custom-tabs .nav-link {
        color: var(--pkg-muted);
        border: none;
        border-bottom: 2px solid transparent;
        padding: 0.75rem 1.25rem;
    }
    .pkg-custom-tabs .nav-link:hover {
        border-color: transparent;
        color: var(--pkg-primary);
    }
    .pkg-custom-tabs .nav-link.active {
        color: var(--pkg-primary);
        background-color: transparent;
        border-color: transparent;
        border-bottom: 2px solid var(--pkg-primary);
    }
</style>
