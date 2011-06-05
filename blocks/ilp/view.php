<?PHP 

/*
 * @copyright &copy; 2007 University of London Computer Centre
 * @author http://www.ulcc.ac.uk, http://moodle.ulcc.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package ILP
 * @version 1.0
 */

//  Lists all the users who's ilp one can view

    require_once('../../config.php');
    require_once($CFG->dirroot.'/user/profile/lib.php');
    require_once('block_ilp_lib.php');
    include('access_context.php');    

    global $USER, $CFG;

    $contextid    = optional_param('contextid', 0, PARAM_INT);

    $user = get_record('user', 'id',$userid);

/// Print headers

    if ($course->id != SITEID) {
        print_header((($access_isuser)?get_string('viewmyilp','block_ilp'):fullname($user)." ".get_string('ilp','block_ilp')), $course->fullname,"<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> -> ".          (($access_isuser)?get_string('viewmyilp','block_ilp'):(($courseid)?"<a href=\"list.php?courseid=$course->id\">".get_string('ilps','block_ilp')."</a>":get_string('ilp','block_ilp')))." -> ".fullname($user)."", "", "", true, "&nbsp;", navmenu($course));

    } else {

        print_header(fullname($user)." ".(($access_isuser)?get_string('viewmyilp','block_ilp'):get_string('ilp','block_ilp')), $course->fullname,                    (($access_isuser)?get_string('viewmyilp','block_ilp'):(($courseid)?"<a href=\"list.php?courseid=$course->id\">".get_string('ilps','block_ilp')."</a>":get_string('ilp','block_ilp')))." -> ".fullname($user)."", "", "", true, "&nbsp;", navmenu($course));

    }

    block_ilp_report($user->id,$course->id);

    print_footer($course);

?>

