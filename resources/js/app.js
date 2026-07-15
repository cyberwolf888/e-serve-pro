// NFR-08 — initialise KTUI components
import KTCore from '@keenthemes/ktui';
import { KTModal } from '@keenthemes/ktui';
KTCore.init();

// FR-GR-02 / FR-SA-03 — populate the shared activate/deactivate confirm modal
// from the clicked row action's data-* attributes before it opens.
document.querySelectorAll('[id$="_status_modal"][data-kt-modal]').forEach((modal) => {
    modal.addEventListener('show', () => {
        const trigger = KTModal.getInstance(modal)?.getTargetElement();
        if (!trigger) return;

        const { action, method, message, label, variant } = trigger.dataset;
        modal.querySelector(`#${modal.id}_message`).textContent = message ?? '';
        modal.querySelector(`#${modal.id}_form`).action = action ?? '';
        modal.querySelector(`#${modal.id}_method`).value = method ?? 'PATCH';

        const submit = modal.querySelector(`#${modal.id}_submit`);
        submit.textContent = label ?? '';
        submit.classList.toggle('kt-btn-destructive', variant === 'destructive');
        submit.classList.toggle('kt-btn-primary', variant !== 'destructive');
    });
});
