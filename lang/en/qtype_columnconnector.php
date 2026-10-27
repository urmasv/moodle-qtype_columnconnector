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
 * Ingliskeelsed stringid qtype_columnconnector jaoks.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcell'] = 'Add cell';
$string['cell'] = 'Cell';
$string['cellfallback'] = 'cell';
$string['cellimages'] = 'Cell images';
$string['cellimages_help'] = 'Upload images to use in cells. Then choose an uploaded image from the dropdown on each cell. Alternatively a cell may use an external image URL.';
$string['cellposition'] = 'Column {$a->col}, cell {$a->row}';
$string['cellpositionrows'] = 'Row {$a->col}, cell {$a->row}';
$string['celltext'] = 'Text';
$string['clearconnections'] = 'Clear connections';
$string['column'] = 'Column';
$string['columnconnectorsettings'] = 'Column connector settings';
$string['columntitle'] = 'Column title';
$string['connectionadded'] = 'Connected: {$a}';
$string['connectioncorrect'] = '{$a}: correct';
$string['connectioncount'] = '{$a} connections';
$string['connectionincorrect'] = '{$a}: incorrect';
$string['connectionmissing'] = '{$a}: missing (correct connection)';
$string['connectionremoved'] = 'Connection removed: {$a}';
$string['connectionscleared'] = 'All connections removed.';
$string['connectionsummary'] = 'Connections';
$string['contenteditor'] = 'Columns, cells and correct connections';
$string['correctansweris'] = 'The correct answer is: {$a}';
$string['correctconnections'] = 'Correct connections to the previous column';
$string['correctconnectionsrow'] = 'Correct connections to the previous row';
$string['correctpoints'] = 'Points for a correct connection';
$string['correctpoints_help'] = 'Default: +1 point for each correct connection. The overall question mark is scaled from these points.';
$string['editorloaderror'] = 'The editor could not load. Purge caches (Site administration → Development → Purge caches) and try again.';
$string['emptycolumn'] = 'There are no cells in this column.';
$string['enableh5pimport'] = 'Enable H5P import';
$string['enableh5pimport_desc'] = 'When enabled, the question editing form shows an „Import from H5P" field that imports content from an existing H5P.ColumnConnector (.h5p) file into the question. Off by default. Examples: <a href="https://urmasvessin.ee/testid/h5p/h5p-sisutuup-connect-with-lines-uhenda-joontega/" target="_blank" rel="noopener">urmasvessin.ee</a>. Source code (H5P content type): <a href="https://github.com/urmasv/H5P.ColumnConnector" target="_blank" rel="noopener">GitHub</a>.';
$string['erroranswerkey'] = 'A correct connection is malformed or does not join neighbouring columns.';
$string['erroranswerkeyindex'] = 'A correct connection refers to a cell that does not exist.';
$string['errorcorrectpoints'] = 'Points for a correct connection must be greater than zero.';
$string['erroremptycolumn'] = 'Column {$a} must contain at least one cell.';
$string['errorincorrectpoints'] = 'Points for an incorrect connection must be zero or negative.';
$string['errorinvalidcontent'] = 'The question content is incomplete or invalid.';
$string['errorinvalidquestion'] = 'Question "{$a->name}" cannot be saved: {$a->errors}';
$string['errormissingcolumns'] = 'The content must define at least as many columns as selected.';
$string['errormissingpoints'] = 'Points for a missing connection must be zero or negative.';
$string['errornoanswerkey'] = 'Mark at least one correct connection.';
$string['errornumcolumns'] = 'The number of columns must be between 2 and 7.';
$string['h5pimport'] = 'Import from H5P';
$string['h5pimport_help'] = 'Choose an existing H5P.ColumnConnector (.h5p) file and press “Load from H5P”. The content (columns, cells, connections, images) is loaded into the editor; review and save.';
$string['h5pimporterror'] = 'Could not read the H5P file.';
$string['h5pimportload'] = 'Load from H5P';
$string['h5pimporttoolarge'] = 'The H5P file is too large. Maximum size: {$a}.';
$string['h5pimporttoomanyfiles'] = 'The H5P file contains too many content files. Maximum: {$a}.';
$string['h5pimportunpackedtoolarge'] = 'The unpacked content of the H5P file is too large. Maximum size: {$a}.';
$string['h5pimportwrongtype'] = 'This is not H5P.ColumnConnector content.';
$string['imageabove'] = 'Above the text';
$string['imagealigncenter'] = 'centre';
$string['imagealignleft'] = 'left';
$string['imagealt'] = 'Image alternative text';
$string['imageleft'] = 'To the left of the text';
$string['imageleftsize'] = 'Left image size';
$string['imageposition'] = 'Image position';
$string['imageurl'] = 'Image URL';
$string['incorrectpoints'] = 'Points for an incorrect connection';
$string['interactionhelp'] = 'Select a cell and then a cell in a neighbouring column to connect them. Selecting the same pair again removes the connection. Press Escape to cancel the selection.';
$string['interactionhelprows'] = 'Select a cell and then a cell in a neighbouring row to connect them. Selecting the same pair again removes the connection. Press Escape to cancel the selection.';
$string['invalidtarget'] = 'You can only connect to a cell in a neighbouring column.';
$string['layoutcolumns'] = 'Columns (vertical)';
$string['layoutmode'] = 'Layout';
$string['layoutrows'] = 'Rows (horizontal)';
$string['linecurved'] = 'Curved';
$string['linestraight'] = 'Straight';
$string['linestyle'] = 'Line style';
$string['missingpoints'] = 'Points for a missing connection';
$string['movedown'] = 'Move down';
$string['moveup'] = 'Move up';
$string['noconnection'] = 'no connection';
$string['noimage'] = '— no image —';
$string['noneighbourcells'] = 'The previous column has no cells.';
$string['numcolumns'] = 'Number of columns';
$string['numcolumns_help'] = 'Choose whether the activity is displayed with two to seven columns.';
$string['numrows'] = 'Number of rows';
$string['pleaseconnect'] = 'Please make at least one connection.';
$string['pluginname'] = 'Column connector';
$string['pluginname_help'] = 'Create a question with 2–7 columns of cells. Learners draw lines connecting cells in neighbouring columns; the answer is graded against the connections you define.';
$string['pluginname_link'] = 'question/type/columnconnector';
$string['pluginnameadding'] = 'Adding a column connector question';
$string['pluginnameediting'] = 'Editing a column connector question';
$string['pluginnamesummary'] = 'Displays 2–7 titled columns with cells and lets learners connect neighbouring column cells with lines. Points are awarded for correct, incorrect and missing connections.';
$string['privacy:preference:defaultmark'] = 'The default mark set for a given question.';
$string['refreshimages'] = 'Refresh image list';
$string['regradeissuecellorderchanged'] = 'The cells or their order have changed, so the attempt cannot be regraded with this version.';
$string['regradeissuestructurechanged'] = 'The number of columns or cells has changed, so the attempt cannot be regraded with this version.';
$string['removecell'] = 'Remove cell';
$string['row'] = 'Row';
$string['rowtitle'] = 'Row title';
$string['rtealigncenter'] = 'Align center';
$string['rtealignleft'] = 'Align left';
$string['rtealignright'] = 'Align right';
$string['rtebold'] = 'Bold';
$string['rtebullet'] = 'Bulleted list';
$string['rteclear'] = 'Clear formatting';
$string['rteitalic'] = 'Italic';
$string['rtelink'] = 'Insert link';
$string['rtelinkprompt'] = 'Link URL:';
$string['rtenumbered'] = 'Numbered list';
$string['rteunderline'] = 'Underline';
$string['selectcell'] = 'Select: {$a}';
$string['selectedcell'] = 'Selected: {$a}. Choose a cell in a neighbouring column.';
$string['showclear'] = 'Show the "Clear connections" button';
$string['showmissing'] = 'Show missing connections';
$string['showmissing_help'] = 'When enabled, missing correct connections (amber lines) are shown to the learner after submission — provided the review options allow showing the right answer. Off by default.';
$string['sizelarge'] = 'large';
$string['sizemedium'] = 'medium';
$string['sizesmall'] = 'small';
$string['statsconnected'] = '{$a}: connected';
$string['statsnotconnected'] = '{$a}: not connected';
$string['uploadedimage'] = 'Uploaded image';
