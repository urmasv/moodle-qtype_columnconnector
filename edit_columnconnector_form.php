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
 * columnconnector küsimuse toimetamise vorm.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/columnconnector/question.php');

/**
 * columnconnector küsimuse toimetamise vorm.
 *
 * Tulpi, lahtreid ja õigeid ühendusi toimetatakse visuaalse toimetajaga
 * (AMD-moodul qtype_columnconnector/editor), mis kirjutab kokku pandud mudeli
 * peidetud väljale. Õpetaja JSON-i ei näe.
 */
class qtype_columnconnector_edit_form extends question_edit_form {

    /**
     * @return string küsimusetüübi nimi
     */
    public function qtype() {
        return 'columnconnector';
    }

    /**
     * Lisa tüübispetsiifilised väljad.
     *
     * @param MoodleQuickForm $mform
     */
    protected function definition_inner($mform) {
        global $PAGE;

        // --- Visuaalne toimetaja ÜLEVAL: tulbad, lahtrid, õiged ühendused. ---
        $mform->addElement('header', 'columnconnectorcontent',
            get_string('contenteditor', 'qtype_columnconnector'));
        $mform->setExpanded('columnconnectorcontent');

        // Impordi olemasolevast H5P.ColumnConnector failist (vormisisene, ilma AJAX-ita).
        $mform->addElement('filepicker', 'h5pimport',
            get_string('h5pimport', 'qtype_columnconnector'), null,
            ['accepted_types' => ['.h5p'], 'maxbytes' => 0]);
        $mform->addHelpButton('h5pimport', 'h5pimport', 'qtype_columnconnector');
        $mform->registerNoSubmitButton('loadh5p');
        $mform->addElement('submit', 'loadh5p',
            get_string('h5pimportload', 'qtype_columnconnector'));

        $mform->addElement('select', 'numcolumns',
            get_string('numcolumns', 'qtype_columnconnector'),
            ['2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7']);
        $mform->setDefault('numcolumns', 2);
        $mform->addHelpButton('numcolumns', 'numcolumns', 'qtype_columnconnector');

        // Lahtrite piltide üleslaadimine (H5P image-välja vaste).
        $mform->addElement('filemanager', 'cellimages',
            get_string('cellimages', 'qtype_columnconnector'), null, [
                'subdirs' => 0,
                'maxfiles' => -1,
                'accepted_types' => ['web_image'],
            ]);
        $mform->addHelpButton('cellimages', 'cellimages', 'qtype_columnconnector');

        // Peidetud väli, kuhu toimetaja kirjutab kokku pandud mudeli.
        $mform->addElement('hidden', 'contentjson', '');
        $mform->setType('contentjson', PARAM_RAW);

        // Konteiner, kuhu AMD-toimetaja end renderdab.
        $mform->addElement('html',
            '<div id="cc-editor-root" class="cc-editor-root" data-cc-editor="1"></div>');

        $PAGE->requires->js_call_amd('qtype_columnconnector/editor', 'init', [[
            'rootSelector' => '#cc-editor-root',
            'inputId' => 'id_contentjson',
            'numColumnsId' => 'id_numcolumns',
            'cellImagesInputId' => 'id_cellimages',
            'strings' => [
                'columnTitle' => get_string('columntitle', 'qtype_columnconnector'),
                'cell' => get_string('cell', 'qtype_columnconnector'),
                'cellText' => get_string('celltext', 'qtype_columnconnector'),
                'imageUrl' => get_string('imageurl', 'qtype_columnconnector'),
                'imagePosition' => get_string('imageposition', 'qtype_columnconnector'),
                'imageAbove' => get_string('imageabove', 'qtype_columnconnector'),
                'imageLeft' => get_string('imageleft', 'qtype_columnconnector'),
                'imageLeftSize' => get_string('imageleftsize', 'qtype_columnconnector'),
                'sizeSmall' => get_string('sizesmall', 'qtype_columnconnector'),
                'sizeMedium' => get_string('sizemedium', 'qtype_columnconnector'),
                'sizeLarge' => get_string('sizelarge', 'qtype_columnconnector'),
                'imageAlignLeft' => get_string('imagealignleft', 'qtype_columnconnector'),
                'imageAlignCenter' => get_string('imagealigncenter', 'qtype_columnconnector'),
                'alt' => get_string('imagealt', 'qtype_columnconnector'),
                'addCell' => get_string('addcell', 'qtype_columnconnector'),
                'removeCell' => get_string('removecell', 'qtype_columnconnector'),
                'moveUp' => get_string('moveup', 'qtype_columnconnector'),
                'moveDown' => get_string('movedown', 'qtype_columnconnector'),
                'correctConnections' => get_string('correctconnections', 'qtype_columnconnector'),
                'noConnection' => get_string('noconnection', 'qtype_columnconnector'),
                'connectionCount' => get_string('connectioncount', 'qtype_columnconnector'),
                'noNeighbourCells' => get_string('noneighbourcells', 'qtype_columnconnector'),
                'cellFallback' => get_string('cellfallback', 'qtype_columnconnector'),
                'column' => get_string('column', 'qtype_columnconnector'),
                'uploadedImage' => get_string('uploadedimage', 'qtype_columnconnector'),
                'noImage' => get_string('noimage', 'qtype_columnconnector'),
                'refreshImages' => get_string('refreshimages', 'qtype_columnconnector'),
                'rteBold' => get_string('rtebold', 'qtype_columnconnector'),
                'rteItalic' => get_string('rteitalic', 'qtype_columnconnector'),
                'rteUnderline' => get_string('rteunderline', 'qtype_columnconnector'),
                'rteBullet' => get_string('rtebullet', 'qtype_columnconnector'),
                'rteNumbered' => get_string('rtenumbered', 'qtype_columnconnector'),
                'rteAlignLeft' => get_string('rtealignleft', 'qtype_columnconnector'),
                'rteAlignCenter' => get_string('rtealigncenter', 'qtype_columnconnector'),
                'rteAlignRight' => get_string('rtealignright', 'qtype_columnconnector'),
                'rteLink' => get_string('rtelink', 'qtype_columnconnector'),
                'rteLinkPrompt' => get_string('rtelinkprompt', 'qtype_columnconnector'),
                'rteClear' => get_string('rteclear', 'qtype_columnconnector'),
                'loadError' => get_string('editorloaderror', 'qtype_columnconnector'),
            ],
        ]]);

        // --- Seaded ALLPOOL. ---
        $mform->addElement('header', 'columnconnectorheader',
            get_string('columnconnectorsettings', 'qtype_columnconnector'));

        $mform->addElement('select', 'layoutmode',
            get_string('layoutmode', 'qtype_columnconnector'), [
                'columns' => get_string('layoutcolumns', 'qtype_columnconnector'),
                'rows' => get_string('layoutrows', 'qtype_columnconnector'),
            ]);
        $mform->setDefault('layoutmode', 'columns');

        $mform->addElement('select', 'linestyle',
            get_string('linestyle', 'qtype_columnconnector'), [
                'curved' => get_string('linecurved', 'qtype_columnconnector'),
                'straight' => get_string('linestraight', 'qtype_columnconnector'),
            ]);
        $mform->setDefault('linestyle', 'curved');

        $mform->addElement('advcheckbox', 'showclear',
            get_string('showclear', 'qtype_columnconnector'));
        $mform->setDefault('showclear', 1);

        $mform->addElement('advcheckbox', 'showmissing',
            get_string('showmissing', 'qtype_columnconnector'));
        $mform->setDefault('showmissing', 0);
        $mform->addHelpButton('showmissing', 'showmissing', 'qtype_columnconnector');

        $mform->addElement('text', 'correctpoints',
            get_string('correctpoints', 'qtype_columnconnector'), ['size' => 6]);
        $mform->setType('correctpoints', PARAM_FLOAT);
        $mform->setDefault('correctpoints', 1);
        $mform->addHelpButton('correctpoints', 'correctpoints', 'qtype_columnconnector');

        $mform->addElement('text', 'incorrectpoints',
            get_string('incorrectpoints', 'qtype_columnconnector'), ['size' => 6]);
        $mform->setType('incorrectpoints', PARAM_FLOAT);
        $mform->setDefault('incorrectpoints', -1);

        $mform->addElement('text', 'missingpoints',
            get_string('missingpoints', 'qtype_columnconnector'), ['size' => 6]);
        $mform->setType('missingpoints', PARAM_FLOAT);
        $mform->setDefault('missingpoints', 0);
    }

    /**
     * Valmista salvestatud andmed vormi jaoks ette.
     *
     * @param object $question
     * @return object
     */
    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);

        if (!empty($question->options)) {
            $options = $question->options;
            $question->numcolumns = $options->numcolumns;
            $question->layoutmode = $options->layoutmode;
            $question->linestyle = $options->linestyle;
            $question->showclear = $options->showclear;
            $question->correctpoints = $options->correctpoints;
            $question->incorrectpoints = $options->incorrectpoints;
            $question->missingpoints = $options->missingpoints;
            $question->showmissing = $options->showmissing ?? 0;
            $question->contentjson = $options->content ?? '';
        }

        // Valmista lahtrite piltide mustandiala ette (laadib olemasolevad failid).
        $draftitemid = file_get_submitted_draft_itemid('cellimages');
        file_prepare_draft_area($draftitemid, $this->context->id,
            'qtype_columnconnector', 'cellimages',
            !empty($question->id) ? (int)$question->id : null,
            ['subdirs' => 0, 'maxfiles' => -1, 'accepted_types' => ['web_image']]);
        $question->cellimages = $draftitemid;

        return $question;
    }

    /**
     * Käitle vormisisest „Laadi H5P-st" nuppu: loe .h5p, teisenda ja täida vorm.
     */
    public function definition_after_data() {
        parent::definition_after_data();
        $mform = $this->_form;

        if (!optional_param('loadh5p', false, PARAM_RAW)) {
            return;
        }

        global $USER;
        $draftid = file_get_submitted_draft_itemid('h5pimport');
        if (!$draftid) {
            return;
        }
        $usercontext = context_user::instance($USER->id);
        $fs = get_file_storage();
        $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftid, 'id', false);
        $file = $files ? reset($files) : null;
        if (!$file) {
            return;
        }

        // Pildid salvestatakse samasse lahtripiltide mustandialasse, mida failihaldur kasutab.
        $cellimagesdraft = file_get_submitted_draft_itemid('cellimages');

        try {
            list($numcolumns, $contentjson) = qtype_columnconnector_h5p_importer::import_stored_file(
                $file, $cellimagesdraft, $this->context);
        } catch (moodle_exception $e) {
            $mform->setElementError('h5pimport', $e->getMessage());
            return;
        }

        if (!empty($numcolumns) && $mform->elementExists('numcolumns')) {
            $mform->getElement('numcolumns')->setValue((string)$numcolumns);
        }
        if ($mform->elementExists('contentjson')) {
            $mform->getElement('contentjson')->setValue($contentjson);
        }
        // Lahtripildid on juba mustandialas; failihaldur ja toimetaja kuvavad need.
    }

    /**
     * Valideeri vormiandmed.
     *
     * @param array $data
     * @param array $files
     * @return array vead
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $numcolumns = (int)($data['numcolumns'] ?? 2);
        if ($numcolumns < 2 || $numcolumns > 7) {
            $errors['numcolumns'] = get_string('errornumcolumns', 'qtype_columnconnector');
        }

        $decoded = json_decode($data['contentjson'] ?? '', true);
        if (!is_array($decoded)) {
            $errors['numcolumns'] = get_string('errorinvalidcontent', 'qtype_columnconnector');
            return $errors;
        }

        $columns = $decoded['columns'] ?? null;
        if (!is_array($columns) || count($columns) < $numcolumns) {
            $errors['numcolumns'] = get_string('errormissingcolumns', 'qtype_columnconnector');
            return $errors;
        }

        for ($i = 0; $i < $numcolumns; $i++) {
            $cells = $columns[$i]['cells'] ?? null;
            if (!is_array($cells) || count($cells) < 1) {
                $errors['numcolumns'] = get_string('erroremptycolumn', 'qtype_columnconnector', $i + 1);
                return $errors;
            }
        }

        foreach (($decoded['answerkey'] ?? []) as $item) {
            $conn = qtype_columnconnector_question::normalize_connection($item);
            if ($conn === null) {
                $errors['numcolumns'] = get_string('erroranswerkey', 'qtype_columnconnector');
                return $errors;
            }
            foreach (['from', 'to'] as $end) {
                $col = $conn[$end]['col'];
                $row = $conn[$end]['row'];
                if ($col >= $numcolumns || !isset($columns[$col]['cells'][$row])) {
                    $errors['numcolumns'] = get_string('erroranswerkeyindex', 'qtype_columnconnector');
                    return $errors;
                }
            }
        }

        return $errors;
    }
}
