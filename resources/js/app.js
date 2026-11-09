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

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('form[data-loading]').forEach((form) => {
        form.addEventListener('submit', () => {
            const button = form.querySelector('button[type="submit"]');

            if (!button || button.disabled) {
                return;
            }

            button.disabled = true;
            button.innerHTML = `<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>${button.dataset.loadingText || 'Memproses...'}`;
        });
    });
});
