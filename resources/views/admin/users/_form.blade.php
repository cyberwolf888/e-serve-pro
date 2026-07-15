{{-- admin/users/_form.blade.php — FR-SA-02 / §9 / NFR-08 / M2
     $user optional (null = create mode); $isEdit bool --}}

{{-- Name --}}
<div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
    <label class="kt-form-label max-w-56" for="name">Nama Lengkap</label>
    <div class="grow">
        <input
            id="name"
            name="name"
            type="text"
            class="kt-input w-full @error('name') border-destructive @enderror"
            value="{{ old('name', $user?->name) }}"
            placeholder="Nama lengkap"
            required
        />
        @error('name')
            <p class="text-destructive text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

{{-- Email --}}
<div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
    <label class="kt-form-label max-w-56" for="email">Email</label>
    <div class="grow">
        <input
            id="email"
            name="email"
            type="email"
            class="kt-input w-full @error('email') border-destructive @enderror"
            value="{{ old('email', $user?->email) }}"
            placeholder="email@contoh.com"
            required
        />
        @error('email')
            <p class="text-destructive text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

{{-- Role (create only — immutable after creation) --}}
@unless ($isEdit ?? false)
<div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
    <label class="kt-form-label max-w-56" for="role">Peran</label>
    <div class="grow">
        <select
            id="role"
            name="role"
            class="kt-input w-full @error('role') border-destructive @enderror"
            required
        >
            <option value="">-- Pilih Peran --</option>
            <option value="guru" {{ old('role') === 'guru' ? 'selected' : '' }}>Guru</option>
            <option value="siswa" {{ old('role') === 'siswa' ? 'selected' : '' }}>Siswa</option>
        </select>
        @error('role')
            <p class="text-destructive text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>
@endunless

{{-- Password --}}
<div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
    <label class="kt-form-label max-w-56" for="password">Kata Sandi</label>
    <div class="grow">
        <div class="kt-input @error('password') border-destructive @enderror" data-kt-toggle-password="true">
            <input
                id="password"
                name="password"
                type="password"
                placeholder="{{ ($isEdit ?? false) ? 'Kata sandi baru (opsional)' : 'Minimal 8 karakter' }}"
                {{ ($isEdit ?? false) ? '' : 'required' }}
            />
            <button class="kt-btn kt-btn-sm kt-btn-ghost kt-btn-icon bg-transparent! -me-1.5" data-kt-toggle-password-trigger="true" type="button">
                <span class="kt-toggle-password-active:hidden">
                    <i class="ki-filled ki-eye text-muted-foreground"></i>
                </span>
                <span class="hidden kt-toggle-password-active:block">
                    <i class="ki-filled ki-eye-slash text-muted-foreground"></i>
                </span>
            </button>
        </div>
        @if ($isEdit ?? false)
            <p class="text-xs text-secondary-foreground mt-1">Kosongkan jika tidak diubah</p>
        @endif
        @error('password')
            <p class="text-destructive text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

{{-- Password confirmation --}}
<div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
    <label class="kt-form-label max-w-56" for="password_confirmation">Konfirmasi Kata Sandi</label>
    <div class="grow">
        <div class="kt-input" data-kt-toggle-password="true">
            <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                placeholder="Ulangi kata sandi"
                {{ ($isEdit ?? false) ? '' : 'required' }}
            />
            <button class="kt-btn kt-btn-sm kt-btn-ghost kt-btn-icon bg-transparent! -me-1.5" data-kt-toggle-password-trigger="true" type="button">
                <span class="kt-toggle-password-active:hidden">
                    <i class="ki-filled ki-eye text-muted-foreground"></i>
                </span>
                <span class="hidden kt-toggle-password-active:block">
                    <i class="ki-filled ki-eye-slash text-muted-foreground"></i>
                </span>
            </button>
        </div>
    </div>
</div>
