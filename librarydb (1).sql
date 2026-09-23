-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 23, 2026 at 10:27 AM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `librarydb`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
CREATE TABLE IF NOT EXISTS `books` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_books_category` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `quantity`, `category_id`) VALUES
(1, 'Madol Doova', 'Martin Wickramasinghe', 14, 1),
(2, 'A History of the World', 'J.M. Roberts', 2, 3),
(3, 'Introduction to IT', 'John Smith', 10, 4),
(6, 'Gamperaliya', 'Martin Wikramasingha', 2, 1),
(7, 'Hath Pana', 'Kumarathunga Munidaasa', 5, 5),
(8, 'project Management', 'James Clear', 3, 4),
(9, 'Heenseraya', 'Martin Wikramasingha', 5, 5),
(10, 'English Grammer', 'Raymond Murtcy', 5, 2),
(11, 'Viragaya', 'Martin Wikramasingha', 0, 1),
(12, 'Kiyawana Nuwana', 'Kumarathunga Munidaasa', 1, 5);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`) VALUES
(1, 'Novels'),
(2, 'Language'),
(3, 'History'),
(4, 'Technology'),
(5, 'Short Story');

-- --------------------------------------------------------

--
-- Table structure for table `fines`
--

DROP TABLE IF EXISTS `fines`;
CREATE TABLE IF NOT EXISTS `fines` (
  `id` int NOT NULL AUTO_INCREMENT,
  `issue_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `book_id` int DEFAULT NULL,
  `late_days` int DEFAULT NULL,
  `fine_amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'Unpaid',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fines`
--

INSERT INTO `fines` (`id`, `issue_id`, `user_id`, `book_id`, `late_days`, `fine_amount`, `status`) VALUES
(1, 3, 2, 4, 10, 500.00, 'Paid'),
(2, 10, 5, 1, 12, 600.00, 'Paid'),
(3, 11, 2, 9, 1, 50.00, 'Paid'),
(4, 12, 4, 9, 3, 150.00, 'Paid'),
(5, 9, 2, 8, 3, 150.00, 'Paid'),
(6, 13, 4, 6, 2, 100.00, 'Paid'),
(7, 8, 5, 3, 5, 250.00, 'Paid'),
(8, 11, 4, 6, 1, 50.00, 'Paid'),
(9, 3, 2, 8, 2, 100.00, 'Paid'),
(10, 4, 2, 1, 3, 150.00, 'Paid'),
(11, 11, 2, 7, 6, 300.00, 'Paid'),
(12, 13, 2, 8, 18, 900.00, 'Unpaid');

-- --------------------------------------------------------

--
-- Table structure for table `issued_books`
--

DROP TABLE IF EXISTS `issued_books`;
CREATE TABLE IF NOT EXISTS `issued_books` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `book_id` int NOT NULL,
  `request_date` date NOT NULL,
  `issue_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `actual_return_date` date DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `expire_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `issued_books`
--

INSERT INTO `issued_books` (`id`, `user_id`, `book_id`, `request_date`, `issue_date`, `return_date`, `actual_return_date`, `status`, `expire_date`) VALUES
(1, 2, 7, '2026-07-14', '2026-06-19', '2026-07-15', '2026-07-14', 'Returned', '0000-00-00'),
(2, 2, 2, '2026-07-14', '2026-07-14', '2026-07-28', '2026-07-14', 'Returned', '0000-00-00'),
(3, 2, 8, '2026-07-14', '2026-06-23', '2026-07-12', '2026-07-14', 'Returned', '0000-00-00'),
(4, 2, 1, '2026-07-14', '2026-06-12', '2026-07-11', '2026-07-14', 'Returned', '0000-00-00'),
(5, 3, 1, '2026-07-14', '2026-07-01', '2026-07-28', '2026-07-14', 'Returned', '0000-00-00'),
(6, 4, 1, '2026-07-14', '2026-07-14', '2026-07-28', '2026-07-14', 'Returned', '0000-00-00'),
(7, 3, 2, '2026-07-14', '2026-06-16', '2026-07-07', NULL, 'Issued', '0000-00-00'),
(8, 2, 10, '2026-07-14', NULL, NULL, NULL, 'Cancelled', '0000-00-00'),
(9, 2, 1, '2026-07-14', '2026-07-14', '2026-07-28', '2026-07-14', 'Returned', '0000-00-00'),
(10, 3, 1, '2026-07-14', '2026-07-14', '2026-07-28', NULL, 'Issued', '0000-00-00'),
(11, 2, 7, '2026-07-14', '2026-06-25', '2026-07-08', '2026-07-14', 'Returned', '0000-00-00'),
(12, 2, 6, '2026-07-23', NULL, NULL, NULL, 'Cancelled', '0000-00-00'),
(13, 2, 8, '2026-06-25', '2026-06-25', '2026-07-07', '2026-07-25', 'Returned', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Unread',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=158 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `status`, `created_at`) VALUES
(1, 2, 'Book Issued Successfully', 'Your request for \"Cricket My Style\" has been approved! Please return it before 2026-07-25.', 'Read', '2026-07-10 19:28:34'),
(2, 2, 'Book Returned Successfully', 'You returned \"Cricket My Style\". Overdue: 10 days. Total Fine: Rs. 500.00 collected.', 'Read', '2026-07-10 19:31:16'),
(3, 2, 'Book Issued Successfully', 'Your request for \"Amba Yahaluwo\" has been approved! Please return it before 2026-07-01.', 'Read', '2026-07-10 19:59:05'),
(4, 2, 'Book Returned Successfully', 'You returned \"Amba Yahaluwo\". Overdue: 10 days. Total Fine: Rs. 500.00 collected.', 'Read', '2026-07-10 19:59:21'),
(5, 2, 'Book Issued Successfully', 'Your request for \"Amba Yahaluwo\" has been approved! Please return it before 2026-07-01.', 'Read', '2026-07-10 20:14:46'),
(6, 2, 'Book Returned Successfully', 'You returned \"Amba Yahaluwo\". Overdue: 10 days. Total Fine: Rs. 500.00 collected.', 'Read', '2026-07-10 20:15:20'),
(7, 2, 'Fine Payment Success', 'Your library fine payment has been processed successfully. Thank you!', 'Read', '2026-07-10 20:17:46'),
(8, 2, 'Fine Payment Success', 'Your library fine payment has been processed successfully. Thank you!', 'Read', '2026-07-10 20:17:46'),
(9, 2, 'Book Issued Successfully', 'Your request for \"Cricket My Style\" has been approved! Please return it before 2026-07-11.', 'Read', '2026-07-11 03:24:54'),
(10, 2, 'Book Returned Successfully', 'Thank you for returning \"Cricket My Style\" on time!', 'Read', '2026-07-11 03:25:36'),
(11, 2, 'Book Issued Successfully', 'Your request for \"Mahatma Gandhi Biography\" has been approved! Please return it before 2026-07-12.', 'Read', '2026-07-11 04:05:33'),
(12, 3, 'Reservation Confirmed', 'Your reservation request for the book \'Mahatma Gandhi Biography\' has been placed successfully.', 'Read', '2026-07-11 04:07:39'),
(13, 3, 'Book Ready for Collection', 'The book \"\" you reserved is now available! Please collect it from the library.', 'Read', '2026-07-11 04:08:29'),
(14, 2, 'Book Returned Successfully', 'Thank you for returning \"Mahatma Gandhi Biography\" on time!', 'Read', '2026-07-11 04:08:29'),
(15, 3, 'Reservation Ready!', 'Good news! Your reserved book \'Mahatma Gandhi Biography\' is now available for pickup at the counter.', 'Read', '2026-07-11 04:09:28'),
(16, 2, 'Book Issued Successfully', 'Your request for \"Mahatma Gandhi Biography\" has been approved! Please return it before 2026-07-25.', 'Read', '2026-07-11 04:12:59'),
(17, 4, 'Reservation Confirmed', 'Your reservation request for the book \'Mahatma Gandhi Biography\' has been placed successfully.', 'Read', '2026-07-11 04:13:37'),
(18, 4, 'Book Ready for Collection', 'The book \"Mahatma Gandhi Biography\" you reserved is now available! Please collect it from the library.', 'Read', '2026-07-11 04:53:06'),
(19, 2, 'Book Returned Successfully', 'Thank you for returning \"Mahatma Gandhi Biography\" on time!', 'Read', '2026-07-11 04:53:06'),
(20, 4, 'Reservation Ready!', 'Good news! Your reserved book \'Mahatma Gandhi Biography\' is now available for pickup at the counter.', 'Read', '2026-07-11 04:53:41'),
(21, 1, 'New Borrow Request', 'User ID 2 requested to borrow: Learn Computer Basics', 'Read', '2026-07-11 05:43:21'),
(22, 1, 'New Borrow Request', 'User ID  requested to borrow: Cricket My Style', 'Read', '2026-07-11 09:52:30'),
(23, 2, 'Book Issued Successfully', 'Your request for \"Rich Dad Poor Dad\" has been approved! Please return it before 2026-07-25.', 'Read', '2026-07-11 10:11:09'),
(24, 1, 'New Borrow Request', 'User ID  requested to borrow: Madol Doova', 'Read', '2026-07-11 10:41:01'),
(25, 1, 'New Reservation Request', 'User ID  joined the waiting list for: Mahatma Gandhi Biography', 'Read', '2026-07-11 10:41:57'),
(26, 5, 'Reservation Confirmed', 'Your reservation request for the book \'Mahatma Gandhi Biography\' has been placed successfully.', 'Read', '2026-07-11 10:42:20'),
(27, 5, 'Book Issued Successfully', 'Your request for \"Madol Doova\" has been approved! Please return it before 2026-06-29.', 'Read', '2026-07-11 10:47:27'),
(28, 5, 'Book Returned Successfully', 'You returned \"Madol Doova\". Total Fine: Rs. 600.00', 'Read', '2026-07-11 10:49:12'),
(29, 5, 'Fine Payment Success', 'Your library fine payment has been processed successfully. Thank you!', 'Read', '2026-07-11 10:52:05'),
(30, 5, 'Fine Payment Success', 'Your library fine payment has been processed successfully. Thank you!', 'Read', '2026-07-11 10:52:05'),
(31, 1, 'New Borrow Request', 'User ID  requested to borrow: To Kill a Mockingbird', 'Read', '2026-07-12 13:51:57'),
(32, 2, 'Book Issued Successfully', 'Your request for \"To Kill a Mockingbird\" has been approved! Please return it before 2026-07-11.', 'Read', '2026-07-12 14:13:58'),
(33, 2, 'Book Returned Successfully', 'You returned \"To Kill a Mockingbird\". Total Fine: Rs. 50.00', 'Read', '2026-07-12 14:15:20'),
(34, 2, 'Fine Payment Success', 'Your library fine payment has been processed successfully. Thank you!', 'Read', '2026-07-12 14:17:51'),
(35, 2, 'Fine Payment Success', 'Your library fine payment has been processed successfully. Thank you!', 'Read', '2026-07-12 14:17:51'),
(36, 1, 'New Borrow Request', 'User ID 4 requested to borrow: To Kill a Mockingbird', 'Read', '2026-07-12 14:36:40'),
(37, 4, 'Book Issued Successfully', 'Your request for \"To Kill a Mockingbird\" has been approved! Please return it before 2026-07-09.', 'Read', '2026-07-12 14:37:59'),
(38, 1, 'New Reservation Request', 'User ID 2 joined the waiting list for: To Kill a Mockingbird', 'Read', '2026-07-12 14:39:14'),
(39, 2, 'Reservation Confirmed', 'Your reservation request for the book \'To Kill a Mockingbird\' has been placed successfully.', 'Read', '2026-07-12 14:39:29'),
(40, 2, 'Book Ready for Collection', 'The book \"To Kill a Mockingbird\" you reserved is now available! Please collect it from the library.', 'Read', '2026-07-12 14:41:03'),
(41, 2, 'Reservation Ready!', 'Good news! Your reserved book \'To Kill a Mockingbird\' is now available for pickup at the counter.', 'Read', '2026-07-12 14:42:41'),
(42, 2, 'Book Returned Successfully', 'You returned \"The Great Gatsby\". Total Fine: Rs. 150.00', 'Read', '2026-07-12 14:57:58'),
(43, 2, 'Fine Payment Success', 'Your library fine payment has been processed successfully. Thank you!', 'Read', '2026-07-12 14:59:16'),
(44, 2, 'Fine Payment Success', 'Your library fine payment has been processed successfully. Thank you!', 'Read', '2026-07-12 14:59:16'),
(45, 4, 'Fine Payment Success', 'Your library fine payment has been processed successfully. Thank you!', 'Read', '2026-07-12 15:08:39'),
(46, 4, 'Fine Payment Success', 'Your library fine payment has been processed successfully. Thank you!', 'Read', '2026-07-12 15:08:39'),
(47, 1, 'New Borrow Request', 'User ID 4 requested to borrow: The World History', 'Read', '2026-07-12 15:09:58'),
(48, 4, 'Book Issued Successfully', 'Your request for \"The World History\" has been approved! Please return it before 2026-07-10.', 'Read', '2026-07-12 15:11:09'),
(49, 4, 'Book Returned Successfully', '', 'Read', '2026-07-12 15:11:49'),
(50, 4, 'Fine Payment Success', 'Your library fine payment has been processed successfully. Thank you!', 'Read', '2026-07-12 15:13:36'),
(51, 4, 'Fine Payment Success', 'Your library fine payment has been processed successfully. Thank you!', 'Read', '2026-07-12 15:13:37'),
(52, 2, 'Book Issued Successfully', 'Your request for \"Data Structures in C++\" has been approved! Please return it before 2026-07-26.', 'Read', '2026-07-12 16:07:25'),
(53, 2, 'Book Issued Successfully', 'Your request for \"To Kill a Mockingbird\" has been approved! Please return it before 2026-07-27.', 'Read', '2026-07-13 05:39:13'),
(54, 1, 'New Borrow Request', 'User ID 4 requested to borrow: To Kill a Mockingbird', 'Read', '2026-07-13 05:41:52'),
(55, 4, 'Book Issued Successfully', 'Your request for \"To Kill a Mockingbird\" has been approved! Please return it before 2026-07-27.', 'Read', '2026-07-13 05:42:31'),
(56, 1, 'New Borrow Request', 'User ID 2 requested to borrow: Introduction to Java', 'Read', '2026-07-13 08:04:09'),
(57, 2, 'Book Issued Successfully', 'Your request for \"Introduction to Java\" has been approved! Please return it before 2026-07-27.', 'Read', '2026-07-13 08:13:02'),
(58, 1, 'New Borrow Request', 'User ID 2 requested to borrow: madolduwa', 'Read', '2026-07-13 08:57:02'),
(59, 2, 'Book Issued Successfully', 'Your request for \"madolduwa\" has been approved! Please return it before 2026-07-27.', 'Read', '2026-07-13 08:59:38'),
(60, 1, 'New Reservation Request', 'User ID 4 joined the waiting list for: madolduwa', 'Read', '2026-07-13 09:52:49'),
(61, 4, 'Reservation Confirmed', 'Your reservation request for the book \'madolduwa\' has been placed successfully.', 'Read', '2026-07-13 09:53:27'),
(62, 4, 'Book Ready for Collection', 'The book \"madolduwa\" you reserved is now available! Please collect it from the library.', 'Read', '2026-07-13 09:56:58'),
(63, 4, 'Reservation Ready!', 'Good news! Your reserved book \'madolduwa\' is now available for pickup at the counter.', 'Read', '2026-07-13 09:58:13'),
(64, 1, 'New Borrow Request', 'User ID 5 requested to borrow: Gamperaliya', 'Read', '2026-07-13 12:42:18'),
(65, 1, 'New Borrow Request', 'User ID 2 requested to borrow: A History of the World', 'Read', '2026-07-13 13:17:54'),
(66, 2, 'Book Issued Successfully', 'Your request for \"A History of the World\" has been approved! Please return it before 2026-07-27.', 'Read', '2026-07-13 13:45:24'),
(67, 0, 'Borrow Request Expired', 'Your borrow request has expired because the book was not collected within 3 days.', 'Unread', '2026-07-13 13:58:23'),
(68, 0, 'Borrow Request Expired', 'Your borrow request has expired because the book was not collected within 3 days.', 'Unread', '2026-07-13 13:59:31'),
(69, 0, 'Borrow Request Expired', 'Your borrow request has expired because the book was not collected within 3 days.', 'Unread', '2026-07-13 13:59:41'),
(70, 1, 'New Borrow Request', 'User ID 2 requested to borrow: A History of the World', 'Read', '2026-07-13 14:16:30'),
(71, 2, 'Borrow Request Expired', 'Your borrow request has expired because you did not collect the book within 3 days.', 'Read', '2026-07-13 14:17:18'),
(72, 1, 'New Borrow Request', 'User ID 2 requested to borrow: A History of the World', 'Read', '2026-07-13 14:59:35'),
(73, 1, 'New Borrow Request', 'User ID 5 requested to borrow: Introduction to IT', 'Read', '2026-07-13 16:20:30'),
(74, 1, 'New Borrow Request', 'User ID 2 requested to borrow: Gamperaliya', 'Read', '2026-07-13 16:40:19'),
(75, 2, 'Book Issued Successfully', 'Your request for \"Gamperaliya\" has been approved! Please return it before 2026-07-27.', 'Read', '2026-07-13 16:41:59'),
(76, 1, 'New Borrow Request', 'User ID 5 requested to borrow: A History of the World', 'Read', '2026-07-13 16:48:56'),
(77, 4, 'Reservation Ready!', 'Good news! Your reserved book \'Unknown Book\' is now available for pickup at the counter.', 'Read', '2026-07-13 17:31:38'),
(78, 1, 'New Borrow Request', 'User ID 4 requested to borrow: Gamperaliya', 'Read', '2026-07-13 17:32:33'),
(79, 1, 'New Borrow Request', 'User ID 4 requested to borrow: Madol Doova', 'Read', '2026-07-13 17:49:26'),
(80, 1, 'New Borrow Request', 'User ID 4 requested to borrow: Introduction to IT', 'Read', '2026-07-13 17:58:09'),
(81, 4, 'Borrow Request Success', 'Your borrow request for Introduction to IT is successful! Please visit the library and collect the book within 3 days. Otherwise, your request will be automatically cancelled.', 'Read', '2026-07-13 17:58:09'),
(82, 1, 'New Borrow Request', 'User ID 4 requested to borrow: A History of the World', 'Read', '2026-07-13 18:02:37'),
(83, 4, 'Borrow Request Success', 'Success! Your request for A History of the World is confirmed. Please pick up the book within 3 days before it is automatically cancelled.', 'Read', '2026-07-13 18:02:37'),
(84, 5, 'Book Issued Successfully', 'Your request for \"Introduction to IT\" has been approved! Please return it before 2026-08-08.', 'Read', '2026-07-13 18:07:12'),
(85, 5, 'Book Returned Successfully', 'You returned \"Introduction to IT\". Total Fine: Rs. 250.00', 'Read', '2026-07-13 18:08:21'),
(86, 5, 'Fine Payment Success', 'Your library fine payment has been processed successfully. Thank you!', 'Read', '2026-07-13 18:10:28'),
(87, 5, 'Fine Payment Success', 'Your library fine payment has been processed successfully. Thank you!', 'Read', '2026-07-13 18:10:28'),
(88, 4, 'Book Issued Successfully', 'Your request for \"Gamperaliya\" has been approved! Please return it before 2026-07-12.', 'Read', '2026-07-13 18:15:25'),
(89, 4, 'Book Returned Successfully', 'You returned \"Gamperaliya\". Total Fine: Rs. 50.00', 'Read', '2026-07-13 18:15:51'),
(90, 4, 'Fine Payment Success', 'Your library fine payment has been processed successfully. Thank you!', 'Read', '2026-07-13 18:16:50'),
(91, 4, 'Fine Payment Success', 'Your library fine payment has been processed successfully. Thank you!', 'Read', '2026-07-13 18:16:50'),
(92, 4, 'Fine Payment Success', 'Your library fine payment has been processed successfully. Thank you!', 'Read', '2026-07-13 18:17:15'),
(93, 1, 'New Borrow Request', 'User ID 3 requested to borrow: Hath Pana', 'Read', '2026-07-13 19:41:22'),
(94, 3, 'Borrow Request Success', 'Success! Your request for Hath Pana is confirmed. Please pick up the book within 3 days before it is automatically cancelled.', 'Read', '2026-07-13 19:41:22'),
(95, 3, 'Book Issued Successfully', 'Your request for \"Hath Pana\" has been approved! Please return it before 2026-07-28.', 'Read', '2026-07-13 19:44:00'),
(96, 1, 'New Reservation Request', 'User ID 2 joined the waiting list for: Hath Pana', 'Read', '2026-07-13 19:45:00'),
(97, 2, 'Reservation Confirmed', 'Your reservation request for the book \'Hath Pana\' has been placed successfully.', 'Read', '2026-07-13 19:45:26'),
(98, 2, 'Book Ready for Collection', 'The book \"Hath Pana\" is now available! Please collect it by 2026-07-17 (within 3 days).', 'Read', '2026-07-13 19:47:29'),
(99, 3, 'Book Returned Successfully', 'Thank you for returning \"Hath Pana\".', 'Read', '2026-07-13 19:47:29'),
(100, 2, 'Reservation Ready!', 'Good news! Your reserved book \'Hath Pana\' is now available for pickup at the counter.', 'Read', '2026-07-13 19:48:27'),
(101, 1, 'New Borrow Request', 'User ID 2 requested to borrow: Introduction to IT', 'Read', '2026-07-13 20:01:51'),
(102, 2, 'Borrow Request Success', 'Success! Your request for Introduction to IT is confirmed. Please pick up the book within 3 days before it is automatically cancelled.', 'Read', '2026-07-13 20:01:51'),
(103, 1, 'New Reservation Request', 'User ID 5 joined the waiting list for: Hath Pana', 'Read', '2026-07-13 20:11:30'),
(104, 5, '', '', 'Read', '2026-07-13 20:11:58'),
(105, 5, '', '', 'Read', '2026-07-13 20:12:41'),
(106, 5, '', '', 'Read', '2026-07-13 20:14:20'),
(107, 1, 'New Reservation Request', 'User ID 3 joined the waiting list for: Hath Pana', 'Read', '2026-07-13 20:16:46'),
(108, 1, 'New Borrow Request', 'User ID 2 requested to borrow: Hath Pana', 'Read', '2026-07-13 20:51:07'),
(109, 2, 'Borrow Request Success', 'Success! Your request for Hath Pana is confirmed. Please pick up the book within 3 days before it is automatically cancelled.', 'Read', '2026-07-13 20:51:07'),
(110, 2, 'Book Issued Successfully', 'Your request for \"Hath Pana\" has been approved! Please return it before 2026-07-28.', 'Read', '2026-07-13 20:52:37'),
(111, 1, 'New Borrow Request', 'User ID 2 requested to borrow: A History of the World', 'Read', '2026-07-14 01:22:54'),
(112, 2, 'Borrow Request Success', 'Success! Your request for A History of the World is confirmed. Please pick up the book within 3 days before it is automatically cancelled.', 'Read', '2026-07-14 01:22:54'),
(113, 2, 'Book Returned Successfully', 'Thank you for returning \"Hath Pana\".', 'Read', '2026-07-14 01:28:44'),
(114, 1, 'New Borrow Request', 'User ID 2 requested to borrow: project Management', 'Read', '2026-07-14 01:42:33'),
(115, 2, 'Borrow Request Success', 'Success! Your request for project Management is confirmed. Please pick up the book within 3 days before it is automatically cancelled.', 'Read', '2026-07-14 01:42:33'),
(116, 2, 'Book Issued Successfully', 'Your request for \"project Management\" has been approved! Please return it before 2026-07-12.', 'Read', '2026-07-14 01:44:24'),
(117, 1, 'New Reservation Request', 'User ID 3 joined the waiting list for: project Management', 'Read', '2026-07-14 01:46:06'),
(118, 3, 'Book Ready for Collection', 'The book \"project Management\" is now available! Please collect it by 2026-07-17 (within 3 days).', 'Read', '2026-07-14 01:47:17'),
(119, 2, 'Book Returned Successfully', 'You returned \"project Management\". Total Fine: Rs. 100.00', 'Read', '2026-07-14 01:47:17'),
(120, 2, 'Book Issued Successfully', 'Your request for \"A History of the World\" has been approved! Please return it before 2026-07-28.', 'Read', '2026-07-14 01:55:26'),
(121, 2, 'Book Returned Successfully', 'Thank you for returning \"A History of the World\".', 'Read', '2026-07-14 01:55:41'),
(122, 2, 'Fine Payment Success', 'Your library fine payment has been processed successfully. Thank you!', 'Read', '2026-07-14 01:58:00'),
(123, 2, 'Fine Payment Success', 'Your library fine payment has been processed successfully. Thank you!', 'Read', '2026-07-14 01:58:00'),
(124, 1, 'New Borrow Request', 'User ID 2 requested to borrow: Madol Doova', 'Read', '2026-07-14 01:58:55'),
(125, 2, 'Borrow Request Success', 'Success! Your request for Madol Doova is confirmed. Please pick up the book within 3 days before it is automatically cancelled.', 'Read', '2026-07-14 01:58:55'),
(126, 2, 'Book Issued Successfully', 'Your request for \"Madol Doova\" has been approved! Please return it before 2026-07-11.', 'Read', '2026-07-14 02:01:26'),
(127, 1, 'New Borrow Request', 'User ID 3 requested to borrow: Madol Doova', 'Read', '2026-07-14 02:14:56'),
(128, 3, 'Borrow Request Success', 'Success! Your request for Madol Doova is confirmed. Please pick up the book within 3 days before it is automatically cancelled.', 'Read', '2026-07-14 02:14:56'),
(129, 3, 'Book Issued Successfully', 'Your request for \"Madol Doova\" has been approved! Please return it before 2026-07-28.', 'Read', '2026-07-14 02:17:02'),
(130, 1, 'New Reservation Request', 'User ID 4 joined the waiting list for: Madol Doova', 'Read', '2026-07-14 02:17:38'),
(131, 4, 'Book Ready for Collection', 'The book \"Madol Doova\" is now available! Please collect it by 2026-07-17 (within 3 days).', 'Unread', '2026-07-14 02:18:26'),
(132, 2, 'Book Returned Successfully', 'You returned \"Madol Doova\". Total Fine: Rs. 150.00', 'Read', '2026-07-14 02:18:26'),
(133, 3, 'Book Returned Successfully', 'Thank you for returning \"Madol Doova\".', 'Read', '2026-07-14 02:19:32'),
(134, 4, 'Book Issued Successfully', 'Your request for \"Madol Doova\" has been approved! Please return it before 2026-07-28.', 'Unread', '2026-07-14 02:20:11'),
(135, 4, 'Book Returned Successfully', 'Thank you for returning \"Madol Doova\".', 'Unread', '2026-07-14 02:25:59'),
(136, 1, 'New Borrow Request', 'User ID 3 requested to borrow: A History of the World', 'Read', '2026-07-14 02:31:34'),
(137, 3, 'Borrow Request Success', 'Success! Your request for A History of the World is confirmed. Please pick up the book within 3 days before it is automatically cancelled.', 'Read', '2026-07-14 02:31:34'),
(138, 3, 'Book Issued Successfully', 'Your request for \"A History of the World\" has been approved! Please return it before 2026-07-07.', 'Read', '2026-07-14 02:33:21'),
(139, 1, 'New Borrow Request', 'User ID 2 requested to borrow: English Grammer', 'Read', '2026-07-14 03:52:53'),
(140, 2, 'Borrow Request Success', 'Success! Your request for English Grammer is confirmed. Please pick up the book within 3 days before it is automatically cancelled.', 'Read', '2026-07-14 03:52:53'),
(141, 1, 'New Borrow Request', 'User ID 2 requested to borrow: Madol Doova', 'Read', '2026-07-14 05:11:29'),
(142, 2, 'Borrow Request Success', 'Success! Your request for Madol Doova is confirmed. Please pick up the book within 3 days before it is automatically cancelled.', 'Read', '2026-07-14 05:11:29'),
(143, 2, 'Book Issued Successfully', 'Your request for \"Madol Doova\" has been approved! Please return it before 2026-07-28.', 'Read', '2026-07-14 05:13:46'),
(144, 1, 'New Reservation Request', 'User ID 3 joined the waiting list for: Madol Doova', 'Read', '2026-07-14 05:15:56'),
(145, 3, 'Book Ready for Collection', 'The book \"Madol Doova\" is now available! Please collect it by 2026-07-17 (within 3 days).', 'Read', '2026-07-14 05:17:04'),
(146, 2, 'Book Returned Successfully', 'Thank you for returning \"Madol Doova\".', 'Read', '2026-07-14 05:17:04'),
(147, 3, 'Book Issued Successfully', 'Your request for \"Madol Doova\" has been approved! Please return it before 2026-07-28.', 'Read', '2026-07-14 05:18:57'),
(148, 1, 'New Borrow Request', 'User ID 2 requested to borrow: Hath Pana', 'Read', '2026-07-14 05:20:41'),
(149, 2, 'Borrow Request Success', 'Success! Your request for Hath Pana is confirmed. Please pick up the book within 3 days before it is automatically cancelled.', 'Read', '2026-07-14 05:20:41'),
(150, 2, 'Book Issued Successfully', 'Your request for \"Hath Pana\" has been approved! Please return it before 2026-07-08.', 'Read', '2026-07-14 05:22:02'),
(151, 2, 'Book Returned Successfully', 'You returned \"Hath Pana\". Total Fine: Rs. 300.00', 'Read', '2026-07-14 05:22:27'),
(152, 2, 'Fine Payment Success', 'Your library fine payment has been processed successfully. Thank you!', 'Read', '2026-07-14 05:24:07'),
(153, 2, 'Fine Payment Success', 'Your library fine payment has been processed successfully. Thank you!', 'Read', '2026-07-14 05:24:07'),
(154, 1, 'New Borrow Request', 'User ID 2 requested to borrow: Gamperaliya', 'Unread', '2026-07-23 14:43:36'),
(155, 2, 'Borrow Request Success', 'Success! Your request for Gamperaliya is confirmed. Please pick up the book within 3 days before it is automatically cancelled.', 'Read', '2026-07-23 14:43:36'),
(156, 2, 'Book Issued Successfully', 'Your request for \"project Management\" has been approved! Please return it before 2026-07-07.', 'Read', '2026-07-25 06:22:55'),
(157, 2, 'Book Returned Successfully', 'You returned \"project Management\". Total Fine: Rs. 900.00', 'Read', '2026-07-25 06:23:08');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
CREATE TABLE IF NOT EXISTS `reservations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `book_id` int NOT NULL,
  `reserve_date` date NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `expiry_date` date DEFAULT NULL,
  `is_quantity_restored` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_res_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `book_id`, `reserve_date`, `status`, `expiry_date`, `is_quantity_restored`) VALUES
(1, 3, 8, '2026-07-14', 'Cancelled', '2026-07-17', 0),
(2, 4, 1, '2026-07-14', 'Approved', '2026-07-17', 0),
(3, 3, 1, '2026-07-14', 'Approved', '2026-07-17', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `member_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_pic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `address`, `email`, `username`, `password`, `role`, `member_id`, `profile_pic`) VALUES
(1, 'Library Admin', 'Colombo', 'thaniyaumayangani5@gmail.com', 'admin', '$2y$10$LQjmgmgAEzWa8qAFIGbsJOH6xH37Uz.GwTx/bi1G0IMyA.hpa6rSa', 'admin', NULL, NULL),
(2, 'Thaniya Thennakoon', 'Matale', 'thaniyathennakoon@gmail.com', 'thaniya', '$2y$10$MIXLQUXxhu2l.iOWuPspZOMBmcgnluvxvn15sndHLmq4iNajvshPO', 'user', 'LIB2026002', NULL),
(3, 'Dilshani senarathna', 'kandy', 'dilshani@gmail.com', 'dilshani', '$2y$10$yPFwjzaRPBWdfdVCYKvHLukLUMjr2iOz1Ggxq2PjumGuEb5FXbkw.', 'user', 'LIB2026003', NULL),
(4, 'Dewmi Jayarathna', 'Matara', 'dewmi@gmail.com', 'dewmi', '$2y$10$ETe5h50WI7UGmS1L/bL/COfWPC/5GNRDstb2BLzvo.eGknljVNdwW', 'user', 'LIB2026004', NULL),
(5, 'Shihara Sandeepani', 'Matara', 'shiharasandeepani@gmail.com', 'shihara', '$2y$10$hvSUAH1pYUAIkOkW3F.j8.4DaS0pU4n9W8SkbkBtWowN3oOHeniLS', 'user', 'LIB2026005', 'uploads/user_5_1783766382.jpg'),
(6, 'Nimal kumara', 'kandy', 'nimal@gmail.com', 'nimal', '$2y$10$JAFLyr1zzy.umOnfqO81juSK94tdWkEyGSHBUNb7RRLGTkpVovp4O', 'user', 'LIB2026006', NULL),
(7, 'anurada perera', 'Matale', 'anurada@gmail.com', 'anurada', '$2y$10$s/U.i12Nn1tTreGYpHnfYeKno0XG4NZOukPvfyOE/VCtHr3tLWFUC', 'user', 'LIB2026007', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
