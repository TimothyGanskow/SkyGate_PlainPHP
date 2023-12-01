-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 01. Dez 2023 um 12:16
-- Server-Version: 10.4.28-MariaDB
-- PHP-Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `skydatasystems`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `postcode`
--

CREATE TABLE `postcode` (
  `id` bigint(20) NOT NULL,
  `postcode` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `postcode`
--

INSERT INTO `postcode` (`id`, `postcode`) VALUES
(14, 20000),
(15, 30000),
(16, 40000),
(17, 50000),
(18, 60000);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `registry`
--

CREATE TABLE `registry` (
  `id` bigint(20) NOT NULL,
  `userID` bigint(20) NOT NULL,
  `postID` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `telefon` varchar(100) NOT NULL,
  `postcode` int(100) NOT NULL,
  `place` varchar(100) NOT NULL,
  `terms` tinyint(1) NOT NULL,
  `mailConfirmed` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `registry`
--

INSERT INTO `registry` (`id`, `userID`, `postID`, `name`, `telefon`, `postcode`, `place`, `terms`, `mailConfirmed`) VALUES
(94, 146, 0, 'potty', '676767676767', 66666, 'potsdam', 1, 0),
(96, 148, 0, 'potty', '676767676767', 66666, 'BARCELONA', 1, 0),
(97, 149, 0, 'potty', '676767676767', 66666, 'potsdam', 1, 0),
(98, 150, 0, 'potty', '676767676767', 66666, 'potsdam', 1, 0),
(99, 151, 0, 'potty', '676767676767', 66666, 'potsdam', 1, 0),
(100, 152, 0, 'potty', '676767676767', 66666, 'potsdam', 1, 0),
(103, 155, 0, 'potty', '676767676767', 66666, 'potsdam', 1, 0),
(106, 158, 0, 'Timothy Ganskow', '12312312312', 12207, 'berlin', 1, 0),
(107, 160, 0, 'Timothy Ganskow', '123123123123', 12207, 'berlin', 1, 0),
(108, 161, 0, 'Timothy Ganskow', '123123123123', 12207, 'berlin', 1, 0),
(109, 162, 0, 'Timothy Ganskow', '12312312312', 12207, 'berlin', 1, 0),
(110, 164, 0, 'Timothy Ganskow', '123123123123', 12207, 'asdasd', 1, 0),
(111, 165, 0, 'Timothy Ganskow', '1231123123', 12207, 'berlin', 1, 0),
(112, 167, 0, 'Timothy Ganskow', '123123123123', 12207, 'berlin', 1, 0),
(113, 168, 0, 'Timothy Ganskow', '123123123123', 12207, 'qwe', 1, 0),
(114, 169, 0, 'Timothy Ganskow', '12312312312', 12207, 'berlin', 1, 0),
(115, 170, 0, 'Timothy Ganskow', '123123123123', 12207, 'berlin', 1, 0),
(116, 171, 0, 'Timothy Ganskow', '12123123123', 12207, 'berlin', 1, 1),
(118, 175, 0, 'Timothy Ganskow', '123123123123', 12207, 'qweqw', 1, 0),
(119, 176, 0, 'Timothy Ganskow', '12312312312', 12207, 'berlin', 1, 0),
(121, 178, 0, 'Timothy Ganskow', '123123123123', 12207, 'adasd', 1, 0),
(133, 191, 0, 'aaa', '1231231232', 123123, 'munch', 1, 0),
(135, 193, 0, 'Timothy Ganskow', '123123123123', 12207, 'ber', 1, 0),
(137, 196, 0, 'huhu', '1231231233', 12203, 'berlin', 1, 0),
(138, 198, 0, 'huhu', '1231231233', 12203, 'berlin', 1, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `passwort` text NOT NULL,
  `mailToken` text NOT NULL,
  `refreshToken` text DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `endedAt` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `email`, `passwort`, `mailToken`, `refreshToken`, `createdAt`, `endedAt`) VALUES
(146, 'sotsssssdam@burg.de', '$2y$10$LStdI8VFwsir9fxvGJYkaOrpiMA0y3iViOxlRP4KbxkKjMhFZMgLa', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDAwNTYzMjUsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDAwNTYzMjUsImV4cCI6MTcwMDA1NjkyNSwidXNlcm5hbWUiOiJzb3RzZGFtQGJ1cmcuZGUifQ.msQHlTTyaJuHJsrXWhulivk3KIl8bTiXN5KxurYWF4M', NULL, '2023-11-15 13:52:05', NULL),
(148, 'rasdtsdam@burg.de', '$2y$10$SKZQ3yfEsU8yL/LYtGPoBObgNjUF0LyWh.5EMvDyylaQ7qTFbxpSq', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDAwNjczNTMsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDAwNjczNTMsImV4cCI6MTcwMDA2Nzk1MywidXNlcm5hbWUiOiJyYXNkdHNkYW1AYnVyZy5kZSJ9.MWjIoQ_dj2tQVlFqpn1xAyaYJLLHu2LO15WJia9mqew', NULL, '2023-11-15 16:55:53', NULL),
(149, 'aadsasotsdam@burg.de', '$2y$10$36dPphALxlXq84xxCeC1OewPWl9H3vvlbO9xfU0wuCvqDsLLNQ50e', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDAwNjczNjAsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDAwNjczNjAsImV4cCI6MTcwMDA2Nzk2MCwidXNlcm5hbWUiOiJhYWRzYXNvdHNkYW1AYnVyZy5kZSJ9.KbLZP5q2nwGZXmywWaqAuyHHi1OA2BpBZvLg5zGTzMs', NULL, '2023-11-15 16:56:00', NULL),
(150, 'dddddsasotsdam@burg.de', '$2y$10$tC38fPBR94PiswlDbjPwAuIEN4W3wwh.bVQKihaZbyk4ebSVLL06.', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDAwNjczNjcsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDAwNjczNjcsImV4cCI6MTcwMDA2Nzk2NywidXNlcm5hbWUiOiJkZGRkZHNhc290c2RhbUBidXJnLmRlIn0.HFpU2aEDM0bExA4-ZFXtbRR9xO4ij88Ghr0ko4U9yq8', NULL, '2023-11-15 16:56:07', NULL),
(151, 'qweqwqwedddddsasotsdam@burg.de', '$2y$10$ownIOa4yl6QYuSasqByAK.Q8iHtj323VBbcw0d.fens5o8Qn/FrBy', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDAwNjczNzUsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDAwNjczNzUsImV4cCI6MTcwMDA2Nzk3NSwidXNlcm5hbWUiOiJxd2Vxd3F3ZWRkZGRkc2Fzb3RzZGFtQGJ1cmcuZGUifQ.Q0Zya0AHKDXuRStacnH4oJNSJhYBRXIsTyFpBL5aGpg', NULL, '2023-11-15 16:56:15', NULL),
(152, 'rrrsasotsdam@burg.de', '$2y$10$0oUC.lja0WIbtXOOSpogfOrFXaEcAg04vauYLJvL5we4d0kcKFnES', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDAwNjczODMsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDAwNjczODMsImV4cCI6MTcwMDA2Nzk4MywidXNlcm5hbWUiOiJycnJzYXNvdHNkYW1AYnVyZy5kZSJ9.MJaBvoXF2XK9XEuVfRVV0THGdYrmBdXxy2eJtXTCmDs', NULL, '2023-11-15 16:56:23', NULL),
(155, 'wwwtsdam@burg.de', '$2y$10$3FzzIb2bpxPr0eksMOaz7ugzXU8w1ub.EmkAByy/ngLKMRv69Igxe', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDAwNjc0MTQsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDAwNjc0MTQsImV4cCI6MTcwMDA2ODAxNCwidXNlcm5hbWUiOiJ3d3d0c2RhbUBidXJnLmRlIn0.lXM_Ii7vLoqk8c2RFCsIXtiEt4wN612h2b4u5ZZf3-Q', NULL, '2023-11-15 16:56:54', NULL),
(158, 't.gee@hotmail.deaaaaa', '$2y$10$rFi8vNoa8t4hSEdQl3GzguLGhEHOJ2cpFMXkNdBOKW.RDiyVGR1Va', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDAyMTEzNDcsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDAyMTEzNDcsImV4cCI6MTcwMDIxMTk0NywidXNlcm5hbWUiOiJ0LmdlZUBob3RtYWlsLmRlYWFhYWEifQ.YE1arInuL8X3CQIRvR3UxiE0IS_2JcyLS5RlsqoBx0A', NULL, '2023-11-17 08:55:47', NULL),
(160, 't.gee@hotmail.deqqq', '$2y$10$679HvwYWv8.VXspWVzwwVeNlj3oMdoi1nByPccdHcLbBQB.rO2AHC', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDAyMTE0MzMsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDAyMTE0MzMsImV4cCI6MTcwMDIxMjAzMywidXNlcm5hbWUiOiJ0LmdlZUBob3RtYWlsLmRlcXFxIn0.lgzkzeGQysikW7duVJLcm99bQCNhSmXZ11r0IL9XlNw', NULL, '2023-11-17 08:57:13', NULL),
(161, 't.gee@hotmail.deaaaa', '$2y$10$vK9HpyJIV4hF6Zn9cxXLqOzao0LXiI/gwYcEp9RpEyE9KLCuVzpni', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDAyMTE0NjQsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDAyMTE0NjQsImV4cCI6MTcwMDIxMjA2NCwidXNlcm5hbWUiOiJ0LmdlZUBob3RtYWlsLmRlYWFhYSJ9.MaEQQICLATQlvIk4qeY-UxGTBNz8PQtb_HAkNO92mqE', NULL, '2023-11-17 08:57:44', NULL),
(162, 't.gee@hotmail.deaaa', '$2y$10$xT8VvUFoY1vAMjyjGwM0O.LvYX16yN7kBbtUN.X6vSAkkJtSSeSnS', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDAyMTE0OTksImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDAyMTE0OTksImV4cCI6MTcwMDIxMjA5OSwidXNlcm5hbWUiOiJ0LmdlZUBob3RtYWlsLmRlYWFhIn0.o6be3gJbD36F2Bbz7RxVBDupjYDW2jlxr7Qx7OdDX4o', NULL, '2023-11-17 08:58:19', NULL),
(164, 't.gee@hotmail.deqqqa', '$2y$10$Cqhs9QtRHrKKmz3jPC7p9.2q7epVdqGmvsGokXhjnOce1Bz2g/tMq', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDAyMTE1MzgsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDAyMTE1MzgsImV4cCI6MTcwMDIxMjEzOCwidXNlcm5hbWUiOiJ0LmdlZUBob3RtYWlsLmRlcXFxYSJ9.g9cAbksqj50bkxqiLwXkvEfDvnCVZM56FuG2Jfkv_hM', NULL, '2023-11-17 08:58:58', NULL),
(165, 't.gee@hotmail.dewwwww', '$2y$10$Fl7rKDZ8TQmJnqadvXqU8.ZZ/6EPLv2a789sm8KQYE8HWr..KcHVC', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDAyMTE1NzgsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDAyMTE1NzgsImV4cCI6MTcwMDIxMjE3OCwidXNlcm5hbWUiOiJ0LmdlZUBob3RtYWlsLmRld3d3d3cifQ.wTpKI_L4KIVvp82q4KhcpUwQyZDKpEUoz4PZDBmXjcA', NULL, '2023-11-17 08:59:38', NULL),
(167, 't.gee@hotmail.de123', '$2y$10$WkiqLOlC8wZIjzxZ2pVM5uKHjhfdiKZ.DJvYRil9dw4Y/5ufrZcRK', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDAyMTE3MzYsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDAyMTE3MzYsImV4cCI6MTcwMDIxMjMzNiwidXNlcm5hbWUiOiJ0LmdlZUBob3RtYWlsLmRlMTIzIn0.rQ6LSWAIybHLuE4iC2td0aiZ9LJD4Mepjce3Sv1EIQc', NULL, '2023-11-17 09:02:16', NULL),
(168, 't.gee@hotmail.deqweqwe', '$2y$10$n/ZNWjtsTuicp/Ngbsyxs.gHamA69wlFCuxJbCFJmxNOEeemWLtA6', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDAyMTE3NjEsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDAyMTE3NjEsImV4cCI6MTcwMDIxMjM2MSwidXNlcm5hbWUiOiJ0LmdlZUBob3RtYWlsLmRlcXdlcXdlIn0.p-FXwaj6wYNy3rRT4aPl3Bnt49SS2vHka4_1L80TBus', NULL, '2023-11-17 09:02:41', NULL),
(169, 't.gee@hotmail.deqwe', '$2y$10$89xDgv2C4GB.h88ITE6kPe8o7YrMAe33oxz0ye1CKlDL9V5YoIcie', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDAyMTE3OTgsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDAyMTE3OTgsImV4cCI6MTcwMDIxMjM5OCwidXNlcm5hbWUiOiJ0LmdlZUBob3RtYWlsLmRlcXdlIn0.IVDfni3hasnOvyCvj7BtwhPFJS4HI77MauzbLx8qsJs', NULL, '2023-11-17 09:03:18', NULL),
(170, 't.gee@hotmail.dewwww', '$2y$10$ETBIXfh52KagNqhiizshHOtoHy10qT8hGEUq9TUJ01Xiv7Or9jodO', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDAyMTE4MjMsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDAyMTE4MjMsImV4cCI6MTcwMDIxMjQyMywidXNlcm5hbWUiOiJ0LmdlZUBob3RtYWlsLmRld3d3dyJ9.GlSpmhMCgBpsRKpR6no4hWellBBNUHjGNAUEJqNlkd4', NULL, '2023-11-17 09:03:43', NULL),
(171, 't.gee@hotmail.de1111', '$2y$10$Q0J1w9LG9lfyZxwnC1FzL.OQCiP/23b3u2Od5FFb.eTgTk0ZFkmgq', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDAyMTIwNzUsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDAyMTIwNzUsImV4cCI6MTcwMDIxMjY3NSwidXNlcm5hbWUiOiJ0LmdlZUBob3RtYWlsLmRlMTExMSJ9.RAOes4nH7nuSWPtTM_y-AEMFKm7QD1U0LTeJLko07P0', NULL, '2023-11-17 09:07:55', NULL),
(175, 't.gee@hotmail.deaaaahhh', '$2y$10$RdxsbGGaIMFmbu0fQhUM/OzzEHeEyJwXe0yufKfKYhBMDgCAlntle', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDAyMTIzNTUsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDAyMTIzNTUsImV4cCI6MTcwMDIxMjk1NSwidXNlcm5hbWUiOiJ0LmdlZUBob3RtYWlsLmRlYWFhYWhoaCJ9.PDv8UGPW1CIdUNxSKyOfuwExAoE4N4CgNAkLH1YV96s', NULL, '2023-11-17 09:12:35', NULL),
(176, 't.gee@hotmail.deqqqqqqq', '$2y$10$EaHxQZUD6AhzdzuH8s.NJe0AXbkeHMTV9Z2mUEJaXfM9RHUA92yHK', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDAyMTI0MTYsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDAyMTI0MTYsImV4cCI6MTcwMDIxMzAxNiwidXNlcm5hbWUiOiJ0LmdlZUBob3RtYWlsLmRlcXFxcXFxcSJ9.zOECi8brLJ1z3My4C_Gc199xS_7rsdqUYu4MsE5BVVM', NULL, '2023-11-17 09:13:36', NULL),
(178, 't.gee@hotmail.deasdasdasd', '$2y$10$disyZUtYzxAvD6BjN1MCB.lMDJcolSmVwt7/1VJJYw0MocDtWGupe', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDAyMzI5ODYsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDAyMzI5ODYsImV4cCI6MTcwMDIzMzU4NiwidXNlcm5hbWUiOiJ0LmdlZUBob3RtYWlsLmRlYXNkYXNkYXNkIn0.Hq6UEcC4oxCTlNBTVCRRslm_cde5gcoJ9zOQHHXvpxw', NULL, '2023-11-17 14:56:26', NULL),
(191, 's.ganskow@acternity.com', '$2y$10$vOjk95XCQRZEWv/hTwuuJOlkZerPP4Jm7tsfdGS9773176z2v/uBC', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDA2NDUxMTQsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDA2NDUxMTQsImV4cCI6MTcwMDY0NTcxNCwidXNlcm5hbWUiOiJzLmdhbnNrb3dAYWN0ZXJuaXR5LmNvbSJ9.Pd_VcAZYYAVzpXPrSby1Gi09uKPT2Y2AdPpGSwT1TtM', NULL, '2023-11-22 09:25:14', NULL),
(193, 't.gee@hotmail.deaa', '$2y$10$u1JFXLEdQUz0MaS5LBfWSuJnTX8hJHzPpPwD7EVSmhk1Mm8IHb5jS', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDA3MzMzMzgsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDA3MzMzMzgsImV4cCI6MTcwMDczMzkzOCwidXNlcm5hbWUiOiJ0LmdlZUBob3RtYWlsLmRlYWEifQ.DW6pWPS7jHL3WXxa5JdsQ7NxNFJ7rRQjwegxMGHwA_E', NULL, '2023-11-23 09:55:38', NULL),
(196, 't.gee@hotmail.deaqwe', '$2y$10$GlpW4ENvmG9q/2WEaf8ZPeIXqp/AW.C5IlJQLHYkggWUgC2lncikG', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDA3NDMzNDMsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDA3NDMzNDMsImV4cCI6MTcwMDc0Mzk0MywidXNlcm5hbWUiOiJ0LmdlZUBob3RtYWlsLmRlYXF3ZSJ9.CUsDrGsgi4IErTqNlJqp4thFCivHwN363n4irPGUiZA', NULL, '2023-11-23 12:42:23', NULL),
(198, 't.gee@hotmail.deaqweasdasd', '$2y$10$E/C7KLzoCCF65CRCDgGFqu/JN07YzeaHSj0y2ocjdLXJMqsfEvWzi', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MDA3NDM1NTgsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6MzAwMC9pbmRleC5waHAiLCJuYmYiOjE3MDA3NDM1NTgsImV4cCI6MTcwMDc0NDE1OCwidXNlcm5hbWUiOiJ0LmdlZUBob3RtYWlsLmRlYXF3ZWFzZGFzZCJ9.B7Xut6PN07Cm2wFAEoR71lEs9sY_DdatJhGquWNRaEo', NULL, '2023-11-23 12:45:58', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userspermission`
--

CREATE TABLE `userspermission` (
  `id` bigint(20) NOT NULL,
  `userID` bigint(20) NOT NULL,
  `permission` tinyint(3) NOT NULL,
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `userspermission`
--

INSERT INTO `userspermission` (`id`, `userID`, `permission`, `updatedAt`) VALUES
(101, 146, 1, '2023-11-15 13:52:05'),
(103, 148, 2, '2023-11-15 16:55:53'),
(104, 149, 2, '2023-11-15 16:56:00'),
(105, 150, 1, '2023-11-15 16:56:07'),
(106, 151, 1, '2023-11-15 16:56:15'),
(107, 152, 1, '2023-11-15 16:56:23'),
(110, 155, 1, '2023-11-15 16:56:54'),
(113, 158, 1, '2023-11-17 08:55:47'),
(114, 160, 1, '2023-11-17 08:57:13'),
(115, 161, 1, '2023-11-17 08:57:44'),
(116, 162, 1, '2023-11-17 08:58:19'),
(117, 164, 1, '2023-11-17 08:58:58'),
(118, 165, 1, '2023-11-17 08:59:38'),
(119, 167, 3, '2023-11-17 09:02:16'),
(120, 168, 1, '2023-11-17 09:02:41'),
(121, 169, 1, '2023-11-17 09:03:18'),
(122, 170, 1, '2023-11-17 09:03:43'),
(123, 171, 2, '2023-11-17 09:07:55'),
(125, 175, 1, '2023-11-17 09:12:35'),
(126, 176, 3, '2023-11-17 09:13:36'),
(128, 178, 1, '2023-11-17 14:56:26'),
(140, 191, 1, '2023-11-22 09:25:14'),
(142, 193, 1, '2023-11-23 09:55:38'),
(144, 196, 1, '2023-11-23 12:42:23'),
(145, 198, 1, '2023-11-23 12:45:58');

-- --------------------------------------------------------

--
-- Stellvertreter-Struktur des Views `view_name`
-- (Siehe unten für die tatsächliche Ansicht)
--
CREATE TABLE `view_name` (
`id` bigint(20)
,`email` varchar(50)
,`name` varchar(100)
,`postcode` int(100)
,`place` varchar(100)
,`telefon` varchar(100)
,`passwort` text
,`permission` tinyint(3)
,`mailConfirmed` tinyint(1)
);

-- --------------------------------------------------------

--
-- Struktur des Views `view_name`
--
DROP TABLE IF EXISTS `view_name`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_name`  AS SELECT `users`.`id` AS `id`, `users`.`email` AS `email`, `registry`.`name` AS `name`, `registry`.`postcode` AS `postcode`, `registry`.`place` AS `place`, `registry`.`telefon` AS `telefon`, `users`.`passwort` AS `passwort`, `userspermission`.`permission` AS `permission`, `registry`.`mailConfirmed` AS `mailConfirmed` FROM ((`users` join `registry` on(`users`.`id` = `registry`.`userID`)) join `userspermission` on(`users`.`id` = `userspermission`.`userID`)) ;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `postcode`
--
ALTER TABLE `postcode`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `registry`
--
ALTER TABLE `registry`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users` (`userID`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniqeEmail` (`email`);

--
-- Indizes für die Tabelle `userspermission`
--
ALTER TABLE `userspermission`
  ADD PRIMARY KEY (`id`),
  ADD KEY `permissionToUsers` (`userID`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `postcode`
--
ALTER TABLE `postcode`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT für Tabelle `registry`
--
ALTER TABLE `registry`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=291;

--
-- AUTO_INCREMENT für Tabelle `userspermission`
--
ALTER TABLE `userspermission`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `registry`
--
ALTER TABLE `registry`
  ADD CONSTRAINT `regToUsers` FOREIGN KEY (`userID`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users` FOREIGN KEY (`userID`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `userspermission`
--
ALTER TABLE `userspermission`
  ADD CONSTRAINT `permissionToUsers` FOREIGN KEY (`userID`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
