<div>
    <x-input-label for="name" value="Nama *" />
    <x-text-input id="name" name="name" type="text" class="mt-1.5 block w-full"
        :value="old('name', $user->name ?? '')" required autofocus autocomplete="name" />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div>
    <x-input-label for="email" value="Alamat Email *" />
    <x-text-input id="email" name="email" type="email" class="mt-1.5 block w-full"
        :value="old('email', $user->email ?? '')" required autocomplete="email" />
    <x-input-error :messages="$errors->get('email')" class="mt-2" />
</div>

<div>
    <x-input-label for="role" value="Peran *" />
    <select id="role" name="role" required class="input mt-1.5 block w-full">
        <option value="">Pilih peran...</option>
        @foreach ($roles as $role)
            <option value="{{ $role }}" @selected(old('role', $user->role?->value ?? '') === $role)>{{ $role }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('role')" class="mt-2" />
</div>

<div>
    <x-input-label for="password" :value="$user->exists ? 'Password Baru (opsional)' : 'Password *'" />
    <x-text-input id="password" name="password" type="password" class="mt-1.5 block w-full"
        :required="! $user->exists" autocomplete="new-password" />
    @if ($user->exists)
        <p class="hint">Kosongkan jika password tidak ingin diubah.</p>
    @endif
    <x-input-error :messages="$errors->get('password')" class="mt-2" />
</div>