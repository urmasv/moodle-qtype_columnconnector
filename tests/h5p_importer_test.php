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

use qtype_columnconnector_h5p_importer;

/**
 * H5P.ColumnConnector impordi testid.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\qtype_columnconnector_h5p_importer::class)]
final class h5p_importer_test extends \advanced_testcase {
    /** @var string 1×1 PNG-pilt. */
    public const PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

    /**
     * H5P 2.0 vormingu teisendus.
     */
    public function test_convert_v2(): void {
        [$numcolumns, $model, $imagerefs] = qtype_columnconnector_h5p_importer::convert([
            'columns' => [
                ['title' => 'A', 'cells' => [
                    ['text' => 'a1'],
                    ['text' => 'a2', 'image' => ['file' => ['path' => 'images/x.png']]],
                ]],
                ['title' => 'B', 'cells' => [
                    ['text' => 'b1', 'correctToPrevious' => '2'],
                    ['text' => 'b2', 'correctToPrevious' => '1,2'],
                ]],
            ],
        ]);
        $this->assertEquals(2, $numcolumns);
        $this->assertEquals('A', $model['columns'][0]['title']);
        $this->assertCount(3, $model['answerkey']);
        $this->assertEquals([['col' => 0, 'row' => 1, 'path' => 'images/x.png']], $imagerefs);
    }

    /**
     * H5P 1.3 vormingu teisendus.
     */
    public function test_convert_v13(): void {
        [$numcolumns, $model] = qtype_columnconnector_h5p_importer::convert([
            'behaviour' => ['columns' => 2],
            'columnOne' => ['cells' => [['text' => 'a']]],
            'columnTwo' => ['cells' => [['text' => 'b', 'correctToColumnOne' => '1']]],
        ]);
        $this->assertEquals(2, $numcolumns);
        $this->assertEquals(
            [['from' => ['col' => 1, 'row' => 0], 'to' => ['col' => 0, 'row' => 0]]],
            $model['answerkey']
        );
    }

    /**
     * Paketiväliseid faile ei lubata.
     */
    public function test_resolve_package_file_rejects_traversal(): void {
        $dir = make_request_directory();
        mkdir($dir . '/content/images', 0777, true);
        file_put_contents($dir . '/content/images/ok.png', 'x');
        file_put_contents($dir . '/secret.txt', 'secret');

        $this->assertEquals(
            realpath($dir . '/content/images/ok.png'),
            qtype_columnconnector_h5p_importer::resolve_package_file($dir . '/content', 'images/ok.png')
        );
        $this->assertNull(qtype_columnconnector_h5p_importer::resolve_package_file($dir . '/content', '../secret.txt'));
        $this->assertNull(qtype_columnconnector_h5p_importer::resolve_package_file($dir . '/content', 'images/../../secret.txt'));
        $this->assertNull(qtype_columnconnector_h5p_importer::resolve_package_file($dir . '/content', '/etc/passwd'));
        $this->assertNull(qtype_columnconnector_h5p_importer::resolve_package_file($dir . '/content', 'images/missing.png'));
        $this->assertNull(qtype_columnconnector_h5p_importer::resolve_package_file($dir . '/content', ''));
    }

    /**
     * Täielik import: paketisisene pilt imporditakse, paketiväline fail mitte.
     */
    public function test_import_stored_file(): void {
        global $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        $content = [
            'instructions' => '<p>Juhis</p>',
            'columns' => [
                ['title' => 'A', 'cells' => [
                    ['text' => 'a1', 'image' => ['file' => ['path' => 'images/ok.png']]],
                    ['text' => 'a2', 'image' => ['file' => ['path' => '../h5p.json']]],
                    ['text' => 'a3', 'image' => ['file' => ['path' => 'images/../../h5p.json']]],
                ]],
                ['title' => 'B', 'cells' => [['text' => 'b1', 'correctToPrevious' => '1']]],
            ],
        ];
        $zippath = make_request_directory() . '/test.h5p';
        $zip = new \ZipArchive();
        $zip->open($zippath, \ZipArchive::CREATE);
        $zip->addFromString('h5p.json', json_encode(['mainLibrary' => 'H5P.ColumnConnector']));
        $zip->addFromString('content/content.json', json_encode($content));
        $zip->addFromString('content/images/ok.png', base64_decode(self::PNG));
        $zip->close();

        $usercontext = \context_user::instance($USER->id);
        $fs = get_file_storage();
        $uploaddraft = file_get_unused_draft_itemid();
        $file = $fs->create_file_from_pathname([
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => $uploaddraft,
            'filepath' => '/',
            'filename' => 'test.h5p',
        ], $zippath);

        $imagesdraft = file_get_unused_draft_itemid();
        [$numcolumns, $contentjson, $instructions] = qtype_columnconnector_h5p_importer::import_stored_file(
            $file,
            $imagesdraft,
            \context_system::instance()
        );

        $this->assertEquals(2, $numcolumns);
        $this->assertEquals('<p>Juhis</p>', $instructions);
        $model = json_decode($contentjson, true);
        $this->assertEquals('ok.png', $model['columns'][0]['cells'][0]['image']);
        $this->assertSame('', $model['columns'][0]['cells'][1]['image']);
        $this->assertSame('', $model['columns'][0]['cells'][2]['image']);

        $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $imagesdraft, 'filename', false);
        $this->assertEquals(['ok.png'], array_values(array_map(fn($f) => $f->get_filename(), $files)));
    }

    /**
     * Vale sisutüübiga pakett lükatakse tagasi.
     */
    public function test_import_wrong_type(): void {
        global $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        $zippath = make_request_directory() . '/other.h5p';
        $zip = new \ZipArchive();
        $zip->open($zippath, \ZipArchive::CREATE);
        $zip->addFromString('h5p.json', json_encode(['mainLibrary' => 'H5P.MultiChoice']));
        $zip->addFromString('content/content.json', '{}');
        $zip->close();

        $file = get_file_storage()->create_file_from_pathname([
            'contextid' => \context_user::instance($USER->id)->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => file_get_unused_draft_itemid(),
            'filepath' => '/',
            'filename' => 'other.h5p',
        ], $zippath);

        $this->expectException(\moodle_exception::class);
        qtype_columnconnector_h5p_importer::import_stored_file($file, file_get_unused_draft_itemid(), \context_system::instance());
    }

    /**
     * Loo .h5p pakett kasutaja mustandialasse.
     *
     * @param array $content content.json sisu
     * @param array $files lisafailid: tee arhiivis => sisu
     * @return \stored_file
     */
    protected function create_package(array $content, array $files = []): \stored_file {
        global $USER;
        $zippath = make_request_directory() . '/test.h5p';
        $zip = new \ZipArchive();
        $zip->open($zippath, \ZipArchive::CREATE);
        $zip->addFromString('h5p.json', json_encode(['mainLibrary' => 'H5P.ColumnConnector']));
        $zip->addFromString('content/content.json', json_encode($content));
        foreach ($files as $name => $data) {
            $zip->addFromString($name, $data);
        }
        $zip->close();
        return get_file_storage()->create_file_from_pathname([
            'contextid' => \context_user::instance($USER->id)->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => file_get_unused_draft_itemid(),
            'filepath' => '/',
            'filename' => 'test.h5p',
        ], $zippath);
    }

    /**
     * Kahe lahtriga sisu, mille esimesele lahtrile on viidatud pilt.
     *
     * @param string $imagepath
     * @return array
     */
    protected function two_cell_content(string $imagepath = 'images/a.png'): array {
        return [
            'columns' => [
                ['title' => 'A', 'cells' => [['text' => 'a', 'image' => ['file' => ['path' => $imagepath]]]]],
                ['title' => 'B', 'cells' => [['text' => 'b', 'correctToPrevious' => '1']]],
            ],
        ];
    }

    /**
     * Käivita import ja tagasta veakood (või null, kui viga ei tekkinud).
     *
     * @param \stored_file $file
     * @return string|null
     */
    protected function import_error(\stored_file $file): ?string {
        try {
            qtype_columnconnector_h5p_importer::import_stored_file(
                $file,
                file_get_unused_draft_itemid(),
                \context_system::instance()
            );
        } catch (\moodle_exception $e) {
            return $e->errorcode;
        }
        return null;
    }

    /**
     * Mahupiirang tuleb saidi sättest moodlecourse | maxbytes.
     */
    public function test_get_max_bytes_uses_course_default_maxbytes(): void {
        global $CFG;
        $this->resetAfterTest();
        set_config('maxbytes', 1024, 'moodlecourse');
        $this->assertEquals(1024, qtype_columnconnector_h5p_importer::get_max_bytes());
        // Väärtus 0 tähendab saidi üleslaadimispiirangut.
        set_config('maxbytes', 0, 'moodlecourse');
        $this->assertEquals(
            get_max_upload_file_size($CFG->maxbytes),
            qtype_columnconnector_h5p_importer::get_max_bytes()
        );
    }

    /**
     * Üleslaaditud fail ei tohi ületada mahupiirangut.
     */
    public function test_import_file_too_large(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $file = $this->create_package($this->two_cell_content(), ['content/images/a.png' => base64_decode(self::PNG)]);
        set_config('maxbytes', $file->get_filesize() - 1, 'moodlecourse');
        $this->assertEquals('h5pimporttoolarge', $this->import_error($file));
        set_config('maxbytes', $file->get_filesize() + 10000, 'moodlecourse');
        $this->assertNull($this->import_error($file));
    }

    /**
     * Lahtipakitav kogumaht ei tohi ületada mahupiirangut (hästi pakkuv suur pilt).
     */
    public function test_import_unpacked_too_large(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $file = $this->create_package($this->two_cell_content(), ['content/images/a.png' => str_repeat("\0", 200000)]);
        $this->assertLessThan(20000, $file->get_filesize());
        set_config('maxbytes', 20000, 'moodlecourse');
        $this->assertEquals('h5pimportunpackedtoolarge', $this->import_error($file));
    }

    /**
     * content-kaustas tohib olla kuni lahtrite arv + 1 faili.
     */
    public function test_import_too_many_files(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $png = base64_decode(self::PNG);
        // Kaks lahtrit: lubatud 3 sisufaili.
        $ok = $this->create_package($this->two_cell_content(), [
            'content/images/a.png' => $png,
            'content/images/b.png' => $png,
            'content/images/c.png' => $png,
        ]);
        $this->assertNull($this->import_error($ok));
        $toomany = $this->create_package($this->two_cell_content(), [
            'content/images/a.png' => $png,
            'content/images/b.png' => $png,
            'content/images/c.png' => $png,
            'content/images/d.png' => $png,
        ]);
        $this->assertEquals('h5pimporttoomanyfiles', $this->import_error($toomany));
    }

    /**
     * Teekide failid (väljaspool content-kausta) ei lähe arvesse ega pakita lahti.
     */
    public function test_import_ignores_library_files(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $files = ['content/images/a.png' => base64_decode(self::PNG)];
        for ($i = 0; $i < 50; $i++) {
            $files['H5P.ColumnConnector-2.0/scripts/file' . $i . '.js'] = str_repeat('x', 1000);
        }
        $file = $this->create_package($this->two_cell_content(), $files);
        set_config('maxbytes', $file->get_filesize() + 5000, 'moodlecourse');
        $this->assertNull($this->import_error($file));
    }
}
