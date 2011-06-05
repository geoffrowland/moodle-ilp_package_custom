<?PHP



    // ULCCGPL 



    require_once('../../config.php');

    require_once('block_ilp_student_info_lib.php');

	$module = 'project/ilp';
	$config = get_config($module);
	

    $reset     = optional_param('reset', 0, PARAM_TEXT);    



    $courseid   = optional_param('courseid', SITEID, PARAM_INT);

    $teacherid  = optional_param('teacherid', 0, PARAM_INT);



    $userid     = required_param('userid', PARAM_INT);     

    $per        = required_param('per', PARAM_ALPHA);     

    $which      = required_param('which', PARAM_ALPHA);  



    $id         = optional_param('id', 0, PARAM_INT);   



    $newtext    = optional_param('text', 0, PARAM_RAW);



    if ($courseid) {

        if (! $course = get_record('course', 'id', $courseid)) {

            error("Course ID is incorrect");

        }

        if (! $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id)) {

            error("Context ID is incorrect");

        }

        require_login($course);

    }





    $sitecontext = get_context_instance(CONTEXT_SYSTEM);



    $usercontext = get_context_instance(CONTEXT_USER, $userid);

    if (!$usercontext) {

        error("bad user");

    }



    // ACCESS CONTROL

    $access_isgod = 0 ; 

    $access_isuser = 0 ; 

    $access_isteacher = 0 ; 

    $access_istutor = 0 ; 

    if (has_capability('moodle/site:doanything',$sitecontext)) {  // are we god ?

        $access_isgod = 1 ;

    }

    if ($userid == $USER->id and ($which == 'shared' or $which == 'student')) { // are we the user ourselves ?

        $access_isuser = 1 ;

    }

    if ($courseid) {

        if (has_capability('block/ilp_student_info:viewclass',$coursecontext) and ($which == 'shared' or $which == 'teacher')) { // are we the teacher on the course ?

            $access_isteacher = 1 ;

        }

    }

    if (has_capability('block/ilp_student_info:view',$usercontext) and ($which == 'shared' or $which == 'teacher')) { // are we the personal tutor ? 

        $access_istutor = 1 ;

    }

    if (!($access_isgod or $access_isuser or $access_isteacher or $access_istutor)) {

        error("insufficient access");

    }



	
    // UPDATE OR ADD

    if ($newtext and !$reset) { // update or add - what if it's empty ?



        $text = new Object ;

        $text->text = $newtext ;

        $text->lastchanged_userid = $USER->id ;

        $text->lastchanged_datetime = time() ;



        if ($id) { // update

            $text->id = $id ;

            update_record('ilp_student_info_text', addslashes_object($text));

        } else { // add

            $id = insert_record('ilp_student_info_text', addslashes_object($text), true);



            // get parent if there is one... 

            $sql = "SELECT * FROM ilp_student_info_per_$per WHERE student_userid = $userid ";

            if($courseid) {

                $sql .= "AND courseid = $courseid " ;

            }

            if($teacherid) {

                $sql .= "AND teacher_userid = $teacherid " ;

            }

            $parent = get_record_sql($sql,0,0);



            if ($parent) { // there is so update it.

                $which .= '_textid' ;

                $parent->$which = $id ;

                update_record('ilp_student_info_per_'.$per, $parent);

            } else { // create new parent for the new child

                $parent = new Object ;

                $parent->student_userid = $userid ;

                if($courseid) {

                    $parent->courseid = $courseid ;

                }

                if($teacherid) {

                    $parent->teacher_userid = $teacherid ;

                }

                $which .= '_textid' ;

                $parent->$which = $id ;

                insert_record('ilp_student_info_per_'.$per, $parent, true);

            }

        }

        print_header();



?>

<script language="JavaScript">

window.opener.location.href = window.opener.location.href;

window.close();

</script>

<?php

        // refresh parent and close popup

        exit;

    }



    $text = block_ilp_student_info_get_text($userid,$teacherid,$courseid,$per,$which) ;

    if ($reset) {

        $name = "block_ilp_student_info_default_per_{$per}_{$which}_text";

        $text->text = $config->$name ;

    }



/// Print simple headers for popup window

    print_header();

?>



<form method="POST" action="edit_text.php" >

<input type="hidden" name="per" value="<?php echo $per ?>" />

<input type="hidden" name="which" value="<?php echo $which ?>" />

<input type="hidden" name="userid" value="<?php echo $userid ?>" />

<?php if ($courseid) { ?>

<input type="hidden" name="courseid" value="<?php echo $courseid ?>" />

<?php } ?>

<?php if ($teacherid) { ?>

<input type="hidden" name="teacherid" value="<?php echo $teacherid ?>" />

<?php } ?>

<?php if ($id) { ?>

<input type="hidden" name="id" value="<?php echo $id ?>" />

<?php } ?>



<?php

    print_textarea(true, 23, 50, 0, 0, 'text', stripslashes($text->text));

?>

<br />

<input type="submit" name="reset" value="Reset" />

<input type="button" value="Cancel" onClick="window.close()" />

<input type="submit" name="submit" value="Save" />

</form>

<?php 

    use_html_editor(); 

?>



