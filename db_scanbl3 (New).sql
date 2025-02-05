-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 04 Feb 2025 pada 00.51
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_scanbl3`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `accounts`
--

CREATE TABLE `accounts` (
  `id_account` int(11) NOT NULL,
  `real_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(100) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `accounts`
--

INSERT INTO `accounts` (`id_account`, `real_name`, `username`, `password`, `role`, `date`) VALUES
(4, 'admin', 'adminmeiji', '$2y$10$eTJ9IErRblemTwGbj51ZeuOhpsAPds1BO38R7nvtIiNsbg8FxGNuS', 'Admin', '2025-02-03 03:52:27'),
(5, 'haris rifky', 'kikiyee', '$2y$10$rnxr4XexJwdQnirL0yenn.gHuL.AhVWAGQWsU65MrjHpNKg4FR47u', 'Operator', '2025-02-03 03:53:08');

-- --------------------------------------------------------

--
-- Struktur dari tabel `add_master`
--

CREATE TABLE `add_master` (
  `id_master` int(11) NOT NULL,
  `product` varchar(100) NOT NULL,
  `rss_code` varchar(100) NOT NULL,
  `jam_code` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `add_master`
--

INSERT INTO `add_master` (`id_master`, `product`, `rss_code`, `jam_code`) VALUES
(1, 'Product A', '011001234567890210123ABC', ''),
(2, 'Product BB', '0195012345678903', '');

--
-- Trigger `add_master`
--
DELIMITER $$
CREATE TRIGGER `after_update_add_master` AFTER UPDATE ON `add_master` FOR EACH ROW BEGIN
    DECLARE v_Barcode VARCHAR(255);
    DECLARE v_Product VARCHAR(255);
    DECLARE v_Jam_Code VARCHAR(255);

    -- Ambil data dari add_master berdasarkan id_master yang diupdate
    SET v_Barcode = NEW.rss_code;
    SET v_Product = NEW.product;
    SET v_Jam_Code = NEW.jam_code;

    -- Masukkan log ke log_add_product
    INSERT INTO log_add_product (Barcode, Product, Jam_Code, Date, Counter, No_Lot, Change_Type, Change_Date, operator)
    SELECT v_Barcode, v_Product, v_Jam_Code, NOW(), ap.counter, ap.no_lot, 'UPDATE', NOW(), ap.operator
    FROM add_product ap
    WHERE ap.add_master_id_master = NEW.id_master;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_delete_add_master` BEFORE DELETE ON `add_master` FOR EACH ROW BEGIN
    DECLARE v_Barcode VARCHAR(255);
    DECLARE v_Product VARCHAR(255);
    DECLARE v_Jam_Code VARCHAR(255);

    -- Ambil data dari add_master yang akan dihapus
    SET v_Barcode = OLD.rss_code;
    SET v_Product = OLD.product;
    SET v_Jam_Code = OLD.jam_code;

    -- Masukkan log ke log_add_product
    INSERT INTO log_add_product (Barcode, Product, Jam_Code, Date, Counter, No_Lot, Change_Type, Change_Date, operator)
    SELECT v_Barcode, v_Product, v_Jam_Code, NOW(), ap.counter, ap.no_lot, 'DELETE', NOW(), ap.operator
    FROM add_product ap
    WHERE ap.add_master_id_master = OLD.id_master;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `add_product`
--

CREATE TABLE `add_product` (
  `id_add` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `no_lot` int(11) NOT NULL,
  `counter` int(11) NOT NULL,
  `add_master_id_master` int(11) NOT NULL,
  `operator` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `add_product`
--

INSERT INTO `add_product` (`id_add`, `date`, `no_lot`, `counter`, `add_master_id_master`, `operator`) VALUES
(1, '2025-02-03 02:53:51', 1, 1, 1, 'carveynaa'),
(2, '2025-02-03 02:53:54', 1, 2, 1, 'carveynaa'),
(3, '2025-02-03 02:54:17', 1, 3, 1, 'carveynaa'),
(7, '2025-02-03 07:53:08', 2, 1, 2, 'kikiyee');

--
-- Trigger `add_product`
--
DELIMITER $$
CREATE TRIGGER `after_delete_add_product` AFTER DELETE ON `add_product` FOR EACH ROW BEGIN
    DECLARE v_Barcode VARCHAR(255);
    DECLARE v_Product VARCHAR(255);
    DECLARE v_Jam_Code VARCHAR(255);

    -- Ambil data dari add_master berdasarkan add_master_id_master
    SELECT rss_code, product, jam_code
    INTO v_Barcode, v_Product, v_Jam_Code
    FROM add_master
    WHERE id_master = OLD.add_master_id_master;

    -- Masukkan log ke log_add_product
    INSERT INTO log_add_product (Barcode, Product, Jam_Code, Date, Counter, No_Lot, Change_Type, Change_Date, operator)
    VALUES (v_Barcode, v_Product, v_Jam_Code, NOW(), OLD.counter, OLD.no_lot, 'DELETE', NOW(), OLD.operator);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_insert_add_product` AFTER INSERT ON `add_product` FOR EACH ROW BEGIN
    DECLARE v_Barcode VARCHAR(255);
    DECLARE v_Product VARCHAR(255);
    DECLARE v_Jam_Code VARCHAR(255);

    -- Ambil data dari add_master berdasarkan add_master_id_master
    SELECT rss_code, product, jam_code
    INTO v_Barcode, v_Product, v_Jam_Code
    FROM add_master
    WHERE id_master = NEW.add_master_id_master;

    -- Masukkan log ke log_add_product
    INSERT INTO log_add_product (Barcode, Product, Jam_Code, Date, Counter, No_Lot, Change_Type, Change_Date, operator)
    VALUES (v_Barcode, v_Product, v_Jam_Code, NOW(), NEW.counter, NEW.no_lot, 'INSERT', NOW(), NEW.operator);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_update_add_product` AFTER UPDATE ON `add_product` FOR EACH ROW BEGIN
    DECLARE v_Barcode VARCHAR(255);
    DECLARE v_Product VARCHAR(255);
    DECLARE v_Jam_Code VARCHAR(255);

    -- Ambil data dari add_master berdasarkan add_master_id_master
    SELECT rss_code, product, jam_code
    INTO v_Barcode, v_Product, v_Jam_Code
    FROM add_master
    WHERE id_master = NEW.add_master_id_master;

    -- Masukkan log ke log_add_product
    INSERT INTO log_add_product (Barcode, Product, Jam_Code, Date, Counter, No_Lot, Change_Type, Change_Date, operator)
    VALUES (v_Barcode, v_Product, v_Jam_Code, NOW(), NEW.counter, NEW.no_lot, 'UPDATE', NOW(), NEW.operator);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_add_product`
--

CREATE TABLE `log_add_product` (
  `id_log` int(11) NOT NULL,
  `Barcode` varchar(255) DEFAULT NULL,
  `Product` varchar(255) DEFAULT NULL,
  `Jam_Code` varchar(255) DEFAULT NULL,
  `Date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Counter` int(11) DEFAULT NULL,
  `No_Lot` int(11) DEFAULT NULL,
  `Change_Type` enum('INSERT','UPDATE','DELETE') DEFAULT NULL,
  `Change_Date` timestamp NOT NULL DEFAULT current_timestamp(),
  `operator` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `log_add_product`
--

INSERT INTO `log_add_product` (`id_log`, `Barcode`, `Product`, `Jam_Code`, `Date`, `Counter`, `No_Lot`, `Change_Type`, `Change_Date`, `operator`) VALUES
(1, '011001234567890210123ABC', 'Product A', '', '2025-02-03 02:53:51', 1, 1, 'INSERT', '2025-02-03 02:53:51', 'carveynaa'),
(2, '011001234567890210123ABC', 'Product A', '', '2025-02-03 02:53:54', 2, 1, 'INSERT', '2025-02-03 02:53:54', 'carveynaa'),
(3, '011001234567890210123ABC', 'Product A', '', '2025-02-03 02:54:17', 3, 1, 'INSERT', '2025-02-03 02:54:17', 'carveynaa'),
(4, '011001234567890210123ABC', 'Product A', '', '2025-02-03 02:54:21', 4, 1, 'INSERT', '2025-02-03 02:54:21', 'carveynaa'),
(5, '011001234567890210123ABC', 'Product A', '', '2025-02-03 02:54:24', 5, 1, 'INSERT', '2025-02-03 02:54:24', 'carveynaa'),
(6, '011001234567890210123ABC', 'Product A', '', '2025-02-03 02:55:26', 6, 1, 'INSERT', '2025-02-03 02:55:26', 'carveynaa'),
(7, '0195012345678903', 'Product B', '', '2025-02-03 07:53:08', 1, 2, 'INSERT', '2025-02-03 07:53:08', 'kikiyee'),
(8, '0195012345678903', 'Product B', '', '2025-02-03 07:53:10', 2, 2, 'INSERT', '2025-02-03 07:53:10', 'kikiyee'),
(9, '0195012345678903', 'Product B', '', '2025-02-03 07:53:12', 3, 2, 'INSERT', '2025-02-03 07:53:12', 'kikiyee'),
(10, '0195012345678903', 'Product B', '', '2025-02-03 07:53:14', 4, 2, 'INSERT', '2025-02-03 07:53:14', 'kikiyee'),
(11, '0195012345678903', 'Product B', '', '2025-02-03 07:53:16', 5, 2, 'INSERT', '2025-02-03 07:53:16', 'kikiyee'),
(12, '0195012345678903', 'Product B', '', '2025-02-03 07:53:18', 6, 2, 'INSERT', '2025-02-03 07:53:18', 'kikiyee'),
(13, '0195012345678903', 'Product B', '', '2025-02-03 07:53:38', 6, 2, 'DELETE', '2025-02-03 07:53:38', 'kikiyee'),
(14, '0195012345678903', 'Product BB', '', '2025-02-03 07:55:41', 1, 2, 'UPDATE', '2025-02-03 07:55:41', 'kikiyee'),
(15, '0195012345678903', 'Product BB', '', '2025-02-03 07:55:41', 2, 2, 'UPDATE', '2025-02-03 07:55:41', 'kikiyee'),
(16, '0195012345678903', 'Product BB', '', '2025-02-03 07:55:41', 3, 2, 'UPDATE', '2025-02-03 07:55:41', 'kikiyee'),
(17, '0195012345678903', 'Product BB', '', '2025-02-03 07:55:41', 4, 2, 'UPDATE', '2025-02-03 07:55:41', 'kikiyee'),
(18, '0195012345678903', 'Product BB', '', '2025-02-03 07:55:41', 5, 2, 'UPDATE', '2025-02-03 07:55:41', 'kikiyee'),
(21, '0195012345678903', 'Product BB', '', '2025-02-03 08:37:38', 5, 2, 'DELETE', '2025-02-03 08:37:38', 'kikiyee'),
(22, '0195012345678903', 'Product BB', '', '2025-02-03 08:41:42', 4, 2, 'DELETE', '2025-02-03 08:41:42', 'kikiyee'),
(23, '0195012345678903', 'Product BB', '', '2025-02-03 08:42:12', 3, 2, 'DELETE', '2025-02-03 08:42:12', 'kikiyee'),
(24, '011001234567890210123ABC', 'Product A', '', '2025-02-03 08:46:18', 6, 1, 'DELETE', '2025-02-03 08:46:18', 'carveynaa'),
(25, '011001234567890210123ABC', 'Product A', '', '2025-02-03 08:46:22', 5, 1, 'DELETE', '2025-02-03 08:46:22', 'carveynaa'),
(26, '0195012345678903', 'Product BB', '', '2025-02-03 08:46:31', 2, 2, 'DELETE', '2025-02-03 08:46:31', 'kikiyee'),
(27, '011001234567890210123ABC', 'Product A', '', '2025-02-03 08:46:40', 4, 1, 'DELETE', '2025-02-03 08:46:40', 'carveynaa');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id_account`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `add_master`
--
ALTER TABLE `add_master`
  ADD PRIMARY KEY (`id_master`);

--
-- Indeks untuk tabel `add_product`
--
ALTER TABLE `add_product`
  ADD PRIMARY KEY (`id_add`),
  ADD KEY `add_product_add_master_fk` (`add_master_id_master`);

--
-- Indeks untuk tabel `log_add_product`
--
ALTER TABLE `log_add_product`
  ADD PRIMARY KEY (`id_log`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id_account` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `add_master`
--
ALTER TABLE `add_master`
  MODIFY `id_master` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `add_product`
--
ALTER TABLE `add_product`
  MODIFY `id_add` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `log_add_product`
--
ALTER TABLE `log_add_product`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `add_product`
--
ALTER TABLE `add_product`
  ADD CONSTRAINT `add_product_add_master_fk` FOREIGN KEY (`add_master_id_master`) REFERENCES `add_master` (`id_master`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_add_master` FOREIGN KEY (`add_master_id_master`) REFERENCES `add_master` (`id_master`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
