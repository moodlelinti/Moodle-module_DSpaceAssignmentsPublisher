<?php
//defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');
require_once("../../config.php");
require_once("lib.php");
require_once($CFG->libdir.'/weblib.php');
require_login();
if(has_capability('mod/sword:view',context_user::instance($USER->id))){
	echo get_string($_POST["str"],"sword");
}
