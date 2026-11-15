import { showToast } from './toast';

async function copyText(text) {
    if (navigator.clipboard && window.isSecureContext) {
        await navigator.clipboard.writeText(text);

        return;
    }

    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.setAttribute('readonly', '');
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();

    const copied = document.execCommand('copy');
    textarea.remove();

    if (!copied) {
        throw new Error('Penyalinan tidak didukung.');
    }
}

export function initShareButtons() {
    document.querySelectorAll('[data-share-url]').forEach((button) => {
        button.addEventListener('click', async () => {
            const url = new URL(button.dataset.shareUrl, window.location.origin).href;
            const title = button.dataset.shareTitle || document.title;
            const text = button.dataset.shareText || `Lihat buku "${title}" di perpustakaan sekolah.`;

            if (navigator.share) {
                try {
                    await navigator.share({ title, text, url });

                    return;
                } catch (error) {
                    if (error.name === 'AbortError') {
                        return;
                    }
                }
            }

            try {
                await copyText(url);
                showToast('Tautan buku berhasil disalin.', 'success');
            } catch {
                window.prompt('Salin tautan buku:', url);
            }
        });
    });
}
