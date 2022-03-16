-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Stř 16. bře 2022, 16:31
-- Verze serveru: 10.1.32-MariaDB
-- Verze PHP: 8.0.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `rt_soft_product`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(60) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `category`
--

INSERT INTO `category` (`id`, `name`) VALUES
(2, 'Boty'),
(3, 'Kalhoty'),
(4, 'Čepice'),
(5, 'Neurčeno'),
(6, 'Bundy');

-- --------------------------------------------------------

--
-- Struktura tabulky `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `price` int(9) DEFAULT NULL,
  `datetime` datetime DEFAULT '0000-00-00 00:00:00',
  `active` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `FK_category` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `product`
--

INSERT INTO `product` (`id`, `name`, `price`, `datetime`, `active`, `FK_category`) VALUES
(21, 'Nike boty', 1890, '2022-03-23 00:00:00', 'Neaktivní', 2),
(26, 'Bunda Hannah', 2239, '2022-03-30 00:00:00', 'Aktivní', 6),
(28, 'Kalhoty Levis', 456, '2022-03-30 00:00:00', 'Neaktivní', 3),
(31, 'Čepice Vans', 899, '2022-03-24 00:00:00', 'Aktivní', 4);

-- --------------------------------------------------------

--
-- Struktura tabulky `product_tag`
--

CREATE TABLE `product_tag` (
  `FK_product` int(11) NOT NULL,
  `FK_tag` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `product_tag`
--

INSERT INTO `product_tag` (`FK_product`, `FK_tag`) VALUES
(21, 4),
(26, 1),
(26, 4),
(28, 4),
(31, 4);

-- --------------------------------------------------------

--
-- Struktura tabulky `tag`
--

CREATE TABLE `tag` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `tag`
--

INSERT INTO `tag` (`id`, `name`) VALUES
(1, 'modrá'),
(4, 'červené');

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`FK_category`);

--
-- Indexy pro tabulku `product_tag`
--
ALTER TABLE `product_tag`
  ADD PRIMARY KEY (`FK_product`,`FK_tag`) USING BTREE,
  ADD KEY `FK_tag` (`FK_tag`);

--
-- Indexy pro tabulku `tag`
--
ALTER TABLE `tag`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pro tabulku `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT pro tabulku `tag`
--
ALTER TABLE `tag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_2` FOREIGN KEY (`FK_category`) REFERENCES `category` (`id`);

--
-- Omezení pro tabulku `product_tag`
--
ALTER TABLE `product_tag`
  ADD CONSTRAINT `fk_product_id` FOREIGN KEY (`FK_product`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `fk_product_tag` FOREIGN KEY (`FK_tag`) REFERENCES `tag` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
