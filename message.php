<?php
require_once("../../config.php");
require_once("lib.php");
require_once($CFG->libdir.'/weblib.php');
require_login();
echo get_string($_POST["str"],"sword");

