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

/**
 * Taastab columnconnector küsimuse tüübispetsiifilised andmed.
 */
class restore_qtype_columnconnector_plugin extends restore_qtype_plugin {

    /**
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
     * @param array $data
     */
    public function process_columnconnector($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $questioncreated = $this->get_mappingid('question_created',
            $this->get_old_parentid('question'));

        if (!$questioncreated) {
            return;
        }
        $data->questionid = $this->get_new_parentid('question');
        $newitemid = $DB->insert_record('qtype_columnconnector', $data);
        $this->set_mapping('qtype_columnconnector', $oldid, $newitemid);
    }

    /**
     * Taasta lahtrite piltide failid (seotud küsimuse id-ga).
     */
    protected function after_execute_question() {
        $this->add_related_files('qtype_columnconnector', 'cellimages', 'question');
    }

    /**
     * Väljad, mis jäetakse identiteedihäshi arvutusest välja (MDL-83541).
     *
     * @return array
     */
    protected function define_excluded_identity_hash_fields(): array {
        return [
            '/id',
            '/questionid',
        ];
    }
}
