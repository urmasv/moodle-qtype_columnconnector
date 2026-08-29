# Muudatuste logi

Kõik märkimisväärsed muudatused on kirjas selles failis.
Kuupäevad järgivad `version.php`-s olevat versioonitemplit (AAAAKKPP).

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
