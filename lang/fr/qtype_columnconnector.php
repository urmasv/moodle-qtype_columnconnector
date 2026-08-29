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
 * Strings for component 'qtype_columnconnector', language 'fr'.
 *
 * @package    qtype_columnconnector
 * @copyright  2026 Urmas Vessin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Connecteur de colonnes';
$string['pluginname_help'] = 'Créez une question avec 2 à 7 colonnes de cellules. Les apprenants tracent des lignes reliant les cellules de colonnes voisines ; la réponse est évaluée selon les connexions que vous définissez.';
$string['pluginname_link'] = 'question/type/columnconnector';
$string['pluginnameadding'] = 'Ajout d’une question connecteur de colonnes';
$string['pluginnameediting'] = 'Modification d’une question connecteur de colonnes';
$string['pluginnamesummary'] = 'Affiche de 2 à 7 colonnes titrées avec des cellules et permet aux apprenants de relier par des lignes les cellules de colonnes voisines. Des points sont attribués pour les connexions correctes, incorrectes et manquantes.';
$string['columnconnectorsettings'] = 'Réglages du connecteur de colonnes';
$string['numcolumns'] = 'Nombre de colonnes';
$string['numcolumns_help'] = 'Choisissez si l’activité est affichée avec deux à sept colonnes.';
$string['layoutmode'] = 'Disposition';
$string['layoutcolumns'] = 'Colonnes (vertical)';
$string['layoutrows'] = 'Lignes (horizontal)';
$string['linestyle'] = 'Style de ligne';
$string['linecurved'] = 'Courbe';
$string['linestraight'] = 'Droite';
$string['showclear'] = 'Afficher le bouton « Effacer les connexions »';
$string['showmissing'] = 'Afficher les connexions manquantes';
$string['showmissing_help'] = 'Lorsque cette option est activée, les connexions correctes manquantes (lignes ambre) sont affichées à l’apprenant après la soumission — à condition que les options de relecture autorisent l’affichage de la bonne réponse. Désactivé par défaut.';
$string['instructions'] = 'Consignes pour l’apprenant';
$string['instructions_help'] = 'Courte consigne HTML affichée au-dessus des colonnes.';
$string['correctpoints'] = 'Points pour une connexion correcte';
$string['correctpoints_help'] = 'Par défaut : +1 point pour chaque connexion correcte. La note globale de la question est calculée à partir de ces points.';
$string['incorrectpoints'] = 'Points pour une connexion incorrecte';
$string['missingpoints'] = 'Points pour une connexion manquante';
$string['clearconnections'] = 'Effacer les connexions';
$string['emptycolumn'] = 'Il n’y a aucune cellule dans cette colonne.';
$string['invalidtarget'] = 'Vous ne pouvez relier qu’à une cellule d’une colonne voisine.';
$string['duplicate'] = 'Cette connexion existe déjà.';
$string['selectedcell'] = 'Cellule sélectionnée. Choisissez une cellule dans une colonne voisine.';
$string['pleaseconnect'] = 'Veuillez établir au moins une connexion.';
$string['errornumcolumns'] = 'Le nombre de colonnes doit être compris entre 2 et 7.';
$string['errormissingcolumns'] = 'Le contenu doit définir au moins autant de colonnes que sélectionné.';
$string['erroremptycolumn'] = 'La colonne {$a} doit contenir au moins une cellule.';
$string['erroranswerkey'] = 'Une connexion correcte est mal formée ou ne relie pas des colonnes voisines.';
$string['erroranswerkeyindex'] = 'Une connexion correcte renvoie à une cellule qui n’existe pas.';
$string['contenteditor'] = 'Colonnes, cellules et connexions correctes';
$string['h5pimport'] = 'Importer depuis H5P';
$string['h5pimport_help'] = 'Choisissez un fichier H5P.ColumnConnector (.h5p) existant et cliquez sur « Charger depuis H5P ». Le contenu (colonnes, cellules, connexions, images) est chargé dans l’éditeur ; vérifiez et enregistrez.';
$string['h5pimportload'] = 'Charger depuis H5P';
$string['h5pimporterror'] = 'Impossible de lire le fichier H5P.';
$string['h5pimportwrongtype'] = 'Ce n’est pas un contenu H5P.ColumnConnector.';
$string['column'] = 'Colonne';
$string['columntitle'] = 'Titre de la colonne';
$string['cell'] = 'Cellule';
$string['celltext'] = 'Texte';
$string['imageurl'] = 'URL de l’image';
$string['imageposition'] = 'Position de l’image';
$string['imageabove'] = 'Au-dessus du texte';
$string['imageleft'] = 'À gauche du texte';
$string['imageleftsize'] = 'Taille de l’image à gauche';
$string['imagealignleft'] = 'à gauche';
$string['imagealigncenter'] = 'centré';
$string['sizesmall'] = 'petite';
$string['sizemedium'] = 'moyenne';
$string['sizelarge'] = 'grande';
$string['imagealt'] = 'Texte alternatif de l’image';
$string['addcell'] = 'Ajouter une cellule';
$string['removecell'] = 'Supprimer la cellule';
$string['moveup'] = 'Monter';
$string['movedown'] = 'Descendre';
$string['correctconnections'] = 'Connexions correctes vers la colonne précédente';
$string['noconnection'] = 'aucune connexion';
$string['connectioncount'] = '{$a} connexions';
$string['noneighbourcells'] = 'La colonne précédente n’a aucune cellule.';
$string['cellfallback'] = 'cellule';
$string['defaultinstructions'] = 'Reliez les cellules aux cellules des colonnes voisines. Une cellule peut être reliée à plusieurs cellules de la colonne voisine.';
$string['errorinvalidcontent'] = 'Le contenu de la question est incomplet ou invalide.';
$string['editorloaderror'] = 'L’éditeur n’a pas pu se charger. Purgez les caches (Administration du site → Développement → Purger les caches) et réessayez.';
$string['cellimages'] = 'Images des cellules';
$string['cellimages_help'] = 'Téléversez des images à utiliser dans les cellules. Choisissez ensuite une image téléversée dans la liste déroulante de chaque cellule. Une cellule peut également utiliser une URL d’image externe.';
$string['uploadedimage'] = 'Image téléversée';
$string['noimage'] = '— aucune image —';
$string['refreshimages'] = 'Actualiser la liste des images';
$string['rtebold'] = 'Gras';
$string['rteitalic'] = 'Italique';
$string['rteunderline'] = 'Souligné';
$string['rtebullet'] = 'Liste à puces';
$string['rtenumbered'] = 'Liste numérotée';
$string['rtelink'] = 'Insérer un lien';
$string['rtelinkprompt'] = 'URL du lien :';
$string['rtealignleft'] = 'Aligner à gauche';
$string['rtealigncenter'] = 'Centrer';
$string['rtealignright'] = 'Aligner à droite';
$string['rteclear'] = 'Effacer la mise en forme';
$string['privacy:metadata'] = 'Le plugin de type de question Connecteur de colonnes ne stocke aucune donnée personnelle.';
