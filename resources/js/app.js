import { initKiosk } from './kiosk';
import { initNotifications } from './notifications';
import { initShareButtons } from './share';

document.addEventListener('DOMContentLoaded', () => {
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
            button.querySelector('[data-password-show]')?.classList.toggle('hidden', !isPassword);
            button.querySelector('[data-password-hide]')?.classList.toggle('hidden', isPassword);
        });
    });

    const confirmMessage = document.querySelector('[data-confirm-message]');
    const confirmAccept = document.querySelector('[data-confirm-accept]');
    let pendingConfirmForm = null;

    const submitConfirmed = (form) => {
        form.dataset.confirmed = 'true';

        if (typeof form.requestSubmit === 'function') {
            form.requestSubmit();
        } else {
            form.submit();
        }

        delete form.dataset.confirmed;
    };

    confirmAccept?.addEventListener('click', () => {
        const form = pendingConfirmForm;
        pendingConfirmForm = null;
        window.Flux?.modal('confirm').close();

        if (form) {
            submitConfirmed(form);
        }
    });

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.confirmed === 'true') {
                delete form.dataset.confirmed;
                return;
            }

            if (!window.Flux) {
                if (window.confirm(form.dataset.confirm)) {
                    submitConfirmed(form);
                }

                event.preventDefault();
                return;
            }

            event.preventDefault();
            pendingConfirmForm = form;

            if (confirmMessage) {
                confirmMessage.textContent = form.dataset.confirm;
            }

            window.Flux.modal('confirm').show();
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
            button.innerHTML = `<span class="inline-flex items-center gap-2"><svg class="size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path></svg>${button.dataset.loadingText || 'Memproses...'}</span>`;
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

    document.querySelectorAll('[data-auto-submit]').forEach((field) => {
        field.addEventListener('change', () => {
            const form = field.closest('form');

            if (!form) {
                return;
            }

            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                form.submit();
            }
        });
    });

    const syncScrollableRegions = () => {
        document.querySelectorAll('[data-scrollable-region]').forEach((region) => {
            if (region.scrollWidth > region.clientWidth + 1) {
                region.setAttribute('tabindex', '0');
            } else {
                region.removeAttribute('tabindex');
            }
        });
    };

    syncScrollableRegions();
    window.addEventListener('resize', syncScrollableRegions);
    window.addEventListener('load', syncScrollableRegions);

    if ('ResizeObserver' in window) {
        const scrollRegionObserver = new ResizeObserver(syncScrollableRegions);

        document.querySelectorAll('[data-scrollable-region]').forEach((region) => {
            scrollRegionObserver.observe(region);
        });
    }

    initShareButtons();
    initNotifications();
    initKiosk();
});
