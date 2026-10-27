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
 * H5P.ColumnConnector -> qtype_columnconnector impordiloogika.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Teisendab H5P.ColumnConnector sisu selle küsimusetüübi mudeliks.
 */
class qtype_columnconnector_h5p_importer {
    /** @var string[] tulpade rühmanimed H5P-s (1.–7. tulp). */
    public const COLUMN_NAMES = [
        'columnOne', 'columnTwo', 'columnThree', 'columnFour',
        'columnFive', 'columnSix', 'columnSeven',
    ];

    /** @var string[] järgarvnimed (0-põhine indeks -> nimi). */
    public const ORDINALS = ['One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven'];

    /**
     * Puhas teisendus: H5P content.json massiiv -> [numcolumns, mudel, pildiviited].
     * Ei kasuta Moodle'i API-t, seega on eraldi testitav. Tunneb ära nii uue
     * (2.0, dünaamiline `columns` loend) kui vana (1.3, columnOne..Four) vormingu.
     *
     * @param array $h5p dekodeeritud content.json
     * @return array [int $numcolumns, array $model, array $imagerefs]
     *   $imagerefs = list of ['col'=>int, 'row'=>int, 'path'=>string] (H5P failipildid)
     */
    public static function convert(array $h5p) {
        // 2.0: tulbad on dünaamiline loend `columns`. 1.3: fikseeritud columnOne..Four.
        if (isset($h5p['columns']) && is_array($h5p['columns'])) {
            return self::convert_v2($h5p);
        }
        return self::convert_v13($h5p);
    }

    /**
     * Teisenda H5P.ColumnConnector 2.0 sisu.
     *
     * @param array $h5p
     * @return array [int, array, array]
     */
    protected static function convert_v2(array $h5p) {
        $rawcolumns = array_values($h5p['columns']);
        $numcolumns = max(2, min(7, count($rawcolumns)));

        $columns = [];
        $imagerefs = [];
        for ($c = 0; $c < $numcolumns; $c++) {
            $col = (isset($rawcolumns[$c]) && is_array($rawcolumns[$c])) ? $rawcolumns[$c] : [];
            $cells = [];
            $rawcells = (isset($col['cells']) && is_array($col['cells'])) ? $col['cells'] : [];
            foreach ($rawcells as $row => $cell) {
                $cell = is_array($cell) ? $cell : [];
                $image = (isset($cell['image']) && is_array($cell['image'])) ? $cell['image'] : [];
                $extra = (isset($image['imageExtra']) && is_array($image['imageExtra'])) ? $image['imageExtra'] : [];
                $cells[] = [
                    'text' => (string)($cell['text'] ?? ''),
                    'image' => '',
                    'imageurl' => (string)($extra['url'] ?? ''),
                    'imageposition' => (($image['position'] ?? 'above') === 'left') ? 'left' : 'above',
                    'imageleftsize' => in_array(($image['size'] ?? 'small'), ['medium', 'large'], true)
                        ? $image['size'] : 'small',
                    'imagealign' => (($image['align'] ?? 'center') === 'left') ? 'left' : 'center',
                    'alt' => (string)($extra['alt'] ?? ''),
                ];
                // 2.0 üleslaaditud pilt: image.file objekt path-väljaga.
                if (isset($image['file']) && is_array($image['file']) && !empty($image['file']['path'])) {
                    $imagerefs[] = ['col' => $c, 'row' => (int)$row, 'path' => (string)$image['file']['path']];
                }
            }
            $columns[] = ['title' => (string)($col['title'] ?? ''), 'cells' => $cells];
        }

        // Vastusevõti: iga tulba (v.a esimene) lahtri correctToPrevious viitab eelmisele tulbale.
        $answerkey = [];
        for ($c = 1; $c < $numcolumns; $c++) {
            $col = (isset($rawcolumns[$c]) && is_array($rawcolumns[$c])) ? $rawcolumns[$c] : [];
            $rawcells = (isset($col['cells']) && is_array($col['cells'])) ? $col['cells'] : [];
            foreach ($rawcells as $row => $cell) {
                if (!is_array($cell) || !isset($cell['correctToPrevious'])) {
                    continue;
                }
                foreach (self::parse_indices($cell['correctToPrevious']) as $onebased) {
                    $answerkey[] = [
                        'from' => ['col' => $c, 'row' => (int)$row],
                        'to' => ['col' => $c - 1, 'row' => $onebased - 1],
                    ];
                }
            }
        }

        return [$numcolumns, ['columns' => $columns, 'answerkey' => $answerkey], $imagerefs];
    }

    /**
     * Teisenda H5P.ColumnConnector 1.3 sisu (fikseeritud columnOne..Four).
     *
     * @param array $h5p
     * @return array [int, array, array]
     */
    protected static function convert_v13(array $h5p) {
        $numcolumns = max(2, min(7, (int)($h5p['behaviour']['columns'] ?? 2)));

        $columns = [];
        $imagerefs = [];
        for ($c = 0; $c < $numcolumns; $c++) {
            $col = isset($h5p[self::COLUMN_NAMES[$c]]) && is_array($h5p[self::COLUMN_NAMES[$c]])
                ? $h5p[self::COLUMN_NAMES[$c]] : [];
            $cells = [];
            $rawcells = (isset($col['cells']) && is_array($col['cells'])) ? $col['cells'] : [];
            foreach ($rawcells as $row => $cell) {
                $cell = is_array($cell) ? $cell : [];
                $cells[] = [
                    'text' => (string)($cell['text'] ?? ''),
                    'image' => '',
                    'imageurl' => (string)($cell['imageUrl'] ?? ''),
                    'imageposition' => (($cell['imagePosition'] ?? 'above') === 'left') ? 'left' : 'above',
                    'imageleftsize' => in_array(($cell['imageLeftSize'] ?? 'small'), ['medium', 'large'], true)
                        ? $cell['imageLeftSize'] : 'small',
                    'imagealign' => (($cell['imageAlign'] ?? 'center') === 'left') ? 'left' : 'center',
                    'alt' => (string)($cell['alt'] ?? ''),
                ];
                // H5P failipilt (image objekt path-väljaga) -> viide hilisemaks väljavõtmiseks.
                if (isset($cell['image']) && is_array($cell['image']) && !empty($cell['image']['path'])) {
                    $imagerefs[] = ['col' => $c, 'row' => (int)$row, 'path' => (string)$cell['image']['path']];
                }
            }
            $columns[] = ['title' => (string)($col['title'] ?? ''), 'cells' => $cells];
        }

        // Vastusevõti: iga tulba (v.a esimene) lahtri correctToColumn<eelmine> väljast.
        $answerkey = [];
        for ($c = 1; $c < $numcolumns; $c++) {
            $field = 'correctToColumn' . self::ORDINALS[$c - 1];
            $col = isset($h5p[self::COLUMN_NAMES[$c]]) && is_array($h5p[self::COLUMN_NAMES[$c]])
                ? $h5p[self::COLUMN_NAMES[$c]] : [];
            $rawcells = (isset($col['cells']) && is_array($col['cells'])) ? $col['cells'] : [];
            foreach ($rawcells as $row => $cell) {
                if (!is_array($cell) || !isset($cell[$field])) {
                    continue;
                }
                foreach (self::parse_indices($cell[$field]) as $onebased) {
                    $answerkey[] = [
                        'from' => ['col' => $c, 'row' => (int)$row],
                        'to' => ['col' => $c - 1, 'row' => $onebased - 1],
                    ];
                }
            }
        }

        return [$numcolumns, ['columns' => $columns, 'answerkey' => $answerkey], $imagerefs];
    }

    /**
     * Eralda stringist 1-põhised indeksid (nt "[1,3]", "1 3", "2").
     *
     * @param mixed $value
     * @return int[]
     */
    public static function parse_indices($value) {
        $out = [];
        if (!is_string($value) && !is_numeric($value)) {
            return $out;
        }
        if (preg_match_all('/\d+/', (string)$value, $m)) {
            foreach ($m[0] as $n) {
                $n = (int)$n;
                if ($n > 0) {
                    $out[] = $n;
                }
            }
        }
        return $out;
    }

    /**
     * Impordi lubatud suurim maht baitides.
     *
     * Aluseks on saidi säte moodlecourse | maxbytes (kursuse vaikimisi üleslaadimise
     * piirang). Väärtus 0 tähendab Moodle'is saidi üleslaadimispiirangut; tulemus ei
     * ületa kunagi saidi ega PHP üleslaadimispiirangut.
     *
     * @return int
     */
    public static function get_max_bytes() {
        global $CFG;
        return (int)get_max_upload_file_size((int)($CFG->maxbytes ?? 0), (int)get_config('moodlecourse', 'maxbytes'));
    }

    /**
     * Normaliseeri paketisisene suhteline tee (ilma "..", "." ja tühjade osadeta).
     *
     * @param string $path
     * @return string|null normaliseeritud tee või null, kui tee pole lubatud
     */
    public static function normalise_package_path($path) {
        if (!is_string($path) || $path === '' || strpos($path, "\0") !== false) {
            return null;
        }
        $parts = explode('/', str_replace('\\', '/', ltrim($path, '/\\')));
        foreach ($parts as $part) {
            if ($part === '' || $part === '.' || $part === '..') {
                return null;
            }
        }
        return implode('/', $parts);
    }

    /**
     * Impordi üleslaaditud .h5p Moodle'i mustandifailist.
     *
     * Arhiivi ei pakita tervikuna lahti: kõigepealt kontrollitakse faili suurust ja
     * arhiivi sisukorda, seejärel pakitakse lahti ainult h5p.json, content/content.json
     * ja lahtritele viidatud pildid. Piirangud:
     * - üleslaaditud faili suurus ja lahtipakitavate failide kogumaht ei ületa
     *   get_max_bytes() väärtust;
     * - content-kaustas (v.a content.json) on kuni lahtrite arv + 1 faili
     *   (ülesande enda pilt ja iga lahtri pilt).
     *
     * @param stored_file $file üleslaaditud .h5p
     * @param int $cellimagesdraftid lahtripiltide mustandiala itemid
     * @param context $context vormi kontekst
     * @return array [int $numcolumns, string $contentjson, string $instructions]
     * @throws moodle_exception kui fail pole sobiv H5P.ColumnConnector või ületab piiranguid
     */
    public static function import_stored_file($file, $cellimagesdraftid, $context) {
        global $CFG, $USER;
        require_once($CFG->libdir . '/filelib.php');

        $maxbytes = self::get_max_bytes();
        if ($file->get_filesize() > $maxbytes) {
            throw new moodle_exception('h5pimporttoolarge', 'qtype_columnconnector', '', display_size($maxbytes));
        }

        $packer = get_file_packer('application/zip');
        $tmpdir = make_request_directory();
        $zippath = $tmpdir . '/package.h5p';
        $tmpdir .= '/extract';
        if (!$file->copy_content_to($zippath) || !mkdir($tmpdir)) {
            throw new moodle_exception('h5pimporterror', 'qtype_columnconnector');
        }

        // Arhiivi sisukord: failinimi => lahtipakitud suurus.
        $list = $packer->list_files($zippath);
        if (!is_array($list)) {
            throw new moodle_exception('h5pimporterror', 'qtype_columnconnector');
        }
        $entries = [];
        foreach ($list as $info) {
            if (empty($info->is_directory)) {
                $entries[ltrim($info->pathname, '/')] = (int)$info->size;
            }
        }
        if (!isset($entries['h5p.json'], $entries['content/content.json'])) {
            throw new moodle_exception('h5pimporterror', 'qtype_columnconnector');
        }
        $unpacked = $entries['h5p.json'] + $entries['content/content.json'];
        if ($unpacked > $maxbytes) {
            throw new moodle_exception('h5pimportunpackedtoolarge', 'qtype_columnconnector', '', display_size($maxbytes));
        }
        self::extract_files($packer, $zippath, $tmpdir, ['h5p.json', 'content/content.json'], $maxbytes);

        $contentpath = $tmpdir . '/content/content.json';
        $h5pjsonpath = $tmpdir . '/h5p.json';
        if (!file_exists($contentpath) || !file_exists($h5pjsonpath)) {
            throw new moodle_exception('h5pimporterror', 'qtype_columnconnector');
        }

        $h5pjson = json_decode(file_get_contents($h5pjsonpath), true);
        if (!is_array($h5pjson) || ($h5pjson['mainLibrary'] ?? '') !== 'H5P.ColumnConnector') {
            throw new moodle_exception('h5pimportwrongtype', 'qtype_columnconnector');
        }

        $content = json_decode(file_get_contents($contentpath), true);
        if (!is_array($content)) {
            throw new moodle_exception('h5pimporterror', 'qtype_columnconnector');
        }

        [$numcolumns, $model, $imagerefs] = self::convert($content);

        // Failide arvu piirang: ülesande enda pilt + iga lahtri pilt.
        $numcells = 0;
        foreach ($model['columns'] as $column) {
            $numcells += count($column['cells']);
        }
        $maxfiles = $numcells + 1;
        $contentfiles = 0;
        foreach (array_keys($entries) as $name) {
            if (strpos($name, 'content/') === 0 && $name !== 'content/content.json') {
                $contentfiles++;
            }
        }
        if ($contentfiles > $maxfiles) {
            throw new moodle_exception('h5pimporttoomanyfiles', 'qtype_columnconnector', '', $maxfiles);
        }

        // Paki lahti ainult viidatud pildid, kui nende kogumaht mahub piirangusse.
        $onlyfiles = [];
        foreach ($imagerefs as $ref) {
            $path = self::normalise_package_path($ref['path']);
            if ($path === null || !isset($entries['content/' . $path]) || isset($onlyfiles['content/' . $path])) {
                continue;
            }
            $onlyfiles['content/' . $path] = true;
            $unpacked += $entries['content/' . $path];
        }
        if ($unpacked > $maxbytes) {
            throw new moodle_exception('h5pimportunpackedtoolarge', 'qtype_columnconnector', '', display_size($maxbytes));
        }
        if ($onlyfiles) {
            self::extract_files($packer, $zippath, $tmpdir, array_keys($onlyfiles), $maxbytes);
        }

        // Juhis õppijale: 2.0 tipptasandil, 1.3 behaviour-grupis. See tuuakse
        // Moodle'i küsimuse tekstiks (vt vormi definition_after_data).
        $instructions = '';
        if (isset($content['instructions']) && is_string($content['instructions'])) {
            $instructions = $content['instructions'];
        } else if (
            isset($content['behaviour']['instructions'])
                && is_string($content['behaviour']['instructions'])
        ) {
            $instructions = $content['behaviour']['instructions'];
        }

        // Salvesta failipildid lahtripiltide mustandialasse ja sea cell.image.
        if ($imagerefs) {
            $fs = get_file_storage();
            $usercontext = context_user::instance($USER->id);
            foreach ($imagerefs as $ref) {
                $src = self::resolve_package_file($tmpdir . '/content', $ref['path']);
                if ($src === null) {
                    continue;
                }
                $filename = clean_param(basename($src), PARAM_FILE);
                if ($filename === '' || !file_mimetype_in_typegroup(mimeinfo('type', $filename), 'web_image')) {
                    continue;
                }
                // Väldi nimekonflikte mustandialas.
                if ($fs->file_exists($usercontext->id, 'user', 'draft', $cellimagesdraftid, '/', $filename)) {
                    $filename = uniqid() . '_' . $filename;
                }
                $filerecord = [
                    'contextid' => $usercontext->id,
                    'component' => 'user',
                    'filearea' => 'draft',
                    'itemid' => $cellimagesdraftid,
                    'filepath' => '/',
                    'filename' => $filename,
                ];
                $fs->create_file_from_pathname($filerecord, $src);
                $model['columns'][$ref['col']]['cells'][$ref['row']]['image'] = $filename;
            }
        }

        return [$numcolumns, json_encode($model), $instructions];
    }

    /**
     * Paki arhiivist lahti ainult antud failid ja kontrolli tegelikku kogumahtu.
     *
     * Arhiivi sisukorras olevad suurused võivad olla valed, seetõttu kontrollitakse
     * pärast lahtipakkimist kogu ajutise kausta tegelikku mahtu.
     *
     * @param file_packer $packer
     * @param string $zippath arhiivi tee
     * @param string $targetdir sihtkaust
     * @param array $onlyfiles lahtipakitavad failid
     * @param int $maxbytes lubatud kogumaht
     * @throws moodle_exception kui lahtipakkimine ebaõnnestub või maht ületab piirangu
     */
    protected static function extract_files($packer, $zippath, $targetdir, array $onlyfiles, $maxbytes) {
        if ($packer->extract_to_pathname($zippath, $targetdir, $onlyfiles, null, true) !== true) {
            throw new moodle_exception('h5pimporterror', 'qtype_columnconnector');
        }
        $total = 0;
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($targetdir, FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $item) {
            if ($item->isFile()) {
                $total += $item->getSize();
            }
        }
        if ($total > $maxbytes) {
            throw new moodle_exception('h5pimportunpackedtoolarge', 'qtype_columnconnector', '', display_size($maxbytes));
        }
    }

    /**
     * Leia H5P paketis viidatud fail, lubades ainult faile paketi content-kausta sees.
     *
     * Kaitseb teede eest nagu "../../config.php" ja absoluutsete teede eest.
     *
     * @param string $basedir lahtipakitud paketi content-kaust
     * @param string $relpath content.json-is olev suhteline tee
     * @return string|null faili absoluutne tee või null, kui fail pole lubatud
     */
    public static function resolve_package_file($basedir, $relpath) {
        $base = realpath($basedir);
        if ($base === false || !is_string($relpath) || $relpath === '' || strpos($relpath, "\0") !== false) {
            return null;
        }
        $candidate = realpath($base . DIRECTORY_SEPARATOR . ltrim($relpath, '/\\'));
        if ($candidate === false || !is_file($candidate) || !is_readable($candidate)) {
            return null;
        }
        if (strpos($candidate, $base . DIRECTORY_SEPARATOR) !== 0) {
            return null;
        }
        return $candidate;
    }
}
