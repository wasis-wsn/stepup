#!/bin/bash
# ==============================================================================
# STEPUP - Automated Production Backup Script
# Location on server: /usr/local/sbin/backup-stepup.sh
# Target backup directory: /srv/backup/stepup
# ==============================================================================

set -eo pipefail

BACKUP_DIR="/srv/backup/stepup"
TIMESTAMP=$(date +"%Y-%m-%d_%H%M%S")
DATE_LOG=$(date +"%Y-%m-%d %H:%M:%S")
LOG_FILE="${BACKUP_DIR}/backup.log"

# Pastikan folder tujuan backup ada di storage HDD 500GB
mkdir -p "${BACKUP_DIR}"

echo "[${DATE_LOG}] === Starting STEPUP Production Backup ===" >> "${LOG_FILE}"

# 1. Logical Database Backup (MySQL dump)
DB_BACKUP_FILE="${BACKUP_DIR}/stepup_db_${TIMESTAMP}.sql.gz"
if docker ps --format '{{.Names}}' | grep -q "^stepup-prod-mysql$"; then
    echo "[${DATE_LOG}] Backing up MySQL database (stepup_db)..." >> "${LOG_FILE}"
    
    # Ambil DB_PASSWORD dari environment container atau konfigurasi
    docker exec stepup-prod-mysql mysqldump -u root -proot_password --single-transaction --quick stepup_db | gzip > "${DB_BACKUP_FILE}"
    
    DB_SIZE=$(du -h "${DB_BACKUP_FILE}" | cut -f1)
    echo "[${DATE_LOG}] MySQL backup completed successfully: ${DB_BACKUP_FILE} (${DB_SIZE})" >> "${LOG_FILE}"
else
    echo "[${DATE_LOG}] WARNING: Container stepup-prod-mysql is not running! Skipping DB backup." >> "${LOG_FILE}"
fi

# 2. Laravel Storage Backup (Uploaded files, product images)
STORAGE_BACKUP_FILE="${BACKUP_DIR}/stepup_storage_${TIMESTAMP}.tar.gz"
STORAGE_VOLUME_PATH="/var/lib/docker/volumes/stepup_storage_data/_data"

if [ -d "${STORAGE_VOLUME_PATH}" ]; then
    echo "[${DATE_LOG}] Backing up Laravel storage files..." >> "${LOG_FILE}"
    tar -czf "${STORAGE_BACKUP_FILE}" -C "${STORAGE_VOLUME_PATH}" .
    STORAGE_SIZE=$(du -h "${STORAGE_BACKUP_FILE}" | cut -f1)
    echo "[${DATE_LOG}] Storage backup completed successfully: ${STORAGE_BACKUP_FILE} (${STORAGE_SIZE})" >> "${LOG_FILE}"
else
    echo "[${DATE_LOG}] Volume ${STORAGE_VOLUME_PATH} not found, backing up via docker container..." >> "${LOG_FILE}"
    docker run --rm --volumes-from stepup-prod-app -v "${BACKUP_DIR}:/backup" alpine tar -czf "/backup/stepup_storage_${TIMESTAMP}.tar.gz" -C /var/www/html/storage/app . || true
fi

# 3. Retention Policy: Hapus backup yang lebih tua dari 7 hari
echo "[${DATE_LOG}] Cleaning backups older than 7 days..." >> "${LOG_FILE}"
find "${BACKUP_DIR}" -type f \( -name "*.sql.gz" -o -name "*.tar.gz" \) -mtime +7 -exec rm -f {} \;

echo "[${DATE_LOG}] === STEPUP Backup Routine Finished ===" >> "${LOG_FILE}"
