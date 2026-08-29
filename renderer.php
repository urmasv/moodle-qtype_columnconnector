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
 * columnconnector küsimuse renderdaja.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * columnconnector renderdaja.
 */
class qtype_columnconnector_renderer extends qtype_renderer {

    /**
     * Renderda küsimuse sõnastus ja juhtelemendid.
     *
     * @param question_attempt $qa
     * @param question_display_options $options
     * @return string
     */
    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {
        global $PAGE;

        $question = $qa->get_question();
        $inputname = $qa->get_qt_field_name('answer');
        $response = $qa->get_last_qt_var('answer', '');
        $connections = qtype_columnconnector_question::decode_connections($response);

        // Kas paljastada õige/vale värvid ja puuduvad ühendused.
        $reveal = (bool)$options->correctness;
        $showkey = (bool)$options->rightanswer && !empty($question->showmissing);

        $out = '';
        $out .= html_writer::tag('div',
            $question->format_text($question->questiontext, $question->questiontextformat,
                $qa, 'question', 'questiontext', $question->id),
            ['class' => 'qtext']);

        $out .= html_writer::start_tag('div', ['class' => 'ablock']);

        // Peidetud väli õppija vastuse jaoks (JSON ühendused).
        $out .= html_writer::empty_tag('input', [
            'type' => 'hidden',
            'name' => $inputname,
            'id' => $inputname,
            'value' => s($response),
        ]);

        // Lava ja tulbad.
        $containerid = 'cc_' . $qa->get_slot();
        $out .= html_writer::start_tag('div', [
            'class' => 'cc-container cc-layout-' . $question->layoutmode
                . (($options->readonly || $reveal) ? ' cc-readonly' : ''),
            'id' => $containerid,
        ]);

        $out .= html_writer::start_tag('div', ['class' => 'cc-stage']);
        $out .= '<svg class="cc-lines" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"></svg>';
        $out .= html_writer::start_tag('div', [
            'class' => 'cc-columns',
            'data-column-count' => (int)$question->numcolumns,
        ]);

        $hastitle = false;
        for ($c = 0; $c < $question->numcolumns; $c++) {
            if (trim((string)($question->columns[$c]['title'] ?? '')) !== '') {
                $hastitle = true;
                break;
            }
        }

        for ($c = 0; $c < $question->numcolumns; $c++) {
            $column = $question->columns[$c] ?? ['title' => '', 'cells' => []];
            $out .= html_writer::start_tag('div', ['class' => 'cc-column', 'data-col' => $c]);

            if ($hastitle) {
                $title = (string)($column['title'] ?? '');
                $titleclass = 'cc-column-title' . ($title === '' ? ' cc-column-title-empty' : '');
                $out .= html_writer::tag('div', s($title), ['class' => $titleclass]);
            }

            $order = $question->get_display_order($c);
            if (empty($order)) {
                $out .= html_writer::tag('div', get_string('emptycolumn', 'qtype_columnconnector'),
                    ['class' => 'cc-empty-column']);
            }

            foreach ($order as $row) {
                $cell = $column['cells'][$row] ?? null;
                if ($cell === null) {
                    continue;
                }
                $out .= $this->render_cell($c, $row, $cell, $question->contextid, $question->id,
                    $qa->get_usage_id(), $qa->get_slot());
            }

            $out .= html_writer::end_tag('div');
        }

        $out .= html_writer::end_tag('div'); // cc-columns.
        $out .= html_writer::end_tag('div'); // cc-stage.

        // Nupp "Tühista ühendused".
        if (!empty($question->showclear) && empty($options->readonly)) {
            $out .= html_writer::start_tag('div', ['class' => 'cc-controls']);
            $out .= html_writer::tag('button',
                get_string('clearconnections', 'qtype_columnconnector'),
                ['type' => 'button', 'class' => 'btn btn-secondary cc-clear']);
            $out .= html_writer::end_tag('div');
        }

        // Ligipääsetavuse teadete ala.
        $out .= html_writer::tag('div', '', [
            'class' => 'cc-sr-status', 'aria-live' => 'polite', 'role' => 'status',
        ]);

        $out .= html_writer::end_tag('div'); // cc-container.
        $out .= html_writer::end_tag('div'); // ablock.

        // Konfiguratsioon JavaScripti jaoks.
        $config = [
            'containerId' => $containerid,
            'inputName' => $inputname,
            'layoutMode' => $question->layoutmode,
            'lineStyle' => $question->linestyle,
            'numColumns' => (int)$question->numcolumns,
            'readonly' => (bool)$options->readonly,
            'reveal' => $reveal,
            'showKey' => $showkey,
            'connections' => array_values($connections),
            'answerKey' => ($reveal || $showkey) ? array_values($question->answerkey) : [],
            'strings' => [
                'invalidTarget' => get_string('invalidtarget', 'qtype_columnconnector'),
                'duplicate' => get_string('duplicate', 'qtype_columnconnector'),
                'selected' => get_string('selectedcell', 'qtype_columnconnector'),
            ],
        ];

        $PAGE->requires->js_call_amd('qtype_columnconnector/player', 'init', [$config]);

        return $out;
    }

    /**
     * Renderda üks lahter nupuna.
     *
     * @param int $col
     * @param int $row
     * @param array $cell
     * @return string
     */
    protected function render_cell($col, $row, $cell, $contextid = null, $questionid = null,
            $qubaid = null, $slot = null) {
        $contentclass = 'cc-cell-content';
        $imagesize = in_array(($cell['imageleftsize'] ?? 'small'), ['medium', 'large'], true)
            ? $cell['imageleftsize'] : 'small';
        $contentclass .= ' cc-cell-size-' . $imagesize;
        if (($cell['imageposition'] ?? 'above') === 'left') {
            $contentclass .= ' cc-cell-content-image-left';
        } else {
            $align = (($cell['imagealign'] ?? 'center') === 'left') ? 'left' : 'center';
            $contentclass .= ' cc-cell-align-' . $align;
        }

        // Eelista üleslaaditud pilti; muidu kasuta välist URL-i.
        $src = '';
        $image = trim((string)($cell['image'] ?? ''));
        if ($image !== '' && $contextid && $questionid && $qubaid !== null && $slot !== null) {
            $src = moodle_url::make_pluginfile_url($contextid, 'qtype_columnconnector',
                'cellimages', "$qubaid/$slot/$questionid", '/', $image)->out();
        } else {
            $src = trim((string)($cell['imageurl'] ?? ''));
        }

        $inner = '';
        if ($src !== '') {
            $inner .= html_writer::empty_tag('img', [
                'class' => 'cc-cell-image',
                'src' => $src,
                'alt' => (string)($cell['alt'] ?? ''),
                'draggable' => 'false',
            ]);
        }
        $text = (string)($cell['text'] ?? '');
        if (trim($text) !== '') {
            $inner .= html_writer::tag('span',
                format_text($text, FORMAT_HTML, ['noclean' => true]),
                ['class' => 'cc-cell-text']);
        }

        $content = html_writer::tag('span', $inner, ['class' => $contentclass]);

        return html_writer::tag('button', $content, [
            'type' => 'button',
            'class' => 'cc-cell',
            'data-col' => $col,
            'data-row' => $row,
            'aria-pressed' => 'false',
        ]);
    }

    /**
     * Konkreetne tagasiside pole eraldi väljadena määratud;
     * tulemus kuvatakse standardse hindeteate kaudu.
     *
     * @param question_attempt $qa
     * @return string
     */
    public function specific_feedback(question_attempt $qa) {
        return '';
    }
}
