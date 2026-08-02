#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/furugi}"
QUEUE_SERVICE_NAME="${QUEUE_SERVICE_NAME:-furugi-queue.service}"
QUEUE_SERVICE_SOURCE="${APP_DIR}/deploy/systemd/${QUEUE_SERVICE_NAME}"
QUEUE_SERVICE_TARGET="/etc/systemd/system/${QUEUE_SERVICE_NAME}"

if [[ "${EUID}" -ne 0 ]]; then
    echo "ERROR: Run as root. Example: sudo bash deploy/scripts/install-startup-services.sh"
    exit 1
fi

if [[ ! -d "${APP_DIR}" || ! -f "${APP_DIR}/artisan" ]]; then
    echo "ERROR: APP_DIR is not a Laravel project: ${APP_DIR}"
    exit 1
fi

if [[ ! -f "${QUEUE_SERVICE_SOURCE}" ]]; then
    echo "ERROR: systemd service file was not found: ${QUEUE_SERVICE_SOURCE}"
    exit 1
fi

echo "==> Enable Apache2 auto start"
systemctl enable apache2

echo "==> Install FURUGI queue worker systemd unit"
install -m 0644 "${QUEUE_SERVICE_SOURCE}" "${QUEUE_SERVICE_TARGET}"
systemctl daemon-reload
systemctl enable "${QUEUE_SERVICE_NAME}"

echo "==> Rebuild Laravel production cache"
cd "${APP_DIR}"
sudo -u www-data php artisan optimize:clear
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

echo "==> Start or restart services"
systemctl restart apache2
systemctl restart "${QUEUE_SERVICE_NAME}"

echo "==> Service status"
systemctl --no-pager --full status apache2 || true
systemctl --no-pager --full status "${QUEUE_SERVICE_NAME}" || true

echo "Done: Apache2 and ${QUEUE_SERVICE_NAME} will start automatically after reboot."
