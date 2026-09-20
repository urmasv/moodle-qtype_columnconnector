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
 * Strings for component 'qtype_columnconnector', language 'es'.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Conector de columnas';
$string['pluginname_help'] = 'Cree una pregunta con 2 a 7 columnas de celdas. Los estudiantes trazan líneas que conectan celdas de columnas vecinas; la respuesta se califica según las conexiones que usted defina.';
$string['pluginname_link'] = 'question/type/columnconnector';
$string['pluginnameadding'] = 'Añadir una pregunta de conector de columnas';
$string['pluginnameediting'] = 'Editar una pregunta de conector de columnas';
$string['pluginnamesummary'] = 'Muestra de 2 a 7 columnas con título y celdas y permite a los estudiantes conectar con líneas las celdas de columnas vecinas. Se otorgan puntos por conexiones correctas, incorrectas y faltantes.';
$string['columnconnectorsettings'] = 'Ajustes del conector de columnas';
$string['numcolumns'] = 'Número de columnas';
$string['numrows'] = 'Número de filas';
$string['numcolumns_help'] = 'Elija si la actividad se muestra con dos a siete columnas.';
$string['layoutmode'] = 'Disposición';
$string['layoutcolumns'] = 'Columnas (vertical)';
$string['layoutrows'] = 'Filas (horizontal)';
$string['linestyle'] = 'Estilo de línea';
$string['linecurved'] = 'Curva';
$string['linestraight'] = 'Recta';
$string['showclear'] = 'Mostrar el botón «Borrar conexiones»';
$string['showmissing'] = 'Mostrar conexiones faltantes';
$string['showmissing_help'] = 'Cuando está activado, las conexiones correctas faltantes (líneas ámbar) se muestran al estudiante después del envío, siempre que las opciones de revisión permitan mostrar la respuesta correcta. Desactivado de forma predeterminada.';
$string['instructions'] = 'Instrucciones para el estudiante';
$string['instructions_help'] = 'Breve instrucción HTML mostrada sobre las columnas.';
$string['correctpoints'] = 'Puntos por una conexión correcta';
$string['correctpoints_help'] = 'Predeterminado: +1 punto por cada conexión correcta. La calificación global de la pregunta se escala a partir de estos puntos.';
$string['incorrectpoints'] = 'Puntos por una conexión incorrecta';
$string['missingpoints'] = 'Puntos por una conexión faltante';
$string['clearconnections'] = 'Borrar conexiones';
$string['emptycolumn'] = 'No hay celdas en esta columna.';
$string['invalidtarget'] = 'Solo puede conectar con una celda de una columna vecina.';
$string['duplicate'] = 'Esta conexión ya existe.';
$string['selectedcell'] = 'Celda seleccionada. Elija una celda en una columna vecina.';
$string['pleaseconnect'] = 'Realice al menos una conexión.';
$string['errornumcolumns'] = 'El número de columnas debe estar entre 2 y 7.';
$string['errormissingcolumns'] = 'El contenido debe definir al menos tantas columnas como las seleccionadas.';
$string['erroremptycolumn'] = 'La columna {$a} debe contener al menos una celda.';
$string['erroranswerkey'] = 'Una conexión correcta está mal formada o no une columnas vecinas.';
$string['erroranswerkeyindex'] = 'Una conexión correcta se refiere a una celda que no existe.';
$string['contenteditor'] = 'Columnas, celdas y conexiones correctas';
$string['h5pimport'] = 'Importar desde H5P';
$string['h5pimport_help'] = 'Elija un archivo H5P.ColumnConnector (.h5p) existente y pulse «Cargar desde H5P». El contenido (columnas, celdas, conexiones, imágenes) se carga en el editor; revise y guarde.';
$string['h5pimportload'] = 'Cargar desde H5P';
$string['h5pimporterror'] = 'No se pudo leer el archivo H5P.';
$string['h5pimportwrongtype'] = 'Este no es contenido H5P.ColumnConnector.';
$string['column'] = 'Columna';
$string['row'] = 'Fila';
$string['columntitle'] = 'Título de la columna';
$string['rowtitle'] = 'Título de la fila';
$string['cell'] = 'Celda';
$string['celltext'] = 'Texto';
$string['imageurl'] = 'URL de la imagen';
$string['imageposition'] = 'Posición de la imagen';
$string['imageabove'] = 'Encima del texto';
$string['imageleft'] = 'A la izquierda del texto';
$string['imageleftsize'] = 'Tamaño de la imagen a la izquierda';
$string['imagealignleft'] = 'izquierda';
$string['imagealigncenter'] = 'centrado';
$string['sizesmall'] = 'pequeña';
$string['sizemedium'] = 'mediana';
$string['sizelarge'] = 'grande';
$string['imagealt'] = 'Texto alternativo de la imagen';
$string['addcell'] = 'Añadir celda';
$string['removecell'] = 'Eliminar celda';
$string['moveup'] = 'Subir';
$string['movedown'] = 'Bajar';
$string['correctconnections'] = 'Conexiones correctas con la columna anterior';
$string['correctconnectionsrow'] = 'Conexiones correctas con la fila anterior';
$string['noconnection'] = 'sin conexión';
$string['connectioncount'] = '{$a} conexiones';
$string['noneighbourcells'] = 'La columna anterior no tiene celdas.';
$string['cellfallback'] = 'celda';
$string['defaultinstructions'] = 'Conecte las celdas con celdas de columnas vecinas. Una celda puede conectarse con varias celdas de la columna vecina.';
$string['errorinvalidcontent'] = 'El contenido de la pregunta está incompleto o no es válido.';
$string['editorloaderror'] = 'No se pudo cargar el editor. Purgue las cachés (Administración del sitio → Desarrollo → Purgar cachés) e inténtelo de nuevo.';
$string['cellimages'] = 'Imágenes de las celdas';
$string['cellimages_help'] = 'Suba imágenes para usar en las celdas. Luego elija una imagen subida en el desplegable de cada celda. Como alternativa, una celda puede usar una URL de imagen externa.';
$string['uploadedimage'] = 'Imagen subida';
$string['noimage'] = '— sin imagen —';
$string['refreshimages'] = 'Actualizar la lista de imágenes';
$string['rtebold'] = 'Negrita';
$string['rteitalic'] = 'Cursiva';
$string['rteunderline'] = 'Subrayado';
$string['rtebullet'] = 'Lista con viñetas';
$string['rtenumbered'] = 'Lista numerada';
$string['rtelink'] = 'Insertar enlace';
$string['rtelinkprompt'] = 'URL del enlace:';
$string['rtealignleft'] = 'Alinear a la izquierda';
$string['rtealigncenter'] = 'Centrar';
$string['rtealignright'] = 'Alinear a la derecha';
$string['rteclear'] = 'Quitar formato';
$string['privacy:metadata'] = 'El plugin de tipo de pregunta Conector de columnas no almacena datos personales.';
