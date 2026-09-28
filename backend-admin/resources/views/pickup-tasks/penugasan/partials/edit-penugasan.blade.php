{{-- Modal Edit Penugasan: Kelola daftar tugas di dalam penugasan --}}
<div class="modal fade" id="editPenugasanModal" tabindex="-1" aria-labelledby="editPenugasanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header text-white border-bottom-0 py-3 rounded-top-4" style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);">
                <h5 class="modal-title fw-bold" id="editPenugasanModalLabel">
                    <i class="fa-solid fa-list-check me-2"></i>Kelola Daftar Tugas
                </h5>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="formEditPenugasan" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 bg-light">
                    {{-- Info Penugasan --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <div class="row align-items-center g-3">
                                <div class="col-12 col-md-auto mb-2 mb-md-0">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 px-3 py-2 text-center border border-primary">
                                        <div class="small fw-bold text-uppercase opacity-75 mb-1">NO DO</div>
                                        <div class="fw-bold fs-5" id="editPenugasanDO">-</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-secondary small mb-1">Driver Utama</label>
                                    <select class="form-select border-light-subtle rounded-3" name="driver_id" id="edit_driver_id" required>
                                        <option value="">Pilih Driver</option>
                                        @foreach($drivers as $driver)
                                            <option value="{{ $driver->id }}">{{ $driver->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-secondary small mb-1">Co Driver</label>
                                    <select class="form-select border-light-subtle rounded-3" name="co_driver_id" id="edit_co_driver_id">
                                        <option value="">Tidak Ada</option>
                                        @foreach($drivers as $driver)
                                            <option value="{{ $driver->id }}">{{ $driver->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-secondary small mb-1">Kendaraan</label>
                                    <select class="form-select border-light-subtle rounded-3" name="vehicle_id" id="edit_vehicle_id" required>
                                        <option value="">Pilih Kendaraan</option>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}">[{{ $vehicle->plate_number }}] {{ $vehicle->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 mt-3 pt-3 border-top">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <div class="form-check form-switch form-check-inline">
                                                <input class="form-check-input fs-5 mt-0" type="checkbox" id="is_out_of_city_edit" name="is_out_of_city" value="1" onchange="toggleEtaFieldEdit()">
                                                <label class="form-check-label fw-bold text-secondary ms-2" for="is_out_of_city_edit">Penugasan Luar Kota</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4" id="eta_container_edit" style="display: none;">
                                            <div class="input-group">
                                                <span class="input-group-text bg-white text-muted border-light-subtle"><i class="fa-solid fa-clock"></i></span>
                                                <input type="datetime-local" class="form-control border-light-subtle" name="estimated_arrival" id="estimated_arrival_edit" placeholder="Estimasi Sampai">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Daftar Tugas --}}
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center rounded-top-4">
                            <h6 class="mb-0 fw-bold" style="color: #1e40af;">
                                <i class="fa-solid fa-tasks me-2 text-primary"></i>Pilih Tugas untuk Penugasan Ini
                            </h6>
                            <span class="badge bg-primary rounded-pill px-3 py-2 shadow-sm" id="editSelectedTaskCount">0 Dipilih</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                                <table class="table table-hover align-middle mb-0" id="editTableSelectTasks">
                                    <thead class="table-light position-sticky top-0 shadow-sm" style="z-index: 2;">
                                        <tr>
                                            <th width="50" class="text-center border-bottom-0 py-3">
                                                <input class="form-check-input fs-5 m-0" type="checkbox" id="editCheckAllTasks">
                                            </th>
                                            <th class="border-bottom-0 py-3 text-secondary text-xs fw-bold">Tipe</th>
                                            <th class="border-bottom-0 py-3 text-secondary text-xs fw-bold">No Ref / SO</th>
                                            <th class="border-bottom-0 py-3 text-secondary text-xs fw-bold">Tujuan / Pickup</th>
                                            <th class="border-bottom-0 py-3 text-secondary text-xs fw-bold">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted" id="editLoadingTasksText">
                                                <div class="spinner-border spinner-border-sm me-2 text-primary" role="status"></div>
                                                Memuat daftar tugas...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-white border-top py-3 rounded-bottom-4">
                    <button type="button" class="btn btn-light border rounded-3 px-4 fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-bold shadow-sm" id="btnSaveEditPenugasan" disabled>
                        <i class="fa-solid fa-save me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        const editForm = document.getElementById('formEditPenugasan');
        const editCheckAll = document.getElementById('editCheckAllTasks');
        const editTable = document.getElementById('editTableSelectTasks');

        function editUpdateSelectedCount() {
            let count = document.querySelectorAll('#editTableSelectTasks .edit-task-checkbox:checked, #editTableSelectTasks input[type="hidden"][name="selected_tasks[]"]').length;
            document.getElementById('editSelectedTaskCount').innerText = count + ' Dipilih';
            document.getElementById('btnSaveEditPenugasan').disabled = false; // Allow saving even if 0 tasks
        }

        window.removeAssignedTask = function(btn) {
            btn.closest('tr').remove();
            editUpdateSelectedCount();
        };

        // Check all
        editCheckAll.addEventListener('change', function() {
            let isChecked = this.checked;
            document.querySelectorAll('#editTableSelectTasks .edit-task-checkbox').forEach(cb => cb.checked = isChecked);
            editUpdateSelectedCount();
        });

        // Individual checkboxes
        editTable.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('edit-task-checkbox')) {
                editUpdateSelectedCount();
                let total = document.querySelectorAll('#editTableSelectTasks .edit-task-checkbox').length;
                let checked = document.querySelectorAll('#editTableSelectTasks .edit-task-checkbox:checked').length;
                editCheckAll.checked = (checked === total && total > 0);
            }
        });

        function toggleEtaFieldEdit() {
            const checkbox = document.getElementById('is_out_of_city_edit');
            const container = document.getElementById('eta_container_edit');
            if (checkbox && container) {
                container.style.display = checkbox.checked ? 'block' : 'none';
            }
        }

        /**
         * Open the edit penugasan modal
         * @param {string} deliveryOrderId - UUID of the delivery order
         * @param {string} noDo - DO number to display
         * @param {string} driverId
         * @param {string} coDriverId
         * @param {string} vehicleId
         * @param {string} isOutOfCity
         * @param {string} estimatedArrival
         */
        window.openEditPenugasanModal = function(deliveryOrderId, noDo, driverId, coDriverId, vehicleId, isOutOfCity = false, estimatedArrival = '') {
            // Set form action
            editForm.action = '/pickup-tasks/penugasan/' + deliveryOrderId;

            // Display info
            document.getElementById('editPenugasanDO').textContent = noDo;
            
            // Set selects
            if (document.getElementById('edit_driver_id')) document.getElementById('edit_driver_id').value = driverId || '';
            if (document.getElementById('edit_co_driver_id')) document.getElementById('edit_co_driver_id').value = coDriverId || '';
            if (document.getElementById('edit_vehicle_id')) document.getElementById('edit_vehicle_id').value = vehicleId || '';
            
            // Set fields
            document.getElementById('is_out_of_city_edit').checked = (isOutOfCity == true || isOutOfCity == '1');
            toggleEtaFieldEdit();
            if (estimatedArrival) {
                document.getElementById('estimated_arrival_edit').value = estimatedArrival.substring(0, 16);
            } else {
                document.getElementById('estimated_arrival_edit').value = '';
            }

            // Load tasks
            loadDeliveryOrderTasks(deliveryOrderId);

            let modal = new bootstrap.Modal(document.getElementById('editPenugasanModal'));
            modal.show();
        };

        function loadDeliveryOrderTasks(deliveryOrderId) {
            let tbody = editTable.querySelector('tbody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm me-2" role="status" style="color: #1e40af;"></div>
                        Memuat daftar tugas...
                    </td>
                </tr>
            `;
            editCheckAll.checked = false;
            editUpdateSelectedCount();

            fetch('/api/delivery-order-tasks/' + deliveryOrderId)
                .then(r => r.json())
                .then(data => {
                    let allTasks = [];

                    // Assigned tasks first (pre-checked)
                    (data.assigned || []).forEach(task => {
                        task._checked = true;
                        task._group = 'assigned';
                        allTasks.push(task);
                    });

                    // Unassigned tasks (unchecked)
                    (data.unassigned || []).forEach(task => {
                        task._checked = false;
                        task._group = 'unassigned';
                        allTasks.push(task);
                    });

                    if (allTasks.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Tidak ada tugas tersedia.</td>
                            </tr>
                        `;
                        editUpdateSelectedCount();
                        return;
                    }

                    let html = '';

                    // Group header: Tugas Saat Ini
                    let assignedCount = allTasks.filter(t => t._group === 'assigned').length;
                    let unassignedCount = allTasks.filter(t => t._group === 'unassigned').length;

                    if (assignedCount > 0) {
                        html += `<tr class="table-primary"><td colspan="5" class="fw-bold small py-2 ps-3" style="color: #1e40af;"><i class="fa-solid fa-check-circle me-2"></i>Tugas Saat Ini di Penugasan (${assignedCount})</td></tr>`;
                    }

                    allTasks.filter(t => t._group === 'assigned').forEach(task => {
                        html += buildTaskRow(task);
                    });

                    if (unassignedCount > 0) {
                        html += `<tr class="table-warning"><td colspan="5" class="fw-bold small py-2 ps-3" style="color: #92400e;"><i class="fa-solid fa-plus-circle me-2"></i>Tugas Tersedia (Belum Di-assign) (${unassignedCount})</td></tr>`;
                    }

                    allTasks.filter(t => t._group === 'unassigned').forEach(task => {
                        html += buildTaskRow(task);
                    });

                    tbody.innerHTML = html;
                    editUpdateSelectedCount();

                    // Update check-all state
                    let total = document.querySelectorAll('#editTableSelectTasks .edit-task-checkbox').length;
                    let checked = document.querySelectorAll('#editTableSelectTasks .edit-task-checkbox:checked').length;
                    editCheckAll.checked = (checked === total && total > 0);
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

        function buildTaskRow(task) {
            let typeBadge = task.task_type === 'pickup'
                ? `<span class="badge bg-light text-dark border"><i class="fa-solid fa-box-open text-orange"></i> PICKUP</span>`
                : `<span class="badge bg-light text-dark border"><i class="fa-solid fa-truck-fast text-info"></i> DELIVERY</span>`;

            let refNumber = task.task_type === 'pickup'
                ? (task.reference_number || 'N/A')
                : (task.sales_order ? task.sales_order.so_number : 'N/A');

            let targetName = task.task_type === 'pickup'
                ? (task.pickup_name || '-')
                : (task.sales_order ? task.sales_order.customer_name : (task.customer_name || '-'));

            let valId = task.task_type + '_' + task.id;
            let checked = task._checked ? 'checked' : '';

            let statusBadge = task.status || 'draft';

            if (task._group === 'assigned') {
                let removeBtn = '';
                if (['assigned', 'draft'].includes(task.status) || !task.status) {
                    removeBtn = `
                            <button type="button" class="btn btn-sm text-danger p-0 border-0 bg-transparent" onclick="removeAssignedTask(this)" title="Cabut Tugas">
                                <i class="fa-solid fa-xmark fs-5"></i>
                            </button>
                    `;
                }
                
                return `
                    <tr>
                        <td class="text-center align-middle">
                            <input type="hidden" name="selected_tasks[]" value="${valId}">
                            ${removeBtn}
                        </td>
                        <td class="align-middle">${typeBadge}</td>
                        <td class="align-middle"><span class="fw-bold">${refNumber}</span></td>
                        <td class="align-middle">${targetName}</td>
                        <td class="align-middle"><span class="badge bg-secondary">${statusBadge.toUpperCase().replace('_', ' ')}</span></td>
                    </tr>
                `;
            } else {
                return `
                    <tr>
                        <td class="text-center align-middle"><input class="form-check-input edit-task-checkbox" type="checkbox" name="selected_tasks[]" value="${valId}" ${checked}></td>
                        <td class="align-middle">${typeBadge}</td>
                        <td class="align-middle"><span class="fw-bold">${refNumber}</span></td>
                        <td class="align-middle">${targetName}</td>
                        <td class="align-middle"><span class="badge bg-secondary">${statusBadge.toUpperCase().replace('_', ' ')}</span></td>
                    </tr>
                `;
            }
        }
    })();
</script>
@endpush
