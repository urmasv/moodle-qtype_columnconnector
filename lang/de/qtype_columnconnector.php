<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'qtype_columnconnector', language 'de'.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Spaltenverbinder';
$string['pluginname_help'] = 'Erstellen Sie eine Frage mit 2–7 Spalten von Zellen. Lernende ziehen Linien, um Zellen in benachbarten Spalten zu verbinden; die Antwort wird anhand der von Ihnen definierten Verbindungen bewertet.';
$string['pluginname_link'] = 'question/type/columnconnector';
$string['pluginnameadding'] = 'Spaltenverbinder-Frage hinzufügen';
$string['pluginnameediting'] = 'Spaltenverbinder-Frage bearbeiten';
$string['pluginnamesummary'] = 'Zeigt 2–7 betitelte Spalten mit Zellen an und lässt Lernende Zellen benachbarter Spalten mit Linien verbinden. Punkte werden für richtige, falsche und fehlende Verbindungen vergeben.';
$string['columnconnectorsettings'] = 'Einstellungen für Spaltenverbinder';
$string['numcolumns'] = 'Anzahl der Spalten';
$string['numcolumns_help'] = 'Wählen Sie, ob die Aktivität mit zwei bis sieben Spalten angezeigt wird.';
$string['layoutmode'] = 'Layout';
$string['layoutcolumns'] = 'Spalten (vertikal)';
$string['layoutrows'] = 'Zeilen (horizontal)';
$string['linestyle'] = 'Linienstil';
$string['linecurved'] = 'Gebogen';
$string['linestraight'] = 'Gerade';
$string['showclear'] = 'Schaltfläche „Verbindungen löschen“ anzeigen';
$string['showmissing'] = 'Fehlende Verbindungen anzeigen';
$string['showmissing_help'] = 'Wenn aktiviert, werden fehlende richtige Verbindungen (bernsteinfarbene Linien) nach der Abgabe angezeigt – sofern die Berichtsoptionen das Anzeigen der richtigen Antwort erlauben. Standardmäßig deaktiviert.';
$string['instructions'] = 'Anweisungen für Lernende';
$string['instructions_help'] = 'Kurze HTML-Anweisung, die über den Spalten angezeigt wird.';
$string['correctpoints'] = 'Punkte für eine richtige Verbindung';
$string['correctpoints_help'] = 'Standard: +1 Punkt für jede richtige Verbindung. Die Gesamtbewertung der Frage wird aus diesen Punkten skaliert.';
$string['incorrectpoints'] = 'Punkte für eine falsche Verbindung';
$string['missingpoints'] = 'Punkte für eine fehlende Verbindung';
$string['clearconnections'] = 'Verbindungen löschen';
$string['emptycolumn'] = 'In dieser Spalte gibt es keine Zellen.';
$string['invalidtarget'] = 'Sie können nur mit einer Zelle in einer benachbarten Spalte verbinden.';
$string['duplicate'] = 'Diese Verbindung besteht bereits.';
$string['selectedcell'] = 'Zelle ausgewählt. Wählen Sie eine Zelle in einer benachbarten Spalte.';
$string['pleaseconnect'] = 'Bitte stellen Sie mindestens eine Verbindung her.';
$string['errornumcolumns'] = 'Die Anzahl der Spalten muss zwischen 2 und 7 liegen.';
$string['errormissingcolumns'] = 'Der Inhalt muss mindestens so viele Spalten definieren wie ausgewählt.';
$string['erroremptycolumn'] = 'Spalte {$a} muss mindestens eine Zelle enthalten.';
$string['erroranswerkey'] = 'Eine richtige Verbindung ist fehlerhaft oder verbindet keine benachbarten Spalten.';
$string['erroranswerkeyindex'] = 'Eine richtige Verbindung verweist auf eine Zelle, die nicht existiert.';
$string['contenteditor'] = 'Spalten, Zellen und richtige Verbindungen';
$string['h5pimport'] = 'Aus H5P importieren';
$string['h5pimport_help'] = 'Wählen Sie eine vorhandene H5P.ColumnConnector-Datei (.h5p) und klicken Sie auf „Aus H5P laden“. Der Inhalt (Spalten, Zellen, Verbindungen, Bilder) wird in den Editor geladen; prüfen und speichern.';
$string['h5pimportload'] = 'Aus H5P laden';
$string['h5pimporterror'] = 'Die H5P-Datei konnte nicht gelesen werden.';
$string['h5pimportwrongtype'] = 'Dies ist kein H5P.ColumnConnector-Inhalt.';
$string['column'] = 'Spalte';
$string['columntitle'] = 'Spaltentitel';
$string['cell'] = 'Zelle';
$string['celltext'] = 'Text';
$string['imageurl'] = 'Bild-URL';
$string['imageposition'] = 'Bildposition';
$string['imageabove'] = 'Über dem Text';
$string['imageleft'] = 'Links vom Text';
$string['imageleftsize'] = 'Größe des linken Bildes';
$string['imagealignleft'] = 'links';
$string['imagealigncenter'] = 'zentriert';
$string['sizesmall'] = 'klein';
$string['sizemedium'] = 'mittel';
$string['sizelarge'] = 'groß';
$string['imagealt'] = 'Alternativtext des Bildes';
$string['addcell'] = 'Zelle hinzufügen';
$string['removecell'] = 'Zelle entfernen';
$string['moveup'] = 'Nach oben';
$string['movedown'] = 'Nach unten';
$string['correctconnections'] = 'Richtige Verbindungen zur vorherigen Spalte';
$string['noconnection'] = 'keine Verbindung';
$string['connectioncount'] = '{$a} Verbindungen';
$string['noneighbourcells'] = 'Die vorherige Spalte hat keine Zellen.';
$string['cellfallback'] = 'Zelle';
$string['defaultinstructions'] = 'Verbinden Sie die Zellen mit Zellen in benachbarten Spalten. Eine Zelle kann mit mehreren Zellen der benachbarten Spalte verbunden werden.';
$string['errorinvalidcontent'] = 'Der Frageninhalt ist unvollständig oder ungültig.';
$string['editorloaderror'] = 'Der Editor konnte nicht geladen werden. Leeren Sie die Caches (Website-Administration → Entwicklung → Caches leeren) und versuchen Sie es erneut.';
$string['cellimages'] = 'Zellenbilder';
$string['cellimages_help'] = 'Laden Sie Bilder hoch, die in Zellen verwendet werden sollen. Wählen Sie dann bei jeder Zelle ein hochgeladenes Bild aus der Dropdown-Liste. Alternativ kann eine Zelle eine externe Bild-URL verwenden.';
$string['uploadedimage'] = 'Hochgeladenes Bild';
$string['noimage'] = '— kein Bild —';
$string['refreshimages'] = 'Bildliste aktualisieren';
$string['rtebold'] = 'Fett';
$string['rteitalic'] = 'Kursiv';
$string['rteunderline'] = 'Unterstrichen';
$string['rtebullet'] = 'Aufzählungsliste';
$string['rtenumbered'] = 'Nummerierte Liste';
$string['rtelink'] = 'Link einfügen';
$string['rtelinkprompt'] = 'Link-URL:';
$string['rtealignleft'] = 'Linksbündig';
$string['rtealigncenter'] = 'Zentriert';
$string['rtealignright'] = 'Rechtsbündig';
$string['rteclear'] = 'Formatierung entfernen';
$string['privacy:metadata'] = 'Der Fragetyp Spaltenverbinder speichert keine personenbezogenen Daten.';
