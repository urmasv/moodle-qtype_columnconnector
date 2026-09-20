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

$string['pluginname'] = 'Ühenda joontega';
$string['pluginname_help'] = 'Loo küsimus 2–7 tulba lahtritega. Õppija ühendab joontega naabertulpade lahtreid; vastust hinnatakse sinu määratud õigete ühenduste järgi.';
$string['pluginname_link'] = 'question/type/columnconnector';
$string['pluginnameadding'] = 'Ühenda joontega küsimuse lisamine';
$string['pluginnameediting'] = 'Ühenda joontega küsimuse toimetamine';
$string['pluginnamesummary'] = 'Kuvab 2–7 pealkirjastatud tulpa lahtritega ja laseb õppijal ühendada naabertulpade lahtreid joontega. Punkte antakse õigete, valede ja puuduvate ühenduste eest.';

$string['columnconnectorsettings'] = 'Seaded';
$string['numcolumns'] = 'Tulpade arv';
$string['numrows'] = 'Ridade arv';
$string['numcolumns_help'] = 'Vali, kas tegevust kuvatakse kahe kuni seitsme tulbaga.';
$string['layoutmode'] = 'Paigutus';
$string['layoutcolumns'] = 'Tulbad (vertikaalne)';
$string['layoutrows'] = 'Read (horisontaalne)';
$string['linestyle'] = 'Joone stiil';
$string['linecurved'] = 'Kaardus';
$string['linestraight'] = 'Sirge';
$string['showclear'] = 'Näita nuppu „Tühista ühendused”';
$string['showmissing'] = 'Näita puuduvaid ühendusi';
$string['showmissing_help'] = 'Kui sees, näidatakse õppijale pärast esitamist ka puuduvad õiged ühendused (oranžid jooned) — juhul kui ülevaade lubab õigeid vastuseid näidata. Vaikimisi väljas.';
$string['instructions'] = 'Juhis õppijale';
$string['instructions_help'] = 'Lühike HTML-juhis, mida näidatakse tulpade kohal.';
$string['correctpoints'] = 'Punktid õige ühenduse eest';
$string['correctpoints_help'] = 'Vaikimisi: +1 punkt iga õige ühenduse eest. Küsimuse koondhinne arvutatakse nendest punktidest.';
$string['incorrectpoints'] = 'Punktid vale ühenduse eest';
$string['missingpoints'] = 'Punktid puuduva ühenduse eest';

$string['clearconnections'] = 'Tühista ühendused';
$string['emptycolumn'] = 'Selles tulbas pole lahtreid.';
$string['invalidtarget'] = 'Ühendada saab ainult naabertulba lahtriga.';
$string['duplicate'] = 'See ühendus on juba olemas.';
$string['selectedcell'] = 'Lahter valitud. Vali naabertulba lahter.';
$string['pleaseconnect'] = 'Palun tee vähemalt üks ühendus.';

$string['errornumcolumns'] = 'Tulpade arv peab olema vahemikus 2 kuni 7.';
$string['errormissingcolumns'] = 'Sisu peab määrama vähemalt sama palju tulpi, kui on valitud.';
$string['erroremptycolumn'] = 'Tulp {$a} peab sisaldama vähemalt üht lahtrit.';
$string['erroranswerkey'] = 'Õige ühendus on vigane või ei seo naabertulpi.';
$string['erroranswerkeyindex'] = 'Õige ühendus viitab lahtrile, mida pole olemas.';

$string['contenteditor'] = 'Tulbad, lahtrid ja õiged ühendused';
$string['h5pimport'] = 'Impordi H5P-st';
$string['h5pimport_help'] = 'Vali olemasolev H5P.ColumnConnector (.h5p) fail ja vajuta „Laadi H5P-st". Sisu (tulbad, lahtrid, ühendused, pildid) laaditakse toimetajasse; vaata üle ja salvesta.';
$string['h5pimportload'] = 'Laadi H5P-st';
$string['h5pimporterror'] = 'H5P-faili lugemine ebaõnnestus.';
$string['h5pimportwrongtype'] = 'See ei ole H5P.ColumnConnector tüüpi sisu.';
$string['column'] = 'Tulp';
$string['row'] = 'Rida';
$string['columntitle'] = 'Tulba pealkiri';
$string['rowtitle'] = 'Rea pealkiri';
$string['cell'] = 'Lahter';
$string['celltext'] = 'Tekst';
$string['imageurl'] = 'Pildi URL';
$string['imageposition'] = 'Pildi asend';
$string['imageabove'] = 'teksti kohal';
$string['imageleft'] = 'tekstist vasakul';
$string['imageleftsize'] = 'Pildi suurus';
$string['imagealignleft'] = 'vasakul';
$string['imagealigncenter'] = 'keskel';
$string['sizesmall'] = 'väike';
$string['sizemedium'] = 'keskmine';
$string['sizelarge'] = 'suur';
$string['imagealt'] = 'Pildi alternatiivtekst';
$string['addcell'] = 'Lisa lahter';
$string['removecell'] = 'Eemalda lahter';
$string['moveup'] = 'Liiguta üles';
$string['movedown'] = 'Liiguta alla';
$string['correctconnections'] = 'Õiged ühendused eelmise tulbaga';
$string['correctconnectionsrow'] = 'Õiged ühendused eelmise reaga';
$string['noconnection'] = 'ühendus puudub';
$string['connectioncount'] = '{$a} ühendust';
$string['noneighbourcells'] = 'Eelmises tulbas pole lahtreid.';
$string['cellfallback'] = 'lahter';
$string['defaultinstructions'] = 'Ühenda lahtrid naabertulpades olevate lahtritega. Üks lahter võib olla ühendatud mitme naabertulba lahtriga.';
$string['errorinvalidcontent'] = 'Küsimuse sisu on puudulik või vigane.';

$string['editorloaderror'] = 'Toimetajat ei õnnestunud laadida. Puhastage vahemälu (Saidi haldus → Arendus → Puhasta vahemälud) ja proovige uuesti.';

$string['cellimages'] = 'Lahtrite pildid';
$string['cellimages_help'] = 'Laadi siia üles pildid, mida lahtrites kasutada. Seejärel vali iga lahtri juures rippmenüüst üleslaaditud pilt. Alternatiivina võib lahtrile anda välise pildi-URL-i.';
$string['uploadedimage'] = 'Üleslaaditud pilt';
$string['noimage'] = '— pilt puudub —';
$string['refreshimages'] = 'Värskenda pildinimekirja';
$string['rtebold'] = 'Rasvane';
$string['rteitalic'] = 'Kaldkiri';
$string['rteunderline'] = 'Allajoonitud';
$string['rtebullet'] = 'Täpploend';
$string['rtenumbered'] = 'Nummerdatud loend';
$string['rtelink'] = 'Lisa link';
$string['rtelinkprompt'] = 'Lingi URL:';
$string['rtealignleft'] = 'Vasakjoondus';
$string['rtealigncenter'] = 'Keskjoondus';
$string['rtealignright'] = 'Paremjoondus';
$string['rteclear'] = 'Eemalda vorming';

$string['privacy:metadata'] = 'Küsimusetüüp „Tulpade ühendaja” ei salvesta isikuandmeid.';
