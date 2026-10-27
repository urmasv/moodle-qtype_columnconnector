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

use question_classified_response;
use question_state;
use test_question_maker;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/columnconnector/question.php');

/**
 * columnconnector küsimuse hindamise ja abimeetodite testid.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\qtype_columnconnector_question::class)]
final class question_test extends \advanced_testcase {
    /**
     * Abifunktsioon: ehita ühendus.
     *
     * @param int $fc lähtetulp
     * @param int $fr lähterida
     * @param int $tc sihttulp
     * @param int $tr sihtrida
     * @return array
     */
    protected function conn(int $fc, int $fr, int $tc, int $tr): array {
        return ['from' => ['col' => $fc, 'row' => $fr], 'to' => ['col' => $tc, 'row' => $tr]];
    }

    /**
     * Vastus JSON-kujul.
     *
     * @param array ...$connections
     * @return array
     */
    protected function response(array ...$connections): array {
        return ['answer' => json_encode($connections)];
    }

    /**
     * Kõik õige annab täispunktid.
     */
    public function test_all_correct(): void {
        $key = [$this->conn(0, 0, 1, 0), $this->conn(0, 1, 1, 1)];
        $r = \qtype_columnconnector_question::grade_connections($key, $key, 1, -1, 0);
        $this->assertEquals(1.0, $r['fraction']);
        $this->assertEquals(2, $r['score']);
        $this->assertEquals(0, $r['incorrect']);
        $this->assertEquals(0, $r['missing']);
    }

    /**
     * Suund ei loe: sama ühendus vastupidises järjekorras.
     */
    public function test_direction_independent(): void {
        $key = [$this->conn(0, 0, 1, 0)];
        $resp = [$this->conn(1, 0, 0, 0)];
        $r = \qtype_columnconnector_question::grade_connections($key, $resp, 1, -1, 0);
        $this->assertEquals(1.0, $r['fraction']);
    }

    /**
     * Üks õige ja üks vale annab 0 punkti.
     */
    public function test_one_correct_one_wrong(): void {
        $key = [$this->conn(0, 0, 1, 0), $this->conn(0, 1, 1, 1)];
        $resp = [$this->conn(0, 0, 1, 0), $this->conn(0, 1, 1, 0)];
        $r = \qtype_columnconnector_question::grade_connections($key, $resp, 1, -1, 0);
        $this->assertEquals(1, $r['score']);
        $this->assertEquals(1, $r['incorrect']);
        $this->assertEquals(1, $r['missing']);
        $this->assertEquals(0.0, $r['fraction']);
    }

    /**
     * Osaliselt õige vastus ilma valedeta annab osalise murdosa.
     */
    public function test_partial_no_wrong(): void {
        $key = [$this->conn(0, 0, 1, 0), $this->conn(0, 1, 1, 1)];
        $r = \qtype_columnconnector_question::grade_connections($key, [$this->conn(0, 0, 1, 0)], 1, -1, 0);
        $this->assertEquals(0.5, $r['fraction']);
        $this->assertEquals(1, $r['missing']);
    }

    /**
     * Negatiivne kogusumma piiratakse nulliga.
     */
    public function test_negative_clamped(): void {
        $key = [$this->conn(0, 0, 1, 0)];
        $resp = [$this->conn(0, 0, 1, 1), $this->conn(0, 1, 1, 1)];
        $r = \qtype_columnconnector_question::grade_connections($key, $resp, 1, -1, 0);
        $this->assertEquals(0.0, $r['fraction']);
        $this->assertEquals(0, $r['points']);
    }

    /**
     * Duplikaadid eemaldatakse dekodeerimisel.
     */
    public function test_decode_deduplicates(): void {
        $raw = json_encode([$this->conn(0, 0, 1, 0), $this->conn(1, 0, 0, 0)]);
        $this->assertCount(1, \qtype_columnconnector_question::decode_connections($raw));
    }

    /**
     * Mitte-naabertulpade ja vigased ühendused lükatakse tagasi.
     */
    public function test_invalid_connections_rejected(): void {
        $raw = json_encode([
            $this->conn(0, 0, 2, 0),
            ['from' => 'x', 'to' => ['col' => 1, 'row' => 0]],
            ['from' => ['col' => -1, 'row' => 0], 'to' => ['col' => 0, 'row' => 0]],
        ]);
        $this->assertCount(0, \qtype_columnconnector_question::decode_connections($raw));
        $this->assertSame([], \qtype_columnconnector_question::decode_connections('not json'));
    }

    /**
     * Hindamine küsimuse kaudu.
     */
    public function test_grade_response(): void {
        $q = test_question_maker::make_question('columnconnector', 'twocolumn');
        [$fraction, $state] = $q->grade_response($this->response($this->conn(0, 0, 1, 0), $this->conn(0, 1, 1, 1)));
        $this->assertEquals(1.0, $fraction);
        $this->assertEquals(question_state::$gradedright, $state);

        [$fraction, $state] = $q->grade_response($this->response($this->conn(0, 0, 1, 0)));
        $this->assertEquals(0.5, $fraction);
        $this->assertEquals(question_state::$gradedpartial, $state);
    }

    /**
     * Õige vastus annab täispunktid ja on täielik.
     */
    public function test_get_correct_response(): void {
        $q = test_question_maker::make_question('columnconnector', 'twocolumn');
        $correct = $q->get_correct_response();
        $this->assertTrue($q->is_complete_response($correct));
        [$fraction] = $q->grade_response($correct);
        $this->assertEquals(1.0, $fraction);
    }

    /**
     * Vastuse täielikkus ja valideerimisteade.
     */
    public function test_is_complete_and_validation(): void {
        $q = test_question_maker::make_question('columnconnector', 'twocolumn');
        $this->assertFalse($q->is_complete_response([]));
        $this->assertFalse($q->is_complete_response(['answer' => '[]']));
        $this->assertTrue($q->is_complete_response($this->response($this->conn(0, 0, 1, 1))));
        $this->assertEquals(get_string('pleaseconnect', 'qtype_columnconnector'), $q->get_validation_error([]));
        $this->assertEquals('', $q->get_validation_error($this->response($this->conn(0, 0, 1, 1))));
    }

    /**
     * Sama vastus suunast ja järjekorrast sõltumata.
     */
    public function test_is_same_response(): void {
        $q = test_question_maker::make_question('columnconnector', 'twocolumn');
        $a = $this->response($this->conn(0, 0, 1, 0), $this->conn(0, 1, 1, 1));
        $b = $this->response($this->conn(1, 1, 0, 1), $this->conn(1, 0, 0, 0));
        $this->assertTrue($q->is_same_response($a, $b));
        $this->assertFalse($q->is_same_response($a, $this->response($this->conn(0, 0, 1, 0))));
    }

    /**
     * Vastuse kokkuvõte kasutab lahtrite teksti.
     */
    public function test_summarise_response(): void {
        $q = test_question_maker::make_question('columnconnector', 'twocolumn');
        $this->assertEquals('Üks – 1; Kaks – 2', $q->summarise_response(
            $this->response($this->conn(0, 0, 1, 0), $this->conn(0, 1, 1, 1))
        ));
        $this->assertEquals('', $q->summarise_response([]));
    }

    /**
     * Ilma tekstita lahtrit kirjeldab alt-tekst või asukoht.
     */
    public function test_describe_cell_fallbacks(): void {
        $q = test_question_maker::make_question('columnconnector', 'twocolumn');
        $q->columns[0]['cells'][0]['text'] = '';
        $q->columns[0]['cells'][0]['alt'] = 'Lipp';
        $q->columns[0]['cells'][1]['text'] = '';
        $this->assertEquals('Lipp', $q->describe_cell(['col' => 0, 'row' => 0]));
        $this->assertEquals(
            get_string('cellposition', 'qtype_columnconnector', (object)['col' => 1, 'row' => 2]),
            $q->describe_cell(['col' => 0, 'row' => 1])
        );
    }

    /**
     * Statistika jaoks klassifitseeritakse iga lahtripaar: ühendatud või ühendamata.
     */
    public function test_classify_response(): void {
        $q = test_question_maker::make_question('columnconnector', 'twocolumn');
        $classified = $q->classify_response($this->response(
            $this->conn(0, 0, 1, 0),
            $this->conn(0, 1, 1, 0),
            $this->conn(0, 5, 1, 0)
        ));
        $connected = fn($a) => get_string('statsconnected', 'qtype_columnconnector', $a);
        $notconnected = fn($a) => get_string('statsnotconnected', 'qtype_columnconnector', $a);
        $this->assertEquals([
            '0:0-1:0' => new question_classified_response('connected', $connected('Üks – 1'), 0.5),
            '0:0-1:1' => new question_classified_response('notconnected', $notconnected('Üks – 2'), 0),
            '0:1-1:0' => new question_classified_response('connected', $connected('Kaks – 1'), -0.5),
            // Tegemata õige ühendus: missingpoints = 0.
            '0:1-1:1' => new question_classified_response('notconnected', $notconnected('Kaks – 2'), 0),
        ], $classified);
    }

    /**
     * Puuduva õige ühenduse murdosa arvestab missingpoints väärtust.
     */
    public function test_classify_response_missing_points(): void {
        $q = test_question_maker::make_question('columnconnector', 'twocolumn');
        $q->missingpoints = -1;
        $classified = $q->classify_response($this->response($this->conn(0, 0, 1, 0)));
        $this->assertEquals('notconnected', $classified['0:1-1:1']->responseclassid);
        $this->assertEquals(-0.5, $classified['0:1-1:1']->fraction);
        $this->assertEquals(0, $classified['0:0-1:1']->fraction);
    }

    /**
     * Vastuseta katse korral on iga osa "vastus puudub".
     */
    public function test_classify_response_no_response(): void {
        $q = test_question_maker::make_question('columnconnector', 'twocolumn');
        $classified = $q->classify_response([]);
        $this->assertCount(4, $classified);
        foreach ($classified as $part) {
            $this->assertEquals(question_classified_response::no_response(), $part);
        }
    }

    /**
     * Ühenduste olek ja puuduvad ühendused arvutatakse serveris.
     */
    public function test_connection_feedback_and_missing(): void {
        $q = test_question_maker::make_question('columnconnector', 'twocolumn');
        $connections = [$this->conn(0, 0, 1, 0), $this->conn(0, 1, 1, 0)];
        $feedback = $q->get_connection_feedback($connections);
        $this->assertEquals('correct', $feedback[0]['status']);
        $this->assertEquals('incorrect', $feedback[1]['status']);
        $this->assertEquals([$this->conn(0, 1, 1, 1)], $q->get_missing_connections($connections));
    }

    /**
     * Lisa testküsimuse lahtritele id-d.
     *
     * @param \qtype_columnconnector_question $q
     * @return \qtype_columnconnector_question
     */
    protected function with_ids(\qtype_columnconnector_question $q): \qtype_columnconnector_question {
        foreach ($q->columns as $c => $column) {
            foreach (array_keys($column['cells']) as $r) {
                $q->columns[$c]['cells'][$r]['id'] = 'n' . $c . 'x' . $r;
            }
        }
        return $q;
    }

    /**
     * Ümberhindamine: lahtrite arvu muutus keelab ümberhindamise.
     */
    public function test_regrade_structure_changed(): void {
        $q = test_question_maker::make_question('columnconnector', 'twocolumn');
        $same = test_question_maker::make_question('columnconnector', 'twocolumn');
        $this->assertNull($q->validate_can_regrade_with_other_version($same));

        $changed = test_question_maker::make_question('columnconnector', 'twocolumn');
        array_pop($changed->columns[1]['cells']);
        $this->assertEquals(
            get_string('regradeissuestructurechanged', 'qtype_columnconnector'),
            $q->validate_can_regrade_with_other_version($changed)
        );
    }

    /**
     * Ümberhindamine: sama arvu lahtrite järjekorra muutus keelab ümberhindamise.
     */
    public function test_regrade_cell_order_changed(): void {
        $q = $this->with_ids(test_question_maker::make_question('columnconnector', 'twocolumn'));
        $reordered = $this->with_ids(test_question_maker::make_question('columnconnector', 'twocolumn'));
        $reordered->columns[1]['cells'] = array_reverse($reordered->columns[1]['cells']);
        $this->assertEquals(
            get_string('regradeissuecellorderchanged', 'qtype_columnconnector'),
            $q->validate_can_regrade_with_other_version($reordered)
        );
    }

    /**
     * Ümberhindamine: lahtri sisu parandamine sama id-ga on lubatud.
     */
    public function test_regrade_cell_content_edited_same_id(): void {
        $q = $this->with_ids(test_question_maker::make_question('columnconnector', 'twocolumn'));
        $edited = $this->with_ids(test_question_maker::make_question('columnconnector', 'twocolumn'));
        $edited->columns[0]['cells'][0]['text'] = '<p>Üks (parandatud)</p>';
        $edited->answerkey = [$this->conn(0, 0, 1, 1), $this->conn(0, 1, 1, 0)];
        $this->assertNull($q->validate_can_regrade_with_other_version($edited));
    }

    /**
     * Ümberhindamine: uus lahter teise id-ga samal kohal keelab ümberhindamise.
     */
    public function test_regrade_cell_replaced(): void {
        $q = $this->with_ids(test_question_maker::make_question('columnconnector', 'twocolumn'));
        $replaced = $this->with_ids(test_question_maker::make_question('columnconnector', 'twocolumn'));
        $replaced->columns[1]['cells'][0]['id'] = 'nuus';
        $this->assertEquals(
            get_string('regradeissuecellorderchanged', 'qtype_columnconnector'),
            $q->validate_can_regrade_with_other_version($replaced)
        );
    }

    /**
     * Ümberhindamine id-deta sisuga: võrreldakse lahtrite sisu kohtade kaupa.
     */
    public function test_regrade_without_ids_compares_content(): void {
        $q = test_question_maker::make_question('columnconnector', 'twocolumn');
        $reordered = test_question_maker::make_question('columnconnector', 'twocolumn');
        $reordered->columns[0]['cells'] = array_reverse($reordered->columns[0]['cells']);
        $this->assertEquals(
            get_string('regradeissuecellorderchanged', 'qtype_columnconnector'),
            $q->validate_can_regrade_with_other_version($reordered)
        );
    }
}
