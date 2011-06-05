<?php  



/*

 * @copyright &copy; 2007 University of London Computer Centre

 * @author http://www.ulcc.ac.uk, http://moodle.ulcc.ac.uk

 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License

 * @package ILP

 * @version 1.0

 */

require_once("../../config.php");

    $courseid   = optional_param('courseid', 1, PARAM_INT);
    $mode = optional_param('mode', '', PARAM_TEXT);
    
    require_login();
    
    $sitecontext = get_context_instance(CONTEXT_SYSTEM);
    
    if (has_capability('moodle/legacy:guest', $sitecontext, NULL, false)) {
        error("You are logged in as Guest.");
    }
    
    if ($courseid) {

        if (! $course = get_record('course', 'id', $courseid)) {
            error("Course ID is incorrect");
        }

        if (! $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id)) {
            error("Context ID is incorrect");
        }
    }
                        
function ilp_get_status($userid) {
    global $CFG;
    $module = 'project/ilp';
    $config = get_config($module);
    
    if($CFG->ilpconcern_status_per_student == 1){

        if($studentstatus = get_record('ilpconcern_status', 'userid', $userid)){

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

                $thisstudentstatus = get_string('withdrawn', 'ilpconcern');

                break;
                
            case "4":

                $thisstudentstatus = get_string('contract', 'ilpconcern');

                break;
        }

        $studentstatusnum = $studentstatus->status;

    }else{

        $studentstatusnum = 0;

        $thisstudentstatus = get_string('green', 'ilpconcern');

    }
    
        return $thisstudentstatus;
    
    }else{
    
        return '';
    
    }

}

function ilp_report_byuser_csv($courseid) {
    global $CFG, $SESSION;
    
    $fields = array('username'  => 'Username',
                    'firstname' => 'First Name',
                    'lastname'  => 'Last Name',
                    'idnumber'  => 'ID Number',
                    'status'    => 'Status',
                    'tutorcompletedtargets' => 'Completed Targets (Tutor Set)',
                    'tutorsettargets' => 'Tutor Set Targets',
                    'studentcompletedtargets' => 'Completed Targets (Student Set)',
                    'studentsettargets' => 'Student Set Targets',
                    'subjectreports' => 'Subject Reports',
                    'report1'    => 'Report 1 Total',
                    'report2'    => 'Report 2 Total',
                    'report3'    => 'Report 3 Total',
                    'report4'    => 'Report 4 Total',
                    'report5'    => 'Report 5 Total',
                    'report6'    => 'Report 6 Total'
                     
                    );

    $filename = clean_filename('ilpuserreport.csv');

    header("Content-Type: application/download\n");
    header("Content-Disposition: attachment; filename=$filename");
    header("Expires: 0");
    header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");

    $delimiter = get_string('listsep');
    $encdelim  = '&#'.ord($delimiter);

    $row = array(); 
    foreach ($fields as $fieldname) {
        $row[] = str_replace($delimiter, $encdelim, $fieldname);
    }
    echo implode($delimiter, $row)."\n";
    
    if($courseid > 1) {
        $select = 'SELECT u.id, u.firstname, u.lastname, u.idnumber ';
        $context = get_context_instance(CONTEXT_COURSE, $courseid);
        
        $from = 'FROM '.$CFG->prefix.'user u INNER JOIN
        '.$CFG->prefix.'role_assignments ra on u.id=ra.userid LEFT OUTER JOIN
        '.$CFG->prefix.'user_lastaccess ul on (ra.userid=ul.userid and ul.courseid = '.$courseid.') LEFT OUTER JOIN
        '.$CFG->prefix.'role r on ra.roleid = r.id '; 
        
        $where  = "WHERE ra.contextid = $context->id
        AND u.deleted = 0
        AND (ul.courseid = $courseid OR ul.courseid IS NULL)
        AND u.username <> 'guest' 
        AND ra.roleid = 5
        AND r.id = 5 ";
        
        $sort = "ORDER BY u.lastname ASC ";
        
        $users = get_records_sql($select.$from.$where);
        
    }else{
        $users = get_records('user');
    }
    
    foreach ($users as $auser) {
        $row = array();
        if (!$user = get_record('user', 'id', $auser->id)) {
            continue;
        }
            
    $report = array('username' => $user->username,
                    'firstname' => $user->firstname,
                    'lastname'  => $user->lastname,
                    'idnumber'  => $user->idnumber,
                    'status'    => ilp_get_status($user->id)
                    );
                    
        $report['tutorcompletedtargets'] = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilptarget_posts WHERE setforuserid = '.$user->id.' AND setforuserid != setbyuserid AND status = "1"');
        
        $report['tutorsettargets'] = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilptarget_posts WHERE setforuserid = '.$user->id.' AND setforuserid != setbyuserid AND status != "3"' );
        
        $report['studentcompletedtargets'] = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilptarget_posts WHERE setforuserid = '.$user->id.' AND setforuserid = setbyuserid AND status = "1"');
    
        $report['studentsettargets'] = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilptarget_posts WHERE setforuserid = '.$user->id.' AND setforuserid = setbyuserid AND status != "3"' );
        $report['subjectreports'] = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilp_student_info_per_teacher WHERE student_userid = '.$user->id);   
    
        $report['report1'] = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilpconcern_posts WHERE setforuserid = '.$user->id.' AND status = "0"' );
    
        $report['report2'] = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilpconcern_posts WHERE setforuserid = '.$user->id.' AND status = "1"' );
    
        $report['report3'] = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilpconcern_posts WHERE setforuserid = '.$user->id.' AND status = "2"' );
    
        $report['report4'] = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilpconcern_posts WHERE setforuserid = '.$user->id.' AND status = "3"' );

        $report['report5'] = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilpconcern_posts WHERE setforuserid = '.$user->id.' AND status = "4"' );
 
        $report['report6'] = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilpconcern_posts WHERE setforuserid = '.$user->id.' AND status = "5"' );
    
        foreach ($report as $data){
            $row[] = str_replace($delimiter, $encdelim, $data);
        }
        
        echo implode($delimiter, $row)."\n";
    }
    die;
}

function ilp_report_bycourse_csv() {
    global $CFG, $SESSION;
    
    $fields = array('coursename'  => 'Course',
                    'shortname' => 'Short Name',
                    'idnumber'  => 'Course ID',
                    'category' => 'Category',
                    'tutorcompletedtargets' => 'Completed Targets (Tutor Set)',
                    'tutorsettargets' => 'Tutor Set Targets',
                    'studentcompletedtargets' => 'Completed Targets (Student Set)',
                    'studentsettargets' => 'Student Set Targets',
                    'subjectreports' => 'Subject Reports',
                    'report1'    => 'Report 1 Total',
                    'report2'    => 'Report 2 Total',
                    'report3'    => 'Report 3 Total',
                    'report3'    => 'Report 4 Total',
                    'report5'    => 'Report 5 Total',
                    'report6'    => 'Report 6 Total'

                    );
    
    $filename = clean_filename('ilpcoursereport.csv');

    header("Content-Type: application/download\n");
    header("Content-Disposition: attachment; filename=$filename");
    header("Expires: 0");
    header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");

    $delimiter = get_string('listsep');
    $encdelim  = '&#'.ord($delimiter);

    $row = array(); 
    foreach ($fields as $fieldname) {
        $row[] = str_replace($delimiter, $encdelim, $fieldname);
    }
    echo implode($delimiter, $row)."\n";
    
    $courses = get_records('course');
    
    foreach ($courses as $course) {
        $row = array();
        if (!$course = get_record('course', 'id', $course->id)) {
            continue;
        }
    
    if($course->id > 1) {
        $category = get_record('course_categories','id',$course->category);
    }else{
        $category->name = 'None';
    }
            
    $report = array('username' => $course->fullname,
                    'firstname' => $course->shortname,
                    'idnumber'  => $course->idnumber,
                    'category' => $category->name
                    );
                    
    $report['tutorcompletedtargets'] = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilptarget_posts WHERE course = '.$course->id.' AND setforuserid != setbyuserid AND status = "1"');
    $report['tutorsettargets'] = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilptarget_posts WHERE course = '.$course->id.' AND setforuserid != setbyuserid AND status != "3"' );
    
    $report['studentcompletedtargets'] = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilptarget_posts WHERE course = '.$course->id.' AND setforuserid = setbyuserid AND status = "1"');
    
    $report['studentsettargets'] = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilptarget_posts WHERE course = '.$course->id.' AND setforuserid = setbyuserid AND status != "3"' );
    $report['subjectreports'] = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilp_student_info_per_teacher WHERE courseid = '.$course->id);
        
    $report['report1'] = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilpconcern_posts WHERE course = '.$course->id.' AND status = "0"' );
    
    $report['report2'] = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilpconcern_posts WHERE course = '.$course->id.' AND status = "1"' );
    
    $report['report3'] = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilpconcern_posts WHERE course = '.$course->id.' AND status = "2"' );
    
    $report['report4'] = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilpconcern_posts WHERE course = '.$course->id.' AND status = "3"' );
    
    $report['report5'] = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilpconcern_posts WHERE course = '.$course->id.' AND status = "4"' );

    $report['report6'] = count_records_sql('SELECT COUNT(*) FROM '.$CFG->prefix.'ilpconcern_posts WHERE course = '.$course->id.' AND status = "5"' );

        foreach ($report as $data){
            $row[] = str_replace($delimiter, $encdelim, $data);
        }
        
        echo implode($delimiter, $row)."\n";
    }
    die;
}


if(has_capability('moodle/site:doanything',$sitecontext) || has_capability('block/ilp:viewclass',$coursecontext)){  
    if ($mode == 'user') {
        ilp_report_byuser_csv($course->id);
    }elseif($mode == 'course'){
        ilp_report_bycourse_csv();
    }
}

    
?>
