<!-- Modal Cost Resume -->
<div class="modal fade" id="costResumeModal" tabindex="-1" aria-labelledby="costResumeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 400px;">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            
            <!-- Header -->
            <div class="modal-header border-0 bg-light pb-3 pt-4 px-4 d-flex flex-column align-items-center position-relative">
                <button type="button" class="btn-close position-absolute top-0 end-0 mt-3 me-3 shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                    <span class="material-symbols-rounded fs-2">receipt_long</span>
                </div>
                <h5 class="modal-title fw-bold text-dark mb-1" id="costResumeModalLabel" style="font-size: 18px;">Rincian Biaya Packing</h5>
                <span class="text-muted" style="font-size: 12px;">Estimasi biaya material & jasa</span>
            </div>

            <!-- Body -->
            <div class="modal-body px-4 pt-4 pb-0">
                <div class="d-flex flex-column gap-3">
                    
                    <!-- Item: Rangka -->
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="material-symbols-rounded text-muted" style="font-size: 18px;">view_in_ar</span>
                            <span class="text-secondary fw-semibold" style="font-size: 14px;">Rangka</span>
                        </div>
                        <strong class="text-dark font-monospace" id="cost-rangka" style="font-size: 14px;">Rp {{ number_format($costRangka ?? 0, 0, ',', '.') }}</strong>
                    </div>

                    <!-- Item: Penutup -->
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="material-symbols-rounded text-muted" style="font-size: 18px;">layers</span>
                            <span class="text-secondary fw-semibold" style="font-size: 14px;">Penutup</span>
                        </div>
                        <strong class="text-dark font-monospace" id="cost-penutup" style="font-size: 14px;">Rp {{ number_format($costPenutup ?? 0, 0, ',', '.') }}</strong>
                    </div>

                    <!-- Item: Bawah -->
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="material-symbols-rounded text-muted" style="font-size: 18px;">call_to_action</span>
                            <span class="text-secondary fw-semibold" style="font-size: 14px;">Bawah</span>
                        </div>
                        <strong class="text-dark font-monospace" id="cost-bawah" style="font-size: 14px;">Rp {{ number_format($costBawah ?? 0, 0, ',', '.') }}</strong>
                    </div>

                    <hr class="text-muted opacity-25 my-1 border-dashed">

                    <!-- Item: Paku -->
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="material-symbols-rounded text-muted" style="font-size: 18px;">hardware</span>
                            <span class="text-secondary fw-semibold" style="font-size: 14px;">Biaya Paku</span>
                        </div>
                        <strong class="text-dark font-monospace" id="cost-paku" style="font-size: 14px;">Rp 0</strong>
                    </div>

                    <!-- Item: Manpower -->
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="material-symbols-rounded text-muted" style="font-size: 18px;">engineering</span>
                            <span class="text-secondary fw-semibold" style="font-size: 14px;">Biaya Manpower</span>
                        </div>
                        <strong class="text-dark font-monospace" id="cost-manpower" style="font-size: 14px;">Rp 0</strong>
                    </div>

                </div>
            </div>

            <!-- Footer Banner -->
            <div class="modal-footer border-0 p-4 pt-3 mt-2">
                <div class="w-100 d-flex flex-column align-items-center justify-content-center p-3 rounded-4" style="background: linear-gradient(135deg, #0b2a55 0%, #1e4a87 100%); position: relative; overflow: hidden;">
                    <!-- Pattern overlay for aesthetics -->
                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.1; background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 10px 10px;"></div>
                    
                    <span class="fw-bold tracking-wide text-uppercase mb-1 position-relative" style="font-size: 11px; color: rgba(255,255,255,0.8); letter-spacing: 1px;">Total Biaya Pack</span>
                    <span class="fw-black text-white position-relative" id="cost-total" style="font-size: 26px;">Rp {{ number_format($totalCost ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .border-dashed {
        border-top: 1px dashed #6c757d;
    }
</style>
