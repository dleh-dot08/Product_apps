{{-- Modal Edit Penugasan: Kelola daftar tugas di dalam penugasan --}}
<div class="modal fade" id="editPenugasanModal" tabindex="-1" aria-labelledby="editPenugasanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header text-white border-bottom-0 py-3 rounded-top-4" style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);">
                <h5 class="modal-title fw-bold" id="editPenugasanModalLabel">
                    <i class="fa-solid fa-list-check me-2"></i>Kelola Daftar Tugas
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="formEditPenugasan" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 bg-light">
                    {{-- Info Penugasan --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body py-3">
                            <div class="row align-items-center g-2">
                                <div class="col-auto">
                                    <span class="badge rounded-pill bg-primary px-3 py-2" style="font-size: 14px;" id="editPenugasanDO">-</span>
                                </div>
                                <div class="col">
                                    <div class="fw-bold text-dark" id="editPenugasanDriverInfo" style="font-size: 14px;">-</div>
                                    <div class="text-muted small" id="editPenugasanVehicleInfo">-</div>
                                </div>
                                <div class="col-auto">
                                    <div class="form-check form-switch mt-1">
                                        <input class="form-check-input" type="checkbox" id="is_out_of_city_edit" name="is_out_of_city" value="1" onchange="toggleEtaFieldEdit()">
                                        <label class="form-check-label fw-bold text-secondary small" for="is_out_of_city_edit">Luar Kota</label>
                                    </div>
                                </div>
                                <div class="col-md-3" id="eta_container_edit" style="display: none;">
                                    <label class="form-label fw-bold text-secondary small mb-0">Estimasi Sampai</label>
                                    <input type="datetime-local" class="form-control form-control-sm" name="estimated_arrival" id="estimated_arrival_edit">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Daftar Tugas --}}
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold" style="color: #1e40af;">
                                <i class="fa-solid fa-list-check me-2"></i>Pilih Tugas untuk Penugasan Ini
                            </h6>
                            <span class="badge rounded-pill text-white" id="editSelectedTaskCount" style="background-color: #1e40af;">0 Dipilih</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                                <table class="table table-hover align-middle mb-0" id="editTableSelectTasks">
                                    <thead class="table-light position-sticky top-0" style="z-index: 1;">
                                        <tr>
                                            <th width="40" class="text-center">
                                                <input class="form-check-input" type="checkbox" id="editCheckAllTasks">
                                            </th>
                                            <th>Tipe</th>
                                            <th>No Ref / SO</th>
                                            <th>Tujuan / Pickup</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted" id="editLoadingTasksText">
                                                <div class="spinner-border spinner-border-sm me-2" role="status" style="color: #1e40af;"></div>
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
                    <button type="submit" class="btn px-4 rounded-3 fw-bold text-white" id="btnSaveEditPenugasan" disabled style="background-color: #1e40af; border-color: #1e40af;">
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
            let count = document.querySelectorAll('#editTableSelectTasks .edit-task-checkbox:checked').length;
            document.getElementById('editSelectedTaskCount').innerText = count + ' Dipilih';
            document.getElementById('btnSaveEditPenugasan').disabled = (count === 0);
        }

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
         * @param {string} manifestId - UUID of the manifest
         * @param {string} noDo - DO number to display
         * @param {string} driverName - driver info
         * @param {string} vehicleInfo - vehicle info
         * @param {string} isOutOfCity
         * @param {string} estimatedArrival
         */
        window.openEditPenugasanModal = function(manifestId, noDo, driverName, vehicleInfo, isOutOfCity = false, estimatedArrival = '') {
            // Set form action
            editForm.action = '/pickup-tasks/penugasan/' + manifestId;

            // Display info
            document.getElementById('editPenugasanDO').textContent = noDo;
            document.getElementById('editPenugasanDriverInfo').textContent = driverName;
            document.getElementById('editPenugasanVehicleInfo').textContent = vehicleInfo;
            
            // Set fields
            document.getElementById('is_out_of_city_edit').checked = (isOutOfCity == true || isOutOfCity == '1');
            toggleEtaFieldEdit();
            if (estimatedArrival) {
                document.getElementById('estimated_arrival_edit').value = estimatedArrival.substring(0, 16);
            } else {
                document.getElementById('estimated_arrival_edit').value = '';
            }

            // Load tasks
            loadManifestTasks(manifestId);

            let modal = new bootstrap.Modal(document.getElementById('editPenugasanModal'));
            modal.show();
        };

        function loadManifestTasks(manifestId) {
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

            fetch('/api/manifest-tasks/' + manifestId)
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

            return `
                <tr>
                    <td class="text-center"><input class="form-check-input edit-task-checkbox" type="checkbox" name="selected_tasks[]" value="${valId}" ${checked}></td>
                    <td>${typeBadge}</td>
                    <td><span class="fw-bold">${refNumber}</span></td>
                    <td>${targetName}</td>
                    <td><span class="badge bg-secondary">${statusBadge.toUpperCase().replace('_', ' ')}</span></td>
                </tr>
            `;
        }
    })();
</script>
@endpush
