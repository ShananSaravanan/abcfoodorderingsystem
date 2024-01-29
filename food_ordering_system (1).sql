-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 29, 2024 at 09:08 PM
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
-- Database: `food_ordering_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `chef`
--

CREATE TABLE `chef` (
  `id` int(11) NOT NULL,
  `Chef_Name` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `Chef_Contact` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chef`
--

INSERT INTO `chef` (`id`, `Chef_Name`, `email`, `password`, `Chef_Contact`) VALUES
(4, 'Dreshya', 'shananmessi10@gmail.com', '$2y$10$YvjhPbplI3gv0FffcDkBS.gYwn7wBS.lIpDB3yiC5tSHoYWgtv5g.', '123'),
(5, 'Shanan', 'shananmessi10@gmail.com', '$2y$10$nz2G/c.nmvQSqf84ETN54OO9yXyp9U69hma1IA.GUP.DQeSy/vCwC', '234');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id` int(100) NOT NULL,
  `Cust_Name` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `Cust_contact` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id`, `Cust_Name`, `email`, `password`, `Cust_contact`) VALUES
(1, 'Wesh', 'wesh@gmail.com', '$2y$10$ClqsRa4Vt2AFqx9PG4gpuOvn9R.VGOyDAa4rWqR2..j67Sc.Tzksq', '231@gmail.com'),
(2, 'Shanan Saravanan', 'shananmessi10@gmail.com', '$2y$10$8JwUlf0gUqub9FeHDots8OpOebzrEwW..d61JQNKXc7wtQIjq/x5S', '0198765421');

-- --------------------------------------------------------

--
-- Table structure for table `customerorder`
--

CREATE TABLE `customerorder` (
  `id` int(11) NOT NULL,
  `Customer_ID` int(100) NOT NULL,
  `Order_Date` date NOT NULL,
  `Status` varchar(255) NOT NULL,
  `Chef_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deliveryhistory`
--

CREATE TABLE `deliveryhistory` (
  `id` int(11) NOT NULL,
  `Order_ID` int(11) NOT NULL,
  `Delivery_Date` date NOT NULL,
  `Delivery_Status` varchar(255) NOT NULL,
  `Delivery_Time` time NOT NULL,
  `Personnel_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deliverypersonnel`
--

CREATE TABLE `deliverypersonnel` (
  `id` int(11) NOT NULL,
  `Personnel_Name` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `Contact_Information` varchar(255) NOT NULL,
  `Delivery_VehicleInformation` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deliverypersonnel`
--

INSERT INTO `deliverypersonnel` (`id`, `Personnel_Name`, `email`, `password`, `Contact_Information`, `Delivery_VehicleInformation`) VALUES
(1, 'zamir', 'shananmessi10@gmail.com', '$2y$10$42C4B/hfEIExlBaNC9davOkWXMaBbQuPyS62Nk4YbbiirufWceg.e', '123', '123');

-- --------------------------------------------------------

--
-- Table structure for table `inventorymanagement`
--

CREATE TABLE `inventorymanagement` (
  `Ingredient_ID` int(11) NOT NULL,
  `Ingredient_Name` varchar(255) NOT NULL,
  `Quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `vendor_id` int(100) NOT NULL,
  `Item_Name` varchar(255) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `Price` float(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `vendor_id`, `Item_Name`, `Description`, `Price`) VALUES
(2, 127, 'Nasi Lemak Curry Udang', 'Yes yes', 9.50),
(4, 127, 'Nasi Lemak Ayam', 'Delicious Food', 1.50),
(5, 127, 'Nasi Lemak Ayam', 'Delicious Food', 1.50);

-- --------------------------------------------------------

--
-- Table structure for table `vendor`
--

CREATE TABLE `vendor` (
  `id` int(100) NOT NULL,
  `Vendor_Name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `Vendor_Contact` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor`
--

INSERT INTO `vendor` (`id`, `Vendor_Name`, `email`, `password`, `Vendor_Contact`) VALUES
(123, '123@gmail.com', '$2y$10$nqtsljayhd31BLcYwhU1xeRp/HnbBQmowEjr/cJTMNNwPQikwr5Q.', '123', ''),
(124, '123@gmail.com', '$2y$10$HmTZFLRwuxXsm.cbZ1oNQOVc.YIFlKstH.HyBYQ3iAJ7oPFDuVkMy', '123', ''),
(125, '123@gmail.com', '$2y$10$UxqTckHP7X5vX3HRmJEjuOhP0cJ6bF.nBm3ccBBomBIwpo44YQvPG', '123', ''),
(126, '123', '123@gmail.com', '$2y$10$xFd5ipNKzeJESaICvHh5FujQWVXwtBS0.FiGaoXp4FWJX5OMF39jq', '231'),
(127, 'Shanan', 'shananmessi10@gmail.com', '$2y$10$okMMNgFHV1Q.biBesq.MMOLnxMJNC.uO0EpbBzOFRDnzR8ry8nqxK', '+60174610143');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chef`
--
ALTER TABLE `chef`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customerorder`
--
ALTER TABLE `customerorder`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Chef_ID` (`Chef_ID`),
  ADD KEY `Customer_ID` (`Customer_ID`);

--
-- Indexes for table `deliveryhistory`
--
ALTER TABLE `deliveryhistory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Order_ID` (`Order_ID`),
  ADD KEY `Personnel_ID` (`Personnel_ID`);

--
-- Indexes for table `deliverypersonnel`
--
ALTER TABLE `deliverypersonnel`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventorymanagement`
--
ALTER TABLE `inventorymanagement`
  ADD PRIMARY KEY (`Ingredient_ID`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `vendor`
--
ALTER TABLE `vendor`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chef`
--
ALTER TABLE `chef`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customerorder`
--
ALTER TABLE `customerorder`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deliveryhistory`
--
ALTER TABLE `deliveryhistory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deliverypersonnel`
--
ALTER TABLE `deliverypersonnel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventorymanagement`
--
ALTER TABLE `inventorymanagement`
  MODIFY `Ingredient_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `vendor`
--
ALTER TABLE `vendor`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customerorder`
--
ALTER TABLE `customerorder`
  ADD CONSTRAINT `chef_chefID` FOREIGN KEY (`Chef_ID`) REFERENCES `chef` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `customer_customerid` FOREIGN KEY (`Customer_ID`) REFERENCES `customer` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `deliveryhistory`
--
ALTER TABLE `deliveryhistory`
  ADD CONSTRAINT `deliveryhistory_ibfk_1` FOREIGN KEY (`Order_ID`) REFERENCES `customerorder` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `deliveryhistory_ibfk_2` FOREIGN KEY (`Personnel_ID`) REFERENCES `deliverypersonnel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `vendor_vendorid` FOREIGN KEY (`vendor_id`) REFERENCES `vendor` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
