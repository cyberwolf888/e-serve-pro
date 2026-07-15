{{-- guru/quizzes/_form.blade.php — FR-GR-09 / §9 / NFR-08 / M5 --}}
<div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
    <label class="kt-form-label max-w-56" for="title">Judul Kuis</label>
    <div class="grow">
        <input id="title" name="title" type="text" class="kt-input w-full @error('title') border-destructive @enderror" value="{{ old('title', $quiz?->title) }}" required />
        @error('title')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>
<div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
    <label class="kt-form-label max-w-56" for="description">Deskripsi</label>
    <div class="grow">
        <textarea id="description" name="description" class="kt-textarea w-full" rows="3">{{ old('description', $quiz?->description) }}</textarea>
        @error('description')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>
<div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
    <label class="kt-form-label max-w-56" for="opens_at">Dibuka Pada</label>
    <div class="grow">
        <input id="opens_at" name="opens_at" type="datetime-local" class="kt-input w-full @error('opens_at') border-destructive @enderror" value="{{ old('opens_at', $quiz?->opens_at?->format('Y-m-d\TH:i')) }}" />
        <p class="text-secondary-foreground text-xs mt-1">Kosongkan agar langsung tersedia setelah dipublikasikan.</p>
        @error('opens_at')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>
<div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
    <label class="kt-form-label max-w-56" for="closes_at">Ditutup Pada</label>
    <div class="grow">
        <input id="closes_at" name="closes_at" type="datetime-local" class="kt-input w-full @error('closes_at') border-destructive @enderror" value="{{ old('closes_at', $quiz?->closes_at?->format('Y-m-d\TH:i')) }}" />
        <p class="text-secondary-foreground text-xs mt-1">Kosongkan agar tidak ada batas waktu.</p>
        @error('closes_at')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>
