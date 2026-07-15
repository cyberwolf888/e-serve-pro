{{-- guru/quiz-questions/_form.blade.php — FR-GR-09 / DATA-10 / §9 / NFR-08 / M5 --}}
@php
    $optionTexts = old('options', $question?->options->pluck('option_text')->all() ?? ['', '']);
    $correctIndex = old('correct_option', $question ? $question->options->search(fn ($o) => $o->is_correct) : 0);
@endphp
<div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
    <label class="kt-form-label max-w-56" for="question_text">Pertanyaan</label>
    <div class="grow">
        <textarea id="question_text" name="question_text" class="kt-textarea w-full @error('question_text') border-destructive @enderror" rows="3" required>{{ old('question_text', $question?->question_text) }}</textarea>
        @error('question_text')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>

<div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
    <label class="kt-form-label max-w-56">Pilihan Jawaban</label>
    <div class="grow grid gap-2.5">
        @error('options')<p class="text-destructive text-xs">{{ $message }}</p>@enderror
        @error('correct_option')<p class="text-destructive text-xs">{{ $message }}</p>@enderror
        <div id="options-wrap" class="grid gap-2.5">
            @foreach($optionTexts as $i => $text)
                <div class="flex items-center gap-2.5 option-row">
                    <input type="radio" name="correct_option" value="{{ $i }}" class="kt-radio shrink-0" @checked((int) $correctIndex === $i) required />
                    <input type="text" name="options[]" class="kt-input w-full" value="{{ $text }}" placeholder="Pilihan {{ chr(65 + $i) }}" required />
                    <button type="button" class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost remove-option shrink-0" {{ count($optionTexts) <= 2 ? 'disabled' : '' }}>
                        <i class="ki-filled ki-trash"></i>
                    </button>
                </div>
            @endforeach
        </div>
        <button type="button" id="add-option" class="kt-btn kt-btn-sm kt-btn-outline self-start">
            <i class="ki-filled ki-plus me-1"></i>Tambah Pilihan
        </button>
        <p class="text-secondary-foreground text-xs">Pilih radio di samping jawaban yang benar. Minimal 2, maksimal 26 pilihan.</p>
    </div>
</div>

<script>
(function () {
    const wrap = document.getElementById('options-wrap');
    const addBtn = document.getElementById('add-option');

    function refresh() {
        const rows = wrap.querySelectorAll('.option-row');
        rows.forEach((row, i) => {
            row.querySelector('input[type=radio]').value = i;
            row.querySelector('input[type=text]').placeholder = 'Pilihan ' + String.fromCharCode(65 + i);
            row.querySelector('.remove-option').disabled = rows.length <= 2;
        });
    }

    addBtn.addEventListener('click', function () {
        if (wrap.children.length >= 26) return;
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2.5 option-row';
        row.innerHTML = '<input type="radio" name="correct_option" class="kt-radio shrink-0" required />'
            + '<input type="text" name="options[]" class="kt-input w-full" required />'
            + '<button type="button" class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost remove-option shrink-0"><i class="ki-filled ki-trash"></i></button>';
        wrap.appendChild(row);
        refresh();
    });

    wrap.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-option');
        if (btn && wrap.querySelectorAll('.option-row').length > 2) {
            btn.closest('.option-row').remove();
            refresh();
        }
    });
})();
</script>
