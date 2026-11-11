import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((element) => {
        new bootstrap.Tooltip(element);
    });

    document.querySelectorAll('.toast').forEach((element) => {
        new bootstrap.Toast(element, { autohide: true, delay: 5500 }).show();
    });

    document.querySelectorAll('[data-toggle-password]').forEach((button) => {
        button.addEventListener('click', () => {
            const target = document.querySelector(button.dataset.togglePassword);

            if (!target) {
                return;
            }

            const isPassword = target.type === 'password';
            target.type = isPassword ? 'text' : 'password';
            button.setAttribute(
                'aria-label',
                isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi',
            );

            const icon = button.querySelector('i');

            if (icon) {
                icon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
            }
        });
    });

    const confirmModalElement = document.getElementById('confirmModal');
    const confirmModal = confirmModalElement
        ? bootstrap.Modal.getOrCreateInstance(confirmModalElement)
        : null;
    const confirmMessage = confirmModalElement?.querySelector('[data-confirm-message]');
    const confirmAccept = confirmModalElement?.querySelector('[data-confirm-accept]');
    let pendingConfirmForm = null;

    confirmModalElement?.addEventListener('hidden.bs.modal', () => {
        pendingConfirmForm = null;
    });

    confirmAccept?.addEventListener('click', () => {
        const form = pendingConfirmForm;
        pendingConfirmForm = null;
        confirmModal?.hide();

        if (!form) {
            return;
        }

        form.dataset.confirmed = 'true';

        if (typeof form.requestSubmit === 'function') {
            form.requestSubmit();
        } else {
            form.submit();
        }

        delete form.dataset.confirmed;
    });

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.confirmed === 'true') {
                delete form.dataset.confirmed;
                return;
            }

            event.preventDefault();

            if (!confirmModal) {
                if (window.confirm(form.dataset.confirm)) {
                    form.dataset.confirmed = 'true';

                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                    } else {
                        form.submit();
                    }

                    delete form.dataset.confirmed;
                }

                return;
            }

            pendingConfirmForm = form;

            if (confirmMessage) {
                confirmMessage.textContent = form.dataset.confirm;
            }

            confirmModal.show();
        });
    });

    document.querySelectorAll('form[data-loading]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (event.defaultPrevented) {
                return;
            }

            const button = form.querySelector('button[type="submit"]');

            if (!button || button.disabled) {
                return;
            }

            button.disabled = true;
            button.innerHTML = `<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>${button.dataset.loadingText || 'Memproses...'}`;
        });
    });

    document.querySelectorAll('[data-print]').forEach((button) => {
        button.addEventListener('click', () => window.print());
    });

    document.querySelectorAll('[data-close-print]').forEach((button) => {
        button.addEventListener('click', () => {
            window.close();

            if (!window.closed) {
                window.history.back();
            }
        });
    });
});
