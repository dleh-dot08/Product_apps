<div class="modal fade" id="createAssignmentModal" tabindex="-1" aria-labelledby="createAssignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header text-white border-bottom-0 py-3 rounded-top-4" style="background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);">
                <h5 class="modal-title fw-bold" id="createAssignmentModalLabel">
                    <i class="fa-solid fa-clipboard-check me-2"></i>Tambah Penugasan Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('pickup-tasks.penugasan.store') }}" method="POST" id="formCreateAssignment">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <!-- Section: Informasi Penugasan -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold" style="color: #ea580c;"><i class="fa-solid fa-circle-info me-2"></i>Informasi Penugasan</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-secondary small">No Delivery Order</label>
                                    <input type="text" class="form-control bg-light" name="do_number" id="do_number" readonly placeholder="Auto Generate (DO-DDMMYY-XXX)">
                                    <small class="text-muted" style="font-size: 0.7rem;">Dibuat otomatis saat disimpan</small>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-bold text-secondary small mb-0">Tanggal Penugasan <span class="text-danger">*</span></label>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" id="is_out_of_city_create" name="is_out_of_city" value="1" onchange="toggleEtaFieldCreate()" style="cursor: pointer;">
                                            <label class="form-check-label text-secondary small" for="is_out_of_city_create" style="font-size: 0.75rem; cursor: pointer;">Luar Kota</label>
                                        </div>
                                    </div>
                                    <input type="date" class="form-control" name="dispatch_date" id="dispatch_date" required value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-3" id="eta_container_create" style="display: none;">
                                    <label class="form-label fw-bold text-secondary small">Estimasi Sampai</label>
                                    <input type="datetime-local" class="form-control" name="estimated_arrival" id="estimated_arrival_create">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-secondary small">Kendaraan <span class="text-danger">*</span></label>
                                    <select class="form-select select2-assignment" name="vehicle_id" id="vehicle_id" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}">([{{ $vehicle->plate_number }}]) {{ $vehicle->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small">Driver Utama <span class="text-danger">*</span></label>
                                    <select class="form-select select2-assignment" name="driver_id" id="driver_id" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach($drivers as $driver)
                                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small">Co Driver</label>
                                    <select class="form-select select2-assignment" name="co_driver_id" id="co_driver_id">
                                        <option value="">-- Kosong --</option>
                                        @foreach($drivers as $driver)
                                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Pilih List Tugas -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold" style="color: #ea580c;"><i class="fa-solid fa-list-check me-2"></i>Pilih Daftar Tugas (Pickups / Deliveries)</h6>
                            <span class="badge rounded-pill" id="selectedTaskCount" style="background-color: #ea580c;">0 Dipilih</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-hover align-middle mb-0" id="tableSelectTasks">
                                    <thead class="table-light position-sticky top-0" style="z-index: 1;">
                                        <tr>
                                            <th width="40" class="text-center">
                                                <input class="form-check-input" type="checkbox" id="checkAllTasks">
                                            </th>
                                            <th>Tipe</th>
                                            <th>No Ref / SO</th>
                                            <th>Tujuan / Pickup</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Akan diisi melalui AJAX / looping data tugas yang belum di-assign -->
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted" id="loadingTasksText">
                                                <div class="spinner-border spinner-border-sm me-2" role="status" style="color: #ea580c;"></div>
                                                Memuat daftar tugas...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-white border-top-0 py-3 rounded-bottom-4">
                    <button type="button" class="btn btn-light border px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-orange px-4 rounded-3 fw-bold text-white" id="btnSaveAssignment" disabled style="background-color: #f97316; border-color: #f97316;">
                        <i class="fa-solid fa-save me-2"></i>Simpan Penugasan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Inisialisasi Modal dan Select2
    window.openAssignmentModal = function(action) {
        if (action === 'create') {
            $('#formCreateAssignment')[0].reset();
            $('#dispatch_date').val(new Date().toISOString().split('T')[0]);
            
            // Inisialisasi select2
            $('.select2-assignment').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#createAssignmentModal')
            });

            // Load unassigned tasks (Contoh pemanggilan AJAX, bisa disesuaikan nanti)
            loadUnassignedTasks();
            
            var modal = new bootstrap.Modal(document.getElementById('createAssignmentModal'));
            modal.show();
        }
    };

    // Fungsi centang semua
    document.getElementById('checkAllTasks').addEventListener('change', function() {
        let isChecked = this.checked;
        let checkboxes = document.querySelectorAll('.task-checkbox');
        let count = 0;
        
        checkboxes.forEach(function(cb) {
            cb.checked = isChecked;
            if (isChecked) count++;
        });
        
        updateSelectedCount(count);
    });

    function updateSelectedCount(count) {
        document.getElementById('selectedTaskCount').innerText = count + ' Dipilih';
        document.getElementById('btnSaveAssignment').disabled = (count === 0);
    }

    function toggleEtaFieldCreate() {
        const checkbox = document.getElementById('is_out_of_city_create');
        const container = document.getElementById('eta_container_create');
        if (checkbox && container) {
            container.style.display = checkbox.checked ? 'block' : 'none';
        }
    }

    // Event delegation untuk checkbox tugas
    document.getElementById('tableSelectTasks').addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('task-checkbox')) {
            let checkedCount = document.querySelectorAll('.task-checkbox:checked').length;
            updateSelectedCount(checkedCount);
            
            // Update master checkbox
            let totalCount = document.querySelectorAll('.task-checkbox').length;
            document.getElementById('checkAllTasks').checked = (checkedCount === totalCount && totalCount > 0);
        }
    });

    function loadUnassignedTasks() {
        let tbody = document.querySelector('#tableSelectTasks tbody');
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-4 text-muted" id="loadingTasksText">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    Memuat daftar tugas...
                </td>
            </tr>
        `;

        fetch('{{ route("api.unassigned-tasks") }}')
            .then(response => response.json())
            .then(tasks => {
                if (tasks.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada tugas yang belum di-assign.</td>
                        </tr>
                    `;
                    updateSelectedCount(0);
                    return;
                }

                let html = '';
                tasks.forEach(task => {
                    let typeBadge = task.task_type === 'pickup' 
                        ? `<span class="badge bg-light text-dark border"><i class="fa-solid fa-box-open text-orange"></i> PICKUP</span>`
                        : `<span class="badge bg-light text-dark border"><i class="fa-solid fa-truck-fast text-info"></i> DELIVERY</span>`;
                        
                    let refNumber = task.task_type === 'pickup' 
                        ? (task.reference_number || 'N/A')
                        : (task.sales_order ? task.sales_order.so_number : 'N/A');
                        
                    let targetName = task.task_type === 'pickup'
                        ? task.pickup_name
                        : task.customer_name; // From delivery target
                        
                    if (task.task_type === 'delivery' && !targetName && task.sales_order) {
                        targetName = task.sales_order.customer_name;
                    }
                    
                    let valId = task.task_type + '_' + task.id;

                    html += `
                        <tr>
                            <td class="text-center"><input class="form-check-input task-checkbox" type="checkbox" name="selected_tasks[]" value="${valId}"></td>
                            <td>${typeBadge}</td>
                            <td><span class="fw-bold">${refNumber}</span></td>
                            <td>${targetName || '-'}</td>
                            <td><span class="badge bg-secondary">${task.status}</span></td>
                        </tr>
                    `;
                });

                tbody.innerHTML = html;
                updateSelectedCount(0);
                document.getElementById('checkAllTasks').checked = false;
            })
            .catch(err => {
                console.error(err);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-4 text-danger">Gagal memuat tugas. Silakan coba lagi.</td>
                    </tr>
                `;
            });
    }
</script>
@endpush
