/**
 * Perilaku halaman kiosk kunjungan: jam sekolah dan fokus otomatis ke kolom scan.
 */
export function initKiosk() {
    const clock = document.querySelector('[data-kiosk-clock]');
    const date = document.querySelector('[data-kiosk-date]');

    if (clock || date) {
        const timeZone = document.documentElement.dataset.timezone || 'Asia/Jakarta';
        const timeFormatter = new Intl.DateTimeFormat('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
            timeZone,
        });
        const dateFormatter = new Intl.DateTimeFormat('id-ID', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            timeZone,
        });

        const updateClock = () => {
            const now = new Date();

            if (clock) {
                clock.textContent = timeFormatter.format(now);
            }

            if (date) {
                date.textContent = dateFormatter.format(now);
            }
        };

        updateClock();
        window.setInterval(updateClock, 1000);
    }

    const input = document.querySelector('[data-kiosk-input]');

    if (!input) {
        return;
    }

    input.focus();
    input.select();
}
