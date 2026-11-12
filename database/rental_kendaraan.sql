-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Nov 07, 2026 at 04:25 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rental_kendaraan`
--
CREATE DATABASE IF NOT EXISTS `rental_kendaraan` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `rental_kendaraan`;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

DROP TABLE IF EXISTS `kategori`;
CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`, `deskripsi`) VALUES
(1, 'Sepeda', 'Sepeda kayuh untuk keliling kota dan kampus'),
(2, 'Skuter Listrik', 'Skuter bertenaga baterai, cocok untuk jarak menengah'),
(3, 'Sepeda Listrik', 'Sepeda dengan bantuan motor listrik, hemat tenaga'),
(4, 'Sepeda Lipat', 'Sepeda lipat praktis dibawa ke mana saja');

-- --------------------------------------------------------

--
-- Table structure for table `kendaraan`
--

DROP TABLE IF EXISTS `kendaraan`;
CREATE TABLE `kendaraan` (
  `id` int(11) NOT NULL,
  `kategori_id` int(11) NOT NULL,
  `lokasi_id` int(11) NOT NULL,
  `nama_kendaraan` varchar(100) NOT NULL,
  `harga_per_jam` int(11) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kendaraan`
--

INSERT INTO `kendaraan` (`id`, `kategori_id`, `lokasi_id`, `nama_kendaraan`, `harga_per_jam`, `stok`) VALUES
(1, 1, 1, 'Sepeda Gunung Polygon Cascade', 10000, 5),
(2, 1, 2, 'Sepeda Gunung United Miami', 12000, 4),
(3, 1, 3, 'Sepeda Fixie Pacific', 8000, 4),
(4, 2, 1, 'Skuter Listrik Xiaomi M365', 25000, 3),
(5, 2, 3, 'Skuter Listrik Ninebot Max', 30000, 2),
(6, 2, 4, 'Skuter Listrik Segway ES2', 28000, 3),
(7, 3, 2, 'Sepeda Listrik Selis Mini', 35000, 3),
(8, 3, 5, 'Sepeda Listrik Exotic Rider', 40000, 1),
(9, 4, 4, 'Sepeda Lipat Element Pikes', 12000, 4),
(10, 4, 5, 'Sepeda Lipat Polygon Urbano', 14000, 3),
(11, 1, 5, 'Sepeda City Bike Federal', 9000, 4),
(12, 2, 2, 'Skuter Listrik Navee N65', 32000, 1);

-- --------------------------------------------------------

--
-- Table structure for table `lokasi`
--

DROP TABLE IF EXISTS `lokasi`;
CREATE TABLE `lokasi` (
  `id` int(11) NOT NULL,
  `nama_lokasi` varchar(100) NOT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `kota` varchar(50) DEFAULT 'Bandung'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lokasi`
--

INSERT INTO `lokasi` (`id`, `nama_lokasi`, `alamat`, `kota`) VALUES
(1, 'Kampus ITENAS', 'Jl. PHH. Mustofa No.23', 'Bandung'),
(2, 'Dago', 'Jl. Ir. H. Juanda', 'Bandung'),
(3, 'Cihampelas', 'Jl. Cihampelas', 'Bandung'),
(4, 'Alun-Alun Bandung', 'Jl. Asia Afrika', 'Bandung'),
(5, 'Braga', 'Jl. Braga', 'Bandung');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

DROP TABLE IF EXISTS `pembayaran`;
CREATE TABLE `pembayaran` (
  `id` int(11) NOT NULL,
  `penyewaan_id` int(11) NOT NULL,
  `metode` enum('tunai','transfer','e-wallet') NOT NULL,
  `jumlah_bayar` int(11) NOT NULL,
  `tgl_bayar` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `penyewaan_id`, `metode`, `jumlah_bayar`, `tgl_bayar`) VALUES
(1, 1, 'tunai', 20000, '2026-10-28 09:00:00'),
(2, 2, 'e-wallet', 75000, '2026-10-29 10:30:00'),
(3, 3, 'transfer', 12000, '2026-10-30 14:00:00'),
(4, 4, 'e-wallet', 140000, '2026-10-31 08:00:00'),
(5, 5, 'tunai', 24000, '2026-11-01 13:00:00'),
(6, 6, 'transfer', 60000, '2026-11-02 15:30:00'),
(7, 7, 'tunai', 24000, '2026-11-03 09:15:00'),
(8, 8, 'e-wallet', 28000, '2026-11-04 16:00:00'),
(9, 9, 'transfer', 84000, '2026-11-06 10:00:00'),
(10, 10, 'e-wallet', 160000, '2026-11-07 08:30:00'),
(11, 11, 'transfer', 64000, '2026-11-07 09:45:00'),
(12, 12, 'tunai', 27000, '2026-11-07 10:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `penyewaan`
--

DROP TABLE IF EXISTS `penyewaan`;
CREATE TABLE `penyewaan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `kendaraan_id` int(11) NOT NULL,
  `jam_mulai` datetime NOT NULL,
  `durasi_jam` int(11) NOT NULL,
  `total_harga` int(11) NOT NULL,
  `status` enum('berjalan','selesai') DEFAULT 'berjalan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penyewaan`
--

INSERT INTO `penyewaan` (`id`, `user_id`, `kendaraan_id`, `jam_mulai`, `durasi_jam`, `total_harga`, `status`) VALUES
(1, 2, 1, '2026-10-28 09:00:00', 2, 20000, 'selesai'),
(2, 3, 4, '2026-10-29 10:30:00', 3, 75000, 'selesai'),
(3, 4, 9, '2026-10-30 14:00:00', 1, 12000, 'selesai'),
(4, 5, 7, '2026-10-31 08:00:00', 4, 140000, 'selesai'),
(5, 6, 2, '2026-11-01 13:00:00', 2, 24000, 'selesai'),
(6, 7, 5, '2026-11-02 15:30:00', 2, 60000, 'selesai'),
(7, 8, 3, '2026-11-03 09:15:00', 3, 24000, 'selesai'),
(8, 9, 10, '2026-11-04 16:00:00', 2, 28000, 'selesai'),
(9, 10, 6, '2026-11-06 10:00:00', 3, 84000, 'selesai'),
(10, 11, 8, '2026-11-07 08:30:00', 4, 160000, 'berjalan'),
(11, 2, 12, '2026-11-07 09:45:00', 2, 64000, 'berjalan'),
(12, 5, 11, '2026-11-07 10:15:00', 3, 27000, 'berjalan');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`) VALUES
(1, 'Faris', 'faris', '$2y$10$2rSPMlzA6QNSqmrM4cwaiOEq1l41q/sxdAPQrBMGstoGqjMR5y30W', 'admin'),
(2, 'Adhi', 'adhi', '$2y$10$Di5n0FWS/NnAByiP9cBp5uutgKWaynQap3LLdz5tW6a8fLRdOYvVS', 'user'),
(3, 'Bambang', 'bambang', '$2y$10$IoXvlXm19kwGb3uOc3BkYeJyXcuXDftDT5KXPA/iTkcycQe6Vs6GW', 'user'),
(4, 'Bayu', 'bayu', '$2y$10$9.5X9r5oRMsbmYMorJensur9BgJth8KOQjEBrMfQYw96QGVGuFQdy', 'user'),
(5, 'Zama', 'zama', '$2y$10$EpW5KoVlp0xecEv9aapLD.9zCNMqz8b2V0foHL72dbrfTRN3P/AwS', 'user'),
(6, 'Radit', 'radit', '$2y$10$c6JRyrbGoe6fIwQnvkS2TOpqKxEn/5nSjlS32VxYkAg9W04C7.O4i', 'user'),
(7, 'Idham', 'idham', '$2y$10$egLILiu3rEo/3Xh8hotaq.2KOSxuqltbiy1emOeA2wJJ./KXmndyi', 'user'),
(8, 'Rafi', 'rafi', '$2y$10$vxXIA811aTz/L07lNOwp3efaSx8X1p5YYZfE0frdPKW6wGmozsvfm', 'user'),
(9, 'Nizar', 'nizar', '$2y$10$r//xAyP42FFpHix8lBbYweULejwdvfrYoKFi8tRoCdrTaL3PkYmY2', 'user'),
(10, 'Ilham', 'ilham', '$2y$10$nKrd5wrHXjnHfcSLndKE3O0JtDXo8tWyxHqiJpp6FRwIxMROrIiX.', 'user'),
(11, 'Nadhif', 'nadhif', '$2y$10$fnwRqkcmyDHcSzf0AITCA.IvQeejJoFpaMpgMh5rKxXLB1KP/g5wi', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kategori_id` (`kategori_id`),
  ADD KEY `lokasi_id` (`lokasi_id`);

--
-- Indexes for table `lokasi`
--
ALTER TABLE `lokasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penyewaan_id` (`penyewaan_id`);

--
-- Indexes for table `penyewaan`
--
ALTER TABLE `penyewaan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `kendaraan_id` (`kendaraan_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kendaraan`
--
ALTER TABLE `kendaraan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `lokasi`
--
ALTER TABLE `lokasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `penyewaan`
--
ALTER TABLE `penyewaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD CONSTRAINT `kendaraan_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`),
  ADD CONSTRAINT `kendaraan_ibfk_2` FOREIGN KEY (`lokasi_id`) REFERENCES `lokasi` (`id`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`penyewaan_id`) REFERENCES `penyewaan` (`id`);

--
-- Constraints for table `penyewaan`
--
ALTER TABLE `penyewaan`
  ADD CONSTRAINT `penyewaan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `penyewaan_ibfk_2` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
