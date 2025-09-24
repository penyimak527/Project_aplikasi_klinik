Information My Project
Aplikasi Klinik
Aplikasi Klinik adalah sebuah aplikasi berbasis web yang dibangun menggunakan framework CodeIgniter 3 untuk mengelola operasional klinik, seperti manajemen pasien, jadwal dokter, dan data terkait lainnya. Repository ini berisi source code untuk aplikasi tersebut.
Deskripsi
Aplikasi ini dirancang untuk membantu administrasi klinik dalam mengelola data pasien, dokter, dan jadwal pelayanan. Project ini dibuat tanpa menggunakan Composer untuk dependency management, sehingga semua library dan konfigurasi dilakukan secara manual sesuai dengan struktur CodeIgniter 3.
Catatan: Fitur laporan belum diimplementasikan dalam aplikasi ini.
Prasyarat
Untuk menjalankan aplikasi ini, pastikan Anda memiliki:

PHP versi 5.6 atau lebih tinggi
MySQL atau MariaDB sebagai database
Web server (contoh: Apache) dengan modul mod_rewrite diaktifkan untuk URL rewriting
CodeIgniter 3 (dapat diunduh dari situs resmi CodeIgniter)

Instalasi
Unduh CodeIgniter 3:

Kunjungi situs resmi CodeIgniter 3 untuk mengunduh versi framework yang sesuai.
Ekstrak file ke direktori web server Anda (misalnya, htdocs pada XAMPP).

Clone atau Salin Repository:

Clone repository ini atau salin isi folder ke dalam direktori CodeIgniter yang sudah diekstrak.git clone https://github.com/penyimak527/Project_aplikasi_klinik.git



Konfigurasi Database:

Buat database baru di MySQL/MariaDB.
Impor skema database dari file database.sql (jika ada) ke database Anda.
Buka file application/config/database.php dan sesuaikan pengaturan database:'hostname' => 'localhost',
'username' => 'your_username',
'password' => 'your_password',
'database' => 'nama_database',



Konfigurasi Base URL:

Buka file application/config/config.php dan atur base_url sesuai dengan lokasi aplikasi Anda:$config['base_url'] = 'http://localhost/aplikasi_klinik/';



Jalankan Aplikasi:

Pastikan web server dan MySQL sudah berjalan.
Akses aplikasi melalui browser dengan URL yang sesuai (misalnya, http://localhost/aplikasi_klinik/).

Struktur Folder
Berikut adalah struktur utama folder dalam repository ini:
aplikasi_klinik/
├── application/
│   ├── config/      # File konfigurasi seperti database.php dan config.php
│   ├── controllers/ # Controller untuk logika aplikasi
│   ├── models/      # Model untuk interaksi dengan database
│   ├── views/       # View untuk tampilan antarmuka
├── system/          # Core framework CodeIgniter 3
├── assets/          # File statis seperti CSS, JS, dan gambar
└── README.md        # File ini

Cara Penggunaan

Login: Akses halaman login (biasanya di base_url) untuk masuk ke sistem.
Manajemen Data:
Pasien: Tambah, edit, atau hapus data pasien.
Dokter: Kelola data dokter dan jadwal praktik.
Jadwal: Atur jadwal pelayanan klinik.



Catatan: Fitur laporan belum tersedia dan akan ditambahkan pada pembaruan berikutnya.
Kontribusi
Jika Anda ingin berkontribusi pada pengembangan aplikasi ini:

Fork repository ini.
Buat branch baru untuk fitur atau perbaikan (git checkout -b fitur-baru).
Commit perubahan Anda (git commit -m 'Menambahkan fitur X').
Push ke branch Anda (git push origin fitur-baru).
Buat Pull Request di repository ini.

Catatan Tambahan

Project ini tidak menggunakan Composer, jadi pastikan semua library tambahan sudah disertakan secara manual di folder application/libraries atau application/third_party.
Untuk keamanan, pastikan Anda mengatur hak akses file dan folder dengan benar di server produksi.
Dokumentasi resmi CodeIgniter 3 dapat diakses di https://www.codeigniter.com/userguide3/ untuk referensi pengembangan.

Lisensi
Aplikasi ini bersifat open-source dan dapat digunakan sesuai dengan lisensi yang ditentukan (misalnya, MIT License). Pastikan untuk memeriksa file LICENSE di repository (jika ada).