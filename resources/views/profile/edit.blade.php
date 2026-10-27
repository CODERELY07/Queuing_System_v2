<x-app-layout title="Profile">
    <x-page-header title="Profile" subtitle="Your name, email and password." />

    @include('include.message')

    <div class="max-w-3xl space-y-6">
        @foreach (['update-profile-information-form', 'update-password-form', 'delete-user-form'] as $section)
            <x-panel class="p-6 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.' . $section)
                </div>
            </x-panel>
        @endforeach
    </div>
</x-app-layout>
