Redaxo Version: 4.*
Ersteller: Dagmar Uttich
Datum: 24. Januar 2006
Titel: readme.txt [deutsch]


1. Über REDAXO
  1.1 Allgemein
  1.2 Hilfe
  1.3 Technische Voraussetzungen
  1.4 Charakteristika
  1.5 Einstieg
2. Lizenzbestimmungen
3. Installation
  3.1 Update von REDAXO3.x
4. Dokumentation


Vorab ein Wort in eigener Sache:
Was sollte einen dazu bewegen, REDAXO zu nutzen?
Zitat aus dem Forum: „Die nette Community und der gute Support. ;-) “


1. Über REDAXO
1.1 Allgemein
Unser Ziel war und ist ein einfaches, schnell zu erlernendes Redaktionssystem zu entwickeln,
welches dennoch einen hohen Grad an Flexibilität gewährleistet. Wir hoffen, eure Zustimmung zu
finden, dass uns dies geglückt ist. Wir nehmen auch gerne Anregungen von euch entgegen, um
REDAXO kontinuierlich zu optimieren.

REDAXO bietet einem die Möglichkeit, Webseiten zu erstellen, die hinsichtlich der individuellen
Gestaltung keinerlei Einschränkungen unterliegen. Gleichzeitig wird durch die Trennung von Inhalt,
Funktionalität und Design eine leichte und schnelle Bearbeitung gerade auch für mehrere Bearbeiter
mit unterschiedlichen Kentnissen gewährleistet. Die Aktualisierung von Inhalten kann anschließend
zeitnah und ohne besondere Programmierkentnisse erfolgen.

Ein Vorteil von REDAXO ist, dass bereits HTML/CSS-Kenntnisse ausreichen, um Internetauftritte
mit REDAXO realisieren zu können. Hier helfen einem die Demo-Versionen, die Bestandteil der
REDAXO-Distributionen sind, weiter.

Alle Anpassungen an spezielle Anforderungen können in PHP programmiert werden. Eine
zusätzliche Skriptsprache ist bei REDAXO nicht vorgesehen.

1.2 Hilfe
Hilfe findet man bei unserer sehr aktiven Community (http://forum.redaxo.de/), in dem Wiki
(http://wiki.redaxo.de/) oder in der Online-Dokumentation (http://www.redaxo.de/4-0-dokuredaxode.html).


1.3 Technische Voraussetzungen
Um REDAXO beim Erstellen und Pflegen von Webseiten einsetzen zu können, braucht man einen
Hosting-Tarif, der die Interpretation von PHP-Skripten und den Zugriff auf eine MySQL-Datenbank
beinhaltet.


1.4 Charakteristika
Kategorien/Artikel:
Die Struktur einer Website wird über die Definition von Kategorien festgelegt. Innerhalb der
Kategorien werden ggf. Unterkategorien und/oder Artikel benannt. Artikel sind die eigentlichen
Seiten, die angezeigt werden. Das Design eines Artikels wird durch Zuweisung eines Templates
vorgegeben. Die Inhalte werden mit Hilfe von Modulen den Artikeln zugewiesen.
Es gibt definierte Klassen und Funktionen, die die Abfrage von Daten aus der Datenbank
erleichtern.

Module:
Module, die zur Eingabe der Inhalte verwendet werden, sind Bestandteil der Demoversionen.
Mittlerweile gibt es auch einen Fundus an weiteren Modulen auf den REDAXO-Seiten, die
heruntergeladen werden können.
Fertige Module können von den REDAXO-Seiten unter folgender Adresse heruntergeladen werden:
http://www.redaxo.de/17-0-module.html

AddOns:
Bestimmte Funktionen können als sogenannte AddOns in das System eingebunden werden. Das
sind z. B. die Statistik oder die Import/Export-Funktion. AddOns werden unter der folgenden Adresse zum Download angeboten:
http://www.redaxo.de/18-0-addons.html

Mehrsprachigkeit:
REDAXO ist speziell dazu ausgelegt eine Seite mit der gleichen Struktur in mehreren Sprachen zu pflegen.
Die Strukturen werden automatisch in allen vorgesehenen Sprachen gespiegelt. Inhalte können kopiert werden.
Mit REDAXO können Seiten in beliebig vielen Sprachen erstellt werden. Seit der Version 3.1 ist auch utf-8-Codierung möglich.

Medienpool:
Über einen Medienpool werden relevante Dateien, z. B. Bilder oder PDF's, zentral verwaltet.

Benutzerverwaltung
Es ist eine Benutzerverwaltung integriert, über die einzelnen Personen ein zielgerichteter und ggf.
auch eingeschränkter Zugriff auf das Backend ermöglicht wird.



1.5 Einstieg
REDAXO basiert auf der Skriptsprache PHP und setzt die Datenbank MySQL ein. Kenntnisse in
dieser Sprache und im Umgang mit der Datenbank sind zwar zu empfehlen, aber nicht unbedingt
erforderlich. Anhand der Demo-Versionen kann man bereits eigene Webseiten erstellen und dabei
lernen, das System zu nutzen.


2. Lizenzbestimmungen
REDAXO ist ein Open Source Programm, das der General Public License (GPL) unterliegt.
Detailliertere Informationen können der Datei _lizenz.txt, die Bestandteil dieser Distribution ist,
entnommen werden.


3. Installation
Die Installation ist sehr einfach und erfordert keine besonderen Voraussetzungen außer den oben
genannten.
Das Programm wird mit einem FTP-Programm auf den Webserver geladen. Anschließend kann man
den Setup per Klick starten und die in den einzelnen Schritten erforderlichen Angaben eintragen.
Nach dem Importieren von Inhalten in das System mittels der Import-Funktion von REDAXO wird
beim Aufrufen der URL die angegebene Startseite angezeigt.
Die einzelnen Schritte der Installation sind in der Online-Dokumentation detailliert beschrieben.
siehe: http://www.redaxo.de/29-0-a1--installation.html

3.1 Update von REDAXO 3.x

Folgende Änderungen sind bei einem Update von REDAXO 3.x zu beachten:


Datenbank
----------

Die Datenbanktabellen wurde verändert. 
Im Setup kann ein Update der Datenbank automatisiert durchgeführt werden.
Wichtig dabei ist, dass man zuvor die komplette Seite sichert!


Templates
----------

alt:
<?php include($REX['INCLUDE_PATH'] .'/generated/templates/2.template'); ?>

neu:
// innerhalb von PHP Tags
$navTemplate = new rex_template(2);  
include $navTemplate->getFile();

// ausserhalb von PHP Tags
REX_TEMPLATE[2]


CTYPES 
-------

Alle Ctypes sind nun via Backend zu verwalten. 
Die Datei ctype.inc.php wurde komplett entfernt!
Ctypes können nun pro Template hinterlegt werden.


Allgemeines 
------------

  * $REX['INCLUDE_PATH'], $REX['MEDIAFOLDER'] sind jetzt absolute Pfade!
  * Umbenennungen
    - Dateien:
       - function_rex_modrewrite.inc.php -> function_rex_url.inc.php
    - Klassen/Methoden/Funktionen:
      - Alte Klassenbezeichnung ab nun NICHT mehr verwenden !
          Klassen:
          + login -> rex_login
          + sql -> rex_sql
          + article -> rex_article
          Methoden:
          + sql::query() -> sql::setQuery()
          + sql::get_array() -> sql::getArray()
          + sql::resetCounter() -> sql::reset()
          + sql::nextValue() -> sql::next()
          + sql::where() -> sql::setWhere()
      Attribute:
      + sql->select -> sql->query
          Funktionen:
          + title -> rex_title()
          + getUrlById -> rex_getUrl()
  * Rückwärtskompatibilität eingeschränkt durch:
    - Bugfix: OOCategory::getArticles() 1. Parameter $ignore_offlines default-Wert von True auf False geändert
    - Bugfix: OOMediaCategory::getRootCategories() 1. Parameter $ignore_offlines entfernt, da es kein status bei Medienkategorien gibt
    
siehe: http://www.redaxo.de/221-0-update-hinweise.html


4. Dokumentation
Auf den REDAXO-Seiten findet ihr eine ausführliche Online-Dokumentation, die einem den
Einstieg und die Arbeit mit REDAXO erleichtern wird.

siehe: http://www.redaxo.de/4-0-dokuredaxode.html