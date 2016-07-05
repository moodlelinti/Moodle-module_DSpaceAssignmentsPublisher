<?php
/**
 * This file adds the settings pages to the navigation menu
 *
 *
 *
 *
 */
defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot.'/mod/sword/getCollections.php');
require_once($CFG->dirroot.'/mod/sword/locallib.php');
 $PAGE->requires->js('/mod/sword/js/jquery.js', true);
 $PAGE->requires->js('/mod/sword/js/settings.js',true);
if ($ADMIN->fulltree) {
/* $empty= false; //marca si se selecciono una opcion o se dejo el valor vacio en el combobox.
	if(!class_exists('admin_setting_sword_configselect')){

		class  admin_setting_sword_configselect extends  admin_setting_configselect{
			public function validate($data){
				global $empty;
				error_log("llegue");
				error_log($data);				
				if($data="2"){
					$empty=true;			
				}
				else{
					$empty=false;			
				}
			}
		}
	}*/
	if(!class_exists('custom_admin_setting_sword_configtext')){
		
		class custom_admin_setting_sword_configtext extends admin_setting_configtext{
				public function validate($data){
					//global $empty;
					//error_log("apa");
					/*if(!$empty){return true;}
					error_log("empty es true");*/
					$retrieve = new RetrieveCollections();
					if($retrieve->hasCollections($data)){
						return true;				
					}
					else{
						return "El valor ingresado no corresponde a un repositorio valido";					
					}
			}

		}

	}
		require_once($CFG->dirroot.'/mod/sword/lib.php');
    $options = array(0=>get_string('produccion','sword'), 1=>get_string('desarrollo','sword'),2=>" ");
    $str = get_string('config_prod_o_desarrollo', 'sword');
    $combobox=$settings->add(new admin_setting_configselect('sword_select_repo',
                                    get_string('prod_o_desarrollo', 'sword'),
                                    $str, 0, $options));
		$str = get_string('config_url_repo','sword');
		$settings->add(new custom_admin_setting_sword_configtext('sword_repo_url',get_string('url_repo','sword'),$str,"vacio"));
}

