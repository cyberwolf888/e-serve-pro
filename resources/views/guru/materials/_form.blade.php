{{-- guru/materials/_form.blade.php — FR-GR-04 / FR-GR-05 / FR-SA-03 / BR-04 / §9 / NFR-08 / M4 --}}
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
            <span>Tautan</span>
        </label>
        <label class="kt-form-label flex items-center gap-2.5">
            <input type="radio" name="type" value="file" class="kt-radio" onclick="document.getElementById('file_field').classList.remove('hidden');document.getElementById('figma_field').classList.add('hidden')" {{ old('type', $material?->type) === 'file' ? 'checked' : '' }} />
            <span>Unggah File</span>
        </label>
    </div>
</div>

<div id="figma_field" class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 {{ old('type', $material?->type) === 'file' ? 'hidden' : '' }}">
    <label class="kt-form-label max-w-56" for="figma_url">URL</label>
    <div class="grow">
        <input id="figma_url" name="figma_url" type="url" class="kt-input w-full @error('figma_url') border-destructive @enderror" value="{{ old('figma_url', $material?->figma_url) }}" placeholder="https://..." />
        <div class="kt-alert kt-alert-light kt-alert-primary kt-alert-sm mt-2.5">
            <div class="kt-alert-icon"><i class="ki-filled ki-information-2 text-primary"></i></div>
            <div class="kt-alert-description text-xs">Anda dapat menambahkan tautan dari <span class="font-medium">Figma</span>, <span class="font-medium">Canva</span>, atau <span class="font-medium">YouTube</span>.</div>
        </div>
        @error('figma_url')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>

<div id="file_field" class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5 {{ old('type', $material?->type) === 'file' ? '' : 'hidden' }}">
    <label class="kt-form-label max-w-56" for="file">Berkas PDF</label>
    <div class="grow">
        <input id="file" name="file" type="file" accept=".pdf,.pptx,.docx,application/pdf,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="kt-input w-full @error('file') border-destructive @enderror" />
        <div class="kt-alert kt-alert-light kt-alert-primary kt-alert-sm mt-2.5">
            <div class="kt-alert-icon"><i class="ki-filled ki-information-2 text-primary"></i></div>
            <div class="kt-alert-description text-xs">Maks. 20 MB, format <span class="font-medium">PDF</span>, <span class="font-medium">PPTX</span>, atau <span class="font-medium">DOCX</span>. Kosongkan untuk mempertahankan berkas lama.</div>
        </div>
        @if($material?->file_path)
            <p class="text-secondary-foreground text-xs mt-1">Berkas saat ini: {{ $material->file_size_kb }} KB.</p>
        @endif
        @error('file')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>
