# DeskaLink

DeskaLink adalah platform digital yang mempertemukan penyedia jasa (partner) dan pembeli jasa/desain (client) dalam satu ekosistem terpadu. Platform ini mendukung fitur penjualan jasa, desain digital, manajemen pengguna, moderasi admin, serta sistem portofolio untuk meningkatkan kepercayaan antar pengguna.

## 🚀 Fitur Utama

### Umum:
- 🔐 Sistem login manual dan login Google
- 📱 Responsif menggunakan Tailwind CSS
- 🛡️ Register dengan peran: Client atau Partner
- 📦 Upload jasa dan desain digital oleh partner
- ✅ Moderasi konten oleh admin (approve, reject, banned)
- 📊 Statistik pengguna dan konten di dashboard
- 🧾 Riwayat moderasi tercatat
- 🖼️ Partner memiliki halaman portofolio

### Admin:
- Dashboard statistik
- CRUD pengguna (filter berdasarkan role & status)
- Manajemen konten jasa/desain digital
- Riwayat moderasi
- Manajemen transaksi (planned)
- Laporan & pengaduan (planned)
- Pengaturan sistem (planned)

### Partner:
- Dashboard partner
- Tambah/edit/hapus jasa & desain digital
- Upload preview gambar & file utama desain
- Portofolio: karya, sertifikat, penghargaan, dll
- Riwayat moderasi terbaru

---

## 🧱 Struktur Direktori (Saat Ini)

Deskalink/
├── admin_dashboard/
│   ├── dashboard_admin.php
│   ├── manage_users.php
│   ├── manage_contents.php
│   ├── sidebar.php
│   ├── update_user.php, add_user.php, delete_user.php, ...
│
├── partner_dashboard/
│   ├── dashboard_partner.php
│   ├── my_services.php
│   ├── my_designs.php
│   ├── portfolio.php
│   ├── add_service.php, edit_service.php, delete_service.php
│   ├── add_design.php, edit_design.php, delete_design.php
│   ├── save_portfolio.php, update_portfolio.php, delete_portfolio.php
│   ├── sidebar_partner.php
│
├── users/
│   ├── login.php
│   ├── register.php
│   ├── complete-profile.php
│   ├── callback.php (Google OAuth)
│
├── service/
│   └── config.php
│
├── assets/
│   └── images/, js/, css/ (optional)
│
├── CHANGELOG.md
├── index.php
├── README.md
├── release.sh
└── VERSION


---

## 🚀 Cara Menjalankan

1. Clone repositori ini ke folder `htdocs` (XAMPP).
2. Jalankan Apache dan MySQL di XAMPP.
3. Import file SQL ke phpMyAdmin (`deskalink` DB).
4. Konfigurasi file `service/config.php` sesuai koneksi lokal.
5. Untuk login Google:
   - Buat project di Google Cloud Console
   - Aktifkan OAuth 2.0
   - Simpan client ID dan secret di `config.php`

---

## 📝 Cara Membuat Rilis Baru

Proyek ini menggunakan release.sh untuk memudahkan pembuatan rilis baru, memperbarui versi, dan memperbarui file CHANGELOG.md dengan deskripsi perubahan.

Langkah-langkah:
1. Buka terminal (rekomendasi menggunakan Git Bash) dan jalankan perintah berikut:
   ```bash
   bash release.sh
2. Input versi baru: Masukkan nomor versi baru (misalnya 0.1.4).
3. Pilih kategori perubahan: Pilih kategori perubahan seperti Added, Fixed, Changed, dll.
4. Masukkan deskripsi perubahan: Masukkan deskripsi singkat tentang perubahan. Tekan Ctrl + D untuk selesai menulis.
5. Tambah entri perubahan lagi? Jika ingin menambahkan perubahan dengan kategori lain, pilih y dan ulangi langkah 3-4. Jika selesai, pilih n.

Setelah itu, skrip akan:
- Memperbarui file VERSION dengan versi terbaru.
- Memperbarui CHANGELOG.md dengan entri baru.
- Membuat commit baru dan tag untuk versi tersebut di Git.
- Melakukan push ke remote repository di GitHub.

---

## 📌 Catatan Pengembangan Selanjutnya

- [ ] Sistem transaksi dan pembayaran
- [ ] Penilaian & ulasan dari client
- [ ] Notifikasi moderasi (email/internal)
- [ ] Penjadwalan meeting antara client-partner
- [ ] Mode dark/light untuk pengguna
- [ ] Pagination & pencarian
- [ ] Validasi form lebih kompleks (server + client side)
- [ ] Upload file via server (bukan hanya URL)

---

## 🧑‍💻 Developer

- Umar Mukhtar
- Dimas Rhoyhan Budi S.
- Mokhammad Afrylianto Aryo Abdi
- James Petra Benaya P. N.

---

## 💡 Lisensi

Proyek ini dikembangkan untuk keperluan akademik dan tidak untuk penggunaan komersial tanpa izin.
