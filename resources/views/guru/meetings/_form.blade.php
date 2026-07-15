{{-- guru/meetings/_form.blade.php — FR-GR-06 / §9 / NFR-08 / M4 --}}
<div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
    <label class="kt-form-label max-w-56" for="title">Judul Pertemuan</label>
    <div class="grow">
        <input id="title" name="title" type="text" class="kt-input w-full @error('title') border-destructive @enderror" value="{{ old('title', $meeting?->title) }}" required />
        @error('title')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>
<div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
    <label class="kt-form-label max-w-56" for="scheduled_at">Jadwal</label>
    <div class="grow">
        <input id="scheduled_at" name="scheduled_at" type="datetime-local" class="kt-input w-full @error('scheduled_at') border-destructive @enderror" value="{{ old('scheduled_at', $meeting?->scheduled_at?->format('Y-m-d\TH:i')) }}" required />
        @error('scheduled_at')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>
<div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
    <label class="kt-form-label max-w-56" for="notes">Catatan</label>
    <div class="grow">
        <textarea id="notes" name="notes" class="kt-textarea w-full" rows="4">{{ old('notes', $meeting?->notes) }}</textarea>
        @error('notes')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>
