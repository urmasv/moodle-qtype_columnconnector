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
 * columnconnector küsimusetüübi klass: salvestab ja laadib küsimuse andmeid.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/type/columnconnector/question.php');

/**
 * columnconnector küsimusetüübi definitsioon.
 *
 * Andmed hoitakse tabelis qtype_columnconnector. Salvestus ja laadimine on
 * teostatud selgesõnaliselt (mitte extra_question_fields kaudu), et tagada
 * tõrkekindel käitumine ka siis, kui kirje puudub.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_columnconnector extends question_type {
    /** @var array vaikeväärtused seadetele. */
    public const DEFAULTS = [
        'numcolumns' => 2,
        'layoutmode' => 'columns',
        'linestyle' => 'curved',
        'showclear' => 1,
        'showmissing' => 0,
        'correctpoints' => 1,
        'incorrectpoints' => -1,
        'missingpoints' => 0,
    ];

    /** @var int tulpade minimaalne arv. */
    public const MIN_COLUMNS = 2;

    /** @var int tulpade maksimaalne arv. */
    public const MAX_COLUMNS = 7;

    /**
     * Laadi küsimuse valikud andmebaasist objekti $question->options külge.
     *
     * @param object $question
     * @return bool
     */
    public function get_question_options($question) {
        global $DB;

        parent::get_question_options($question);

        $options = $DB->get_record('qtype_columnconnector', ['questionid' => $question->id]);
        if (!$options) {
            $options = (object)(self::DEFAULTS + [
                'content' => json_encode(['columns' => [], 'answerkey' => []]),
            ]);
        }

        foreach (array_merge(array_keys(self::DEFAULTS), ['content']) as $field) {
            $question->options->$field = $options->$field;
        }

        return true;
    }

    /**
     * Salvesta küsimuse valikud vormist (või impordist) andmebaasi.
     *
     * @param object $question vormiandmed
     * @return object|null
     */
    public function save_question_options($question) {
        global $DB;

        $content = $this->build_content($question);

        // Sama valideerimine kehtib vormi, XML-impordi ja muu salvestamise korral.
        // Vea korral katkestab Moodle salvestamise; XML-impordil kuvatakse teade ja
        // import tühistatakse.
        $errors = self::validate_question_data(
            $question->numcolumns ?? self::DEFAULTS['numcolumns'],
            $question->correctpoints ?? self::DEFAULTS['correctpoints'],
            $question->incorrectpoints ?? self::DEFAULTS['incorrectpoints'],
            $question->missingpoints ?? self::DEFAULTS['missingpoints'],
            $content
        );
        if ($errors) {
            $result = new stdClass();
            $result->error = get_string('errorinvalidquestion', 'qtype_columnconnector', (object)[
                'name' => format_string($question->name ?? ''),
                'errors' => implode(' ', array_unique($errors)),
            ]);
            return $result;
        }

        $record = new stdClass();
        $record->questionid = $question->id;
        $record->numcolumns = self::clamp_numcolumns($question->numcolumns ?? self::DEFAULTS['numcolumns']);
        $record->layoutmode = ($question->layoutmode ?? 'columns') === 'rows' ? 'rows' : 'columns';
        $record->linestyle = ($question->linestyle ?? 'curved') === 'straight' ? 'straight' : 'curved';
        $record->showclear = empty($question->showclear) ? 0 : 1;
        $record->showmissing = empty($question->showmissing) ? 0 : 1;
        $record->correctpoints = (float)($question->correctpoints ?? self::DEFAULTS['correctpoints']);
        $record->incorrectpoints = (float)($question->incorrectpoints ?? self::DEFAULTS['incorrectpoints']);
        $record->missingpoints = (float)($question->missingpoints ?? self::DEFAULTS['missingpoints']);
        $record->content = json_encode($content);

        $existing = $DB->get_record('qtype_columnconnector', ['questionid' => $question->id]);
        if ($existing) {
            $record->id = $existing->id;
            $DB->update_record('qtype_columnconnector', $record);
        } else {
            $DB->insert_record('qtype_columnconnector', $record);
        }

        // Salvesta lahtrite piltide mustandiala failid küsimuse failialasse.
        if (isset($question->cellimages) && !empty($question->context)) {
            file_save_draft_area_files(
                (int)$question->cellimages,
                $question->context->id,
                'qtype_columnconnector',
                'cellimages',
                (int)$question->id,
                self::cellimages_options()
            );
        }

        $this->save_hints($question);

        return null;
    }

    /**
     * Kontrolli küsimuse hindamisseadistust ja sisu.
     *
     * Ühine kontroll vormile (edit_columnconnector_form::validation()) ja
     * salvestamisele (save_question_options(), sh XML-import):
     * - punktid õige ühenduse eest > 0, vale ja puuduva ühenduse eest <= 0;
     * - tulpade arv 2–7 ja igas tulbas vähemalt üks lahter;
     * - puhastatud vastusevõtmes vähemalt üks õige ühendus.
     *
     * @param mixed $numcolumns
     * @param mixed $correctpoints
     * @param mixed $incorrectpoints
     * @param mixed $missingpoints
     * @param array $content puhastatud sisu (build_content() tulemus)
     * @return array vormivälja nimi => veateade; tühi, kui vigu pole
     */
    public static function validate_question_data($numcolumns, $correctpoints, $incorrectpoints, $missingpoints, array $content) {
        $errors = [];
        if (!is_numeric($correctpoints) || (float)$correctpoints <= 0) {
            $errors['correctpoints'] = get_string('errorcorrectpoints', 'qtype_columnconnector');
        }
        if (!is_numeric($incorrectpoints) || (float)$incorrectpoints > 0) {
            $errors['incorrectpoints'] = get_string('errorincorrectpoints', 'qtype_columnconnector');
        }
        if (!is_numeric($missingpoints) || (float)$missingpoints > 0) {
            $errors['missingpoints'] = get_string('errormissingpoints', 'qtype_columnconnector');
        }

        $n = is_numeric($numcolumns) ? (int)$numcolumns : 0;
        if ($n < self::MIN_COLUMNS || $n > self::MAX_COLUMNS) {
            $errors['numcolumns'] = get_string('errornumcolumns', 'qtype_columnconnector');
            return $errors;
        }
        $columns = $content['columns'] ?? [];
        for ($i = 0; $i < $n; $i++) {
            if (empty($columns[$i]['cells'])) {
                $errors['numcolumns'] = get_string('erroremptycolumn', 'qtype_columnconnector', $i + 1);
                return $errors;
            }
        }
        if (empty($content['answerkey'])) {
            $errors['numcolumns'] = get_string('errornoanswerkey', 'qtype_columnconnector');
        }
        return $errors;
    }

    /**
     * Lahtripiltide failiala sätted.
     *
     * @return array
     */
    public static function cellimages_options() {
        return ['subdirs' => 0, 'maxfiles' => -1, 'accepted_types' => ['web_image']];
    }

    /**
     * Piira tulpade arv lubatud vahemikku.
     *
     * @param mixed $value
     * @return int
     */
    public static function clamp_numcolumns($value) {
        return max(self::MIN_COLUMNS, min(self::MAX_COLUMNS, (int)$value));
    }

    /**
     * Ehita puhastatud sisu-mudel.
     *
     * Eelistab vormi välja 'contentjson'; impordil kasutab välja 'content'.
     * Lahtri HTML puhastatakse, pildi-URL peab olema http(s) aadress ning
     * vastusevõtmest jäetakse välja ühendused, mis viitavad puuduvale lahtrile.
     *
     * @param object $question
     * @return array ['columns' => .., 'answerkey' => ..]
     */
    public function build_content($question) {
        $source = null;
        if (isset($question->contentjson) && trim((string)$question->contentjson) !== '') {
            $source = json_decode($question->contentjson, true);
        } else if (isset($question->content) && !empty($question->content)) {
            $source = is_array($question->content) ? $question->content : json_decode($question->content, true);
        }
        if (!is_array($source)) {
            $source = [];
        }

        $numcolumns = self::clamp_numcolumns($question->numcolumns ?? self::DEFAULTS['numcolumns']);
        $incolumns = (isset($source['columns']) && is_array($source['columns'])) ? array_values($source['columns']) : [];

        $columns = [];
        for ($i = 0; $i < $numcolumns; $i++) {
            $col = (isset($incolumns[$i]) && is_array($incolumns[$i])) ? $incolumns[$i] : [];
            $cells = [];
            $incells = (isset($col['cells']) && is_array($col['cells'])) ? array_values($col['cells']) : [];
            foreach ($incells as $cell) {
                $cells[] = self::clean_cell(is_array($cell) ? $cell : []);
            }
            $columns[] = [
                'title' => clean_param((string)($col['title'] ?? ''), PARAM_TEXT),
                'cells' => $cells,
            ];
        }
        $columns = self::assign_cell_ids($columns);

        $answerkey = [];
        $seen = [];
        $inkey = (isset($source['answerkey']) && is_array($source['answerkey'])) ? $source['answerkey'] : [];
        foreach ($inkey as $item) {
            $conn = qtype_columnconnector_question::normalize_connection($item);
            if ($conn === null) {
                continue;
            }
            if (
                !isset($columns[$conn['from']['col']]['cells'][$conn['from']['row']])
                    || !isset($columns[$conn['to']['col']]['cells'][$conn['to']['row']])
            ) {
                continue;
            }
            $key = qtype_columnconnector_question::connection_key($conn);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $answerkey[] = $conn;
        }

        return ['columns' => $columns, 'answerkey' => $answerkey];
    }

    /**
     * Puhasta ühe lahtri andmed.
     *
     * @param array $cell
     * @return array
     */
    public static function clean_cell(array $cell) {
        $size = $cell['imageleftsize'] ?? 'small';
        return [
            'id' => self::clean_cell_id($cell['id'] ?? ''),
            'text' => clean_text((string)($cell['text'] ?? ''), FORMAT_HTML),
            'image' => clean_param((string)($cell['image'] ?? ''), PARAM_FILE),
            'imageurl' => self::clean_image_url((string)($cell['imageurl'] ?? '')),
            'imageposition' => ($cell['imageposition'] ?? 'above') === 'left' ? 'left' : 'above',
            'imageleftsize' => in_array($size, ['medium', 'large'], true) ? $size : 'small',
            'imagealign' => (($cell['imagealign'] ?? 'center') === 'left') ? 'left' : 'center',
            'alt' => clean_param((string)($cell['alt'] ?? ''), PARAM_TEXT),
        ];
    }

    /**
     * Puhasta lahtri id (tähed, numbrid, '_' ja '-', kuni 32 märki).
     *
     * @param mixed $id
     * @return string
     */
    public static function clean_cell_id($id) {
        $id = clean_param(is_scalar($id) ? (string)$id : '', PARAM_ALPHANUMEXT);
        return strlen($id) <= 32 ? $id : '';
    }

    /** @var string uue lahtri juhusliku id eesliide. */
    public const CELL_ID_NEW = 'n';

    /** @var string vanast (id-deta) sisust tuletatud id eesliide. */
    public const CELL_ID_LEGACY = 'h';

    /**
     * Anna igale lahtrile tulba piires unikaalne püsiv id.
     *
     * Lahtri id jääb samaks, kui lahtrit toimetatakse või teisaldatakse, ning
     * võimaldab ümberhindamisel tuvastada lahtrite järjekorra muutumise.
     *
     * Režiimid id-ta lahtrite jaoks:
     * - 'new' (salvestamine vormist, XML- ja H5P-import): lahter saab uue juhusliku id,
     *   sest tegemist on uue või asendatud lahtriga;
     * - 'legacy' (uuendusskript, vanade varunduste taastamine): id tuletatakse lahtri
     *   sisust, nii et sama sisu annab igas küsimuseversioonis sama id ja eri sisu
     *   erineva id. Nii on ka eraldi teisendatud vanade versioonide vahel tuvastatav,
     *   kas lahtrite järjekord on muutunud.
     *
     * @param array $columns tulbad
     * @param string $mode 'new' või 'legacy'
     * @return array tulbad, kus igal lahtril on id
     */
    public static function assign_cell_ids(array $columns, $mode = 'new') {
        foreach ($columns as $c => $column) {
            $cells = (isset($column['cells']) && is_array($column['cells'])) ? $column['cells'] : [];
            $used = [];
            foreach ($cells as $r => $cell) {
                $cell = is_array($cell) ? $cell : [];
                $id = self::clean_cell_id($cell['id'] ?? '');
                if ($id !== '' && !isset($used[$id])) {
                    $used[$id] = true;
                } else {
                    $id = '';
                }
                // Id on alati lahtri esimene väli, et sama sisu annaks sama JSON-i.
                unset($cell['id']);
                $cells[$r] = ['id' => $id] + $cell;
            }
            foreach ($cells as $r => $cell) {
                if ($cell['id'] !== '') {
                    continue;
                }
                if ($mode === 'legacy') {
                    $base = self::CELL_ID_LEGACY . substr(sha1(self::cell_content_signature($cell)), 0, 16);
                    $id = $base;
                    for ($n = 2; isset($used[$id]); $n++) {
                        // Sama sisuga lahtrid samas tulbas: järjekorranumber sisu esinemise järgi.
                        $id = $base . '-' . $n;
                    }
                } else {
                    do {
                        $id = self::CELL_ID_NEW . strtolower(random_string(12));
                    } while (isset($used[$id]));
                }
                $used[$id] = true;
                $cells[$r]['id'] = $id;
            }
            $columns[$c]['cells'] = $cells;
        }
        return $columns;
    }

    /**
     * Lahtri sisu allkiri (tekst, pilt, pildi-URL, alt-tekst).
     *
     * @param array $cell
     * @return string
     */
    public static function cell_content_signature(array $cell) {
        return json_encode([
            (string)($cell['text'] ?? ''),
            (string)($cell['image'] ?? ''),
            (string)($cell['imageurl'] ?? ''),
            (string)($cell['alt'] ?? ''),
        ]);
    }

    /**
     * Kas id on usaldusväärne lahtri identiteedi tunnus.
     *
     * Versiooni 1.2.1 varasem väljalase andis id-ta lahtritele asukohapõhised id-d
     * (c<tulp>r<rida>). Need ei eristanud eri versioonides samal kohal olevaid eri
     * lahtreid, seetõttu neid identiteedina ei kasutata.
     *
     * @param string $id
     * @return bool
     */
    public static function is_reliable_cell_id($id) {
        return $id !== '' && !preg_match('/^c\d+r\d+$/', $id);
    }

    /**
     * Eemalda sisu-JSON-ist lahtrite id-d (taastamise identiteedi võrdluseks).
     *
     * @param string|null $contentjson
     * @return string|null
     */
    public static function strip_cell_ids($contentjson) {
        $content = json_decode((string)$contentjson, true);
        if (!is_array($content) || !isset($content['columns']) || !is_array($content['columns'])) {
            return $contentjson;
        }
        foreach ($content['columns'] as $c => $column) {
            if (!isset($column['cells']) || !is_array($column['cells'])) {
                continue;
            }
            foreach ($column['cells'] as $r => $cell) {
                if (is_array($cell)) {
                    unset($content['columns'][$c]['cells'][$r]['id']);
                }
            }
        }
        return json_encode($content);
    }

    /**
     * Lisa vanemale (id-deta) sisu-JSON-ile lahtrite id-d sisu põhjal.
     *
     * Puuduvad, vigase kujuga ja samas tulbas korduvad id-d asendatakse sisupõhise id-ga
     * (korduva id esimene esinemine säilib). Kasutatakse uuendusskriptis, taastamisel ja taastamise identiteedi võrdluses,
     * et kõik kolm annaksid sama tulemuse. Kui midagi ei muutu, tagastatakse
     * algne string muutmata kujul.
     *
     * @param string|null $contentjson
     * @return string|null
     */
    public static function add_missing_cell_ids($contentjson) {
        $content = json_decode((string)$contentjson, true);
        if (!is_array($content) || !isset($content['columns']) || !is_array($content['columns'])) {
            return $contentjson;
        }
        // Teisendus on vajalik, kui mõnel lahtril puudub id, see pole korrektse kujuga
        // või kordub samas tulbas (nt vigases varukoopias).
        $missing = false;
        foreach ($content['columns'] as $column) {
            $seen = [];
            foreach ((isset($column['cells']) && is_array($column['cells'])) ? $column['cells'] : [] as $cell) {
                $raw = is_array($cell) && is_scalar($cell['id'] ?? null) ? (string)$cell['id'] : '';
                $id = self::clean_cell_id($raw);
                if ($id === '' || $id !== $raw || isset($seen[$id])) {
                    $missing = true;
                    break 2;
                }
                $seen[$id] = true;
            }
        }
        if (!$missing) {
            return $contentjson;
        }
        $content['columns'] = self::assign_cell_ids($content['columns'], 'legacy');
        return json_encode($content);
    }

    /**
     * Luba ainult absoluutsed http(s) pildiaadressid.
     *
     * @param string $url
     * @return string puhastatud URL või tühi string
     */
    public static function clean_image_url($url) {
        $url = trim($url);
        if ($url === '' || !preg_match('~^https?://~i', $url)) {
            return '';
        }
        return clean_param($url, PARAM_URL);
    }

    /**
     * Täida küsimuse eksemplar andmebaasist laetud andmetega.
     *
     * @param question_definition $question
     * @param object $questiondata
     */
    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $options = $questiondata->options;
        $question->numcolumns = (int)$options->numcolumns;
        $question->layoutmode = $options->layoutmode;
        $question->linestyle = $options->linestyle;
        $question->showclear = (int)$options->showclear;
        $question->showmissing = (int)($options->showmissing ?? 0);
        $question->correctpoints = (float)$options->correctpoints;
        $question->incorrectpoints = (float)$options->incorrectpoints;
        $question->missingpoints = (float)$options->missingpoints;

        $content = json_decode($options->content ?? '', true);
        if (!is_array($content)) {
            $content = ['columns' => [], 'answerkey' => []];
        }
        $question->columns = $content['columns'] ?? [];
        $question->answerkey = $content['answerkey'] ?? [];
    }

    /**
     * Juhusliku pakkumise eeldatav skoor.
     *
     * Hinnangut ei saa põhjendatult arvutada: tulemus sõltub sellest, mitu ühendust
     * õppija teeb (0 kuni kõik naaberlahtrite paarid), ja ühenduste arvule pole
     * loomulikku juhuslikku jaotust. Moodle'i API lubab sellisel juhul tagastada null.
     *
     * @param object $questiondata
     * @return null
     */
    public function get_random_guess_score($questiondata) {
        return null;
    }

    /**
     * Võimalikud vastused statistika jaoks.
     *
     * Iga naabertulpade lahtripaar on eraldi osa kahe vastuseklassiga:
     * "connected" (ühendus tehti) ja "notconnected" (ühendust ei tehtud).
     *
     * @param object $questiondata
     * @return array osa id => [klassi id => question_possible_response]
     */
    public function get_possible_responses($questiondata) {
        /** @var qtype_columnconnector_question $question */
        $question = $this->make_question($questiondata);
        $parts = [];
        foreach ($question->get_response_pairs() as $key => $pair) {
            foreach (['connected', 'notconnected'] as $class) {
                [$label, $fraction] = $question->get_pair_class_details($pair['conn'], $class);
                $parts[$key][$class] = new question_possible_response($label, $fraction);
            }
            $parts[$key][null] = question_possible_response::no_response();
        }
        return $parts;
    }

    /**
     * Kustuta küsimus ja selle valikud.
     *
     * @param int $questionid
     * @param int $contextid
     */
    public function delete_question($questionid, $contextid) {
        global $DB;
        $DB->delete_records('qtype_columnconnector', ['questionid' => $questionid]);
        parent::delete_question($questionid, $contextid);
    }

    /**
     * Ekspordi küsimus Moodle XML-vormingusse.
     *
     * @param object $question
     * @param qformat_xml $format
     * @param mixed $extra
     * @return string
     */
    public function export_to_xml($question, qformat_xml $format, $extra = null) {
        $options = $question->options;
        $output = '';
        $output .= "    <numcolumns>" . (int)$options->numcolumns . "</numcolumns>\n";
        $output .= "    <layoutmode>" . $format->xml_escape($options->layoutmode) . "</layoutmode>\n";
        $output .= "    <linestyle>" . $format->xml_escape($options->linestyle) . "</linestyle>\n";
        $output .= "    <showclear>" . (int)$options->showclear . "</showclear>\n";
        $output .= "    <showmissing>" . (int)($options->showmissing ?? 0) . "</showmissing>\n";
        $output .= "    <correctpoints>" . (float)$options->correctpoints . "</correctpoints>\n";
        $output .= "    <incorrectpoints>" . (float)$options->incorrectpoints . "</incorrectpoints>\n";
        $output .= "    <missingpoints>" . (float)$options->missingpoints . "</missingpoints>\n";
        $output .= "    <content>" . $format->writetext($options->content) . "</content>\n";

        // Lahtrite pildid (base64), nagu tuuma qtype_ddimageortext puhul.
        $fs = get_file_storage();
        $files = $fs->get_area_files(
            $question->contextid,
            'qtype_columnconnector',
            'cellimages',
            $question->id,
            'filename',
            false
        );
        if ($files) {
            $output .= "    <cellimages>\n" . $format->write_files($files) . "    </cellimages>\n";
        }
        return $output;
    }

    /**
     * Impordi küsimus Moodle XML-vormingust.
     *
     * Vanemate versioonide eksporditud väli <instructions> tõstetakse küsimuse
     * tekstiks, kui küsimuse tekst on tühi.
     *
     * @param mixed $data
     * @param object $question
     * @param qformat_xml $format
     * @param mixed $extra
     * @return object|bool
     */
    public function import_from_xml($data, $question, qformat_xml $format, $extra = null) {
        if (!isset($data['@']['type']) || $data['@']['type'] != 'columnconnector') {
            return false;
        }

        $qo = $format->import_headers($data);
        $qo->qtype = 'columnconnector';

        // Toores väärtus: lubamatu tulpade arv lükatakse salvestamisel tagasi (validate_question_data()).
        $qo->numcolumns = $format->getpath($data, ['#', 'numcolumns', 0, '#'], 2);
        $qo->layoutmode = $format->getpath($data, ['#', 'layoutmode', 0, '#'], 'columns');
        $qo->linestyle = $format->getpath($data, ['#', 'linestyle', 0, '#'], 'curved');
        $qo->showclear = $format->getpath($data, ['#', 'showclear', 0, '#'], 1);
        $qo->showmissing = $format->getpath($data, ['#', 'showmissing', 0, '#'], 0);
        $qo->correctpoints = $format->getpath($data, ['#', 'correctpoints', 0, '#'], 1);
        $qo->incorrectpoints = $format->getpath($data, ['#', 'incorrectpoints', 0, '#'], -1);
        $qo->missingpoints = $format->getpath($data, ['#', 'missingpoints', 0, '#'], 0);
        $qo->content = $format->getpath($data, ['#', 'content', 0, '#', 'text', 0, '#'], '');

        // Varasemate versioonide juhis -> küsimuse tekst, kui see on tühi.
        $instructions = $format->getpath($data, ['#', 'instructions', 0, '#', 'text', 0, '#'], '');
        if (!html_is_blank($instructions) && html_is_blank($qo->questiontext)) {
            $qo->questiontext = $instructions;
            $qo->questiontextformat = (int)$format->getpath($data, ['#', 'instructionsformat', 0, '#'], FORMAT_HTML);
        }

        // Lahtrite pildid mustandialasse; save_question_options() salvestab need küsimuse alla.
        $filexml = $format->getpath($data, ['#', 'cellimages', 0, '#', 'file'], []);
        $draftitemid = $format->import_files_as_draft($filexml);
        if ($draftitemid) {
            $qo->cellimages = $draftitemid;
        }

        return $qo;
    }

    /**
     * Teisalda küsimuse failid uude konteksti.
     *
     * @param int $questionid
     * @param int $oldcontextid
     * @param int $newcontextid
     */
    public function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $fs = get_file_storage();
        $fs->move_area_files_to_new_context(
            $oldcontextid,
            $newcontextid,
            'qtype_columnconnector',
            'cellimages',
            $questionid
        );
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
    }

    /**
     * Kustuta küsimuse failid.
     *
     * @param int $questionid
     * @param int $contextid
     */
    protected function delete_files($questionid, $contextid) {
        parent::delete_files($questionid, $contextid);
        $fs = get_file_storage();
        $fs->delete_area_files($contextid, 'qtype_columnconnector', 'cellimages', $questionid);
        $this->delete_files_in_hints($questionid, $contextid);
    }
}
