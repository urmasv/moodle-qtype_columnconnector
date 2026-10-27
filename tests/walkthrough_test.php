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

use question_display_options;
use question_state;
use test_question_maker;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

/**
 * columnconnector küsimuse läbimängutestid eri käitumisrežiimides.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\qtype_columnconnector_renderer::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(\qtype_columnconnector_question::class)]
final class walkthrough_test extends \qbehaviour_walkthrough_test_base {
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
     * Loe renderdatud väljundist mängija seaded (data-config).
     *
     * @return array
     */
    protected function get_player_config(): array {
        $this->assertMatchesRegularExpression('~class="cc-container[^"]*"[^>]*data-config="([^"]*)"~', $this->currentoutput);
        preg_match('~class="cc-container[^"]*"[^>]*data-config="([^"]*)"~', $this->currentoutput, $m);
        return json_decode(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5), true);
    }

    /**
     * Edasilükatud tagasiside: vastusevõtit ei saadeta enne, kui õiget vastust tohib näidata.
     */
    public function test_deferred_feedback_does_not_leak_answer_key(): void {
        $q = test_question_maker::make_question('columnconnector', 'twocolumn');
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);

        $this->render();
        $config = $this->get_player_config();
        $this->assertFalse($config['readonly']);
        $this->assertFalse($config['showCorrectness']);
        $this->assertSame([], $config['connections']);
        $this->assertSame([], $config['missing']);
        $this->assertStringNotContainsString('answerKey', $this->currentoutput);

        $this->process_submission($this->answer([0, 0, 1, 0], [0, 1, 1, 0]));
        $this->check_current_state(question_state::$complete);
        $this->render();
        $config = $this->get_player_config();
        $this->assertArrayNotHasKey('status', $config['connections'][0]);

        $this->finish();
        $this->check_current_state(question_state::$gradedwrong);
        $this->check_current_mark(0);

        // Kui ülevaates on ainult "Kas õige", on õppija joonte olek olemas, puuduvaid ühendusi mitte.
        $this->displayoptions->correctness = question_display_options::VISIBLE;
        $this->displayoptions->rightanswer = question_display_options::HIDDEN;
        $this->render();
        $config = $this->get_player_config();
        $this->assertTrue($config['readonly']);
        $this->assertEquals(['correct', 'incorrect'], array_column($config['connections'], 'status'));
        $this->assertSame([], $config['missing']);
        $this->assertStringContainsString('cc-readonly', $this->currentoutput);

        // Kui ülevaates on ka õige vastus, saadetakse puuduv ühendus (küsimuses showmissing = 1).
        $this->displayoptions->rightanswer = question_display_options::VISIBLE;
        $this->render();
        $config = $this->get_player_config();
        $this->assertEquals(
            [['from' => ['col' => 0, 'row' => 1], 'to' => ['col' => 1, 'row' => 1]]],
            $config['missing']
        );
    }

    /**
     * Kui küsimuses on puuduvate ühenduste näitamine välja lülitatud, ei saadeta neid ka õige vastuse korral.
     */
    public function test_showmissing_off_hides_missing(): void {
        $q = test_question_maker::make_question('columnconnector', 'twocolumn');
        $q->showmissing = 0;
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);
        $this->process_submission($this->answer([0, 0, 1, 0]));
        $this->finish();
        $this->check_current_state(question_state::$gradedpartial);
        $this->check_current_mark(0.5);

        $this->displayoptions->correctness = question_display_options::VISIBLE;
        $this->displayoptions->rightanswer = question_display_options::VISIBLE;
        $this->render();
        $config = $this->get_player_config();
        $this->assertSame([], $config['missing']);
    }

    /**
     * Adaptiivne režiim: pärast kontrollimist saab vastust edasi muuta.
     */
    public function test_adaptive_remains_editable_after_check(): void {
        $q = test_question_maker::make_question('columnconnector', 'twocolumn');
        $this->start_attempt_at_question($q, 'adaptive', 1);

        $this->process_submission($this->answer([0, 0, 1, 1]) + ['-submit' => 1]);
        $this->check_current_state(question_state::$todo);
        $this->render();
        $config = $this->get_player_config();
        $this->assertFalse($config['readonly']);
        $this->assertTrue($config['showCorrectness']);
        $this->assertEquals(['incorrect'], array_column($config['connections'], 'status'));
        $this->assertSame([], $config['missing']);
        $this->assertStringNotContainsString('cc-readonly', $this->currentoutput);
        $this->assertStringContainsString('cc-clear', $this->currentoutput);

        $this->process_submission($this->answer([0, 0, 1, 0], [0, 1, 1, 1]) + ['-submit' => 1]);
        $this->check_current_state(question_state::$complete);
        $this->finish();
        $this->check_current_state(question_state::$gradedright);
        // Trahv lahutatakse ühe vale katse eest.
        $this->check_current_mark(1 - $q->penalty);
    }

    /**
     * Interaktiivne režiim ilma vihjeteta: üks katse.
     */
    public function test_interactive_single_try(): void {
        $q = test_question_maker::make_question('columnconnector', 'twocolumn');
        $this->start_attempt_at_question($q, 'interactive', 1);
        $this->process_submission($this->answer([0, 0, 1, 0], [0, 1, 1, 1]) + ['-submit' => 1]);
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(1);
    }

    /**
     * Ligipääsetavus: lahtritel on nimed ja kasutusjuhis on seotud konteineriga.
     */
    public function test_accessibility_markup(): void {
        $q = test_question_maker::make_question('columnconnector', 'twocolumn');
        $q->columns[0]['cells'][0]['text'] = '';
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);
        $this->render();
        $this->assertStringContainsString('aria-describedby="' . $this->quba->get_question_attempt($this->slot)
            ->get_outer_question_div_unique_id() . '-cc-help"', $this->currentoutput);
        $position = get_string('cellposition', 'qtype_columnconnector', (object)['col' => 1, 'row' => 1]);
        $this->assertStringContainsString(
            'aria-label="' . get_string('selectcell', 'qtype_columnconnector', $position) . '"',
            $this->currentoutput
        );
    }

    /**
     * Lahtri sisu (sh hüperlink) ei ole valikunupu sees.
     */
    public function test_cell_link_outside_select_button(): void {
        $q = test_question_maker::make_question('columnconnector', 'twocolumn');
        $q->columns[0]['cells'][0]['text'] = '<p>Vaata <a href="https://example.com/">linki</a></p>';
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);
        $this->render();
        $this->assertStringContainsString('href="https://example.com/"', $this->currentoutput);
        // Nupud ei sisalda linki ega muud sisu: valikunupud on tühjad.
        $this->assertDoesNotMatchRegularExpression('~<button[^>]*>(?:(?!</button>).)*<a\s~s', $this->currentoutput);
        $this->assertMatchesRegularExpression(
            '~<button type="button" class="cc-cell-select"[^>]*></button>~',
            $this->currentoutput
        );
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
        $q->questionbankentryid = 1;
        return $q;
    }

    /**
     * Ümberhindamine uue versiooniga: parandatud vastusevõti rakendub, ümberjärjestamine on keelatud.
     */
    public function test_regrade_with_other_version(): void {
        $q = $this->with_ids(test_question_maker::make_question('columnconnector', 'twocolumn'));
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);
        // Katses on ühendused Üks–2 ja Kaks–1 (esialgse võtme järgi vale).
        $this->process_submission($this->answer([0, 0, 1, 1], [0, 1, 1, 0]));
        $this->finish();
        $this->check_current_mark(0);

        // Uues versioonis on võti parandatud; lahtrid ja nende järjekord on samad.
        $fixed = $this->with_ids(test_question_maker::make_question('columnconnector', 'twocolumn'));
        $fixed->answerkey = [
            ['from' => ['col' => 0, 'row' => 0], 'to' => ['col' => 1, 'row' => 1]],
            ['from' => ['col' => 0, 'row' => 1], 'to' => ['col' => 1, 'row' => 0]],
        ];
        $this->assertNull($this->quba->validate_can_regrade_with_other_version($this->slot, $fixed));
        $this->quba->regrade_question($this->slot, true, null, $fixed);
        $this->check_current_mark(1);

        // Uues versioonis on teise tulba lahtrite järjekord muudetud: ümberhindamine on keelatud.
        $reordered = $this->with_ids(test_question_maker::make_question('columnconnector', 'twocolumn'));
        $reordered->columns[1]['cells'] = array_reverse($reordered->columns[1]['cells']);
        $this->assertEquals(
            get_string('regradeissuecellorderchanged', 'qtype_columnconnector'),
            $this->quba->validate_can_regrade_with_other_version($this->slot, $reordered)
        );
    }
}
