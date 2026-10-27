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

use qformat_xml;
use question_bank;
use question_possible_response;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/format/xml/format.php');
require_once($CFG->dirroot . '/question/type/columnconnector/questiontype.php');

/**
 * columnconnector küsimusetüübi klassi testid.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\qtype_columnconnector::class)]
final class question_type_test extends \advanced_testcase {
    /**
     * Loo testküsimus küsimustepanka.
     *
     * @param array $overrides vormiandmete ülekirjutused
     * @return \stdClass küsimuse andmed (question_bank::load_question_data)
     */
    protected function create_question(array $overrides = []): \stdClass {
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $course = $this->getDataGenerator()->create_course();
        // Testi mooduli kontekst sobib küsimustele nii Moodle 4.x kui ka 5.x-is.
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);
        $cat = $generator->create_question_category(['contextid' => \context_module::instance($quiz->cmid)->id]);
        $question = $generator->create_question('columnconnector', 'twocolumn', ['category' => $cat->id] + $overrides);
        return question_bank::load_question_data($question->id);
    }

    /**
     * Parsi XML (Moodle 5.1+ core\xml_parser, varasemates xmlize()).
     *
     * @param string $xml
     * @return array
     */
    protected function parse_xml(string $xml): array {
        global $CFG;
        if (class_exists(\core\xml_parser::class)) {
            return (new \core\xml_parser())->parse($xml);
        }
        require_once($CFG->libdir . '/xmlize.php');
        return xmlize($xml);
    }

    /**
     * Põhiomadused.
     */
    public function test_basic_properties(): void {
        $qtype = question_bank::get_qtype('columnconnector');
        $this->assertEquals('columnconnector', $qtype->name());
        $this->assertTrue($qtype->can_analyse_responses());
        $this->assertTrue($qtype->is_real_question_type());
    }

    /**
     * Salvestamine ja laadimine.
     */
    public function test_save_and_load(): void {
        $data = $this->create_question();
        $this->assertEquals(2, $data->options->numcolumns);
        $this->assertEquals(1, $data->options->showmissing);
        $content = json_decode($data->options->content, true);
        $this->assertCount(2, $content['columns']);
        $this->assertCount(2, $content['answerkey']);
        $this->assertFalse(property_exists($data->options, 'instructions'));
    }

    /**
     * Salvestamisel puhastatakse lahtri HTML, pildi-URL ja vigased ühendused.
     */
    public function test_save_cleans_content(): void {
        $content = [
            'columns' => [
                ['title' => '<b>A</b>', 'cells' => [
                    ['text' => '<p onclick="alert(1)">Üks<script>alert(2)</script></p>', 'imageurl' => 'javascript:alert(3)'],
                    ['text' => '<p>Kaks</p>', 'imageurl' => 'https://example.com/a.png'],
                ]],
                ['title' => 'B', 'cells' => [
                    ['text' => '<p>1</p>'],
                    ['text' => '<p>2</p>'],
                ]],
            ],
            'answerkey' => [
                ['from' => ['col' => 0, 'row' => 0], 'to' => ['col' => 1, 'row' => 0]],
                ['from' => ['col' => 0, 'row' => 1], 'to' => ['col' => 1, 'row' => 9]],
                ['from' => ['col' => 0, 'row' => 1], 'to' => ['col' => 2, 'row' => 0]],
            ],
        ];
        $data = $this->create_question(['contentjson' => json_encode($content)]);
        $saved = json_decode($data->options->content, true);

        $cell = $saved['columns'][0]['cells'][0];
        $this->assertStringNotContainsString('script', $cell['text']);
        $this->assertStringNotContainsString('onclick', $cell['text']);
        $this->assertStringContainsString('Üks', $cell['text']);
        $this->assertSame('', $cell['imageurl']);
        $this->assertSame('https://example.com/a.png', $saved['columns'][0]['cells'][1]['imageurl']);
        $this->assertSame('A', $saved['columns'][0]['title']);
        $this->assertEquals(
            [['from' => ['col' => 0, 'row' => 0], 'to' => ['col' => 1, 'row' => 0]]],
            $saved['answerkey']
        );
    }

    /**
     * Statistika võimalikud vastused: iga naaberlahtrite paar on osa.
     */
    public function test_get_possible_responses(): void {
        $data = $this->create_question();
        $responses = question_bank::get_qtype('columnconnector')->get_possible_responses($data);
        $this->assertCount(4, $responses);
        $connected = fn($a) => get_string('statsconnected', 'qtype_columnconnector', $a);
        $notconnected = fn($a) => get_string('statsnotconnected', 'qtype_columnconnector', $a);
        $this->assertEquals([
            'connected' => new question_possible_response($connected('Üks – 1'), 0.5),
            'notconnected' => new question_possible_response($notconnected('Üks – 1'), 0),
            null => question_possible_response::no_response(),
        ], $responses['0:0-1:0']);
        $this->assertEquals([
            'connected' => new question_possible_response($connected('Üks – 2'), -0.5),
            'notconnected' => new question_possible_response($notconnected('Üks – 2'), 0),
            null => question_possible_response::no_response(),
        ], $responses['0:0-1:1']);
    }

    /**
     * Uue küsimuse lahtrid saavad juhuslikud id-d; olemasolevad id-d säilivad.
     */
    public function test_cell_ids_assigned_and_preserved(): void {
        $data = $this->create_question();
        $content = json_decode($data->options->content, true);
        $ids = array_merge(
            array_column($content['columns'][0]['cells'], 'id'),
            array_column($content['columns'][1]['cells'], 'id')
        );
        foreach ($ids as $id) {
            $this->assertMatchesRegularExpression('/^n[a-z0-9]{12}$/', $id);
        }
        $this->assertCount(4, array_unique($ids));

        // Toimetaja saadab teisaldatud lahtrid koos id-ga; uus lahter on id-ta.
        $original = array_column($content['columns'][0]['cells'], 'id');
        $content['columns'][0]['cells'] = array_reverse($content['columns'][0]['cells']);
        $content['columns'][0]['cells'][] = ['id' => '', 'text' => 'Kolm'];
        $saved = question_bank::get_qtype('columnconnector')->build_content((object)[
            'numcolumns' => 2,
            'contentjson' => json_encode($content),
        ]);
        $ids = array_column($saved['columns'][0]['cells'], 'id');
        $this->assertEquals(array_reverse($original), array_slice($ids, 0, 2));
        $this->assertMatchesRegularExpression('/^n[a-z0-9]{12}$/', $ids[2]);
        $this->assertNotContains($ids[2], $original);
    }

    /**
     * Vanale sisule lisatakse id-d sisu põhjal: deterministlikult ja sisu järgi eristatavalt.
     */
    public function test_add_missing_cell_ids(): void {
        $this->resetAfterTest();
        $legacy = json_encode([
            'columns' => [
                ['title' => '', 'cells' => [['text' => 'a'], ['text' => 'b'], ['text' => 'a']]],
                ['title' => '', 'cells' => [['text' => 'a']]],
            ],
            'answerkey' => [],
        ]);
        $first = \qtype_columnconnector::add_missing_cell_ids($legacy);
        $this->assertSame($first, \qtype_columnconnector::add_missing_cell_ids($legacy));
        $decoded = json_decode($first, true);
        $ids = array_column($decoded['columns'][0]['cells'], 'id');
        $this->assertMatchesRegularExpression('/^h[0-9a-f]{16}$/', $ids[0]);
        $this->assertNotEquals($ids[0], $ids[1]);
        // Sama sisuga lahter samas tulbas saab järjekorranumbri.
        $this->assertEquals($ids[0] . '-2', $ids[2]);
        // Sama sisu teises tulbas annab sama id (id on unikaalne tulba piires).
        $this->assertEquals($ids[0], $decoded['columns'][1]['cells'][0]['id']);
        $this->assertSame($first, \qtype_columnconnector::add_missing_cell_ids($first));
        $this->assertSame('not json', \qtype_columnconnector::add_missing_cell_ids('not json'));
    }

    /**
     * Korrektse kujuga, kuid samas tulbas korduvad või vigase kujuga id-d parandatakse.
     */
    public function test_add_missing_cell_ids_fixes_duplicates(): void {
        $this->resetAfterTest();
        $json = json_encode([
            'columns' => [
                ['title' => '', 'cells' => [
                    ['id' => 'nabc', 'text' => 'A'],
                    ['id' => 'nabc', 'text' => 'B'],
                    ['id' => 'nabc', 'text' => 'C'],
                ]],
                ['title' => '', 'cells' => [['id' => 'nabc', 'text' => '1'], ['id' => 'n x', 'text' => '2']]],
            ],
            'answerkey' => [],
        ]);
        $fixed = \qtype_columnconnector::add_missing_cell_ids($json);
        $this->assertNotSame($json, $fixed);
        $this->assertSame($fixed, \qtype_columnconnector::add_missing_cell_ids($json));
        $columns = json_decode($fixed, true)['columns'];

        $ids = array_column($columns[0]['cells'], 'id');
        $this->assertEquals('nabc', $ids[0]);
        $this->assertCount(3, array_unique($ids));
        $this->assertMatchesRegularExpression('/^h[0-9a-f]{16}$/', $ids[1]);
        $this->assertMatchesRegularExpression('/^h[0-9a-f]{16}$/', $ids[2]);
        // Teises tulbas sama id on lubatud (unikaalsus tulba piires); vigase kujuga id puhastatakse
        // samamoodi nagu tavalisel salvestamisel (clean_cell_id).
        $this->assertEquals('nabc', $columns[1]['cells'][0]['id']);
        $this->assertEquals('nx', $columns[1]['cells'][1]['id']);
        // Parandatud sisu enam ei muudeta.
        $this->assertSame($fixed, \qtype_columnconnector::add_missing_cell_ids($fixed));
    }

    /**
     * Ehita küsimuse definitsioon antud sisuga (hindamise võrdluseks).
     *
     * @param string $contentjson
     * @return \qtype_columnconnector_question
     */
    protected function make_question_with_content(string $contentjson): \qtype_columnconnector_question {
        $q = \test_question_maker::make_question('columnconnector', 'twocolumn');
        $content = json_decode($contentjson, true);
        $q->columns = $content['columns'];
        $q->answerkey = $content['answerkey'];
        return $q;
    }

    /**
     * Uuendusskripti stsenaarium: eraldi teisendatud vanad versioonid [A, B] ja [B, A].
     */
    public function test_regrade_after_migration_detects_reorder(): void {
        $this->resetAfterTest();
        $legacy = fn(array $texts) => json_encode([
            'columns' => [
                ['title' => '', 'cells' => array_map(fn($t) => ['text' => $t], $texts)],
                ['title' => '', 'cells' => [['text' => '1'], ['text' => '2']]],
            ],
            'answerkey' => [['from' => ['col' => 0, 'row' => 0], 'to' => ['col' => 1, 'row' => 0]]],
        ]);
        // Kumbki versioon teisendatakse eraldi, nagu uuendusskript seda teeb.
        $v1 = $this->make_question_with_content(\qtype_columnconnector::add_missing_cell_ids($legacy(['A', 'B'])));
        $v2 = $this->make_question_with_content(\qtype_columnconnector::add_missing_cell_ids($legacy(['B', 'A'])));
        $v3 = $this->make_question_with_content(\qtype_columnconnector::add_missing_cell_ids($legacy(['A', 'B'])));

        $this->assertEquals(
            get_string('regradeissuecellorderchanged', 'qtype_columnconnector'),
            $v1->validate_can_regrade_with_other_version($v2)
        );
        $this->assertNull($v1->validate_can_regrade_with_other_version($v3));
    }

    /**
     * Varasema 1.2.1 asukohapõhised id-d (c0r0, c0r1) ei luba ümberjärjestatud versiooni ümberhindamist.
     */
    public function test_regrade_ignores_positional_ids(): void {
        $content = fn(array $texts) => json_encode([
            'columns' => [
                ['title' => '', 'cells' => [
                    ['id' => 'c0r0', 'text' => $texts[0]],
                    ['id' => 'c0r1', 'text' => $texts[1]],
                ]],
                ['title' => '', 'cells' => [['id' => 'c1r0', 'text' => '1'], ['id' => 'c1r1', 'text' => '2']]],
            ],
            'answerkey' => [['from' => ['col' => 0, 'row' => 0], 'to' => ['col' => 1, 'row' => 0]]],
        ]);
        $v1 = $this->make_question_with_content($content(['A', 'B']));
        $v2 = $this->make_question_with_content($content(['B', 'A']));
        $this->assertEquals(
            get_string('regradeissuecellorderchanged', 'qtype_columnconnector'),
            $v1->validate_can_regrade_with_other_version($v2)
        );
    }

    /**
     * Uued versioonid päris salvestusteega: toimetamine, teisaldamine ja sisu asendamine (H5P-import).
     */
    public function test_regrade_across_saved_versions(): void {
        $data = $this->create_question();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $v1 = question_bank::load_question($data->id);
        $content = json_decode($data->options->content, true);

        // Teksti parandamine, id-d säilivad: ümberhindamine lubatud.
        $edited = $content;
        $edited['columns'][0]['cells'][0]['text'] = '<p>Üks (parandatud)</p>';
        $v2 = $generator->update_question($data, null, ['contentjson' => json_encode($edited)]);
        $this->assertNull($v1->validate_can_regrade_with_other_version(question_bank::load_question($v2->id)));

        // Lahtrite teisaldamine toimetajas (id-d liiguvad kaasa): keelatud.
        $moved = $content;
        $moved['columns'][1]['cells'] = array_reverse($moved['columns'][1]['cells']);
        $v3 = $generator->update_question($data, null, ['contentjson' => json_encode($moved)]);
        $this->assertEquals(
            get_string('regradeissuecellorderchanged', 'qtype_columnconnector'),
            $v1->validate_can_regrade_with_other_version(question_bank::load_question($v3->id))
        );

        // Sisu asendamine sama mõõdu H5P-impordiga (lahtritel pole id-sid): keelatud.
        $imported = $content;
        foreach ($imported['columns'] as $c => $column) {
            foreach (array_keys($column['cells']) as $r) {
                unset($imported['columns'][$c]['cells'][$r]['id']);
                $imported['columns'][$c]['cells'][$r]['text'] = 'Uus ' . $c . $r;
            }
        }
        $v4 = $generator->update_question($data, null, ['contentjson' => json_encode($imported)]);
        $this->assertEquals(
            get_string('regradeissuecellorderchanged', 'qtype_columnconnector'),
            $v1->validate_can_regrade_with_other_version(question_bank::load_question($v4->id))
        );
    }

    /**
     * Juhusliku pakkumise skoori ei saa põhjendatult hinnata.
     */
    public function test_get_random_guess_score(): void {
        $data = $this->create_question();
        $this->assertNull(question_bank::get_qtype('columnconnector')->get_random_guess_score($data));
    }

    /**
     * Ühine valideerimine: vigased punktid ja tühi vastusevõti.
     */
    public function test_validate_question_data(): void {
        $this->resetAfterTest();
        $content = ['columns' => [['cells' => [['text' => 'a']]], ['cells' => [['text' => '1']]]], 'answerkey' => [
            ['from' => ['col' => 0, 'row' => 0], 'to' => ['col' => 1, 'row' => 0]],
        ]];
        $this->assertSame([], \qtype_columnconnector::validate_question_data(2, 1, -1, 0, $content));
        $errors = \qtype_columnconnector::validate_question_data(2, 0, 1, 0.5, ['answerkey' => []] + $content);
        $this->assertEquals(['correctpoints', 'incorrectpoints', 'missingpoints', 'numcolumns'], array_keys($errors));
        $this->assertEquals(get_string('errornoanswerkey', 'qtype_columnconnector'), $errors['numcolumns']);
        $this->assertArrayHasKey('numcolumns', \qtype_columnconnector::validate_question_data(9, 1, -1, 0, $content));
    }

    /**
     * Vigase hindamisseadistusega XML-import lükatakse veateatega tagasi.
     */
    public function test_xml_import_rejects_invalid_scoring(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);
        $context = \context_module::instance($quiz->cmid);
        $category = $this->getDataGenerator()->get_plugin_generator('core_question')
            ->create_question_category(['contextid' => $context->id]);

        $content = json_encode([
            'columns' => [['title' => '', 'cells' => [['text' => 'a']]], ['title' => '', 'cells' => [['text' => '1']]]],
            'answerkey' => [['from' => ['col' => 0, 'row' => 0], 'to' => ['col' => 1, 'row' => 0]]],
        ]);
        $xml = fn($points, $content) => '<?xml version="1.0" encoding="UTF-8"?><quiz>
  <question type="columnconnector">
    <name><text>Vigane</text></name>
    <questiontext format="html"><text>Ühenda</text></questiontext>
    <numcolumns>2</numcolumns>
    <correctpoints>' . $points[0] . '</correctpoints>
    <incorrectpoints>' . $points[1] . '</incorrectpoints>
    <missingpoints>0</missingpoints>
    <content><text><![CDATA[' . $content . ']]></text></content>
  </question></quiz>';

        $cases = [
            'vale ühendus annab punkte' => $xml([1, 1], $content),
            'õige ühendus 0 punkti' => $xml([0, -1], $content),
            'vastusevõti tühi pärast puhastamist' => $xml([1, -1], str_replace('"row":0}}', '"row":7}}', $content)),
            // 9 täidetud tulpa: varem piirati vaikselt 7-ni ja kaks tulpa kadusid veateateta.
            'tulpade arv väljaspool lubatud vahemikku' => str_replace(
                '<numcolumns>2</numcolumns>',
                '<numcolumns>9</numcolumns>',
                $xml([1, -1], json_encode([
                    'columns' => array_fill(0, 9, ['title' => '', 'cells' => [['text' => 'x']]]),
                    'answerkey' => [['from' => ['col' => 0, 'row' => 0], 'to' => ['col' => 1, 'row' => 0]]],
                ]))
            ),
        ];
        foreach ($cases as $name => $data) {
            $before = $DB->count_records('question', ['qtype' => 'columnconnector']);
            $file = make_request_directory() . '/import.xml';
            file_put_contents($file, $data);
            $qformat = new qformat_xml();
            $qformat->setCategory($category);
            $qformat->setContexts([$context]);
            $qformat->setCourse($course);
            $qformat->setFilename($file);
            $qformat->setRealfilename('import.xml');
            $qformat->setMatchgrades('error');
            $qformat->setCatfromfile(false);
            $qformat->setContextfromfile(false);
            $qformat->setStoponerror(true);
            ob_start();
            $ok = $qformat->importpreprocess() && $qformat->importprocess();
            $output = ob_get_clean();
            $this->assertFalse($ok, $name);
            $this->assertStringContainsString('Vigane', $output, $name);
            $this->assertEquals($before, $DB->count_records('question', ['qtype' => 'columnconnector']), $name);
        }
    }

    /**
     * Moodle XML eksport ja import säilitavad sisu.
     */
    public function test_xml_round_trip(): void {
        $data = $this->create_question();
        $exporter = new qformat_xml();
        $xml = $exporter->writequestion($data);
        $this->assertStringNotContainsString('<instructions>', $xml);

        $importer = new qformat_xml();
        $parsed = $this->parse_xml('<quiz>' . $xml . '</quiz>');
        $qo = $importer->try_importing_using_qtypes($parsed['quiz']['#']['question'][0], null, null, 'columnconnector');
        $this->assertEquals('columnconnector', $qo->qtype);
        $this->assertEquals(2, $qo->numcolumns);
        $this->assertEquals(1, $qo->showmissing);
        $this->assertEquals(json_decode($data->options->content, true), json_decode($qo->content, true));
    }

    /**
     * Varasemate versioonide juhis imporditakse küsimuse tekstiks, kui see on tühi.
     */
    public function test_xml_import_legacy_instructions(): void {
        $xml = '<question type="columnconnector">
    <name><text>Vana</text></name>
    <questiontext format="html"><text><![CDATA[<p></p>]]></text></questiontext>
    <generalfeedback format="html"><text></text></generalfeedback>
    <numcolumns>2</numcolumns>
    <instructionsformat>1</instructionsformat>
    <instructions><text><![CDATA[<p>Ühenda paarid.</p>]]></text></instructions>
    <content><text><![CDATA[{"columns":[],"answerkey":[]}]]></text></content>
  </question>';
        $importer = new qformat_xml();
        $parsed = $this->parse_xml($xml);
        $qo = $importer->try_importing_using_qtypes($parsed['question'], null, null, 'columnconnector');
        $this->assertEquals('<p>Ühenda paarid.</p>', $qo->questiontext);
        $this->assertEquals(FORMAT_HTML, $qo->questiontextformat);
    }

    /**
     * Imporditud vastusevõtmest jäetakse välja ühendused puuduvate lahtriteni.
     */
    public function test_import_prunes_invalid_rows(): void {
        $content = [
            'columns' => [
                ['title' => '', 'cells' => [['text' => 'a'], ['text' => 'b']]],
                ['title' => '', 'cells' => [['text' => '1']]],
            ],
            'answerkey' => [
                ['from' => ['col' => 0, 'row' => 0], 'to' => ['col' => 1, 'row' => 0]],
                ['from' => ['col' => 0, 'row' => 1], 'to' => ['col' => 1, 'row' => 3]],
            ],
        ];
        // Impordil tuleb sisu väljal 'content', mitte 'contentjson'.
        $data = $this->create_question(['contentjson' => '', 'content' => json_encode($content)]);
        $saved = json_decode($data->options->content, true);
        $this->assertCount(1, $saved['answerkey']);
    }
}
