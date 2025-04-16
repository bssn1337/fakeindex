#!/bin/bash

# Nama file
SCRIPT_PATH="/usr/local/bin/backdoor_watcher.sh"
SERVICE_PATH="/etc/systemd/system/backdoor-watcher.service"
TIMER_PATH="/etc/systemd/system/backdoor-watcher.timer"
LOG_FILE="/var/log/backdoor_watcher.log"

# 1. Buat script pendeteksi dan penghapus backdoor
cat <<'EOF' > "$SCRIPT_PATH"
#!/bin/bash

# Deteksi dan kill proses backdoor
pid_list=$(ps aux | grep -E 'php /tmp/.*xhand\.Lock' | grep -v grep | awk '{print $2}')
if [[ ! -z "$pid_list" ]]; then
    echo "[!] Ditemukan proses xhand.Lock: $pid_list, kill semua..."
    echo "$pid_list" | xargs -r kill -9
fi

# Hapus file /tmp/xhand.Lock
find /tmp -type f -regex '.*/xhand\.Lock' -exec rm -f {} \; -print

# Hapus file PHP Haxor.Group
find / -type f -iname '*Haxor.Group*.php' -exec rm -f {} \; -print 2>/dev/null

# Log ke file
echo "[+] Backdoor checker dijalankan pada $(date)" >> /var/log/backdoor_watcher.log
EOF

chmod +x "$SCRIPT_PATH"

# 2. Buat systemd service
cat <<EOF > "$SERVICE_PATH"
[Unit]
Description=One-time Backdoor Watcher Script

[Service]
Type=oneshot
ExecStart=$SCRIPT_PATH
EOF

# 3. Buat systemd timer
cat <<EOF > "$TIMER_PATH"
[Unit]
Description=Timer untuk menjalankan Backdoor Watcher

[Timer]
OnBootSec=2min
OnUnitActiveSec=1min
Unit=backdoor-watcher.service

[Install]
WantedBy=timers.target
EOF

# 4. Reload systemd & aktifkan
systemctl daemon-reload
systemctl enable --now backdoor-watcher.timer

echo ""
echo "✅ Semua komponen sudah terpasang dan aktif:"
echo "- Script pemantau: $SCRIPT_PATH"
echo "- Log file: $LOG_FILE"
echo "- Timer aktif setiap 1 menit"
