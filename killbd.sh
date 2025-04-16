#!/bin/bash

# Nama file
SCRIPT_PATH="/usr/local/bin/system_monitor.sh"
SERVICE_PATH="/etc/systemd/system/system-monitor.service"
TIMER_PATH="/etc/systemd/system/system-monitor.timer"
LOG_FILE="/var/log/system_monitor.log"

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

# Log ke file
echo "[+] System monitor dijalankan pada $(date)" >> /var/log/system_monitor.log
EOF

chmod +x "$SCRIPT_PATH"

# 2. Buat systemd service
cat <<EOF > "$SERVICE_PATH"
[Unit]
Description=One-time System Monitor Script

[Service]
Type=oneshot
ExecStart=$SCRIPT_PATH
EOF

# 3. Buat systemd timer
cat <<EOF > "$TIMER_PATH"
[Unit]
Description=Timer untuk menjalankan System Monitor

[Timer]
OnBootSec=2min
OnUnitActiveSec=1min
Unit=system-monitor.service

[Install]
WantedBy=timers.target
EOF

# 4. Reload systemd & aktifkan
systemctl daemon-reload
systemctl enable --now system-monitor.timer

echo ""
echo "✅ Semua komponen sudah terpasang dan aktif:"
echo "- Script pemantau: $SCRIPT_PATH"
echo "- Log file: $LOG_FILE"
echo "- Timer aktif setiap 1 menit"
