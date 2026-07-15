{{-- Reusable activate/deactivate confirmation modal — wired via resources/js/app.js --}}
@props(['id' => 'confirm_status_modal'])

<div class="kt-modal" data-kt-modal="true" id="{{ $id }}">
    <div class="kt-modal-content max-w-[440px] top-[15%]">
        <div class="kt-modal-header">
            <h3 class="kt-modal-title">Konfirmasi</h3>
            <button type="button" class="kt-btn kt-btn-icon kt-btn-ghost shrink-0" data-kt-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="kt-modal-body">
            <p id="{{ $id }}_message" class="text-sm text-secondary-foreground"></p>
        </div>
        <div class="flex justify-end gap-2.5 px-5 pb-5">
            <button type="button" class="kt-btn kt-btn-secondary" data-kt-modal-dismiss="true">Batal</button>
            <form id="{{ $id }}_form" method="POST" action="">
                @csrf
                <input type="hidden" name="_method" id="{{ $id }}_method" value="PATCH">
                <button type="submit" id="{{ $id }}_submit" class="kt-btn"></button>
            </form>
        </div>
    </div>
</div>
