[1||Was kann die Engine?]
Diese Engine bietet folgende Features:

- Verwaltunger multipler Hilfe Files
- Multilinugual
- Ausgabe als Html
- Ausgabe als PDF
- Direkter Einstieg in Kapitel/Titel

[2||Aufbau der Dateistruktur]
Im Verzeichniss {link ../../source}source{/link} befinden sich die tatsächlichen
Source File der Doku.

Diese Textfiles bestimmen den Content der Dokumentation.

Die Folder im Verzeichniss {link ../../source}source{/link} sind die verschiedenen Docs die von
der Engine verwaltet werden.

<b>Beispiel:</b>

- editor
- {link ../../source/docengine/}docengine{/link}
- developer

<b>Aufbau eines Doc Folders:</b>

.
..
docengine/ <-- Docu Name
docengine/de_DE/ <-- Docu Sprache
docengine/de_DE/index.inc <-- Kapitel Index
docengine/de_DE/text/ <-- Unterkapitel Texte Folder
docengine/de_DE/img/ <-- Unterkapitel Bilder Folder
docengine/de_DE/text/1.inc <-- Kapitel 1 Texte
docengine/de_DE/text/2.inc <-- Kapitel 2 Texte
docengine/de_DE/img/1_0.jpg <-- Verwendetes Bild
docengine/de_DE/img/1_1.jpg <-- Verwendetes Bild
docengine/de_DE/img/2_0.jpg <-- Verwendetes Bild

[3||Kapitel Syntax index.inc]
In diesen Foldern finden wir nun die datei {link ../../source/docengine/de_DE/index.inc}index.inc{/link}
Diese index Datei definiert die vorhandenen Kapitel des Hilfesystems.

Syntax der {link ../../source/docengine/de_DE/index.inc}index.inc{/link}:

Kapitel ID || Kapitel Name

{code}%5B7||Mein Demo Kaptitel%5D
{/code}

Die Kapitel ID ist eine Unique ID.
Die ist wichtig um direkt auf ein Kapitel zu linken.

[4||Unterkapitel Syntax]
Die Syntax dieser Files ähnelt der index.inc

{code}%5B1||Mein Titel%5D
Und ich bin der Text der unter diesem Titel
erscheinen wird.

%5B2||Mein Titel Nummer Zwei%5D
Und ich bin der Text der unter diesem Titel
erscheinen wird.
{/code}

Auch hier ist die UNIQUE ID wichtig.
Zur Info: Die Unique ID muss nur in diesem Kaptitel unique sein.

[5||Unterkapitel Special Syntax]
<b>Folgenden Special Tags können verwendet werden:</b>

Text Formatierung:

Html Formatierungen bold,underline,italic

{code}<b></b>
<i></i>
<u></u>
{/code}

Interner Link:

{code}%7Bilink 1_2%7D Link zu Kapitel 1 Unterkapitel 2 %7B/ilink%7D
{/code}

ergibt:

{ilink 1_2}Link zu Kapitel 1 Unterkapitel 2{/ilink}

Externer Link:

{code}%7Blink http://www.redaxo.de%7D Link auf www.redaxo.de %7B/link%7D
{/code}

ergibt:

{link http://www.redaxo.de}Link auf www.redaxo.de{/link}

Bild einfügen: erlaubte Formate JPG,PNG

{code}%7Bimg logo.png%7D
{/code}

ergibt:

{img logo.png}




