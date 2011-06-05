<?php  



/*

 * @copyright &copy; 2007 University of London Computer Centre

 * @author http://www.ulcc.ac.uk, http://moodle.ulcc.ac.uk

 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License

 * @package ILP

 * @version 1.0

 */



    require_once("../../config.php");
    require_once($CFG->dirroot.'/blocks/ilp/block_ilp_lib.php');
    require_once("lib.php");
    //////////////////////////////////////////////////////////
    require_once($CFG->dirroot.'/message/lib.php');

    global $CFG, $USER;

		

    $id = optional_param('id', 0, PARAM_INT); // Course Module ID, or

    $a  = optional_param('a', 0, PARAM_INT);  // concerns ID

	$userid = optional_param('userid', 0, PARAM_INT); //User's concerns we wish to view

	$courseid     = optional_param('courseid', 0, PARAM_INT); 

	$status = optional_param('status', 0, PARAM_INT);
	$studentstatus = optional_param('studentstatus', 0, PARAM_INT);
	
	$concernspost = optional_param('concernspost', 0, PARAM_INT);
	$action = optional_param('action',NULL, PARAM_CLEAN);
	$template = optional_param('template',0,PARAM_INT);
	

	

	require_login();

    

    add_to_log($userid, "concerns", "view", "view.php", "$userid");

    

/// Print the main part of the page

	if ($userid > 0){

		$user = get_record('user', 'id', ''.$userid.'');

	}else{

		$user = $USER;

	}

	

	$strconcerns = get_string("modulenameplural", "ilpconcern");

    $strconcern  = get_string("modulename", "ilpconcern");

    $strilp = get_string("ilp", "block_ilp");

    $stredit = get_string("edit");

    $strdelete = get_string("delete");

    $strcomments = get_string("comments", "ilpconcern");

	

	if($id > 0){ //module is accessed through a course module use course context 

		if (! $cm = get_record("course_modules", "id", $id)) {

            error("Course Module ID was incorrect");

        }

    

        if (! $course = get_record("course", "id", $cm->course)) {

            error("Course is misconfigured");

        }

    

        if (! $concerns = get_record("ilpconcern", "id", $cm->instance)) {

            error("Course module is incorrect");

        }

		$context = get_context_instance(CONTEXT_MODULE, $cm->id);

		$link_values = '?id='.$cm->id.'&amp;userid='.$user->id;

		

		$navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> -> <a href=\"../../blocks/ilp/view.php?courseid=$course->id&amp;id=$user->id\">$strilp</a>";		

		print_header("$strconcerns: ".fullname($user)."", "$course->fullname",

                 "$navigation -> ".$strconcerns." -> ".fullname($user)."", 

                  "", "", true, update_module_button($cm->id, $course->id, $strconcerns), 

                  navmenu($course, $cm));

		

		$baseurl = $CFG->wwwroot.'/mod/ilpconcern/view.php?id='.$id.'&amp;userid='.$user->id;

		$footer = $course;

    }elseif ($courseid > 0) { //module is accessed via report from within course 



	$course = get_record('course', 'id', $courseid);

		$context = get_context_instance(CONTEXT_COURSE, $course->id);

		$link_values = '?courseid='.$course->id.'&amp;userid='.$user->id;

		$navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> -> <a href=\"../../blocks/ilp/view.php?courseid=$course->id&amp;id=$user->id\">$strilp</a>";		

		print_header("$strconcerns: ".fullname($user)."", "$course->fullname",

                 "$navigation -> ".$strconcerns." -> ".fullname($user)."", 

                  "", "", true, "", "");

		

		$baseurl = $CFG->wwwroot.'/mod/ilptarget/view.php?id='.$id.'&amp;userid='.$user->id;

		$footer = $course;





	}else{ //module is accessed independent of a course use user context

		if($user->id == $USER->id) {
			$context = get_context_instance(CONTEXT_SYSTEM);
		}else{
			$context = get_context_instance(CONTEXT_USER, $user->id);
		}

		$link_values = '?userid='.$user->id;

		

		$navigation = "<a href=\"../../blocks/ilp/view.php?id=$user->id\">$strilp</a>";

		

		print_header("$strconcerns: ".fullname($user)."", "", "$navigation -> ".$strconcerns." -> ".fullname($user)."", 

                  "", "", true, "", "");

			

		$baseurl = $CFG->wwwroot.'/mod/ilpconcern/view.php?userid='.$user->id;

		$footer = '';

	}

	

	//Allow users to see their own profile, but prevent others

	

	if (has_capability('moodle/legacy:guest', $context, NULL, false)) {

        error("You are logged in as Guest.");

       } 

if ($action == 'updatestatus') {

//Sets message details for Reports
$messagefrom = get_record('user', 'id', $USER->id);
$messageto = get_record('user', 'id', $userid);
//////////////////////////////////////////////////////////
$messagetogeoff = get_record('user', 'id', 1);
$messagetowyn = get_record('user', 'id', 3);
/////////////////////////////////////////////////////////
$plpurl = $CFG->wwwroot.'/blocks/ilp/view.php'.$link_values;

	if($report = get_record('ilpconcern_status', 'userid', $userid)){	
		$report->status = $studentstatus;      
		$report->timemodified = time();
		$report->modifiedbyuser = $USER->id;		
		update_record('ilpconcern_status', $report);
	}else{
		$report = new Object; 
		$report->userid = $userid;
		$report->created  = time();
		$report->modified = time();
		$report->modifiedbyuser = $USER->id;
		$report->status = $studentstatus;			
		insert_record('ilpconcern_status', $report, true);
	}
	
	if($CFG->ilpconcern_send_concern_message == 1){
		
		switch($studentstatus) {
			case "0":
				$thisconcernstatus = get_string('green', 'ilpconcern');	
				break;
			case "1":
				$thisconcernstatus = get_string('amber', 'ilpconcern');
				break;
			case "2":
				$thisconcernstatus = get_string('red', 'ilpconcern');
					break;
			case "3":
				$thisconcernstatus = get_string('contract', 'ilpconcern');
					break;
			case "4":
		   	$thisconcernstatus = get_string('withdrawn', 'ilpconcern');
					break;

			}
				
			$updatedstatus = get_string('statusupdate', 'ilpconcern', $thisconcernstatus);
		
			$message = '<p>'.$updatedstatus.'<br /><a href="'.$plpurl.'">'.$concernview.'</a></p>';				
			message_post_message($messagefrom, $messageto, $message, FORMAT_HTML, 'direct');
			/////////////////////////////////////////////////////////////////////////////////////////////
			//message_post_message($messagefrom, $messagetogeoff, $message, FORMAT_HTML, 'direct');
			//message_post_message($messagefrom, $messagetowyn, $message, FORMAT_HTML, 'direct');
		}  		        

}

		//Determine report type 
					
		switch($status) {
			case "0":
				$thisreporttype = get_string('report1', 'ilpconcern');
				break;
			case "1":
				$thisreporttype = get_string('report2', 'ilpconcern');
				break;
			case "2":
				$thisreporttype = get_string('report3', 'ilpconcern');
				break;
			case "3":
				$thisreporttype = get_string('report4', 'ilpconcern');
				break;
			case "4":
				$thisreporttype = get_string('report5', 'ilpconcern');
				break;
			case "5":
				$thisreporttype = get_string('report6', 'ilpconcern');
				break;
		}
		
$mform = new ilpconcern_updateconcern_form('', array('userid' => $user->id, 'id' => $id, 'courseid' => $courseid, 'concernspost' => $concernspost, 'status' => $status, 'reporttype' => $thisreporttype, 'template' => $template));

if ($mform->is_cancelled()){
redirect("concerns_view.php?courseid=$courseid&userid=$userid&status=$status");
}
if($fromform = $mform->get_data()){        
	$mform->process_data($fromform);
}
if($action == 'delete'){ //Check to see if we are deleting a comment
	delete_records('ilpconcern_posts', 'id', $concernspost);
}
if($action == 'updateconcern'){
//	print_heading(get_string('add','ilpconcern'));
		
	if($CFG->ilpconcern_use_template == 1){
		$select = "module = 'ilpconcern' AND status = $status";
		$no_templates = count_records_select('ilp_module_template',$select);
		
		if($no_templates > 1){	
		 	$templates = get_records_select('ilp_module_template',$select,'name');
			
			$options = array();
    		foreach ($templates as $templateoption) {
				$options[$templateoption->id] = $templateoption->name;
			}
//			echo '<div class="ilpcenter">';		
//			popup_form ($CFG->wwwroot.'/mod/ilpconcern/concerns_view.php?'.(($courseid > 1)?'courseid='.$courseid.'&amp;' : '').'userid='.$userid.'&amp;action=updateconcern&amp;status='.$status.'&amp;template=', $options, "choosetemplate", $template, get_string('select').'...', "", "", false, 'self', get_string('template','ilpconcern'));
//			echo '</div>';
                      popup_form ($CFG->wwwroot.'/mod/ilpconcern/concerns_view.php?'.(($courseid > 1)?'courseid='.$courseid.'&amp;' : '').'userid='.$userid.'&amp;action=updateconcern&amp;status='.$status.'&amp;template=', $options, "choosetemplate", $template, get_string('select').'...', "", "", false, 'self', get_string('templatechoose','ilpconcern'));
		}
	}
	
	$mform->display();
}else{ 
	if($CFG->ilpconcern_status_per_student == 1){

	if($studentstatus = get_record('ilpconcern_status', 'userid', $user->id)){

		switch ($studentstatus->status) {
			case "0":
			    $thisstudentstatus = get_string('green', 'ilpconcern');
			    break;
			case "1":
			    $thisstudentstatus = get_string('amber', 'ilpconcern');
			    break;
			case "2":
			    $thisstudentstatus = get_string('red', 'ilpconcern');
			    break;
			case "3":
			    $thisstudentstatus = get_string('contract', 'ilpconcern');
			    break;
			case "4":
			    $thisstudentstatus = get_string('withdrawn', 'ilpconcern');
			    break;
		}
	}else{
		$studentstatus->status = 0;
		$thisstudentstatus = get_string('green', 'ilpconcern');
	}

	if(has_capability('mod/ilpconcern:updatestudentstatus', $context)){
              require_capability('mod/ilpconcern:view', $context);
              print_heading(get_string('reportfor', 'ilpconcern',get_string('report'.($status + 1).'plural', 'ilpconcern')).fullname($user));

		//print_heading(get_string('studentstatus', 'ilpconcern').': '.$thisstudentstatus, '', '2', $class='main status-'.$studentstatus->status.'');
      //////////////////////////////////////////
		echo '<div class="ilpcenter noprint">';
      echo '<h2><a href="'.$CFG->wwwroot.'/mod/ilpconcern/status_history.php'.$link_values.'"><span class="status-'.$studentstatus->status.'">';
      echo get_string('studentstatus', 'ilpconcern').': '.$thisstudentstatus;
      echo '</span></a></h2>';
      ///////////////////////////////////////////		
		update_student_status_menu($user->id,$courseid);
		echo '</div>';
	}else{
      print_heading(get_string('myreport', 'ilpconcern',get_string('report'.($status + 1).'plural', 'ilpconcern')));
//  	print_heading(get_string('mystudentstatus', 'ilpconcern').': '.$thisstudentstatus, '', '2', $class='main status-'.$studentstatus->status.'');
///////////////////////////
               echo '<div class="ilpcenter">';
               echo '<h2><a href="'.$CFG->wwwroot.'/mod/ilpconcern/status_history.php'.$link_values.'"><span class="status-'.$studentstatus->status.'">';
               echo get_string('mystudentstatus', 'ilpconcern').': '.$thisstudentstatus;
               echo '</span></a></h2>';
               echo '</div>';
	}
//}else{
//	if($USER->id != $user->id){
//		require_capability('mod/ilpconcern:view', $context);
//		print_heading(get_string('concernsreports', 'ilpconcern', fullname($user)));
//	}else{
//		print_heading(get_string('myconcerns', 'ilpconcern'));
//	}
} 

	$tabs = array();
   	$tabrows = array();
   	
      if($CFG->ilpconcern_report5 == 1 && (has_capability('mod/ilpconcern:viewreport5', $context))){
    	$tabrows[] = new tabobject('4', "$link_values&amp;status=4", get_string('report5', 'ilpconcern'));
		}
		if($CFG->ilpconcern_report2 == 1 && (has_capability('mod/ilpconcern:viewreport2', $context))){
    	$tabrows[] = new tabobject('1', "$link_values&amp;status=1", get_string('report2', 'ilpconcern'));
		}
		if($CFG->ilpconcern_report3 == 1 && (has_capability('mod/ilpconcern:viewreport3', $context))){
    	$tabrows[] = new tabobject('2', "$link_values&amp;status=2", get_string('report3', 'ilpconcern'));
		}
		if($CFG->ilpconcern_report4 == 1 && (has_capability('mod/ilpconcern:viewreport4', $context))){
    	$tabrows[] = new tabobject('3', "$link_values&amp;status=3", get_string('report4', 'ilpconcern'));
		}
		if($CFG->ilpconcern_report6 == 1 && (has_capability('mod/ilpconcern:viewreport6', $context))){
    	$tabrows[] = new tabobject('5', "$link_values&amp;status=5", get_string('report6', 'ilpconcern'));
		}
		if($CFG->ilpconcern_report1 == 1 && (has_capability('mod/ilpconcern:viewreport1', $context))){
		$tabrows[] = new tabobject('0', "$link_values&amp;status=0", get_string('report1', 'ilpconcern'));
		}

		

		$tabs[] = $tabrows;


      echo '<div class="noprint">';
      print_tabs($tabs, $status);
      echo '</div>';
// 	$i = $status + 1;
//		display_ilpconcern($user->id,$courseid,$i,TRUE,FALSE,FALSE,$sortorder='DESC',0);
              $i = $status + 1;		
		echo '<div class="addbox noprint">';
				
//			if(has_capability('mod/ilpconcern:viewreport5', $context)){ 
//			if($CFG->ilpconcern_report5 == 1 && (has_capability('mod/ilpconcern:addreport5', $context) || ($USER->id == $user->id && has_capability('mod/ilpconcern:addownreport5', $context)))) {
//				echo '<a href="'.$link_values.'&amp;action=updateconcern&amp;status=4">'.get_string('addconcern', 'ilpconcern', get_string('report5', 'ilpconcern')).'</a>';
//			}
//			}
//			if(has_capability('mod/ilpconcern:viewreport2', $context)){ 
//			if($CFG->ilpconcern_report2 == 1 && (has_capability('mod/ilpconcern:addreport2', $context) || ($USER->id == $user->id && has_capability('mod/ilpconcern:addownreport2', $context)))) {
//				echo '<a href="'.$link_values.'&amp;action=updateconcern&amp;status=1">'.get_string('addconcern', 'ilpconcern', get_string('report2', 'ilpconcern')).'</a>';
//			}
//			}
//			if(has_capability('mod/ilpconcern:viewreport3', $context)){ 
//			if($CFG->ilpconcern_report3 == 1 && (has_capability('mod/ilpconcern:addreport3', $context) || ($USER->id == $user->id && has_capability('mod/ilpconcern:addownreport3', $context)))) {
//				echo '<a href="'.$link_values.'&amp;action=updateconcern&amp;status=2">'.get_string('addconcern', 'ilpconcern', get_string('report3', 'ilpconcern')).'</a>';
//			}
//			}
//			if(has_capability('mod/ilpconcern:viewreport4', $context)){ 
//			if($CFG->ilpconcern_report4 == 1 && (has_capability('mod/ilpconcern:addreport4', $context) || ($USER->id == $user->id && has_capability('mod/ilpconcern:addownreport4', $context)))) {
//				echo '<a href="'.$link_values.'&amp;action=updateconcern&amp;status=3">'.get_string('addconcern', 'ilpconcern', get_string('report4', 'ilpconcern')).'</a>';
//			}
//			}
//
//			if(has_capability('mod/ilpconcern:viewreport6', $context)){ 
//			if($CFG->ilpconcern_report6 == 1 && (has_capability('mod/ilpconcern:addreport6', $context) || ($USER->id == $user->id && has_capability('mod/ilpconcern:addownreport6', $context)))) {
//				echo '<a href="'.$link_values.'&amp;action=updateconcern&amp;status=5">'.get_string('addconcern', 'ilpconcern', get_string('report6', 'ilpconcern')).'</a>';
//			}
//			}
//
//			if(has_capability('mod/ilpconcern:viewreport1', $context)){ 
//			if($CFG->ilpconcern_report1 == 1 && (has_capability('mod/ilpconcern:addreport1', $context) || ($USER->id == $user->id && has_capability('mod/ilpconcern:addownreport1', $context)))) {
//				echo '<a href="'.$link_values.'&amp;action=updateconcern&amp;status=0">'.get_string('addconcern', 'ilpconcern', get_string('report1', 'ilpconcern')).'</a>';
//			}
//			}
              if(eval('return $CFG->ilpconcern_report'.$i.';') == 1 && (has_capability('mod/ilpconcern:addreport'.$i, $context) || ($USER->id == $user->id && has_capability('mod/ilpconcern:addownreport'.$i, $context)))) {
                      echo '<a class="button" href="'.$link_values.'&amp;action=updateconcern&amp;status='.($i - 1).'" onclick="this.blur();"><span>'.get_string('addconcern', 'ilpconcern', get_string('report'.$i, 'ilpconcern')).'</span></a>';
              }
		echo '</div>';
              echo '<div class="clearer">&nbsp;</div>';
               
              display_ilpconcern($user->id,$courseid,$i,TRUE,FALSE,FALSE,$sortorder='DESC',0);
  }
		
/// Finish the page

    print_footer($footer);

?>