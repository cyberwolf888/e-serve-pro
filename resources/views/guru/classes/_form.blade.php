{{-- guru/classes/_form.blade.php — FR-GR-02 / §9 / NFR-08 / M3 --}}
<div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
    <label class="kt-form-label max-w-56" for="name">Nama Kelas</label>
    <div class="grow">
        <input id="name" name="name" type="text" class="kt-input w-full @error('name') border-destructive @enderror" value="{{ old('name', $class?->name) }}" required />
        @error('name')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>
<div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
    <label class="kt-form-label max-w-56" for="description">Deskripsi</label>
    <div class="grow">
        <textarea id="description" name="description" class="kt-textarea w-full" rows="4">{{ old('description', $class?->description) }}</textarea>
        @error('description')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>
