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

namespace qtype_columnconnector;

use question_bank;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

/**
 * columnconnector varunduse ja taastamise testid.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\backup_qtype_columnconnector_plugin::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(\restore_qtype_columnconnector_plugin::class)]
final class backup_test extends \advanced_testcase {
    /**
     * Kursuse varundamine ja taastamine uude kursusesse säilitab sisu ja lahtripildid.
     */
    public function test_backup_restore_with_cell_image(): void {
        global $DB, $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $course = $generator->create_course();
        $quiz = $generator->create_module('quiz', ['course' => $course->id]);
        $cat = $questiongenerator->create_question_category(['contextid' => \context_module::instance($quiz->cmid)->id]);

        // Lahtripilt mustandialasse.
        $draftid = file_get_unused_draft_itemid();
        get_file_storage()->create_file_from_string([
            'contextid' => \context_user::instance($USER->id)->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => $draftid,
            'filepath' => '/',
            'filename' => 'pilt.png',
        ], 'pildisisu');

        $formdata = \test_question_maker::get_question_form_data('columnconnector', 'twocolumn');
        $content = json_decode($formdata->contentjson, true);
        $content['columns'][0]['cells'][0]['image'] = 'pilt.png';
        $question = $questiongenerator->create_question('columnconnector', 'twocolumn', [
            'category' => $cat->id,
            'contentjson' => json_encode($content),
            'cellimages' => $draftid,
        ]);
        quiz_add_quiz_question($question->id, $DB->get_record('quiz', ['id' => $quiz->id]));

        $count = $DB->count_records('question', ['qtype' => 'columnconnector']);

        // Varunda ja taasta uude kursusesse.
        $bc = new \backup_controller(
            \backup::TYPE_1COURSE,
            $course->id,
            \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO,
            \backup::MODE_IMPORT,
            $USER->id
        );
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        $newcourseid = \restore_dbops::create_new_course('Taastatud', 'TAAST', $course->category);
        $rc = new \restore_controller(
            $backupid,
            $newcourseid,
            \backup::INTERACTIVE_NO,
            \backup::MODE_IMPORT,
            $USER->id,
            \backup::TARGET_NEW_COURSE
        );
        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();

        $this->assertEquals($count + 1, $DB->count_records('question', ['qtype' => 'columnconnector']));
        $newid = $DB->get_field_sql('SELECT MAX(id) FROM {question} WHERE qtype = ?', ['columnconnector']);
        $newdata = question_bank::load_question_data($newid);
        $olddata = question_bank::load_question_data($question->id);

        $this->assertEquals($olddata->options->content, $newdata->options->content);
        $this->assertEquals($olddata->options->showmissing, $newdata->options->showmissing);
        $this->assertNotEquals($olddata->contextid, $newdata->contextid);

        $files = get_file_storage()->get_area_files(
            $newdata->contextid,
            'qtype_columnconnector',
            'cellimages',
            $newid,
            'filename',
            false
        );
        $this->assertCount(1, $files);
        $this->assertEquals('pilt.png', reset($files)->get_filename());
    }

    /**
     * Loo kaks kursust: küsimus on teise kursuse küsimustepangas, test esimeses kursuses.
     *
     * @return array [kursus1, kursus2, küsimus]
     */
    protected function create_shared_question_setup(): array {
        global $DB;
        $generator = $this->getDataGenerator();
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $course1 = $generator->create_course();
        $course2 = $generator->create_course();
        if (\core_component::get_component_directory('mod_qbank')) {
            $qbank = $generator->create_module('qbank', ['course' => $course2->id]);
            $bankcontext = \context_module::instance($qbank->cmid);
        } else {
            $bankcontext = \context_course::instance($course2->id);
        }
        $cat = $questiongenerator->create_question_category(['contextid' => $bankcontext->id]);
        $question = $questiongenerator->create_question('columnconnector', 'twocolumn', ['category' => $cat->id]);
        $quiz = $generator->create_module('quiz', ['course' => $course1->id]);
        quiz_add_quiz_question($question->id, $DB->get_record('quiz', ['id' => $quiz->id]));
        return [$course1, $course2, $question];
    }

    /**
     * Varunda kursus (importrežiimis) ja tagasta varunduse id.
     *
     * @param int $courseid
     * @return string
     */
    protected function backup_course(int $courseid): string {
        global $USER;
        $bc = new \backup_controller(
            \backup::TYPE_1COURSE,
            $courseid,
            \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO,
            \backup::MODE_IMPORT,
            $USER->id
        );
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();
        return $backupid;
    }

    /**
     * Taasta varundus olemasolevasse kursusesse (lisades).
     *
     * @param string $backupid
     * @param int $courseid
     */
    protected function restore_into_course(string $backupid, int $courseid): void {
        global $USER;
        $rc = new \restore_controller(
            $backupid,
            $courseid,
            \backup::INTERACTIVE_NO,
            \backup::MODE_IMPORT,
            $USER->id,
            \backup::TARGET_CURRENT_ADDING
        );
        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();
    }

    /**
     * Jagatud küsimusega kursuse korduv taastamine ei tekita küsimustepanka duplikaate.
     */
    public function test_repeated_restore_does_not_duplicate_shared_question(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        [$course1, $course2, $question] = $this->create_shared_question_setup();
        $count = $DB->count_records('question', ['qtype' => 'columnconnector']);

        $this->restore_into_course($this->backup_course($course1->id), $course2->id);
        $this->restore_into_course($this->backup_course($course1->id), $course2->id);

        $this->assertEquals($count, $DB->count_records('question', ['qtype' => 'columnconnector']));
        foreach (get_fast_modinfo($course2->id)->get_instances_of('quiz') as $cm) {
            $structure = \mod_quiz\question\bank\qbank_helper::get_question_structure(
                $cm->instance,
                \context_module::instance($cm->id)
            );
            $this->assertEquals($question->id, reset($structure)->questionid);
        }
    }

    /**
     * Vanema (1.1) vormingu varundus vastab uuendatud küsimusele ega tekita duplikaati.
     *
     * Vanas vormingus puuduvad lahtrite id-d ning juhis on eraldi väljas.
     */
    public function test_restore_legacy_backup_matches_upgraded_question(): void {
        global $CFG, $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        [$course1, $course2, $question] = $this->create_shared_question_setup();
        // Uuendusskripti läbinud küsimus: juhis on küsimuse tekstis, lahtrite id-d on tuletatud sisust.
        $DB->set_field('question', 'questiontext', '<p>Ühenda paarid.</p>', ['id' => $question->id]);
        $content = json_decode($DB->get_field('qtype_columnconnector', 'content', ['questionid' => $question->id]), true);
        foreach ($content['columns'] as $c => $column) {
            foreach (array_keys($column['cells']) as $r) {
                unset($content['columns'][$c]['cells'][$r]['id']);
            }
        }
        $DB->set_field(
            'qtype_columnconnector',
            'content',
            \qtype_columnconnector::add_missing_cell_ids(json_encode($content)),
            ['questionid' => $question->id]
        );
        \question_bank::notify_question_edited($question->id);
        $count = $DB->count_records('question', ['qtype' => 'columnconnector']);

        $backupid = $this->backup_course($course1->id);

        // Muuda varundus vanaks vorminguks.
        $file = $CFG->backuptempdir . '/' . $backupid . '/questions.xml';
        $doc = new \DOMDocument();
        $doc->load($file);
        $xpath = new \DOMXPath($doc);
        $legacydone = 0;
        foreach ($xpath->query('//question[qtype="columnconnector"]') as $qnode) {
            $xpath->query('questiontext', $qnode)->item(0)->nodeValue = '';
            $options = $xpath->query('.//plugin_qtype_columnconnector_question/columnconnector', $qnode)->item(0);
            $contentnode = $xpath->query('content', $options)->item(0);
            $content = json_decode($contentnode->nodeValue, true);
            foreach ($content['columns'] as $c => $column) {
                foreach (array_keys($column['cells']) as $r) {
                    unset($content['columns'][$c]['cells'][$r]['id']);
                }
            }
            $contentnode->nodeValue = '';
            $contentnode->appendChild($doc->createTextNode(json_encode($content)));
            $instructions = $doc->createElement('instructions');
            $instructions->appendChild($doc->createTextNode('<p>Ühenda paarid.</p>'));
            $options->appendChild($instructions);
            $options->appendChild($doc->createElement('instructionsformat', '1'));
            $legacydone++;
        }
        $this->assertEquals(1, $legacydone);
        $doc->save($file);

        $this->restore_into_course($backupid, $course2->id);

        $this->assertEquals($count, $DB->count_records('question', ['qtype' => 'columnconnector']));
    }

    /**
     * Vanema vormingu andmed teisendatakse samale kujule, mis uuendatud küsimusel.
     */
    public function test_convert_backup_to_questiondata_legacy(): void {
        $this->resetAfterTest();
        if (!method_exists(\restore_qtype_plugin::class, 'convert_backup_to_questiondata')) {
            $this->markTestSkipped('Moodle\'i versioonis puudub convert_backup_to_questiondata().');
        }
        $legacycontent = json_encode([
            'columns' => [['title' => '', 'cells' => [['text' => 'a']]], ['title' => '', 'cells' => [['text' => '1']]]],
            'answerkey' => [['from' => ['col' => 0, 'row' => 0], 'to' => ['col' => 1, 'row' => 0]]],
        ]);
        $data = \restore_qtype_columnconnector_plugin::convert_backup_to_questiondata([
            'qtype' => 'columnconnector',
            'questiontext' => '',
            'questiontextformat' => FORMAT_HTML,
            'plugin_qtype_columnconnector_question' => [
                'columnconnector' => [[
                    'id' => 5,
                    'numcolumns' => 2,
                    'content' => $legacycontent,
                    'instructions' => '<p>Juhis</p>',
                    'instructionsformat' => FORMAT_HTML,
                ]],
            ],
        ]);
        $this->assertEquals('<p>Juhis</p>', $data->questiontext);
        $this->assertFalse(property_exists($data->options, 'instructions'));
        $this->assertSame(\qtype_columnconnector::add_missing_cell_ids($legacycontent), $data->options->content);
    }

    /**
     * Sama templiga, kuid erineva sisuga küsimusi ei seota taastamisel omavahel.
     *
     * Vaste Moodle'i testile repeated_restore_test::test_restore_course_with_same_stamp_questions,
     * mis selle küsimusetüübi puhul vahele jääb, sest küsimusetüüp ei kasuta vastuste tabelit.
     */
    public function test_restore_same_stamp_different_content(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        [$course1, $course2, $question1] = $this->create_shared_question_setup();
        $generator = $this->getDataGenerator();
        $question2 = $generator->get_plugin_generator('core_question')->create_question(
            'columnconnector',
            'twocolumn',
            ['category' => $question1->category]
        );
        $quiz2 = $generator->create_module('quiz', ['course' => $course1->id]);
        quiz_add_quiz_question($question2->id, $DB->get_record('quiz', ['id' => $quiz2->id]));

        // Teisel küsimusel sama tempel, kuid muudetud lahtri tekst.
        $DB->set_field('question', 'stamp', $question1->stamp, ['id' => $question2->id]);
        $content = json_decode($DB->get_field('qtype_columnconnector', 'content', ['questionid' => $question2->id]), true);
        $content['columns'][0]['cells'][0]['text'] = '<p>Muudetud</p>';
        $DB->set_field('qtype_columnconnector', 'content', json_encode($content), ['questionid' => $question2->id]);
        \question_bank::notify_question_edited($question2->id);
        $count = $DB->count_records('question', ['qtype' => 'columnconnector']);

        $this->restore_into_course($this->backup_course($course1->id), $course2->id);

        $this->assertEquals($count, $DB->count_records('question', ['qtype' => 'columnconnector']));
        $used = [];
        foreach (get_fast_modinfo($course2->id)->get_instances_of('quiz') as $cm) {
            $structure = \mod_quiz\question\bank\qbank_helper::get_question_structure(
                $cm->instance,
                \context_module::instance($cm->id)
            );
            $used[] = reset($structure)->questionid;
        }
        sort($used);
        $expected = [$question1->id, $question2->id];
        sort($expected);
        $this->assertEquals($expected, $used);
    }

    /**
     * Identsed küsimused (erinevad ainult lahtrite id-d) on taastamisel sama identiteediga.
     *
     * Vaste Moodle'i testile repeated_restore_test::test_restore_quiz_with_duplicate_questions.
     */
    public function test_identity_hash_ignores_cell_ids(): void {
        global $CFG;
        require_once($CFG->dirroot . '/backup/util/helper/restore_questions_parser_processor.class.php');
        $this->resetAfterTest();
        $this->setAdminUser();
        if (!method_exists(\restore_questions_parser_processor::class, 'generate_question_identity_hash')) {
            $this->markTestSkipped('Moodle\'i versioonis puudub taastamise identiteedi räsi.');
        }
        [$course1, $course2, $question1] = $this->create_shared_question_setup();
        $question2 = $this->getDataGenerator()->get_plugin_generator('core_question')->create_question(
            'columnconnector',
            'twocolumn',
            ['category' => $question1->category]
        );
        $hash = function (int $questionid): string {
            global $DB;
            $question = $DB->get_record('question', ['id' => $questionid]);
            $question->export_process = true;
            get_question_options($question);
            unset($question->export_process, $question->categoryobject, $question->questioncategoryid);
            // Ainult sisulised väljad: tempel ja ajad erinevad niikuinii.
            $question->stamp = '';
            $question->timecreated = $question->timemodified = 0;
            return \restore_questions_parser_processor::generate_question_identity_hash($question);
        };
        $content1 = json_decode(question_bank::load_question_data($question1->id)->options->content, true);
        $content2 = json_decode(question_bank::load_question_data($question2->id)->options->content, true);
        $this->assertNotEquals($content1['columns'][0]['cells'][0]['id'], $content2['columns'][0]['cells'][0]['id']);
        $this->assertEquals($hash($question1->id), $hash($question2->id));
    }

    /**
     * Vigase varukoopia taastamisel parandatakse samas tulbas korduvad lahtrite id-d.
     */
    public function test_restore_fixes_duplicate_cell_ids(): void {
        global $CFG, $DB, $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $quiz = $generator->create_module('quiz', ['course' => $course->id]);
        $cat = $generator->get_plugin_generator('core_question')
            ->create_question_category(['contextid' => \context_module::instance($quiz->cmid)->id]);
        $question = $generator->get_plugin_generator('core_question')
            ->create_question('columnconnector', 'twocolumn', ['category' => $cat->id]);
        quiz_add_quiz_question($question->id, $DB->get_record('quiz', ['id' => $quiz->id]));

        $backupid = $this->backup_course($course->id);

        // Tee varukoopiast vigane: teise tulba lahtritel on sama (korrektse kujuga) id.
        $file = $CFG->backuptempdir . '/' . $backupid . '/questions.xml';
        $doc = new \DOMDocument();
        $doc->load($file);
        $xpath = new \DOMXPath($doc);
        $node = $xpath->query('//question[qtype="columnconnector"]//plugin_qtype_columnconnector_question/columnconnector/content')
            ->item(0);
        $content = json_decode($node->nodeValue, true);
        foreach (array_keys($content['columns'][1]['cells']) as $r) {
            $content['columns'][1]['cells'][$r]['id'] = 'nkordus';
        }
        $node->nodeValue = '';
        $node->appendChild($doc->createTextNode(json_encode($content)));
        $doc->save($file);

        $newcourseid = \restore_dbops::create_new_course('Taastatud', 'TAAST2', $course->category);
        $rc = new \restore_controller(
            $backupid,
            $newcourseid,
            \backup::INTERACTIVE_NO,
            \backup::MODE_IMPORT,
            $USER->id,
            \backup::TARGET_NEW_COURSE
        );
        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();

        $newid = $DB->get_field_sql('SELECT MAX(id) FROM {question} WHERE qtype = ?', ['columnconnector']);
        $this->assertNotEquals($question->id, $newid);
        $restored = json_decode($DB->get_field('qtype_columnconnector', 'content', ['questionid' => $newid]), true);
        $ids = array_column($restored['columns'][1]['cells'], 'id');
        $this->assertEquals('nkordus', $ids[0]);
        $this->assertCount(count($ids), array_unique($ids));
    }
}
