#!/bin/bash

# 1. Minta input versi baru
read -p "Masukkan versi baru (misal 1.0.1): " version

# 2. Tanggal hari ini
today=$(date +"%Y-%m-%d")

# 3. Cek dan buat file VERSION jika belum ada
if [ ! -f VERSION ]; then
  echo "VERSION belum ada. Membuat file baru..."
  echo "0.0.0" > VERSION
fi

# Update file VERSION
echo "$version" > VERSION
echo "VERSION diperbarui ke $version"

# 4. Cek dan buat file CHANGELOG.md jika belum ada
if [ ! -f CHANGELOG.md ]; then
  echo "CHANGELOG.md belum ada. Membuat file baru..."
  echo "# Changelog" > CHANGELOG.md
  echo "" >> CHANGELOG.md
fi

# 5. Input perubahan dengan kategori
declare -A changes

while true; do
  echo ""
  echo "=== Tambah entri perubahan ==="
  echo "Pilih kategori:"
  echo "1. Added"
  echo "2. Fixed"
  echo "3. Changed"
  echo "4. Deprecated"
  echo "5. Removed"
  echo "6. Security"
  read -p "Masukkan pilihan kategori (1-6): " choice

  case $choice in
    1) category="Added" ;;
    2) category="Fixed" ;;
    3) category="Changed" ;;
    4) category="Deprecated" ;;
    5) category="Removed" ;;
    6) category="Security" ;;
    *) echo "Pilihan tidak valid, default ke 'Changed'."; category="Changed" ;;
  esac

  echo "Masukkan deskripsi perubahan untuk kategori '$category' (tekan Ctrl+D saat selesai):"
  input=$(cat)
  if [ -n "$input" ]; then
    changes["$category"]+="$input"$'\n'
  fi

  echo ""
  read -p "Tambah entri perubahan lagi? (y/n): " lanjut
  [[ "$lanjut" != "y" ]] && break
done

# 6. Buat entri baru di CHANGELOG.md
tempfile=$(mktemp)
echo "## [$version] - $today" > "$tempfile"

# Simpan deskripsi pertama untuk commit message
first_description=""

for category in "${!changes[@]}"; do
  echo "### $category" >> "$tempfile"
  while IFS= read -r line; do
    if [ -z "$first_description" ]; then
      first_description="$line"
    fi
    echo "$line" >> "$tempfile"
  done <<< "${changes[$category]}"
  echo "" >> "$tempfile"
done

# Gabungkan dengan CHANGELOG.md lama
cat CHANGELOG.md >> "$tempfile"
mv "$tempfile" CHANGELOG.md

echo "CHANGELOG.md diperbarui"

# 7. Git commit, tag, dan push
git add VERSION CHANGELOG.md
git commit -m "Rilis versi $version - $first_description"
git tag "v$version"
git push origin main
git push origin "v$version"

echo "✅ Versi $version berhasil dirilis ke GitHub 🚀"
