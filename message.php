<?php
defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');
require_once("../../config.php");
require_once("lib.php");
echo get_string($_POST["str"],"sword");
