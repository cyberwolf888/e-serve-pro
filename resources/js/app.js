// NFR-08 — initialise KTUI components
import KTCore from '@keenthemes/ktui';
import { KTModal, KTToast } from '@keenthemes/ktui';
KTCore.init();

// NFR-08 — flash notifications as KTUI Toasts on every page
const toastIcon = (variant) => ({
    success: '<i class="ki-filled ki-check-circle"></i>',
    destructive: '<i class="ki-filled ki-cross-circle"></i>',
    error: '<i class="ki-filled ki-cross-circle"></i>',
    warning: '<i class="ki-filled ki-warning"></i>',
    info: '<i class="ki-filled ki-information-2"></i>',
}[variant] ?? '<i class="ki-filled ki-notification"></i>');

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-kt-toast]').forEach((el) => {
        const variant = el.dataset.variant ?? 'primary';
        KTToast.show({
            message: el.dataset.message ?? el.textContent ?? '',
            variant,
            icon: toastIcon(variant),
            duration: Number(el.dataset.duration ?? 4000),
            position: el.dataset.position ?? 'top-end',
        });
        el.remove();
    });
});

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

// FR-GR-15 / M7.9 — native repeatable LKM role and SOP fields.
document.addEventListener('click', (event) => {
    const addRole = event.target.closest('[data-add-role]');
    if (addRole) {
        const roles = document.querySelector('[data-lkm-roles]');
        const template = document.querySelector('[data-lkm-role-template]');
        if (!roles || !template) return;

        const index = Number(roles.dataset.nextIndex ?? 0);
        const fragment = template.content.cloneNode(true);
        fragment.querySelectorAll('*').forEach((element) => {
            for (const attribute of [...element.attributes]) {
                if (attribute.value.includes('__ROLE__')) element.setAttribute(attribute.name, attribute.value.replaceAll('__ROLE__', index));
            }
        });
        roles.append(fragment);
        roles.dataset.nextIndex = index + 1;
        return;
    }

    const addSop = event.target.closest('[data-add-sop]');
    if (addSop) {
        const role = addSop.closest('[data-role-fields]');
        const fragment = role.querySelector('[data-sop-template]').content.cloneNode(true);
        fragment.querySelector('input').name = role.dataset.sopName;
        role.querySelector('[data-sop-items]').append(fragment);
        return;
    }

    const removeSop = event.target.closest('[data-remove-sop]');
    if (removeSop) removeSop.closest('[data-sop-item]').remove();

    const removeRole = event.target.closest('[data-remove-role]');
    if (removeRole) removeRole.closest('[data-role-fields]').remove();
});
