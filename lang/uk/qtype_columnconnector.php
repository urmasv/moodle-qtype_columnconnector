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

$string['pluginname'] = 'З’єднувач стовпців';
$string['pluginname_help'] = 'Створіть запитання з 2–7 стовпцями клітинок. Учні проводять лінії, з’єднуючи клітинки сусідніх стовпців; відповідь оцінюється за визначеними вами з’єднаннями.';
$string['pluginname_link'] = 'question/type/columnconnector';
$string['pluginnameadding'] = 'Додавання запитання «З’єднувач стовпців»';
$string['pluginnameediting'] = 'Редагування запитання «З’єднувач стовпців»';
$string['pluginnamesummary'] = 'Відображає від 2 до 7 озаглавлених стовпців із клітинками та дозволяє учням з’єднувати лініями клітинки сусідніх стовпців. Бали нараховуються за правильні, неправильні та пропущені з’єднання.';
$string['columnconnectorsettings'] = 'Налаштування з’єднувача стовпців';
$string['numcolumns'] = 'Кількість стовпців';
$string['numrows'] = 'Кількість рядків';
$string['numcolumns_help'] = 'Виберіть, чи відображати діяльність із двома–сімома стовпцями.';
$string['layoutmode'] = 'Компонування';
$string['layoutcolumns'] = 'Стовпці (вертикально)';
$string['layoutrows'] = 'Рядки (горизонтально)';
$string['linestyle'] = 'Стиль лінії';
$string['linecurved'] = 'Крива';
$string['linestraight'] = 'Пряма';
$string['showclear'] = 'Показувати кнопку «Очистити з’єднання»';
$string['showmissing'] = 'Показувати пропущені з’єднання';
$string['showmissing_help'] = 'Якщо ввімкнено, пропущені правильні з’єднання (бурштинові лінії) показуються учневі після надсилання — за умови, що параметри перегляду дозволяють показ правильної відповіді. Типово вимкнено.';
$string['instructions'] = 'Інструкції для учня';
$string['instructions_help'] = 'Коротка HTML-інструкція, що відображається над стовпцями.';
$string['correctpoints'] = 'Бали за правильне з’єднання';
$string['correctpoints_help'] = 'Типово: +1 бал за кожне правильне з’єднання. Загальна оцінка запитання масштабується з цих балів.';
$string['incorrectpoints'] = 'Бали за неправильне з’єднання';
$string['missingpoints'] = 'Бали за пропущене з’єднання';
$string['clearconnections'] = 'Очистити з’єднання';
$string['emptycolumn'] = 'У цьому стовпці немає клітинок.';
$string['invalidtarget'] = 'З’єднувати можна лише з клітинкою сусіднього стовпця.';
$string['duplicate'] = 'Це з’єднання вже існує.';
$string['selectedcell'] = 'Клітинку вибрано. Виберіть клітинку в сусідньому стовпці.';
$string['pleaseconnect'] = 'Будь ласка, створіть хоча б одне з’єднання.';
$string['errornumcolumns'] = 'Кількість стовпців має бути від 2 до 7.';
$string['errormissingcolumns'] = 'Вміст має визначати щонайменше стільки стовпців, скільки вибрано.';
$string['erroremptycolumn'] = 'Стовпець {$a} повинен містити щонайменше одну клітинку.';
$string['erroranswerkey'] = 'Правильне з’єднання сформовано неправильно або не з’єднує сусідні стовпці.';
$string['erroranswerkeyindex'] = 'Правильне з’єднання посилається на клітинку, якої не існує.';
$string['contenteditor'] = 'Стовпці, клітинки та правильні з’єднання';
$string['h5pimport'] = 'Імпорт з H5P';
$string['h5pimport_help'] = 'Виберіть наявний файл H5P.ColumnConnector (.h5p) і натисніть «Завантажити з H5P». Вміст (стовпці, клітинки, з’єднання, зображення) завантажиться в редактор; перегляньте та збережіть.';
$string['h5pimportload'] = 'Завантажити з H5P';
$string['h5pimporterror'] = 'Не вдалося прочитати файл H5P.';
$string['h5pimportwrongtype'] = 'Це не вміст H5P.ColumnConnector.';
$string['column'] = 'Стовпець';
$string['row'] = 'Рядок';
$string['columntitle'] = 'Заголовок стовпця';
$string['rowtitle'] = 'Заголовок рядка';
$string['cell'] = 'Клітинка';
$string['celltext'] = 'Текст';
$string['imageurl'] = 'URL зображення';
$string['imageposition'] = 'Розташування зображення';
$string['imageabove'] = 'Над текстом';
$string['imageleft'] = 'Ліворуч від тексту';
$string['imageleftsize'] = 'Розмір зображення ліворуч';
$string['imagealignleft'] = 'ліворуч';
$string['imagealigncenter'] = 'по центру';
$string['sizesmall'] = 'малий';
$string['sizemedium'] = 'середній';
$string['sizelarge'] = 'великий';
$string['imagealt'] = 'Альтернативний текст зображення';
$string['addcell'] = 'Додати клітинку';
$string['removecell'] = 'Видалити клітинку';
$string['moveup'] = 'Вгору';
$string['movedown'] = 'Вниз';
$string['correctconnections'] = 'Правильні з’єднання з попереднім стовпцем';
$string['correctconnectionsrow'] = 'Правильні з’єднання з попереднім рядком';
$string['noconnection'] = 'немає з’єднання';
$string['connectioncount'] = '{$a} з’єднань';
$string['noneighbourcells'] = 'У попередньому стовпці немає клітинок.';
$string['cellfallback'] = 'клітинка';
$string['defaultinstructions'] = 'З’єднайте клітинки з клітинками сусідніх стовпців. Одну клітинку можна з’єднати з кількома клітинками сусіднього стовпця.';
$string['errorinvalidcontent'] = 'Вміст запитання неповний або недійсний.';
$string['editorloaderror'] = 'Не вдалося завантажити редактор. Очистіть кеш (Адміністрування → Розробка → Очистити кеш) і повторіть спробу.';
$string['cellimages'] = 'Зображення клітинок';
$string['cellimages_help'] = 'Завантажте зображення для використання в клітинках. Потім виберіть завантажене зображення у розкривному списку кожної клітинки. Також клітинка може використовувати зовнішній URL зображення.';
$string['uploadedimage'] = 'Завантажене зображення';
$string['noimage'] = '— немає зображення —';
$string['refreshimages'] = 'Оновити список зображень';
$string['rtebold'] = 'Жирний';
$string['rteitalic'] = 'Курсив';
$string['rteunderline'] = 'Підкреслений';
$string['rtebullet'] = 'Маркований список';
$string['rtenumbered'] = 'Нумерований список';
$string['rtelink'] = 'Вставити посилання';
$string['rtelinkprompt'] = 'URL посилання:';
$string['rtealignleft'] = 'Вирівняти ліворуч';
$string['rtealigncenter'] = 'По центру';
$string['rtealignright'] = 'Вирівняти праворуч';
$string['rteclear'] = 'Очистити форматування';
$string['privacy:metadata'] = 'Плагін типу запитання «З’єднувач стовпців» не зберігає персональних даних.';
