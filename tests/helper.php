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
 * Testküsimuste abiklass qtype_columnconnector jaoks.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Loob columnconnector testküsimusi.
 */
class qtype_columnconnector_test_helper extends question_test_helper {

    /**
     * @return array saadaolevate testküsimuste nimed
     */
    public function get_test_questions() {
        return ['twocolumn'];
    }

    /**
     * Kahe tulba testküsimuse sisu-mudel.
     *
     * @return array
     */
    protected static function twocolumn_content() {
        return [
            'columns' => [
                ['title' => 'A', 'cells' => [
                    ['text' => '<p>Üks</p>', 'imageurl' => '', 'imageposition' => 'above',
                        'imageleftsize' => 'small', 'alt' => ''],
                    ['text' => '<p>Kaks</p>', 'imageurl' => '', 'imageposition' => 'above',
                        'imageleftsize' => 'small', 'alt' => ''],
                ]],
                ['title' => 'B', 'cells' => [
                    ['text' => '<p>1</p>', 'imageurl' => '', 'imageposition' => 'above',
                        'imageleftsize' => 'small', 'alt' => ''],
                    ['text' => '<p>2</p>', 'imageurl' => '', 'imageposition' => 'above',
                        'imageleftsize' => 'small', 'alt' => ''],
                ]],
            ],
            'answerkey' => [
                ['from' => ['col' => 0, 'row' => 0], 'to' => ['col' => 1, 'row' => 0]],
                ['from' => ['col' => 0, 'row' => 1], 'to' => ['col' => 1, 'row' => 1]],
            ],
        ];
    }

    /**
     * Loo kahe tulba küsimuse definitsioon.
     *
     * @return qtype_columnconnector_question
     */
    public static function make_columnconnector_question_twocolumn() {
        question_bank::load_question_definition_classes('columnconnector');
        $q = new qtype_columnconnector_question();
        test_question_maker::initialise_a_question($q);
        $q->name = 'Tulpade ühendaja (2 tulpa)';
        $q->questiontext = 'Ühenda lahtrid.';
        $q->generalfeedback = 'Üldtagasiside.';
        $q->qtype = question_bank::get_qtype('columnconnector');

        $q->numcolumns = 2;
        $q->layoutmode = 'columns';
        $q->linestyle = 'curved';
        $q->showclear = 1;
        $q->correctpoints = 1;
        $q->incorrectpoints = -1;
        $q->missingpoints = 0;
        $q->instructions = '<p>Ühenda paarid.</p>';
        $q->instructionsformat = FORMAT_HTML;

        $content = self::twocolumn_content();
        $q->columns = $content['columns'];
        $q->answerkey = $content['answerkey'];

        return $q;
    }

    /**
     * Kahe tulba küsimuse vormiandmed (andmegeneraatorile).
     *
     * @return stdClass
     */
    public function get_columnconnector_question_form_data_twocolumn() {
        $form = new stdClass();
        $form->name = 'Tulpade ühendaja (2 tulpa)';
        $form->questiontext = ['text' => 'Ühenda lahtrid.', 'format' => FORMAT_HTML];
        $form->defaultmark = 1;
        $form->generalfeedback = ['text' => 'Üldtagasiside.', 'format' => FORMAT_HTML];

        $form->numcolumns = 2;
        $form->layoutmode = 'columns';
        $form->linestyle = 'curved';
        $form->showclear = 1;
        $form->correctpoints = 1;
        $form->incorrectpoints = -1;
        $form->missingpoints = 0;
        $form->instructions = '<p>Ühenda paarid.</p>';
        $form->contentjson = json_encode(self::twocolumn_content());

        return $form;
    }
}
