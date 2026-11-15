import * as bootstrap from 'bootstrap';

let toastContainer = null;

function getToastContainer() {
    if (toastContainer && document.body.contains(toastContainer)) {
        return toastContainer;
    }

    toastContainer = document.querySelector('[data-toast-container]');

    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.dataset.toastContainer = '';
        toastContainer.setAttribute('aria-live', 'polite');
        toastContainer.setAttribute('aria-atomic', 'true');
        document.body.appendChild(toastContainer);
    }

    return toastContainer;
}

export function showToast(message, variant = 'info') {
    const container = getToastContainer();
    const toastElement = document.createElement('div');
    const isWarning = variant === 'warning';

    toastElement.className = `toast align-items-center border-0 text-bg-${variant}`;
    toastElement.setAttribute('role', 'alert');
    toastElement.setAttribute('aria-live', 'assertive');
    toastElement.setAttribute('aria-atomic', 'true');

    const wrapper = document.createElement('div');
    wrapper.className = 'd-flex';

    const body = document.createElement('div');
    body.className = 'toast-body';
    body.textContent = message;

    const close = document.createElement('button');
    close.type = 'button';
    close.className = `btn-close ${isWarning ? '' : 'btn-close-white'} me-2 m-auto`;
    close.dataset.bsDismiss = 'toast';
    close.setAttribute('aria-label', 'Tutup');

    wrapper.append(body, close);
    toastElement.appendChild(wrapper);
    container.appendChild(toastElement);

    toastElement.addEventListener('hidden.bs.toast', () => toastElement.remove());

    new bootstrap.Toast(toastElement, { autohide: true, delay: 5500 }).show();
}
