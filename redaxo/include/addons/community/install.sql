
DROP TABLE IF EXISTS `rex_com_user`;

CREATE TABLE `rex_com_user` (
  `id` int(11) NOT NULL auto_increment,
  `admin` tinyint(4) NOT NULL default '0',
  `login` varchar(255) NOT NULL default '',
  `password` varchar(255) NOT NULL default '',
  `gender` char(1) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `firstname` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `street` varchar(255) NOT NULL default '',
  `zip` varchar(255) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `phone` varchar(255) NOT NULL default '',
  `hobby` text NOT NULL,
  `interests` text NOT NULL,
  `more` text NOT NULL,
  `status` int(11) NOT NULL default '0',
  `email_checked` int(11) NOT NULL default '0',
  `activation_key` varchar(255) NOT NULL default '',
  `last_newsletterid` varchar(255) NOT NULL default '',
  `session_id` varchar(255) NOT NULL default '',
  `last_xs` int(11) NOT NULL default '0',
  `last_login` int(11) NOT NULL default '0',
  `show_contactinfo` tinyint(4) NOT NULL default '0',
  `show_personalinfo` tinyint(4) NOT NULL default '0',
  `sendemail_guestbook` tinyint(4) NOT NULL default '0',
  `sendemail_contactrequest` tinyint(4) NOT NULL default '0',
  `sendemail_newmessage` tinyint(4) NOT NULL default '0',
  `show_guestbook` int(11) NOT NULL default '0',
  `moderator` tinyint(4) NOT NULL default '0',
  `activity` int(11) NOT NULL default '0',
  `sendemail_newletter` tinyint(4) NOT NULL default '0',
  `online_status` tinyint(4) NOT NULL default '0',
  `motto` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `rex_com_user_field`;

CREATE TABLE `rex_com_user_field` (
  `id` int(11) NOT NULL auto_increment,
  `prior` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `userfield` varchar(255) NOT NULL default '',
  `type` int(11) NOT NULL default '0',
  `extra1` varchar(255) NOT NULL default '',
  `extra2` varchar(255) NOT NULL default '',
  `extra3` varchar(255) NOT NULL default '',
  `inlist` tinyint(4) NOT NULL default '0',
  `editable` tinyint(4) NOT NULL default '0',
  `mandatory` tinyint(4) NOT NULL default '0',
  `defaultvalue` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (1, 10, 'Login', 'login', 2, '', '', '', 1, 1, 1, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (2, 20, 'Passwort', 'password', 2, '', '', '', 0, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (3, 30, 'Email', 'email', 2, '', '', '', 1, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (4, 40, 'Status', 'status', 5, '0=inaktiv|1=aktiv|2=angefragt', '', '', 1, 1, 1, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (5, 100, 'session_id', 'session_id', 2, '', '', '', 0, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (6, 110, 'last_xs', 'last_xs', 1, '', '', '', 0, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (7, 120, 'last_login', 'last_login', 1, '', '', '', 0, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (8, 140, 'email_checked', 'email_checked', 6, '', '', '', 0, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (9, 150, 'activation_key', 'activation_key', 2, '', '', '', 0, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (10, 160, 'last_newsletterid', 'last_newsletterid', 2, '', '', '', 0, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (11, 200, 'gender', 'gender', 5, '1=m&auml;nnlich|2=weiblich', '', '', 0, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (36, 205, 'Bildname', 'image', 2, '', '', '', 1, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (12, 210, 'Name', 'name', 2, '', '', '', 1, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (14, 220, 'Vorname', 'firstname', 2, '', '', '', 1, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (15, 230, 'Strasse', 'street', 2, '', '', '', 0, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (16, 240, 'PLZ', 'zip', 2, '', '', '', 0, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (17, 250, 'Ort/Stadt', 'city', 2, '', '', '', 0, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (18, 260, 'Telefon', 'phone', 2, '', '', '', 0, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (39, 261, 'Fax', 'fax', 2, '', '', '', 0, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (40, 262, 'ICQ', 'icq', 2, '', '', '', 0, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (41, 263, 'Skype', 'skype', 2, '', '', '', 0, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (13, 280, 'Geburtstag', 'birthday', 2, '', '', '', 1, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (37, 299, 'Basisinfo anzeigen', 'show_basisinfo', 5, '0=Privat - unsichtbar f&uuml;r alle|1=Sichtbar f&uuml;r meine Kontakte|2=&ouml;ffentlich - f&uuml;r alle sichtbar', '', '', 0, 1, 1, '0');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (19, 300, 'Kontaktinfo anzeigen', 'show_contactinfo', 5, '0=Privat - unsichtbar f&uuml;r alle|1=Sichtbar f&uuml;r meine Kontakte|2=&ouml;ffentlich - f&uuml;r alle sichtbar', '', '', 0, 1, 1, '0');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (20, 310, 'Persönlich Infos anzeigen', 'show_personalinfo', 5, '0=Privat - unsichtbar für alle|1=Sichtbar f&uuml;r meine Kontakte|2=&ouml;ffentlich - f&uuml;r alle sichtbar', '', '', 0, 1, 1, '0');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (22, 320, 'G&auml;stebuch anzeigen', 'show_guestbook', 5, '0=Privat - unsichtbar f&uuml;r alle|1=Sichtbar f&uuml;r meine Kontakte|2=&ouml;ffentlich - f&uuml;r alle sichtbar', '', '', 0, 1, 1, '0');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (23, 330, 'E-Mail bei Kontaktanfrage', 'sendemail_contactrequest', 6, '', '', '', 0, 1, 0, '0');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (24, 340, 'E-Mail bei neuer Nachricht', 'sendemail_newmessage', 6, '', '', '', 0, 1, 0, '0');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (25, 350, 'E-Mail bei neuem Gästebucheintrag', 'sendemail_guestbook', 6, '', '', '', 0, 1, 0, '0');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (26, 400, 'Admin', 'admin', 6, '', '', '', 1, 1, 0, '0');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (27, 410, 'Moderator', 'moderator', 6, '', '', '', 0, 1, 0, '0');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (28, 500, 'Meine Hobbies', 'hobby', 3, '', '', '', 0, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (29, 510, 'Mich interessiert', 'interests', 3, '', '', '', 0, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (30, 520, 'Mehr &uuml;ber mich', 'more', 3, '', '', '', 0, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (31, 170, 'activity', 'activity', 1, '', '', '', 0, 1, 0, '50');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (35, 610, 'motto', 'motto', 3, '', '', '', 0, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (33, 360, 'Newsletter empfangen', 'sendemail_newletter', 6, '', '', '', 0, 1, 0, '');
INSERT INTO `rex_com_user_field` (`id`, `prior`, `name`, `userfield`, `type`, `extra1`, `extra2`, `extra3`, `inlist`, `editable`, `mandatory`, `defaultvalue`) VALUES (34, 105, 'online_status', 'online_status', 6, '', '', '', 0, 0, 0, '0');
