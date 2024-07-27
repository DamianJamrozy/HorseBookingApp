-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Lip 27, 2024 at 06:18 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `klub_jezdziecki`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `administrators`
--

CREATE TABLE `administrators` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `administrators`
--

INSERT INTO `administrators` (`id`, `user_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `user_id`) VALUES
(1, 2),
(2, 3);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `horses`
--

CREATE TABLE `horses` (
  `id` int(11) NOT NULL,
  `imie` varchar(50) NOT NULL,
  `kolor` int(11) DEFAULT NULL,
  `rasa` int(11) DEFAULT NULL,
  `stan_zdrowia` int(11) DEFAULT NULL,
  `rodzaj_konia` int(11) DEFAULT NULL,
  `opis` text DEFAULT NULL,
  `data_urodzenia` date DEFAULT NULL,
  `wzrost` int(11) DEFAULT NULL,
  `zdjecie` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `horses`
--

INSERT INTO `horses` (`id`, `imie`, `kolor`, `rasa`, `stan_zdrowia`, `rodzaj_konia`, `opis`, `data_urodzenia`, `wzrost`, `zdjecie`) VALUES
(1, 'Bajka', 1, 1, 1, 1, 'Koń o spokojnym charakterze, idealny dla początkujących jeźdźców.', '2022-01-26', 160, 'img/horses/66a3696c37feb.jpg'),
(2, 'Arabeska', 2, 2, 2, 2, 'Koń o dużej energii, wymaga doświadczonego jeźdźca.', '2015-08-20', 155, 'img/horses/arabeska.jpg'),
(3, 'Dżoker', 3, 1, 1, 3, 'Koń o wyjątkowo przyjaznym usposobieniu, doskonały do jazdy rekreacyjnej.', '2010-03-10', 165, 'img/horses/dżoker.jpg'),
(4, 'Miranda', 4, 1, 1, 4, 'Koń o szybkim biegu, doskonały do treningów wyścigowych.', '2016-11-25', 158, 'img/horses/miranda.jpg');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `horses_breed`
--

CREATE TABLE `horses_breed` (
  `id_breed` int(11) NOT NULL,
  `rasa` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `horses_breed`
--

INSERT INTO `horses_breed` (`id_breed`, `rasa`) VALUES
(1, 'Zimnokrwisty'),
(2, 'Arabski'),
(3, 'Koń Pełnej Krwi Angielskiej');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `horses_color`
--

CREATE TABLE `horses_color` (
  `id_color` int(11) NOT NULL,
  `kolor` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `horses_color`
--

INSERT INTO `horses_color` (`id_color`, `kolor`) VALUES
(1, 'Czarny'),
(2, 'Biały'),
(3, 'Kasztanowy'),
(4, 'Gniady');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `horses_health`
--

CREATE TABLE `horses_health` (
  `id_health` int(11) NOT NULL,
  `stan_zdrowia` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `horses_health`
--

INSERT INTO `horses_health` (`id_health`, `stan_zdrowia`) VALUES
(1, 'Zdrowy'),
(2, 'Stabilny'),
(3, 'Chory');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `horses_type`
--

CREATE TABLE `horses_type` (
  `id_type` int(11) NOT NULL,
  `rodzaj` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `horses_type`
--

INSERT INTO `horses_type` (`id_type`, `rodzaj`) VALUES
(1, 'Wierzchowy'),
(2, 'Wyścigowy'),
(3, 'Kawaleryjski'),
(4, 'Pokazowy'),
(5, 'Rekreacyjny'),
(6, 'Wytrzymałościowy'),
(7, 'Westernowy'),
(8, 'Sportowy'),
(9, 'Paradny'),
(10, 'Roboczy');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `klient_id` int(11) NOT NULL,
  `kon_id` int(11) NOT NULL,
  `trener_id` int(11) NOT NULL,
  `data_rezerwacji_od` datetime NOT NULL,
  `data_rezerwacji_do` datetime NOT NULL,
  `reservation_status` enum('aktywna','anulowana') DEFAULT 'aktywna'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `trainers`
--

CREATE TABLE `trainers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainers`
--

INSERT INTO `trainers` (`id`, `user_id`) VALUES
(4, 5),
(5, 6),
(12, 22);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `imie` varchar(50) NOT NULL,
  `nazwisko` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `ulica` varchar(100) NOT NULL,
  `nr_domu` varchar(10) NOT NULL,
  `kod_pocztowy` varchar(10) NOT NULL,
  `miasto` varchar(50) NOT NULL,
  `telefon` varchar(20) NOT NULL,
  `zdjecie` varchar(255) DEFAULT NULL,
  `hashed_password` varchar(255) NOT NULL,
  `rola` enum('klient','trener','administrator') NOT NULL DEFAULT 'klient',
  `stopien_jezdziecki` enum('początkujący','średniozaawansowany','zaawansowany') DEFAULT 'początkujący'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `imie`, `nazwisko`, `email`, `ulica`, `nr_domu`, `kod_pocztowy`, `miasto`, `telefon`, `zdjecie`, `hashed_password`, `rola`, `stopien_jezdziecki`) VALUES
(1, 'Jan', 'Nowak', 'admin@admin', 'ul. Administratorów 1', 'A1', '00-001', 'Warszawa', '111222333', 'img/employee/admin.jpg', '$2y$10$x3K93dHr9GH5Qsq/z7foveqAsAT416yQ.vnQYFmQDxWWeFT4FyOgm', 'administrator', 'początkujący'),
(2, 'Anna', 'Kowalska', 'klient1@klient.pl', 'ul. Testowa 2', '2B', '10-200', 'Poznań', '444555666', 'img/users/klient1.jpg', '$2y$10$JH2I0AqlbUc3vW.enko.7ePw9iqUjx0SXq0ONNkvhWEPuTh/t/C/G', 'klient', 'początkujący'),
(3, 'Piotr', 'Wiśniewski', 'piotr.wisniewski@example.com', 'ul. Spacerowa 3', '3C', '20-300', 'Kraków', '555666777', 'img/users/klient2.jpg', '$2y$10$.j./muNElNoEobNhirsliOYErz/0MbWolIt.Ch9DODj5AFOLcXchS', 'klient', 'początkujący'),
(5, 'Adam', 'Nowakowski', 'adam.nowakowski@example.com', 'ul. Szkoleniowa 6', '6F', '50-600', 'Wrocław', '888999000', 'img/employee/trener2.jpg', '$2y$10$DvjVg3th8dhHA3qn8b0EbuPkvThYIaHyZBEjUEMWN6aSkVXX14qfS', 'trener', 'zaawansowany'),
(6, 'Katarzyna', 'Wójcik', 'katarzyna.wojcik@example.com', 'ul. Konna 7', '7G', '60-700', 'Katowice', '999000111', 'img/employee/trener3.jpg', '$2y$10$dqYlsTwyPUle87Bj7O3aFO85yovrh/sW9s5r2NgcDARaFskC646W6', 'trener', 'początkujący'),
(22, 'Test', 'Trener', 'trener@trener', 'ul', '10', '33-333', 'Rzeszów', '555555555', 'img/users/klient1.jpg', '$2y$10$9QwGDQ8kY0SQcdHb9x2RGeJVtTtK6N4zJqcnsq7uflEo64eEA//Bm', 'trener', 'zaawansowany'),
(25, 'Test', 'Klient', 'klient@klient', 'ul', '10', '33-333', 'Rzeszów', '666666666', 'img/users/66a37d1e2d2d9.jpg', '$2y$10$fhW.4E8q2UHTDRrCRHD8HOVcstvWt1U1som4zufdX7RUf1c/c4wze', '', 'początkujący');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `administrators`
--
ALTER TABLE `administrators`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `horses`
--
ALTER TABLE `horses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kolor` (`kolor`,`rasa`,`stan_zdrowia`,`rodzaj_konia`),
  ADD KEY `rasa` (`rasa`),
  ADD KEY `stan_zdrowia` (`stan_zdrowia`),
  ADD KEY `rodzaj_konia` (`rodzaj_konia`);

--
-- Indeksy dla tabeli `horses_breed`
--
ALTER TABLE `horses_breed`
  ADD PRIMARY KEY (`id_breed`);

--
-- Indeksy dla tabeli `horses_color`
--
ALTER TABLE `horses_color`
  ADD PRIMARY KEY (`id_color`);

--
-- Indeksy dla tabeli `horses_health`
--
ALTER TABLE `horses_health`
  ADD PRIMARY KEY (`id_health`);

--
-- Indeksy dla tabeli `horses_type`
--
ALTER TABLE `horses_type`
  ADD PRIMARY KEY (`id_type`);

--
-- Indeksy dla tabeli `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `klient_id` (`klient_id`),
  ADD KEY `kon_id` (`kon_id`),
  ADD KEY `trener_id` (`trener_id`);

--
-- Indeksy dla tabeli `trainers`
--
ALTER TABLE `trainers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `administrators`
--
ALTER TABLE `administrators`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `horses`
--
ALTER TABLE `horses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `horses_breed`
--
ALTER TABLE `horses_breed`
  MODIFY `id_breed` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `horses_color`
--
ALTER TABLE `horses_color`
  MODIFY `id_color` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `horses_health`
--
ALTER TABLE `horses_health`
  MODIFY `id_health` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `horses_type`
--
ALTER TABLE `horses_type`
  MODIFY `id_type` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trainers`
--
ALTER TABLE `trainers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `administrators`
--
ALTER TABLE `administrators`
  ADD CONSTRAINT `administrators_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `horses`
--
ALTER TABLE `horses`
  ADD CONSTRAINT `horses_ibfk_1` FOREIGN KEY (`rasa`) REFERENCES `horses_breed` (`id_breed`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `horses_ibfk_2` FOREIGN KEY (`stan_zdrowia`) REFERENCES `horses_health` (`id_health`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `horses_ibfk_3` FOREIGN KEY (`rodzaj_konia`) REFERENCES `horses_type` (`id_type`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `horses_ibfk_4` FOREIGN KEY (`kolor`) REFERENCES `horses_color` (`id_color`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`klient_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`kon_id`) REFERENCES `horses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_3` FOREIGN KEY (`trener_id`) REFERENCES `trainers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trainers`
--
ALTER TABLE `trainers`
  ADD CONSTRAINT `trainers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
