-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 02, 2025 at 06:07 AM
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
-- Database: `toko4`
--

-- --------------------------------------------------------

--
-- Table structure for table `catatan_penjualan`
--

CREATE TABLE `catatan_penjualan` (
  `catatan_id` int(11) NOT NULL,
  `transaction_date` date NOT NULL,
  `username` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `catatan_penjualan`
--

INSERT INTO `catatan_penjualan` (`catatan_id`, `transaction_date`, `username`) VALUES
(11, '2024-12-18', 'admin'),
(12, '2024-12-11', 'admin'),
(13, '2024-12-11', 'admin1'),
(14, '2024-12-18', 'admin2');

-- --------------------------------------------------------

--
-- Table structure for table `hutang`
--

CREATE TABLE `hutang` (
  `hutang_id` int(11) NOT NULL,
  `hutang_deadline` date NOT NULL,
  `hutang_total` decimal(10,2) NOT NULL,
  `nota_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hutang`
--

INSERT INTO `hutang` (`hutang_id`, `hutang_deadline`, `hutang_total`, `nota_id`) VALUES
(1, '2024-12-31', 33000.00, 2),
(2, '2024-12-31', 6740.00, 3),
(3, '2024-12-20', 69000.00, 5),
(4, '2024-12-31', 0.00, 6);

-- --------------------------------------------------------

--
-- Table structure for table `nota_pembelian`
--

CREATE TABLE `nota_pembelian` (
  `nota_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `save_method` varchar(50) DEFAULT NULL,
  `detail_pembelian` text DEFAULT NULL,
  `tanggal` date NOT NULL,
  `username` varchar(100) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nota_pembelian`
--

INSERT INTO `nota_pembelian` (`nota_id`, `total`, `save_method`, `detail_pembelian`, `tanggal`, `username`, `supplier_id`) VALUES
(2, 66000.00, 'credit', '', '2024-12-18', 'admin', 5),
(3, 7740.00, 'credit', 'aaa', '2024-12-09', 'admin', 7),
(4, 0.00, 'cash', '', '2024-12-11', 'admin', 11),
(5, 69000.00, 'credit', 'vh', '2024-12-20', 'admin', 14),
(6, 0.00, 'credit', '', '2024-12-20', 'admin', 14);

-- --------------------------------------------------------

--
-- Table structure for table `order_restock`
--

CREATE TABLE `order_restock` (
  `order_restock_id` int(11) NOT NULL,
  `order_restock_qty` int(11) NOT NULL,
  `order_restock_price` decimal(10,2) NOT NULL,
  `order_restock_delivery_status` varchar(20) DEFAULT NULL,
  `nota_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `order_restock_delivered_QTY` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_restock`
--

INSERT INTO `order_restock` (`order_restock_id`, `order_restock_qty`, `order_restock_price`, `order_restock_delivery_status`, `nota_id`, `produk_id`, `order_restock_delivered_QTY`) VALUES
(1, 2, 32000.00, 'Delivered', 2, 1, 2),
(2, 2, 1000.00, 'pending', 2, 2, 0),
(3, 2, 345.00, 'Delivered', 3, 1, 2),
(4, 2, 3525.00, 'pending', 3, 2, 0),
(5, 3, 23000.00, 'pending', 5, 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `penjualan_detail`
--

CREATE TABLE `penjualan_detail` (
  `produk_id` int(11) NOT NULL,
  `catatan_id` int(11) NOT NULL,
  `penjualan_detail_qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjualan_detail`
--

INSERT INTO `penjualan_detail` (`produk_id`, `catatan_id`, `penjualan_detail_qty`) VALUES
(1, 11, 2),
(2, 11, 2),
(12, 12, 1),
(12, 13, 2),
(16, 14, 3);

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `produk_id` int(11) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `stok` int(11) NOT NULL,
  `harga_jual` decimal(10,2) NOT NULL,
  `username` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`produk_id`, `nama_produk`, `stok`, `harga_jual`, `username`) VALUES
(1, 'Semen Portland 50kg', 100, 55000.00, 'admin'),
(2, 'Batu Bata Merah', 2000, 800.00, 'admin'),
(3, 'Pasir Kasar 1 Kubik', 50, 75000.00, 'admin'),
(4, 'Besi Beton Ulir 12mm', 120, 125000.00, 'admin'),
(5, 'Kayu Balok 5x10x400cm', 80, 45000.00, 'admin'),
(6, 'Genteng Metal', 300, 17500.00, 'admin'),
(7, 'Pipa PVC 3 Inch', 150, 27000.00, 'admin'),
(8, 'Triplek 9mm', 100, 65000.00, 'admin'),
(9, 'Keramik Lantai 40x40', 500, 58000.00, 'admin'),
(10, 'Cat Tembok Putih 5kg', 70, 150000.00, 'admin'),
(12, 'Semen', 20, 50000.00, 'admin1'),
(13, 'Batu bata', 39, 57000.00, 'admin1'),
(16, 'Paku', 3, 13000.00, 'admin2');

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `supplier_id` int(11) NOT NULL,
  `nama_supplier` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `nomor_telepon` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`supplier_id`, `nama_supplier`, `username`, `nomor_telepon`) VALUES
(1, 'PT. Sumber Bangunan', 'admin', ''),
(2, 'CV. Jaya Abadi', 'admin', ''),
(3, 'UD. Sejahtera Mandiri', 'admin', ''),
(4, 'Toko Makmur Bersama', 'admin', ''),
(5, 'CV. Bintang Timur', 'admin', ''),
(6, 'PT. Gemilang Perkasa', 'admin', ''),
(7, 'UD. Prima Material', 'admin', ''),
(8, 'PT. Cahaya Mandiri', 'admin', ''),
(9, 'CV. Karya Beton', 'admin', ''),
(10, 'Toko Amanah Jaya', 'admin', ''),
(11, 'UD ABC', 'admin1', '0546-899032'),
(12, 'UD DEF', 'admin1', '08946783321'),
(13, 'UD ABC', 'admin2', '08946783321'),
(14, 'UD ABC', 'adminn', '0546-899032');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`username`, `password`) VALUES
('admin', '123'),
('admin1', '$2y$10$oaVeHViBzkysjWy/BRU48u4ISMl/wLHOwuO/.lVo9LH1E3fSDDYfO'),
('admin2', '$2y$10$tmbiyILXh075ZiMhcvAOXen1wQzw1ozqzGZk8BloEXE6DCGscSjBa'),
('adminn', '$2y$10$wOJreHGrTNc7m2cKJwayYOxc1Ttbe0DGzwCnxHgk7mA89l86nEYfK');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `catatan_penjualan`
--
ALTER TABLE `catatan_penjualan`
  ADD PRIMARY KEY (`catatan_id`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `hutang`
--
ALTER TABLE `hutang`
  ADD PRIMARY KEY (`hutang_id`),
  ADD KEY `nota_id` (`nota_id`);

--
-- Indexes for table `nota_pembelian`
--
ALTER TABLE `nota_pembelian`
  ADD PRIMARY KEY (`nota_id`),
  ADD KEY `username` (`username`),
  ADD KEY `fk_supplier_id` (`supplier_id`);

--
-- Indexes for table `order_restock`
--
ALTER TABLE `order_restock`
  ADD PRIMARY KEY (`order_restock_id`),
  ADD KEY `nota_id` (`nota_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indexes for table `penjualan_detail`
--
ALTER TABLE `penjualan_detail`
  ADD PRIMARY KEY (`produk_id`,`catatan_id`),
  ADD KEY `catatan_id` (`catatan_id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`produk_id`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`supplier_id`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `catatan_penjualan`
--
ALTER TABLE `catatan_penjualan`
  MODIFY `catatan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `hutang`
--
ALTER TABLE `hutang`
  MODIFY `hutang_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `nota_pembelian`
--
ALTER TABLE `nota_pembelian`
  MODIFY `nota_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_restock`
--
ALTER TABLE `order_restock`
  MODIFY `order_restock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `produk_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `hutang`
--
ALTER TABLE `hutang`
  ADD CONSTRAINT `hutang_ibfk_1` FOREIGN KEY (`nota_id`) REFERENCES `nota_pembelian` (`nota_id`);

--
-- Constraints for table `nota_pembelian`
--
ALTER TABLE `nota_pembelian`
  ADD CONSTRAINT `fk_supplier_id` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `nota_pembelian_ibfk_1` FOREIGN KEY (`username`) REFERENCES `user` (`username`);

--
-- Constraints for table `order_restock`
--
ALTER TABLE `order_restock`
  ADD CONSTRAINT `order_restock_ibfk_1` FOREIGN KEY (`nota_id`) REFERENCES `nota_pembelian` (`nota_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_restock_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`produk_id`);

--
-- Constraints for table `penjualan_detail`
--
ALTER TABLE `penjualan_detail`
  ADD CONSTRAINT `penjualan_detail_ibfk_1` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`produk_id`),
  ADD CONSTRAINT `penjualan_detail_ibfk_2` FOREIGN KEY (`catatan_id`) REFERENCES `catatan_penjualan` (`catatan_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
