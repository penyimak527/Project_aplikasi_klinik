-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 26, 2025 at 08:42 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aplikasi_klinik`
--

-- --------------------------------------------------------

--
-- Table structure for table `contoh`
--

CREATE TABLE `contoh` (
  `id` int(11) NOT NULL,
  `contoh` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `contoh_multiple`
--

CREATE TABLE `contoh_multiple` (
  `id` int(11) NOT NULL,
  `contoh` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `contoh_multiple_detail`
--

CREATE TABLE `contoh_multiple_detail` (
  `id` int(11) NOT NULL,
  `id_contoh_multiple` varchar(255) DEFAULT NULL,
  `contoh_multiple` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `kpg_dokter`
--

CREATE TABLE `kpg_dokter` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_pegawai` varchar(255) DEFAULT NULL,
  `nama_pegawai` varchar(255) DEFAULT NULL,
  `id_poli` int(11) DEFAULT NULL,
  `nama_poli` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `kpg_jabatan`
--

CREATE TABLE `kpg_jabatan` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `kpg_pegawai`
--

CREATE TABLE `kpg_pegawai` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_jabatan` int(11) DEFAULT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `no_telp` varchar(255) DEFAULT NULL,
  `nama_jabatan` varchar(255) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `mst_diagnosa`
--

CREATE TABLE `mst_diagnosa` (
  `id` int(11) UNSIGNED NOT NULL,
  `nama_diagnosa` varchar(100) DEFAULT NULL,
  `id_poli` int(11) DEFAULT NULL,
  `nama_poli` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `mst_pasien`
--

CREATE TABLE `mst_pasien` (
  `id` int(11) NOT NULL,
  `no_rm` varchar(255) NOT NULL,
  `nama_pasien` varchar(255) NOT NULL,
  `nik` varchar(255) NOT NULL,
  `jenis_kelamin` varchar(255) NOT NULL,
  `tanggal_lahir` varchar(255) NOT NULL,
  `umur` varchar(255) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `pekerjaan` varchar(255) NOT NULL,
  `no_telp` varchar(255) NOT NULL,
  `status_perkawinan` varchar(255) NOT NULL,
  `nama_wali` varchar(255) NOT NULL,
  `golongan_darah` varchar(255) NOT NULL,
  `alergi` varchar(255) NOT NULL,
  `status_operasi` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mst_poli`
--

CREATE TABLE `mst_poli` (
  `id` int(11) UNSIGNED NOT NULL,
  `kode` varchar(100) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `mst_tindakan`
--

CREATE TABLE `mst_tindakan` (
  `id` int(11) UNSIGNED NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `harga` varchar(100) DEFAULT NULL,
  `id_poli` int(11) DEFAULT NULL,
  `nama_poli` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pol_anak`
--

CREATE TABLE `pol_anak` (
  `id` int(11) UNSIGNED NOT NULL,
  `kode_invoice` varchar(225) DEFAULT NULL,
  `id_pasien` int(11) DEFAULT NULL,
  `nik` varchar(255) DEFAULT NULL,
  `nama_pasien` varchar(225) DEFAULT NULL,
  `id_dokter` int(11) DEFAULT NULL,
  `nama_dokter` varchar(225) DEFAULT NULL,
  `keluhan` varchar(225) DEFAULT NULL,
  `berat_badan` varchar(225) DEFAULT NULL,
  `tinggi_badan` varchar(225) DEFAULT NULL,
  `suhu` varchar(225) DEFAULT NULL,
  `status_imunisasi` varchar(225) DEFAULT NULL,
  `catatan` varchar(225) DEFAULT NULL,
  `tanggal` varchar(225) DEFAULT NULL,
  `waktu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pol_anak_diagnosa`
--

CREATE TABLE `pol_anak_diagnosa` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_rm_anak` int(11) DEFAULT NULL,
  `id_diagnosa` int(11) DEFAULT NULL,
  `diagnosa` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pol_anak_tindakan`
--

CREATE TABLE `pol_anak_tindakan` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_pol_anak` int(11) DEFAULT NULL,
  `id_tindakan` int(11) DEFAULT NULL,
  `tindakan` varchar(255) DEFAULT NULL,
  `harga` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pol_gigi`
--

CREATE TABLE `pol_gigi` (
  `id` int(11) UNSIGNED NOT NULL,
  `kode_invoice` varchar(225) DEFAULT NULL,
  `id_pasien` int(11) DEFAULT NULL,
  `nik` varchar(255) DEFAULT NULL,
  `nama_pasien` varchar(225) DEFAULT NULL,
  `id_dokter` int(11) DEFAULT NULL,
  `nama_dokter` varchar(225) DEFAULT NULL,
  `keluhan` varchar(225) DEFAULT NULL,
  `catatan` varchar(225) DEFAULT NULL,
  `tanggal` varchar(225) DEFAULT NULL,
  `waktu` varchar(225) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pol_gigi_diagnosa`
--

CREATE TABLE `pol_gigi_diagnosa` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_pol_gigi` int(11) DEFAULT NULL,
  `id_diagnosa` int(11) DEFAULT NULL,
  `diagnosa` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pol_gigi_tindakan`
--

CREATE TABLE `pol_gigi_tindakan` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_pol_gigi` int(11) DEFAULT NULL,
  `id_tindakan` int(11) DEFAULT NULL,
  `tindakan` varchar(255) DEFAULT NULL,
  `harga` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pol_kecantikan`
--

CREATE TABLE `pol_kecantikan` (
  `id` int(11) UNSIGNED NOT NULL,
  `kode_invoice` varchar(225) DEFAULT NULL,
  `id_pasien` int(11) DEFAULT NULL,
  `nik` varchar(255) DEFAULT NULL,
  `nama_pasien` varchar(225) DEFAULT NULL,
  `id_dokter` int(11) DEFAULT NULL,
  `nama_dokter` varchar(225) DEFAULT NULL,
  `keluhan` varchar(225) DEFAULT NULL,
  `jenis_treatment` varchar(225) DEFAULT NULL,
  `riwayat_alergi` varchar(225) DEFAULT NULL,
  `produk_digunakan` varchar(225) DEFAULT NULL,
  `hasil_perawatan` varchar(225) DEFAULT NULL,
  `tanggal` varchar(225) DEFAULT NULL,
  `waktu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pol_kecantikan_detail`
--

CREATE TABLE `pol_kecantikan_detail` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_pol_kecantikan` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pol_kecantikan_diagnosa`
--

CREATE TABLE `pol_kecantikan_diagnosa` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_pol_kecantikan` int(11) DEFAULT NULL,
  `id_diagnosa` int(11) DEFAULT NULL,
  `diagnosa` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pol_kecantikan_tindakan`
--

CREATE TABLE `pol_kecantikan_tindakan` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_rm_kecantikan` int(11) DEFAULT NULL,
  `id_tindakan` int(11) DEFAULT NULL,
  `tindakan` varchar(255) DEFAULT NULL,
  `harga` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pol_resep`
--

CREATE TABLE `pol_resep` (
  `id` int(11) UNSIGNED NOT NULL,
  `kode_invoice` int(11) DEFAULT NULL,
  `kode_resep` varchar(100) DEFAULT NULL,
  `id_pasien` int(11) DEFAULT NULL,
  `nik` varchar(255) DEFAULT NULL,
  `nama_pasien` varchar(255) DEFAULT NULL,
  `id_dokter` int(11) DEFAULT NULL,
  `total_harga` varchar(255) DEFAULT NULL,
  `tanggal` varchar(255) DEFAULT NULL,
  `waktu` varchar(255) DEFAULT NULL,
  `nama_dokter` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pol_resep_obat`
--

CREATE TABLE `pol_resep_obat` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_pol_resep` int(11) DEFAULT NULL,
  `id_barang` int(11) DEFAULT NULL,
  `id_barang_detail` varchar(255) DEFAULT NULL,
  `nama_barang` varchar(255) DEFAULT NULL,
  `id_satuan_barang` varchar(255) DEFAULT NULL,
  `satuan_barang` varchar(255) DEFAULT NULL,
  `urutan_satuan` varchar(255) DEFAULT NULL,
  `jumlah` varchar(255) DEFAULT NULL,
  `harga` varchar(255) DEFAULT NULL,
  `aturan_pakai` varchar(255) DEFAULT NULL,
  `sub_total_harga` varchar(255) DEFAULT NULL,
  `laba` varchar(255) DEFAULT NULL,
  `sub_total_laba` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pol_resep_racikan`
--

CREATE TABLE `pol_resep_racikan` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_pol_resep` int(11) DEFAULT NULL,
  `nama_racikan` varchar(255) DEFAULT NULL,
  `jumlah` varchar(255) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `harga` varchar(255) DEFAULT NULL,
  `sub_total_harga` varchar(255) DEFAULT NULL,
  `laba` varchar(255) DEFAULT NULL,
  `sub_total_laba` varchar(255) DEFAULT NULL,
  `aturan_pakai` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pol_resep_racikan_detail`
--

CREATE TABLE `pol_resep_racikan_detail` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_pol_resep_racikan` int(11) DEFAULT NULL,
  `id_barang` int(11) DEFAULT NULL,
  `id_barang_detail` varchar(255) DEFAULT NULL,
  `nama_barang` varchar(255) DEFAULT NULL,
  `id_satuan_barang` varchar(255) DEFAULT NULL,
  `satuan_barang` varchar(255) DEFAULT NULL,
  `urutan_satuan` varchar(255) DEFAULT NULL,
  `jumlah` varchar(255) DEFAULT NULL,
  `harga` varchar(255) DEFAULT NULL,
  `sub_total_harga` varchar(255) DEFAULT NULL,
  `laba` varchar(255) DEFAULT NULL,
  `sub_total_laba` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pol_umum`
--

CREATE TABLE `pol_umum` (
  `id` int(11) UNSIGNED NOT NULL,
  `kode_invoice` varchar(225) DEFAULT NULL,
  `id_pasien` int(11) DEFAULT NULL,
  `nik` varchar(225) DEFAULT NULL,
  `nama_pasien` varchar(255) DEFAULT NULL,
  `id_dokter` int(11) DEFAULT NULL,
  `nama_dokter` varchar(225) DEFAULT NULL,
  `keluhan` varchar(225) DEFAULT NULL,
  `tekanan_darah` varchar(225) DEFAULT NULL,
  `suhu` varchar(225) DEFAULT NULL,
  `nadi` varchar(225) DEFAULT NULL,
  `catatan` varchar(225) DEFAULT NULL,
  `tanggal` varchar(225) DEFAULT NULL,
  `waktu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pol_umum_diagnosa`
--

CREATE TABLE `pol_umum_diagnosa` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_pol_umum` int(11) DEFAULT NULL,
  `id_diagnosa` int(11) DEFAULT NULL,
  `diagnosa` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pol_umum_tindakan`
--

CREATE TABLE `pol_umum_tindakan` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_pol_umum` int(11) DEFAULT NULL,
  `id_tindakan` int(11) DEFAULT NULL,
  `tindakan` varchar(255) DEFAULT NULL,
  `harga` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `rsp_antrian`
--

CREATE TABLE `rsp_antrian` (
  `id` int(11) UNSIGNED NOT NULL,
  `kode_invoice` varchar(255) DEFAULT NULL,
  `no_antrian` int(11) DEFAULT NULL,
  `id_poli` int(11) DEFAULT NULL,
  `nama_poli` varchar(255) DEFAULT NULL,
  `tanggal_antri` varchar(255) DEFAULT NULL,
  `waktu_antri` varchar(255) DEFAULT NULL,
  `tanggal_dipanggil` varchar(255) DEFAULT NULL,
  `waktu_dipanggil` varchar(255) DEFAULT NULL,
  `lama_menunggu` varchar(255) DEFAULT NULL,
  `status_antrian` varchar(255) DEFAULT NULL,
  `tanggal` varchar(255) DEFAULT NULL,
  `waktu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `rsp_booking`
--

CREATE TABLE `rsp_booking` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_pasien` int(11) DEFAULT NULL,
  `nik` varchar(255) DEFAULT NULL,
  `nama_pasien` varchar(255) DEFAULT NULL,
  `id_poli` int(11) DEFAULT NULL,
  `nama_poli` varchar(255) DEFAULT NULL,
  `kode_booking` varchar(255) DEFAULT NULL,
  `tanggal_booking` varchar(255) DEFAULT NULL,
  `waktu_booking` varchar(255) DEFAULT NULL,
  `tanggal` varchar(255) DEFAULT NULL,
  `waktu` varchar(255) DEFAULT NULL,
  `status_booking` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `rsp_jadwal_dokter`
--

CREATE TABLE `rsp_jadwal_dokter` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_pegawai` int(11) DEFAULT NULL,
  `nama_pegawai` varchar(150) DEFAULT NULL,
  `hari` varchar(50) DEFAULT NULL,
  `jam_mulai` varchar(50) DEFAULT NULL,
  `jam_selesai` varchar(50) DEFAULT NULL,
  `shift` varchar(225) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `rsp_jenis_biaya`
--

CREATE TABLE `rsp_jenis_biaya` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rsp_pemasukan`
--

CREATE TABLE `rsp_pemasukan` (
  `id` int(11) NOT NULL,
  `id_user` varchar(255) DEFAULT NULL,
  `nama_user` varchar(255) DEFAULT NULL,
  `id_jenis_biaya` varchar(255) DEFAULT NULL,
  `nama_jenis_biaya` varchar(255) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `nominal` varchar(255) DEFAULT NULL,
  `tanggal` varchar(255) DEFAULT NULL,
  `waktu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rsp_pembayaran`
--

CREATE TABLE `rsp_pembayaran` (
  `id` int(11) NOT NULL,
  `kode_invoice` varchar(255) DEFAULT NULL,
  `id_pasien` varchar(255) DEFAULT NULL,
  `nik` varchar(255) DEFAULT NULL,
  `nama_pasien` varchar(255) DEFAULT NULL,
  `id_dokter` varchar(255) DEFAULT NULL,
  `nama_dokter` varchar(255) DEFAULT NULL,
  `id_user` varchar(255) DEFAULT NULL,
  `nama_user` varchar(255) DEFAULT NULL,
  `biaya_tindakan` varchar(255) DEFAULT NULL,
  `biaya_resep` varchar(255) DEFAULT NULL,
  `total_invoice` varchar(255) DEFAULT NULL,
  `metode_pembayaran` varchar(255) DEFAULT NULL,
  `bank` varchar(255) DEFAULT NULL,
  `bayar` varchar(255) DEFAULT NULL,
  `kembali` varchar(255) DEFAULT NULL,
  `tanggal` varchar(255) DEFAULT NULL,
  `waktu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rsp_pengeluaran`
--

CREATE TABLE `rsp_pengeluaran` (
  `id` int(11) NOT NULL,
  `id_user` varchar(255) DEFAULT NULL,
  `nama_user` varchar(255) DEFAULT NULL,
  `id_jenis_biaya` varchar(255) DEFAULT NULL,
  `nama_jenis_biaya` varchar(255) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `nominal` varchar(255) DEFAULT NULL,
  `tanggal` varchar(255) DEFAULT NULL,
  `waktu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rsp_registrasi`
--

CREATE TABLE `rsp_registrasi` (
  `id` int(11) UNSIGNED NOT NULL,
  `kode_invoice` varchar(255) DEFAULT NULL,
  `id_pasien` int(11) DEFAULT NULL,
  `nik` varchar(255) DEFAULT NULL,
  `nama_pasien` varchar(255) DEFAULT NULL,
  `tanggal` varchar(100) DEFAULT NULL,
  `waktu` varchar(50) DEFAULT NULL,
  `status_registrasi` varchar(50) DEFAULT NULL,
  `id_booking` int(11) DEFAULT NULL,
  `kode_booking` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contoh`
--
ALTER TABLE `contoh`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contoh_multiple`
--
ALTER TABLE `contoh_multiple`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contoh_multiple_detail`
--
ALTER TABLE `contoh_multiple_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kpg_dokter`
--
ALTER TABLE `kpg_dokter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kpg_jabatan`
--
ALTER TABLE `kpg_jabatan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kpg_pegawai`
--
ALTER TABLE `kpg_pegawai`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mst_diagnosa`
--
ALTER TABLE `mst_diagnosa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mst_pasien`
--
ALTER TABLE `mst_pasien`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `mst_poli`
--
ALTER TABLE `mst_poli`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mst_tindakan`
--
ALTER TABLE `mst_tindakan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pol_anak`
--
ALTER TABLE `pol_anak`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pol_anak_diagnosa`
--
ALTER TABLE `pol_anak_diagnosa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pol_anak_tindakan`
--
ALTER TABLE `pol_anak_tindakan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pol_gigi`
--
ALTER TABLE `pol_gigi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pol_gigi_diagnosa`
--
ALTER TABLE `pol_gigi_diagnosa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pol_gigi_tindakan`
--
ALTER TABLE `pol_gigi_tindakan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pol_kecantikan`
--
ALTER TABLE `pol_kecantikan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pol_kecantikan_detail`
--
ALTER TABLE `pol_kecantikan_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pol_kecantikan_diagnosa`
--
ALTER TABLE `pol_kecantikan_diagnosa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pol_kecantikan_tindakan`
--
ALTER TABLE `pol_kecantikan_tindakan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pol_resep`
--
ALTER TABLE `pol_resep`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pol_resep_obat`
--
ALTER TABLE `pol_resep_obat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pol_resep_racikan`
--
ALTER TABLE `pol_resep_racikan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pol_resep_racikan_detail`
--
ALTER TABLE `pol_resep_racikan_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pol_umum`
--
ALTER TABLE `pol_umum`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pol_umum_diagnosa`
--
ALTER TABLE `pol_umum_diagnosa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pol_umum_tindakan`
--
ALTER TABLE `pol_umum_tindakan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rsp_antrian`
--
ALTER TABLE `rsp_antrian`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rsp_booking`
--
ALTER TABLE `rsp_booking`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rsp_jadwal_dokter`
--
ALTER TABLE `rsp_jadwal_dokter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rsp_jenis_biaya`
--
ALTER TABLE `rsp_jenis_biaya`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `rsp_pemasukan`
--
ALTER TABLE `rsp_pemasukan`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `rsp_pembayaran`
--
ALTER TABLE `rsp_pembayaran`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `rsp_pengeluaran`
--
ALTER TABLE `rsp_pengeluaran`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `rsp_registrasi`
--
ALTER TABLE `rsp_registrasi`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contoh`
--
ALTER TABLE `contoh`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contoh_multiple`
--
ALTER TABLE `contoh_multiple`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contoh_multiple_detail`
--
ALTER TABLE `contoh_multiple_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kpg_dokter`
--
ALTER TABLE `kpg_dokter`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kpg_jabatan`
--
ALTER TABLE `kpg_jabatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kpg_pegawai`
--
ALTER TABLE `kpg_pegawai`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mst_diagnosa`
--
ALTER TABLE `mst_diagnosa`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mst_pasien`
--
ALTER TABLE `mst_pasien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mst_poli`
--
ALTER TABLE `mst_poli`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mst_tindakan`
--
ALTER TABLE `mst_tindakan`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pol_anak`
--
ALTER TABLE `pol_anak`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pol_anak_diagnosa`
--
ALTER TABLE `pol_anak_diagnosa`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pol_anak_tindakan`
--
ALTER TABLE `pol_anak_tindakan`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pol_gigi`
--
ALTER TABLE `pol_gigi`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pol_gigi_diagnosa`
--
ALTER TABLE `pol_gigi_diagnosa`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pol_gigi_tindakan`
--
ALTER TABLE `pol_gigi_tindakan`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pol_kecantikan`
--
ALTER TABLE `pol_kecantikan`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pol_kecantikan_detail`
--
ALTER TABLE `pol_kecantikan_detail`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pol_kecantikan_diagnosa`
--
ALTER TABLE `pol_kecantikan_diagnosa`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pol_kecantikan_tindakan`
--
ALTER TABLE `pol_kecantikan_tindakan`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pol_resep`
--
ALTER TABLE `pol_resep`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pol_resep_obat`
--
ALTER TABLE `pol_resep_obat`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pol_resep_racikan`
--
ALTER TABLE `pol_resep_racikan`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pol_resep_racikan_detail`
--
ALTER TABLE `pol_resep_racikan_detail`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pol_umum`
--
ALTER TABLE `pol_umum`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pol_umum_diagnosa`
--
ALTER TABLE `pol_umum_diagnosa`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pol_umum_tindakan`
--
ALTER TABLE `pol_umum_tindakan`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rsp_antrian`
--
ALTER TABLE `rsp_antrian`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rsp_booking`
--
ALTER TABLE `rsp_booking`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rsp_jadwal_dokter`
--
ALTER TABLE `rsp_jadwal_dokter`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rsp_jenis_biaya`
--
ALTER TABLE `rsp_jenis_biaya`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rsp_pemasukan`
--
ALTER TABLE `rsp_pemasukan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rsp_pembayaran`
--
ALTER TABLE `rsp_pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rsp_pengeluaran`
--
ALTER TABLE `rsp_pengeluaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rsp_registrasi`
--
ALTER TABLE `rsp_registrasi`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
