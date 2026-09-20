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

$string['pluginname'] = 'Соединитель столбцов';
$string['pluginname_help'] = 'Создайте вопрос с 2–7 столбцами ячеек. Учащиеся проводят линии, соединяя ячейки соседних столбцов; ответ оценивается по заданным вами соединениям.';
$string['pluginname_link'] = 'question/type/columnconnector';
$string['pluginnameadding'] = 'Добавление вопроса «Соединитель столбцов»';
$string['pluginnameediting'] = 'Редактирование вопроса «Соединитель столбцов»';
$string['pluginnamesummary'] = 'Отображает от 2 до 7 озаглавленных столбцов с ячейками и позволяет учащимся соединять линиями ячейки соседних столбцов. Баллы начисляются за правильные, неправильные и пропущенные соединения.';
$string['columnconnectorsettings'] = 'Настройки соединителя столбцов';
$string['numcolumns'] = 'Количество столбцов';
$string['numrows'] = 'Количество строк';
$string['numcolumns_help'] = 'Выберите, отображать ли задание с двумя–семью столбцами.';
$string['layoutmode'] = 'Расположение';
$string['layoutcolumns'] = 'Столбцы (вертикально)';
$string['layoutrows'] = 'Строки (горизонтально)';
$string['linestyle'] = 'Стиль линии';
$string['linecurved'] = 'Кривая';
$string['linestraight'] = 'Прямая';
$string['showclear'] = 'Показывать кнопку «Очистить соединения»';
$string['showmissing'] = 'Показывать пропущенные соединения';
$string['showmissing_help'] = 'Если включено, пропущенные правильные соединения (янтарные линии) показываются учащемуся после отправки — при условии, что настройки просмотра разрешают показ правильного ответа. По умолчанию отключено.';
$string['instructions'] = 'Инструкции для учащегося';
$string['instructions_help'] = 'Краткая HTML-инструкция, отображаемая над столбцами.';
$string['correctpoints'] = 'Баллы за правильное соединение';
$string['correctpoints_help'] = 'По умолчанию: +1 балл за каждое правильное соединение. Итоговая оценка вопроса масштабируется из этих баллов.';
$string['incorrectpoints'] = 'Баллы за неправильное соединение';
$string['missingpoints'] = 'Баллы за пропущенное соединение';
$string['clearconnections'] = 'Очистить соединения';
$string['emptycolumn'] = 'В этом столбце нет ячеек.';
$string['invalidtarget'] = 'Соединять можно только с ячейкой соседнего столбца.';
$string['duplicate'] = 'Это соединение уже существует.';
$string['selectedcell'] = 'Ячейка выбрана. Выберите ячейку в соседнем столбце.';
$string['pleaseconnect'] = 'Пожалуйста, создайте хотя бы одно соединение.';
$string['errornumcolumns'] = 'Количество столбцов должно быть от 2 до 7.';
$string['errormissingcolumns'] = 'Содержимое должно определять не меньше столбцов, чем выбрано.';
$string['erroremptycolumn'] = 'Столбец {$a} должен содержать хотя бы одну ячейку.';
$string['erroranswerkey'] = 'Правильное соединение задано неверно или не соединяет соседние столбцы.';
$string['erroranswerkeyindex'] = 'Правильное соединение ссылается на несуществующую ячейку.';
$string['contenteditor'] = 'Столбцы, ячейки и правильные соединения';
$string['h5pimport'] = 'Импорт из H5P';
$string['h5pimport_help'] = 'Выберите существующий файл H5P.ColumnConnector (.h5p) и нажмите «Загрузить из H5P». Содержимое (столбцы, ячейки, соединения, изображения) загрузится в редактор; проверьте и сохраните.';
$string['h5pimportload'] = 'Загрузить из H5P';
$string['h5pimporterror'] = 'Не удалось прочитать файл H5P.';
$string['h5pimportwrongtype'] = 'Это не содержимое H5P.ColumnConnector.';
$string['column'] = 'Столбец';
$string['row'] = 'Строка';
$string['columntitle'] = 'Заголовок столбца';
$string['rowtitle'] = 'Заголовок строки';
$string['cell'] = 'Ячейка';
$string['celltext'] = 'Текст';
$string['imageurl'] = 'URL изображения';
$string['imageposition'] = 'Положение изображения';
$string['imageabove'] = 'Над текстом';
$string['imageleft'] = 'Слева от текста';
$string['imageleftsize'] = 'Размер изображения слева';
$string['imagealignleft'] = 'слева';
$string['imagealigncenter'] = 'по центру';
$string['sizesmall'] = 'маленький';
$string['sizemedium'] = 'средний';
$string['sizelarge'] = 'большой';
$string['imagealt'] = 'Альтернативный текст изображения';
$string['addcell'] = 'Добавить ячейку';
$string['removecell'] = 'Удалить ячейку';
$string['moveup'] = 'Вверх';
$string['movedown'] = 'Вниз';
$string['correctconnections'] = 'Правильные соединения с предыдущим столбцом';
$string['correctconnectionsrow'] = 'Правильные соединения с предыдущей строкой';
$string['noconnection'] = 'нет соединения';
$string['connectioncount'] = '{$a} соединений';
$string['noneighbourcells'] = 'В предыдущем столбце нет ячеек.';
$string['cellfallback'] = 'ячейка';
$string['defaultinstructions'] = 'Соедините ячейки с ячейками соседних столбцов. Одна ячейка может быть соединена с несколькими ячейками соседнего столбца.';
$string['errorinvalidcontent'] = 'Содержимое вопроса неполное или недопустимое.';
$string['editorloaderror'] = 'Не удалось загрузить редактор. Очистите кэш (Администрирование → Разработка → Очистить кэш) и повторите попытку.';
$string['cellimages'] = 'Изображения ячеек';
$string['cellimages_help'] = 'Загрузите изображения для использования в ячейках. Затем выберите загруженное изображение в раскрывающемся списке каждой ячейки. Также ячейка может использовать внешний URL изображения.';
$string['uploadedimage'] = 'Загруженное изображение';
$string['noimage'] = '— нет изображения —';
$string['refreshimages'] = 'Обновить список изображений';
$string['rtebold'] = 'Полужирный';
$string['rteitalic'] = 'Курсив';
$string['rteunderline'] = 'Подчёркнутый';
$string['rtebullet'] = 'Маркированный список';
$string['rtenumbered'] = 'Нумерованный список';
$string['rtelink'] = 'Вставить ссылку';
$string['rtelinkprompt'] = 'URL ссылки:';
$string['rtealignleft'] = 'По левому краю';
$string['rtealigncenter'] = 'По центру';
$string['rtealignright'] = 'По правому краю';
$string['rteclear'] = 'Очистить форматирование';
$string['privacy:metadata'] = 'Плагин типа вопроса «Соединитель столбцов» не хранит персональные данные.';
