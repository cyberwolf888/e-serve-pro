{{-- guru/materials/_form.blade.php — FR-GR-04 / FR-GR-05 / BR-04 / §9 / NFR-08 / M4 --}}
@if ($errors->any())
    <div class="kt-alert kt-alert-destructive">Periksa kembali data yang diisi.</div>
@endif

<div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
    <label class="kt-form-label max-w-56" for="title">Judul Materi</label>
    <div class="grow">
        <input id="title" name="title" type="text" class="kt-input w-full @error('title') border-destructive @enderror" value="{{ old('title', $material?->title) }}" required />
        @error('title')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>

<div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
    <label class="kt-form-label max-w-56">Jenis Materi</label>
    <div class="grow flex gap-5">
        <label class="kt-form-label flex items-center gap-2.5">
            <input type="radio" name="type" value="figma" class="kt-radio" onclick="document.getElementById('figma_field').classList.remove('hidden');document.getElementById('file_field').classList.add('hidden')" {{ old('type', $material?->type) !== 'file' ? 'checked' : '' }} />
            <span>Tautan Figma</span>
        </label>
        <label class="kt-form-label flex items-center gap-2.5">
            <input type="radio" name="type" value="file" class="kt-radio" onclick="document.getElementById('file_field').classList.remove('hidden');document.getElementById('figma_field').classList.add('hidden')" {{ old('type', $material?->type) === 'file' ? 'checked' : '' }} />
            <span>Unggah PDF</span>
        </label>
    </div>
</div>

<div id="figma_field" class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 {{ old('type', $material?->type) === 'file' ? 'hidden' : '' }}">
    <label class="kt-form-label max-w-56" for="figma_url">URL Figma</label>
    <div class="grow">
        <input id="figma_url" name="figma_url" type="url" class="kt-input w-full @error('figma_url') border-destructive @enderror" value="{{ old('figma_url', $material?->figma_url) }}" placeholder="https://figma.com/..." />
        @error('figma_url')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>

<div id="file_field" class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 {{ old('type', $material?->type) === 'file' ? '' : 'hidden' }}">
    <label class="kt-form-label max-w-56" for="file">Berkas PDF</label>
    <div class="grow">
        <input id="file" name="file" type="file" accept="application/pdf" class="kt-input w-full @error('file') border-destructive @enderror" />
        <p class="text-secondary-foreground text-xs mt-1">Maks. 20 MB, format PDF.</p>
        @if($material?->file_path)
            <p class="text-secondary-foreground text-xs mt-1">Berkas saat ini: {{ $material->file_size_kb }} KB. Kosongkan untuk mempertahankan berkas lama.</p>
        @endif
        @error('file')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>
