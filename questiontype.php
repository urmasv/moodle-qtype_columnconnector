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
 * teostatud selgesõnaliselt (mitte extra_question_fields kaudu), et vältida
 * baasklassi väljaloendipäringuid ja tagada tõrkekindel käitumine ka siis,
 * kui kirje puudub.
 */
class qtype_columnconnector extends question_type {

    /** @var array vaikeväärtused seadetele. */
    const DEFAULTS = [
        'numcolumns' => 2,
        'layoutmode' => 'columns',
        'linestyle' => 'curved',
        'showclear' => 1,
        'showmissing' => 0,
        'correctpoints' => 1,
        'incorrectpoints' => -1,
        'missingpoints' => 0,
        'instructions' => '',
        'instructionsformat' => FORMAT_HTML,
    ];

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

        foreach (['numcolumns', 'layoutmode', 'linestyle', 'showclear', 'showmissing',
                  'correctpoints', 'incorrectpoints', 'missingpoints',
                  'instructions', 'instructionsformat', 'content'] as $field) {
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

        $record = new stdClass();
        $record->questionid = $question->id;
        $record->numcolumns = max(2, min(7, (int)($question->numcolumns ?? 2)));
        $record->layoutmode = ($question->layoutmode ?? 'columns') === 'rows' ? 'rows' : 'columns';
        $record->linestyle = ($question->linestyle ?? 'curved') === 'straight' ? 'straight' : 'curved';
        $record->showclear = empty($question->showclear) ? 0 : 1;
        $record->showmissing = empty($question->showmissing) ? 0 : 1;
        $record->correctpoints = (float)($question->correctpoints ?? 1);
        $record->incorrectpoints = (float)($question->incorrectpoints ?? -1);
        $record->missingpoints = (float)($question->missingpoints ?? 0);

        // Juhis: vormist tuleb string, impordist samuti string.
        if (isset($question->instructions) && is_array($question->instructions)) {
            $record->instructions = $question->instructions['text'];
            $record->instructionsformat = $question->instructions['format'];
        } else {
            $record->instructions = (string)($question->instructions ?? '');
            $record->instructionsformat = (int)($question->instructionsformat ?? FORMAT_HTML);
        }

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
            file_save_draft_area_files((int)$question->cellimages, $question->context->id,
                'qtype_columnconnector', 'cellimages', (int)$question->id,
                ['subdirs' => 0, 'maxfiles' => -1, 'accepted_types' => ['web_image']]);
        }

        $this->save_hints($question);

        return null;
    }

    /**
     * Ehita kanooniline sisu-mudel. Eelistab vormi välja 'contentjson';
     * impordil kasutab välja 'content'.
     *
     * @param object $question
     * @return array ['columns'=>.., 'answerkey'=>..]
     */
    protected function build_content($question) {
        $source = null;
        if (isset($question->contentjson) && trim((string)$question->contentjson) !== '') {
            $source = json_decode($question->contentjson, true);
        } else if (isset($question->content) && trim((string)$question->content) !== '') {
            $source = is_array($question->content) ? $question->content
                : json_decode($question->content, true);
        }
        if (!is_array($source)) {
            $source = [];
        }

        $numcolumns = max(2, min(7, (int)($question->numcolumns ?? 2)));
        $incolumns = $source['columns'] ?? [];

        $columns = [];
        for ($i = 0; $i < $numcolumns; $i++) {
            $col = $incolumns[$i] ?? [];
            $cells = [];
            foreach (($col['cells'] ?? []) as $cell) {
                $cells[] = [
                    'text' => (string)($cell['text'] ?? ''),
                    'image' => clean_param((string)($cell['image'] ?? ''), PARAM_FILE),
                    'imageurl' => (string)($cell['imageurl'] ?? ''),
                    'imageposition' => ($cell['imageposition'] ?? 'above') === 'left' ? 'left' : 'above',
                    'imageleftsize' => in_array(($cell['imageleftsize'] ?? 'small'), ['medium', 'large'], true)
                        ? $cell['imageleftsize'] : 'small',
                    'imagealign' => (($cell['imagealign'] ?? 'center') === 'left') ? 'left' : 'center',
                    'alt' => (string)($cell['alt'] ?? ''),
                ];
            }
            $columns[] = ['title' => (string)($col['title'] ?? ''), 'cells' => $cells];
        }

        $answerkey = [];
        $seen = [];
        foreach (($source['answerkey'] ?? []) as $item) {
            $conn = qtype_columnconnector_question::normalize_connection($item);
            if ($conn === null) {
                continue;
            }
            if ($conn['from']['col'] >= $numcolumns || $conn['to']['col'] >= $numcolumns) {
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
        $question->instructions = $options->instructions;
        $question->instructionsformat = (int)$options->instructionsformat;

        $content = json_decode($options->content ?? '', true);
        if (!is_array($content)) {
            $content = ['columns' => [], 'answerkey' => []];
        }
        $question->columns = $content['columns'] ?? [];
        $question->answerkey = $content['answerkey'] ?? [];
    }

    /**
     * @param object $questiondata
     * @return float juhusliku pakkumise eeldatav skoor
     */
    public function get_random_guess_score($questiondata) {
        return 0;
    }

    /**
     * @param object $questiondata
     * @return array
     */
    public function get_possible_responses($questiondata) {
        return [];
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

    // ---------------------------------------------------------------------
    // Moodle XML import / export.
    // ---------------------------------------------------------------------

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
        $output .= "    <correctpoints>" . $options->correctpoints . "</correctpoints>\n";
        $output .= "    <incorrectpoints>" . $options->incorrectpoints . "</incorrectpoints>\n";
        $output .= "    <missingpoints>" . $options->missingpoints . "</missingpoints>\n";
        $output .= "    <instructionsformat>" . (int)$options->instructionsformat . "</instructionsformat>\n";
        $output .= "    <instructions>" . $format->writetext($options->instructions) . "</instructions>\n";
        $output .= "    <content>" . $format->writetext($options->content) . "</content>\n";
        return $output;
    }

    /**
     * Impordi küsimus Moodle XML-vormingust.
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

        $qo->numcolumns = $format->getpath($data, ['#', 'numcolumns', 0, '#'], 2);
        $qo->layoutmode = $format->getpath($data, ['#', 'layoutmode', 0, '#'], 'columns');
        $qo->linestyle = $format->getpath($data, ['#', 'linestyle', 0, '#'], 'curved');
        $qo->showclear = $format->getpath($data, ['#', 'showclear', 0, '#'], 1);
        $qo->showmissing = $format->getpath($data, ['#', 'showmissing', 0, '#'], 0);
        $qo->correctpoints = $format->getpath($data, ['#', 'correctpoints', 0, '#'], 1);
        $qo->incorrectpoints = $format->getpath($data, ['#', 'incorrectpoints', 0, '#'], -1);
        $qo->missingpoints = $format->getpath($data, ['#', 'missingpoints', 0, '#'], 0);
        $qo->instructionsformat = $format->getpath($data, ['#', 'instructionsformat', 0, '#'], FORMAT_HTML);
        $qo->instructions = $format->getpath($data, ['#', 'instructions', 0, '#', 'text', 0, '#'], '');
        $qo->content = $format->getpath($data, ['#', 'content', 0, '#', 'text', 0, '#'], '');

        return $qo;
    }

    /**
     * @param int $questionid
     * @param int $oldcontextid
     * @param int $newcontextid
     */
    public function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $fs = get_file_storage();
        $fs->move_area_files_to_new_context($oldcontextid, $newcontextid,
            'qtype_columnconnector', 'cellimages', $questionid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
    }

    /**
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
