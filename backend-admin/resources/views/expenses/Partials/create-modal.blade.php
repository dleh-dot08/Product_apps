<!-- Modal Catat Pengeluaran Baru -->
<div class="modal fade" id="createExpenseModal" tabindex="-1" aria-labelledby="createExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-orange-gradient text-white border-0 py-3">
                <h5 class="modal-title fw-bold" id="createExpenseModalLabel"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Catat Pengeluaran Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Pilih Shift (Driver) <span class="text-danger">*</span></label>
                            <select name="shift_id" class="form-select form-select-lg border-light-subtle shadow-sm bg-body-tertiary" required>
                                <option value="">-- Pilih Shift Aktif --</option>
                                @foreach($activeShifts as $shift)
                                    <option value="{{ $shift->id }}">{{ $shift->driver->name ?? 'Unknown' }} - Tgl: {{ $shift->work_date }} - Kendaraan: {{ $shift->vehicle->plate_number ?? '-' }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Kategori Pengeluaran <span class="text-danger">*</span></label>
                            <select name="category" class="form-select form-select-lg border-light-subtle shadow-sm bg-body-tertiary" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="fuel">Bensin / Solar</option>
                                <option value="toll">E-Toll</option>
                                <option value="parking">Parkir / Mel</option>
                                <option value="meal">Uang Makan</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Nominal (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control form-control-lg border-light-subtle shadow-sm bg-body-tertiary" required min="1" placeholder="50000">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Waktu Kejadian</label>
                            <input type="datetime-local" name="occurred_at" class="form-control form-control-lg border-light-subtle shadow-sm bg-body-tertiary" value="{{ date('Y-m-d\TH:i') }}">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Foto Struk / Kuitansi</label>
                            <input type="file" name="receipt" class="form-control form-control-lg border-light-subtle shadow-sm bg-body-tertiary" accept="image/*">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Keterangan / Deskripsi</label>
                            <textarea name="description" class="form-control border-light-subtle shadow-sm bg-body-tertiary" rows="2" placeholder="Isi tol jagorawi, parkir area gudang X..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-white">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border shadow-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-orange rounded-pill px-4 fw-semibold shadow-sm"><i class="fa-solid fa-save me-2"></i>Simpan Pengeluaran</button>
                </div>
            </form>
        </div>
    </div>
</div>
