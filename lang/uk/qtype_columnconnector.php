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
 * Strings for component 'qtype_columnconnector', language 'uk'.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcell'] = 'Додати клітинку';
$string['cell'] = 'Клітинка';
$string['cellfallback'] = 'клітинка';
$string['cellimages'] = 'Зображення клітинок';
$string['cellimages_help'] = 'Завантажте зображення для використання в клітинках. Потім виберіть завантажене зображення у розкривному списку кожної клітинки. Також клітинка може використовувати зовнішній URL зображення.';
$string['cellposition'] = 'Стовпець {$a->col}, клітинка {$a->row}';
$string['cellpositionrows'] = 'Рядок {$a->col}, клітинка {$a->row}';
$string['celltext'] = 'Текст';
$string['clearconnections'] = 'Очистити з’єднання';
$string['column'] = 'Стовпець';
$string['columnconnectorsettings'] = 'Налаштування з’єднувача стовпців';
$string['columntitle'] = 'Заголовок стовпця';
$string['connectionadded'] = 'З’єднано: {$a}';
$string['connectioncorrect'] = '{$a}: правильно';
$string['connectioncount'] = '{$a} з’єднань';
$string['connectionincorrect'] = '{$a}: неправильно';
$string['connectionmissing'] = '{$a}: відсутнє (правильне з’єднання)';
$string['connectionremoved'] = 'З’єднання видалено: {$a}';
$string['connectionscleared'] = 'Усі з’єднання видалено.';
$string['connectionsummary'] = 'З’єднання';
$string['contenteditor'] = 'Стовпці, клітинки та правильні з’єднання';
$string['correctansweris'] = 'Правильна відповідь: {$a}';
$string['correctconnections'] = 'Правильні з’єднання з попереднім стовпцем';
$string['correctconnectionsrow'] = 'Правильні з’єднання з попереднім рядком';
$string['correctpoints'] = 'Бали за правильне з’єднання';
$string['correctpoints_help'] = 'Типово: +1 бал за кожне правильне з’єднання. Загальна оцінка запитання масштабується з цих балів.';
$string['editorloaderror'] = 'Не вдалося завантажити редактор. Очистіть кеш (Адміністрування → Розробка → Очистити кеш) і повторіть спробу.';
$string['emptycolumn'] = 'У цьому стовпці немає клітинок.';
$string['enableh5pimport'] = 'Увімкнути імпорт з H5P';
$string['enableh5pimport_desc'] = 'Якщо увімкнено, у формі редагування запитання з’являється поле «Імпорт з H5P», яке імпортує вміст з наявного файлу H5P.ColumnConnector (.h5p) у запитання. Типово вимкнено. Приклади: <a href="https://urmasvessin.ee/testid/h5p/h5p-sisutuup-connect-with-lines-uhenda-joontega/" target="_blank" rel="noopener">urmasvessin.ee</a>. Вихідний код (тип вмісту H5P): <a href="https://github.com/urmasv/H5P.ColumnConnector" target="_blank" rel="noopener">GitHub</a>.';
$string['erroranswerkey'] = 'Правильне з’єднання сформовано неправильно або не з’єднує сусідні стовпці.';
$string['erroranswerkeyindex'] = 'Правильне з’єднання посилається на клітинку, якої не існує.';
$string['errorcorrectpoints'] = 'Бали за правильне з’єднання мають бути більшими за нуль.';
$string['erroremptycolumn'] = 'Стовпець {$a} повинен містити щонайменше одну клітинку.';
$string['errorincorrectpoints'] = 'Бали за неправильне з’єднання мають бути нульовими або від’ємними.';
$string['errorinvalidcontent'] = 'Вміст запитання неповний або недійсний.';
$string['errorinvalidquestion'] = 'Питання «{$a->name}» не можна зберегти: {$a->errors}';
$string['errormissingcolumns'] = 'Вміст має визначати щонайменше стільки стовпців, скільки вибрано.';
$string['errormissingpoints'] = 'Бали за відсутнє з’єднання мають бути нульовими або від’ємними.';
$string['errornoanswerkey'] = 'Позначте принаймні одне правильне з’єднання.';
$string['errornumcolumns'] = 'Кількість стовпців має бути від 2 до 7.';
$string['h5pimport'] = 'Імпорт з H5P';
$string['h5pimport_help'] = 'Виберіть наявний файл H5P.ColumnConnector (.h5p) і натисніть «Завантажити з H5P». Вміст (стовпці, клітинки, з’єднання, зображення) завантажиться в редактор; перегляньте та збережіть.';
$string['h5pimporterror'] = 'Не вдалося прочитати файл H5P.';
$string['h5pimportload'] = 'Завантажити з H5P';
$string['h5pimporttoolarge'] = 'Файл H5P завеликий. Максимальний розмір: {$a}.';
$string['h5pimporttoomanyfiles'] = 'Файл H5P містить забагато файлів вмісту. Максимум: {$a}.';
$string['h5pimportunpackedtoolarge'] = 'Розпакований вміст файлу H5P завеликий. Максимальний розмір: {$a}.';
$string['h5pimportwrongtype'] = 'Це не вміст H5P.ColumnConnector.';
$string['imageabove'] = 'Над текстом';
$string['imagealigncenter'] = 'по центру';
$string['imagealignleft'] = 'ліворуч';
$string['imagealt'] = 'Альтернативний текст зображення';
$string['imageleft'] = 'Ліворуч від тексту';
$string['imageleftsize'] = 'Розмір зображення ліворуч';
$string['imageposition'] = 'Розташування зображення';
$string['imageurl'] = 'URL зображення';
$string['incorrectpoints'] = 'Бали за неправильне з’єднання';
$string['interactionhelp'] = 'Виберіть клітинку, а потім клітинку в сусідньому стовпці, щоб з’єднати їх. Повторний вибір тієї самої пари видаляє з’єднання. Натисніть Escape, щоб скасувати вибір.';
$string['interactionhelprows'] = 'Виберіть клітинку, а потім клітинку в сусідньому рядку, щоб з’єднати їх. Повторний вибір тієї самої пари видаляє з’єднання. Натисніть Escape, щоб скасувати вибір.';
$string['invalidtarget'] = 'З’єднувати можна лише з клітинкою сусіднього стовпця.';
$string['layoutcolumns'] = 'Стовпці (вертикально)';
$string['layoutmode'] = 'Компонування';
$string['layoutrows'] = 'Рядки (горизонтально)';
$string['linecurved'] = 'Крива';
$string['linestraight'] = 'Пряма';
$string['linestyle'] = 'Стиль лінії';
$string['missingpoints'] = 'Бали за пропущене з’єднання';
$string['movedown'] = 'Вниз';
$string['moveup'] = 'Вгору';
$string['noconnection'] = 'немає з’єднання';
$string['noimage'] = '— немає зображення —';
$string['noneighbourcells'] = 'У попередньому стовпці немає клітинок.';
$string['numcolumns'] = 'Кількість стовпців';
$string['numcolumns_help'] = 'Виберіть, чи відображати діяльність із двома–сімома стовпцями.';
$string['numrows'] = 'Кількість рядків';
$string['pleaseconnect'] = 'Будь ласка, створіть хоча б одне з’єднання.';
$string['pluginname'] = 'З’єднувач стовпців';
$string['pluginname_help'] = 'Створіть запитання з 2–7 стовпцями клітинок. Учні проводять лінії, з’єднуючи клітинки сусідніх стовпців; відповідь оцінюється за визначеними вами з’єднаннями.';
$string['pluginname_link'] = 'question/type/columnconnector';
$string['pluginnameadding'] = 'Додавання запитання «З’єднувач стовпців»';
$string['pluginnameediting'] = 'Редагування запитання «З’єднувач стовпців»';
$string['pluginnamesummary'] = 'Відображає від 2 до 7 озаглавлених стовпців із клітинками та дозволяє учням з’єднувати лініями клітинки сусідніх стовпців. Бали нараховуються за правильні, неправильні та пропущені з’єднання.';
$string['privacy:preference:defaultmark'] = 'Типова оцінка, встановлена для питання.';
$string['refreshimages'] = 'Оновити список зображень';
$string['regradeissuecellorderchanged'] = 'Клітинки або їхній порядок змінилися, тому спробу не можна переоцінити з цією версією.';
$string['regradeissuestructurechanged'] = 'Кількість стовпців або комірок змінилася, тому спробу не можна переоцінити з цією версією.';
$string['removecell'] = 'Видалити клітинку';
$string['row'] = 'Рядок';
$string['rowtitle'] = 'Заголовок рядка';
$string['rtealigncenter'] = 'По центру';
$string['rtealignleft'] = 'Вирівняти ліворуч';
$string['rtealignright'] = 'Вирівняти праворуч';
$string['rtebold'] = 'Жирний';
$string['rtebullet'] = 'Маркований список';
$string['rteclear'] = 'Очистити форматування';
$string['rteitalic'] = 'Курсив';
$string['rtelink'] = 'Вставити посилання';
$string['rtelinkprompt'] = 'URL посилання:';
$string['rtenumbered'] = 'Нумерований список';
$string['rteunderline'] = 'Підкреслений';
$string['selectcell'] = 'Вибрати: {$a}';
$string['selectedcell'] = 'Вибрано: {$a}. Виберіть клітинку в сусідньому стовпці.';
$string['showclear'] = 'Показувати кнопку «Очистити з’єднання»';
$string['showmissing'] = 'Показувати пропущені з’єднання';
$string['showmissing_help'] = 'Якщо ввімкнено, пропущені правильні з’єднання (бурштинові лінії) показуються учневі після надсилання — за умови, що параметри перегляду дозволяють показ правильної відповіді. Типово вимкнено.';
$string['sizelarge'] = 'великий';
$string['sizemedium'] = 'середній';
$string['sizesmall'] = 'малий';
$string['statsconnected'] = '{$a}: з’єднано';
$string['statsnotconnected'] = '{$a}: не з’єднано';
$string['uploadedimage'] = 'Завантажене зображення';
