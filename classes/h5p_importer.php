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

defined('MOODLE_INTERNAL') || die();

/**
 * Teisendab H5P.ColumnConnector sisu selle küsimusetüübi mudeliks.
 */
class qtype_columnconnector_h5p_importer {

    /** @var string[] tulpade rühmanimed H5P-s (1.–7. tulp). */
    const COLUMN_NAMES = [
        'columnOne', 'columnTwo', 'columnThree', 'columnFour',
        'columnFive', 'columnSix', 'columnSeven',
    ];

    /** @var string[] järgarvnimed (0-põhine indeks -> nimi). */
    const ORDINALS = ['One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven'];

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
     * Impordi üleslaaditud .h5p Moodle'i mustandifailist.
     * Pakib .h5p lahti, teisendab sisu, salvestab failipildid lahtripiltide
     * mustandialasse ja tagastab valmis mudeli.
     *
     * @param stored_file $file üleslaaditud .h5p
     * @param int $cellimagesdraftid lahtripiltide mustandiala itemid
     * @param context $context vormi kontekst
     * @return array [int $numcolumns, string $contentjson, string $instructions]
     * @throws moodle_exception kui fail pole sobiv H5P.ColumnConnector
     */
    public static function import_stored_file($file, $cellimagesdraftid, $context) {
        global $USER;

        $packer = get_file_packer('application/zip');
        $tmpdir = make_request_directory();
        if (!$file->extract_to_pathname($packer, $tmpdir)) {
            throw new moodle_exception('h5pimporterror', 'qtype_columnconnector');
        }

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

        list($numcolumns, $model, $imagerefs) = self::convert($content);

        // „Juhis õppijale": 2.0 tipptasandil, 1.3 behaviour-grupis. See tuuakse
        // Moodle'i küsimuse tekstiks (vt vormi definition_after_data).
        $instructions = '';
        if (isset($content['instructions']) && is_string($content['instructions'])) {
            $instructions = $content['instructions'];
        } else if (isset($content['behaviour']['instructions'])
                && is_string($content['behaviour']['instructions'])) {
            $instructions = $content['behaviour']['instructions'];
        }

        // Salvesta failipildid lahtripiltide mustandialasse ja sea cell.image.
        if ($imagerefs) {
            $fs = get_file_storage();
            $usercontext = context_user::instance($USER->id);
            foreach ($imagerefs as $ref) {
                $src = $tmpdir . '/content/' . ltrim($ref['path'], '/');
                if (!is_readable($src)) {
                    continue;
                }
                $filename = clean_param(basename($ref['path']), PARAM_FILE);
                if ($filename === '') {
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
}
