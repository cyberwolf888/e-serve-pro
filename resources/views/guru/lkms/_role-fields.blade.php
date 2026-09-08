{{-- FR-GR-15 / BR-09 / §9 / NFR-08 / M7.9 --}}
@php
    $field = fn (string $name) => $namePrefix ? $namePrefix.'['.$name.']' : $name;
    $inputId = fn (string $name) => $idPrefix.'_'.$name;
@endphp
<div class="kt-card" data-role-fields data-sop-name="{{ $field('sop_items') }}[]">
    <div class="kt-card-header flex items-center justify-between gap-3">
        <h3 class="kt-card-title text-sm">Peran</h3>
        @if($removable ?? false)
            <button type="button" class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost text-destructive" data-remove-role aria-label="Hapus peran"><i class="ki-filled ki-trash"></i></button>
        @endif
    </div>
    <div class="kt-card-content grid gap-4 p-5">
        <div>
            <label class="kt-form-label" for="{{ $inputId('name') }}">Nama Peran</label>
            <input id="{{ $inputId('name') }}" name="{{ $field('name') }}" type="text" class="kt-input w-full" value="{{ $roleData['name'] ?? '' }}" required />
            @if($message = $errors->first($field('name')))<p class="text-destructive text-xs mt-1">{{ $message }}</p>@endif
        </div>
        <div>
            <label class="kt-form-label" for="{{ $inputId('description') }}">Deskripsi Peran</label>
            <textarea id="{{ $inputId('description') }}" name="{{ $field('description') }}" class="kt-textarea w-full" rows="2" required>{{ $roleData['description'] ?? '' }}</textarea>
            @if($message = $errors->first($field('description')))<p class="text-destructive text-xs mt-1">{{ $message }}</p>@endif
        </div>
        <div>
            <label class="kt-form-label" for="{{ $inputId('instructions') }}">Instruksi</label>
            <textarea id="{{ $inputId('instructions') }}" name="{{ $field('instructions') }}" class="kt-textarea w-full" rows="3" required>{{ $roleData['instructions'] ?? '' }}</textarea>
            @if($message = $errors->first($field('instructions')))<p class="text-destructive text-xs mt-1">{{ $message }}</p>@endif
        </div>
        <div class="grid gap-2">
            <div class="flex items-center justify-between gap-3">
                <span class="kt-form-label">Poin SOP</span>
                <button type="button" class="kt-btn kt-btn-sm kt-btn-outline" data-add-sop><i class="ki-filled ki-plus"></i>Tambah Poin</button>
            </div>
            <div class="grid gap-2" data-sop-items>
                @foreach(($roleData['sop_items'] ?? ['']) as $item)
                    <div class="flex gap-2" data-sop-item>
                        <input name="{{ $field('sop_items') }}[]" type="text" class="kt-input grow" value="{{ $item }}" aria-label="Poin SOP" required />
                        <button type="button" class="kt-btn kt-btn-icon kt-btn-ghost text-destructive" data-remove-sop aria-label="Hapus poin SOP"><i class="ki-filled ki-trash"></i></button>
                    </div>
                @endforeach
            </div>
            @if($message = $errors->first($field('sop_items')))<p class="text-destructive text-xs">{{ $message }}</p>@endif
            <template data-sop-template>
                <div class="flex gap-2" data-sop-item>
                    <input type="text" class="kt-input grow" aria-label="Poin SOP" required />
                    <button type="button" class="kt-btn kt-btn-icon kt-btn-ghost text-destructive" data-remove-sop aria-label="Hapus poin SOP"><i class="ki-filled ki-trash"></i></button>
                </div>
            </template>
        </div>
    </div>
</div>
