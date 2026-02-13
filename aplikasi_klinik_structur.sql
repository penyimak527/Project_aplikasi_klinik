/*
 Navicat Premium Data Transfer

 Source Server         : localku
 Source Server Type    : MySQL
 Source Server Version : 80030
 Source Host           : localhost:3306
 Source Schema         : aplikasi_klinik

 Target Server Type    : MySQL
 Target Server Version : 80030
 File Encoding         : 65001

 Date: 13/02/2026 10:53:28
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for adm_grup_hak_akses
-- ----------------------------
DROP TABLE IF EXISTS `adm_grup_hak_akses`;
CREATE TABLE `adm_grup_hak_akses`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_grup_hak_akses` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 22 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for adm_hak_akses
-- ----------------------------
DROP TABLE IF EXISTS `adm_hak_akses`;
CREATE TABLE `adm_hak_akses`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_hak_akses` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `id_grup_hak_akses` int NULL DEFAULT NULL,
  `nama_grup_hak_akses` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 42 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for adm_level
-- ----------------------------
DROP TABLE IF EXISTS `adm_level`;
CREATE TABLE `adm_level`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_level` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for adm_level_akses
-- ----------------------------
DROP TABLE IF EXISTS `adm_level_akses`;
CREATE TABLE `adm_level_akses`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_level` int NOT NULL,
  `id_hak_akses` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 382 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for adm_user
-- ----------------------------
DROP TABLE IF EXISTS `adm_user`;
CREATE TABLE `adm_user`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_pegawai` int NULL DEFAULT NULL,
  `nama_pegawai` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `id_level` int NULL DEFAULT NULL,
  `nama_level` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` enum('Aktif','Nonaktif') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for apt_barang
-- ----------------------------
DROP TABLE IF EXISTS `apt_barang`;
CREATE TABLE `apt_barang`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_jenis_barang` int UNSIGNED NULL DEFAULT NULL,
  `nama_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_apt_barang_id_jenis_barang`(`id_jenis_barang`) USING BTREE,
  CONSTRAINT `fk_apt_barang_id_jenis_barang` FOREIGN KEY (`id_jenis_barang`) REFERENCES `apt_jenis_barang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 43 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for apt_barang_detail
-- ----------------------------
DROP TABLE IF EXISTS `apt_barang_detail`;
CREATE TABLE `apt_barang_detail`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_barang` int UNSIGNED NULL DEFAULT NULL,
  `kode_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nama_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_satuan_barang` int UNSIGNED NULL DEFAULT NULL,
  `satuan_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `isi_satuan_turunan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `urutan_satuan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_apt_barang_detail_id_barang`(`id_barang`) USING BTREE,
  INDEX `fk_apt_barang_detail_id_satuan_barang`(`id_satuan_barang`) USING BTREE,
  CONSTRAINT `fk_apt_barang_detail_id_barang` FOREIGN KEY (`id_barang`) REFERENCES `apt_barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_apt_barang_detail_id_satuan_barang` FOREIGN KEY (`id_satuan_barang`) REFERENCES `apt_satuan_barang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 158 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for apt_faktur
-- ----------------------------
DROP TABLE IF EXISTS `apt_faktur`;
CREATE TABLE `apt_faktur`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user` int UNSIGNED NULL DEFAULT NULL,
  `nama_user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_supplier` int UNSIGNED NULL DEFAULT NULL,
  `nama_supplier` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `no_faktur` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `total_harga` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `status_bayar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tanggal_bayar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `metode_pembayaran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `bank` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `bayar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `status_diskon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `jenis_diskon` enum('persen','rupiah') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `diskon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tanggal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `waktu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_apt_faktur_id_user`(`id_user`) USING BTREE,
  INDEX `fk_apt_faktur_id_supplier`(`id_supplier`) USING BTREE,
  CONSTRAINT `fk_apt_faktur_id_supplier` FOREIGN KEY (`id_supplier`) REFERENCES `apt_supplier` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_apt_faktur_id_user` FOREIGN KEY (`id_user`) REFERENCES `kpg_pegawai` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for apt_faktur_detail
-- ----------------------------
DROP TABLE IF EXISTS `apt_faktur_detail`;
CREATE TABLE `apt_faktur_detail`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_faktur` int UNSIGNED NULL DEFAULT NULL,
  `id_barang` int UNSIGNED NULL DEFAULT NULL,
  `id_barang_detail` int NULL DEFAULT NULL,
  `nama_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_satuan_barang` int UNSIGNED NULL DEFAULT NULL,
  `satuan_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `urutan_satuan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `jumlah` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `harga_awal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sub_total_harga_awal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `harga_jual` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `laba` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `kadaluarsa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_apt_faktur_detail_id_faktur`(`id_faktur`) USING BTREE,
  INDEX `fk_apt_faktur_detail_id_barang`(`id_barang`) USING BTREE,
  INDEX `fk_apt_faktur_detail_id_barang_detail`(`id_barang_detail`) USING BTREE,
  INDEX `fk_apt_faktur_detail_id_satuan_barang`(`id_satuan_barang`) USING BTREE,
  CONSTRAINT `fk_apt_faktur_detail_id_barang` FOREIGN KEY (`id_barang`) REFERENCES `apt_barang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_apt_faktur_detail_id_barang_detail` FOREIGN KEY (`id_barang_detail`) REFERENCES `apt_barang_detail` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_apt_faktur_detail_id_faktur` FOREIGN KEY (`id_faktur`) REFERENCES `apt_faktur` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_apt_faktur_detail_id_satuan_barang` FOREIGN KEY (`id_satuan_barang`) REFERENCES `apt_satuan_barang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 197 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for apt_faktur_pelunasan
-- ----------------------------
DROP TABLE IF EXISTS `apt_faktur_pelunasan`;
CREATE TABLE `apt_faktur_pelunasan`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_faktur` int UNSIGNED NOT NULL,
  `id_user` int NULL DEFAULT NULL,
  `nama_user` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `bayar` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `tanggal` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `waktu` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `metode_bayar` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `bank` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_pelunasan_ke_faktur`(`id_faktur`) USING BTREE,
  CONSTRAINT `fk_pelunasan_ke_faktur` FOREIGN KEY (`id_faktur`) REFERENCES `apt_faktur` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for apt_jenis_barang
-- ----------------------------
DROP TABLE IF EXISTS `apt_jenis_barang`;
CREATE TABLE `apt_jenis_barang`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_jenis` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for apt_pelanggan
-- ----------------------------
DROP TABLE IF EXISTS `apt_pelanggan`;
CREATE TABLE `apt_pelanggan`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_customer` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `umur` int NULL DEFAULT NULL,
  `no_telp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for apt_retur
-- ----------------------------
DROP TABLE IF EXISTS `apt_retur`;
CREATE TABLE `apt_retur`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_user` int UNSIGNED NULL DEFAULT NULL,
  `nama_user` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `id_faktur` int UNSIGNED NULL DEFAULT NULL,
  `kode_retur` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `tanggal` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `waktu` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_apt_retur_id_user`(`id_user`) USING BTREE,
  INDEX `fk_apt_retur_id_faktur`(`id_faktur`) USING BTREE,
  CONSTRAINT `fk_apt_retur_id_faktur` FOREIGN KEY (`id_faktur`) REFERENCES `apt_faktur` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_apt_retur_id_user` FOREIGN KEY (`id_user`) REFERENCES `kpg_pegawai` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for apt_retur_detail
-- ----------------------------
DROP TABLE IF EXISTS `apt_retur_detail`;
CREATE TABLE `apt_retur_detail`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_retur` int NULL DEFAULT NULL,
  `id_barang` int UNSIGNED NULL DEFAULT NULL,
  `id_barang_detail` int NULL DEFAULT NULL,
  `nama_barang` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `jumlah_beli` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `jumlah_retur` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_apt_retur_detail_id_retur`(`id_retur`) USING BTREE,
  INDEX `fk_apt_retur_detail_id_barang`(`id_barang`) USING BTREE,
  INDEX `fk_apt_retur_detail_id_barang_detail`(`id_barang_detail`) USING BTREE,
  CONSTRAINT `fk_apt_retur_detail_id_barang` FOREIGN KEY (`id_barang`) REFERENCES `apt_barang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_apt_retur_detail_id_barang_detail` FOREIGN KEY (`id_barang_detail`) REFERENCES `apt_barang_detail` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_apt_retur_detail_id_retur` FOREIGN KEY (`id_retur`) REFERENCES `apt_retur` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 38 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for apt_satuan_barang
-- ----------------------------
DROP TABLE IF EXISTS `apt_satuan_barang`;
CREATE TABLE `apt_satuan_barang`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_satuan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for apt_stok
-- ----------------------------
DROP TABLE IF EXISTS `apt_stok`;
CREATE TABLE `apt_stok`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_barang` int UNSIGNED NULL DEFAULT NULL,
  `id_barang_detail` int NULL DEFAULT NULL,
  `stok` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `harga_awal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `harga_jual` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `laba` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `kadaluarsa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_apt_stok_id_barang`(`id_barang`) USING BTREE,
  INDEX `fk_apt_stok_id_barang_detail`(`id_barang_detail`) USING BTREE,
  CONSTRAINT `fk_apt_stok_id_barang` FOREIGN KEY (`id_barang`) REFERENCES `apt_barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_apt_stok_id_barang_detail` FOREIGN KEY (`id_barang_detail`) REFERENCES `apt_barang_detail` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 237 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for apt_supplier
-- ----------------------------
DROP TABLE IF EXISTS `apt_supplier`;
CREATE TABLE `apt_supplier`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_supplier` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nama_supplier` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `alamat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `no_telp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `no_rek` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `bank` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for apt_transaksi
-- ----------------------------
DROP TABLE IF EXISTS `apt_transaksi`;
CREATE TABLE `apt_transaksi`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user` int UNSIGNED NULL DEFAULT NULL,
  `id_pelanggan` int NULL DEFAULT NULL,
  `nama_user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `kode_invoice` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nama_customer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `total_invoice` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `metode_pembayaran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `bank` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `bayar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `kembali` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tanggal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `waktu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_apt_transaksi_id_user`(`id_user`) USING BTREE,
  INDEX `id_pelanggan`(`id_pelanggan`) USING BTREE,
  CONSTRAINT `apt_transaksi_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `apt_pelanggan` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_apt_transaksi_id_user` FOREIGN KEY (`id_user`) REFERENCES `kpg_pegawai` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for apt_transaksi_detail
-- ----------------------------
DROP TABLE IF EXISTS `apt_transaksi_detail`;
CREATE TABLE `apt_transaksi_detail`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_transaksi` int UNSIGNED NULL DEFAULT NULL,
  `id_barang` int UNSIGNED NULL DEFAULT NULL,
  `id_barang_detail` int NULL DEFAULT NULL,
  `nama_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_satuan_barang` int UNSIGNED NULL DEFAULT NULL,
  `satuan_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `urutan_satuan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `jumlah` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `harga` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `laba` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sub_total_harga` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sub_total_laba` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_apt_transaksi_detail_id_transaksi`(`id_transaksi`) USING BTREE,
  INDEX `fk_apt_transaksi_detail_id_barang`(`id_barang`) USING BTREE,
  INDEX `fk_apt_transaksi_detail_id_barang_detail`(`id_barang_detail`) USING BTREE,
  INDEX `fk_apt_transaksi_detail_id_satuan_barang`(`id_satuan_barang`) USING BTREE,
  CONSTRAINT `fk_apt_transaksi_detail_id_barang` FOREIGN KEY (`id_barang`) REFERENCES `apt_barang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_apt_transaksi_detail_id_barang_detail` FOREIGN KEY (`id_barang_detail`) REFERENCES `apt_barang_detail` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_apt_transaksi_detail_id_satuan_barang` FOREIGN KEY (`id_satuan_barang`) REFERENCES `apt_satuan_barang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_apt_transaksi_detail_id_transaksi` FOREIGN KEY (`id_transaksi`) REFERENCES `apt_transaksi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 104 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for apt_transaksi_resep
-- ----------------------------
DROP TABLE IF EXISTS `apt_transaksi_resep`;
CREATE TABLE `apt_transaksi_resep`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user` int UNSIGNED NULL DEFAULT NULL,
  `nama_user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `kode_invoice_registrasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_pasian` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nama_pasien` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_dokter` int UNSIGNED NULL DEFAULT NULL,
  `nama_dokter` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `total_invoice` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `metode_pembayaran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `bank` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `bayar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `kembali` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tanggal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `waktu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_apt_transaksi_resep_id_user`(`id_user`) USING BTREE,
  INDEX `fk_apt_transaksi_resep_id_dokter`(`id_dokter`) USING BTREE,
  CONSTRAINT `fk_apt_transaksi_resep_id_user` FOREIGN KEY (`id_user`) REFERENCES `kpg_pegawai` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for apt_transaksi_resep_obat
-- ----------------------------
DROP TABLE IF EXISTS `apt_transaksi_resep_obat`;
CREATE TABLE `apt_transaksi_resep_obat`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_transaksi_resep` int UNSIGNED NULL DEFAULT NULL,
  `id_barang` int UNSIGNED NULL DEFAULT NULL,
  `id_barang_detail` int NULL DEFAULT NULL,
  `nama_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_satuan_barang` int UNSIGNED NULL DEFAULT NULL,
  `satuan_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `urutan_satuan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `jumlah` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `harga` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `laba` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sub_total_harga` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sub_total_laba` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `aturan_pakai` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_apt_transaksi_resep_obat_id_transaksi_resep`(`id_transaksi_resep`) USING BTREE,
  INDEX `fk_apt_transaksi_resep_obat_id_barang`(`id_barang`) USING BTREE,
  INDEX `fk_apt_transaksi_resep_obat_id_barang_detail`(`id_barang_detail`) USING BTREE,
  INDEX `fk_apt_transaksi_resep_obat_id_satuan_barang`(`id_satuan_barang`) USING BTREE,
  CONSTRAINT `fk_apt_transaksi_resep_obat_id_barang` FOREIGN KEY (`id_barang`) REFERENCES `apt_barang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_apt_transaksi_resep_obat_id_barang_detail` FOREIGN KEY (`id_barang_detail`) REFERENCES `apt_barang_detail` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_apt_transaksi_resep_obat_id_satuan_barang` FOREIGN KEY (`id_satuan_barang`) REFERENCES `apt_satuan_barang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_apt_transaksi_resep_obat_id_transaksi_resep` FOREIGN KEY (`id_transaksi_resep`) REFERENCES `apt_transaksi_resep` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 74 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for apt_transaksi_resep_racikan
-- ----------------------------
DROP TABLE IF EXISTS `apt_transaksi_resep_racikan`;
CREATE TABLE `apt_transaksi_resep_racikan`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_transaksi_resep` int UNSIGNED NULL DEFAULT NULL,
  `nama_racikan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `jumlah` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `keterangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `harga` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sub_total_harga` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `laba` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sub_total_laba` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `aturan_pakai` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_apt_transaksi_resep_racikan_id_transaksi_resep`(`id_transaksi_resep`) USING BTREE,
  CONSTRAINT `fk_apt_transaksi_resep_racikan_id_transaksi_resep` FOREIGN KEY (`id_transaksi_resep`) REFERENCES `apt_transaksi_resep` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for apt_transaksi_resep_racikan_detail
-- ----------------------------
DROP TABLE IF EXISTS `apt_transaksi_resep_racikan_detail`;
CREATE TABLE `apt_transaksi_resep_racikan_detail`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_transaksi_resep_racikan` int UNSIGNED NULL DEFAULT NULL,
  `id_barang` int UNSIGNED NULL DEFAULT NULL,
  `id_barang_detail` int NULL DEFAULT NULL,
  `nama_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_satuan_barang` int UNSIGNED NULL DEFAULT NULL,
  `satuan_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `urutan_satuan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `jumlah` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `harga` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sub_total_harga` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `laba` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sub_total_laba` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_apt_transaksi_resep_racikan_detail_id_transaksi_resep_racikan`(`id_transaksi_resep_racikan`) USING BTREE,
  INDEX `fk_apt_transaksi_resep_racikan_detail_id_barang`(`id_barang`) USING BTREE,
  INDEX `fk_apt_transaksi_resep_racikan_detail_id_barang_detail`(`id_barang_detail`) USING BTREE,
  INDEX `fk_apt_transaksi_resep_racikan_detail_id_satuan_barang`(`id_satuan_barang`) USING BTREE,
  CONSTRAINT `fk_apt_transaksi_resep_racikan_detail_id_barang` FOREIGN KEY (`id_barang`) REFERENCES `apt_barang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_apt_transaksi_resep_racikan_detail_id_barang_detail` FOREIGN KEY (`id_barang_detail`) REFERENCES `apt_barang_detail` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_apt_transaksi_resep_racikan_detail_id_satuan_barang` FOREIGN KEY (`id_satuan_barang`) REFERENCES `apt_satuan_barang` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_apt_transaksi_resep_racikan_detail_id_transaksi_resep_racikan` FOREIGN KEY (`id_transaksi_resep_racikan`) REFERENCES `apt_transaksi_resep_racikan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 92 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for contoh
-- ----------------------------
DROP TABLE IF EXISTS `contoh`;
CREATE TABLE `contoh`  (
  `id` int NOT NULL,
  `contoh` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for contoh_multiple
-- ----------------------------
DROP TABLE IF EXISTS `contoh_multiple`;
CREATE TABLE `contoh_multiple`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `contoh` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for contoh_multiple_detail
-- ----------------------------
DROP TABLE IF EXISTS `contoh_multiple_detail`;
CREATE TABLE `contoh_multiple_detail`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_contoh_multiple` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `contoh_multiple` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kpg_dokter
-- ----------------------------
DROP TABLE IF EXISTS `kpg_dokter`;
CREATE TABLE `kpg_dokter`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pegawai` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nama_pegawai` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_poli` int NULL DEFAULT NULL,
  `nama_poli` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 85 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kpg_jabatan
-- ----------------------------
DROP TABLE IF EXISTS `kpg_jabatan`;
CREATE TABLE `kpg_jabatan`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kpg_pegawai
-- ----------------------------
DROP TABLE IF EXISTS `kpg_pegawai`;
CREATE TABLE `kpg_pegawai`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_jabatan` int NULL DEFAULT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `no_telp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nama_jabatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `alamat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for mst_diagnosa
-- ----------------------------
DROP TABLE IF EXISTS `mst_diagnosa`;
CREATE TABLE `mst_diagnosa`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_diagnosa` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_poli` int NULL DEFAULT NULL,
  `nama_poli` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 110 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for mst_pasien
-- ----------------------------
DROP TABLE IF EXISTS `mst_pasien`;
CREATE TABLE `mst_pasien`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `no_rm` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nama_pasien` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nik` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `jenis_kelamin` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `tanggal_lahir` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `umur` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `alamat` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `pekerjaan` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `no_telp` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `status_perkawinan` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nama_wali` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `golongan_darah` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `alergi` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `status_operasi` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `username` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `password` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 198 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for mst_poli
-- ----------------------------
DROP TABLE IF EXISTS `mst_poli`;
CREATE TABLE `mst_poli`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 45 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for mst_tindakan
-- ----------------------------
DROP TABLE IF EXISTS `mst_tindakan`;
CREATE TABLE `mst_tindakan`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `harga` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_poli` int NULL DEFAULT NULL,
  `nama_poli` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 141 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pol_anak
-- ----------------------------
DROP TABLE IF EXISTS `pol_anak`;
CREATE TABLE `pol_anak`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_invoice` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_pasien` int NULL DEFAULT NULL,
  `nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nama_pasien` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_dokter` int NULL DEFAULT NULL,
  `nama_dokter` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `keluhan` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `berat_badan` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tinggi_badan` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `suhu` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `status_imunisasi` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `catatan` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tanggal` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `waktu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pol_anak_diagnosa
-- ----------------------------
DROP TABLE IF EXISTS `pol_anak_diagnosa`;
CREATE TABLE `pol_anak_diagnosa`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_rm_anak` int NULL DEFAULT NULL,
  `id_diagnosa` int NULL DEFAULT NULL,
  `diagnosa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pol_anak_tindakan
-- ----------------------------
DROP TABLE IF EXISTS `pol_anak_tindakan`;
CREATE TABLE `pol_anak_tindakan`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pol_anak` int NULL DEFAULT NULL,
  `id_tindakan` int NULL DEFAULT NULL,
  `tindakan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `harga` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pol_gigi
-- ----------------------------
DROP TABLE IF EXISTS `pol_gigi`;
CREATE TABLE `pol_gigi`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_invoice` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_pasien` int NULL DEFAULT NULL,
  `nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nama_pasien` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_dokter` int NULL DEFAULT NULL,
  `nama_dokter` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `keluhan` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `catatan` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tanggal` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `waktu` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pol_gigi_diagnosa
-- ----------------------------
DROP TABLE IF EXISTS `pol_gigi_diagnosa`;
CREATE TABLE `pol_gigi_diagnosa`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pol_gigi` int NULL DEFAULT NULL,
  `id_diagnosa` int NULL DEFAULT NULL,
  `diagnosa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pol_gigi_tindakan
-- ----------------------------
DROP TABLE IF EXISTS `pol_gigi_tindakan`;
CREATE TABLE `pol_gigi_tindakan`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pol_gigi` int NULL DEFAULT NULL,
  `id_tindakan` int NULL DEFAULT NULL,
  `tindakan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `harga` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pol_kecantikan
-- ----------------------------
DROP TABLE IF EXISTS `pol_kecantikan`;
CREATE TABLE `pol_kecantikan`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_invoice` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_pasien` int NULL DEFAULT NULL,
  `nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nama_pasien` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_dokter` int NULL DEFAULT NULL,
  `nama_dokter` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `keluhan` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `jenis_treatment` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `riwayat_alergi` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `produk_digunakan` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `hasil_perawatan` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tanggal` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `waktu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 180 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pol_kecantikan_detail
-- ----------------------------
DROP TABLE IF EXISTS `pol_kecantikan_detail`;
CREATE TABLE `pol_kecantikan_detail`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pol_kecantikan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_pasien` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 131 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pol_kecantikan_diagnosa
-- ----------------------------
DROP TABLE IF EXISTS `pol_kecantikan_diagnosa`;
CREATE TABLE `pol_kecantikan_diagnosa`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pol_kecantikan` int NULL DEFAULT NULL,
  `id_diagnosa` int NULL DEFAULT NULL,
  `diagnosa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 419 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pol_kecantikan_tindakan
-- ----------------------------
DROP TABLE IF EXISTS `pol_kecantikan_tindakan`;
CREATE TABLE `pol_kecantikan_tindakan`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pol_kecantikan` int NULL DEFAULT NULL,
  `id_tindakan` int NULL DEFAULT NULL,
  `tindakan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `harga` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 425 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pol_resep
-- ----------------------------
DROP TABLE IF EXISTS `pol_resep`;
CREATE TABLE `pol_resep`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_invoice` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `kode_resep` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_pasien` int NULL DEFAULT NULL,
  `nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nama_pasien` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_dokter` int NULL DEFAULT NULL,
  `total_harga` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tanggal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `waktu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nama_dokter` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 128 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pol_resep_obat
-- ----------------------------
DROP TABLE IF EXISTS `pol_resep_obat`;
CREATE TABLE `pol_resep_obat`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pol_resep` int NULL DEFAULT NULL,
  `id_barang` int NULL DEFAULT NULL,
  `id_barang_detail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nama_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_satuan_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `satuan_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `urutan_satuan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `jumlah` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `harga` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `aturan_pakai` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sub_total_harga` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `laba` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sub_total_laba` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 219 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pol_resep_racikan
-- ----------------------------
DROP TABLE IF EXISTS `pol_resep_racikan`;
CREATE TABLE `pol_resep_racikan`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pol_resep` int NULL DEFAULT NULL,
  `nama_racikan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `jumlah` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `keterangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `harga` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sub_total_harga` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `laba` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sub_total_laba` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `aturan_pakai` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 104 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pol_resep_racikan_detail
-- ----------------------------
DROP TABLE IF EXISTS `pol_resep_racikan_detail`;
CREATE TABLE `pol_resep_racikan_detail`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pol_resep_racikan` int NULL DEFAULT NULL,
  `id_barang` int NULL DEFAULT NULL,
  `id_barang_detail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nama_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_satuan_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `satuan_barang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `urutan_satuan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `jumlah` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `harga` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sub_total_harga` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `laba` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sub_total_laba` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 222 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pol_umum
-- ----------------------------
DROP TABLE IF EXISTS `pol_umum`;
CREATE TABLE `pol_umum`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_invoice` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_pasien` int NULL DEFAULT NULL,
  `nik` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nama_pasien` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_dokter` int NULL DEFAULT NULL,
  `nama_dokter` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `keluhan` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tekanan_darah` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `suhu` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nadi` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `catatan` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tanggal` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `waktu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 33 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pol_umum_diagnosa
-- ----------------------------
DROP TABLE IF EXISTS `pol_umum_diagnosa`;
CREATE TABLE `pol_umum_diagnosa`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pol_umum` int NULL DEFAULT NULL,
  `id_diagnosa` int NULL DEFAULT NULL,
  `diagnosa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pol_umum_tindakan
-- ----------------------------
DROP TABLE IF EXISTS `pol_umum_tindakan`;
CREATE TABLE `pol_umum_tindakan`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pol_umum` int NULL DEFAULT NULL,
  `id_tindakan` int NULL DEFAULT NULL,
  `tindakan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `harga` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 28 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for rsp_antrian
-- ----------------------------
DROP TABLE IF EXISTS `rsp_antrian`;
CREATE TABLE `rsp_antrian`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_invoice` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `no_antrian` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_poli` int NULL DEFAULT NULL,
  `nama_poli` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_dokter` int NULL DEFAULT NULL,
  `tanggal_antri` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `waktu_antri` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tanggal_dipanggil` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `waktu_dipanggil` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `lama_menunggu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `status_antrian` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tanggal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `waktu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 381 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for rsp_booking
-- ----------------------------
DROP TABLE IF EXISTS `rsp_booking`;
CREATE TABLE `rsp_booking`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pasien` int NULL DEFAULT NULL,
  `nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nama_pasien` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_poli` int NULL DEFAULT NULL,
  `nama_poli` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_dokter` int NULL DEFAULT NULL,
  `nama_dokter` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `kode_booking` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tanggal_booking` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `waktu_booking` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tanggal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `waktu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `status_booking` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for rsp_jadwal_dokter
-- ----------------------------
DROP TABLE IF EXISTS `rsp_jadwal_dokter`;
CREATE TABLE `rsp_jadwal_dokter`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pegawai` int NULL DEFAULT NULL,
  `nama_pegawai` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `hari` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `jam_mulai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `jam_selesai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 176 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for rsp_jenis_biaya
-- ----------------------------
DROP TABLE IF EXISTS `rsp_jenis_biaya`;
CREATE TABLE `rsp_jenis_biaya`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for rsp_pemasukan
-- ----------------------------
DROP TABLE IF EXISTS `rsp_pemasukan`;
CREATE TABLE `rsp_pemasukan`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_user` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nama_user` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `id_jenis_biaya` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nama_jenis_biaya` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `keterangan` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nominal` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `tanggal` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `waktu` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for rsp_pembayaran
-- ----------------------------
DROP TABLE IF EXISTS `rsp_pembayaran`;
CREATE TABLE `rsp_pembayaran`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_invoice` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `id_pasien` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nik` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nama_pasien` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `id_dokter` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nama_dokter` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `id_user` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nama_user` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `biaya_tindakan` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `biaya_resep` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `total_invoice` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `metode_pembayaran` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `bank` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `bayar` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `kembali` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `tanggal` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `waktu` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 99 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for rsp_pengeluaran
-- ----------------------------
DROP TABLE IF EXISTS `rsp_pengeluaran`;
CREATE TABLE `rsp_pengeluaran`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_user` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nama_user` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `id_jenis_biaya` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nama_jenis_biaya` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `keterangan` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nominal` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `tanggal` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `waktu` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for rsp_registrasi
-- ----------------------------
DROP TABLE IF EXISTS `rsp_registrasi`;
CREATE TABLE `rsp_registrasi`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_invoice` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_dokter` int NULL DEFAULT NULL,
  `nama_dokter` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `id_poli` int NULL DEFAULT NULL,
  `nama_poli` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `id_pasien` int NULL DEFAULT NULL,
  `nik` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nama_pasien` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tanggal` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `waktu` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `status_registrasi` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_booking` int NULL DEFAULT NULL,
  `kode_booking` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 466 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
