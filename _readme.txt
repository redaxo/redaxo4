
produktname/product: redaxo 2.7
scriptsprache: php
datenbank/database: mysql
lizenz/licence: gnu general public licence (gpl)
copyright: pergopa kristinus gbr, frankfurt, germany
webseite/website: www.pergopa.de
webseite/website: www.redaxo.de
email: info@redaxo.de

// ---------------------- ZU BEACHTENDE LIZENZEN/PLEASE CHECK THESE LICENCES

php - class tar - Josh Barger <joshb@npt.com> Copyright (C) 2002
javascript - htmlArea - License (based on BSD license) - Copyright (c) 2002, interactivetools.com, inc.

// ---------------------- INSTALLATION/INSTALLATION

1. entpacken/unpack

1.1. de - redaxo.zip oder redaxo.tar.gz entpacken 
1.1. en - unpack redaxo.zip or redaxo.tar.gz


1.2. de - sie sollten einen ordner mit htdocs_redaxo erhalten 
1.2. en - you should have a folder named htdocs_redaxo

2. de - upload der daten
2. en - upload

2.1. de - laden sie den inhalt des htdocs_redaxo ordners in ihr DocumentRoot verzeichnis (z.b. htdocs oder www oder www.servername.de) über z.b. ftp.
2.1. en - please upload the folder in you webservers documentRoot ( htdocs or www or .. ) via ftp or ssh ...

2.2. de - in ihrem DocumentRoot verzeichnis muesste nun eine "index.php" datei und der "redaxo" ordner liegen.
2.2. en - in this folder should be an "index.php" and the "redaxo" folder.

2.3. de - geben sie in ihren browser ihre serveradresse ein. www.servername.de/index.php
2.3. en - please check with your browser the functionality of php. -> www.servername.de/index.php

2.4. de - sie sollten "Kein Startartikel selektiert" erhalten. wenn nicht dann nochmal bei 2. anfangen
2.4. en - you should get the message "Kein Startartikel selektiert" or "No startarticle selected" which shows you that php works otherwise start with 2.

3. setup
3. setup

3.1. rufen sie www.servername.de/redaxo/setup.php auf und führen sie die dort angegebenen schritte durch
3.1. to start the installation of the database... goto www.servername.de/redaxo/setup.php

4. wenn alles erfolgreich abgeschlossen
4. after all

4.1. löschen sie /redaxo/setup.php und /redaxo/setup_en.php oder verschieben sie diese datei.
4.2. delete the /redaxo/setup.php and /redaxo/setup_en.php

// ---------------------- BENÖTIGTE EINSTELLUNGEN/PROGRAMME

php.ini:
register_globals = on

optional:
imagemagick
