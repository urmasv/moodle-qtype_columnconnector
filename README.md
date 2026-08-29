# Ühenda joontega (qtype_columnconnector)

Moodle'i küsimusetüüp, kus õppija ühendab **naabertulpade lahtreid joontega**.
Tulpi (või ridu) on 2–7, iga lahter võib sisaldada rikasteksti ja pilti.
Vastust hinnatakse autori määratud õigete ühenduste järgi. Käitumine kordab
H5P sisutüüpi **H5P.ColumnConnector**.

<!-- Asenda OWNER/REPO oma hoidla nimega. -->
![Moodle plugin CI](https://github.com/OWNER/moodle-qtype_columnconnector/actions/workflows/ci.yml/badge.svg)

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

- Moodle 4.1 või uuem (`$plugin->requires = 2022112800`).
- PHP 8.1+.

## Paigaldus

1. Kopeeri kaust asukohta `question/type/columnconnector` (nii et failid on
   `question/type/columnconnector/version.php` jne).
2. Logi sisse administraatorina ja käivita andmebaasi uuendus
   (Saidi haldus → Teavitused).
3. Uuendamisel puhasta vahemälud (Saidi haldus → Arendus → Puhasta
   vahemälud), et uus JavaScript ja CSS laaditaks.

Zip-paigalduseks pane hoidla juur (see kaust) `.zip`-i nii, et arhiivi sees on
kaust `columnconnector/`.

## Kasutamine

Lisa küsimustepanka uus **„Ühenda joontega"** küsimus. Määra tulpade arv ja
paigutus, täida tulpade pealkirjad ja lahtrid ning märgi igal 2.–7. tulba
lahtril õiged ühendused eelmise tulba lahtritega. Soovi korral impordi sisu
olemasolevast `.h5p`-failist väljaga **„Impordi H5P-st"**.

## Hindamine

    punktid = õigeid × correctpoints + valesid × incorrectpoints
              + puuduvaid × missingpoints
    punktid = max(0, punktid)
    murdosa = punktid / (õigete_koguarv × correctpoints)   (0..1)

Ühendused on suunast sõltumatud (A–B = B–A), duplikaate ei arvestata.
Lahtrite kuvamisjärjekord segatakse iga katse alguses; hindamine kasutab
lahtrite püsivaid indekseid, seega segamine tulemust ei mõjuta.

## Näidisküsimused

`samples/columnconnector-naidised.xml` sisaldab näidisküsimusi Moodle XML
vormingus (impordi küsimustepanka).

## Testid ja CI

Ühiktestid on kaustas `tests/`. GitHubis käivitab
`.github/workflows/ci.yml` [moodle-plugin-ci](https://github.com/moodlehq/moodle-plugin-ci)
kontrollid (phplint, phpcs Moodle'i standard, phpdoc, PHPUnit, grunt jm)
Moodle 4.1 / 4.5 / 5.0 ja PHP 8.1–8.3 vastu.

## Failistruktuur

    version.php                    — plugina versioon ja metaandmed
    questiontype.php               — salvestus/laadimine, XML import/eksport
    question.php                   — küsimuse definitsioon ja hindamine
    edit_columnconnector_form.php  — toimetamise vorm + H5P-import
    renderer.php                   — kuvamine
    styles.css                     — stiilid
    classes/h5p_importer.php       — H5P (1.3/2.0) → mudel teisendus
    classes/privacy/provider.php   — privaatsus (ei salvesta isikuandmeid)
    db/install.xml, db/upgrade.php — andmebaas
    amd/src/player.js              — interaktiivne joonistamine (õpilane)
    amd/src/editor.js              — visuaalne autoritoimetaja (õpetaja)
    lang/                          — 7 keelefaili
    backup/moodle2/                — varundus ja taastamine
    tests/                         — abiklass ja ühiktestid
    samples/                       — näidisküsimused (Moodle XML)

## Litsents

GNU GPL v3 või uuem — vaata [LICENSE](LICENSE).

## Autor

© 2026 Urmas Vessin. Ikoon (`pix/icon.svg`) on autori enda oma.
Käitumine ja andmemudel on tuletatud H5P sisutüübist H5P.ColumnConnector.
