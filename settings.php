<?php
/**
 * This file adds the settings pages to the navigation menu
 *
 *
 *
 *
 */
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
		require_once($CFG->dirroot.'/mod/sword/lib.php');
    $options = array(0=>get_string('produccion','sword'), 1=>get_string('desarrollo','sword'));
    $str = get_string('config_prod_o_desarrollo', 'sword');
    $settings->add(new admin_setting_configselect('sword_select_repo',
                                    get_string('prod_o_desarrollo', 'sword'),
                                    $str, 0, $options));
}

