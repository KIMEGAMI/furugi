

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/service-worker.js').catch(() => {});
    });
}

let deferredInstallPrompt = null;

window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    deferredInstallPrompt = event;

    document.querySelectorAll('[data-pwa-install]').forEach((button) => {
        button.hidden = false;
        button.disabled = false;
    });
});

window.addEventListener('appinstalled', () => {
    deferredInstallPrompt = null;

    document.querySelectorAll('[data-pwa-install]').forEach((button) => {
        button.hidden = true;
    });
});

window.addEventListener('click', async (event) => {
    const installButton = event.target.closest('[data-pwa-install]');

    if (! installButton || ! deferredInstallPrompt) {
        return;
    }

    installButton.disabled = true;
    deferredInstallPrompt.prompt();

    await deferredInstallPrompt.userChoice;
    deferredInstallPrompt = null;
    installButton.hidden = true;
});
