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
 * columnconnector varunduse tugi.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Varundab columnconnector küsimuse tüübispetsiifilised andmed.
 */
class backup_qtype_columnconnector_plugin extends backup_qtype_plugin {

    /**
     * @return backup_plugin_element
     */
    protected function define_question_plugin_structure() {

        $plugin = $this->get_plugin_element(null, '../../qtype', 'columnconnector');

        $pluginwrapper = new backup_nested_element($this->get_recommended_name());
        $plugin->add_child($pluginwrapper);

        $options = new backup_nested_element('columnconnector', ['id'], [
            'questionid', 'numcolumns', 'layoutmode', 'linestyle', 'showclear', 'showmissing',
            'correctpoints', 'incorrectpoints', 'missingpoints',
            'instructions', 'instructionsformat', 'content',
        ]);

        $pluginwrapper->add_child($options);

        $options->set_source_table('qtype_columnconnector', ['questionid' => backup::VAR_PARENTID]);

        // Lahtrite pildid on seotud küsimuse id-ga.
        $options->annotate_files('qtype_columnconnector', 'cellimages', 'questionid');

        return $plugin;
    }
}
