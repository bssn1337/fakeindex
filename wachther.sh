#!/bin/bash

# Path untuk user biasa
HOME_DIR="$HOME/.local/system_monitor"
mkdir -p "$HOME_DIR"

SCRIPT_PATH="$HOME_DIR/system_monitor.sh"
LOG_FILE="$HOME_DIR/system_monitor.log"
CRON_ENTRY="* * * * * bash $SCRIPT_PATH >> $LOG_FILE 2>&1"

# 1. Buat script monitor
cat <<'EOF' > "$SCRIPT_PATH"
#!/bin/bash

# Deteksi dan kill proses backdoor
pid_list=$(ps aux | grep -E 'php /tmp/.*xhand\.Lock' | grep -v grep | awk '{print $2}')
if [[ ! -z "$pid_list" ]]; then
    echo "[!] Ditemukan proses xhand.Lock: \$pid_list, kill semua..."
    echo "\$pid_list" | xargs -r kill -9
fi

# Hapus file /tmp/xhand.Lock
find /tmp -type f -regex '.*/xhand\.Lock' -exec rm -f {} \; -print

# Log
echo "[+] System monitor dijalankan pada \$(date)"
EOF

chmod +x "$SCRIPT_PATH"

# 2. Tambahkan ke crontab jika belum ada
if ! crontab -l | grep -q "$SCRIPT_PATH"; then
    (crontab -l 2>/dev/null; echo "$CRON_ENTRY") | crontab -
    echo "✅ Cron ditambahkan: script dijalankan tiap 1 menit."
else
    echo "ℹ️ Cron sudah ada, tidak ditambahkan ulang."
fi

# 3. Info
echo ""
echo "✅ Semua komponen selesai di-setup:"
echo "- Script: $SCRIPT_PATH"
echo "- Log file: $LOG_FILE"
