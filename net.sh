#!/bin/bash

# 🚀 GS-NETCAT INSTALLER SCRIPT
# Versi: 1.1.0-simple
# Penulis: @you
# Deskripsi: Script instalasi sederhana untuk gs-netcat dan dependensinya

set -euo pipefail

# 🎨 Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# 🔧 Konfigurasi
INSTALL_DIR="${INSTALL_DIR:-/usr/local/bin}"
LIB_DIR="${LIB_DIR:-/usr/local/lib}"
VERSION="1.4.43"  # Ganti dengan versi terbaru jika perlu
REPO_URL="https://github.com/hackerschoice/gsocket"

# 📌 Fungsi bantuan
error() {
    echo -e "${RED}❌ [ERROR]${NC} $1" >&2
    exit 1
}

info() {
    echo -e "${BLUE}🔹 [INFO]${NC} $1"
}

success() {
    echo -e "${GREEN}✅ [SUCCESS]${NC} $1"
}

# 🛑 Pastikan script dijalankan sebagai root
if [ "$(id -u)" -ne 0 ]; then
    error "Harap jalankan sebagai root: sudo bash $0"
fi

# 🖥️ Deteksi arsitektur
ARCH=$(uname -m)
case "$ARCH" in
    x86_64) ARCH="x86_64" ;;
    armv7l) ARCH="armv7" ;;
    aarch64) ARCH="aarch64" ;;
    *) error "Arsitektur tidak didukung: $ARCH" ;;
esac

# 📦 Install dependensi dasar
info "Menginstal dependensi sistem..."
if command -v apt >/dev/null; then
    apt update && apt install -y wget tar || error "Gagal menginstal dependensi"
elif command -v yum >/dev/null; then
    yum install -y wget tar || error "Gagal menginstal dependensi"
elif command -v dnf >/dev/null; then
    dnf install -y wget tar || error "Gagal menginstal dependensi"
elif command -v apk >/dev/null; then
    apk add --no-cache wget tar || error "Gagal menginstal dependensi"
else
    warning "Package manager tidak dikenali. Anda mungkin perlu menginstal dependensi secara manual."
fi

# 📥 Download dan install
BASE_URL="${REPO_URL}/releases/download/v${VERSION}"
TMP_DIR=$(mktemp -d)

info "Mengunduh gs-netcat..."
wget -q --show-progress -O "$TMP_DIR/gs-netcat" "${BASE_URL}/gs-netcat_linux-${ARCH}" || error "Gagal mengunduh gs-netcat"

info "Mengunduh gsocket..."
wget -q --show-progress -O "$TMP_DIR/gsocket.tar.gz" "${BASE_URL}/gsocket_linux-${ARCH}.tar.gz" || error "Gagal mengunduh gsocket"

info "Mengekstrak gsocket..."
tar -xzf "$TMP_DIR/gsocket.tar.gz" -C "$TMP_DIR" || error "Gagal mengekstrak gsocket"

# 📂 Install file
info "Menginstal file ke sistem..."
mkdir -p "$INSTALL_DIR" "$LIB_DIR"

install -m 755 "$TMP_DIR/gs-netcat" "$INSTALL_DIR/gs-netcat"
install -m 755 "$TMP_DIR/gsocket" "$INSTALL_DIR/gsocket"

# Install tools tambahan jika ada
for tool in blitz gs-mount gs-sftp gs_funcs; do
    if [ -f "$TMP_DIR/$tool" ]; then
        install -m 755 "$TMP_DIR/$tool" "$INSTALL_DIR/"
    fi
done

# Install library jika ada
if [ -f "$TMP_DIR/gsocket_dso.so.0" ]; then
    install -m 644 "$TMP_DIR/gsocket_dso.so.0" "$LIB_DIR/"
    ldconfig
fi

# 🧹 Bersihkan
rm -rf "$TMP_DIR"

# 🎉 Selesai
success "Instalasi selesai!"
cat <<EOF

Cara penggunaan:
  Mendengarkan koneksi:
    ${GREEN}gs-netcat -l -s 'KataKunciRahasiaku'${NC}

  Membuat koneksi:
    ${GREEN}gs-netcat -s 'KataKunciRahasiaku'${NC}

  Bantuan lengkap:
    gs-netcat -h
    gsocket -h

Direktori instalasi:
  Binari: ${INSTALL_DIR}
  Library: ${LIB_DIR}
EOF
