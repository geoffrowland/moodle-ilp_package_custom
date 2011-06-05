<?PHP 



//  Lists the student info texts relevant to the student.

//  with links to edit for those who can. 

    require_once('../../config.php');
    require_once('block_ilp_student_info_lib.php');
    require_once($CFG->dirroot.'/blocks/ilp/block_ilp_lib.php');
    require_once('access_context.php'); 

    $contextid    = optional_param('contextid', 0, PARAM_INT);               // one of this or
    $courseid     = optional_param('courseid', SITEID, PARAM_INT);                  // this are required
    $userid       = optional_param('id', 0, PARAM_INT);                  // this is required
    $view       = optional_param('view', 'all', PARAM_TEXT); 
    $text       = optional_param('text', 'all', PARAM_TEXT);   

 $module = 'project/ilp';
$config = get_config($module);

    if (!$userid) {

        $userid = $USER->id ;

    }

    include('access_context.php'); 

    $user = get_record('user','id',$userid);

/// Print headers

    if ($course->id != SITEID) {
        echo '<span class="noprint">'.print_ilpheader(fullname($user)." ".get_string('ilp_student_info','block_ilp_student_info'), $course->fullname,"<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> -> "."<a href=\"../../blocks/ilp/view.php?courseid=$course->id&amp;id=$user->id\">".get_string("ilp", "block_ilp")."</a> -> ".get_string('ilp_student_info','block_ilp_student_info')." -> ".fullname($user)."", "", "", true, "&nbsp;", navmenu($course)).'</span>';
        
        //print_header();
    } else {

        echo '<span class="noprint">'.print_ilpheader(fullname($user)." ".get_string('ilp_student_info','block_ilp_student_info'), $course->fullname,"<a href=\"../../blocks/ilp/view.php?courseid=$course->id&amp;id=$user->id\">".get_string("ilp", "block_ilp")."</a> -> ".get_string('ilp_student_info','block_ilp_student_info')." -> ".fullname($user)."", "", "", true, "&nbsp;", navmenu($course)).'</span>';
    }

if (file_exists('templates/custom/template.php')) {
  include('templates/custom/template.php');
}elseif (file_exists('template.php')) {
  include('template.php');
}else{
  error("missing template \"$template\"") ; 
}
 
//echo '<div class="noprint">';
print_ilpfooter($course);
//echo '</div>';

?>
