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

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/accesslib.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/repository/lib.php');
require_once($CFG->dirroot . '/mod/assign/mod_form.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->dirroot . '/grade/grading/lib.php');
require_once($CFG->dirroot . '/mod/assign/feedbackplugin.php');
require_once($CFG->dirroot . '/mod/assign/submissionplugin.php');
require_once($CFG->dirroot . '/mod/assign/renderable.php');
require_once($CFG->dirroot . '/mod/sword/sword_gradingtable.php');
require_once($CFG->libdir . '/eventslib.php');
require_once($CFG->libdir . '/portfolio/caller.php');
require_once($CFG->dirroot . '/mod/sword/sword_submissions_form.php');
require_once($CFG->dirroot . '/mod/sword/renderer.php');
/**
 * Internal library of functions for module sword
 *
 * All the sword specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package    mod_sword
 * @copyright  2014 Maria Emilia Charnelli
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/mod/assign/locallib.php');


  

class sword_assign extends assign {
  
  private $cm_sword;  
  private $context;
  private $cm;
  private $submissionplugins;
  private $feedbackplugins;
  
  /** @var assign_renderer the custom renderer for this module */
  private $output;
  
  public function __construct($context,$cm,$course, $cm_sword)
  {
     parent::__construct($context, $cm, $course);     
     $this->cm      = $cm;
     $this->context = $context;   
     $this->cm_sword = $cm_sword;
     
     $this->submissionplugins = $this->load_plugins('assignsubmission');
     $this->feedbackplugins = $this->load_plugins('assignfeedback');
  }

public function view( $action='grading') {
      
        $o = '';
        $mform = null;
        $notices = array();
        $nextpageparams = array();
        
        $swordid  = $this->cm_sword->id;
        $cm  = $this->cm;
        //$assignment  = $this->assignment;
        

        if (!empty($this->get_course_module()->id)) {
            $nextpageparams['id'] = $this->get_course_module()->id;
        }

        // Handle form submissions first.
        if ($action == 'savesubmission') {
            $action = 'editsubmission';
            if ($this->process_save_submission($mform, $notices)) {
                $action = 'redirect';
                $nextpageparams['action'] = 'view';
            }
        } else if ($action == 'editprevioussubmission') {
            $action = 'editsubmission';
            if ($this->process_copy_previous_attempt($notices)) {
                $action = 'redirect';
                $nextpageparams['action'] = 'editsubmission';
            }
        } else if ($action == 'addattempt') {
            $this->process_add_attempt(required_param('userid', PARAM_INT));
            $action = 'redirect';
            $nextpageparams['action'] = 'grading';
        } else if ($action == 'reverttodraft') {
            $this->process_revert_to_draft();
            $action = 'redirect';
            $nextpageparams['action'] = 'grading';
        } else if ($action == 'unlock') {
            $this->process_unlock_submission();
            $action = 'redirect';
            $nextpageparams['action'] = 'grading';
        } else if ($action == 'setbatchmarkingworkflowstate') {
            $this->process_set_batch_marking_workflow_state();
            $action = 'redirect';
            $nextpageparams['action'] = 'grading';
        } else if ($action == 'setbatchmarkingallocation') {
            $this->process_set_batch_marking_allocation();
            $action = 'redirect';
            $nextpageparams['action'] = 'grading';
        } else if ($action == 'confirmsubmit') {
            $action = 'submit';
            if ($this->process_submit_for_grading($mform)) {
                $action = 'redirect';
                $nextpageparams['action'] = 'view';
            }
        } else if ($action == 'gradingbatchoperation') {
            $action = $this->process_grading_batch_operation($mform,$this->context,$swordid, $cm);
            if ($action == 'grading') {
                $action = 'redirect';
                $nextpageparams['action'] = 'grading';
            }
        } else if ($action == 'submitgrade') {
            if (optional_param('saveandshownext', null, PARAM_RAW)) {
                // Save and show next.
                $action = 'grade';
                if ($this->process_save_grade($mform)) {
                    $action = 'redirect';
                    $nextpageparams['action'] = 'grade';
                    $nextpageparams['rownum'] = optional_param('rownum', 0, PARAM_INT) + 1;
                    $nextpageparams['useridlistid'] = optional_param('useridlistid', time(), PARAM_INT);
                }
            } else if (optional_param('nosaveandprevious', null, PARAM_RAW)) {
                $action = 'redirect';
                $nextpageparams['action'] = 'grade';
                $nextpageparams['rownum'] = optional_param('rownum', 0, PARAM_INT) - 1;
                $nextpageparams['useridlistid'] = optional_param('useridlistid', time(), PARAM_INT);
            } else if (optional_param('nosaveandnext', null, PARAM_RAW)) {
                $action = 'redirect';
                $nextpageparams['action'] = 'grade';
                $nextpageparams['rownum'] = optional_param('rownum', 0, PARAM_INT) + 1;
                $nextpageparams['useridlistid'] = optional_param('useridlistid', time(), PARAM_INT);
            } else if (optional_param('savegrade', null, PARAM_RAW)) {
                // Save changes button.
                $action = 'grade';
                if ($this->process_save_grade($mform)) {
                    $action = 'redirect';
                    $nextpageparams['action'] = 'savegradingresult';
                }
            } else {
                // Cancel button.
                $action = 'redirect';
                $nextpageparams['action'] = 'grading';
            }
        } else if ($action == 'quickgrade') {
            $message = $this->process_save_quick_grades();
            $action = 'quickgradingresult';
        } else if ($action == 'saveoptions') {
            $this->process_save_grading_options();
            $action = 'redirect';
            $nextpageparams['action'] = 'grading';
        } else if ($action == 'saveextension') {
            $action = 'grantextension';
            if ($this->process_save_extension($mform)) {
                $action = 'redirect';
                $nextpageparams['action'] = 'grading';
            }
        } else if ($action == 'revealidentitiesconfirm') {
            $this->process_reveal_identities();
            $action = 'redirect';
            $nextpageparams['action'] = 'grading';
        }

        $returnparams = array('rownum'=>optional_param('rownum', 0, PARAM_INT),
                              'useridlistid'=>optional_param('useridlistid', 0, PARAM_INT));
        $this->register_return_link($action, $returnparams);

        // Now show the right view page.
        if ($action == 'redirect') {
            $nextpageurl = new moodle_url('/mod/assign/view.php', $nextpageparams);
            redirect($nextpageurl);
            return;
        } else if ($action == 'savegradingresult') {
            $message = get_string('gradingchangessaved', 'assign');
            $o .= $this->view_savegrading_result($message);
        } else if ($action == 'quickgradingresult') {
            $mform = null;
            $o .= $this->view_quickgrading_result($message);
        } else if ($action == 'grade') {
            $o .= $this->view_single_grade_page($mform);
        } else if ($action == 'viewpluginassignfeedback') {
            $o .= $this->view_plugin_content('assignfeedback');
        } else if ($action == 'viewpluginassignsubmission') {
            $o .= $this->view_plugin_content('assignsubmission');
        } else if ($action == 'editsubmission') {
            $o .= $this->view_edit_submission_page($mform, $notices);
        } else if ($action == 'grading') {
            $o .= $this->view_grading_page();
        } else if ($action == 'downloadall') {
            $o .= $this->download_submissions();
        } else if ($action == 'submit') {
            $o .= $this->check_submit_for_grading($mform);
        } else if ($action == 'grantextension') {
            $o .= $this->view_grant_extension($mform);
        } else if ($action == 'revealidentities') {
            $o .= $this->view_reveal_identities_confirm($mform);
        } else if ($action == 'plugingradingbatchoperation') {
            $o .= $this->view_plugin_grading_batch_operation($mform);
        } else if ($action == 'viewpluginpage') {
             $o .= $this->view_plugin_page();
        } else if ($action == 'viewcourseindex') {
             $o .= $this->view_course_index();
        } else if ($action == 'viewbatchsetmarkingworkflowstate') {
             $o .= $this->view_batch_set_workflow_state($mform);
        } else if ($action == 'viewbatchmarkingallocation') {
            $o .= $this->view_batch_markingallocation($mform);
        } else {
            $o .= $this->view_submission_page();
        }

        return $o;
    }
        /**
     * Lazy load the page renderer and expose the renderer to plugins.
     *
     * @return assign_renderer
     */
    public function get_renderer() {
        global $PAGE;
        if ($this->output) {
            return $this->output;
        }
        $this->output = $PAGE->get_renderer('mod_sword');
        return $this->output;
    }
    
    /**
     * View entire grading page.
     *
     * @return string
     */
    protected function view_grading_page() {
        global $CFG;

        $o = '';
        // Need submit permission to submit an assignment.
        require_capability('mod/assign:grade', $this->context);
        require_once($CFG->dirroot . '/mod/assign/gradeform.php');

        // Only load this if it is.

        $o .= $this->view_publish_table();

        $o .= $this->view_footer();

        $logmessage = get_string('viewsubmissiongradingtable', 'assign');
        $this->add_to_log('view submission grading table', $logmessage);
        return $o;
    }
    
    
     /**
     * View the grading table of all submissions for this assignment.
     *
     * @return string
     */
    protected function view_publish_table() {
        global $USER, $CFG;

        // Include grading options form.
        require_once($CFG->dirroot . '/mod/sword/gradingoptionsform.php');
        require_once($CFG->dirroot . '/mod/assign/quickgradingform.php');
        require_once($CFG->dirroot . '/mod/assign/gradingbatchoperationsform.php');
        $o = '';
        $cmid = $this->get_course_module()->id;

        $links = array();
        

        if ($this->is_any_submission_plugin_enabled() && $this->count_submissions()) {
             $downloadurl= new moodle_url("#");
           
        }
        

        foreach ($this->get_feedback_plugins() as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                foreach ($plugin->get_grading_actions() as $action => $description) {
                    $url = '/mod/assign/view.php' .
                           '?id=' .  $cmid .
                           '&plugin=' . $plugin->get_type() .
                           '&pluginsubtype=assignfeedback' .
                           '&action=viewpluginpage&pluginaction=' . $action;
                    $links[$url] = $description;
                }
            }
        }

        // Sort links alphabetically based on the link description.
        core_collator::asort($links);

        $perpage = get_user_preferences('assign_perpage', 10);
        $filter = ASSIGN_FILTER_SUBMITTED;
        $markerfilter = get_user_preferences('assign_markerfilter', '');
        $workflowfilter = get_user_preferences('assign_workflowfilter', '');
        $showonlyactiveenrolopt = has_capability('moodle/course:viewsuspendedusers', $this->context);

        $markingallocation = $this->get_instance()->markingallocation &&
            has_capability('mod/assign:manageallocations', $this->context);
        // Get markers to use in drop lists.
        $markingallocationoptions = array();
        if ($markingallocation) {
            $markers = get_users_by_capability($this->context, 'mod/assign:grade');
            $markingallocationoptions[''] = get_string('filternone', 'assign');
            foreach ($markers as $marker) {
                $markingallocationoptions[$marker->id] = fullname($marker);
            }
        }

        $markingworkflow = $this->get_instance()->markingworkflow;
        // Get marking states to show in form.
        $markingworkflowoptions = array();
        if ($markingworkflow) {
            $notmarked = get_string('markingworkflowstatenotmarked', 'assign');
            $markingworkflowoptions[''] = get_string('filternone', 'assign');
            $markingworkflowoptions[ASSIGN_MARKING_WORKFLOW_STATE_NOTMARKED] = $notmarked;
            $markingworkflowoptions = array_merge($markingworkflowoptions, $this->get_marking_workflow_states_for_current_user());
        }

       

        $batchformparams = array('course'    =>$this->cm->course,
                                 'sword'     => $this->cm_sword->id,
                                 'assignment'=> $this->cm->id);
        $classoptions = array('class'=>'sword_form');
        
      
       $gradingbatchoperationsform = new sword_submisison_form('#',
							      $batchformparams,
                                                               'post',
                                                               '',
                                                               $classoptions);
	

 
        $header = new assign_header($this->get_instance(),
                                    $this->get_context(),
                                    false,
                                    $this->get_course_module()->id,
                                    get_string('grading', 'assign'),""
                                    );
        $o .= $this->get_renderer()->render($header);

        $currenturl = $CFG->wwwroot .
                      '/mod/assign/view.php?id=' .
                      $this->get_course_module()->id .
                      '&action=grading';

        $o .= groups_print_activity_menu($this->get_course_module(), $currenturl, true);

        // Plagiarism update status apearring in the grading book.
        if (!empty($CFG->enableplagiarism)) {
            require_once($CFG->libdir . '/plagiarismlib.php');
            $o .= plagiarism_update_status($this->get_course(), $this->get_course_module());
        }

        // Load and print the table of submissions.
        $gradingtable = new sword_publish_table($this, $perpage, $filter, 0, false,null, $this->cm_sword);
            $o .= $this->get_renderer()->render($gradingtable);
      

        $currentgroup = groups_get_activity_group($this->get_course_module(), true);
        $users = array_keys($this->list_participants($currentgroup, true));
        if (count($users) != 0) {
            // If no enrolled user in a course then don't display the batch operations feature.
            $assignform = new assign_form('gradingbatchoperationsform', $gradingbatchoperationsform);
            $o .= $this->get_renderer()->render($assignform);
        }
      
        return $o;
    }
    
    /**
     * Ask the user to confirm they want to perform this batch operation
     *
     * @param moodleform $mform Set to a grading batch operations form
     * @return string - the page to view after processing these actions
     */
    protected function process_grading_batch_operation(& $mform) {
        global $CFG;
        require_once($CFG->dirroot . '/mod/assign/gradingbatchoperationsform.php');
        require_sesskey();
        throw new Exception("AAA");
        $markingallocation = $this->context->get_instance()->markingallocation &&
            has_capability('mod/assign:manageallocations', $this->context);
    
        $batchformparams = array('cm'=>$this->get_course_module()->id,
                                 'submissiondrafts'=>$this->get_instance()->submissiondrafts,
                                 'duedate'=>$this->get_instance()->duedate,
                                 'attemptreopenmethod'=>$this->get_instance()->attemptreopenmethod,
                                 'feedbackplugins'=>$this->get_feedback_plugins(),
                                 'context'=>$this->get_context(),
                                 'markingworkflow'=>$this->get_instance()->markingworkflow,
                                 'markingallocation'=>$markingallocation,
                         );
        $formclasses = array('class'=>'gradingbatchoperationsform');
        $mform = new mod_assign_grading_batch_operations_form(null,
                                                              $batchformparams,
                                                              'post',
                                                              '',
                                                              $formclasses);
                                                              

        if ($data = $mform->get_data()) {
	      // Get the list of users.
	      $users = $data->selectedusers;
	      $userlist = explode(',', $users);

	      $prefix = 'plugingradingbatchoperation_';

	      $this->sword_submissions($userlist);
	      if ($this->get_instance()->teamsubmission && $data->operation == 'addattempt') {
		  // This needs to be handled separately so that each team submission is only re-opened one time.
		  $this->process_add_attempt_group($userlist);
	      }
        }

        $this->view2('grading');
    }
    
    /**
     * Download a zip file of all assignment submissions.
     *
     * @return string - If an error occurs, this will contain the error page.
     */
    public function sword_submissions($userselected) {
        
        global $CFG, $DB;
        $context = context_module::instance($this->cm->id);
        
        // More efficient to load this here.
        require_once($CFG->libdir.'/filelib.php');

        require_capability('mod/assign:grade', $context);

        // Load all users with submit.
        $students = get_enrolled_users($context, "mod/assign:submit", null, 'u.*', null, null, null,
                        null);
        $students_selected = array();
        foreach ($students as $student) {
           if (in_array($student->id, $userselected)) {
                $students_selected[]=$student;
           }
        }
        
         $this->rand_dir_name = substr(chr( mt_rand( ord( 'a' ) ,ord( 'z' ) ) ) .substr( md5( time( ) ) ,1 ),3,9);
         $this->output_directory = $CFG->dataroot . '/sword/'.$this->rand_dir_name . '/' ;

	@mkdir($this->output_directory, $CFG->directorypermissions, true );
        
        // Build a list of files to zip.
        $filesforzipping = array();
        $fs = get_file_storage();

        $groupmode = groups_get_activity_groupmode($this->get_course_module());
        // All users.
        $groupid = 0;
        $groupname = '';
        if ($groupmode) {
            $groupid = groups_get_activity_group($this->get_course_module(), true);
            $groupname = groups_get_group_name($groupid).'-';
        }

       $error = false;
           $sword_metadata=$DB->get_record('sword', array('id' => $this->cm_sword->instance));
        // Get all the files for each student.
        foreach ($students_selected as $student) {
            $userid = $student->id;

            if ((groups_is_member($groupid, $userid) or !$groupmode or !$groupid)) {
                // Get the plugins to add their own files to the zip.

                $submissiongroup = false;
                $groupname = '';
                if ($this->get_instance()->teamsubmission) {
                    $submission = $this->get_group_submission($userid, 0, false);
                    $submissiongroup = $this->get_submission_group($userid);
                    if ($submissiongroup) {
                        $groupname = $submissiongroup->name . '-';
                    } else {
                        $groupname = get_string('defaultteam', 'assign') . '-';
                    }
                } else {
                    $submission = $this->get_user_submission($userid, false);
                }
                $arr=array();
                
                if ($submission) {                    
                    $filesdata=array();  
                    foreach ($this->submissionplugins as $plugin) {
                         
                        if ($plugin->is_enabled() && $plugin->is_visible()) {
                            $pluginfiles = $plugin->get_files($submission, $student);
                            
                                             
                            foreach ($pluginfiles as $zipfilename => $file) {
                              if ($file instanceof stored_file) { 
				  
				  $filetitle=$file->get_filename();
                  // $aux= $this->handleLine("hola;comoestas");
		
				  $newstring = substr($filetitle, -3);
                  if($newstring=="txt"){
				    $contents = $file->get_content();
                    $arr[] = explode("\n", $contents);                
				  }


                    /*
                    PARA VER LOS CODIGOS   PRUEBA
                    " " (ASCII 32 (0x20)), espacio simple.
                    "\t" (ASCII 9 (0x09)), tabulación.
                    "\n" (ASCII 10 (0x0A)), salto de línea.
                    "\r" (ASCII 13 (0x0D)), retorno de carro.
                    "\0" (ASCII 0 (0x00)), el byte NUL.
                    "\x0B" (ASCII 11 (0x0B)), tabulación vertical.
                    */
								
               if($filetitle =="autores.txt"){
                    $contents2 = $file->get_content();
                    $eolchar = $this->detectEOLType($contents2);
                    if(strpos($contents2,"\r\n") !== false){
                 			$arr2 = explode("\r\n", $contents2); /*llamo a la funcion detectEOLTypes para obtener el caracter que divide el string por lineas y usarlo en el explode*/
										}else{
											$arr2= explode($eolchar,$contents2);
											}
										$arr2=$this->remove_empty_slots($arr2);									
                    $all_authors = array();
                    for ($i=0;$i<count($arr2);$i++)  
                    {
												$esLineaValida=true;
                        $st = $this-> handleLine($arr2[$i],$esLineaValida); // Llama a la funcion, retorna un arreglo con $st[0] Nombre y $st[1] correoElecctronico, si la linea no esta vacia /
												if($esLineaValida){                        
													array_push($all_authors, $st); // Guarda el arreglo $st en el arreglo $arr3 //
                    	}
		    						}
                    continue;
                }
					    

				  		$this->copyFileToTemp($file);
				  
				 			$filesdata[] = array (
	                                   "filename" => $file->get_filename(),	    
	                                   "mimetype" => $file->get_mimetype(),
	                          );				  
                              } else {
                                  $this->createFileInTemp("Content.html",$file[0]);                                  
                                  $filesdata[] = array (
	                                   "filename" => "Content.html",	    
	                                   "mimetype" => "text/html",
	                          );                                  
                             } 
                          }
                            
                        }
                        
                    }
                    
                }
           
                $paquete = $this->makePackage($filesdata, $sword_metadata, $arr,$all_authors, $userid, $this->get_instance()->id);
                
                 
                 $resultado  = $this->sendToRepository($paquete,$submission->id, $sword_metadata);
               
                
               
                 
                 
                 $error = $error ||  $resultado;
            }
        }
        $this->delTree($this->output_directory);
        if ($error==true) {
	  echo get_string('msg_error', 'sword');
        } else {
	  echo get_string('msg_send', 'sword');
        }
        
        
    }
    private  function delTree($dir) {
       $files = array_diff(scandir($dir), array('.','..'));
       foreach ($files as $file) {
        (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
       }
      return rmdir($dir);
    } 
    
     /**
     * create xml with METS content
     * $rootin is The location of the files (without final directory)
     * $dirin is The location of the files
     * $rootout is The location to write the package out to
     * $fileout is The filename to save the package as
     */
     private function makePackage($filesdata, $sword_metadata, $arr,$all_authors, $userid,$assigid ) 
     {
        
        global $CFG,$DB;
         require_once('api/packager_mets_swap.php');
         
         $user=$DB->get_record('user', array('id' => $userid));
         $assignment=$DB->get_record('assign',array('id'=> $assigid ));
         $course=$DB->get_record('course',array('id'=>$assignment->course));
         
        
        // add context metadata   
        
        $datos=array(
        "author" => $user->firstname . ' '. $user->lastname,
        "title"  => $assignment->name . '-' . $user->lastname,
        "rootin"   => $CFG->dataroot, 
        "dirin"    => 'sword/' . $this->rand_dir_name . '/',
        "rootout"  => $CFG->dataroot . '/sword/' . $this->rand_dir_name .'/',
	"fileout"  => basename(tempnam($CFG->dataroot . '/sword/' . $this->rand_dir_name . '/', 'sword_').'.zip')
	//"fileout"  => $assignment->name . '-' . $user->lastname .'.zip'
	);

     
        
        $datos["files"]=$filesdata;
        
        
        // add default metadata
        
        
         if (($arr!=NULL) && ($sword_metadata->subject != NULL)) {                               
           $arr[]=$sword_metadata->subject;           
           $datos["subject"]=$arr;
         } else {
           if ($arr!=NULL) {
	      $datos["subject"]=array($arr);
           }
           if ($sword_metadata->subject != NULL)      {
	    $datos["subject"]=array($sword_metadata->subject);
           }
	   
         }
         if($sword_metadata->type != NULL) {
            $datos["type"]=$sword_metadata->type;
           error_log("type bien");
         }
	else{
		error_log("no se tomo el campo type del formulario");	
	}
	
         if($sword_metadata->abstrac != NULL) {
            $datos["abstract"]=$sword_metadata->abstrac;
           error_log("abstract bien");
         }
	else{
		error_log("no se tomo el campo abstract del formulario");	
	}
         
         if($sword_metadata->rights != NULL) {
            $datos["rights"]=$sword_metadata->rights;
           error_log("hasta aca bien");
         }
	else{
		error_log("no se tomo el campo rights del formulario");	
	}
         if($sword_metadata->language != NULL) {
            $datos["language"]= $sword_metadata->language;
         }
         else{
		error_log("no se tomo el campo lenguaje del formulario");	
	}

	//set the publisher with the course name 
	  //error_log(var_dump($course));
	  $datos["publisher"]= $course->fullname;

         if($sword_metadata->teacher != NULL) {
            $datos["teacher"]=$sword_metadata->teacher;
         }
        else{
		error_log("no se tomo el campo teacher del formulario");	
	}
	if($sword_metadata->programminglanguage != NULL) {
            $datos["programminglanguage"]=$sword_metadata->programminglanguage;
         }
        else{
		error_log("no se tomo el campo programming language del formulario");	
	}
	if($sword_metadata->programminglanguage != NULL) {
            $datos["teachermail"]=$sword_metadata->teachermail;
         }
        else{
		error_log("no se tomo el campo programming language del formulario");	
	}
             
        $this->makeMets($datos, $all_authors);
          
        return $datos["rootout"].$datos["fileout"];
     }
     
    /**
    * make METS package
    **/
    private function makeMets($datos,$authors) 
    {
			$packager = new PackagerMetsSwap($datos["rootin"], $datos["dirin"], $datos["rootout"], $datos["fileout"]);
      $this->loadMetadata($packager, $datos, $authors);
			$packager->create();
    }
    
    /**
    * cargar metadatos en el mets.xml
    **/
    private function loadMetadata($packager, $datos, $authors)
    {
    
		/*Modifico para agregar todos los autores y mails de autores que se reciben en el arreglo authors*/
		$i;
		for($i=0; $i < count($authors); $i++){
			$aux=$authors[$i];
			if(!empty($aux[0])){
				if(isset($aux[0])){
      		$packager->addCreator($aux[0]);
				}
			}
			
			if(!empty($aux[1])){			
				if(isset($aux[1])){
					$packager->addMailCreator($aux[1]);
			}
			}
		}
    foreach($datos["files"] as $file) {
      $packager->addFile($file["filename"], $file["mimetype"]);
    }

	$packager->setTitle($datos["title"]);
	$packager->addCreator($datos["author"]);
	
	
	if (array_key_exists("subject",$datos)){
	    foreach($datos["subject"] as $subject) {
		$packager->addSubject($subject);
	    }
	}
	if(array_key_exists("type",$datos)){
		if($datos["type"]=="software"){
		$packager->setType("http://purl.org/dc/dcmitype/Software");}
	}
	else{ error_log("no existe la clave type en el arreglo");}	
	if(array_key_exists("abstract",$datos)){
		$packager->setAbstract($datos["abstract"]);
	}
	else{ error_log("no existe la clave abstrac en el arreglo");}
	if (array_key_exists("rights",$datos)){
	    $packager->addRights($datos["rights"]);
	}
	
	if (array_key_exists("language",$datos)){
	    $packager->setLanguage($datos["language"]);
	}
	
	if (array_key_exists("publisher",$datos)){
	   $packager->setPublisher($datos["publisher"]);
	}
	/*USO campos especiales para los metadatos propios*/
	if (array_key_exists("teacher",$datos)){
	   $packager->setTeacherName($datos["teacher"]);
	}
	if (array_key_exists("teachermail",$datos)){
	   $packager->setTeacherMail($datos["teachermail"]);
	}
	if (array_key_exists("programminglanguage",$datos)){
	   $packager->setProgrammingLanguage($datos["programminglanguage"]);
	}
	
    
    }
    
     
     

     /**
     * Deposit package to repository
     * $swordid sword instance
     * $package package to deposit
     */
     private function sendToRepository($package, $submissionid, $sword) {
     global $CFG,$DB;
     
                    $dir= $this->output_directory . 'mets_swap_package.zip';
		    
                    //$sword=$DB->get_record('sword', array('id' => $swordid));
		    
		    // The URL of the service document
		    $url = $this->get_url($sword->url);
		    
		    
		    // The user (if required)
		    $user = $sword->username;
		    
		    // The password of the user (if required)
		    $pw = $sword->password;
		    

		    // Atom entry to deposit
		    $atomentry = "test-files/atom_multipart/atom";
		    
		    

		    // The test content zip file to deposit
		    $zipcontentfile = $dir;



		    // The content type of the test file
		    $contenttype = "application/zip";

		    //$packageformat="http://purl.org/net/sword-types/METSDSpaceSIP";
		    $packageformat="http://purl.org/net/sword-types/METSDSpaceSIP";
		    /*error_log($package->getTittle());     //COMENTE ESTAS DOS LINEAS POR QUE SINO NO ANDABA //package es el nombre del archivo a enviar
		    error_log($package->getPublisher());
		    */

		    /*guardo una copia del paquete a enviar antes de realizar el envio*/
		    /*if (!copy($package, $CFG->dirroot.'/mod/sword/prueba.zip')) {
    				error_log("no se pudo guardar una copia del envio");
					}*/
			
		    require_once($CFG->dirroot .'/mod/sword/api/swordappclient.php');
		    
		    
		    
		    $error = false;
		    try{
		        $sac = new SWORDAPPClient();
		        $dr = $sac->deposit($url, $user, $pw, '', $package, $packageformat,$contenttype, false);
		        //error_log($dr);
		   	
			error_log($dr->sac_status);
			if ($dr->sac_status!=201) {  
			      $status='error';
			      $error = true;
			} else {
			      $status='send';
			      $error = false;
			}
		
		   } catch(Exception $e){		      
		      $status='error';
		      $error = true;
		    
		   }
		   
		   
		   $previous_submission = $DB->get_record('sword_submissions',array('submission'=>$submissionid, 'sword'=>$sword->id ,'type'=>'assign'));
		   if ($previous_submission != NULL) {		      
		      $previous_submission->status = $status;
		      $DB->update_record('sword_submissions', $previous_submission);
		   } else {
		      $sword_submission=new stdClass();
		      $sword_submission->submission=$submissionid;
		      $sword_submission->sword=$sword->id;
		      $sword_submission->type='assign';
		      $sword_submission->status=$status;
		      $DB->insert_record('sword_submissions', $sword_submission);
		   }
		   
		   
		   
		   return $error;
     
     }
     
       /**
    * copy file to temporal directory 
    */
    private function copyFileToTemp($file) 
    {
      
      $tempFile=@fopen($this->output_directory . $file->get_filename(),"wb");                    
      if ($tempFile ) {
           fwrite($tempFile,$file->get_content());
           fclose($tempFile);  
      }              
    }
    private function createFileInTemp($filename, $content)
    {
     
      file_put_contents($this->output_directory .$filename,$content);
    }




    private  function handleLine($string,$estado) {
            //funcion que maneja una linea del archivo dividiendo por ; el nombre del correo_electronico
            //error_log("llegue a la function");
	    if((strlen($string)>3)&&(strpos($string,';')!=false)){
            	return explode(";", $string);
		}
	    else{
		$estado= false;
		return false;		
		}
       }
    private function detectEOLType($string){
	//http://stackoverflow.com/questions/11066857/detect-eol-type-using-php
	//funcion que devuelve el caracter que identifica el caracter utilizado como fin de linea en el string
	/*	
		return $eol;
	*/
    $eols = array_count_values(str_split(preg_replace("/[^\r\n]/", "", $string)));
    $eola = array_keys($eols, max($eols));
    $eol = implode("", $eola);
    $bool1= strpos("\r\n",$string != 0);
    if($bool1){return  "\r\n";}
    else{return $eol;}
    }
	private function remove_empty_slots($arr2){
			for($i= 0; $i < count($arr2);$i++){
					$aux=$arr2[$i];
					if(empty($aux)||$aux==""){
								unset($arr2[$i]);					
						}			
			}
			//var_dump($arr2);
			$arr2= array_values($arr2);
			return $arr2;
	}
  private function get_URL($url){
		if($url[0]=='1'){return "repositorio.info.unlp.edu.ar".substr($url,1);}
		if($url[0]=='2'){return "dspace-dev.linti.unlp.edu.ar".substr($url,1);}
		return "urlinvalida";	
}  
    
    

}
