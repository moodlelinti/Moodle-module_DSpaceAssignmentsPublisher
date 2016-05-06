<?php
require_once("../../config.php");
require_once("lib.php");
require_once($CFG->libdir.'/plagiarismlib.php');
try {

	require_login();
	//error_log("mi id de usuario es: ".$USER->id);
	//error_log("puedo enviar al repo 1 es si".has_capability('mod/sword:selectrepo',context_user::instance($USER->id)));
	//error_log(var_dump($PAGE));	
	//$context = context_module::instance();
			
	//error_log(var_dump(get_users_by_capability($context, 'mod/sword:selectrepo')));
	//if(has_capability('mod/sword:view',context_user::instance($USER->id))){
		$swordid        = required_param('swordid',PARAM_INT);          // SWORD ID
		$course_id             = required_param('id',PARAM_INT);          // Course module ID
		$submissions    = required_param_array('submissions',PARAM_INT);// submissions selected
		$assignment_id  = required_param('assignment_id',PARAM_INT);// submissions selected
		$user						= required_param('user',PARAM_STRINGID);
		$password				=	required_param('password',PARAM_STRINGID);
		
		$cm = get_coursemodule_from_id('assign', $assignment_id, 0, false, MUST_EXIST);
		$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
		$cm_sword = get_coursemodule_from_id('sword', $swordid, 0, false, MUST_EXIST);



		require_login($course, true, $cm);

		/// Load up the required assignment code

		require($CFG->dirroot.'/mod/sword/locallib.php');

		$context= context_module::instance($cm->id);
		$sword_assign = new sword_assign($context,$cm,$course,$cm_sword);
		$sword_assign->sword_submissions($submissions,$user,$password);

} catch(Exception $e)  {
  echo $e;
  echo get_string('msg_error', 'sword');
}

