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

/**
 * columnconnector renderdaja.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_columnconnector_renderer extends qtype_renderer {
    /**
     * Renderda küsimuse sõnastus ja juhtelemendid.
     *
     * Õigsus arvutatakse serveris: brauserisse saadetakse ainult õppija enda
     * ühenduste olek ning puuduvad ühendused ainult siis, kui õiget vastust
     * tohib näidata.
     *
     * @param question_attempt $qa
     * @param question_display_options $options
     * @return string
     */
    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {
        /** @var qtype_columnconnector_question $question */
        $question = $qa->get_question();
        $inputname = $qa->get_qt_field_name('answer');
        $response = (string)$qa->get_last_qt_var('answer', '');
        $connections = qtype_columnconnector_question::decode_connections($response);

        $showcorrectness = !empty($options->correctness);
        $showkey = !empty($options->rightanswer) && !empty($question->showmissing);
        $readonly = !empty($options->readonly);

        $jsconnections = $showcorrectness ? $question->get_connection_feedback($connections) : $connections;
        $missing = $showkey ? $question->get_missing_connections($connections) : [];

        $containerid = $qa->get_outer_question_div_unique_id() . '-cc';
        $helpid = $containerid . '-help';

        $out = html_writer::tag(
            'div',
            $question->format_questiontext($qa),
            ['class' => 'qtext']
        );

        $out .= html_writer::start_tag('div', ['class' => 'ablock']);

        // Peidetud väli õppija vastuse jaoks (JSON ühendused); html_writer escape'ib väärtuse.
        $out .= html_writer::empty_tag('input', [
            'type' => 'hidden',
            'name' => $inputname,
            'id' => $inputname,
            'value' => $response,
        ]);

        $out .= html_writer::tag(
            'p',
            get_string($question->layoutmode === 'rows' ? 'interactionhelprows' : 'interactionhelp', 'qtype_columnconnector'),
            ['id' => $helpid, 'class' => 'cc-sr-only']
        );

        // Mängija seaded antakse data-atribuudina, et vältida js_call_amd parameetrite mahupiirangut.
        $config = [
            'containerId' => $containerid,
            'inputName' => $inputname,
            'layoutMode' => $question->layoutmode,
            'lineStyle' => $question->linestyle,
            'numColumns' => (int)$question->numcolumns,
            'readonly' => $readonly,
            'showCorrectness' => $showcorrectness,
            'connections' => array_values($jsconnections),
            'missing' => array_values($missing),
            'strings' => [
                'invalidTarget' => get_string('invalidtarget', 'qtype_columnconnector'),
                'selected' => get_string('selectedcell', 'qtype_columnconnector'),
                'added' => get_string('connectionadded', 'qtype_columnconnector'),
                'removed' => get_string('connectionremoved', 'qtype_columnconnector'),
                'cleared' => get_string('connectionscleared', 'qtype_columnconnector'),
            ],
        ];
        $out .= html_writer::start_tag('div', [
            'class' => 'cc-container cc-layout-' . $question->layoutmode . ($readonly ? ' cc-readonly' : ''),
            'id' => $containerid,
            'aria-describedby' => $helpid,
            'data-config' => json_encode($config),
        ]);

        $out .= html_writer::start_tag('div', ['class' => 'cc-stage']);
        $out .= '<svg class="cc-lines" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"></svg>';
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
                $out .= html_writer::tag(
                    'div',
                    get_string('emptycolumn', 'qtype_columnconnector'),
                    ['class' => 'cc-empty-column']
                );
            }

            foreach ($order as $row) {
                $cell = $column['cells'][$row] ?? null;
                if ($cell === null) {
                    continue;
                }
                $out .= $this->render_cell($qa, $question, $c, $row, $cell, $readonly);
            }

            $out .= html_writer::end_tag('div');
        }

        $out .= html_writer::end_tag('div'); // Div cc-columns.
        $out .= html_writer::end_tag('div'); // Div cc-stage.

        // Nupp "Tühista ühendused".
        if (!empty($question->showclear) && !$readonly) {
            $out .= html_writer::start_tag('div', ['class' => 'cc-controls']);
            $out .= html_writer::tag(
                'button',
                get_string('clearconnections', 'qtype_columnconnector'),
                ['type' => 'button', 'class' => 'btn btn-secondary cc-clear']
            );
            $out .= html_writer::end_tag('div');
        }

        // Ligipääsetavuse teadete ala.
        $out .= html_writer::tag('div', '', [
            'class' => 'cc-sr-status cc-sr-only',
            'aria-live' => 'polite',
            'role' => 'status',
        ]);

        $out .= html_writer::end_tag('div'); // Div cc-container.

        if ($showcorrectness || $showkey) {
            $out .= $this->connection_summary($question, $showcorrectness ? $jsconnections : [], $missing);
        }

        if ($qa->get_state() == question_state::$invalid) {
            $out .= html_writer::nonempty_tag(
                'div',
                $question->get_validation_error($qa->get_last_qt_data()),
                ['class' => 'validationerror']
            );
        }

        $out .= html_writer::end_tag('div'); // Div ablock.

        $this->page->requires->js_call_amd('qtype_columnconnector/player', 'init', [$containerid]);

        return $out;
    }

    /**
     * Renderda üks lahter nupuna.
     *
     * @param question_attempt $qa
     * @param qtype_columnconnector_question $question
     * @param int $col
     * @param int $row
     * @param array $cell
     * @param bool $readonly
     * @return string
     */
    protected function render_cell(question_attempt $qa, $question, $col, $row, array $cell, $readonly) {
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
        if ($image !== '') {
            $src = moodle_url::make_pluginfile_url(
                $question->contextid,
                'qtype_columnconnector',
                'cellimages',
                $qa->get_usage_id() . '/' . $qa->get_slot() . '/' . $question->id,
                '/',
                $image
            )->out();
        } else {
            $src = qtype_columnconnector::clean_image_url((string)($cell['imageurl'] ?? ''));
        }

        $inner = '';
        $alt = trim((string)($cell['alt'] ?? ''));
        if ($src !== '') {
            $inner .= html_writer::empty_tag('img', [
                'class' => 'cc-cell-image',
                'src' => $src,
                'alt' => $alt,
                'draggable' => 'false',
            ]);
        }
        $text = (string)($cell['text'] ?? '');
        if (trim($text) !== '') {
            $inner .= html_writer::tag(
                'span',
                $question->format_text($text, FORMAT_HTML, $qa, 'qtype_columnconnector', 'celltext', $question->id, true),
                ['class' => 'cc-cell-text']
            );
        }

        // Lahter on konteiner, milles on eraldi valikunupp ja sisu. Sisu (sh hüperlingid)
        // ei asu nupu sees: HTML ei luba nupu sees teisi interaktiivseid elemente ning
        // lingi avamine ja lahtri valimine on eraldi juhtelemendid. Nupp katab kogu lahtri
        // pinna, sisu läbib klõpsud nupuni, välja arvatud lingid (vt styles.css).
        $label = $question->describe_cell(['col' => $col, 'row' => $row]);
        $buttonattributes = [
            'type' => 'button',
            'class' => 'cc-cell-select',
            'aria-pressed' => 'false',
            'aria-label' => get_string('selectcell', 'qtype_columnconnector', $label),
        ];
        if ($readonly) {
            $buttonattributes['aria-disabled'] = 'true';
        }

        return html_writer::div(
            html_writer::tag('button', '', $buttonattributes) .
                html_writer::div($inner, $contentclass),
            'cc-cell',
            [
                'data-col' => $col,
                'data-row' => $row,
                'data-label' => $label,
            ]
        );
    }

    /**
     * Tekstiline kokkuvõte ühendustest ülevaates (ekraanilugejatele).
     *
     * @param qtype_columnconnector_question $question
     * @param array $connections ühendused koos olekuga
     * @param array $missing puuduvad õiged ühendused
     * @return string
     */
    protected function connection_summary($question, array $connections, array $missing) {
        $items = '';
        foreach ($connections as $conn) {
            $items .= html_writer::tag('li', s(get_string(
                $conn['status'] === 'correct' ? 'connectioncorrect' : 'connectionincorrect',
                'qtype_columnconnector',
                $question->describe_connection($conn)
            )));
        }
        foreach ($missing as $conn) {
            $items .= html_writer::tag('li', s(get_string(
                'connectionmissing',
                'qtype_columnconnector',
                $question->describe_connection($conn)
            )));
        }
        if ($items === '') {
            return '';
        }
        return html_writer::div(
            html_writer::tag('p', get_string('connectionsummary', 'qtype_columnconnector')) .
                html_writer::tag('ul', $items),
            'cc-sr-only cc-summary'
        );
    }

    /**
     * Konkreetset tagasisidet pole eraldi väljadena; tulemus on standardse hindeteate kaudu.
     *
     * @param question_attempt $qa
     * @return string
     */
    public function specific_feedback(question_attempt $qa) {
        return '';
    }

    /**
     * Õige vastus tekstina ülevaates.
     *
     * Kuvatakse ainult siis, kui küsimuses on lubatud puuduvate ühenduste näitamine
     * (sama tingimus, mis puuduvate joonte puhul).
     *
     * @param question_attempt $qa
     * @return string
     */
    public function correct_response(question_attempt $qa) {
        /** @var qtype_columnconnector_question $question */
        $question = $qa->get_question();
        if (empty($question->showmissing) || empty($question->answerkey)) {
            return '';
        }
        $summary = $question->summarise_response($question->get_correct_response());
        if ($summary === '') {
            return '';
        }
        return get_string('correctansweris', 'qtype_columnconnector', s($summary));
    }
}
