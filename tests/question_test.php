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
 * columnconnector hindamise ühiktestid.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_columnconnector;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/type/columnconnector/question.php');

/**
 * columnconnector küsimuse hindamise testid.
 *
 * @covers \qtype_columnconnector_question
 */
final class question_test extends \basic_testcase {

    /**
     * Abifunktsioon: ehita ühendus.
     *
     * @param int $fc from col
     * @param int $fr from row
     * @param int $tc to col
     * @param int $tr to row
     * @return array
     */
    protected function conn($fc, $fr, $tc, $tr) {
        return ['from' => ['col' => $fc, 'row' => $fr], 'to' => ['col' => $tc, 'row' => $tr]];
    }

    /**
     * Kõik õige annab täispunktid.
     */
    public function test_all_correct() {
        $key = [$this->conn(0, 0, 1, 0), $this->conn(0, 1, 1, 1)];
        $resp = [$this->conn(0, 0, 1, 0), $this->conn(0, 1, 1, 1)];
        $r = \qtype_columnconnector_question::grade_connections($key, $resp, 1, -1, 0);
        $this->assertEquals(1.0, $r['fraction']);
        $this->assertEquals(2, $r['score']);
        $this->assertEquals(0, $r['incorrect']);
        $this->assertEquals(0, $r['missing']);
    }

    /**
     * Suund ei loe: sama ühendus vastupidises järjekorras.
     */
    public function test_direction_independent() {
        $key = [$this->conn(0, 0, 1, 0)];
        $resp = [$this->conn(1, 0, 0, 0)];
        $r = \qtype_columnconnector_question::grade_connections($key, $resp, 1, -1, 0);
        $this->assertEquals(1.0, $r['fraction']);
    }

    /**
     * Üks õige, üks vale: 1 õige - 1 vale = 0 punkti, murdosa 0.
     */
    public function test_one_correct_one_wrong() {
        $key = [$this->conn(0, 0, 1, 0), $this->conn(0, 1, 1, 1)];
        $resp = [$this->conn(0, 0, 1, 0), $this->conn(0, 1, 1, 0)];
        $r = \qtype_columnconnector_question::grade_connections($key, $resp, 1, -1, 0);
        $this->assertEquals(1, $r['score']);
        $this->assertEquals(1, $r['incorrect']);
        $this->assertEquals(1, $r['missing']);
        // points = 1*1 + 1*(-1) + 1*0 = 0; max = 2.
        $this->assertEquals(0.0, $r['fraction']);
    }

    /**
     * Osaline õige ilma valedeta annab osalise murdosa.
     */
    public function test_partial_no_wrong() {
        $key = [$this->conn(0, 0, 1, 0), $this->conn(0, 1, 1, 1)];
        $resp = [$this->conn(0, 0, 1, 0)];
        $r = \qtype_columnconnector_question::grade_connections($key, $resp, 1, -1, 0);
        // points = 1; max = 2 -> 0.5.
        $this->assertEquals(0.5, $r['fraction']);
        $this->assertEquals(1, $r['missing']);
    }

    /**
     * Negatiivne kogusumma piiratakse nulliga.
     */
    public function test_negative_clamped() {
        $key = [$this->conn(0, 0, 1, 0)];
        $resp = [$this->conn(0, 0, 1, 1), $this->conn(0, 0, 1, 1)];
        $r = \qtype_columnconnector_question::grade_connections($key, $resp, 1, -1, 0);
        $this->assertEquals(0.0, $r['fraction']);
        $this->assertEquals(0, $r['points']);
    }

    /**
     * Duplikaatide eemaldamine dekodeerimisel.
     */
    public function test_decode_deduplicates() {
        $raw = json_encode([$this->conn(0, 0, 1, 0), $this->conn(1, 0, 0, 0)]);
        $decoded = \qtype_columnconnector_question::decode_connections($raw);
        $this->assertCount(1, $decoded);
    }

    /**
     * Mitte-naabertulpade ühendus lükatakse tagasi.
     */
    public function test_non_adjacent_rejected() {
        $decoded = \qtype_columnconnector_question::decode_connections(
            json_encode([$this->conn(0, 0, 2, 0)]));
        $this->assertCount(0, $decoded);
    }

    /**
     * grade_response tagastab õige murdosa ja seisundi.
     */
    public function test_grade_response_via_question() {
        $q = \qtype_columnconnector_test_helper::make_columnconnector_question_twocolumn();
        $response = ['answer' => json_encode([$this->conn(0, 0, 1, 0), $this->conn(0, 1, 1, 1)])];
        list($fraction, $state) = $q->grade_response($response);
        $this->assertEquals(1.0, $fraction);
        $this->assertEquals(\question_state::$gradedright, $state);
    }

    /**
     * is_same_response arvestab suunast sõltumatust.
     */
    public function test_is_same_response() {
        $q = \qtype_columnconnector_test_helper::make_columnconnector_question_twocolumn();
        $a = ['answer' => json_encode([$this->conn(0, 0, 1, 0)])];
        $b = ['answer' => json_encode([$this->conn(1, 0, 0, 0)])];
        $this->assertTrue($q->is_same_response($a, $b));
    }
}
