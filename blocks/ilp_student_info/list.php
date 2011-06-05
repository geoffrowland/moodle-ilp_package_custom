<?PHP 



//  Lists the student info texts relevant to the student.

//  with links to edit for those who can. 





    require_once('../../config.php');

    require_once('block_ilp_student_info_lib.php');



    $contextid    = optional_param('contextid', 0, PARAM_INT);               // one of this or

    $courseid     = optional_param('courseid', SITEID, PARAM_INT);                  // this are required

	$updatepref = optional_param('updatepref', -1, PARAM_INT);



    $coursecontext ;

    if ($contextid) {

        if (! $coursecontext = get_context_instance_by_id($contextid)) {

            error("Context ID is incorrect");

        }

        if (! $course = get_record('course', 'id', $coursecontext->instanceid)) {

            error("Course ID is incorrect");

        }

    } else if ($courseid) {

        if (! $course = get_record('course', 'id', $courseid)) {

            error("Course ID is incorrect");

        }

        if (! $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id)) {

            error("Context ID is incorrect");

        }

    }



    require_login($course);



    $sitecontext = get_context_instance(CONTEXT_SYSTEM);



    if (has_capability('moodle/site:doanything',$sitecontext)) {  // are we god ?

        $access_isgod = 1 ;

    }

    if (has_capability('block/ilp_student_info:viewclass',$coursecontext)) { // are we the teacher on the course ?

        $access_isteacher = 1 ;

    }



/// Print headers



    if ($course->id != $SITE->id) {

        print_header(get_string('ilp_student_info','block_ilp_student_info'), $course->fullname,

                     "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> -> ".

                     get_string('ilp_student_info','block_ilp_student_info'), "", "", true, "&nbsp;", navmenu($course));

    } else {

        print_header(get_string('ilp_student_info','block_ilp_student_info'), $course->fullname,

                     get_string('ilp_student_info','block_ilp_student_info'), "", "", true, "&nbsp;", navmenu($course));

    }

	



    if ($courseid and $access_isteacher) {

	$context = get_context_instance(CONTEXT_COURSE, $course->id);

	$baseurl = $CFG->wwwroot.'/blocks/ilp_student_info/list.php?courseid='.$courseid;

	if ($updatepref > 0){

		$perpage = optional_param('perpage', 10, PARAM_INT);

		$perpage = ($perpage <= 0) ? 10 : $perpage ;

		set_user_preference('target_perpage', $perpage);

	}

	

	/* next we get perpage and from database

     */

    

	$perpage = get_user_preferences('target_perpage', 10);

	$teacherattempts = false; /// Temporary measure

	$page    = optional_param('page', 0, PARAM_INT);

/// Check to see if groups are being used in this course
/// and if so, set $currentgroup to reflect the current group

    $groupmode    = groups_get_course_groupmode($course);   // Groups are being used
    $currentgroup = groups_get_course_group($course, true);

    if (!$currentgroup) {      // To make some other functions work better later
        $currentgroup  = NULL;
    }

    $isseparategroups = ($course->groupmode == SEPARATEGROUPS and $course->groupmodeforce and
                         !has_capability('moodle/site:accessallgroups', $context));	

    /// Get all teachers and students

    //$users = get_users_by_capability($context, 'mod/ilptarget:view'); // everyone with this capability set to non-prohibit



	print_heading("Students in ".$course->shortname);

    groups_print_course_menu($course, $baseurl); 
	
	$doanythingroles = get_roles_with_capability('moodle/site:doanything', CAP_ALLOW, $sitecontext);

	/*if ($roles = get_roles_used_in_context($context)) {

        

        // We should exclude "admin" users (those with "doanything" at site level) because 

        // Otherwise they appear in every participant list



        



        foreach ($roles as $role) {

            if (isset($doanythingroles[$role->id])) {   // Avoid this role (ie admin)

                unset($roles[$role->id]);

                continue;

            }

            $rolenames[$role->id] = strip_tags(format_string($role->name));   // Used in menus etc later on

        }

    }*/

		

	$tablecolumns = array('picture', 'fullname', 'ilp');

	$tableheaders = array('', get_string('fullname'), '');



	require_once($CFG->libdir.'/tablelib.php');

	$table = new flexible_table('mod-targets');

					

	$table->define_columns($tablecolumns);

	$table->define_headers($tableheaders);

	$table->define_baseurl($baseurl);

	

	$table->sortable(true, 'lastname');

	$table->collapsible(false);

	$table->initialbars(true);

	

	$table->column_suppress('picture');	

	$table->column_class('picture', 'picture');

	$table->column_class('fullname', 'fullname');

	$table->column_class('ilp', 'ilp');

	

	$table->set_attribute('cellspacing', '0');

	$table->set_attribute('id', 'attempts');

	$table->set_attribute('class', 'submissions');

	$table->set_attribute('width', '90%');

	$table->set_attribute('align', 'center');

		

	// Start working -- this is necessary as soon as the niceties are over

	$table->setup();

	

	// we are looking for all users with this role assigned in this context or higher

    if ($usercontexts = get_parent_contexts($context)) {

        $listofcontexts = '('.implode(',', $usercontexts).')';

    } else {

        $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);

        $listofcontexts = '('.$sitecontext->id.')'; // must be site

    }

	

	//if (empty($users)) {

		//print_heading(get_string('noattempts','assignment'));

		//return true;

	//}

	

	$select = 'SELECT u.id, u.firstname, u.lastname, u.picture ';

    $from = 'FROM '.$CFG->prefix.'user u INNER JOIN

    '.$CFG->prefix.'role_assignments ra on u.id=ra.userid LEFT OUTER JOIN

    '.$CFG->prefix.'user_lastaccess ul on (ra.userid=ul.userid and ul.courseid = '.$course->id.') LEFT OUTER JOIN

    '.$CFG->prefix.'role r on ra.roleid = r.id '; 

    

    // excluse users with these admin role assignments

    if ($doanythingroles) {

        $adminroles = 'AND ra.roleid NOT IN (';

 

        foreach ($doanythingroles as $aroleid=>$role) {

            $adminroles .= "$aroleid,";

        }

        $adminroles = rtrim($adminroles,",");

        $adminroles .= ')';

    } else {

        $adminroles = '';

    }

	

	// join on 2 conditions

    // otherwise we run into the problem of having records in ul table, but not relevant course

    // and user record is not pulled out

    $where  = "WHERE (ra.contextid = $context->id OR ra.contextid in $listofcontexts)

	 AND u.deleted = 0

        AND (ul.courseid = $course->id OR ul.courseid IS NULL)

        AND u.username <> 'guest' 

	 AND ra.roleid = 5

	 AND r.shortname = 'student'

        $adminroles";

     



    $wheresearch = '';

	

	if ($currentgroup) {    // Displaying a group by choice

        // FIX: TODO: This will not work if $currentgroup == 0, i.e. "those not in a group"

        $from  .= 'LEFT JOIN '.$CFG->prefix.'groups_members gm ON u.id = gm.userid ';

        $where .= ' AND gm.groupid = '.$currentgroup;

    }

	

	if ($table->get_sql_where()) {

        $where .= ' AND '.$table->get_sql_where();

    }



    if ($table->get_sql_sort()) {

        $sort = ' ORDER BY '.$table->get_sql_sort();

    } else {

        $sort = '';

    }

	

	$nousers = get_records_sql($select.$from.$where.$wheresearch.$sort);

		   

	$table->pagesize($perpage, count($nousers));

	///offset used to calculate index of student in that particular query, needed for the pop up to know who's next

    $offset = $page * $perpage;

	

	if (($ausers = get_records_sql($select.$from.$where.$wheresearch.$sort, $table->get_page_start(), $table->get_page_size())) !== false) {

            

            foreach ($ausers as $auser) {

				

			$picture = print_user_picture($auser->id, $course->id, $auser->picture, false, true);

			

                $update  = '<a href="view.php?courseid='.$courseid.'&amp;id='.$auser->id.'">'.get_string('viewilp_student_info', 'block_ilp_student_info').'</a>';

                

                $row = array($picture, fullname($auser), $update);

                $table->add_data($row);

            }

        }

        



        $table->print_html();  /// Print the whole table



		/// Mini form for setting user preference

        echo '<br />';

        echo '<form name="options" action="list.php?id='.$courseid.'" method="post">';

        echo '<input type="hidden" id="updatepref" name="updatepref" value="1" />';

        echo '<table id="optiontable" align="center">';

        echo '<tr align="right"><td>';

        echo '<label for="perpage">'.get_string('pagesize','ilptarget').'</label>';

        echo ':</td>';

        echo '<td align="left">';

        echo '<input type="text" id="perpage" name="perpage" size="1" value="'.$perpage.'" />';

        helpbutton('pagesize', get_string('pagesize','ilptarget'), 'target');

        echo '</td></tr>';

        echo '<tr>';

        echo '<td colspan="2" align="right">';

        echo '<input type="submit" value="'.get_string('savepreferences').'" />';

        echo '</td></tr></table>';

        echo '</form>';



    } else {

        $courses = get_my_courses($USER->id); // sould be courses i can teach in

        $courses = get_records_sql("SELECT course.* 

                                    FROM {$CFG->prefix}role_assignments ra,

                                         {$CFG->prefix}role_capabilities rc,

                                         {$CFG->prefix}context c,

                                         {$CFG->prefix}course course

                                    WHERE ra.userid = $USER->id

                                    AND   ra.contextid = c.id

                                    AND   ra.roleid = rc.roleid

                                    AND   rc.capability = 'block/ilp_student_info:viewclass'

                                    AND   c.instanceid = course.id

                                    AND   c.contextlevel = ".CONTEXT_COURSE);



        $mentees = get_records_sql("SELECT u.*

                                    FROM {$CFG->prefix}role_assignments ra,

                                         {$CFG->prefix}context c,

                                         {$CFG->prefix}user u

                                    WHERE ra.userid = $USER->id

                                    AND   ra.contextid = c.id

                                    AND   c.instanceid = u.id

                                    AND   c.contextlevel = ".CONTEXT_USER);



        

        if ($mentees) {

            print_heading("Mentees");

            foreach ($mentees as $user) {

?>

<a href="view.php?id=<?php echo $user->id ?>"><?php echo "$user->firstname $user->lastname "?></a><br />

<?php

            }

        }

        if ($courses) {

            print_heading("Courses");

            foreach ($courses as $course) {

?>

<a href="list.php?courseid=<?php echo $course->id ?>"><?php echo $course->shortname ?></a><br />

<?php

            }

        }

        if (!($mentees or $courses)) {

            redirect("view.php",get_string('youarebeingredirectedtoyourown','block_ilp_student_info'),0);

        }

    }

   

    print_footer($course);

?>

