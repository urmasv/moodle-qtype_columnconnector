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
 * Privaatsuse pakkuja qtype_columnconnector jaoks.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_columnconnector\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Küsimusetüüp salvestab ainult kasutaja eelistuse (uue küsimuse vaikimisi hinne),
 * mille paneb paika question_type::save_defaults_for_new_questions().
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\user_preference_provider {

    /**
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_user_preference('qtype_columnconnector_defaultmark', 'privacy:preference:defaultmark');
        return $collection;
    }

    /**
     * Ekspordi kasutaja eelistused.
     *
     * @param int $userid
     */
    public static function export_user_preferences(int $userid) {
        $preference = get_user_preferences('qtype_columnconnector_defaultmark', null, $userid);
        if (null !== $preference) {
            $desc = get_string('privacy:preference:defaultmark', 'qtype_columnconnector');
            writer::export_user_preference('qtype_columnconnector', 'defaultmark', $preference, $desc);
        }
    }
}
