<?PHP // $Id: block_ilp_lib.php,v 1.13 2009/09/06 14:49:06 ulcc Exp $

//  given userid spews out users ilp report
//  this bit just queries db then hands massive assoc array to the template.

function block_ilp_report($id,$courseid) {

    global $CFG, $USER;

	$module = 'project/ilp';
	$config = get_config($module);

    $user = get_record('user','id',$id);

    if (!$user) {
      error("bad user $id");
    }

	if($CFG->ilpconcern_status_per_student == 1){
		if($studentstatus = get_record('ilpconcern_status', 'userid', $id)){
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
			$studentstatusnum = $studentstatus->status;
		}else{
			$studentstatusnum = 0;
			$thisstudentstatus = get_string('green', 'ilpconcern');
		}
	}

    if (file_exists('templates/custom/template.php')) {
      include('templates/custom/template.php');
    }elseif (file_exists('template.php')) {
      include('template.php');
    }else{
      error("missing template \"$template\"") ;
    }

}


function get_my_ilp_courses($userid) {
    global $CFG, $USER;

	$module = 'project/ilp';
	$config = get_config($module);

	$courses = get_my_courses($userid);

	if($config->ilp_limit_categories == '1') {
		$ilp_categories = $config->ilp_categories;
		$allowed_categories = explode(',', $ilp_categories);

		foreach ($courses as $course){
			if(in_array($course->category,$allowed_categories)){
				$ilpcourses[] = $course;
			}
		}
	}else{
		$ilpcourses = $courses;
	}
	return $ilpcourses;
}

function print_row($left, $right) {
    echo "$left $right<br />";
}



function display_custom_profile_fields($userid) {
    global $CFG, $USER;

    if ($categories = get_records_select('user_info_category', '', 'sortorder ASC')) {
        foreach ($categories as $category) {
            if ($fields = get_records_select('user_info_field', "categoryid=$category->id", 'sortorder ASC')) {
                foreach ($fields as $field) {
                    require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
                    $newfield = 'profile_field_'.$field->datatype;
                    $formfield = new $newfield($field->id, $userid);
                    if (!$formfield->is_empty()) {
                        print_row(s($formfield->field->name.':'), $formfield->display_data());
                    }
                }
            }
        }
    }
}

/**
     * Displays the Student Info summary to the ILP
     *
     * @param id   			userid fed from ILP page (required)
     * @param courseid   	courseid fed from ILP page (required)
     * @param full   		display a full report or just a title link - for layout and navigation
     * @param title  		display default title - turn off to add customised title to template
	 * @param icon   		display an icon with the title
	 * @param teachertext   display the teacher text section
	 * @param studenttext   display the student text section
	 * @param sharedtext   	display the shared text section
*/

function display_ilp_student_info ($id,$courseid,$full=TRUE,$title=TRUE,$icon=TRUE,$teachertext=TRUE,$studenttext=TRUE,$sharedtext=TRUE) {

	global $CFG,$USER;
	require_once("../ilp_student_info/block_ilp_student_info_lib.php");
	include ('access_context.php');

	$module = 'project/ilp';
    $config = get_config($module);

	$user = get_record('user','id',$id);

	if($title == TRUE) {
		echo '<h2>';

		if ($icon == TRUE) { 
			if (file_exists('templates/custom/pix/student_info.gif')) {
				echo '<img src="'.$CFG->wwwroot.'/blocks/ilp/templates/custom/pix/student_info.gif" alt="" />';
			}else{
      			echo '<img src="'.$CFG->wwwroot.'/blocks/ilp/pix/student_info.gif" alt="" />'; 
			}
		}

		echo '<a href="'.$CFG->wwwroot.'/blocks/ilp_student_info/view.php?id='.$id.(($courseid)?'&courseid='.$courseid:'').'&amp;view=info">'.(($access_isuser)?get_string('viewmyilp_student_info','block_ilp_student_info'):get_string('ilp_student_info', 'block_ilp_student_info')).'</a></h2>';
	}

	if($full == TRUE) {

		if($config->block_ilp_student_info_allow_per_student_teacher_text == 1 && $teachertext == TRUE) {

			$text = block_ilp_student_info_get_text($user->id,0,0,'student','teacher') ;
			echo '<div class="block_ilp_student_info_text">'.stripslashes($text->text).'</div>';

			if($access_isteacher or $access_istutor or $access_isgod) {
				echo '<span class="noprint">'.block_ilp_student_info_edit_button($user->id,0,(($courseid)? $courseid : 0),'student','teacher',$text->id).'</span>' ;
			}
		}

		if($config->block_ilp_student_info_allow_per_student_student_text == 1 && $studenttext == TRUE) {

			$text = block_ilp_student_info_get_text($user->id,0,0,'student','student') ;
			echo '<div class="block_ilp_student_info_text">'.stripslashes($text->text).'</div>';

			if($access_isuser or $access_isgod) {
			echo '<span class="noprint">'.block_ilp_student_info_edit_button($user->id,0,(($courseid)? $courseid : 0),'student','student',$text->id).'</span>';
			}
		}

		if($config->block_ilp_student_info_allow_per_student_shared_text == 1 && $sharedtext == TRUE) {
			$text = block_ilp_student_info_get_text($user->id,0,0,'student','shared') ;
			echo '<div class="block_ilp_student_info_text">'.stripslashes($text->text).'</div>';

			if($access_isuser or $access_isteacher or $access_istutor or $access_isgod) {
				echo '<span class="noprint">'.block_ilp_student_info_edit_button($user->id,0,(($courseid)? $courseid : 0),'student','shared',$text->id).'</span>';
			}
		}
	}
}
//////////////////

/**
     * Counts total number of targets for a user
     * @param userid   			userid fed from ILP page (required)
*/

function ilptarget_get_total($userid) {
	global $CFG;
	$targettotal = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilptarget_posts WHERE setforuserid = '.$userid.' AND status != "3"' );
	return $targettotal;
}

/**
     * Counts number of achieved targets for a user
     * @param userid   			userid fed from ILP page (required)
*/

function ilptarget_get_achieved ($userid) {
	global $CFG;
	$targetcomplete = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilptarget_posts WHERE setforuserid = '.$userid.' AND status = "1"');
	return $targetcomplete;
}

/**
     * Counts number of achieved targets for a user
     * @param userid   			userid fed from ILP page (required)
*/

function ilptarget_percentage_complete ($userid) {
	return round((ilptarget_get_achieved($userid) / ilptarget_get_total($userid))*100,0);
}

/**
     * Counts number of achieved targets for a user
     * @param userid   			userid fed from ILP page (required)
*/

function ilptarget_display_complete ($userid) {
	return ilptarget_get_achieved($userid).'/'.ilptarget_get_total($userid).' '.get_string('complete', 'ilptarget');
}

/**
     * Get last post for report type
     * @param userid   			userid fed from ILP page (required)
*/

function ilptarget_get_last_report($userid) {
	global $CFG;
	if($lastreport = get_record_sql('SELECT * FROM '.$CFG->prefix.'ilptarget_posts WHERE setforuserid = '.$userid.' AND status = 0 ORDER BY timemodified DESC',FALSE)) {
		return $lastreport->timemodified;
	}
}

/**
     * Checks if there is an unread target by comparing with logs
     * @param userid   			userid fed from ILP page (required)
*/

function ilptarget_check_new ($userid1,$userid2) {
	/*global $CFG;
	if ($lastview = get_record_sql('SELECT * FROM '.$CFG->prefix.'log WHERE userid = '.$userid1.' AND module = \'target\' AND info = '.$userid2.' ORDER BY TIME DESC',FALSE)) {
		$lastviewtime = $lastview->time;
	}else{
		$lastviewtime = 0;
	}
	if(ilptarget_get_total($userid2) > 0 ) {
		$lastreport = ilptarget_get_last_report($userid2);
	}else{
		$lastreport = 0;
	}
	if($lastreport > $lastviewtime) {
		return 1;
	}else{*/
		return 0;
	//}
}
//////////////////
/**
     * Displays the ilptarget summary to the ILP
     *
     * @param id   			   userid fed from ILP page (required)
     * @param courseid   	   courseid fed from ILP page (required)
     * @param full   		   display a full report or just a title link - for layout and navigation
     * @param title  		   display default title - turn off to add customised title to template
	  * @param icon   		   display an icon with the deafult title
	  * @param sortorder       DESC or ASC - to sort on deadline dates
	  * @param limit		      limit the number of targets shown on the page
	  * @param status	         -1 means all otherwise a particular status can be entered
	  * @param tutorsetonly 	display tutor set targets only
	  * @param studentsetonly  display student set targets only
*/

function display_ilptarget ($id,$courseid,$full=TRUE,$title=TRUE,$icon=TRUE,$sortorder='ASC',$limit=0,$status=-1,$tutorsetonly=FALSE,$studentsetonly=FALSE) {

	global $CFG,$USER;
	require_once("$CFG->dirroot/blocks/ilp_student_info/block_ilp_student_info_lib.php");
	require_once("$CFG->dirroot/mod/ilptarget/lib.php");
	include ('access_context.php');

	$module = 'project/ilp';
    $config = get_config($module);

	$user = get_record('user','id',$id);

	$select = "SELECT {$CFG->prefix}ilptarget_posts.*, up.username ";
	$from = "FROM {$CFG->prefix}ilptarget_posts, {$CFG->prefix}user up ";
	$where = "WHERE up.id = setbyuserid AND setforuserid = $id ";

	if($status != -1) {
		$where .= "AND status = $status ";
	}elseif($config->ilp_show_achieved_targets == 1){
    	$where .= "AND status != 3 ";
	}else{
    	$where .= "AND status = 0 ";
	}

	if($CFG->ilptarget_course_specific == 1 && $courseid != 0){
		$where .= "AND course = $courseid ";
	}

	if($tutorsetonly == TRUE && $studentsetonly == FALSE) {
		$where .= "AND setforuserid != setbyuserid ";
	}

	if($studentsetonly == TRUE && $tutorsetonly == FALSE) {
		$where .= "AND setforuserid = setbyuserid ";
	}

	$order = "ORDER BY deadline $sortorder ";

    $target_posts = get_records_sql($select.$from.$where.$order,0,$limit);

	if($title == TRUE) {
		echo '<h2';
		if($full == FALSE) {
			echo ' style="display:inline"';
		}
		echo '>';
		
		if ($icon == TRUE) { 
			if (file_exists('templates/custom/pix/target.gif')) {
				echo '<img src="'.$CFG->wwwroot.'/blocks/ilp/templates/custom/pix/target.gif" alt="" />';
			}else{
      			echo '<img src="'.$CFG->wwwroot.'/blocks/ilp/pix/target.gif" alt="" />'; 
			}
		}
/////////////////////////////////////
//		echo '<a href="'.$CFG->wwwroot.'/mod/ilptarget/target_view.php?'.(($courseid > 1)?'courseid='.$courseid.'&amp;' : '').'userid='.$id.'">'.(($access_isuser)? get_string("mytargets", "ilptarget"):get_string("modulenameplural", "ilptarget")).'</a></h2>';
echo '<a href="'.$CFG->wwwroot.'/mod/ilptarget/target_view.php?'.(($courseid > 1)?'courseid='.$courseid.'&amp;' : '').'userid='.$id.'">'.(($access_isuser)? get_string("mytargets", "ilptarget"):get_string("modulenameplural", "ilptarget")).'</a>';
if(has_capability('mod/ilptarget:addtarget', $context) || ($USER->id == $user->id && has_capability('mod/ilptarget:addowntarget', $context))) {
echo '<div class="ilpadd">';
echo '<a class="button" href="'.$CFG->wwwroot.'/mod/ilptarget/target_view.php?'.(($courseid != SITEID)?'courseid='.$courseid.'&amp;' : '').'userid='.$id.'&amp;action=updatetarget" onclick="this.blur();"><span>'.get_string('add', 'ilptarget').'</span></a>';
echo '</div>';
echo '<div class="clearer">&nbsp;</div>';
}
echo '</h2>';





	}

	if($full == FALSE) {
		$targettotal = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilptarget_posts WHERE setforuserid = '.$user->id.' AND status != "3"' );

		$targetcomplete = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilptarget_posts WHERE setforuserid = '.$user->id.' AND status = "1"');

		echo '<p style="display:inline; margin-left: 5px">'.$targetcomplete.'/'.$targettotal.' '.get_string('complete', 'ilptarget').'</p>';
	}

	if($full == TRUE) {
		echo '<div class="block_ilp_ilptarget">';

		if($target_posts) {
			foreach($target_posts as $post) {
				$posttutor = get_record('user','id',$post->setbyuserid);

				echo '<div class="ilp_post yui-t4">';
				   echo '<div class="bd" role="main">';
					echo '<div class="yui-main">';
					echo '<div class="yui-b"><div class="yui-gd">';
					echo '<div class="yui-u first">';
					echo get_string('name', 'ilptarget');
					echo '</div>';
					echo '<div class="yui-u">';
					echo $post->name;
					echo '</div>';
				echo '</div>';
				echo '<div class="yui-gd">';
					echo '<div class="yui-u first">';
					echo ''.get_string('targetagreed', 'ilptarget').'';
						echo '</div>';
					echo '<div class="yui-u">';
					echo ''.$post->targetset.'';
						echo '</div>';
				echo '</div>';
				echo '</div>';
					echo '</div>';
					echo '<div class="yui-b">';
					echo '<ul>';
					echo '<li>'.get_string('setby', 'ilptarget').': '.fullname($posttutor);
					if($post->courserelated == 1){
						$targetcourse = get_record('course','id',$post->targetcourse);
						echo '<li>'.get_string('course').': '.$targetcourse->shortname.'</li>';
					}
//			   	echo '<li>'.get_string('set', 'ilptarget').': '.userdate($post->timecreated, get_string('strftimedateshort'));
				   echo '<li>'.get_string('set', 'ilptarget').': '.userdate($post->timecreated, get_string('strftimedate'));
//					echo '<li>'.get_string('deadline', 'ilptarget').': '.userdate($post->deadline, get_string('strftimedateshort'));
               echo '<li>'.get_string('deadline', 'ilptarget').': '.userdate($post->deadline, get_string('strftimedate'));
					echo '</ul>';

					$commentcount = count_records('ilptarget_comments', 'targetpost', $post->id);

					echo '<div class="commands"><a href="'.$CFG->wwwroot.'/mod/ilptarget/target_comments.php?'.(($courseid > 1)?'courseid='.$courseid.'&amp;' : '').'userid='.$id.'&amp;targetpost='.$post->id.'">'.$commentcount.' '.get_string("comments", "ilptarget").'</a> ';

					if($post->status == 0 || has_capability('moodle/site:doanything', $context)){
						echo ilptarget_update_status_menu($post->id,$context);
					}
					echo '</div>';

					if($post->status == 1){
						echo '<img class="achieved" src="'.$CFG->pixpath.'/mod/ilptarget/achieved.gif" alt="" />';
					}
					echo '</div>';
					echo '</div>';
				echo '</div>';
			}
		}
		echo '</div>';
	}
}

/**
    * Displays the ilpconcern (report) summary to the ILP
    *
    * @param id   			userid fed from ILP page (required)
    * @param courseid   	courseid fed from ILP page (required)
	 * @param report	   	report number from ILP page (required)
    * @param full      		display a full report or just a title link - for layout and navigation
    * @param title  	   	display default title - turn off to add customised title to template
	 * @param icon   	   	display an icon with the deafult title
	 * @param sortorder     DESC or ASC - to sort on deadline dates
	 * @param limit		    limit the number of targets shown on the page
	 * @param status	    -1 means all otherwise a particular status can be entered

    * report status 0 == Report 1 == Programme Review------------NOT USED-----BUT WILL BE USED -- COMMENTS
    * report status 1 == Report 2 == Concern Note
    * report status 2 == Report 3 == Recognition Note
    * report status 3 == Report 4 == Programme (Subject) Report -- Edited by Lecturer (and Tutor)
    * report status 4 == Report 5 == Attendance Concern Note
    * report status 5 == Report 6 == Request for Absence---------NOT Used

*/

function display_ilpconcern ($id,$courseid,$report,$full=TRUE,$title=TRUE,$icon=TRUE,$sortorder='DESC',$limit=0) {

	global $CFG,$USER;
	require_once("$CFG->dirroot/blocks/ilp_student_info/block_ilp_student_info_lib.php");
	require_once("$CFG->dirroot/mod/ilpconcern/lib.php");
	include ('access_context.php');

	$module = 'project/ilp';
       $config = get_config($module);

	$user = get_record('user','id',$id);

	$status = $report - 1;

	$select = "SELECT {$CFG->prefix}ilpconcern_posts.*, up.username ";
	$from = "FROM {$CFG->prefix}ilpconcern_posts, {$CFG->prefix}user up ";
	$where = "WHERE up.id = setbyuserid AND status = $status AND setforuserid = $id ";

	if($CFG->ilpconcern_course_specific == 1 && $courseid != 0){
		$where .= 'AND course = '.$courseid.' ';
	}

    $order = "ORDER BY deadline $sortorder ";

    $concerns_posts = get_records_sql($select.$from.$where.$order,0,$limit);

	if($title == TRUE) {
		echo '<h2>';
		
		if ($icon == TRUE) { 
			if (file_exists('templates/custom/pix/report'.$report.'.gif')) {
				echo '<img src="'.$CFG->wwwroot.'/blocks/ilp/templates/custom/pix/report'.$report.'.gif" alt="" />';
			}else{
      			echo '<img src="'.$CFG->wwwroot.'/blocks/ilp/pix/report'.$report.'.gif" alt="" />'; 
			}
		}
//////////////////////
//		echo '<a href="'.$CFG->wwwroot.'/mod/ilpconcern/concerns_view.php?'.(($courseid > 1)?'courseid='.$courseid.'&amp;' : '').'userid='.$id.'&amp;status='.$status.'">'.(($access_isuser)? get_string('report'.$report.'plural','ilpconcern'):get_string('report'.$report.'plural','ilpconcern')).'</a></h2>';
echo '<a href="'.$CFG->wwwroot.'/mod/ilpconcern/concerns_view.php?'.(($courseid > 1)?'courseid='.$courseid.'&amp;' : '').'userid='.$id.'&amp;status='.$status.'">'.(($access_isuser)? get_string('report'.$report.'plural','ilpconcern'):get_string('report'.$report.'plural','ilpconcern')).'</a>';
echo '<div class="ilpadd">';
if(eval('return $CFG->ilpconcern_report'.$report.';') == 1 && (has_capability('mod/ilpconcern:addreport'.$report, $context) || ($USER->id == $user->id && has_capability('mod/ilpconcern:addownreport'.$report, $context)))) {
echo '<a class="button" href="'.$CFG->wwwroot.'/mod/ilpconcern/concerns_view.php?'.(($courseid > 1)?'courseid='.$courseid.'&amp;' : '').'userid='.$id.'&amp;status='.$status.'&amp;action=updateconcern&amp;status='.($status).'" onclick="this.blur();"><span>'.get_string('addconcern', 'ilpconcern', get_string('report'.$report, 'ilpconcern')).'</span></a>';
}
echo '</div>';
echo '<div class="clearer">&nbsp;</div>';
echo '</h2>';
/////////////////////////////
	}

	if($full == TRUE) {
	//////////////////////////////////////////////////////
	$ilpcourses = get_my_ilp_courses($user->id);
	foreach ($ilpcourses as $course) {
	    $context = get_context_instance(CONTEXT_COURSE, $course->id);
	    if (empty($tutors)) {
           $tutors = get_users_by_capability($context, 'block/ilp:tutoreditreview', 'u.id,u.firstname,u.lastname', 'u.lastname ASC', '', '', '', '', false);
           }
           if (empty($supertutors)) {
           $supertutors = get_users_by_capability($context, 'block/ilp:supertutoreditreview', 'u.id,u.firstname,u.lastname', 'u.lastname ASC', '', '', '', '', false);
           }
	}
	//////////////////////////////////////////////////////
	// 1 Concern || 2 Recognition ||  4 Attendance Concern
	
	   if (($status == 1)||($status == 2)||($status == 4)) {
		echo '<div class="block_ilp_ilpconcern">';

		if($concerns_posts) {
			foreach($concerns_posts as $post) {
				$posttutor = get_record('user','id',$post->setbyuserid);
            	echo '<div class="ilp_post yui-t4">';
				   echo '<div class="bd" role="main">';
					echo '<div class="yui-main">';
					echo '<div class="yui-b">';
					if(isset($post->name)){
						echo '<div class="yui-gd">';
						echo '<div class="yui-u first">';
						echo get_string('name', 'ilpconcern');
						echo '</div>';
						echo '<div class="yui-u">';
						echo $post->name;
						echo '</div>';
					echo '</div>';
					}
				echo '<div class="yui-gd page-break">';
				//Left Panel
				   echo '<div class="yui-u first noprint">';
					echo ''.get_string('report'.$report,'ilpconcern').'';
					echo '</div>';
				//Middle Main Panel
					echo '<div class="yui-u">';
					echo ''.$post->concernset.'';
					echo '</div>';
				echo '</div>';
				echo '</div>';
					echo '</div>';
				//Right Panel
				
					echo '<div class="yui-b noprint">';
					echo '<ul>';
					echo '<li>'.get_string('setby', 'ilpconcern').': '.fullname($posttutor);
					if($post->courserelated == 1){
						$targetcourse = get_record('course','id',$post->targetcourse);
						echo '<li>'.get_string('course').': '.$targetcourse->shortname.'</li>';
					}
// 				echo '<li>'.get_string('deadline', 'ilpconcern').': '.userdate($post->deadline, get_string('strftimedateshort'));
   				echo '<li>'.get_string('deadline', 'ilpconcern').': '.userdate($post->deadline, get_string('strftimedate'));
					echo '</ul>';

					$commentcount = count_records('ilpconcern_comments', 'concernspost', $post->id);

					echo '<div class="commands"><a href="'.$CFG->wwwroot.'/mod/ilpconcern/concerns_comments.php?'.(($courseid > 1)?'courseid='.$courseid.'&amp;' : '').'userid='.$id.'&amp;concernspost='.$post->id.'">'.$commentcount.' '.get_string("comments", "ilpconcern").'</a>';
////////////////////////////////////////////////////////////////////////////////////////////
					echo ilpconcern_update_menu($post->id,$context);

					echo '</div>';

					echo '</div>';
					echo '</div>';
				echo '</div>';
			}
		}
		echo '</div>';
/////////////////////////////////////////////////////////////////////////////////////////////
//
// Progress (Subject Report)
	} else {
///////////////////////////////////////////////////////////////////////////////////////////////	
	echo '<div class="block_ilp_ilpconcern">';

		if($concerns_posts) {
			foreach($concerns_posts as $post) {
				$posttutor = get_record('user','id',$post->setbyuserid);
            	echo '<div class="ilp_post yui-t4">';
            	//echo '<div>';
				   echo '<div class="bd" role="main">';
				   //echo '<div>';
				   //Dodgy divs
					//echo '<div class="yui-main">';
					echo '<div>';
					//echo '<div class="yui-b">';
					echo '<div>';
					if(isset($post->name)){
						echo '<div class="yui-gd">';
						echo '<div class="yui-u first">';
						echo get_string('name', 'ilpconcern');
						echo '</div>';
						echo '<div class="yui-u">';
						echo $post->name;
						echo '</div>';
					echo '</div>';
					}
				///////////////////////////////////////////////////////////////////
				// Check where page-break is?
				//////////////////////////////////////////
				/////////////////////////////////////////////////////
				echo '<div class="page-break" style="width:100%">';
				//echo '<div class="yui-gd page-break" style"width:100%">';
				//Left Panel
				   //echo '<div class="yui-u first noprint">';
					//echo ''.get_string('report'.$report,'ilpconcern').'';
					//echo '</div>';
				//Middle Main Panel
				   //echo '<div class="yui-u">';
					echo '<div style="width:100%; float:left;">';
					//////////////////////////////////////
					echo '<div style="float:left; width:auto">';
					
					echo '<h1>'.get_string('report'.$report,'ilpconcern').'</h1>';
					echo '<table border="0" cellpadding="5">';
					echo '<tr>';
					echo '<td>';
					echo '<b>Name:</b> <a href="'.$CFG->wwwroot.'/user/view.php?id='.$id.'&courseid=1">'.fullname($user).'</a><br />';
					echo '</td><td>';
                                   // Need to make this more sophisticated for students who change tutor
					//foreach($tutors as $tutor) {
					//echo '<b>Tutor:</b> <a href="'.$CFG->wwwroot.'/user/view.php?id='.$tutor->id.'&courseid=1">'.fullname($tutor).'</a><br />';
					//}

					echo '</td></tr><tr><td>';
                                        echo '<b>Lecturer:</b> <a href="'.$CFG->wwwroot.'/user/view.php?id='.$posttutor->id.'&courseid=1">'.fullname($posttutor).'</a><br />';

					echo '</td><td>';
			                if($post->courserelated == 1){
					$targetcourse = get_record('course','id',$post->targetcourse);
					echo '<b>Subject:</b> <a href="'.$CFG->wwwroot.'/course/view.php?id='.$targetcourse->id.'">'.$targetcourse->shortname.'</a><br />';
               	                        }
					echo '</td></tr>';

					//$commentcount = count_records('ilpconcern_comments', 'concernspost', $post->id);

					//echo '<div class="commands"><a href="'.$CFG->wwwroot.'/mod/ilpconcern/concerns_comments.php?'.(($courseid > 1)?'courseid='.$courseid.'&amp;' : '').'userid='.$id.'&amp;concernspost='.$post->id.'">'.$commentcount.' '.get_string("comments", "ilpconcern").'</a>';
					//echo ilpconcern_update_menu($post->id,$context);
					if(date("n", $post->deadline) > 9) {
					$endyear=date("Y", $post->deadline);
					$startyear=$endyear;
					$startmonth="September";
					$endmonth=date("F", $post->deadline);
					}elseif(date("n", $post->deadline) > 4) {
					$endyear=date("Y", $post->deadline);
					$lastyear=$endyear;
					$startmonth="January";
					$endmonth=date("F", $post->deadline);
					}else{
					$endyear=date("Y", $post->deadline);
					$startyear=$endyear-1;
					$startmonth="September";
					$endmonth=date("F", $post->deadline);
					}					
					echo '<tr><td colspan="2">';
					echo '<b>Report Period:</b> From '.$startmonth.' '.$startyear.' to '.$endmonth.' '.$endyear.'<br /><br />';
			      echo '</td></tr></table>';
			      echo '</div>';
			      
					///////////////////////////////////////
                                  	echo ''.$post->concernset.'';
				//echo '<p style="text-align:right">'.get_string('deadline', 'ilpconcern').': '.userdate($post->deadline, get_string('strftimedateshort')).' '.$endyear.'</p>';
				//echo '<br />';	
            //echo '<p style="text-align:right"><a href="'.$CFG->wwwroot.'/user/view.php?id='.$posttutor->id.'&courseid=1">'.fullname($posttutor).'</a>: '.userdate($post->deadline, get_string('strftimedateshort')).' '.$endyear.'</p>';
				echo '</div>';
				echo '</div>';
				echo '</div>';
					echo '</div>';
				//Right Panel
				
					//echo '<div class="yui-b noprint">';
					echo '<div style="float:right" class="noprint">';
					echo '<br />';
					//echo '<ul>';
					//echo '<li>'.get_string('setby', 'ilpconcern').': '.fullname($posttutor);
					//if($post->courserelated == 1){
					//$targetcourse = get_record('course','id',$post->targetcourse);
					//	echo '<li>'.get_string('course').': '.$targetcourse->shortname.'</li>';
					//}
					//echo '<li>'.get_string('deadline', 'ilpconcern').': '.userdate($post->deadline, get_string('strftimedateshort'));
					//echo '</ul>';

					$commentcount = count_records('ilpconcern_comments', 'concernspost', $post->id);
               echo '<p style="text-align:right"><a href="'.$CFG->wwwroot.'/user/view.php?id='.$posttutor->id.'&courseid=1">'.fullname($posttutor).'</a>: '.userdate($post->deadline, get_string('strftimedateshort')).' '.$endyear.'</p>';
					echo '<div class="commands noprint"><a href="'.$CFG->wwwroot.'/mod/ilpconcern/concerns_comments.php?'.(($courseid > 1)?'courseid='.$courseid.'&amp;' : '').'userid='.$id.'&amp;concernspost='.$post->id.'">'.$commentcount.' '.get_string("comments", "ilpconcern").'</a>';
///////////////////Check this function
               if(ilpconcern_report_update_menu($post->id,$context)!== "") {
					echo ilpconcern_report_update_menu($post->id,$context);
					}else{
                                   // echo '<br />';
                                   foreach($supertutors as $supertutor){
                                   if($supertutor->id == $USER->id) {
                                   echo ' | <a title="'.get_string('edit').'" href="'.$CFG->wwwroot.'/mod/ilpconcern/concerns_view.php?'.(($courseid > 1)?'courseid='.$courseid.'&amp;' : '').'userid='.$id.'&amp;concernspost='.$post->id.'&amp;action=updateconcern"><img src="'.$CFG->pixpath.'/t/edit.gif" alt="'.get_string('edit').'" /> '.get_string('edit').'</a> | <a title="'.get_string('delete').'" href="'.$CFG->wwwroot.'/mod/ilpconcern/concerns_view.php?'.(($courseid > 1)?'courseid='.$courseid.'&amp;' : '').'userid='.$id.'&amp;concernspost='.$post->id.'&amp;action=delete""><img src="'.$CFG->pixpath.'/t/delete.gif" alt="'.get_string('delete').'" /> '.get_string('delete').'</a> | ';
                                   //echo '<b>CQM:</b> '.fullname($supertutor).'<br />';
                                   }
                                   }
                                   }
////////////////////////////////////////////////

					echo '</div>';
					echo '</div>';
					echo '</div>';
				echo '</div>';
			}
		}
		echo '</div>';
		}	
	}
}

///////////////////////////// End of report Loop ////////////////////////////////////

/**
    * NOT CURRENTLY USED
    * Displays the Personal report summary to the ILP
    *
    * @param id   			userid fed from ILP page
    * @param courseid   	courseid fed from ILP page
    * @param full   	   	display a full report or just a title link - for layout and navigation
    * @param title  	   	display default title - turn off to add customised title to template
	 * @param icon     		display an icon with the title
	 * @param teachertext   display the teacher text section
	 * @param studenttext   display the student text section
	 * @param sharedtext   	display the shared text section
*/

function display_ilp_personal_report ($id,$courseid,$full=TRUE,$title=TRUE,$icon=TRUE,$teachertext=TRUE,$studenttext=TRUE,$sharedtext=TRUE) {

	global $CFG,$USER;
	require_once("../ilp_student_info/block_ilp_student_info_lib.php");
	include ('access_context.php');

	$module = 'project/ilp';
    $config = get_config($module);

	$user = get_record('user','id',$id);

	if($title == TRUE) {
		echo '<h2>';
		
		if ($icon == TRUE) { 
			if (file_exists('templates/custom/pix/personal_report.gif')) {
				echo '<img src="'.$CFG->wwwroot.'/blocks/ilp/templates/custom/pix/personal_report.gif" alt="" />';
			}else{
      			echo '<img src="'.$CFG->wwwroot.'/blocks/ilp/pix/personal_report.gif" alt="" />'; 
			}
		}

		echo '<a href="'.$CFG->wwwroot.'/blocks/ilp_student_info/view.php?id='.$id.(($courseid)?'&courseid='.$courseid:'').'&amp;view=personal">'.get_string('personal_report', 'block_ilp').'</a></h2>';
	}

	if($full == TRUE) {

    	$context = get_context_instance(CONTEXT_USER, $user->id);
    	$tutors = get_users_by_capability($context, 'moodle/user:viewuseractivitiesreport', 'u.*', 'u.lastname ASC', '', '', '', '', false);

    	if ($tutors) {

			foreach ($tutors as $tutor) {
				if (count_records('ilp_student_info_per_tutor','teacher_userid',$tutor->id, 'student_userid', $user->id) != 0){
					echo '<table style="text-align:left; margin:5px;" class="generalbox"><tbody><tr><th colspan="3">'.fullname($tutor).'<th></tr>';

					if($config->block_ilp_student_info_allow_per_tutor_teacher_text == 1 && $teachertext == TRUE) {
						$text = block_ilp_student_info_get_text($user->id,$tutor->id,$course->id,'tutor','teacher');

						echo '<tr><td>'.get_string('tutor_comment','block_ilp_student_info').':</td></tr><tr><td><div class="block_ilp_student_info_text">'.stripslashes($text->text).'</div></td>';

						if($tutor->id == $USER->id or $access_isgod) {
							echo '<td><span class="noprint">'.block_ilp_student_info_edit_button($user->id,$tutor->id,$course->id,'tutor','teacher',$text->id).'</span></td>';
						}else{
							echo '<td></td></tr>';
						}
					}

					if($config->block_ilp_student_info_allow_per_tutor_student_text == 1 && $studenttext == TRUE) {
						$text = block_ilp_student_info_get_text($user->id,$tutor->id,$course->id,'tutor','student');

						echo '<tr><td>'.get_string('student_response','block_ilp_student_info').':</td></tr><tr><td><div class="block_ilp_student_info_text">'.stripslashes($text->text).'</div></td>';

						if($access_isuser || $access_isgod) {
							echo '<td><span class="noprint">'.block_ilp_student_info_edit_button($user->id,$tutor->id,$course->id,'tutor','student',$text->id).'</span></td></tr>';
						}else{
							echo '<td></td></tr>';
						}
					}

					if($config->block_ilp_student_info_allow_per_tutor_shared_text == 1 && $sharedtext == TRUE) {
						$text = block_ilp_student_info_get_text($user->id,$tutor->id,$course->id,'tutor','shared') ;

						echo '<tr><td>'.get_string('shared_text','block_ilp_student_info').':</td></tr><tr><td><div class="block_ilp_student_info_text">'.stripslashes($text->text).'</div></td>';

						if($access_isuser or $tutor->id == $USER->id or $access_isgod) {
							echo '<td><span class="noprint">'.block_ilp_student_info_edit_button($user->id,$tutor->id,$course->id,'tutor','shared',$text->id).'</span></td></tr>';
						}else{
							echo '<td></td></tr>';
						}
					}
				}elseif($tutor->id == $USER->id){

					if($config->block_ilp_student_info_allow_per_tutor_teacher_text == 1 && $teachertext == TRUE) {
						$text = block_ilp_student_info_get_text($user->id,$tutor->id,$course->id,'tutor','teacher') ;
						echo '<tr><td>'.get_string('notextteacher','block_ilp').':'.block_ilp_student_info_edit_button($user->id,$tutor->id,$course->id,'tutor','teacher',$text->id).'</td></tr>';
					}

					if($config->block_ilp_student_info_allow_per_tutor_shared_text == 1 && $sharedtext == TRUE) {
						$text = block_ilp_student_info_get_text($user->id,$tutor->id,$course->id,'tutor','shared') ;
						echo '<tr><td>'.get_string('notextshared','block_ilp').':'.block_ilp_student_info_edit_button($user->id,$tutor->id,$course->id,'tutor','shared',$text->id).'</td></tr>';
					}
				}
			}
		}
    	unset($tutors);
		echo '</tbody></table>';
	}
	unset($tutors);
	unset($supertutors);
}

/**
    * Displays the Programme Review (formerly Subject Report) summary to the ILP
    *
    * @param id   			userid fed from ILP page
    * @param courseid   	courseid fed from ILP page
    * @param full   	   	display a full report or just a title link - for layout and navigation
    * @param title  	 	   display default title - turn off to add customised title to template
	 * @param icon     		display an icon with the title
	 * @param teachertext   display the tutor text (formerly teacher text) section  
	 * @param studenttext   display the student text section
	 * @param sharedtext   	display the supertutor (CQM) text(formerly shared text) section
*/

function display_ilp_subject_report ($id,$courseid,$full=TRUE,$title=TRUE,$icon=TRUE,$teachertext=TRUE,$studenttext=TRUE,$sharedtext=TRUE) {

	global $CFG,$USER;
	require_once("../ilp_student_info/block_ilp_student_info_lib.php");
	include ('access_context.php');

	$module = 'project/ilp';
   $config = get_config($module);

	$user = get_record('user','id',$id);

	if($title == TRUE) {
	   // Programme Review Icon and Heading ///////////////////////////////
	   echo'<table width="100%"><tr><td>';
		echo '<h2>';
		
		if ($icon == TRUE) { 
			if (file_exists('templates/custom/pix/subject_report.gif')) {
				echo '<img src="'.$CFG->wwwroot.'/blocks/ilp/templates/custom/pix/subject_report.gif" alt="" />';
			}else{
      			echo '<img src="'.$CFG->wwwroot.'/blocks/ilp/pix/subject_report.gif" alt="" />'; 
			}
		}
      // Avoid blank Programme Review Page
	   //echo '<a href="'.$CFG->wwwroot.'/blocks/ilp_student_info/view.php?id='.$id.(($courseid)?'&courseid='.$courseid:'').'&amp;view=subject">'.get_string('subject_report', 'block_ilp').'</a></h2>';
	   echo get_string('subject_report', 'block_ilp').'</h2>';
      echo '</td><td><img style="float:right; width:100px" src="http://moodle.yeovil.ac.uk/blocks/ilp/pix/yclogo.png" /></td></tr></table>';
	   
	}

	if($full == TRUE) {

		$ilpcourses = get_my_ilp_courses($user->id);

    	foreach ($ilpcourses as $course) {
        	//print_heading("$course->fullname ($course->shortname)", "left", "3");

        	// who teachers with it ?
	      $context = get_context_instance(CONTEXT_COURSE, $course->id);
         $teachers = get_users_by_capability($context, 'block/ilp:tutoreditreview', 'u.id,u.firstname,u.lastname', 'u.lastname ASC', '', '', '', '', false);
			$supertutors = get_users_by_capability($context, 'block/ilp:supertutoreditreview', 'u.id,u.firstname,u.lastname', 'u.lastname ASC', '', '', '', '', false);
        	foreach($supertutors as $supertutor) {
        	foreach($teachers as $teacher) {
        	echo '<table style="text-align:left; margin:5px;" class="generalbox"><tbody>';
				if (count_records('ilp_student_info_per_teacher','teacher_userid',$teacher->id, 'courseid', $course->id, 'student_userid', $user->id) >= 0){
               echo '<tr><td><b>Name:</b> <a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&courseid=1">'.fullname($user).'</a> </td></tr>';
					echo '<tr><td><b>Tutor:</b> <a href="'.$CFG->wwwroot.'/user/view.php?id='.$teacher->id.'&courseid=1">'.fullname($teacher).'</a><td></tr>';
					// echo '<tr><td>From September 2009 To December 2009</td></tr>';
					// echo '<tr><td><b>Review Period:</b> From '.$startmonth.' '.$startyear.' to '.$endmonth.' '.$endyear.'</td></tr>';
                                   echo '<tr><td></td></tr>';
					// Student text					
					if($config->block_ilp_student_info_allow_per_teacher_student_text == 1){
					   if($studenttext == TRUE) {
						    $text = block_ilp_student_info_get_text($user->id,$teacher->id,$course->id,'teacher','student');
						    echo'<tr><td><br />'.get_string('student_response','block_ilp_student_info').':</td></tr><tr><td><div class="block_ilp_student_info_text">'.stripslashes($text->text).'</div></td>';

						    if($access_isuser or $access_isgod) {
								    echo '<td><span class="noprint">'.block_ilp_student_info_edit_button($user->id,$teacher->id,$course->id,'teacher','student',$text->id).'</span></td></tr>' ;
						    }else{
							    echo '<td></td></tr>';
				  		    }
				  		 }else{
				  		 //echo '<td>Test Button 2</tr>';
				  		 }
					}
					
					// Tutor text
					if($config->block_ilp_student_info_allow_per_teacher_teacher_text == 1 && $teachertext == TRUE) {
						$text = block_ilp_student_info_get_text($user->id,$teacher->id,$course->id,'teacher','teacher');
						
						echo '<tr><td><br />'.get_string('tutor_comment','block_ilp_student_info').':</td></tr><tr><td><div class="block_ilp_student_info_text">'.stripslashes($text->text).'</div></td>';

						if($teacher->id == $USER->id or $supertutor->id == $USER->id or $access_isgod) {
							echo '<td><span class="noprint">'.block_ilp_student_info_edit_button($user->id,$teacher->id,$course->id,'teacher','teacher',$text->id).'</span></td></tr>' ;
						}else{
							echo '<td></td></tr>';
				  		}
					}

				}elseif($teacher->id == $USER->id or $access_issupertutor or $access_isgod){

					if($config->block_ilp_student_info_allow_per_teacher_teacher_text == 1) {
						$text = block_ilp_student_info_get_text($user->id,$teacher->id,$course->id,'teacher','teacher') ;
						echo '<tr><td><span class="noprint">'.get_string('notexttutor','block_ilp').':'.block_ilp_student_info_edit_button($user->id,$teacher->id,$course->id,'teacher','teacher',$text->id).'</span></td></tr>';
					}
				}
			}
			}
			unset($supertutors);
			unset($teachers);
						
			// Supertutors (CQMS)
         $teachers = get_users_by_capability($context, 'block/ilp:supertutoreditreview', 'u.id,u.firstname,u.lastname', 'u.lastname ASC', '', '', '', '', false);
			//echo '<table style="text-align:left; margin:5px;" class="generalbox"><tbody>';

			foreach($teachers as $teacher) {
				if (count_records('ilp_student_info_per_teacher','teacher_userid',$teacher->id, 'courseid', $course->id, 'student_userid', $user->id) >= 0){

					// Super Tutor text
					if($config->block_ilp_student_info_allow_per_teacher_teacher_text == 1 && $teachertext == TRUE) {
						$text = block_ilp_student_info_get_text($user->id,$teacher->id,$course->id,'teacher','teacher');
                  echo '<tr><td>Curriculum Quality Manager comment:</td></tr><tr><td><div class="block_ilp_student_info_text">'.stripslashes($text->text).'</div></td>';
						if($teacher->id == $USER->id or $access_isgod) {
						echo '<td><span class="noprint">'.block_ilp_student_info_edit_button($user->id,$teacher->id,$course->id,'teacher','teacher',$text->id).'</span></td></tr>' ;
						}else{
							echo '<td></td></tr>';
				  		}
					}
					echo '<tr><td><a href="'.$CFG->wwwroot.'/user/view.php?id='.$teacher->id.'&courseid=1">'.fullname($teacher).'</a><td></tr>';
				   echo '<tr><td><i>Please contact me at College if there are any aspects of these reports that you wish to discuss</i></td></tr>';

				}elseif($teacher->id == $USER->id or $access_isgod){

					if($config->block_ilp_student_info_allow_per_teacher_teacher_text == 1) {
						$text = block_ilp_student_info_get_text($user->id,$teacher->id,$course->id,'teacher','teacher') ;
						echo '<tr><td><span class="noprint">'.get_string('notextsupertutor','block_ilp').':'.block_ilp_student_info_edit_button($user->id,$teacher->id,$course->id,'teacher','teacher',$text->id).'</span></td></tr>';
					}
				}
			}
			unset($teachers);
			echo '</tbody></table>';
		}
	}
}



?>

