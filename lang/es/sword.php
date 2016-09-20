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
 * English strings for sword
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod
 * @subpackage sword
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'SWORD';
$string['sendtorepo'] = 'Enviar al repositorio';
$string['assignment_list']='Lista de Tareas';
$string['assignment']='Tarea';
$string['msg_error']='Hubo errores en el envío';
$string['msg_send']='El envío se realizó con éxito';
$string['modulenameplural'] = 'swords';
$string['modulename_help'] = 'El módulo SWORD es una extensión del módulo de Tareas que permite a un profesor exportar las entregas realizadas por los alumnos a un repositorio digital DSpace. Dichas entregas pertenecen a cada una de las tareas creadas por el docente a través del módulo Tareas.
Se debe crear una actividad SWORD por cada repositorio que quiera asociar. El docente debe establecer la colección donde dichos recursos se publicarán y un usuario y contraseña para poder realizar el depósito. También podrá definir valores por defecto para algunos metadatos en el estándar Dublin Core simplificado.
A su vez, el alumno puede proveer metadatos subject (palabras claves) para completar su recurso, a través de un archivo .txt con una palabra clave por línea.
Éste módulo recolecta, formatea e incorpora metadatos de forma automática a través del contexto donde su ubica la tarea y por medio de metadatos que el docente puede brindar.
Funciona para los módulos Tareas y Tareas 2.2, y publica todo tipo de entrega';
$string['nosend']='No enviado'; 
$string['send']='Enviado';
$string['error']='Error al enviar'; 
$string['repositoryurl'] = 'Seleccione el repositorio';
$string['repository']='Información del repositorio ';
$string['username']='Nombre de Usuario';
$string['password']='Contraseña';
$string['metadata']='Valores de los metadatos por defecto';
$string['abstract']='Resumen';
$string['subject']='Palabras claves';
$string['programminglanguage']='Lenguaje de programacion';
$string['rights']='Derechos';
$string['language']='Idioma';
$string['teacher']=' Docente';
$string['teachermail']='Mail del docente';
$string['publisher']='Publicador';
$string['swordname'] = 'Nombre';
$string['msg-repository']="Complete la url con la ubicación de SWORD y el número del handle de la colección donde se depositará";
$string['swordname_help'] = 'This is the content of the help tooltip associated with the swordname field. Markdown syntax is supported.';
$string['sword'] = 'SWORD';
$string['pluginadministration'] = 'administración de SWORD';
$string['pluginname'] = 'SWORD';
$string['selectcollection'] = 'Seleccione una colección';
$string['publish_status']   = "Estado de envío";
$string['search_collection'] = "Seleccione una colección del repositorio";
$string['non_selected'] = "No ha seleccionado ninguna entrega";
$string['cannot_get_collections'] = "No se han podido obtener las colecciones";
$string['url_collection'] = "Ingrese la url de la colección";
$string['config_prod_o_desarrollo'] = "Esta opcion le permite al administrador configurar el destino de los envios que maneje este módulo";
$string['prod_o_desarrollo'] = "Seleccione el destino para los envios";
$string['produccion'] = "Producción";
$string['desarrollo'] = "Desarrollo";
$string['prod_repo'] = "URL producción";
$string['dev_repo'] = "URL desarrollo";
$string['config_url_repo'] = 'Este campo le permite ingresar la url manualmente';
$string['setting_URL_error'] = 'El valor ingresado no corresponde a un repositorio valido';
$string['item_present'] = 'Presente en la coleccion destino';
$string['item_absent'] = ' ';
