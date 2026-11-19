const DEFAULT_DURATION = 5500;

export function showToast(message, variant = 'info') {
    if (window.Flux) {
        window.Flux.toast({ text: message, variant, duration: DEFAULT_DURATION });

        return;
    }

    document.dispatchEvent(new CustomEvent('toast-show', {
        detail: {
            slots: { text: message },
            dataset: { variant },
            duration: DEFAULT_DURATION,
        },
    }));
}
