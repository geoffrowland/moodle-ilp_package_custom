<?PHP



/*

 * @copyright &copy; 2007 University of London Computer Centre

 * @author http://www.ulcc.ac.uk, http://moodle.ulcc.ac.uk

 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License

 * @package ILP

 * @version 1.0

 */





class block_ilp extends block_list {

    function init() {

        $this->title = get_string('blockname', 'block_ilp');
        $this->version = 2008053103;

    }

	function has_config() {
        return true;

    }

	function config_save($data) {
    // Default behavior: save all variables as $CFG properties
	$module = 'project/ilp';
       foreach ($data as $name => $value) {
         set_config($name, $value, $module);
       }
       return true;
	}

    function get_content() {

        global $CFG,$USER;
 		$module = 'project/ilp';
		$config = get_config($module);
              include_once($CFG->dirroot.'/my/pagelib.php');
        page_id_and_class($id,$class);

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        // the following 3 lines is need to pass _self_test();
        if (empty($this->instance->pageid)) {
            return '';
        }
        $access_isgod = 0 ;
        $access_isteacher = 0 ;
        $access_isstudent = 0 ;
        $access_istutor = 0 ;
        $access_isother = 0 ;
        $access_ismymoodle = 0;

        if($id == PAGE_MY_MOODLE){
            $access_ismymoodle = 1;
        }

		if (has_capability('moodle/site:doanything', get_context_instance(CONTEXT_SYSTEM))) {  // are we god ?

        $access_isgod = 1 ;
    }

//      if (!$currentcontext = get_context_instance(CONTEXT_COURSE, $this->instance->pageid)) {

        if ($access_ismymoodle || $this->instance->pageid == SITEID || !$currentcontext = get_context_instance(CONTEXT_COURSE, $this->instance->pageid)) {
            $courses = count_records_sql("SELECT course.*
                                    FROM {$CFG->prefix}role_assignments ra,
                                         {$CFG->prefix}role_capabilities rc,
                                         {$CFG->prefix}context c,
                                         {$CFG->prefix}course course
                                    WHERE ra.userid = $USER->id
                                    AND   ra.contextid = c.id
                                    AND   ra.roleid = rc.roleid
                                    AND   rc.capability = 'block/ilp:viewclass'
                                    AND   c.instanceid = course.id
                                    AND   c.contextlevel = ".CONTEXT_COURSE);
            $mentees = count_records_sql("SELECT u.*
                                    FROM {$CFG->prefix}role_assignments ra, {$CFG->prefix}context c, {$CFG->prefix}user u
                                    WHERE ra.userid = $USER->id AND ra.contextid = c.id AND c.instanceid = u.id AND c.contextlevel = ".CONTEXT_USER);
            if($courses > 0) {
                $access_isteacher = 1;
            }elseif($mentees > 0){
                $access_istutor = 1;
            }else{
                $access_isstudent = 1;
            }
            $access_isother = 1 ;
           }else{
            if (has_capability('block/ilp:viewclass',$currentcontext)) { // are we the teacher on the course ?
                $access_isteacher = 1 ;
            } elseif (has_capability('block/ilp:view',$currentcontext)) {  // are we a student on the course ?
                $access_isstudent = 1 ;
            }
        }
        $this->content = new object();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        $url = ($access_isstudent) ? $CFG->wwwroot.'/blocks/ilp/view.php' : $CFG->wwwroot.'/blocks/ilp/list.php' ;

        if (!$access_isother) {
            $url .= "?courseid=".$this->instance->pageid ;
        }
		if(!empty($config->ilp_user_guide_link) && $config->ilp_user_guide_link != '0'){
			$this->content->items[] = '<a href="'.$config->ilp_user_guide_link.'" target="newWin">'.get_string('userguide','block_ilp').'</a>';
			$this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/info.gif" class="icon" alt="" />';
		}
		if ($access_isstudent) {
			$this->content->items[] = '<a href="'.$url.'">'.get_string('viewmyilp','block_ilp').'</a>';
			$this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/users.gif" class="icon" alt="" />';
		}else{
			$this->content->items[] = '<a href="'.$url.'">'.get_string('viewilps','block_ilp').'</a>';
			$this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/users.gif" class="icon" alt="" />';

//			if ($this->instance->pageid != SITEID) {
                     if (!$access_ismymoodle && ($this->instance->pageid != SITEID && ($access_isgod || $access_isteacher))) {
			$this->content->items[] = '<a href="'.$CFG->wwwroot.'/mod/ilptarget/view_students.php?courseid='.$this->instance->pageid.'">'.get_string('modulenameplural','ilptarget').'</a>';
			$this->content->icons[] = '<img src="'.$CFG->pixpath.'/mod/ilptarget/icon.gif" class="icon" alt="" />';
////////
//		if(ilptarget_get_total($USER->id) > 0){
//				$this->content->text .= '<div class="progress-container"><span id="progress_text"><a href="'.$CFG->wwwroot.'/mod/ilptarget/target_view.php?courseid='.$this->instance->pageid.'&amp;userid='.$USER->id.'">'.get_string('modulenameplural','ilptarget').': '.ilptarget_display_complete($USER->id).'</a>';
//				if(ilptarget_check_new ($USER->id,$USER->id) == 1) {
//					$this->content->text .= '<img src="'.$CFG->pixpath.'/i/new.gif" alt="" style="margin-left: 2px" />';
//				}
//				$this->content->text .= '</span><div class="attendance-blue" style="width: '.ilptarget_percentage_complete($USER->id).'%;"></div></div>';
//		}else{
//			$this->content->text .= '<div class="progress-container"><span id="progress_text"><a href="'.$CFG->wwwroot.'/mod/ilptarget/target_view.php?courseid='.$this->instance->pageid.'&amp;userid='.$USER->id.'">'.get_string('norecords','ilptarget').'</a></span><div class="attendance-blue" style="width: 0%;"></div></div>';
// 	}
////////
			$this->content->items[] = '<a href="'.$CFG->wwwroot.'/mod/ilpconcern/view_students.php?courseid='.$this->instance->pageid.'">'.get_string('modulenameplural','ilpconcern').'</a>';
			$this->content->icons[] = '<img src="'.$CFG->pixpath.'/mod/ilpconcern/icon.gif" class="icon" alt="" />';

			}

//			if($access_isgod || $access_isteacher) {
                     if(!$access_ismymoodle && ($access_isgod || ($access_isteacher && $this->instance->pageid != SITEID))) {
				$this->content->items[] = get_string('download_reports','block_ilp').':';
				$this->content->icons[] = '';

				$this->content->items[] = '<a href="'.$CFG->wwwroot.'/blocks/ilp/reports.php?mode=user&amp;courseid='.$this->instance->pageid.'">'.get_string('user_reports','block_ilp').'</a>';
				$this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/users.gif" class="icon" alt="" />';
				if($access_isgod) {
					$this->content->items[] = '<a href="'.$CFG->wwwroot.'/blocks/ilp/reports.php?mode=course">'.get_string('course_reports','block_ilp').'</a>';
					$this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/course.gif" class="icon" alt="" />';
				}
			}
		}

        return $this->content;

    }

    // my moodle can only have SITEID and it's redundant here, so take it away
    function applicable_formats() {
        return array('all' => true, 'my' => false);
    }
}
?>

