<x-app-layout>
    @include('packaging.partials._wizard_form', [
        'mode' => 'edit', 
        'packagingJob' => $packagingJob
    ])
</x-app-layout>
