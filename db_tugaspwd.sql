-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 05, 2023 at 05:20 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_tugaspwd`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `harga` int(11) NOT NULL,
  `satuan` varchar(100) NOT NULL,
  `stok` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id`, `nama`, `harga`, `satuan`, `stok`, `id_kategori`) VALUES
(1, 'Whiskas', 7000, 'gram', 10, 1),
(2, 'BR', 8000, 'kg', 11, 2),
(3, 'Katul', 4000, 'kg', 9, 2),
(4, 'Pisang', 1000, 'pcs', 5, 3),
(5, 'Beras Merah', 15000, 'kg', 5, 2),
(6, 'AD1', 10000, 'kg', 8, 3),
(7, 'AD2', 10000, 'kg', 9, 3),
(8, 'Jagung Giling', 12000, 'kg', 6, 2),
(9, 'Milet putih', 9000, 'kg', 5, 3),
(10, 'Kenari Seed', 25000, 'pcs', 8, 3),
(11, 'Vitamix', 10000, 'pcs', 4, 3),
(12, 'Bolt', 22000, 'kg', 5, 3),
(13, 'Royal Canin ', 60000, 'gram', 3, 1),
(14, 'Excel', 12000, 'kg', 5, 1),
(15, 'Takari ', 7000, 'gram', 3, 4),
(16, 'Pelet lele', 12000, 'kg', 4, 4),
(17, 'Comfeed', 11000, 'kg', 5, 1),
(18, 'Promin', 37500, 'kg', 3, 1),
(19, 'Pelet Koi', 61000, 'kg', 5, 4),
(20, 'Pakan Kelinci', 10000, 'kg', 6, 5),
(21, 'Mineral Feed', 6000, 'pcs', 7, 2),
(22, 'Jangkrik', 18000, 'kg', 2, 3),
(23, 'Polar', 205000, 'ton', 14, 2),
(24, 'Phoenix Kutut', 6000, 'pcs', 10, 3),
(25, 'Topsong', 8000, 'pcs', 4, 3),
(56, 'babi gunung', 20000, 'gram', 230, 1);

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `telepon` varchar(16) NOT NULL,
  `id_shift` int(11) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id`, `nama`, `telepon`, `id_shift`, `password`, `role`) VALUES
(1, 'Azizzah', '+6285640273906', 1, 'azzer', 'shopkeeper'),
(2, 'Brilian', '+6287856037066', 2, 'bril', 'shopkeeper '),
(3, 'Duwi', '+6285157993801', 1, 'duwiaaw', 'admin'),
(32, 'Sukimin', '+62093737', 1, 'djdhdjydkS1_', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `keterangan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `kategori`, `keterangan`) VALUES
(1, 'Kucing ', 'Semua makanan kucing kucing rumah kucing hutan .'),
(2, 'Ayam  ', 'Dedak, Bekatul, Dsb'),
(3, 'Burung ', 'Makanan burung biji bijian pisang dan ulet juga ada'),
(4, 'Ikan ', 'Takari dsb'),
(5, 'Kelinci  ', 'wortel dan makanan kemasan\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `shift`
--

CREATE TABLE `shift` (
  `id` int(11) NOT NULL,
  `shift` varchar(100) NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shift`
--

INSERT INTO `shift` (`id`, `shift`, `jam_mulai`, `jam_selesai`) VALUES
(1, 'Pagi', '08:00:00', '15:00:00'),
(2, 'Malam', '15:00:00', '22:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_shift` (`id_shift`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shift`
--
ALTER TABLE `shift`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `shift`
--
ALTER TABLE `shift`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id`);

--
-- Constraints for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD CONSTRAINT `karyawan_ibfk_1` FOREIGN KEY (`id_shift`) REFERENCES `shift` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
