<x-app-layout>
    <x-slot name="header">
        Profile
    </x-slot>

    <div class="row pt-3">
        <div class="col-md-8 mx-auto">
            
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body p-4">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body p-4">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body p-4">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
