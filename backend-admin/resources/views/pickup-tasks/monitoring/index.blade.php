<x-app-layout>
    @include('pickup-tasks.partials.header', ['activeTab' => 'monitoring'])

<!-- Konten Monitoring Driver -->
<div class="card card-premium table-panel">
    <div class="card-body p-5 text-center">
        <div class="empty-state-icon bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
            <i class="fa-solid fa-map-location-dot fa-2x text-muted"></i>
        </div>
        <h4 class="text-muted">Monitoring Driver</h4>
        <p class="text-secondary">Peta dan status perjalanan driver akan muncul di sini.</p>
    </div>
</div>
</x-app-layout>
