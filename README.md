# SISTEM INFORMASI E-KOPERASI PEGAWAI

Aplikasi web sistem informasi dan toko digital koperasi pegawai yang dibangun menggunakan **Laravel 10**, **MySQL**, **Livewire 3**, **Alpine.js**, dan **Bootstrap 5**.

---

## 🎨 Identitas Visual
- **Hijau Utama:** `#16834A`
- **Kuning Aksen:** `#F5C542`
- **Latar Belakang:** `#F4F6F8`
- **Teks Gelap:** `#252B33`
- **Merah Peringatan:** `#DC3545`

---

## 👥 3 Peran Pengguna (Role) & Kredensial Demo
Aplikasi telah dilengkapi seeder data awal dengan akun uji coba:

| Role | Email Login | Password | NIP / ID | Fungsi Utama |
|---|---|---|---|---|
| **Administrator** | `admin@koperasi.com` | `password` | ADM-001 | Akses penuh dashboard, stok opname, laporan PDF/Excel, CRUD produk, supplier, PO barang masuk, pengguna & setting QRIS. |
| **Petugas / Kasir** | `petugas@koperasi.com` | `password` | KAS-002 | Mesin kasir POS langsung, cetak struk kasir, verifikasi bukti QRIS manual & uang tunai, pemrosesan antrean pesanan. |
| **Pelanggan (Pegawai)** | `pegawai@koperasi.com` | `password` | PEG-10492 | UI belanja mobile-first, keranjang & checkout tanpa reload, upload bukti bayar QRIS, lacak pesanan (ambil sendiri / antar ruangan). |

---

## 🚀 Panduan Menjalankan Proyek

1. **Konfigurasi Database MySQL di `.env`:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ekoperasi
   DB_USERNAME=root
   DB_PASSWORD=
   ```

2. **Migrasi Database & Seeding Data:**
   ```bash
   php artisan migrate:fresh --seed
   ```

3. **Hubungkan Storage Link:**
   ```bash
   php artisan storage:link
   ```

4. **Jalankan Server Lokal:**
   ```bash
   php artisan serve
   ```
   Buka di browser: **[http://localhost:8000](http://localhost:8000)**

---

## 📚 Dokumentasi Lengkap
Dokumentasi teknis menyeluruh, kamus data 19 tabel, dan alur bisnis tersedia di file:
**[DOKUMENTASI_SISTEM_E-KOPERASI.md](DOKUMENTASI_SISTEM_E-KOPERASI.md)**.
