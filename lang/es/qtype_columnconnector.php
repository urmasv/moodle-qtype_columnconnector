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

$string['addcell'] = 'Añadir celda';
$string['cell'] = 'Celda';
$string['cellfallback'] = 'celda';
$string['cellimages'] = 'Imágenes de las celdas';
$string['cellimages_help'] = 'Suba imágenes para usar en las celdas. Luego elija una imagen subida en el desplegable de cada celda. Como alternativa, una celda puede usar una URL de imagen externa.';
$string['cellposition'] = 'Columna {$a->col}, celda {$a->row}';
$string['cellpositionrows'] = 'Fila {$a->col}, celda {$a->row}';
$string['celltext'] = 'Texto';
$string['clearconnections'] = 'Borrar conexiones';
$string['column'] = 'Columna';
$string['columnconnectorsettings'] = 'Ajustes del conector de columnas';
$string['columntitle'] = 'Título de la columna';
$string['connectionadded'] = 'Conectado: {$a}';
$string['connectioncorrect'] = '{$a}: correcta';
$string['connectioncount'] = '{$a} conexiones';
$string['connectionincorrect'] = '{$a}: incorrecta';
$string['connectionmissing'] = '{$a}: falta (conexión correcta)';
$string['connectionremoved'] = 'Conexión eliminada: {$a}';
$string['connectionscleared'] = 'Se han eliminado todas las conexiones.';
$string['connectionsummary'] = 'Conexiones';
$string['contenteditor'] = 'Columnas, celdas y conexiones correctas';
$string['correctansweris'] = 'La respuesta correcta es: {$a}';
$string['correctconnections'] = 'Conexiones correctas con la columna anterior';
$string['correctconnectionsrow'] = 'Conexiones correctas con la fila anterior';
$string['correctpoints'] = 'Puntos por una conexión correcta';
$string['correctpoints_help'] = 'Predeterminado: +1 punto por cada conexión correcta. La calificación global de la pregunta se escala a partir de estos puntos.';
$string['editorloaderror'] = 'No se pudo cargar el editor. Purgue las cachés (Administración del sitio → Desarrollo → Purgar cachés) e inténtelo de nuevo.';
$string['emptycolumn'] = 'No hay celdas en esta columna.';
$string['enableh5pimport'] = 'Habilitar la importación desde H5P';
$string['enableh5pimport_desc'] = 'Si está habilitado, el formulario de la pregunta muestra un campo «Importar desde H5P» que importa el contenido de un archivo H5P.ColumnConnector (.h5p) existente a la pregunta. Desactivado de forma predeterminada. Ejemplos: <a href="https://urmasvessin.ee/testid/h5p/h5p-sisutuup-connect-with-lines-uhenda-joontega/" target="_blank" rel="noopener">urmasvessin.ee</a>. Código fuente (tipo de contenido H5P): <a href="https://github.com/urmasv/H5P.ColumnConnector" target="_blank" rel="noopener">GitHub</a>.';
$string['erroranswerkey'] = 'Una conexión correcta está mal formada o no une columnas vecinas.';
$string['erroranswerkeyindex'] = 'Una conexión correcta se refiere a una celda que no existe.';
$string['errorcorrectpoints'] = 'Los puntos por una conexión correcta deben ser mayores que cero.';
$string['erroremptycolumn'] = 'La columna {$a} debe contener al menos una celda.';
$string['errorincorrectpoints'] = 'Los puntos por una conexión incorrecta deben ser cero o negativos.';
$string['errorinvalidcontent'] = 'El contenido de la pregunta está incompleto o no es válido.';
$string['errorinvalidquestion'] = 'La pregunta «{$a->name}» no se puede guardar: {$a->errors}';
$string['errormissingcolumns'] = 'El contenido debe definir al menos tantas columnas como las seleccionadas.';
$string['errormissingpoints'] = 'Los puntos por una conexión que falta deben ser cero o negativos.';
$string['errornoanswerkey'] = 'Marque al menos una conexión correcta.';
$string['errornumcolumns'] = 'El número de columnas debe estar entre 2 y 7.';
$string['h5pimport'] = 'Importar desde H5P';
$string['h5pimport_help'] = 'Elija un archivo H5P.ColumnConnector (.h5p) existente y pulse «Cargar desde H5P». El contenido (columnas, celdas, conexiones, imágenes) se carga en el editor; revise y guarde.';
$string['h5pimporterror'] = 'No se pudo leer el archivo H5P.';
$string['h5pimportload'] = 'Cargar desde H5P';
$string['h5pimporttoolarge'] = 'El archivo H5P es demasiado grande. Tamaño máximo: {$a}.';
$string['h5pimporttoomanyfiles'] = 'El archivo H5P contiene demasiados archivos de contenido. Máximo: {$a}.';
$string['h5pimportunpackedtoolarge'] = 'El contenido descomprimido del archivo H5P es demasiado grande. Tamaño máximo: {$a}.';
$string['h5pimportwrongtype'] = 'Este no es contenido H5P.ColumnConnector.';
$string['imageabove'] = 'Encima del texto';
$string['imagealigncenter'] = 'centrado';
$string['imagealignleft'] = 'izquierda';
$string['imagealt'] = 'Texto alternativo de la imagen';
$string['imageleft'] = 'A la izquierda del texto';
$string['imageleftsize'] = 'Tamaño de la imagen a la izquierda';
$string['imageposition'] = 'Posición de la imagen';
$string['imageurl'] = 'URL de la imagen';
$string['incorrectpoints'] = 'Puntos por una conexión incorrecta';
$string['interactionhelp'] = 'Seleccione una celda y después una celda de una columna vecina para conectarlas. Al seleccionar de nuevo el mismo par se elimina la conexión. Pulse Escape para cancelar la selección.';
$string['interactionhelprows'] = 'Seleccione una celda y después una celda de una fila vecina para conectarlas. Al seleccionar de nuevo el mismo par se elimina la conexión. Pulse Escape para cancelar la selección.';
$string['invalidtarget'] = 'Solo puede conectar con una celda de una columna vecina.';
$string['layoutcolumns'] = 'Columnas (vertical)';
$string['layoutmode'] = 'Disposición';
$string['layoutrows'] = 'Filas (horizontal)';
$string['linecurved'] = 'Curva';
$string['linestraight'] = 'Recta';
$string['linestyle'] = 'Estilo de línea';
$string['missingpoints'] = 'Puntos por una conexión faltante';
$string['movedown'] = 'Bajar';
$string['moveup'] = 'Subir';
$string['noconnection'] = 'sin conexión';
$string['noimage'] = '— sin imagen —';
$string['noneighbourcells'] = 'La columna anterior no tiene celdas.';
$string['numcolumns'] = 'Número de columnas';
$string['numcolumns_help'] = 'Elija si la actividad se muestra con dos a siete columnas.';
$string['numrows'] = 'Número de filas';
$string['pleaseconnect'] = 'Realice al menos una conexión.';
$string['pluginname'] = 'Conector de columnas';
$string['pluginname_help'] = 'Cree una pregunta con 2 a 7 columnas de celdas. Los estudiantes trazan líneas que conectan celdas de columnas vecinas; la respuesta se califica según las conexiones que usted defina.';
$string['pluginname_link'] = 'question/type/columnconnector';
$string['pluginnameadding'] = 'Añadir una pregunta de conector de columnas';
$string['pluginnameediting'] = 'Editar una pregunta de conector de columnas';
$string['pluginnamesummary'] = 'Muestra de 2 a 7 columnas con título y celdas y permite a los estudiantes conectar con líneas las celdas de columnas vecinas. Se otorgan puntos por conexiones correctas, incorrectas y faltantes.';
$string['privacy:preference:defaultmark'] = 'La calificación predeterminada establecida para una pregunta.';
$string['refreshimages'] = 'Actualizar la lista de imágenes';
$string['regradeissuecellorderchanged'] = 'Las celdas o su orden han cambiado, por lo que el intento no puede recalificarse con esta versión.';
$string['regradeissuestructurechanged'] = 'El número de columnas o celdas ha cambiado, por lo que el intento no se puede recalificar con esta versión.';
$string['removecell'] = 'Eliminar celda';
$string['row'] = 'Fila';
$string['rowtitle'] = 'Título de la fila';
$string['rtealigncenter'] = 'Centrar';
$string['rtealignleft'] = 'Alinear a la izquierda';
$string['rtealignright'] = 'Alinear a la derecha';
$string['rtebold'] = 'Negrita';
$string['rtebullet'] = 'Lista con viñetas';
$string['rteclear'] = 'Quitar formato';
$string['rteitalic'] = 'Cursiva';
$string['rtelink'] = 'Insertar enlace';
$string['rtelinkprompt'] = 'URL del enlace:';
$string['rtenumbered'] = 'Lista numerada';
$string['rteunderline'] = 'Subrayado';
$string['selectcell'] = 'Seleccionar: {$a}';
$string['selectedcell'] = 'Seleccionada: {$a}. Elija una celda en una columna vecina.';
$string['showclear'] = 'Mostrar el botón «Borrar conexiones»';
$string['showmissing'] = 'Mostrar conexiones faltantes';
$string['showmissing_help'] = 'Cuando está activado, las conexiones correctas faltantes (líneas ámbar) se muestran al estudiante después del envío, siempre que las opciones de revisión permitan mostrar la respuesta correcta. Desactivado de forma predeterminada.';
$string['sizelarge'] = 'grande';
$string['sizemedium'] = 'mediana';
$string['sizesmall'] = 'pequeña';
$string['statsconnected'] = '{$a}: conectadas';
$string['statsnotconnected'] = '{$a}: no conectadas';
$string['uploadedimage'] = 'Imagen subida';
