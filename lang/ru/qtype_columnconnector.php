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
 * Strings for component 'qtype_columnconnector', language 'ru'.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcell'] = 'Добавить ячейку';
$string['cell'] = 'Ячейка';
$string['cellfallback'] = 'ячейка';
$string['cellimages'] = 'Изображения ячеек';
$string['cellimages_help'] = 'Загрузите изображения для использования в ячейках. Затем выберите загруженное изображение в раскрывающемся списке каждой ячейки. Также ячейка может использовать внешний URL изображения.';
$string['cellposition'] = 'Столбец {$a->col}, ячейка {$a->row}';
$string['cellpositionrows'] = 'Строка {$a->col}, ячейка {$a->row}';
$string['celltext'] = 'Текст';
$string['clearconnections'] = 'Очистить соединения';
$string['column'] = 'Столбец';
$string['columnconnectorsettings'] = 'Настройки соединителя столбцов';
$string['columntitle'] = 'Заголовок столбца';
$string['connectionadded'] = 'Соединено: {$a}';
$string['connectioncorrect'] = '{$a}: верно';
$string['connectioncount'] = '{$a} соединений';
$string['connectionincorrect'] = '{$a}: неверно';
$string['connectionmissing'] = '{$a}: отсутствует (верное соединение)';
$string['connectionremoved'] = 'Соединение удалено: {$a}';
$string['connectionscleared'] = 'Все соединения удалены.';
$string['connectionsummary'] = 'Соединения';
$string['contenteditor'] = 'Столбцы, ячейки и правильные соединения';
$string['correctansweris'] = 'Правильный ответ: {$a}';
$string['correctconnections'] = 'Правильные соединения с предыдущим столбцом';
$string['correctconnectionsrow'] = 'Правильные соединения с предыдущей строкой';
$string['correctpoints'] = 'Баллы за правильное соединение';
$string['correctpoints_help'] = 'По умолчанию: +1 балл за каждое правильное соединение. Итоговая оценка вопроса масштабируется из этих баллов.';
$string['editorloaderror'] = 'Не удалось загрузить редактор. Очистите кэш (Администрирование → Разработка → Очистить кэш) и повторите попытку.';
$string['emptycolumn'] = 'В этом столбце нет ячеек.';
$string['enableh5pimport'] = 'Включить импорт из H5P';
$string['enableh5pimport_desc'] = 'Если включено, в форме редактирования вопроса отображается поле «Импорт из H5P», которое импортирует содержимое из существующего файла H5P.ColumnConnector (.h5p) в вопрос. По умолчанию отключено. Примеры: <a href="https://urmasvessin.ee/testid/h5p/h5p-sisutuup-connect-with-lines-uhenda-joontega/" target="_blank" rel="noopener">urmasvessin.ee</a>. Исходный код (тип содержимого H5P): <a href="https://github.com/urmasv/H5P.ColumnConnector" target="_blank" rel="noopener">GitHub</a>.';
$string['erroranswerkey'] = 'Правильное соединение задано неверно или не соединяет соседние столбцы.';
$string['erroranswerkeyindex'] = 'Правильное соединение ссылается на несуществующую ячейку.';
$string['errorcorrectpoints'] = 'Баллы за верное соединение должны быть больше нуля.';
$string['erroremptycolumn'] = 'Столбец {$a} должен содержать хотя бы одну ячейку.';
$string['errorincorrectpoints'] = 'Баллы за неверное соединение должны быть нулевыми или отрицательными.';
$string['errorinvalidcontent'] = 'Содержимое вопроса неполное или недопустимое.';
$string['errorinvalidquestion'] = 'Вопрос «{$a->name}» нельзя сохранить: {$a->errors}';
$string['errormissingcolumns'] = 'Содержимое должно определять не меньше столбцов, чем выбрано.';
$string['errormissingpoints'] = 'Баллы за отсутствующее соединение должны быть нулевыми или отрицательными.';
$string['errornoanswerkey'] = 'Отметьте хотя бы одно верное соединение.';
$string['errornumcolumns'] = 'Количество столбцов должно быть от 2 до 7.';
$string['h5pimport'] = 'Импорт из H5P';
$string['h5pimport_help'] = 'Выберите существующий файл H5P.ColumnConnector (.h5p) и нажмите «Загрузить из H5P». Содержимое (столбцы, ячейки, соединения, изображения) загрузится в редактор; проверьте и сохраните.';
$string['h5pimporterror'] = 'Не удалось прочитать файл H5P.';
$string['h5pimportload'] = 'Загрузить из H5P';
$string['h5pimporttoolarge'] = 'Файл H5P слишком большой. Максимальный размер: {$a}.';
$string['h5pimporttoomanyfiles'] = 'Файл H5P содержит слишком много файлов содержимого. Максимум: {$a}.';
$string['h5pimportunpackedtoolarge'] = 'Распакованное содержимое файла H5P слишком большое. Максимальный размер: {$a}.';
$string['h5pimportwrongtype'] = 'Это не содержимое H5P.ColumnConnector.';
$string['imageabove'] = 'Над текстом';
$string['imagealigncenter'] = 'по центру';
$string['imagealignleft'] = 'слева';
$string['imagealt'] = 'Альтернативный текст изображения';
$string['imageleft'] = 'Слева от текста';
$string['imageleftsize'] = 'Размер изображения слева';
$string['imageposition'] = 'Положение изображения';
$string['imageurl'] = 'URL изображения';
$string['incorrectpoints'] = 'Баллы за неправильное соединение';
$string['interactionhelp'] = 'Выберите ячейку, а затем ячейку в соседнем столбце, чтобы соединить их. Повторный выбор той же пары удаляет соединение. Нажмите Escape, чтобы отменить выбор.';
$string['interactionhelprows'] = 'Выберите ячейку, а затем ячейку в соседней строке, чтобы соединить их. Повторный выбор той же пары удаляет соединение. Нажмите Escape, чтобы отменить выбор.';
$string['invalidtarget'] = 'Соединять можно только с ячейкой соседнего столбца.';
$string['layoutcolumns'] = 'Столбцы (вертикально)';
$string['layoutmode'] = 'Расположение';
$string['layoutrows'] = 'Строки (горизонтально)';
$string['linecurved'] = 'Кривая';
$string['linestraight'] = 'Прямая';
$string['linestyle'] = 'Стиль линии';
$string['missingpoints'] = 'Баллы за пропущенное соединение';
$string['movedown'] = 'Вниз';
$string['moveup'] = 'Вверх';
$string['noconnection'] = 'нет соединения';
$string['noimage'] = '— нет изображения —';
$string['noneighbourcells'] = 'В предыдущем столбце нет ячеек.';
$string['numcolumns'] = 'Количество столбцов';
$string['numcolumns_help'] = 'Выберите, отображать ли задание с двумя–семью столбцами.';
$string['numrows'] = 'Количество строк';
$string['pleaseconnect'] = 'Пожалуйста, создайте хотя бы одно соединение.';
$string['pluginname'] = 'Соединитель столбцов';
$string['pluginname_help'] = 'Создайте вопрос с 2–7 столбцами ячеек. Учащиеся проводят линии, соединяя ячейки соседних столбцов; ответ оценивается по заданным вами соединениям.';
$string['pluginname_link'] = 'question/type/columnconnector';
$string['pluginnameadding'] = 'Добавление вопроса «Соединитель столбцов»';
$string['pluginnameediting'] = 'Редактирование вопроса «Соединитель столбцов»';
$string['pluginnamesummary'] = 'Отображает от 2 до 7 озаглавленных столбцов с ячейками и позволяет учащимся соединять линиями ячейки соседних столбцов. Баллы начисляются за правильные, неправильные и пропущенные соединения.';
$string['privacy:preference:defaultmark'] = 'Балл по умолчанию, заданный для вопроса.';
$string['refreshimages'] = 'Обновить список изображений';
$string['regradeissuecellorderchanged'] = 'Ячейки или их порядок изменились, поэтому попытку нельзя переоценить с этой версией.';
$string['regradeissuestructurechanged'] = 'Количество столбцов или ячеек изменилось, поэтому попытку нельзя переоценить с этой версией.';
$string['removecell'] = 'Удалить ячейку';
$string['row'] = 'Строка';
$string['rowtitle'] = 'Заголовок строки';
$string['rtealigncenter'] = 'По центру';
$string['rtealignleft'] = 'По левому краю';
$string['rtealignright'] = 'По правому краю';
$string['rtebold'] = 'Полужирный';
$string['rtebullet'] = 'Маркированный список';
$string['rteclear'] = 'Очистить форматирование';
$string['rteitalic'] = 'Курсив';
$string['rtelink'] = 'Вставить ссылку';
$string['rtelinkprompt'] = 'URL ссылки:';
$string['rtenumbered'] = 'Нумерованный список';
$string['rteunderline'] = 'Подчёркнутый';
$string['selectcell'] = 'Выбрать: {$a}';
$string['selectedcell'] = 'Выбрано: {$a}. Выберите ячейку в соседнем столбце.';
$string['showclear'] = 'Показывать кнопку «Очистить соединения»';
$string['showmissing'] = 'Показывать пропущенные соединения';
$string['showmissing_help'] = 'Если включено, пропущенные правильные соединения (янтарные линии) показываются учащемуся после отправки — при условии, что настройки просмотра разрешают показ правильного ответа. По умолчанию отключено.';
$string['sizelarge'] = 'большой';
$string['sizemedium'] = 'средний';
$string['sizesmall'] = 'маленький';
$string['statsconnected'] = '{$a}: соединены';
$string['statsnotconnected'] = '{$a}: не соединены';
$string['uploadedimage'] = 'Загруженное изображение';
