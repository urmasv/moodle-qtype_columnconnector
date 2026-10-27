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

$string['addcell'] = 'Zelle hinzufügen';
$string['cell'] = 'Zelle';
$string['cellfallback'] = 'Zelle';
$string['cellimages'] = 'Zellenbilder';
$string['cellimages_help'] = 'Laden Sie Bilder hoch, die in Zellen verwendet werden sollen. Wählen Sie dann bei jeder Zelle ein hochgeladenes Bild aus der Dropdown-Liste. Alternativ kann eine Zelle eine externe Bild-URL verwenden.';
$string['cellposition'] = 'Spalte {$a->col}, Zelle {$a->row}';
$string['cellpositionrows'] = 'Zeile {$a->col}, Zelle {$a->row}';
$string['celltext'] = 'Text';
$string['clearconnections'] = 'Verbindungen löschen';
$string['column'] = 'Spalte';
$string['columnconnectorsettings'] = 'Einstellungen für Spaltenverbinder';
$string['columntitle'] = 'Spaltentitel';
$string['connectionadded'] = 'Verbunden: {$a}';
$string['connectioncorrect'] = '{$a}: richtig';
$string['connectioncount'] = '{$a} Verbindungen';
$string['connectionincorrect'] = '{$a}: falsch';
$string['connectionmissing'] = '{$a}: fehlt (richtige Verbindung)';
$string['connectionremoved'] = 'Verbindung entfernt: {$a}';
$string['connectionscleared'] = 'Alle Verbindungen entfernt.';
$string['connectionsummary'] = 'Verbindungen';
$string['contenteditor'] = 'Spalten, Zellen und richtige Verbindungen';
$string['correctansweris'] = 'Die richtige Antwort ist: {$a}';
$string['correctconnections'] = 'Richtige Verbindungen zur vorherigen Spalte';
$string['correctconnectionsrow'] = 'Richtige Verbindungen zur vorherigen Zeile';
$string['correctpoints'] = 'Punkte für eine richtige Verbindung';
$string['correctpoints_help'] = 'Standard: +1 Punkt für jede richtige Verbindung. Die Gesamtbewertung der Frage wird aus diesen Punkten skaliert.';
$string['editorloaderror'] = 'Der Editor konnte nicht geladen werden. Leeren Sie die Caches (Website-Administration → Entwicklung → Caches leeren) und versuchen Sie es erneut.';
$string['emptycolumn'] = 'In dieser Spalte gibt es keine Zellen.';
$string['enableh5pimport'] = 'H5P-Import aktivieren';
$string['enableh5pimport_desc'] = 'Wenn aktiviert, zeigt das Frageformular ein Feld „Aus H5P importieren", mit dem Inhalte aus einer vorhandenen H5P.ColumnConnector-Datei (.h5p) in die Frage importiert werden. Standardmäßig deaktiviert. Beispiele: <a href="https://urmasvessin.ee/testid/h5p/h5p-sisutuup-connect-with-lines-uhenda-joontega/" target="_blank" rel="noopener">urmasvessin.ee</a>. Quellcode (H5P-Inhaltstyp): <a href="https://github.com/urmasv/H5P.ColumnConnector" target="_blank" rel="noopener">GitHub</a>.';
$string['erroranswerkey'] = 'Eine richtige Verbindung ist fehlerhaft oder verbindet keine benachbarten Spalten.';
$string['erroranswerkeyindex'] = 'Eine richtige Verbindung verweist auf eine Zelle, die nicht existiert.';
$string['errorcorrectpoints'] = 'Die Punkte für eine richtige Verbindung müssen größer als null sein.';
$string['erroremptycolumn'] = 'Spalte {$a} muss mindestens eine Zelle enthalten.';
$string['errorincorrectpoints'] = 'Die Punkte für eine falsche Verbindung müssen null oder negativ sein.';
$string['errorinvalidcontent'] = 'Der Frageninhalt ist unvollständig oder ungültig.';
$string['errorinvalidquestion'] = 'Die Frage „{$a->name}“ kann nicht gespeichert werden: {$a->errors}';
$string['errormissingcolumns'] = 'Der Inhalt muss mindestens so viele Spalten definieren wie ausgewählt.';
$string['errormissingpoints'] = 'Die Punkte für eine fehlende Verbindung müssen null oder negativ sein.';
$string['errornoanswerkey'] = 'Markieren Sie mindestens eine richtige Verbindung.';
$string['errornumcolumns'] = 'Die Anzahl der Spalten muss zwischen 2 und 7 liegen.';
$string['h5pimport'] = 'Aus H5P importieren';
$string['h5pimport_help'] = 'Wählen Sie eine vorhandene H5P.ColumnConnector-Datei (.h5p) und klicken Sie auf „Aus H5P laden“. Der Inhalt (Spalten, Zellen, Verbindungen, Bilder) wird in den Editor geladen; prüfen und speichern.';
$string['h5pimporterror'] = 'Die H5P-Datei konnte nicht gelesen werden.';
$string['h5pimportload'] = 'Aus H5P laden';
$string['h5pimporttoolarge'] = 'Die H5P-Datei ist zu groß. Maximale Größe: {$a}.';
$string['h5pimporttoomanyfiles'] = 'Die H5P-Datei enthält zu viele Inhaltsdateien. Maximum: {$a}.';
$string['h5pimportunpackedtoolarge'] = 'Der entpackte Inhalt der H5P-Datei ist zu groß. Maximale Größe: {$a}.';
$string['h5pimportwrongtype'] = 'Dies ist kein H5P.ColumnConnector-Inhalt.';
$string['imageabove'] = 'Über dem Text';
$string['imagealigncenter'] = 'zentriert';
$string['imagealignleft'] = 'links';
$string['imagealt'] = 'Alternativtext des Bildes';
$string['imageleft'] = 'Links vom Text';
$string['imageleftsize'] = 'Größe des linken Bildes';
$string['imageposition'] = 'Bildposition';
$string['imageurl'] = 'Bild-URL';
$string['incorrectpoints'] = 'Punkte für eine falsche Verbindung';
$string['interactionhelp'] = 'Wählen Sie eine Zelle und dann eine Zelle in einer benachbarten Spalte, um sie zu verbinden. Wenn Sie dasselbe Paar erneut wählen, wird die Verbindung entfernt. Mit Escape heben Sie die Auswahl auf.';
$string['interactionhelprows'] = 'Wählen Sie eine Zelle und dann eine Zelle in einer benachbarten Zeile, um sie zu verbinden. Wenn Sie dasselbe Paar erneut wählen, wird die Verbindung entfernt. Mit Escape heben Sie die Auswahl auf.';
$string['invalidtarget'] = 'Sie können nur mit einer Zelle in einer benachbarten Spalte verbinden.';
$string['layoutcolumns'] = 'Spalten (vertikal)';
$string['layoutmode'] = 'Layout';
$string['layoutrows'] = 'Zeilen (horizontal)';
$string['linecurved'] = 'Gebogen';
$string['linestraight'] = 'Gerade';
$string['linestyle'] = 'Linienstil';
$string['missingpoints'] = 'Punkte für eine fehlende Verbindung';
$string['movedown'] = 'Nach unten';
$string['moveup'] = 'Nach oben';
$string['noconnection'] = 'keine Verbindung';
$string['noimage'] = '— kein Bild —';
$string['noneighbourcells'] = 'Die vorherige Spalte hat keine Zellen.';
$string['numcolumns'] = 'Anzahl der Spalten';
$string['numcolumns_help'] = 'Wählen Sie, ob die Aktivität mit zwei bis sieben Spalten angezeigt wird.';
$string['numrows'] = 'Anzahl der Zeilen';
$string['pleaseconnect'] = 'Bitte stellen Sie mindestens eine Verbindung her.';
$string['pluginname'] = 'Spaltenverbinder';
$string['pluginname_help'] = 'Erstellen Sie eine Frage mit 2–7 Spalten von Zellen. Lernende ziehen Linien, um Zellen in benachbarten Spalten zu verbinden; die Antwort wird anhand der von Ihnen definierten Verbindungen bewertet.';
$string['pluginname_link'] = 'question/type/columnconnector';
$string['pluginnameadding'] = 'Spaltenverbinder-Frage hinzufügen';
$string['pluginnameediting'] = 'Spaltenverbinder-Frage bearbeiten';
$string['pluginnamesummary'] = 'Zeigt 2–7 betitelte Spalten mit Zellen an und lässt Lernende Zellen benachbarter Spalten mit Linien verbinden. Punkte werden für richtige, falsche und fehlende Verbindungen vergeben.';
$string['privacy:preference:defaultmark'] = 'Die Standardbewertung für eine Frage.';
$string['refreshimages'] = 'Bildliste aktualisieren';
$string['regradeissuecellorderchanged'] = 'Die Zellen oder ihre Reihenfolge wurden geändert, daher kann der Versuch mit dieser Version nicht neu bewertet werden.';
$string['regradeissuestructurechanged'] = 'Die Anzahl der Spalten oder Zellen hat sich geändert, daher kann der Versuch mit dieser Version nicht neu bewertet werden.';
$string['removecell'] = 'Zelle entfernen';
$string['row'] = 'Zeile';
$string['rowtitle'] = 'Zeilentitel';
$string['rtealigncenter'] = 'Zentriert';
$string['rtealignleft'] = 'Linksbündig';
$string['rtealignright'] = 'Rechtsbündig';
$string['rtebold'] = 'Fett';
$string['rtebullet'] = 'Aufzählungsliste';
$string['rteclear'] = 'Formatierung entfernen';
$string['rteitalic'] = 'Kursiv';
$string['rtelink'] = 'Link einfügen';
$string['rtelinkprompt'] = 'Link-URL:';
$string['rtenumbered'] = 'Nummerierte Liste';
$string['rteunderline'] = 'Unterstrichen';
$string['selectcell'] = 'Auswählen: {$a}';
$string['selectedcell'] = 'Ausgewählt: {$a}. Wählen Sie eine Zelle in einer benachbarten Spalte.';
$string['showclear'] = 'Schaltfläche „Verbindungen löschen“ anzeigen';
$string['showmissing'] = 'Fehlende Verbindungen anzeigen';
$string['showmissing_help'] = 'Wenn aktiviert, werden fehlende richtige Verbindungen (bernsteinfarbene Linien) nach der Abgabe angezeigt – sofern die Berichtsoptionen das Anzeigen der richtigen Antwort erlauben. Standardmäßig deaktiviert.';
$string['sizelarge'] = 'groß';
$string['sizemedium'] = 'mittel';
$string['sizesmall'] = 'klein';
$string['statsconnected'] = '{$a}: verbunden';
$string['statsnotconnected'] = '{$a}: nicht verbunden';
$string['uploadedimage'] = 'Hochgeladenes Bild';
