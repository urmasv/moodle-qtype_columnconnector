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

$string['pluginname'] = 'Column connector';
$string['pluginname_help'] = 'Create a question with 2–7 columns of cells. Learners draw lines connecting cells in neighbouring columns; the answer is graded against the connections you define.';
$string['pluginname_link'] = 'question/type/columnconnector';
$string['pluginnameadding'] = 'Adding a column connector question';
$string['pluginnameediting'] = 'Editing a column connector question';
$string['pluginnamesummary'] = 'Displays 2–7 titled columns with cells and lets learners connect neighbouring column cells with lines. Points are awarded for correct, incorrect and missing connections.';

$string['columnconnectorsettings'] = 'Column connector settings';
$string['numcolumns'] = 'Number of columns';
$string['numrows'] = 'Number of rows';
$string['numcolumns_help'] = 'Choose whether the activity is displayed with two to seven columns.';
$string['layoutmode'] = 'Layout';
$string['layoutcolumns'] = 'Columns (vertical)';
$string['layoutrows'] = 'Rows (horizontal)';
$string['linestyle'] = 'Line style';
$string['linecurved'] = 'Curved';
$string['linestraight'] = 'Straight';
$string['showclear'] = 'Show the "Clear connections" button';
$string['showmissing'] = 'Show missing connections';
$string['showmissing_help'] = 'When enabled, missing correct connections (amber lines) are shown to the learner after submission — provided the review options allow showing the right answer. Off by default.';
$string['instructions'] = 'Instructions for the learner';
$string['instructions_help'] = 'Short HTML instruction shown above the columns.';
$string['correctpoints'] = 'Points for a correct connection';
$string['correctpoints_help'] = 'Default: +1 point for each correct connection. The overall question mark is scaled from these points.';
$string['incorrectpoints'] = 'Points for an incorrect connection';
$string['missingpoints'] = 'Points for a missing connection';

$string['clearconnections'] = 'Clear connections';
$string['emptycolumn'] = 'There are no cells in this column.';
$string['invalidtarget'] = 'You can only connect to a cell in a neighbouring column.';
$string['duplicate'] = 'This connection already exists.';
$string['selectedcell'] = 'Cell selected. Choose a cell in a neighbouring column.';
$string['pleaseconnect'] = 'Please make at least one connection.';

$string['errornumcolumns'] = 'The number of columns must be between 2 and 7.';
$string['errormissingcolumns'] = 'The content must define at least as many columns as selected.';
$string['erroremptycolumn'] = 'Column {$a} must contain at least one cell.';
$string['erroranswerkey'] = 'A correct connection is malformed or does not join neighbouring columns.';
$string['erroranswerkeyindex'] = 'A correct connection refers to a cell that does not exist.';

$string['contenteditor'] = 'Columns, cells and correct connections';
$string['h5pimport'] = 'Import from H5P';
$string['h5pimport_help'] = 'Choose an existing H5P.ColumnConnector (.h5p) file and press “Load from H5P”. The content (columns, cells, connections, images) is loaded into the editor; review and save.';
$string['h5pimportload'] = 'Load from H5P';
$string['h5pimporterror'] = 'Could not read the H5P file.';
$string['h5pimportwrongtype'] = 'This is not H5P.ColumnConnector content.';
$string['column'] = 'Column';
$string['row'] = 'Row';
$string['columntitle'] = 'Column title';
$string['rowtitle'] = 'Row title';
$string['cell'] = 'Cell';
$string['celltext'] = 'Text';
$string['imageurl'] = 'Image URL';
$string['imageposition'] = 'Image position';
$string['imageabove'] = 'Above the text';
$string['imageleft'] = 'To the left of the text';
$string['imageleftsize'] = 'Left image size';
$string['imagealignleft'] = 'left';
$string['imagealigncenter'] = 'centre';
$string['sizesmall'] = 'small';
$string['sizemedium'] = 'medium';
$string['sizelarge'] = 'large';
$string['imagealt'] = 'Image alternative text';
$string['addcell'] = 'Add cell';
$string['removecell'] = 'Remove cell';
$string['moveup'] = 'Move up';
$string['movedown'] = 'Move down';
$string['correctconnections'] = 'Correct connections to the previous column';
$string['correctconnectionsrow'] = 'Correct connections to the previous row';
$string['noconnection'] = 'no connection';
$string['connectioncount'] = '{$a} connections';
$string['noneighbourcells'] = 'The previous column has no cells.';
$string['cellfallback'] = 'cell';
$string['defaultinstructions'] = 'Connect the cells to cells in neighbouring columns. One cell may be connected to several neighbouring column cells.';
$string['errorinvalidcontent'] = 'The question content is incomplete or invalid.';

$string['editorloaderror'] = 'The editor could not load. Purge caches (Site administration → Development → Purge caches) and try again.';

$string['cellimages'] = 'Cell images';
$string['cellimages_help'] = 'Upload images to use in cells. Then choose an uploaded image from the dropdown on each cell. Alternatively a cell may use an external image URL.';
$string['uploadedimage'] = 'Uploaded image';
$string['noimage'] = '— no image —';
$string['refreshimages'] = 'Refresh image list';
$string['rtebold'] = 'Bold';
$string['rteitalic'] = 'Italic';
$string['rteunderline'] = 'Underline';
$string['rtebullet'] = 'Bulleted list';
$string['rtenumbered'] = 'Numbered list';
$string['rtelink'] = 'Insert link';
$string['rtelinkprompt'] = 'Link URL:';
$string['rtealignleft'] = 'Align left';
$string['rtealigncenter'] = 'Align center';
$string['rtealignright'] = 'Align right';
$string['rteclear'] = 'Clear formatting';

$string['privacy:metadata'] = 'The Column connector question type plugin does not store any personal data.';
