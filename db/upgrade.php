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
 * columnconnector uuendusskript.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Uuenda qtype_columnconnector andmebaasi skeem.
 *
 * @param int $oldversion varasem paigaldatud versioon
 * @return bool
 */
function xmldb_qtype_columnconnector_upgrade($oldversion) {
    global $CFG, $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2026061500) {
        $table = new xmldb_table('qtype_columnconnector');

        // Kirjelda kõik oodatavad veerud (nimi => [tüüp, pikkus, notnull, vaikeväärtus, eelnev]).
        $fielddefs = [
            ['id', XMLDB_TYPE_INTEGER, '10', XMLDB_NOTNULL, null, null, true],
            ['questionid', XMLDB_TYPE_INTEGER, '10', XMLDB_NOTNULL, null, 'id', false],
            ['numcolumns', XMLDB_TYPE_INTEGER, '4', XMLDB_NOTNULL, '2', 'questionid', false],
            ['layoutmode', XMLDB_TYPE_CHAR, '16', XMLDB_NOTNULL, 'columns', 'numcolumns', false],
            ['linestyle', XMLDB_TYPE_CHAR, '16', XMLDB_NOTNULL, 'curved', 'layoutmode', false],
            ['showclear', XMLDB_TYPE_INTEGER, '1', XMLDB_NOTNULL, '1', 'linestyle', false],
            ['showmissing', XMLDB_TYPE_INTEGER, '1', XMLDB_NOTNULL, '0', 'showclear', false],
            ['correctpoints', XMLDB_TYPE_NUMBER, '10, 5', XMLDB_NOTNULL, '1', 'showmissing', false],
            ['incorrectpoints', XMLDB_TYPE_NUMBER, '10, 5', XMLDB_NOTNULL, '-1', 'correctpoints', false],
            ['missingpoints', XMLDB_TYPE_NUMBER, '10, 5', XMLDB_NOTNULL, '0', 'incorrectpoints', false],
            ['instructions', XMLDB_TYPE_TEXT, null, null, null, 'missingpoints', false],
            ['instructionsformat', XMLDB_TYPE_INTEGER, '2', XMLDB_NOTNULL, '1', 'instructions', false],
            ['content', XMLDB_TYPE_TEXT, null, null, null, 'instructionsformat', false],
        ];

        if (!$dbman->table_exists($table)) {
            // Tabelit pole (nt varasem paigaldus jättis selle loomata) — loo täielikult.
            foreach ($fielddefs as $d) {
                [$name, $type, $length, $notnull, $default, $previous, $sequence] = $d;
                $table->add_field(
                    $name,
                    $type,
                    $length,
                    null,
                    $notnull ? XMLDB_NOTNULL : null,
                    $sequence ? XMLDB_SEQUENCE : null,
                    $default
                );
            }
            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $table->add_key('questionid', XMLDB_KEY_FOREIGN_UNIQUE, ['questionid'], 'question', ['id']);
            $dbman->create_table($table);
        } else {
            // Tabel on olemas — taga, et iga veerg eksisteerib (parandab skeemitriivi).
            foreach ($fielddefs as $d) {
                [$name, $type, $length, $notnull, $default, $previous, $sequence] = $d;
                if ($name === 'id') {
                    continue;
                }
                $field = new xmldb_field(
                    $name,
                    $type,
                    $length,
                    null,
                    $notnull ? XMLDB_NOTNULL : null,
                    null,
                    $default,
                    $previous
                );
                if (!$dbman->field_exists($table, $field)) {
                    $dbman->add_field($table, $field);
                }
            }
        }

        upgrade_plugin_savepoint(true, 2026061500, 'qtype', 'columnconnector');
    }

    if ($oldversion < 2026061700) {
        // Lisa veerg 'showmissing' (puuduvate ühenduste kuvamine), kui puudub.
        $table = new xmldb_table('qtype_columnconnector');
        $field = new xmldb_field(
            'showmissing',
            XMLDB_TYPE_INTEGER,
            '1',
            null,
            XMLDB_NOTNULL,
            null,
            '0',
            'showclear'
        );
        if ($dbman->table_exists($table) && !$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2026061700, 'qtype', 'columnconnector');
    }

    if ($oldversion < 2026102700) {
        // Eraldi juhise väli eemaldatakse: juhis kuulub küsimuse teksti.
        // Kui küsimuse tekst on tühi ja juhis mitte, tõstetakse juhis küsimuse tekstiks.
        $table = new xmldb_table('qtype_columnconnector');
        $instructions = new xmldb_field('instructions');
        if ($dbman->table_exists($table) && $dbman->field_exists($table, $instructions)) {
            $hasformat = $dbman->field_exists($table, new xmldb_field('instructionsformat'));
            $formatsql = $hasformat ? 'cc.instructionsformat' : FORMAT_HTML;
            $rs = $DB->get_recordset_sql(
                "SELECT cc.questionid, cc.instructions, {$formatsql} AS instructionsformat, q.questiontext
                   FROM {qtype_columnconnector} cc
                   JOIN {question} q ON q.id = cc.questionid
                  WHERE cc.instructions IS NOT NULL"
            );
            foreach ($rs as $record) {
                if (html_is_blank($record->instructions) || !html_is_blank($record->questiontext)) {
                    continue;
                }
                $DB->update_record('question', (object)[
                    'id' => $record->questionid,
                    'questiontext' => $record->instructions,
                    'questiontextformat' => (int)$record->instructionsformat,
                ]);
            }
            $rs->close();
            $dbman->drop_field($table, $instructions);
        }

        $instructionsformat = new xmldb_field('instructionsformat');
        if ($dbman->table_exists($table) && $dbman->field_exists($table, $instructionsformat)) {
            $dbman->drop_field($table, $instructionsformat);
        }

        upgrade_plugin_savepoint(true, 2026102700, 'qtype', 'columnconnector');
    }

    if ($oldversion < 2026102701) {
        // Anna olemasolevate küsimuste lahtritele püsivad id-d (ümberhindamise kontrolli jaoks).
        require_once($CFG->dirroot . '/question/type/columnconnector/questiontype.php');
        $rs = $DB->get_recordset('qtype_columnconnector', null, 'id', 'id, content');
        foreach ($rs as $record) {
            $content = qtype_columnconnector::add_missing_cell_ids($record->content);
            if ($content !== $record->content) {
                $DB->set_field('qtype_columnconnector', 'content', $content, ['id' => $record->id]);
            }
        }
        $rs->close();

        upgrade_plugin_savepoint(true, 2026102701, 'qtype', 'columnconnector');
    }

    return true;
}
