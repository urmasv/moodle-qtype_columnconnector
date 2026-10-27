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

use core_question\statistics\responses\analyser;
use question_bank;
use question_engine;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/lib.php');

/**
 * Vastuste statistika (vastuste analüüs) testid päris katsetega.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\qtype_columnconnector::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(\qtype_columnconnector_question::class)]
final class statistics_test extends \advanced_testcase {
    /**
     * Vastus JSON-kujul.
     *
     * @param array ...$connections [lähtetulp, lähterida, sihttulp, sihtrida]
     * @return array
     */
    protected function answer(array ...$connections): array {
        $out = [];
        foreach ($connections as [$fc, $fr, $tc, $tr]) {
            $out[] = ['from' => ['col' => $fc, 'row' => $fr], 'to' => ['col' => $tc, 'row' => $tr]];
        }
        return ['answer' => json_encode($out)];
    }

    /**
     * Vastuste analüüs loeb nii ühendatud kui ka ühendamata lahtripaare.
     */
    public function test_response_analysis_counts_connected_and_unconnected_pairs(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $quiz = $generator->create_module('quiz', ['course' => $course->id]);
        $context = \context_module::instance($quiz->cmid);
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category(['contextid' => $context->id]);
        $created = $questiongenerator->create_question('columnconnector', 'twocolumn', ['category' => $cat->id]);
        $questiondata = question_bank::load_question_data($created->id);

        // Kolm katset: kõik õige, üks õige ja üks vale, vastuseta.
        $responses = [
            $this->answer([0, 0, 1, 0], [0, 1, 1, 1]),
            $this->answer([0, 0, 1, 0], [0, 1, 1, 0]),
            [],
        ];
        $qubaids = [];
        foreach ($responses as $response) {
            $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $context);
            $quba->set_preferred_behaviour('deferredfeedback');
            $slot = $quba->add_question(question_bank::load_question($created->id));
            $quba->start_all_questions();
            if ($response) {
                $quba->process_action($slot, $response);
            }
            $quba->finish_all_questions();
            question_engine::save_questions_usage_by_activity($quba);
            $qubaids[] = $quba->get_id();
        }

        // Sama qubaid-tingimuse objekti kasutatakse ka vahemälu lugemisel (selle räsi sõltub olekust).
        $qubaidcondition = new \qubaid_list($qubaids);
        $analyser = new analyser($questiondata);
        $analysis = $analyser->calculate($qubaidcondition);

        $counts = [];
        foreach ($analysis->get_subpart_ids(1) as $partid) {
            $subpart = $analysis->get_analysis_for_subpart(1, $partid);
            foreach ($subpart->get_response_class_ids() as $classid) {
                $rows = $subpart->get_response_class($classid)->data_for_question_response_table(true, $partid);
                $counts[$partid][(string)$classid] = array_sum(array_column($rows, 'totalcount'));
            }
        }

        // Neli lahtripaari, igaühel klassid ühendatud / ühendamata / vastus puudub.
        $this->assertCount(4, $counts);
        $this->assertEquals(['connected' => 2, 'notconnected' => 0, '' => 1], $counts['0:0-1:0']);
        $this->assertEquals(['connected' => 1, 'notconnected' => 1, '' => 1], $counts['0:1-1:1']);
        $this->assertEquals(['connected' => 1, 'notconnected' => 1, '' => 1], $counts['0:1-1:0']);
        $this->assertEquals(['connected' => 0, 'notconnected' => 2, '' => 1], $counts['0:0-1:1']);

        // Analüüs salvestatakse ja laaditakse vahemälust samade arvudega.
        $cached = (new analyser($questiondata))->load_cached($qubaidcondition, \question_attempt::LAST_TRY);
        $this->assertNotFalse($cached);
        $rows = $cached->get_analysis_for_subpart(1, '0:0-1:1')->get_response_class('notconnected')
            ->data_for_question_response_table(true, '0:0-1:1');
        $this->assertEquals(2, array_sum(array_column($rows, 'totalcount')));
    }
}
