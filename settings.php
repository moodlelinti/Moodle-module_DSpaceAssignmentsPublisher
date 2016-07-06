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

	if(!class_exists('custom_admin_setting_sword_configtext')){
		
		class custom_admin_setting_sword_configtext extends admin_setting_configtext{
				public function validate($data){
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
    $options = array(0=>get_string('produccion','sword'), 1=>get_string('desarrollo','sword'));
    $str = get_string('config_prod_o_desarrollo', 'sword');
    $combobox=$settings->add(new admin_setting_configselect('sword_select_repo',
                                    get_string('prod_o_desarrollo', 'sword'),
                                    $str, 0, $options));
		$str = get_string('config_url_repo','sword');
		//$settings->add(new custom_admin_setting_sword_configtext('sword_prod_url',get_string('prod_repo','sword'),$str,"vacio"));
		//$settings->add(new custom_admin_setting_sword_configtext('sword_dev_url',get_string('dev_repo','sword'),$str,"vacio"));
		$settings->add(new admin_setting_configtext('sword_prod_url',get_string('prod_repo','sword'),$str,"vacio"));
		$settings->add(new admin_setting_configtext('sword_dev_url',get_string('dev_repo','sword'),$str,"vacio"));
}

