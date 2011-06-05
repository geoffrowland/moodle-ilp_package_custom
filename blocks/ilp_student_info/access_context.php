<?php     

    $userid     = optional_param('id', 0, PARAM_INT);                  // this is required

    $courseid   = optional_param('courseid', SITEID, PARAM_INT);                  // this are required



    if (!$userid) {

        $userid = $USER->id ;

    }



    $sitecontext = get_context_instance(CONTEXT_SYSTEM);



    $usercontext = get_context_instance(CONTEXT_USER, $userid);

    if (!$usercontext) {

       error("User ID is incorrect");

    }



    if ($courseid) {

        if (! $course = get_record('course', 'id', $courseid)) {

            error("Course ID is incorrect");

        }

        if (! $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id)) {

            error("Context ID is incorrect");

        }

    }



    if (has_capability('moodle/legacy:guest', $sitecontext, NULL, false)) {

        error("You are logged in as Guest.");

    }

 

    // ACCESS CONTROL

    $access_isgod = 0 ; 

    $access_isuser = 0 ; 

    $access_isteacher = 0 ; 

    $access_istutor = 0 ;  

    if (has_capability('moodle/site:doanything', $sitecontext)) {  // are we god ?

        $access_isgod = 1 ;

    }

    if ($userid == $USER->id) { // are we the user ourselves ?

        $access_isuser = 1;   

    }

    if(isset($coursecontext)){

      if (has_capability('block/ilp:viewclass',$coursecontext)) { // are we the teacher on the course ?

	 $access_isteacher = 1;

      }

    }

    if (has_capability('block/ilp:view',$usercontext)) { // are we the personal tutor ? 

	 $access_istutor = 1;

    }

    if (!($access_isgod or $access_isuser or $access_isteacher or $access_istutor)) {

        error("insufficient access");

    }



?>

