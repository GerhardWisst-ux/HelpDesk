-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 26. Jun 2025 um 12:04
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `helpdesk`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `customer`
--

CREATE TABLE `customer` (
  `CustomerID` int(11) NOT NULL,
  `Zusatz` varchar(100) NOT NULL,
  `Firma` varchar(100) DEFAULT NULL,
  `ZIP` int(11) NOT NULL,
  `Street` varchar(100) NOT NULL,
  `Location` varchar(100) NOT NULL,
  `Telefon` varchar(100) NOT NULL,
  `Fax` varchar(100) DEFAULT NULL,
  `CountryID` int(11) NOT NULL,
  `Mail` varchar(100) NOT NULL,
  `Internet` varchar(100) DEFAULT NULL,
  `KundeSeit` datetime DEFAULT NULL,
  `PricePerHour` float NOT NULL,
  `Notes` varchar(2000) DEFAULT NULL,
  `CreditLimit` float DEFAULT NULL,
  `Active` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `customer`
--

INSERT INTO `customer` (`CustomerID`, `Zusatz`, `Firma`, `ZIP`, `Street`, `Location`, `Telefon`, `Fax`, `CountryID`, `Mail`, `Internet`, `KundeSeit`, `PricePerHour`, `Notes`, `CreditLimit`, `Active`) VALUES
(1, 'AG', 'Daimler Benz', 70567, 'Sternhäule', 'Stuttgart', '0711-325564', '0711-325565', 1, 'daimler@stuttgart.de', 'www.daimler.de', '2016-01-01 00:00:00', 65, '', 0, 1),
(2, 'AG', 'Porsche', 70465, 'Porscheplatz', 'Stuttgart', '07141-8154712', '07141-8154713', 1, 'porsche@stuttgart.de', 'www.porsche.de', '2010-01-01 00:00:00', 58, '', 0, 1),
(7, 'Test', 'Testfirma', 73743, 'Augsburger Straße. 653', 'Stuttgart', '0711 34168881', '0711 34168880', 1, ' info@testfirma-online.de', 'https://www.testfirma.de', NULL, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `invoice_det`
--

CREATE TABLE `invoice_det` (
  `DET_ID` int(11) NOT NULL,
  `MST_ID` int(11) DEFAULT NULL,
  `PRODUCT_NAME` varchar(255) DEFAULT NULL,
  `AMOUNT` double(11,2) DEFAULT NULL,
  `POSITIONTAX` tinyint(1) NOT NULL,
  `NUMBER` float NOT NULL,
  `HOURS` float NOT NULL,
  `USERID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Daten für Tabelle `invoice_det`
--

INSERT INTO `invoice_det` (`DET_ID`, `MST_ID`, `PRODUCT_NAME`, `AMOUNT`, `POSITIONTAX`, `NUMBER`, `HOURS`, `USERID`) VALUES
(52, 49, 'von 10.00 - 13.00', 292.50, 1, 65, 4.5, 1),
(53, 49, 'von 10.00 - 13.00', 357.50, 1, 65, 5.5, 1),
(54, 50, 'von 10.00 - 13.00', 292.50, 1, 65, 4.5, 1),
(55, 50, 'von 10.00 - 13.00', 357.50, 1, 65, 5.5, 1),
(56, 51, 'von 10.00 - 13.00', 292.50, 1, 65, 4.5, 1),
(57, 51, 'von 10.00 - 13.00', 357.50, 1, 65, 5.5, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `invoice_mst`
--

CREATE TABLE `invoice_mst` (
  `MST_ID` int(11) NOT NULL,
  `INV_NO` varchar(100) DEFAULT NULL,
  `CUSTOMER_NAME` varchar(255) DEFAULT NULL,
  `STREET` text DEFAULT NULL,
  `ADDRESS` text DEFAULT NULL,
  `USERID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Daten für Tabelle `invoice_mst`
--

INSERT INTO `invoice_mst` (`MST_ID`, `INV_NO`, `CUSTOMER_NAME`, `STREET`, `ADDRESS`, `USERID`) VALUES
(49, 'RE01-2025', 'Daimler Benz', 'Sternhäule', 'Stuttgart', 1),
(50, 'RE02-2025', 'Daimler Benz', 'Sternhäule', 'Stuttgart', 1),
(51, 'RE03-2025', 'Daimler Benz', 'Sternhäule', 'Stuttgart', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `priority`
--

CREATE TABLE `priority` (
  `PriorityID` int(11) NOT NULL,
  `Description` varchar(50) NOT NULL,
  `SortOrder` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `priority`
--

INSERT INTO `priority` (`PriorityID`, `Description`, `SortOrder`) VALUES
(1, 'hoch', 2),
(2, 'mittel', 3),
(3, 'niedrig', 4),
(7, 'nicht definiert', 5),
(8, 'Dringend', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `status`
--

CREATE TABLE `status` (
  `StatusID` int(11) NOT NULL,
  `Description` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `status`
--

INSERT INTO `status` (`StatusID`, `Description`) VALUES
(1, 'Aktiv'),
(2, 'Warte auf Kunde'),
(3, 'Eskaliert'),
(4, 'Canceled'),
(5, 'Geschlossen'),
(6, 'Berechnet');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ticket`
--

CREATE TABLE `ticket` (
  `TicketID` int(11) NOT NULL,
  `Description` varchar(150) NOT NULL,
  `Notes` varchar(5000) NOT NULL,
  `CreatedDate` date NOT NULL,
  `DueDate` date NOT NULL,
  `ClosedDate` date NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `PriorityID` int(11) NOT NULL,
  `StatusID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `ticket`
--

INSERT INTO `ticket` (`TicketID`, `Description`, `Notes`, `CreatedDate`, `DueDate`, `ClosedDate`, `CustomerID`, `PriorityID`, `StatusID`, `UserID`) VALUES
(1, 'IDS Installation PCs', 'von 10.00 - 13.00', '2025-05-22', '2025-07-31', '2025-06-06', 1, 1, 6, 1),
(2, 'PC startet nicht', 'Start PC', '2025-04-16', '2025-06-30', '2025-05-22', 2, 2, 4, 1),
(4, 'Netzwerkproblem', 'dringend', '2025-05-20', '2025-05-24', '0000-00-00', 3, 8, 1, 1),
(5, 'Kunde wünscht mobile Lösung', 'sfgsagh', '2025-05-21', '2025-07-31', '0000-00-00', 1, 2, 1, 1),
(7, 'Erstellen einer Barkasse', 'Erstellen eines Webprojektes zur Verwaltung einer Barkasse mit Ausdruck und graphischer Auswertung', '2025-04-01', '2025-06-11', '2025-06-05', 3, 1, 6, 1),
(8, 'Erstellen HelpDesk System ', 'Erstellen eines HelpDesk Systems zur Verwaltung von Kundentickets', '2025-04-01', '2025-06-11', '2025-06-05', 3, 1, 6, 1),
(9, 'Instagramm', 'Einarbeitung und Uploads', '2025-05-31', '2025-06-30', '0000-00-00', 7, 2, 1, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ticketdetail`
--

CREATE TABLE `ticketdetail` (
  `TicketDetailID` int(11) NOT NULL,
  `TicketID` int(11) NOT NULL,
  `ServiceDateTime` datetime NOT NULL,
  `Description` varchar(100) NOT NULL,
  `Notes` varchar(100) DEFAULT NULL,
  `BillingHours` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `ticketdetail`
--

INSERT INTO `ticketdetail` (`TicketDetailID`, `TicketID`, `ServiceDateTime`, `Description`, `Notes`, `BillingHours`) VALUES
(2, 1, '2025-04-28 10:00:00', 'Aufbau PC&#039;s', '3 PC&#039;s aufgebaut und vernetzt', 5.5),
(3, 8, '2025-06-05 15:21:00', 'Löschfunktion bei allen Formen hinzugefügt', 'Löschen erfolgt über Bestätigungs-Messagebox', 5),
(7, 7, '2025-06-05 16:36:00', 'Test Löschen', 'sfgsagh', 5),
(8, 8, '2025-06-05 15:22:00', 'Erstellen PDF- Rechnungserstellungsfunktion', 'Erstellen PDF- Rechnungserstellungsfunktion', 4);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ticketdokument`
--

CREATE TABLE `ticketdokument` (
  `TicketDocumentID` int(11) NOT NULL,
  `TicketID` int(11) NOT NULL,
  `Path` int(11) NOT NULL,
  `CreatedDate` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `passwort` varchar(255) NOT NULL,
  `vorname` varchar(255) NOT NULL DEFAULT '',
  `nachname` varchar(255) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `email`, `passwort`, `vorname`, `nachname`, `created_at`, `updated_at`) VALUES
(1, 'tester@web.de', '$2y$10$cs05zWzGCRIhxRmKyyMabuUuIweqoEC.Lak0XL068ONuKLMAyHAmW', '', '', '2025-04-25 07:57:52', '2025-04-25 07:57:52');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`CustomerID`);

--
-- Indizes für die Tabelle `invoice_det`
--
ALTER TABLE `invoice_det`
  ADD PRIMARY KEY (`DET_ID`);

--
-- Indizes für die Tabelle `invoice_mst`
--
ALTER TABLE `invoice_mst`
  ADD PRIMARY KEY (`MST_ID`);

--
-- Indizes für die Tabelle `priority`
--
ALTER TABLE `priority`
  ADD PRIMARY KEY (`PriorityID`);

--
-- Indizes für die Tabelle `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`StatusID`);

--
-- Indizes für die Tabelle `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`TicketID`);

--
-- Indizes für die Tabelle `ticketdetail`
--
ALTER TABLE `ticketdetail`
  ADD PRIMARY KEY (`TicketDetailID`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `customer`
--
ALTER TABLE `customer`
  MODIFY `CustomerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT für Tabelle `invoice_det`
--
ALTER TABLE `invoice_det`
  MODIFY `DET_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT für Tabelle `invoice_mst`
--
ALTER TABLE `invoice_mst`
  MODIFY `MST_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT für Tabelle `priority`
--
ALTER TABLE `priority`
  MODIFY `PriorityID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT für Tabelle `status`
--
ALTER TABLE `status`
  MODIFY `StatusID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT für Tabelle `ticket`
--
ALTER TABLE `ticket`
  MODIFY `TicketID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT für Tabelle `ticketdetail`
--
ALTER TABLE `ticketdetail`
  MODIFY `TicketDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
