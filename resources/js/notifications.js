import { showToast } from './toast';

const POLL_INTERVAL = 30000;
const SOUND_STORAGE_KEY = 'perpustakaan.notificationSound';

let audioContext = null;

function soundEnabled() {
    try {
        return window.localStorage.getItem(SOUND_STORAGE_KEY) !== 'off';
    } catch {
        return true;
    }
}

function setSoundEnabled(enabled) {
    try {
        window.localStorage.setItem(SOUND_STORAGE_KEY, enabled ? 'on' : 'off');
    } catch {
        // Penyimpanan lokal dapat diblokir; pengaturan suara tetap berlaku selama halaman terbuka.
    }
}

function ensureAudioContext() {
    if (!window.AudioContext && !window.webkitAudioContext) {
        return;
    }

    if (!audioContext) {
        const AudioContextClass = window.AudioContext || window.webkitAudioContext;
        audioContext = new AudioContextClass();
    }

    if (audioContext.state === 'suspended') {
        audioContext.resume();
    }
}

function playChime() {
    if (!soundEnabled()) {
        return;
    }

    ensureAudioContext();

    if (!audioContext || audioContext.state !== 'running') {
        return;
    }

    const start = audioContext.currentTime;

    [880, 1174.66].forEach((frequency, index) => {
        const oscillator = audioContext.createOscillator();
        const gain = audioContext.createGain();
        const noteStart = start + index * 0.12;

        oscillator.type = 'sine';
        oscillator.frequency.value = frequency;
        gain.gain.setValueAtTime(0.0001, noteStart);
        gain.gain.exponentialRampToValueAtTime(0.12, noteStart + 0.02);
        gain.gain.exponentialRampToValueAtTime(0.0001, noteStart + 0.18);

        oscillator.connect(gain);
        gain.connect(audioContext.destination);
        oscillator.start(noteStart);
        oscillator.stop(noteStart + 0.2);
    });
}

function formatTime(value) {
    if (!value) {
        return '-';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return '-';
    }

    return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}

function createEmpty(message) {
    const element = document.createElement('div');
    element.className = 'notification-empty';
    element.textContent = message;

    return element;
}

function createItem({ icon, title, subtitle, href, variant = 'primary' }) {
    const item = document.createElement('a');
    item.className = 'notification-item';
    item.href = href;

    const iconWrapper = document.createElement('span');
    iconWrapper.className = `notification-icon text-bg-${variant}`;

    const iconElement = document.createElement('i');
    iconElement.className = `bi bi-${icon}`;
    iconElement.setAttribute('aria-hidden', 'true');
    iconWrapper.appendChild(iconElement);

    const content = document.createElement('span');
    content.className = 'notification-content';

    const titleElement = document.createElement('span');
    titleElement.className = 'notification-title';
    titleElement.textContent = title;

    const subtitleElement = document.createElement('span');
    subtitleElement.className = 'notification-subtitle';
    subtitleElement.textContent = subtitle;

    content.append(titleElement, subtitleElement);
    item.append(iconWrapper, content);

    return item;
}

function updateBadge(badge, total) {
    if (!badge) {
        return;
    }

    if (total > 0) {
        badge.textContent = total > 9 ? '9+' : String(total);
        badge.classList.remove('d-none');
    } else {
        badge.textContent = '0';
        badge.classList.add('d-none');
    }

    const trigger = badge.closest('[data-notifications]')?.querySelector('[data-bs-toggle="dropdown"]');

    if (trigger) {
        trigger.setAttribute('aria-label', total > 0 ? `Notifikasi, ${total} item` : 'Notifikasi');
    }
}

function renderAdmin(body, updated, data) {
    body.replaceChildren();

    if (data.pending_requests > 0) {
        body.appendChild(createItem({
            icon: 'inbox',
            title: `${data.pending_requests} permintaan menunggu`,
            subtitle: data.latest_request
                ? `${data.latest_request.borrower} \u00b7 ${data.latest_request.book}`
                : 'Buka antrean untuk memproses permintaan.',
            href: data.latest_request?.url ?? data.requests_url,
            variant: 'warning',
        }));
    }

    if (data.overdue_loans > 0) {
        body.appendChild(createItem({
            icon: 'exclamation-triangle',
            title: `${data.overdue_loans} pinjaman terlambat`,
            subtitle: 'Periksa daftar pinjaman yang melewati jatuh tempo.',
            href: data.overdue_url,
            variant: 'danger',
        }));
    }

    if (body.childElementCount === 0) {
        body.appendChild(createEmpty('Tidak ada notifikasi baru.'));
    }

    updated.textContent = `Diperbarui ${formatTime(data.generated_at)}`;
}

function renderBorrower(body, updated, data) {
    body.replaceChildren();

    if (data.pending_requests > 0) {
        body.appendChild(createItem({
            icon: 'hourglass-split',
            title: `${data.pending_requests} pengajuan menunggu`,
            subtitle: 'Pengajuan sedang diperiksa admin perpustakaan.',
            href: data.requests_url,
            variant: 'warning',
        }));
    }

    if (data.overdue_loans > 0) {
        body.appendChild(createItem({
            icon: 'exclamation-triangle',
            title: `${data.overdue_loans} pinjaman terlambat`,
            subtitle: 'Segera kembalikan buku ke perpustakaan.',
            href: data.loans_url,
            variant: 'danger',
        }));
    }

    if (data.active_loans > 0) {
        const nearest = data.loans[0];

        body.appendChild(createItem({
            icon: 'arrow-left-right',
            title: `${data.active_loans} pinjaman aktif`,
            subtitle: nearest
                ? `${nearest.book} \u00b7 jatuh tempo ${nearest.due_at_label ?? nearest.due_at}`
                : 'Lihat daftar pinjaman aktif Anda.',
            href: data.loans_url,
            variant: 'primary',
        }));
    }

    if (body.childElementCount === 0) {
        body.appendChild(createEmpty('Tidak ada notifikasi baru.'));
    }

    updated.textContent = `Diperbarui ${formatTime(data.generated_at)}`;
}

function statusVariant(label) {
    switch (label) {
        case 'Ditolak':
            return 'danger';
        case 'Disetujui':
        case 'Dipinjam':
        case 'Dikembalikan':
            return 'success';
        case 'Dibatalkan':
        case 'Kedaluwarsa':
            return 'warning';
        default:
            return 'info';
    }
}

function handleBorrowerChanges(state, data) {
    if (!state.initialized) {
        return;
    }

    data.requests.forEach((request) => {
        const previous = state.statuses.get(request.id);

        if (previous && previous.label !== request.status_label) {
            showToast(
                `Status pengajuan "${request.book}" berubah menjadi ${request.status_label}.`,
                statusVariant(request.status_label),
            );
        }
    });
}

function initNotificationRoot(root) {
    const mode = root.dataset.mode;
    const endpoint = root.dataset.endpoint;
    const badge = root.querySelector('[data-notification-badge]');
    const body = root.querySelector('[data-notification-body]');
    const updated = root.querySelector('[data-notification-updated]');
    const soundButton = root.querySelector('[data-notification-sound]');

    if (!endpoint || !body || !updated) {
        return;
    }

    const state = {
        inFlight: false,
        initialized: false,
        latestRequestId: null,
        statuses: new Map(),
    };

    const refreshSoundButton = () => {
        if (!soundButton) {
            return;
        }

        const enabled = soundEnabled();
        soundButton.setAttribute('aria-pressed', enabled ? 'true' : 'false');
        soundButton.setAttribute('aria-label', enabled ? 'Matikan suara notifikasi' : 'Aktifkan suara notifikasi');
        soundButton.innerHTML = `<i class="bi bi-volume-${enabled ? 'up' : 'mute'}" aria-hidden="true"></i>`;
    };

    soundButton?.addEventListener('click', () => {
        setSoundEnabled(!soundEnabled());
        refreshSoundButton();
        ensureAudioContext();
    });

    refreshSoundButton();

    const fetchNotifications = async () => {
        if (state.inFlight || (document.hidden && state.initialized)) {
            return;
        }

        state.inFlight = true;

        try {
            const response = await fetch(endpoint, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error(`Notifikasi gagal dimuat (${response.status}).`);
            }

            const data = await response.json();

            if (mode === 'admin') {
                const previousRequestId = state.latestRequestId ?? 0;
                const hasNewRequest = state.initialized
                    && data.latest_request_id !== null
                    && data.latest_request_id > previousRequestId;

                if (hasNewRequest) {
                    playChime();
                    showToast('Permintaan peminjaman baru masuk.', 'info');
                }

                state.latestRequestId = data.latest_request_id;
                renderAdmin(body, updated, data);
                updateBadge(badge, data.pending_requests + data.overdue_loans);
            } else {
                handleBorrowerChanges(state, data);
                state.statuses = new Map(data.requests.map((request) => [request.id, {
                    label: request.status_label,
                    book: request.book,
                }]));
                renderBorrower(body, updated, data);
                updateBadge(badge, data.pending_requests + data.overdue_loans);
            }

            state.initialized = true;
        } catch {
            if (!state.initialized) {
                body.replaceChildren(createEmpty('Notifikasi belum dapat dimuat.'));
                updated.textContent = 'Coba muat ulang halaman.';
            }
        } finally {
            state.inFlight = false;
        }
    };

    fetchNotifications();

    window.setInterval(fetchNotifications, POLL_INTERVAL);

    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            fetchNotifications();
        }
    });

    root.addEventListener('show.bs.dropdown', fetchNotifications);
}

export function initNotifications() {
    document.addEventListener('pointerdown', ensureAudioContext, { once: true, passive: true });

    document.querySelectorAll('[data-notifications]').forEach(initNotificationRoot);
}
