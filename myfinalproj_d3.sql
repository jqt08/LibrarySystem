-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 05, 2024 at 02:24 PM
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
-- Database: `myfinalproj`
--

-- --------------------------------------------------------

--
-- Table structure for table `book`
--

CREATE TABLE `book` (
  `book_id` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `publication_date` date NOT NULL,
  `category` varchar(255) NOT NULL,
  `status` enum('archived','borrowed','available') NOT NULL DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book`
--

INSERT INTO `book` (`book_id`, `title`, `publication_date`, `category`, `status`) VALUES
('BAAPR282015-NFC00001', 'Bahay Kubo', '2015-04-04', 'Non-Fiction', 'available'),
('ONJUL052024-FIC00003', 'One', '2024-07-03', 'Fiction', 'borrowed'),
('TEJAN052015-FIC00002', 'Test Book ', '2015-01-14', 'Fiction', 'available'),
('TEJUL052024-FIC00005', 'Testing lang', '2024-07-01', 'Fiction', 'available'),
('THAPR281925-FIC00001', 'The Great Gatsby', '1925-04-10', 'Fiction', 'available'),
('THJUN052019-FIC00004', 'Three', '2019-06-05', 'Fiction', 'available'),
('TRNOV051992-NFC00002', 'Trial Book', '1992-11-17', 'Non-Fiction', 'available'),
('TWJAN052024-NFC00003', 'Two', '2024-01-02', 'Non-Fiction', 'borrowed');

--
-- Triggers `book`
--
DELIMITER $$
CREATE TRIGGER `generate_book_id` BEFORE INSERT ON `book` FOR EACH ROW BEGIN
    DECLARE v_prefix VARCHAR(2);
    DECLARE v_month VARCHAR(3);
    DECLARE v_day CHAR(2);
    DECLARE v_year CHAR(4);
    DECLARE v_category_code VARCHAR(3);
    DECLARE v_counter CHAR(5);

    -- Generate the prefix from the first 2 letters of the title
    SET v_prefix = UPPER(LEFT(NEW.title, 2));

    -- Generate the month abbreviation
    SET v_month = UPPER(DATE_FORMAT(NEW.publication_date, '%b'));

    -- Extract the current day and year
    SET v_day = DATE_FORMAT(NOW(), '%d');
    SET v_year = DATE_FORMAT(NEW.publication_date, '%Y');

    -- Set the category code (example: FIC for Fiction)
    IF NEW.category = 'Fiction' THEN
        SET v_category_code = 'FIC';
    ELSEIF NEW.category = 'Non-Fiction' THEN
        SET v_category_code = 'NFC';
    -- Add more categories as needed
    ELSE
        SET v_category_code = 'OTH';
    END IF;

    -- Count the number of books in the library for the given category
    -- synchronize the counter ????????????
    SELECT COUNT(*) + 1 INTO v_counter FROM book WHERE category = NEW.category;

    -- Format the count with leading zeros
    SET v_counter = LPAD(v_counter, 5, '0');

    -- Generate the book_id
    SET NEW.book_id = CONCAT(v_prefix, v_month, v_day, v_year, '-', v_category_code, v_counter);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `librarian`
--

CREATE TABLE `librarian` (
  `librarian_id` int(11) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `librarian`
--

INSERT INTO `librarian` (`librarian_id`, `fname`, `lname`, `password`) VALUES
(1, 'Aaron', 'Garcia', '123'),
(2, 'John', 'Semillano', '123');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` int(11) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `book_id1` varchar(50) DEFAULT NULL,
  `book_id2` varchar(50) DEFAULT NULL,
  `fine` decimal(10,2) DEFAULT 0.00,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `fname`, `lname`, `book_id1`, `book_id2`, `fine`, `password`) VALUES
(123, 'x', 'x', 'ONJUL052024-FIC00003', 'TWJAN052024-NFC00003', 0.00, '123'),
(234, 's', 's', NULL, NULL, 0.00, '123'),
(1234, 'Jake', 'Jacobs', NULL, NULL, 0.00, '123'),
(202200000, 'Aaron', 'Garcia', NULL, NULL, 0.00, ''),
(202211565, 'John Patrick', 'Semillano', NULL, NULL, 0.00, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`book_id`);

--
-- Indexes for table `librarian`
--
ALTER TABLE `librarian`
  ADD PRIMARY KEY (`librarian_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `book_id1` (`book_id1`),
  ADD KEY `book_id2` (`book_id2`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `librarian`
--
ALTER TABLE `librarian`
  MODIFY `librarian_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=202211566;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`book_id1`) REFERENCES `book` (`book_id`),
  ADD CONSTRAINT `student_ibfk_2` FOREIGN KEY (`book_id2`) REFERENCES `book` (`book_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
