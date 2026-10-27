# Muudatuste logi

Kõik märkimisväärsed muudatused on kirjas selles failis.
Kuupäevad järgivad `version.php`-s olevat versioonitemplit (AAAAKKPP).

## 1.2.1 — 2026-10-27

- Ümberhindamine: lahtritel on nüüd püsivad id-d, mis säilivad toimetamisel ja
  teisaldamisel. Ümberhindamine teise versiooniga on lubatud ainult siis, kui igas
  tulbas on samad lahtrid samas järjekorras (lahtri sisu parandamine on lubatud).
  Uued ja importimisel (XML, H5P) asendatud lahtrid saavad uue juhusliku id.
  Uuendusskript ja vanade varunduste taastamine tuletavad id lahtri sisust, nii et
  eraldi teisendatud vanade versioonide ümberjärjestamine on tuvastatav. Asukohapõhiseid
  id-sid (`c0r0` jne, 1.2.1 varasem väljalase) ei loeta identiteediks; nende korral
  võrreldakse lahtrite sisu.
- Taastamise identiteedi võrdlus jätab lahtrite id-d välja, nii et sisult identsed
  küsimused ja vanade varunduste sisu seotakse olemasolevate küsimustega nagu varem.
- Uuendusskript ja taastamine parandavad ka samas tulbas korduvad ja vigase kujuga
  lahtrite id-d (nt vigasest varukoopiast), nagu teeb tavaline salvestamine.
- Hindamisseadistuse kontroll on ühine vormile, salvestamisele ja XML-impordile
  (punktide märgid, tulpade arv, tühjad tulbad, tühi vastusevõti); vigane import lükatakse
  veateatega tagasi (ka lubatud vahemikust väljas olev tulpade arv, mida ei piirata vaikselt).
- Lahtri sisu (sh hüperlingid) ei ole enam nupu sees: lahtril on eraldi valikunupp, lingid
  on iseseisvad juhtelemendid.
- `get_random_guess_score()` tagastab `null` (juhusliku pakkumise skoori ei saa
  põhjendatult hinnata).
- Taastamine: `convert_backup_to_questiondata()` viib vanemate varunduste andmed
  (id-deta lahtrid, eraldi juhis) samale kujule, mis taastamisel salvestatakse, et
  korduv taastamine ei tekitaks küsimustepanka duplikaate. Lisatud korduva taastamise
  testid jagatud küsimusega (ka vana vormingu varundusega).
- Statistika: iga lahtripaari kohta loetakse nüüd nii ühendatud kui ka ühendamata
  vastused (ning vastuseta katsed); lisatud vastuste analüüsi test päris katsetega.
- H5P-import: üleslaaditava faili suurus ja lahtipakitavate failide kogumaht on piiratud
  saidi sättega `moodlecourse | maxbytes` (0 = saidi üleslaadimispiirang); arhiivist
  pakitakse lahti ainult h5p.json, content/content.json ja viidatud pildid; content-kaustas
  võib olla kuni lahtrite arv + 1 faili.

## 1.2.0 — 2026-10-27

Turvalisus
- H5P-import: pildifaili tee peab jääma paketi `content`-kausta sisse (varem sai
  `../`-teega lugeda serveri faile); imporditakse ainult veebipilte.
- Lahtri HTML puhastatakse salvestamisel ja kuvamisel; toimetaja puhastab HTML-i enne
  kuvamist (skriptid, sündmuste atribuudid ja `javascript:`-lingid eemaldatakse).
- Pildi-URL peab olema `http(s)`-aadress.
- Vastusevõtit ei saadeta enam brauserisse: joonte õigsus arvutatakse serveris ja
  puuduvad ühendused saadetakse ainult siis, kui õiget vastust tohib näidata.

Parandused
- Adaptiivses režiimis jääb küsimus pärast „Kontrolli“ muudetavaks.
- Vorm nõuab vähemalt ühte õiget ühendust ja kontrollib punktide märke.
- Moodle XML impordil jäetakse vastusevõtmest välja ühendused puuduvate lahtriteni.
- Eemaldatud kasutuseta väli „instructions“ (uuendusskript tõstab juhise küsimuse tekstiks,
  kui see on tühi); XML-import ja vanade varukoopiate taastamine teevad sama.
- Taastamisest eemaldatud üleliigne failide taastamine; tuum taastab lahtripildid ise.
- Statistika vastuste analüüs: `get_possible_responses()` ja `classify_response()`.
- Ligipääsetavus: ühenduse eemaldamine klaviatuuriga (sama paari uuesti valimine),
  Escape tühistab valiku, ekraanilugeja teated ja ülevaate tekstiline kokkuvõte,
  nimeta pildilahtrid saavad asukohapõhise nime.
- Konteineri id on unikaalne (`get_outer_question_div_unique_id()`); seaded antakse
  JavaScriptile data-atribuudina (Moodle'i `js_call_amd` mahupiirang).
- Toimetaja lingiaken on vormisisene (mitte `window.prompt()`).
- Renderdaja kasutab `$this->page`, mitte globaalset `$PAGE`-i.

Ühilduvus ja kvaliteet
- Kohapeal testitud Moodle 5.3+ (PHP 8.3, PostgreSQL 17) ja Moodle 4.5 peal;
  `$plugin->supported = [401, 503]`.
- Koodistandard (moodle, moodle-extra), PHPDoc, ESLint ja Stylelint vigadeta;
  JavaScript ehitatud Moodle'i grunt'iga koos lähtekaartidega.
- Ühiktestid parandatud ja laiendatud (küsimus, küsimusetüüp, läbimängud eri
  režiimides, H5P-import, varundus ja taastamine).
- GitHubi CI seadistus: Moodle 4.1, 4.5, 5.0 ja 5.3 (5.3 PHP 8.3 ja 8.4 ning PostgreSQL 17).

## 1.1.1 — 2026-06-28

- H5P-import loeb nüüd nii uut **H5P.ColumnConnector 2.0** vormingut
  (dünaamiline `columns`-loend, `image`-objekt `file`/`imageExtra`/`position`/
  `size`/`align` väljadega, `correctToPrevious`) kui ka vana 1.3 vormingut.
  Nii üleslaaditud kui URL-iga viidatud pildid tulevad impordil kaasa.

## 1.1.0 — 2026-06-27

- Vormisisene **„Impordi H5P-st"** (.h5p) otse küsimuse loomise/muutmise
  vormil: fail pakitakse lahti, sisu (tulbad, lahtrid, ühendused, pildid)
  laaditakse visuaalsesse toimetajasse. Ei kasuta AJAX-i.

## 1.0.x — 2026-06

- Tulpade/ridade arv tõstetud 2–4 → **2–7**.
- Lahtripildid: **üleslaadimine** Moodle'i failihalduriga (lisaks välisele
  URL-ile), pildi **suurus** (väike/keskmine/suur) nii teksti kohal kui
  vasakul, pildi **asend** (teksti kohal / tekstist vasakul) ja **joondus**
  (vasakul/keskel) teksti kohal oleva pildi jaoks.
- Lahtri tekstil rikastekstitööriistad (paks/kaldkiri/allajoonitud, loendid,
  link, joondus).
- Ülevaaterežiimis värvitud jooned: õige (sinine), vale (punane) ja
  valikuline puuduvate ühenduste kuvamine (merevaigukollane).
- Jooned joonistatakse ümber tööala laiuse muutumisel (kursusemenüü/
  plokisahtli klappimine) `ResizeObserver`-i abil.
- **7 keelefaili**: et, en, de, fr, es, ru, uk.
- Oma ikoon (`pix/icon.svg`).

## 1.0.0

- H5P.ColumnConnector esmane port Moodle'i küsimusetüübiks: andmemudel,
  hindamisvalem, interaktiivne joonistaja (õpilane), visuaalne toimetaja
  (õpetaja), Moodle XML import/eksport, varundus/taastamine, ühiktestid.
