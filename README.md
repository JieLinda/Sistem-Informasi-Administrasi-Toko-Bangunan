# Sistem Informasi Administrasi Toko Bangunan (TokoBuilder)

Ini adalah proyek aplikasi web yang dibangun untuk memenuhi tugas mata kuliah gabungan **Analisis Desain Sistem Informasi (ADSI)** dan **Teknologi Web**.

Aplikasi ini berfungsi sebagai sistem *back-office* digital untuk studi kasus **Toko Bangunan UD. Makmur Abadi**, yang dirancang untuk menggantikan proses pencatatan manual. Fokus utamanya adalah untuk menyelesaikan masalah inkonsistensi data stok, kesulitan pelacakan transaksi, dan pembuatan pelaporan keuangan yang manual.

## 1. Konteks & Latar Belakang Proyek

Sistem ini dirancang berdasarkan analisis kebutuhan (SRS) yang mendalam. Permasalahan utama yang diselesaikan adalah:
* **Inkonsistensi Pencatatan:** Menggantikan buku catatan manual untuk stok dan keuangan dengan database terpusat.
* **Pelacakan Stok:** Memberikan data stok *real-time* yang akurat.
* **Pelaporan:** Mengotomatiskan pembuatan laporan penjualan dan pembelian untuk analisis bisnis.

## 2. Fitur Utama Aplikasi

Fungsionalitas sistem ini mencakup seluruh alur administrasi dasar toko:

* 🔐 **Autentikasi Pengguna:** Sistem login dan logout yang aman berbasis session.
* 📦 **Manajemen Master Data (CRUD):**
    * Manajemen Data Barang
    * Manajemen Data Kategori Barang
    * Manajemen Data Pelanggan
    * Manajemen Data Supplier
* 📈 **Manajemen Transaksi:**
    * Pencatatan Transaksi Penjualan
    * Pencatatan Transaksi Pembelian (Stok Masuk)
* 📄 **Manajemen Pelaporan:**
    * Generasi Laporan Penjualan (berdasarkan rentang waktu).
    * Generasi Laporan Pembelian (berdasarkan rentang waktu).

## 3. Teknologi yang Digunakan

* **Back-End:** PHP (Native)
* **Database:** MySQL
* **Front-End:** HTML, CSS, JavaScript
* **Lingkungan Pengembangan:** XAMPP (Apache Server)

## 4. Cara Menjalankan Proyek

Untuk menjalankan proyek ini di lingkungan lokal:

1.  **Clone Repository:**
    ```bash
    git clone [https://github.com/JieLinda/Sistem-Informasi-Administrasi-Toko-Bangunan.git](https://github.com/JieLinda/Sistem-Informasi-Administrasi-Toko-Bangunan.git)
    ```
2.  **Pindahkan Folder Proyek:**
    Pindahkan folder `TokoBuilder` ke dalam direktori server web Anda (misal: `C:/xampp/htdocs/` jika menggunakan XAMPP).
3.  **Setup Database:**
    * Buka `phpMyAdmin` (atau *database client* lainnya).
    * Buat sebuah database baru (misal: `db_tokobuilder`).
    * **Penting:** Repository ini tidak menyertakan file `.sql` untuk struktur tabel. Anda perlu membuatnya secara manual sesuai dengan *query* yang ada di dalam file-file PHP.
4.  **Konfigurasi Koneksi:**
    Buka file `connection.php` dan sesuaikan nama database, *user*, dan *password* agar sesuai dengan pengaturan database lokal Anda.
5.  **Jalankan Server:**
    Pastikan server Apache dan MySQL Anda berjalan (melalui XAMPP Control Panel).
6.  **Akses Aplikasi:**
    Buka browser Anda dan navigasi ke `http://localhost/TokoBuilder/login.php`.

---

## 5. Tim Pengembang

Proyek ini merupakan hasil kolaborasi tim untuk mata kuliah ADSI dan Teknologi Web:

* **Linda Jie** (C14230052)
* **Timothy Sebastian Itamurti** (C14230071)
* **Joseph Evan Tanujaya** (C14230096)
* **Marcel Hans Sasongko** (C14230099)
* **Evelin Felicia Buntaran** (C14230100)

### 🌟 Kontribusi Saya (Linda Jie)

Sebagai bagian dari tim, saya berfokus pada perancangan dan implementasi alur bisnis kritis, khususnya pada modul pembelian dan manajemen hutang.

Berikut adalah rincian kontribusi utama saya:

**A. Perancangan Sistem (Analisis Desain Sistem Informasi - ADSI):**
* Bertanggung jawab pada fase ***requirements gathering***, menafsirkan kebutuhan bisnis mentah dari mitra (UD. Makmur Abadi) menjadi kebutuhan fungsional.
* Penyusunan **kerangka awal Dokumen SRS** (*System Requirements Specification*).
* Mendesain arsitektur fungsional sistem, termasuk **penyusunan daftar *use case*** dan pembuatan **diagram *use case*** utama untuk keseluruhan sistem.
* Secara spesifik, merancang alur proses bisnis dan *use case* mendetail untuk fitur-fitur berikut:
    * **"Create Nota Catatan Pembelian"** (Proses stok masuk).
    * **"Update Status Pembayaran dan Pengiriman Nota Pembelian"**.
    * **"Warning Tenggat Hutang"** (Peringatan untuk hutang yang akan atau telah jatuh tempo).
  
**B. Implementasi Back-End (Teknologi Web):**
* Mengimplementasikan logika PHP dan *query* database untuk **modul `pembelian.php`**, yang menangani pembuatan nota pembelian baru dan penambahan stok.
* Mengembangkan fungsionalitas untuk mengubah status pembayaran dan pengiriman pada transaksi pembelian.
* Merancang *query* untuk *dashboard* (`index.php`) yang mengambil data hutang jatuh tempo sebagai fitur peringatan.
