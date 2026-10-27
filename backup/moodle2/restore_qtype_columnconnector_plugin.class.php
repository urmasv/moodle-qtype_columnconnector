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
 * columnconnector taastamise tugi.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/columnconnector/questiontype.php');

/**
 * Taastab columnconnector küsimuse tüübispetsiifilised andmed.
 */
class restore_qtype_columnconnector_plugin extends restore_qtype_plugin {
    /**
     * Kirjelda taastatavad rajad.
     *
     * @return restore_path_element[]
     */
    protected function define_question_plugin_structure() {
        $paths = [];
        $elename = 'columnconnector';
        $elepath = $this->get_pathfor('/columnconnector');
        $paths[] = new restore_path_element($elename, $elepath);
        return $paths;
    }

    /**
     * Töötle columnconnector kirje taastamisel.
     *
     * Versiooni 1.1 varundustes võib olla eraldi väli "instructions". Kui küsimuse
     * tekst on tühi, tõstetakse juhis küsimuse tekstiks (nagu uuendusskriptis).
     *
     * @param array $data
     */
    public function process_columnconnector($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $questioncreated = $this->get_mappingid('question_created', $this->get_old_parentid('question'));

        if (!$questioncreated) {
            return;
        }
        $data->questionid = $this->get_new_parentid('question');

        if (isset($data->instructions) && !html_is_blank($data->instructions)) {
            $questiontext = $DB->get_field('question', 'questiontext', ['id' => $data->questionid]);
            if (html_is_blank((string)$questiontext)) {
                $DB->update_record('question', (object)[
                    'id' => $data->questionid,
                    'questiontext' => $data->instructions,
                    'questiontextformat' => (int)($data->instructionsformat ?? FORMAT_HTML),
                ]);
            }
        }
        unset($data->instructions, $data->instructionsformat);

        // Vanemates varundustes puuduvad lahtrite id-d (sama teisendus, mis uuendusskriptis).
        $data->content = qtype_columnconnector::add_missing_cell_ids($data->content ?? null);

        $newitemid = $DB->insert_record('qtype_columnconnector', $data);
        $this->set_mapping('qtype_columnconnector', $oldid, $newitemid);
    }

    /**
     * Teisenda varunduse andmed küsimuse andmestruktuuriks identiteedi võrdluse jaoks.
     *
     * Moodle võrdleb taastamisel varunduse andmeid olemasolevate küsimustega, et vältida
     * duplikaate. Vanemate (1.1) varunduste andmed viiakse samale kujule, mille
     * process_columnconnector() ja uuendusskript andmebaasi salvestavad: lahtrid saavad
     * id-d ja tühja küsimuse teksti asemele tuleb juhis.
     *
     * @param array $backupdata
     * @return stdClass
     */
    public static function convert_backup_to_questiondata(array $backupdata): stdClass {
        $questiondata = parent::convert_backup_to_questiondata($backupdata);
        $options = $questiondata->options ?? new stdClass();

        if (isset($options->content)) {
            $options->content = qtype_columnconnector::add_missing_cell_ids($options->content);
        }
        if (
            isset($options->instructions) && !html_is_blank($options->instructions)
                && html_is_blank((string)($questiondata->questiontext ?? ''))
        ) {
            $questiondata->questiontext = $options->instructions;
            $questiondata->questiontextformat = (int)($options->instructionsformat ?? FORMAT_HTML);
        }
        unset($options->instructions, $options->instructionsformat);
        $questiondata->options = $options;

        return $questiondata;
    }

    /**
     * Eemalda identiteedi räsist väljad, mis ei kuulu küsimuse sisulisse definitsiooni.
     *
     * Lisaks tuuma välistustele eemaldatakse sisu-JSON-ist lahtrite id-d: need on
     * küsimusepõhised tehnilised tunnused (uued lahtrid saavad juhusliku id). Ilma selleta
     * oleksid kaks muidu identset küsimust taastamisel erinevad ja vana varunduse sisu ei
     * vastaks uuendatud küsimusele.
     *
     * @param stdClass $questiondata
     * @param array $excludefields
     * @return stdClass
     */
    public static function remove_excluded_question_data(stdClass $questiondata, array $excludefields = []): stdClass {
        $questiondata = parent::remove_excluded_question_data($questiondata, $excludefields);
        if (isset($questiondata->options) && is_object($questiondata->options) && isset($questiondata->options->content)) {
            // Räsi arvutatakse koopiast, kuid options on jagatud objekt: muuda selle koopiat.
            $questiondata->options = clone $questiondata->options;
            $questiondata->options->content = qtype_columnconnector::strip_cell_ids($questiondata->options->content);
        }
        return $questiondata;
    }

    /**
     * Väljad, mis jäetakse identiteedi räsist välja.
     *
     * Väljad id ja questionid välistab tuum ise. Versiooni 1.1 varundustes olevaid
     * juhise välju küsimuse andmetes enam pole, seega jäetakse need võrdlusest välja.
     *
     * @return array
     */
    protected function define_excluded_identity_hash_fields(): array {
        return [
            '/options/instructions',
            '/options/instructionsformat',
        ];
    }
}
