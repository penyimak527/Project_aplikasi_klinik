<!-- .. ###################
.. What is CodeIgniter
.. ###################

.. CodeIgniter is an Application Development Framework - a toolkit - for people
.. who build web sites using PHP. Its goal is to enable you to develop projects
.. much faster than you could if you were writing code from scratch, by providing
.. a rich set of libraries for commonly needed tasks, as well as a simple
.. interface and logical structure to access these libraries. CodeIgniter lets
.. you creatively focus on your project by minimizing the amount of code needed
.. for a given task.

.. *******************
.. Release Information
.. *******************

.. This repo contains in-development code for future releases. To download the
.. latest stable release please visit the `CodeIgniter Downloads
.. <https://codeigniter.com/download>`_ page.

.. **************************
.. Changelog and New Features
.. **************************

.. You can find a list of all changes for each release in the `user
.. guide change log <https://github.com/bcit-ci/CodeIgniter/blob/develop/user_guide_src/source/changelog.rst>`_.

.. *******************
.. Server Requirements
.. *******************

.. PHP version 5.6 or newer is recommended.

.. It should work on 5.3.7 as well, but we strongly advise you NOT to run
.. such old versions of PHP, because of potential security and performance
.. issues, as well as missing features.

.. ************
.. Installation
.. ************

.. Please see the `installation section <https://codeigniter.com/userguide3/installation/index.html>`_
.. of the CodeIgniter User Guide.

.. *******
.. License
.. *******

.. Please see the `license
.. agreement <https://github.com/bcit-ci/CodeIgniter/blob/develop/user_guide_src/source/license.rst>`_.

.. *********
.. Resources
.. *********

.. -  `User Guide <https://codeigniter.com/docs>`_
.. -  `Contributing Guide <https://github.com/bcit-ci/CodeIgniter/blob/develop/contributing.md>`_
.. -  `Language File Translations <https://github.com/bcit-ci/codeigniter3-translations>`_
.. -  `Community Forums <http://forum.codeigniter.com/>`_
.. -  `Community Wiki <https://github.com/bcit-ci/CodeIgniter/wiki>`_
.. -  `Community Slack Channel <https://codeigniterchat.slack.com>`_

.. Report security issues to our `Security Panel <mailto:security@codeigniter.com>`_
.. or via our `page on HackerOne <https://hackerone.com/codeigniter>`_, thank you.

.. ***************
.. Acknowledgement
.. ***************

.. The CodeIgniter team would like to thank EllisLab, all the
.. contributors to the CodeIgniter project and you, the CodeIgniter user. -->


# Information My Project

## Aplikasi Klinik
Aplikasi Klinik adalah sebuah aplikasi berbasis web yang dibangun menggunakan framework CodeIgniter 3 untuk mengelola operasional klinik, seperti manajemen pasien, jadwal dokter, dan data terkait lainnya. Repository ini berisi source code untuk aplikasi tersebut.

## Deskripsi
Aplikasi ini dirancang untuk membantu administrasi klinik dalam mengelola data pasien, dokter, dan jadwal pelayanan. Project ini dibuat tanpa menggunakan Composer untuk dependency management, sehingga semua library dan konfigurasi dilakukan secara manual sesuai dengan struktur CodeIgniter 3.  
**Catatan**: Fitur laporan belum diimplementasikan dalam aplikasi ini.

## Prasyarat
Untuk menjalankan aplikasi ini, pastikan Anda memiliki:  
- PHP versi 5.6 atau lebih tinggi  
- MySQL atau MariaDB sebagai database  
- Web server (contoh: Apache) dengan modul mod_rewrite diaktifkan untuk URL rewriting  
- CodeIgniter 3 (dapat diunduh dari situs resmi CodeIgniter)  

## Instalasi
1. **Unduh CodeIgniter 3**:  
   - Kunjungi situs resmi CodeIgniter 3 untuk mengunduh versi framework yang sesuai.  
   - Ekstrak file ke direktori web server Anda (misalnya, htdocs pada XAMPP).  

2. **Clone atau Salin Repository**:  
   - Clone repository ini atau salin isi folder ke dalam direktori CodeIgniter yang sudah diekstrak.  
     ```
     git clone <URL_REPOSITORY_ANDA>
     ```

3. **Konfigurasi Database**:  
   - Buat database baru di MySQL/MariaDB.  
   - Impor skema database dari file `database.sql` (jika ada) ke database Anda.  
   - Buka file `application/config/database.php` dan sesuaikan pengaturan database:  
     ```
     'hostname' => 'localhost',
     'username' => 'your_username',
     'password' => 'your_password',
     'database' => 'nama_database',
     ```

4. **Konfigurasi Base URL**:  
   - Buka file `application/config/config.php` dan atur `base_url` sesuai dengan lokasi aplikasi Anda:  
     ```
     $config['base_url'] = 'http://localhost/aplikasi_klinik/';
     ```

5. **Jalankan Aplikasi**:  
   - Pastikan web server dan MySQL sudah berjalan.  
   - Akses aplikasi melalui browser dengan URL yang sesuai (misalnya, `http://localhost/aplikasi_klinik/`).

## Struktur Folder
Berikut adalah struktur utama folder dalam repository ini:  
```
aplikasi_klinik/
├── application/
│   ├── config/      # File konfigurasi seperti database.php dan config.php
│   ├── controllers/ # Controller untuk logika aplikasi
│   ├── models/      # Model untuk interaksi dengan database
│   ├── views/       # View untuk tampilan antarmuka
├── system/          # Core framework CodeIgniter 3
├── assets/          # File statis seperti CSS, JS, dan gambar
└── README.md        # File ini
```

## Cara Penggunaan
1. **Login**: Akses halaman login (biasanya di `base_url`) untuk masuk ke sistem.  
2. **Manajemen Data**:  
   - **Pasien**: Tambah, edit, atau hapus data pasien.  
   - **Dokter**: Kelola data dokter dan jadwal praktik.  
   - **Jadwal**: Atur jadwal pelayanan klinik.  
**Catatan**: Fitur laporan belum tersedia dan akan ditambahkan pada pembaruan berikutnya.

## Kontribusi
Jika Anda ingin berkontribusi pada pengembangan aplikasi ini:  
1. Fork repository ini.  
2. Buat branch baru untuk fitur atau perbaikan (`git checkout -b fitur-baru`).  
3. Commit perubahan Anda (`git commit -m 'Menambahkan fitur X'`).  
4. Push ke branch Anda (`git push origin fitur-baru`).  
5. Buat Pull Request di repository ini.

## Catatan Tambahan
- Project ini tidak menggunakan Composer, jadi pastikan semua library tambahan sudah disertakan secara manual di folder `application/libraries` atau `application/third_party`.  
- Untuk keamanan, pastikan Anda mengatur hak akses file dan folder dengan benar di server produksi.  
- Dokumentasi resmi CodeIgniter 3 dapat diakses di `https://www.codeigniter.com/userguide3/` untuk referensi pengembangan.

## Lisensi
Aplikasi ini bersifat pribadi dan tidak untuk digunakan secara open-source. Penggunaan, modifikasi, atau distribusi aplikasi ini hanya diizinkan dengan persetujuan eksplisit dari pemilik proyek.