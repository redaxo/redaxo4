-- phpMyAdmin SQL Dump
-- version 2.6.1
-- http://www.phpmyadmin.net
-- 
-- Host: db423.1und1.de
-- Erstellungszeit: 03. Juni 2005 um 23:02
-- Server Version: 4.0.24
-- PHP-Version: 4.3.10-2
-- 
-- Datenbank: `db119786826`
-- 

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `rex__glossar`
-- 

CREATE TABLE `rex__glossar` (
  `short_id` int(10) unsigned NOT NULL auto_increment,
  `shortcut` varchar(255) NOT NULL default '',
  `description` varchar(255) default NULL,
  `language` tinyint(1) default '0',
  `casesense` tinyint(1) default '0',
  PRIMARY KEY  (`short_id`,`shortcut`)
) TYPE=MyISAM AUTO_INCREMENT=77 ;

-- 
-- Daten für Tabelle `rex__glossar`
-- 

INSERT INTO `rex__glossar` VALUES (9, 'z.B.', 'zum Beispiel', 0, 1);
INSERT INTO `rex__glossar` VALUES (15, 'e.V.', 'eingetragener Verein', 0, 1);
INSERT INTO `rex__glossar` VALUES (14, 'Email', 'elektronische Post', 1, 0);
INSERT INTO `rex__glossar` VALUES (16, 'SVE', 'Schulvorbereitende Einreichtung', 0, 0);
INSERT INTO `rex__glossar` VALUES (17, 'd.h.', 'das heisst', 0, 1);
INSERT INTO `rex__glossar` VALUES (18, 'bzw.', 'beziehungsweise', 0, 1);
INSERT INTO `rex__glossar` VALUES (19, 'usw.', 'und so weiter', 0, 1);
INSERT INTO `rex__glossar` VALUES (20, 'MSD', ' Mobile Sonderpädagogische Dienst', 0, 1);
INSERT INTO `rex__glossar` VALUES (21, 'PC', 'Personal Computer', 0, 1);
INSERT INTO `rex__glossar` VALUES (22, 'FC', 'Fussball Club', 0, 1);
INSERT INTO `rex__glossar` VALUES (23, 'Dr.', 'Doktor', 0, 1);
INSERT INTO `rex__glossar` VALUES (24, 'Ca.', 'Cirka', 0, 1);
INSERT INTO `rex__glossar` VALUES (25, 'Sa.', 'Samstag', 0, 1);
INSERT INTO `rex__glossar` VALUES (26, 'So.', 'Sonntag', 0, 1);
INSERT INTO `rex__glossar` VALUES (27, 'Std.', 'Stunde', 0, 1);
INSERT INTO `rex__glossar` VALUES (28, 'St.', 'Sankt', 0, 1);
INSERT INTO `rex__glossar` VALUES (29, 'ggf.', 'gegebenenfalls', 0, 1);
INSERT INTO `rex__glossar` VALUES (30, 'Tel.', 'Telefon', 0, 1);
INSERT INTO `rex__glossar` VALUES (31, 'KFZ', 'Kraftfahrzeug', 0, 1);
INSERT INTO `rex__glossar` VALUES (32, 'CD', 'Comact Disk', 0, 1);
INSERT INTO `rex__glossar` VALUES (33, 'VW', 'Volkswagen', 0, 1);
INSERT INTO `rex__glossar` VALUES (34, 'u.a.', 'unter anderem', 0, 1);
INSERT INTO `rex__glossar` VALUES (35, 'ha.', 'Hektar', 0, 1);
INSERT INTO `rex__glossar` VALUES (36, 'km', 'Kilometer', 0, 1);
INSERT INTO `rex__glossar` VALUES (37, 'BITV', 'Verordnung zur barrierefreien Informationstechnik', 0, 1);
INSERT INTO `rex__glossar` VALUES (38, 'Computer', 'Computer', 1, 1);
INSERT INTO `rex__glossar` VALUES (39, 'DeltaTalker', 'ein Computer, der Texte vorliest', 1, 1);
INSERT INTO `rex__glossar` VALUES (40, 'Homepage', 'Internetseite', 1, 1);
INSERT INTO `rex__glossar` VALUES (41, 'Team', 'Gemeinschaft', 1, 1);
INSERT INTO `rex__glossar` VALUES (42, 'Training', 'Übung', 1, 1);
INSERT INTO `rex__glossar` VALUES (43, 'Browser', 'Medium um Internetseiten darzustellen', 1, 1);
INSERT INTO `rex__glossar` VALUES (44, 'Hobbys', 'Freizeitgestaltung', 1, 1);
INSERT INTO `rex__glossar` VALUES (45, 'Dragonball', 'ein Spiel auf dem Computer', 1, 1);
INSERT INTO `rex__glossar` VALUES (46, 'Playstation', 'Soielestation', 1, 1);
INSERT INTO `rex__glossar` VALUES (47, 'Links', 'Adressen von Internetseiten', 1, 1);
INSERT INTO `rex__glossar` VALUES (48, 'Guinness', 'Eintrag von Weltrekorden', 1, 1);
INSERT INTO `rex__glossar` VALUES (49, 'Outdoor', 'Draussen im Freien', 1, 1);
INSERT INTO `rex__glossar` VALUES (50, 'Regie', 'Anleitung', 1, 1);
INSERT INTO `rex__glossar` VALUES (51, 'Layout', 'Darstellung', 1, 1);
INSERT INTO `rex__glossar` VALUES (52, 'Chat', 'Kommunikation via Internet', 1, 1);
INSERT INTO `rex__glossar` VALUES (53, 'Newsletter', 'Rundbrief von Neuigkeiten', 1, 1);
INSERT INTO `rex__glossar` VALUES (54, 'Website', 'Internetseite', 1, 1);
INSERT INTO `rex__glossar` VALUES (55, 'Public', 'Öffentlichkeit', 1, 1);
INSERT INTO `rex__glossar` VALUES (56, 'Lessons', 'Schulstunden', 1, 1);
INSERT INTO `rex__glossar` VALUES (57, 'Recycling', 'Wiedereinführung in einen Kreislauf', 1, 1);
INSERT INTO `rex__glossar` VALUES (58, 'relauncht', 'Erneuerung, Modernisierung hier im speziellen einen Internetauftritt', 1, 1);
INSERT INTO `rex__glossar` VALUES (59, 'Graves', 'Eigenname', 1, 1);
INSERT INTO `rex__glossar` VALUES (60, 'Containern', 'Aufbewahrungsbehälter', 1, 1);
INSERT INTO `rex__glossar` VALUES (61, 'Cowboy', 'Kuhhirte, überwiegend zu Pferde unterwegs', 1, 1);
INSERT INTO `rex__glossar` VALUES (62, 'Louis-seize', 'aus der Zeit von Louis, König aus Frankreich, der einen speziellen Stil darstellt', 2, 1);
INSERT INTO `rex__glossar` VALUES (63, 'Windows', 'Fenster', 1, 1);
INSERT INTO `rex__glossar` VALUES (64, 'MAC', 'Eigenname von Macenthosh', 0, 1);
INSERT INTO `rex__glossar` VALUES (65, 'Explorer', 'Auswahlfenster', 1, 1);
INSERT INTO `rex__glossar` VALUES (66, 'Return', 'Zurück zum letzten Punkt', 1, 1);
INSERT INTO `rex__glossar` VALUES (67, 'Screenreadern', 'Bildschirmlesegerät', 1, 1);
INSERT INTO `rex__glossar` VALUES (68, 'Web', 'Abkürzung aus dem englischen für Internet', 1, 1);
INSERT INTO `rex__glossar` VALUES (69, 'Accessibility', 'Bezeichnung für gute Bedienbarkeit', 1, 1);
INSERT INTO `rex__glossar` VALUES (70, 'World', 'Welt', 1, 1);
INSERT INTO `rex__glossar` VALUES (71, 'Wide', 'weite', 1, 1);
INSERT INTO `rex__glossar` VALUES (72, 'Stylesheets', 'Datei um die Stileigenschaft einer Internetseite darzustellen', 1, 1);
INSERT INTO `rex__glossar` VALUES (73, 'JavaScript', 'Programmiersprache', 1, 1);
INSERT INTO `rex__glossar` VALUES (74, 'Textbrowsern', 'Medium um textbasierte Seiten im Internet darzustellen', 1, 1);
INSERT INTO `rex__glossar` VALUES (75, 'Code', 'Sprache, hier Programmiersprache', 1, 1);
INSERT INTO `rex__glossar` VALUES (76, 'HAWIK', 'Hamburger Wechsler Intelligenztest für Kinder', 0, 1);
        