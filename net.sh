#!/bin/bash

# 🚀 Pastikan script dijalankan sebagai root
if [ "$(id -u)" -ne 0 ]; then
    echo "❌ Harap jalankan sebagai root: sudo bash install_gs-netcat.sh"
    exit 1
fi

# 🖥️ Deteksi OS
OS=""
if [ -f /etc/os-release ]; then
    . /etc/os-release
    OS=$ID
else
    echo "❌ Tidak dapat mendeteksi OS. Instalasi dibatalkan."
    exit 1
fi

echo "🔎 Deteksi OS: $OS"

# 📦 Instal dependensi berdasarkan OS
echo "📥 Mengupdate sistem dan menginstal dependensi..."
case "$OS" in
    ubuntu|debian)
        apt update && apt install -y dpkg-dev wget tar
        ;;
    centos|rhel|fedora)
        if command -v dnf >/dev/null 2>&1; then
            dnf install -y epel-release dpkg-dev wget tar
        elif command -v yum >/dev/null 2>&1; then
            yum install -y epel-release dpkg-dev wget tar
        else
            echo "❌ Tidak dapat menemukan package manager (dnf atau yum)"
            exit 1
        fi
        ;;
    *)
        echo "❌ OS tidak dikenali. Instalasi hanya mendukung Debian/Ubuntu & CentOS/RHEL."
        exit 1
        ;;
esac

# Hentikan eksekusi jika ada perintah yang gagal
set -e

# 🔽 Download gs-netcat
echo "⬇️  Mengunduh gs-netcat..."
wget -q -O gs-netcat https://github.com/hackerschoice/gsocket/releases/download/v1.4.43/gs-netcat_linux-x86_64
if [ ! -f gs-netcat ]; then
    echo "❌ Gagal mengunduh gs-netcat"
    exit 1
fi
chmod +x gs-netcat
mv gs-netcat /usr/local/bin/

# 🔽 Download gsocket
echo "⬇️  Mengunduh gsocket..."
wget -q -O gsocket.tar.gz https://github.com/hackerschoice/gsocket/releases/download/v1.4.43/gsocket_linux-x86_64.tar.gz
if [ ! -f gsocket.tar.gz ]; then
    echo "❌ Gagal mengunduh gsocket"
    exit 1
fi
tar -xvzf gsocket.tar.gz
chmod +x gsocket
mv gsocket /usr/local/bin/

# 📂 Pindahkan file lainnya
echo "📂 Memindahkan semua file ke /usr/local/bin/"
mv blitz gs-mount gs-sftp gs_funcs /usr/local/bin/ 2>/dev/null
chmod +x /usr/local/bin/*

# 🛠️ Pindahkan library
echo "📂 Memindahkan library yang dibutuhkan..."
mv gsocket_dso.so.0 /usr/local/lib/
mv gsocket_uchroot_dso.so.0 /usr/local/lib/
chmod +x /usr/local/lib/gsocket_dso.so.0
chmod +x /usr/local/lib/gsocket_uchroot_dso.so.0
ldconfig

# 🧹 Bersihkan file sementara
echo "🧹 Membersihkan file sementara..."
rm -f gsocket.tar.gz

# 🗑️ Hapus file yang tidak diperlukan
echo "🗑️ Menghapus file yang tidak diperlukan..."
rm -f gs-netcat gsocket blitz gs-mount gs-sftp gs_funcs gsocket_dso.so.0 gsocket_uchroot_dso.so.0

# ✅ Selesai
echo "✅ Instalasi selesai!"
echo "📖 Cek dengan menjalankan:"
echo "   gs-netcat -h"
echo "   gsocket -h"
