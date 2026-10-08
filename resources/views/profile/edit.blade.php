<x-app-layout>
    <x-slot name="header">
        <p class="eyebrow">Akun</p>
        <h1 class="mt-1 font-display text-2xl font-bold text-ink">Profil Saya</h1>
    </x-slot>

    <div class="mx-auto max-w-2xl space-y-6">
        <div class="card p-6 sm:p-8">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="card p-6 sm:p-8">
            @include('profile.partials.update-password-form')
        </div>

        <div class="card p-6 sm:p-8">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
