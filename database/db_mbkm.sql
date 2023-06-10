-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2022 at 03:02 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_mbkm`
--

-- --------------------------------------------------------

--
-- Table structure for table `kontrakmatkul`
--

CREATE TABLE `kontrakmatkul` (
  `kode_mk` varchar(10) NOT NULL,
  `nim` varchar(10) NOT NULL,
  `tipe` varchar(10) DEFAULT NULL,
  `id_kontrakmatkul` int(11) NOT NULL,
  `sem_kontrak` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kontrakmatkul`
--

INSERT INTO `kontrakmatkul` (`kode_mk`, `nim`, `tipe`, `id_kontrakmatkul`, `sem_kontrak`) VALUES
('IK120', '2100137', 'konversi', 19, 5),
('IK150', '2100137', 'konversi', 20, 5),
('IK180', '2100137', 'konversi', 21, 5),
('IK430', '2100187', 'konversi', 22, 6),
('IK210', '2100901', 'konversi', 23, 5);

--
-- Triggers `kontrakmatkul`
--
DELIMITER $$
CREATE TRIGGER `after_konversi_insert` BEFORE INSERT ON `kontrakmatkul` FOR EACH ROW BEGIN

	DECLARE sks_sisa int(11);
    DECLARE sks int(11);
    
    SET sks_sisa = (SELECT tma.sks_sisa_konversi FROM tmahasiswa AS tma WHERE tma.nim = new.nim);
    SET sks = (SELECT tm.sks_mk FROM tmatkul AS tm WHERE new.kode_mk=tm.kode_mk);
    
    IF new.tipe LIKE 'konversi' AND (sks_sisa - sks) >= 0 THEN
        UPDATE tmahasiswa AS tm SET sks_sisa_konversi=sks_sisa_konversi-sks WHERE new.nim=tm.nim;
    END IF;
    
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `kontrakmbkm`
--

CREATE TABLE `kontrakmbkm` (
  `nim` varchar(10) NOT NULL,
  `id_program` varchar(10) NOT NULL,
  `status` varchar(20) NOT NULL,
  `id_kontrakmbkm` int(11) NOT NULL,
  `nip_pembimbingmbkm` varchar(20) DEFAULT '00000',
  `semester_kontrak` int(11) NOT NULL,
  `waktu_mulai` date DEFAULT NULL,
  `waktu_selesai` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kontrakmbkm`
--

INSERT INTO `kontrakmbkm` (`nim`, `id_program`, `status`, `id_kontrakmbkm`, `nip_pembimbingmbkm`, `semester_kontrak`, `waktu_mulai`, `waktu_selesai`) VALUES
('2108067', '103', 'sedang mengikuti', 12, '33333', 5, '2022-06-03', NULL),
('2100137', '110', 'selesai', 13, '44444', 5, '2022-06-03', '2022-06-03'),
('2101114', '105', 'selesai', 14, '55555', 6, '2022-06-03', '2022-06-03'),
('2100187', '111', 'selesai', 15, '66666', 6, '2022-06-03', '2022-06-03'),
('2100901', '107', 'selesai', 16, '11111', 5, '2022-06-03', '2022-06-03'),
('2100991', '104', 'selesai', 17, '77777', 5, '2022-06-03', '2022-06-03'),
('2103507', '103', 'sedang mendaftar', 18, '00000', 5, NULL, NULL);

--
-- Triggers `kontrakmbkm`
--
DELIMITER $$
CREATE TRIGGER `after_insert_kontrakbmkm` AFTER INSERT ON `kontrakmbkm` FOR EACH ROW UPDATE tmahasiswa
SET status_mahasiswa = 'sudah berpartisipasi'
WHERE nim = NEW.nim
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_sedangmengikuti_update` BEFORE UPDATE ON `kontrakmbkm` FOR EACH ROW BEGIN
    IF new.status LIKE 'sedang mengikuti' THEN
    	SET new.waktu_mulai=CURRENT_DATE;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_selesai_update` BEFORE UPDATE ON `kontrakmbkm` FOR EACH ROW BEGIN

    DECLARE sks int(11);
    DECLARE lingkup varchar(10);
    SET sks = (SELECT tp.sks_program FROM tprogrammbkm AS tp WHERE new.id_program=tp.id_program);
    SET lingkup = (SELECT tp.lingkup_program FROM tprogrammbkm AS tp WHERE new.id_program=tp.id_program);
    
    IF new.status LIKE 'selesai' THEN
    	SET new.waktu_selesai=CURRENT_DATE;
        UPDATE tmahasiswa AS tm SET sks_sisa_konversi=sks_sisa_konversi+sks WHERE new.nim=tm.nim;
        UPDATE tmahasiswa AS tm SET sks_akumulatif=sks_akumulatif+sks WHERE new.nim=tm.nim;

        IF lingkup LIKE 'luar' THEN
            UPDATE tmahasiswa AS tm SET sks_luar_univ=sks_luar_univ-sks WHERE new.nim=tm.nim;
        ELSEIF lingkup LIKE 'dalam' THEN
            UPDATE tmahasiswa AS tm SET sks_dalam_univ=sks_dalam_univ-sks WHERE new.nim=tm.nim;
        END IF;
    END IF;
    
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tmahasiswa`
--

CREATE TABLE `tmahasiswa` (
  `nip` varchar(20) NOT NULL,
  `nim` varchar(10) NOT NULL,
  `nama_mahasiswa` varchar(50) NOT NULL,
  `prodi` varchar(50) NOT NULL,
  `email_mahasiswa` varchar(50) NOT NULL,
  `semester_mahasiswa` int(11) NOT NULL,
  `sks_akumulatif` int(11) DEFAULT 0,
  `sks_sisa_konversi` int(11) DEFAULT 0,
  `sks_dalam_univ` int(11) DEFAULT 20,
  `sks_luar_univ` int(11) DEFAULT 40,
  `ipk_mahasiswa` float NOT NULL,
  `status_mahasiswa` varchar(25) DEFAULT 'belum berpartisipasi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tmahasiswa`
--

INSERT INTO `tmahasiswa` (`nip`, `nim`, `nama_mahasiswa`, `prodi`, `email_mahasiswa`, `semester_mahasiswa`, `sks_akumulatif`, `sks_sisa_konversi`, `sks_dalam_univ`, `sks_luar_univ`, `ipk_mahasiswa`, `status_mahasiswa`) VALUES
('11111', '2100137', 'Muhamad Nur Yasin Amadudin', 'Ilmu Komputer', 'dummy@gmail.com', 5, 20, 14, 20, 20, 4, 'sudah berpartisipasi'),
('22222', '2100187', 'Muhammad Hilmy Rasyad Sofyan', 'Pendidikan Ilmu Komputer', 'dummy@gmail.com', 6, 20, 18, 20, 20, 3.7, 'sudah berpartisipasi'),
('99999', '2100192', 'Muhammad Rayhan Nur', 'Ilmu Komputer', 'dummy@gmail.com', 6, 0, 0, 20, 40, 3.9, 'belum berpartisipasi'),
('99999', '2100195', 'Davin Fausta Putra Sanjaya', 'Pendidikan Ilmu Komputer', 'dummy@gmail.com', 5, 0, 0, 20, 40, 3.5, 'belum berpartisipasi'),
('99999', '2100846', 'Rafly Putra Santoso', 'Ilmu Komputer', 'dummy@gmail.com', 6, 0, 0, 20, 40, 4, 'belum berpartisipasi'),
('66666', '2100901', 'Azzahra Siti Hadjar', 'Pendidikan Ilmu Komputer', 'dummy@gmail.com', 5, 2, 0, 20, 38, 3.8, 'sudah berpartisipasi'),
('11111', '2100991', 'Khana Yusdiana', 'Ilmu Komputer', 'dummy@gmail.com', 5, 20, 20, 20, 20, 4, 'sudah berpartisipasi'),
('22222', '2101103', 'Rifqi Fajar Indrayadi', 'Pendidikan Ilmu Komputer', 'dummy@gmail.com', 5, 0, 0, 20, 40, 3.9, 'belum berpartisipasi'),
('66666', '2101114', 'Anandita Kusumah Mulyadi', 'Pendidikan Komputer', 'dummy@gmail.com', 6, 20, 20, 20, 20, 3.9, 'sudah berpartisipasi'),
('66666', '2101147', 'Amida Zulfa Laila', 'Pendidikan Ilmu Komputer', 'dummy@gmail.com', 6, 0, 0, 20, 40, 3.8, 'belum berpartisipasi'),
('66666', '2102159', 'Virza Raihan Kurniawan', 'Ilmu Komputer', 'dummy@gmail.com', 6, 0, 0, 20, 40, 4, 'belum berpartisipasi'),
('22222', '2102204', 'Mohamad Asyqari Anugrah', 'Pendidikan Ilmu Komputer', 'dummy@gmail.com', 5, 0, 0, 20, 40, 3.9, 'belum berpartisipasi'),
('66666', '2102268', 'Audry Leonardo Loo', 'Ilmu Komputer', 'dummy@gmail.com', 5, 0, 0, 20, 40, 3.8, 'belum berpartisipasi'),
('66666', '2102292', 'Harold Vidian Exaudi Simarmata', 'Pendidikan Ilmu Komputer', 'dummy@gmail.com', 5, 0, 0, 20, 40, 4, 'belum berpartisipasi'),
('66666', '2102313', 'Muhammad Kamal Robbani', 'Ilmu Komputer', 'dummy@gmail.com', 5, 0, 0, 20, 40, 4, 'belum berpartisipasi'),
('77777', '2102421', 'Kania Dinda Fasya', 'Pendidikan Ilmu Komputer', 'dummy@gmail.com', 5, 0, 0, 20, 40, 3.8, 'belum berpartisipasi'),
('77777', '2102545', 'Zahra Fitria Maharani', 'Ilmu Komputer', 'dummy@gmail.com', 5, 0, 0, 20, 40, 3.9, 'belum berpartisipasi'),
('11111', '2102585', 'Apri Anggara Yudha', 'Pendidikan Ilmu Komputer', 'dummy@gmail.com', 5, 0, 0, 20, 40, 3.8, 'belum berpartisipasi'),
('22222', '2102665', 'M. Cahyana Bintang Fajar', 'Ilmu Komputer', 'dummy@gmail.com', 3, 0, 0, 20, 40, 3.8, 'belum berpartisipasi'),
('11111', '2102671', 'Anderfa Jalu Kawani', 'Pendidikan Ilmu Komputer', 'dummy@gmail.com', 3, 0, 0, 20, 40, 4, 'belum berpartisipasi'),
('22222', '2102690', '\'Aafiyah Kaltsum', 'Ilmu Komputer', 'dummy@gmail.com', 3, 0, 0, 20, 40, 4, 'belum berpartisipasi'),
('77777', '2102843', 'Najma Qalbi Dwiharani', 'Pendidikan Ilmu Komputer', 'dummy@gmail.com', 5, 0, 0, 20, 40, 4, 'belum berpartisipasi'),
('77777', '2103207', 'Yasmin Fathanah Zakiyyah', 'Ilmu Komputer', 'dummy@gmail.com', 6, 0, 0, 20, 40, 3.9, 'belum berpartisipasi'),
('11111', '2103507', 'Indah Resti Fauzi', 'Pendidikan Ilmu Komputer', 'dummy@gmail.com', 5, 0, 0, 20, 40, 3.8, 'sudah berpartisipasi'),
('77777', '2103703', 'Fauziyyah Zayyan Nur', 'Ilmu Komputer', 'dummy@gmail.com', 6, 0, 0, 20, 40, 4, 'belum berpartisipasi'),
('77777', '2103727', 'Cantika Putri Arbiliansyah', 'Ilmu Komputer', 'dummy@gmail.com', 3, 0, 0, 20, 40, 3.8, 'belum berpartisipasi'),
('11111', '2105673', 'Alghaniyu Naufal Hamid', 'Pendidikan Ilmu Komputer', 'dummy@gmail.com', 6, 0, 0, 20, 40, 3.8, 'belum berpartisipasi'),
('99999', '2105745', 'Ridwan Albana', 'Ilmu Komputer', 'dummy@gmail.com', 6, 0, 0, 20, 40, 4, 'belum berpartisipasi'),
('77777', '2105879', 'Farhan Muzhaffar Tiras Putra', 'Ilmu Komputer', 'dummy@gmail.com', 3, 0, 0, 20, 40, 3.9, 'belum berpartisipasi'),
('11111', '2105885', 'Qurrotu\' Ainii', 'Pendidikan Ilmu Komputer', 'dummy@gmail.com', 6, 0, 0, 20, 40, 3.8, 'belum berpartisipasi'),
('99999', '2105927', 'Febry Syaman Hasan', 'Pendidikan Ilmu Komputer', 'dummy@gmail.com', 4, 0, 0, 20, 40, 4, 'belum berpartisipasi'),
('77777', '2105997', 'Muhammad Fakhri Fadhlurrahman', 'Ilmu Komputer', 'dummy@gmail.com', 5, 0, 0, 20, 40, 3.9, 'belum berpartisipasi'),
('77777', '2106000', 'Sabila Rosad', 'Ilmu Komputer', 'dummy@gmail.com', 4, 0, 0, 20, 40, 4, 'belum berpartisipasi'),
('11111', '2108061', 'Achmad Fauzan', 'Pendidikan Ilmu Komputer', 'dummy@gmail.com', 4, 0, 0, 20, 40, 4, 'belum berpartisipasi'),
('99999', '2108067', 'Villeneuve Andhira Suwandhi', 'Ilmu Komputer', 'dummy@gmail.com', 5, 0, 0, 20, 40, 4, 'sudah berpartisipasi'),
('66666', '2108077', 'Hestina Dwi Hartiwi', 'Pendidikan Ilmu Komputer', 'dummy@gmail.com', 5, 0, 0, 20, 40, 3.8, 'belum berpartisipasi'),
('11111', '2108804', 'Laelatusya\'Diyah', 'Ilmu Komputer', 'dummy@gmail.com', 5, 0, 0, 20, 40, 4, 'belum berpartisipasi'),
('66666', '2108927', 'Muhammad Fahru Rozi', 'Pendidikan Ilmu Komputer', 'dummy@gmail.com', 5, 0, 0, 20, 40, 3.9, 'belum berpartisipasi'),
('22222', '2108938', 'Rafi Arsalan Miraj', 'Ilmu Komputer', 'dummy@gmail.com', 5, 0, 0, 20, 40, 4, 'belum berpartisipasi');

-- --------------------------------------------------------

--
-- Table structure for table `tmatkul`
--

CREATE TABLE `tmatkul` (
  `kode_mk` varchar(10) NOT NULL,
  `nama_mk` varchar(50) NOT NULL,
  `sks_mk` int(11) NOT NULL,
  `semester_mk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tmatkul`
--

INSERT INTO `tmatkul` (`kode_mk`, `nama_mk`, `sks_mk`, `semester_mk`) VALUES
('IK100', 'Algoritma dan Pemrograman 1', 3, 1),
('IK110', 'Kalkulus', 3, 1),
('IK120', 'Paradigma Pemrograman', 2, 5),
('IK130', 'Logika Informatika', 3, 1),
('IK150', 'Statistika', 2, 5),
('IK160', 'Algoritma dan Pemrograman 2', 3, 2),
('IK170', 'Sistem Basis Data', 3, 2),
('IK180', 'Aljabar Linier dan Matriks', 2, 6),
('IK190', 'Etika Profesi Teknologi Informasi dan Komunikasi', 2, 6),
('IK207', 'Jaringan Komputer', 3, 3),
('IK210', 'Metode Numerik', 2, 7),
('IK217', 'Sistem Informasi', 3, 3),
('IK220', 'Sistem Kontrol', 3, 2),
('IK227', 'Teknik Riset Operasi', 2, 6),
('IK230', 'Design dan Pemrograman Web', 3, 2),
('IK237', 'Analisis dan Desain Algoritma', 3, 4),
('IK240', 'Struktur Data', 3, 3),
('IK250', 'Sistem Operasi', 3, 4),
('IK260', 'Teori Bahasa dan Automata', 3, 1),
('IK270', 'Rekayasa Perangkat Lunak', 3, 2),
('IK280', 'Kecerdasan Buatan', 3, 3),
('IK290', 'Desain dan Pemrograman Berorientasi Objek', 3, 4),
('IK300', 'Pemrograman Visual dan Piranti Bergerak', 3, 4),
('IK310', 'Kriptografi', 2, 2),
('IK320', 'Grafika Komputer dan Multimedia', 3, 3),
('IK360', 'Kapita Selekta', 2, 7),
('IK400', 'Metodologi Penelitian', 3, 4),
('IK410', 'Kewirausahaan Ilmu Komputer', 2, 7),
('IK430', 'E-Business', 2, 5),
('IK500', 'Machine Learning', 3, 3),
('IK505', 'Data Mining and Warehouse', 3, 3),
('IK545', 'Big Data Platforms', 2, 4);

-- --------------------------------------------------------

--
-- Table structure for table `tpembimbing`
--

CREATE TABLE `tpembimbing` (
  `nip` varchar(20) NOT NULL,
  `nama_pembimbing` varchar(50) NOT NULL,
  `email_pembimbing` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tpembimbing`
--

INSERT INTO `tpembimbing` (`nip`, `nama_pembimbing`, `email_pembimbing`) VALUES
('00000', 'Belum Ada', 'dummy@gmail.com'),
('11111', 'Ani Anisyah, S.Pd., M.T.', 'dummy@gmail.com'),
('22222', 'Dr. Asep Wahyudin, M.T.', 'dummy@gmail.com'),
('33333', 'Herbert Siregar, M.T.', 'dummy@gmail.com'),
('44444', 'Dr. Muhammad Nursalman, M.T.', 'dummy@gmail.com'),
('55555', 'Dr. Rani Megasari, M.T.', 'dummy@gmail.com'),
('66666', 'Rasim, M.T.', 'dummy@gmail.com'),
('77777', 'Rosa Ariani Sukamto, M.T.', 'dummy@gmail.com'),
('88888', 'Yudi Ahmad Hambali, MT', 'dummy@gmail.com'),
('99999', 'Dr. Yudi Wibisono, M.T.', 'dummy@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `tprogrammbkm`
--

CREATE TABLE `tprogrammbkm` (
  `id_program` varchar(10) NOT NULL,
  `nama_program` varchar(50) NOT NULL,
  `jenis_program` varchar(50) NOT NULL,
  `durasi` int(11) NOT NULL,
  `sks_program` int(11) NOT NULL,
  `lingkup_program` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tprogrammbkm`
--

INSERT INTO `tprogrammbkm` (`id_program`, `nama_program`, `jenis_program`, `durasi`, `sks_program`, `lingkup_program`) VALUES
('100', 'Pertukaran Mahasiswa Luar Prodi', 'Pertukaran Mahasiswa', 6, 20, 'dalam'),
('101', 'Pertukaran Mahasiswa Luar Universitas', 'Pertukaran Mahasiswa', 6, 20, 'luar'),
('102', 'Bangkit', 'Bangkit', 5, 20, 'luar'),
('103', 'Kampus Mengajar', 'Kampus Mengajar', 6, 20, 'luar'),
('104', 'Magang Di Perusahaan', 'Magang', 5, 20, 'luar'),
('105', 'Kegiatan Wirausaha', 'Kegiatan Wirausaha', 6, 20, 'luar'),
('106', 'PKM Peringkat Nasional', 'PKM', 0, 3, 'luar'),
('107', 'PKM Peringkat Universitas', 'PKM', 0, 2, 'luar'),
('108', 'PKM Peringkat Fakultas', 'PKM', 0, 1, 'luar'),
('110', 'Penelitian', 'Penelitian', 6, 20, 'luar'),
('111', 'Studi Independen', 'Studi Independen', 6, 20, 'luar');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kontrakmatkul`
--
ALTER TABLE `kontrakmatkul`
  ADD PRIMARY KEY (`id_kontrakmatkul`),
  ADD KEY `FK_KONTRAKM_KONTRAKMA_TMATKUL` (`kode_mk`),
  ADD KEY `FK_KONTRAKM_KONTRAKMA_TMAHASIS` (`nim`);

--
-- Indexes for table `kontrakmbkm`
--
ALTER TABLE `kontrakmbkm`
  ADD PRIMARY KEY (`id_kontrakmbkm`),
  ADD KEY `FK_KONTRAKM_KONTRAKMB_TMAHASIS` (`nim`),
  ADD KEY `FK_KONTRAKM_KONTRAKMB_TPROGRAM` (`id_program`),
  ADD KEY `NIP_Pembimbing_MBKM` (`nip_pembimbingmbkm`);

--
-- Indexes for table `tmahasiswa`
--
ALTER TABLE `tmahasiswa`
  ADD PRIMARY KEY (`nim`),
  ADD KEY `FK_TMAHASIS_MEMBIMBIN_TPEMBIMB` (`nip`);

--
-- Indexes for table `tmatkul`
--
ALTER TABLE `tmatkul`
  ADD PRIMARY KEY (`kode_mk`);

--
-- Indexes for table `tpembimbing`
--
ALTER TABLE `tpembimbing`
  ADD PRIMARY KEY (`nip`);

--
-- Indexes for table `tprogrammbkm`
--
ALTER TABLE `tprogrammbkm`
  ADD PRIMARY KEY (`id_program`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kontrakmatkul`
--
ALTER TABLE `kontrakmatkul`
  MODIFY `id_kontrakmatkul` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `kontrakmbkm`
--
ALTER TABLE `kontrakmbkm`
  MODIFY `id_kontrakmbkm` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kontrakmatkul`
--
ALTER TABLE `kontrakmatkul`
  ADD CONSTRAINT `FK_KONTRAKM_KONTRAKMA_TMAHASIS` FOREIGN KEY (`nim`) REFERENCES `tmahasiswa` (`nim`),
  ADD CONSTRAINT `FK_KONTRAKM_KONTRAKMA_TMATKUL` FOREIGN KEY (`kode_mk`) REFERENCES `tmatkul` (`kode_mk`);

--
-- Constraints for table `kontrakmbkm`
--
ALTER TABLE `kontrakmbkm`
  ADD CONSTRAINT `FK_KONTRAKM_KONTRAKMB_TMAHASIS` FOREIGN KEY (`nim`) REFERENCES `tmahasiswa` (`nim`),
  ADD CONSTRAINT `FK_KONTRAKM_KONTRAKMB_TPROGRAM` FOREIGN KEY (`id_program`) REFERENCES `tprogrammbkm` (`id_program`),
  ADD CONSTRAINT `kontrakmbkm_ibfk_1` FOREIGN KEY (`nip_pembimbingmbkm`) REFERENCES `tpembimbing` (`nip`);

--
-- Constraints for table `tmahasiswa`
--
ALTER TABLE `tmahasiswa`
  ADD CONSTRAINT `FK_TMAHASIS_MEMBIMBIN_TPEMBIMB` FOREIGN KEY (`nip`) REFERENCES `tpembimbing` (`nip`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
