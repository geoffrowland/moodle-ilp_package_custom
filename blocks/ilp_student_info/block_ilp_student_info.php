<?php



class block_ilp_student_info extends block_list {

    function init() {

        $this->title = get_string('ilp_student_info', 'block_ilp_student_info');

        $this->version = 2008052900;

    }

    function has_config() {

        return true;

    }

    function config_save($data) {
		
		$module = 'project/ilp';
		//addslashes_object($data);
		
        $things_to_allow = array(

            'block_ilp_student_info_allow_per_student_student_text',

            'block_ilp_student_info_allow_per_student_teacher_text',

            'block_ilp_student_info_allow_per_student_shared_text',

            'block_ilp_student_info_allow_per_teacher_student_text',

            'block_ilp_student_info_allow_per_teacher_teacher_text',

            'block_ilp_student_info_allow_per_teacher_shared_text',

            'block_ilp_student_info_allow_per_tutor_student_text',

            'block_ilp_student_info_allow_per_tutor_teacher_text',

            'block_ilp_student_info_allow_per_tutor_shared_text',

        ) ;

        foreach($things_to_allow as $key) { 

            set_config($key, $data->$key, $module);

        }

        $default_texts = array(

            'block_ilp_student_info_default_per_student_student_text',

            'block_ilp_student_info_default_per_student_teacher_text',

            'block_ilp_student_info_default_per_student_shared_text',

            'block_ilp_student_info_default_per_teacher_student_text',

            'block_ilp_student_info_default_per_teacher_teacher_text',

            'block_ilp_student_info_default_per_teacher_shared_text',

            'block_ilp_student_info_default_per_tutor_student_text',

            'block_ilp_student_info_default_per_tutor_teacher_text',

            'block_ilp_student_info_default_per_tutor_shared_text',

        ) ;

        foreach($default_texts as $key) { 

            set_config($key, $data->$key, $module);

        }

        return true;

    }



    function get_content() {



        global $CFG;



        if (empty($this->instance)) {

            $this->content = '';

            return $this->content;

        }



        // the following 3 lines is need to pass _self_test();

        if (empty($this->instance->pageid)) {

            return '';

        }



        $access_isteacher = 0 ;

        $access_isstudent = 0 ;

        $access_isother = 0 ;



        if (!$currentcontext = get_context_instance(CONTEXT_COURSE, $this->instance->pageid)) {

            $access_isother = 1 ;

        } else {

            if (has_capability('block/ilp_student_info:viewclass',$currentcontext)) { // are we the teacher on the course ?

                $access_isteacher = 1 ;

            } elseif (has_capability('block/ilp_student_info:view',$currentcontext)) {  // are we a student on the course ?

                $access_isstudent = 1 ;

            }

        }



        $this->content = new object();

        $this->content->items = array();

        $this->content->icons = array();

        $this->content->footer = '';



        $url = ($access_isstudent) ? $CFG->wwwroot.'/blocks/ilp_student_info/view.php' : $CFG->wwwroot.'/blocks/ilp_student_info/list.php' ;



        if (!$access_isother) {

            $url .= "?courseid=".$this->instance->pageid ;

        }



        $this->content->items[] = '<a title="'.get_string('view','block_ilp_student_info').'" href="'.$url.'">'.(($access_isstudent)?get_string('viewmyilp_student_info','block_ilp_student_info'):get_string('viewilp_student_infos','block_ilp_student_info')).'</a>';

        $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/users.gif" class="icon" alt="" />';

        return $this->content;



    }

}



?>

