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
 * Eestikeelsed stringid qtype_columnconnector jaoks.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcell'] = 'Lisa lahter';
$string['cell'] = 'Lahter';
$string['cellfallback'] = 'lahter';
$string['cellimages'] = 'Lahtrite pildid';
$string['cellimages_help'] = 'Laadi siia üles pildid, mida lahtrites kasutada. Seejärel vali iga lahtri juures rippmenüüst üleslaaditud pilt. Alternatiivina võib lahtrile anda välise pildi-URL-i.';
$string['cellposition'] = 'Tulp {$a->col}, lahter {$a->row}';
$string['cellpositionrows'] = 'Rida {$a->col}, lahter {$a->row}';
$string['celltext'] = 'Tekst';
$string['clearconnections'] = 'Tühista ühendused';
$string['column'] = 'Tulp';
$string['columnconnectorsettings'] = 'Seaded';
$string['columntitle'] = 'Tulba pealkiri';
$string['connectionadded'] = 'Ühendatud: {$a}';
$string['connectioncorrect'] = '{$a}: õige';
$string['connectioncount'] = '{$a} ühendust';
$string['connectionincorrect'] = '{$a}: vale';
$string['connectionmissing'] = '{$a}: puudu (õige ühendus)';
$string['connectionremoved'] = 'Ühendus eemaldatud: {$a}';
$string['connectionscleared'] = 'Kõik ühendused eemaldatud.';
$string['connectionsummary'] = 'Ühendused';
$string['contenteditor'] = 'Tulbad, lahtrid ja õiged ühendused';
$string['correctansweris'] = 'Õige vastus on: {$a}';
$string['correctconnections'] = 'Õiged ühendused eelmise tulbaga';
$string['correctconnectionsrow'] = 'Õiged ühendused eelmise reaga';
$string['correctpoints'] = 'Punktid õige ühenduse eest';
$string['correctpoints_help'] = 'Vaikimisi: +1 punkt iga õige ühenduse eest. Küsimuse koondhinne arvutatakse nendest punktidest.';
$string['editorloaderror'] = 'Toimetajat ei õnnestunud laadida. Puhastage vahemälu (Saidi haldus → Arendus → Puhasta vahemälud) ja proovige uuesti.';
$string['emptycolumn'] = 'Selles tulbas pole lahtreid.';
$string['enableh5pimport'] = 'Luba H5P-import';
$string['enableh5pimport_desc'] = 'Kui lubatud, kuvatakse küsimuse koostamise vormis väli „Impordi H5P-st", millega saab olemasolevast H5P.ColumnConnector (.h5p) failist sisu küsimusse importida. Vaikimisi väljas. Näidised: <a href="https://urmasvessin.ee/testid/h5p/h5p-sisutuup-connect-with-lines-uhenda-joontega/" target="_blank" rel="noopener">urmasvessin.ee</a>. Lähtekood (H5P sisutüüp): <a href="https://github.com/urmasv/H5P.ColumnConnector" target="_blank" rel="noopener">GitHub</a>.';
$string['erroranswerkey'] = 'Õige ühendus on vigane või ei seo naabertulpi.';
$string['erroranswerkeyindex'] = 'Õige ühendus viitab lahtrile, mida pole olemas.';
$string['errorcorrectpoints'] = 'Õige ühenduse punktid peavad olema suuremad kui null.';
$string['erroremptycolumn'] = 'Tulp {$a} peab sisaldama vähemalt üht lahtrit.';
$string['errorincorrectpoints'] = 'Vale ühenduse punktid peavad olema null või negatiivsed.';
$string['errorinvalidcontent'] = 'Küsimuse sisu on puudulik või vigane.';
$string['errorinvalidquestion'] = 'Küsimust „{$a->name}“ ei saa salvestada: {$a->errors}';
$string['errormissingcolumns'] = 'Sisu peab määrama vähemalt sama palju tulpi, kui on valitud.';
$string['errormissingpoints'] = 'Puuduva ühenduse punktid peavad olema null või negatiivsed.';
$string['errornoanswerkey'] = 'Märgi vähemalt üks õige ühendus.';
$string['errornumcolumns'] = 'Tulpade arv peab olema vahemikus 2 kuni 7.';
$string['h5pimport'] = 'Impordi H5P-st';
$string['h5pimport_help'] = 'Vali olemasolev H5P.ColumnConnector (.h5p) fail ja vajuta „Laadi H5P-st". Sisu (tulbad, lahtrid, ühendused, pildid) laaditakse toimetajasse; vaata üle ja salvesta.';
$string['h5pimporterror'] = 'H5P-faili lugemine ebaõnnestus.';
$string['h5pimportload'] = 'Laadi H5P-st';
$string['h5pimporttoolarge'] = 'H5P-fail on liiga suur. Suurim lubatud maht: {$a}.';
$string['h5pimporttoomanyfiles'] = 'H5P-failis on liiga palju sisufaile. Suurim lubatud arv: {$a}.';
$string['h5pimportunpackedtoolarge'] = 'H5P-faili lahtipakitud sisu on liiga suur. Suurim lubatud maht: {$a}.';
$string['h5pimportwrongtype'] = 'See ei ole H5P.ColumnConnector tüüpi sisu.';
$string['imageabove'] = 'teksti kohal';
$string['imagealigncenter'] = 'keskel';
$string['imagealignleft'] = 'vasakul';
$string['imagealt'] = 'Pildi alternatiivtekst';
$string['imageleft'] = 'tekstist vasakul';
$string['imageleftsize'] = 'Pildi suurus';
$string['imageposition'] = 'Pildi asend';
$string['imageurl'] = 'Pildi URL';
$string['incorrectpoints'] = 'Punktid vale ühenduse eest';
$string['interactionhelp'] = 'Vali lahter ja seejärel naabertulba lahter, et need ühendada. Sama paari uuesti valimine eemaldab ühenduse. Valiku tühistamiseks vajuta Escape.';
$string['interactionhelprows'] = 'Vali lahter ja seejärel naaberrea lahter, et need ühendada. Sama paari uuesti valimine eemaldab ühenduse. Valiku tühistamiseks vajuta Escape.';
$string['invalidtarget'] = 'Ühendada saab ainult naabertulba lahtriga.';
$string['layoutcolumns'] = 'Tulbad (vertikaalne)';
$string['layoutmode'] = 'Paigutus';
$string['layoutrows'] = 'Read (horisontaalne)';
$string['linecurved'] = 'Kaardus';
$string['linestraight'] = 'Sirge';
$string['linestyle'] = 'Joone stiil';
$string['missingpoints'] = 'Punktid puuduva ühenduse eest';
$string['movedown'] = 'Liiguta alla';
$string['moveup'] = 'Liiguta üles';
$string['noconnection'] = 'ühendus puudub';
$string['noimage'] = '— pilt puudub —';
$string['noneighbourcells'] = 'Eelmises tulbas pole lahtreid.';
$string['numcolumns'] = 'Tulpade arv';
$string['numcolumns_help'] = 'Vali, kas tegevust kuvatakse kahe kuni seitsme tulbaga.';
$string['numrows'] = 'Ridade arv';
$string['pleaseconnect'] = 'Palun tee vähemalt üks ühendus.';
$string['pluginname'] = 'Ühenda joontega';
$string['pluginname_help'] = 'Loo küsimus 2–7 tulba lahtritega. Õppija ühendab joontega naabertulpade lahtreid; vastust hinnatakse sinu määratud õigete ühenduste järgi.';
$string['pluginname_link'] = 'question/type/columnconnector';
$string['pluginnameadding'] = 'Ühenda joontega küsimuse lisamine';
$string['pluginnameediting'] = 'Ühenda joontega küsimuse toimetamine';
$string['pluginnamesummary'] = 'Kuvab 2–7 pealkirjastatud tulpa lahtritega ja laseb õppijal ühendada naabertulpade lahtreid joontega. Punkte antakse õigete, valede ja puuduvate ühenduste eest.';
$string['privacy:preference:defaultmark'] = 'Küsimusele määratud vaikimisi hinne.';
$string['refreshimages'] = 'Värskenda pildinimekirja';
$string['regradeissuecellorderchanged'] = 'Lahtrid või nende järjekord on muutunud, seetõttu ei saa katset selle versiooniga ümber hinnata.';
$string['regradeissuestructurechanged'] = 'Tulpade või lahtrite arv on muutunud, mistõttu ei saa katset selle versiooniga ümber hinnata.';
$string['removecell'] = 'Eemalda lahter';
$string['row'] = 'Rida';
$string['rowtitle'] = 'Rea pealkiri';
$string['rtealigncenter'] = 'Keskjoondus';
$string['rtealignleft'] = 'Vasakjoondus';
$string['rtealignright'] = 'Paremjoondus';
$string['rtebold'] = 'Rasvane';
$string['rtebullet'] = 'Täpploend';
$string['rteclear'] = 'Eemalda vorming';
$string['rteitalic'] = 'Kaldkiri';
$string['rtelink'] = 'Lisa link';
$string['rtelinkprompt'] = 'Lingi URL:';
$string['rtenumbered'] = 'Nummerdatud loend';
$string['rteunderline'] = 'Allajoonitud';
$string['selectcell'] = 'Vali: {$a}';
$string['selectedcell'] = 'Valitud: {$a}. Vali naabertulba lahter.';
$string['showclear'] = 'Näita nuppu „Tühista ühendused”';
$string['showmissing'] = 'Näita puuduvaid ühendusi';
$string['showmissing_help'] = 'Kui sees, näidatakse õppijale pärast esitamist ka puuduvad õiged ühendused (oranžid jooned) — juhul kui ülevaade lubab õigeid vastuseid näidata. Vaikimisi väljas.';
$string['sizelarge'] = 'suur';
$string['sizemedium'] = 'keskmine';
$string['sizesmall'] = 'väike';
$string['statsconnected'] = '{$a}: ühendatud';
$string['statsnotconnected'] = '{$a}: ühendamata';
$string['uploadedimage'] = 'Üleslaaditud pilt';
