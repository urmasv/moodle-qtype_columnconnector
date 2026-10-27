# Ühenda joontega (qtype_columnconnector)

Moodle'i küsimusetüüp, kus õppija ühendab **naabertulpade lahtreid joontega**.
Tulpi (või ridu) on 2–7, iga lahter võib sisaldada rikasteksti ja pilti.
Vastust hinnatakse autori määratud õigete ühenduste järgi. Käitumine kordab
H5P sisutüüpi **H5P.ColumnConnector**.

## Võimalused

- **2–7 tulpa/rida** (püstine või rõhtne paigutus).
- **Visuaalne autoritoimetaja**: tulpade pealkirjad, lahtrite lisamine/
  eemaldamine/järjestamine, õigete ühenduste valik märkeruutudega.
- **Rikastekst** lahtris (paks, kaldkiri, allajoonitud, loendid, link, joondus).
- **Lahtripildid**: üleslaadimine Moodle'i failihalduriga või väline URL;
  pildi suurus (väike/keskmine/suur), asend (teksti kohal / tekstist vasakul)
  ja joondus (vasakul/keskel) teksti kohal oleva pildi jaoks.
- **Hindamine** eraldi punktidega õige, vale ja puuduva ühenduse eest;
  ülevaates värvitud jooned ja valikuline puuduvate ühenduste kuvamine.
- **H5P-import** otse vormil: loe olemasolev `.h5p`
  (H5P.ColumnConnector 1.3 **ja** 2.0) toimetajasse.
- **7 keelt**: eesti, inglise, saksa, prantsuse, hispaania, vene, ukraina.

## Nõuded

- Moodle 4.1 kuni 5.3 (`$plugin->requires = 2022112800`, `$plugin->supported = [401, 503]`).
- PHP vastavalt Moodle'i versioonile: Moodle 4.1 PHP 7.4–8.1, Moodle 4.5 PHP 8.1–8.3,
  Moodle 5.0–5.1 PHP 8.2+, Moodle 5.2–5.3 PHP 8.3–8.4.
- Moodle 5.3 andmebaasinõuded kehtivad ka siin (nt PostgreSQL 17, MariaDB 11.4, MySQL 8.4).

## Paigaldus

1. Kopeeri plugina kaust nimega `columnconnector` küsimusetüüpide kausta:
   - Moodle 5.1 ja uuemad: `public/question/type/columnconnector`
     (nii et fail on `public/question/type/columnconnector/version.php`);
   - Moodle 4.1–5.0: `question/type/columnconnector`.
2. Logi sisse administraatorina ja käivita andmebaasi uuendus
   (Saidi haldus → Teavitused) või `php admin/cli/upgrade.php`.
3. Uuendamisel puhasta vahemälud (Saidi haldus → Arendus → Puhasta vahemälud).

ZIP-failist paigaldamisel (Saidi haldus → Pluginad → Paigalda plugin) nimetab Moodle
arhiivi juurkausta ise `version.php` järgi ümber, seega sobib ka GitHubist alla laaditud
`moodle-qtype_columnconnector-main.zip`.

## Versioonilt 1.1 uuendamine

Eraldi väli „Juhis õppijale“ on eemaldatud – juhis kuulub küsimuse teksti. Uuendusskript
tõstab olemasoleva juhise küsimuse tekstiks, kui küsimuse tekst on tühi; muul juhul jääb
küsimuse tekst muutmata. Sama tehakse vanade Moodle XML failide impordil ja vanade
varukoopiate taastamisel.

## Kasutamine

Lisa küsimustepanka uus **„Ühenda joontega"** küsimus. Määra tulpade arv ja
paigutus, täida tulpade pealkirjad ja lahtrid ning märgi igal 2.–7. tulba
lahtril õiged ühendused eelmise tulba lahtritega (vähemalt üks ühendus on kohustuslik).
Soovi korral impordi sisu olemasolevast `.h5p`-failist väljaga **„Impordi H5P-st"**
(administraator peab selle plugina seadetes lubama).

Õppija valib lahtri ja seejärel naabertulba lahtri. Sama paari uuesti valimine eemaldab
ühenduse; ühenduse saab eemaldada ka joonele klõpsates. Kõik toimib ka klaviatuuriga
(Tab, Enter/tühik, Escape tühistab valiku) ning muudatustest antakse ekraanilugejale teada.

Ülevaates näidatakse õppija joonte õigsust vastavalt testi sättele „Kas õige“. Puuduvad
õiged ühendused näidatakse ainult siis, kui on lubatud nii testi säte „Õige vastus“ kui ka
küsimuse säte „Näita puuduvaid ühendusi“.

## Hindamine

    punktid = õigeid × correctpoints + valesid × incorrectpoints
              + puuduvaid × missingpoints
    punktid = max(0, punktid)
    murdosa = punktid / (õigete_koguarv × correctpoints)   (0..1)

Punktid õige ühenduse eest peavad olema suuremad kui null, vale ja puuduva ühenduse
eest null või negatiivsed.

Ühendused on suunast sõltumatud (A–B = B–A), duplikaate ei arvestata.
Lahtrite kuvamisjärjekord segatakse iga katse alguses; hindamine kasutab
lahtrite püsivaid indekseid, seega segamine tulemust ei mõjuta.

## Ümberhindamine ja statistika

Igal lahtril on püsiv id, mis säilib lahtri toimetamisel ja teisaldamisel. Testi
ümberhindamine küsimuse uue versiooniga on lubatud ainult siis, kui igas tulbas on samad
lahtrid samas järjekorras; lahtri sisu ja õigete ühenduste parandamine on lubatud.

Vastuste analüüsis on iga naabertulpade lahtripaar eraldi osa, kus loetakse, mitu
õppijat selle paari ühendas ja mitu jättis ühendamata.

## Näidisküsimused

`samples/columnconnector-naidised.xml` sisaldab näidisküsimusi Moodle XML
vormingus (impordi küsimustepanka).

## Litsents

GNU GPL v3 või uuem — vaata [LICENSE](LICENSE).

## Autor

© 2026 Urmas Vessin. Käitumine ja andmemudel on tuletatud H5P sisutüübist H5P.ColumnConnector.
