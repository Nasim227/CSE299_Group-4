-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 13, 2025 at 04:38 PM
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
-- Database: `dreamride`
--

-- --------------------------------------------------------

--
-- Table structure for table `bike`
--

CREATE TABLE `bike` (
  `Company_name` varchar(50) DEFAULT NULL,
  `Product_type` varchar(30) DEFAULT NULL,
  `Product_name` varchar(100) DEFAULT NULL,
  `Product_id` varchar(50) DEFAULT NULL,
  `Release_date` year(4) DEFAULT NULL,
  `Engine` char(20) DEFAULT NULL,
  `Mileage` varchar(10) DEFAULT NULL,
  `Prize` int(11) DEFAULT NULL,
  `Available_qnty` tinyint(4) DEFAULT NULL,
  `Company_pic` varchar(100) DEFAULT NULL,
  `Product_pic` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bike`
--

INSERT INTO `bike` (`Company_name`, `Product_type`, `Product_name`, `Product_id`, `Release_date`, `Engine`, `Mileage`, `Prize`, `Available_qnty`, `Company_pic`, `Product_pic`) VALUES
('Suzuki', 'Bike', 'GIXXER 250', 'Gixxer111', '2024', '249CC', '36+', 399950, 5, 'Images/suzuki.png', 'Images/Gixxer_250.png'),
('Suzuki', 'Bike', 'GIXXER SF', 'Gixxer112', '2015', '155CC', '64+', 349950, 5, 'Images/suzuki.png', 'Images/Gixxer_SF.png'),
('Suzuki', 'Bike', 'GIXXER', 'Gixxer113', '2015', '155CC', '64+', 249950, 5, 'Images/suzuki.png', 'Images/Gixxer.png'),
('Suzuki', 'Bike', 'GIXXER MONOTONE', 'Gixxer114', '2014', '150CC', '40+', 205950, 5, 'Images/suzuki.png', 'Images/Gixxer_monotone.png'),
('Suzuki', 'Bike', 'HAYATE EP', 'Gixxer115', '2012', '110CC', '85+', 118000, 5, 'Images/suzuki.png', 'Images/Hayate_EP.png'),
('Honda', 'Bike', 'SP125', 'honda111', '2018', '125CC', '65+', 165000, 5, 'Images/honda.png', 'Images/SP_125.png'),
('Honda', 'Bike', 'CBR 150R', 'honda112', '2024', '150CC', '40+', 600000, 5, 'Images/honda.png', 'Images/CBR.png'),
('Honda', 'Bike', 'CB Hornet 160R', 'honda113', '2020', '160CC', '40+', 212000, 5, 'Images/honda.png', 'Images/Cb_Hornet.png'),
('Honda', 'Bike', 'X-Blade', 'honda114', '2024', '160CC', '45+', 240000, 5, 'Images/honda.png', 'Images/X_blade.png'),
('Honda', 'Bike', 'Dream 110', 'honda115', '2020', '110CC', '60+', 121000, 5, 'Images/honda.png', 'Images/Dream_110.png'),
('TVS', 'Bike', 'Apache RTR 160 4v(Fi)', 'tvs111', '2018', '160CC', '40+', 274900, 5, 'Images/tvs.png', 'Images/RTR_160.png'),
('TVS', 'Bike', 'Apache RTR 160 4v(ABS)', 'tvs112', '2019', '160CC', '35+', 247900, 5, 'Images/tvs.png', 'Images/RTR_160abs.png'),
('TVS', 'Bike', 'Apache RTR 160 2V', 'tvs113', '2022', '160CC', '40+', 189999, 5, 'Images/tvs.png', 'Images/RTR_1602v.png'),
('TVS', 'Bike', 'Raider 125', 'tvs114', '2024', '125CC', '50+', 169900, 5, 'Images/tvs.png', 'Images/Raider_125.png'),
('Yamaha', 'Bike', 'R15 V3', 'yamaha111', '2017', '155CC', '40+', 525000, 5, 'Images/yamaha.png', 'Images/R_15V3.png'),
('Yamaha', 'Bike', 'MT15 – V2', 'yamaha112', '2023', '155CC', '40+', 535000, 5, 'Images/yamaha.png', 'Images/MT15_V2.png'),
('Yamaha', 'Bike', 'MT15 – V1', 'yamaha113', '2017', '155CC', '40+', 460000, 5, 'Images/yamaha.png', 'Images/MT15_V1.png'),
('Yamaha', 'Bike', 'FZ-X', 'yamaha114', '2022', '150CC', '40+', 307000, 5, 'Images/yamaha.png', 'Images/FZ_X.png'),
('Yamaha', 'Bike', 'FZS-V4', 'yamaha115', '2024', '150CC', '45+', 297500, 5, 'Images/yamaha.png', 'Images/FZS_V4.png'),
('Runner', 'Bike', 'Bolt165R', 'runner111', '2020', '160CC', '40+', 189000, 5, 'Images/runner.jpg', 'Images/Bolt165R.png'),
('Runner', 'Bike', 'Knight Rider V2', 'runner112', '2022', '150CC', '40+', 167000, 5, 'Images/runner.jpg', 'Images/KRV2.png'),
('Runner', 'Bike', 'Royal +', 'runner113', '2022', '110CC', '50+', 108000, 5, 'Images/runner.jpg', 'Images/RoyalPlus.png'),
('Yamaha', 'Bike', 'Saluto', 'yamaha116', '2025', '125CC', '65+', 158000, 5, 'Images/yamaha.png', 'Images/Saluto.png');

-- --------------------------------------------------------

--
-- Table structure for table `booked`
--

CREATE TABLE `booked` (
  `Name` varchar(200) DEFAULT NULL,
  `Email` varchar(200) NOT NULL,
  `Contact_no` varchar(11) DEFAULT NULL,
  `Company_name` varchar(50) DEFAULT NULL,
  `Product_type` varchar(30) DEFAULT NULL,
  `Product_name` varchar(100) DEFAULT NULL,
  `Product_id` varchar(50) NOT NULL,
  `Quantity` tinyint(4) DEFAULT NULL,
  `Booked_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booked`
--

INSERT INTO `booked` (`Name`, `Email`, `Contact_no`, `Company_name`, `Product_type`, `Product_name`, `Product_id`, `Quantity`, `Booked_date`) VALUES
('Admin', 'admin@dreamride.com', '01953150933', 'Castrol', 'engine_oil', 'Castrol 2L', 'castrol111', 2, '2025-04-14 00:00:00'),
('Nasim', 'nasim@gmail.com', '01953150933', 'Castrol', 'engine_oil', 'Castrol 4L', 'castrol112', 4, '2025-04-14 00:00:00'),
('Nasim', 'nasim@gmail.com', '01953150933', 'Suzuki', 'bike', 'GIXXER', 'Gixxer113', 1, '2025-04-15 00:00:00'),
('Nasim', 'nasim@gmail.com', '01953150933', 'Suzuki', 'bike', 'GIXXER', 'Gixxer113', 1, '2025-04-17 00:00:00');

-- --------------------------------------------------------

--
-- Stand-in structure for view `booked_products`
-- (See below for the actual view)
--
CREATE TABLE `booked_products` (
`Name` varchar(200)
,`Email` varchar(200)
,`Contact_no` varchar(11)
,`Company_name` varchar(50)
,`Product_type` varchar(30)
,`Product_name` varchar(100)
,`Product_id` varchar(50)
,`Quantity` tinyint(4)
,`Booked_date` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `engine_oil`
--

CREATE TABLE `engine_oil` (
  `Company_name` varchar(50) DEFAULT NULL,
  `Product_type` varchar(30) DEFAULT NULL,
  `Product_name` varchar(100) DEFAULT NULL,
  `Product_id` varchar(50) DEFAULT NULL,
  `Prize` int(11) DEFAULT NULL,
  `Available_qnty` tinyint(4) DEFAULT NULL,
  `Product_pic` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `engine_oil`
--

INSERT INTO `engine_oil` (`Company_name`, `Product_type`, `Product_name`, `Product_id`, `Prize`, `Available_qnty`, `Product_pic`) VALUES
('Castrol', 'Engine_Oil', 'Castrol 2L', 'castrol111', 1500, 15, 'Images/castrol.jpg'),
('Castrol', 'Engine_Oil', 'Castrol 4L', 'castrol112', 3000, 7, 'Images/castrol.jpg'),
('Mobil', 'Engine_Oil', 'Mobil 2L', 'mobil111', 3400, 15, 'Images/mobil.jpg'),
('Mobil', 'Engine_Oil', 'Mobil 4L', 'mobil112', 6880, 15, 'Images/mobil.jpg'),
('Motul', 'Engine_Oil', 'Motul 2L', 'motul1', 2900, 15, 'Images/motul.jpg'),
('Motul', 'Engine_Oil', 'Motul 4L', 'motul2', 5800, 15, 'Images/motul.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `helmet`
--

CREATE TABLE `helmet` (
  `Company_name` varchar(50) DEFAULT NULL,
  `Product_type` varchar(30) DEFAULT NULL,
  `Product_name` varchar(100) DEFAULT NULL,
  `Product_id` varchar(50) DEFAULT NULL,
  `Prize` int(11) DEFAULT NULL,
  `Available_qnty` tinyint(4) DEFAULT NULL,
  `Product_pic` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `helmet`
--

INSERT INTO `helmet` (`Company_name`, `Product_type`, `Product_name`, `Product_id`, `Prize`, `Available_qnty`, `Product_pic`) VALUES
('Unknown', 'Helmet', 'Helmet 1', 'helmet111', 3000, 10, 'Images/helmet1.png'),
('Unknown', 'Helmet', 'Helmet 2', 'helmet112', 2500, 10, 'Images/helmet2.png'),
('Unknown', 'Helmet', 'Helmet 3', 'helmet113', 3500, 10, 'Images/helmet3.png'),
('Unknown', 'Helmet', 'Helmet 4', 'helmet114', 2800, 10, 'Images/helmet4.png'),
('Unknown', 'Helmet', 'Helmet 5', 'helmet115', 3200, 10, 'Images/helmet6.png'),
('Unknown', 'Helmet', 'Helmet 6', 'helmet116', 3500, 10, 'Images/helmet7.png');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `Company_name` varchar(50) NOT NULL,
  `Product_type` varchar(30) NOT NULL,
  `Product_name` varchar(100) NOT NULL,
  `Product_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`Company_name`, `Product_type`, `Product_name`, `Product_id`) VALUES
('Castrol', 'Engine_Oil', 'Castrol 2L', 'castrol111'),
('Castrol', 'Engine_Oil', 'Castrol 4L', 'castrol112'),
('Honda', 'Bike', 'CB Hornet 160R', 'honda113'),
('Honda', 'Bike', 'CBR 150R', 'honda112'),
('Honda', 'Bike', 'Dream 110', 'honda115'),
('Honda', 'Bike', 'SP125', 'honda111'),
('Honda', 'Bike', 'X-Blade', 'honda114'),
('Honda', 'Scooter', 'Activa', 'honda117'),
('Honda', 'Scooter', 'DIO', 'honda116'),
('Mobil', 'Engine_Oil', 'Mobil 2L', 'mobil111'),
('Mobil', 'Engine_Oil', 'Mobil 4L', 'mobil112'),
('Motul', 'Engine_Oil', 'Motul 2L', 'motul1'),
('Motul', 'Engine_Oil', 'Motul 4L', 'motul2'),
('Runner', 'Bike', 'Bolt165R', 'runner111'),
('Runner', 'Bike', 'Knight Rider V2', 'runner112'),
('Runner', 'Bike', 'Royal +', 'runner113'),
('Runner', 'Scooter', 'Kite Plus', 'runner115'),
('Runner', 'Scooter', 'SKOOTY-110', 'runner114'),
('Suzuki', 'Bike', 'GIXXER', 'Gixxer113'),
('Suzuki', 'Bike', 'GIXXER 250', 'Gixxer111'),
('Suzuki', 'Bike', 'GIXXER MONOTONE', 'Gixxer114'),
('Suzuki', 'Bike', 'GIXXER SF', 'Gixxer112'),
('Suzuki', 'Bike', 'HAYATE EP', 'Gixxer115'),
('TVS', 'Bike', 'Apache RTR 160 2V', 'tvs113'),
('TVS', 'Bike', 'Apache RTR 160 4v(ABS)', 'tvs112'),
('TVS', 'Bike', 'Apache RTR 160 4v(Fi)', 'tvs111'),
('TVS', 'Bike', 'Raider 125', 'tvs114'),
('TVS', 'Scooter', 'Ntorq', 'tvs115'),
('TVS', 'Scooter', 'Rockz', 'tvs116'),
('TVS', 'Scooter', 'Wego', 'tvs117'),
('Unknown', 'Helmet', 'Helmet 1', 'helmet111'),
('Unknown', 'Helmet', 'Helmet 2', 'helmet112'),
('Unknown', 'Helmet', 'Helmet 3', 'helmet113'),
('Unknown', 'Helmet', 'Helmet 4', 'helmet114'),
('Unknown', 'Helmet', 'Helmet 5', 'helmet115'),
('Unknown', 'Helmet', 'Helmet 6', 'helmet116'),
('Walton', 'Scooter', 'TAKYON 1.00(26Ah)', 'walton112'),
('Walton', 'Scooter', 'TAKYON 1.00(38Ah)', 'walton111'),
('Walton', 'Scooter', 'TAKYON LEO(23Ah)', 'walton113'),
('Yamaha', 'Bike', 'FZ-X', 'yamaha114'),
('Yamaha', 'Bike', 'FZS-V4', 'yamaha115'),
('Yamaha', 'Bike', 'MT15 – V1', 'yamaha113'),
('Yamaha', 'Bike', 'MT15 – V2', 'yamaha112'),
('Yamaha', 'Bike', 'R15 V3', 'yamaha111'),
('Yamaha', 'Bike', 'Saluto', 'yamaha116'),
('Yamaha', 'Scooter', 'AEROX', 'yamaha117'),
('Yamaha', 'Scooter', 'RAY-ZR 113 FI', 'yamaha119'),
('Yamaha', 'Scooter', 'RAY-ZR 125 FI', 'yamaha118');

-- --------------------------------------------------------

--
-- Table structure for table `scooter`
--

CREATE TABLE `scooter` (
  `Company_name` varchar(50) DEFAULT NULL,
  `Product_type` varchar(30) DEFAULT NULL,
  `Product_name` varchar(100) DEFAULT NULL,
  `Product_id` varchar(50) DEFAULT NULL,
  `Release_date` year(4) DEFAULT NULL,
  `Engine` char(20) DEFAULT NULL,
  `Mileage` varchar(10) DEFAULT NULL,
  `Prize` int(11) DEFAULT NULL,
  `Available_qnty` tinyint(4) DEFAULT NULL,
  `Company_pic` varchar(100) DEFAULT NULL,
  `Product_pic` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scooter`
--

INSERT INTO `scooter` (`Company_name`, `Product_type`, `Product_name`, `Product_id`, `Release_date`, `Engine`, `Mileage`, `Prize`, `Available_qnty`, `Company_pic`, `Product_pic`) VALUES
('Yamaha', 'Scooter', 'AEROX', 'yamaha117', '2020', '155CC', '40+', 530000, 5, 'Images/yamaha.png', 'Images/Aerox.png'),
('Yamaha', 'Scooter', 'RAY-ZR 125 FI', 'yamaha118', '2020', '125CC', '45+', 270000, 5, 'Images/yamaha.png', 'Images/Ray_Zr.png'),
('Yamaha', 'Scooter', 'RAY-ZR 113 FI', 'yamaha119', '2016', '113CC', '45+', 225000, 5, 'Images/yamaha.png', 'Images/Ray_Zr2.png'),
('Walton', 'Scooter', 'TAKYON 1.00(38Ah)', 'walton111', '2021', '1.2KW Motor', '120km', 149500, 5, 'Images/walton.png', 'Images/Takyon38.png'),
('Walton', 'Scooter', 'TAKYON 1.00(26Ah)', 'walton112', '2021', '1.2KW Motor', '80km', 137500, 5, 'Images/walton.png', 'Images/Takyon38.png'),
('Walton', 'Scooter', 'TAKYON LEO(23Ah)', 'walton113', '2023', '450W Motor', '80km', 78750, 5, 'Images/walton.png', 'Images/leo.png'),
('Honda', 'Scooter', 'DIO', 'honda116', '2018', '110CC', '46+', 199000, 5, 'Images/honda.png', 'Images/DIO.png'),
('Honda', 'Scooter', 'Activa', 'honda117', '2019', '125CC', '50+', 225000, 5, 'Images/honda.png', 'Images/Activa.png'),
('Runner', 'Scooter', 'SKOOTY-110', 'runner114', '2022', '104CC', '45+', 149000, 5, 'Images/runner.jpg', 'Images/Skooty110.png'),
('Runner', 'Scooter', 'Kite Plus', 'runner115', '2022', '110CC', '45+', 120000, 5, 'Images/runner.jpg', 'Images/Kite.png'),
('TVS', 'Scooter', 'Ntorq', 'tvs115', '2021', '125CC', '40+', 204900, 5, 'Images/tvs.png', 'Images/Ntorq.png'),
('TVS', 'Scooter', 'Rockz', 'tvs116', '2025', '125CC', '45+', 153900, 5, 'Images/tvs.png', 'Images/Rockz.png'),
('TVS', 'Scooter', 'Wego', 'tvs117', '2015', '110CC', '55+', 164999, 5, 'Images/tvs.png', 'Images/Wego.png');

-- --------------------------------------------------------

--
-- Table structure for table `sell`
--

CREATE TABLE `sell` (
  `Buyer_Name` varchar(200) DEFAULT NULL,
  `Buyer_Email` varchar(200) DEFAULT NULL,
  `Buyer_Contact_no` varchar(11) DEFAULT NULL,
  `Company_name` varchar(50) DEFAULT NULL,
  `Product_type` varchar(30) DEFAULT NULL,
  `Product_name` varchar(100) DEFAULT NULL,
  `Product_id` varchar(50) DEFAULT NULL,
  `Quantity` tinyint(4) DEFAULT NULL,
  `Booked_date` datetime DEFAULT NULL,
  `Sell_date` datetime DEFAULT NULL,
  `Prize` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sell`
--

INSERT INTO `sell` (`Buyer_Name`, `Buyer_Email`, `Buyer_Contact_no`, `Company_name`, `Product_type`, `Product_name`, `Product_id`, `Quantity`, `Booked_date`, `Sell_date`, `Prize`) VALUES
('Nasim', 'nasim@gmail.com', '01953150933', 'Castrol', 'Engine_Oil', 'Castrol 4L', 'castrol112', 4, '2025-04-14 00:00:00', '2025-04-14 00:00:00', 3000),
('Nasim', 'nasim@gmail.com', '01953150933', 'Castrol', 'Engine_Oil', 'Castrol 4L', 'castrol112', 4, '2025-04-14 00:00:00', '2025-04-14 00:00:00', 3000);

-- --------------------------------------------------------

--
-- Table structure for table `signup`
--

CREATE TABLE `signup` (
  `Name` varchar(200) NOT NULL,
  `Email` varchar(200) NOT NULL,
  `Contact_no` varchar(11) NOT NULL,
  `Password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `signup`
--

INSERT INTO `signup` (`Name`, `Email`, `Contact_no`, `Password`) VALUES
('Admin', 'admin@dreamride.com', '01953150933', '$2y$10$t1XwKmC1GyadeH9YWYQtqefuiLDV1I8bPsaTV0lKrCQ28wUXG97Fq'),
('Munem', 'munem@gmail.com', '01953150933', '$2y$10$ZH9JvJxUthyrVMcIXDmwOuuJoHyw/q.mpIY8kRayiBDqkeg4j5lyq'),
('Nasim', 'nasim@gmail.com', '01953150933', '$2y$10$jsJFw8ga2GnTbU7nzpRSk.7OJRRg0B4SnjB0mGjmTLJfh4TYZOB6K');

-- --------------------------------------------------------

--
-- Stand-in structure for view `sold_products`
-- (See below for the actual view)
--
CREATE TABLE `sold_products` (
`Buyer_Name` varchar(200)
,`Buyer_Email` varchar(200)
,`Buyer_Contact_no` varchar(11)
,`Company_name` varchar(50)
,`Product_type` varchar(30)
,`Product_name` varchar(100)
,`Product_id` varchar(50)
,`Quantity` tinyint(4)
,`Booked_date` datetime
,`Sell_date` datetime
,`Prize` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwbike`
-- (See below for the actual view)
--
CREATE TABLE `vwbike` (
`Company_name` varchar(50)
,`Product_name` varchar(100)
,`Product_id` varchar(50)
,`Release_date` year(4)
,`Available_qnty` tinyint(4)
,`Engine` char(20)
,`Mileage` varchar(10)
,`Prize` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwengineoil`
-- (See below for the actual view)
--
CREATE TABLE `vwengineoil` (
`Company_name` varchar(50)
,`Product_name` varchar(100)
,`Product_id` varchar(50)
,`Available_qnty` tinyint(4)
,`Prize` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwhelmet`
-- (See below for the actual view)
--
CREATE TABLE `vwhelmet` (
`Company_name` varchar(50)
,`Product_name` varchar(100)
,`Product_id` varchar(50)
,`Available_qnty` tinyint(4)
,`Prize` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vwscooter`
-- (See below for the actual view)
--
CREATE TABLE `vwscooter` (
`Company_name` varchar(50)
,`Product_name` varchar(100)
,`Product_id` varchar(50)
,`Release_date` year(4)
,`Available_qnty` tinyint(4)
,`Engine` char(20)
,`Mileage` varchar(10)
,`Prize` int(11)
);

-- --------------------------------------------------------

--
-- Structure for view `booked_products`
--
DROP TABLE IF EXISTS `booked_products`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `booked_products`  AS SELECT `booked`.`Name` AS `Name`, `booked`.`Email` AS `Email`, `booked`.`Contact_no` AS `Contact_no`, `booked`.`Company_name` AS `Company_name`, `booked`.`Product_type` AS `Product_type`, `booked`.`Product_name` AS `Product_name`, `booked`.`Product_id` AS `Product_id`, `booked`.`Quantity` AS `Quantity`, `booked`.`Booked_date` AS `Booked_date` FROM `booked` ;

-- --------------------------------------------------------

--
-- Structure for view `sold_products`
--
DROP TABLE IF EXISTS `sold_products`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sold_products`  AS SELECT `sell`.`Buyer_Name` AS `Buyer_Name`, `sell`.`Buyer_Email` AS `Buyer_Email`, `sell`.`Buyer_Contact_no` AS `Buyer_Contact_no`, `sell`.`Company_name` AS `Company_name`, `sell`.`Product_type` AS `Product_type`, `sell`.`Product_name` AS `Product_name`, `sell`.`Product_id` AS `Product_id`, `sell`.`Quantity` AS `Quantity`, `sell`.`Booked_date` AS `Booked_date`, `sell`.`Sell_date` AS `Sell_date`, `sell`.`Prize` AS `Prize` FROM `sell` ;

-- --------------------------------------------------------

--
-- Structure for view `vwbike`
--
DROP TABLE IF EXISTS `vwbike`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwbike`  AS SELECT `bike`.`Company_name` AS `Company_name`, `bike`.`Product_name` AS `Product_name`, `bike`.`Product_id` AS `Product_id`, `bike`.`Release_date` AS `Release_date`, `bike`.`Available_qnty` AS `Available_qnty`, `bike`.`Engine` AS `Engine`, `bike`.`Mileage` AS `Mileage`, `bike`.`Prize` AS `Prize` FROM `bike` ;

-- --------------------------------------------------------

--
-- Structure for view `vwengineoil`
--
DROP TABLE IF EXISTS `vwengineoil`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwengineoil`  AS SELECT `engine_oil`.`Company_name` AS `Company_name`, `engine_oil`.`Product_name` AS `Product_name`, `engine_oil`.`Product_id` AS `Product_id`, `engine_oil`.`Available_qnty` AS `Available_qnty`, `engine_oil`.`Prize` AS `Prize` FROM `engine_oil` ;

-- --------------------------------------------------------

--
-- Structure for view `vwhelmet`
--
DROP TABLE IF EXISTS `vwhelmet`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwhelmet`  AS SELECT `helmet`.`Company_name` AS `Company_name`, `helmet`.`Product_name` AS `Product_name`, `helmet`.`Product_id` AS `Product_id`, `helmet`.`Available_qnty` AS `Available_qnty`, `helmet`.`Prize` AS `Prize` FROM `helmet` ;

-- --------------------------------------------------------

--
-- Structure for view `vwscooter`
--
DROP TABLE IF EXISTS `vwscooter`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwscooter`  AS SELECT `scooter`.`Company_name` AS `Company_name`, `scooter`.`Product_name` AS `Product_name`, `scooter`.`Product_id` AS `Product_id`, `scooter`.`Release_date` AS `Release_date`, `scooter`.`Available_qnty` AS `Available_qnty`, `scooter`.`Engine` AS `Engine`, `scooter`.`Mileage` AS `Mileage`, `scooter`.`Prize` AS `Prize` FROM `scooter` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bike`
--
ALTER TABLE `bike`
  ADD UNIQUE KEY `Product_id` (`Product_id`),
  ADD KEY `Company_name` (`Company_name`,`Product_type`,`Product_name`,`Product_id`);

--
-- Indexes for table `booked`
--
ALTER TABLE `booked`
  ADD PRIMARY KEY (`Email`,`Product_id`,`Booked_date`),
  ADD KEY `Name` (`Name`,`Email`,`Contact_no`),
  ADD KEY `Company_name` (`Company_name`,`Product_type`,`Product_name`,`Product_id`);

--
-- Indexes for table `engine_oil`
--
ALTER TABLE `engine_oil`
  ADD UNIQUE KEY `Product_id` (`Product_id`),
  ADD KEY `Company_name` (`Company_name`,`Product_type`,`Product_name`,`Product_id`);

--
-- Indexes for table `helmet`
--
ALTER TABLE `helmet`
  ADD UNIQUE KEY `Product_id` (`Product_id`),
  ADD KEY `Company_name` (`Company_name`,`Product_type`,`Product_name`,`Product_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`Company_name`,`Product_type`,`Product_name`,`Product_id`),
  ADD UNIQUE KEY `Product_name` (`Product_name`),
  ADD UNIQUE KEY `Product_id` (`Product_id`);

--
-- Indexes for table `scooter`
--
ALTER TABLE `scooter`
  ADD UNIQUE KEY `Product_id` (`Product_id`),
  ADD KEY `Company_name` (`Company_name`,`Product_type`,`Product_name`,`Product_id`);

--
-- Indexes for table `sell`
--
ALTER TABLE `sell`
  ADD KEY `Buyer_Email` (`Buyer_Email`,`Product_id`,`Booked_date`);

--
-- Indexes for table `signup`
--
ALTER TABLE `signup`
  ADD PRIMARY KEY (`Name`,`Email`,`Contact_no`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bike`
--
ALTER TABLE `bike`
  ADD CONSTRAINT `bike_ibfk_1` FOREIGN KEY (`Company_name`,`Product_type`,`Product_name`,`Product_id`) REFERENCES `product` (`Company_name`, `Product_type`, `Product_name`, `Product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `booked`
--
ALTER TABLE `booked`
  ADD CONSTRAINT `booked_ibfk_1` FOREIGN KEY (`Name`,`Email`,`Contact_no`) REFERENCES `signup` (`Name`, `Email`, `Contact_no`),
  ADD CONSTRAINT `booked_ibfk_2` FOREIGN KEY (`Company_name`,`Product_type`,`Product_name`,`Product_id`) REFERENCES `product` (`Company_name`, `Product_type`, `Product_name`, `Product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `engine_oil`
--
ALTER TABLE `engine_oil`
  ADD CONSTRAINT `engine_oil_ibfk_1` FOREIGN KEY (`Company_name`,`Product_type`,`Product_name`,`Product_id`) REFERENCES `product` (`Company_name`, `Product_type`, `Product_name`, `Product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `helmet`
--
ALTER TABLE `helmet`
  ADD CONSTRAINT `helmet_ibfk_1` FOREIGN KEY (`Company_name`,`Product_type`,`Product_name`,`Product_id`) REFERENCES `product` (`Company_name`, `Product_type`, `Product_name`, `Product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `scooter`
--
ALTER TABLE `scooter`
  ADD CONSTRAINT `scooter_ibfk_1` FOREIGN KEY (`Company_name`,`Product_type`,`Product_name`,`Product_id`) REFERENCES `product` (`Company_name`, `Product_type`, `Product_name`, `Product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sell`
--
ALTER TABLE `sell`
  ADD CONSTRAINT `sell_ibfk_1` FOREIGN KEY (`Buyer_Email`,`Product_id`,`Booked_date`) REFERENCES `booked` (`Email`, `Product_id`, `Booked_date`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
