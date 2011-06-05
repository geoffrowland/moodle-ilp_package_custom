<?php

    if($CFG->block_ilp_student_info_allow_per_student_teacher_text == 1) {

        $text = block_ilp_student_info_get_text($user->id,0,$course->id,'student','teacher') ;

?>

<div class="block_ilp_student_info_text"><?php echo $text->text ?></div>

<?php

        if($access_isteacher or $access_istutor or $access_isgod) {

            echo block_ilp_student_info_edit_button($user->id,0,$course->id,'student','teacher',$text->id) ;

        }

?>

<?php

    }



    if($CFG->block_ilp_student_info_allow_per_student_student_text == 1) {

        $text = block_ilp_student_info_get_text($user->id,0,$course->id,'student','student') ;

?>

<div class="block_ilp_student_info_text"><?php echo $text->text ?></div>

<?php

        if($access_isuser or $access_isgod) {

            echo block_ilp_student_info_edit_button($user->id,0,$course->id,'student','student',$text->id) ;

        }

?>

<?php

    }



    if($CFG->block_ilp_student_info_allow_per_student_shared_text == 1) {

        $text = block_ilp_student_info_get_text($user->id,0,$course->id,'student','shared') ;

?>

<div class="block_ilp_student_info_text"><?php echo $text->text ?></div>

<?php

        if($access_isuser or $access_isteacher or $access_istutor or $access_isgod) {

            echo block_ilp_student_info_edit_button($user->id,0,$course->id,'student','shared',$text->id) ;

        }

?>

<?php

    }



    // have they any personal tutors ?

    $context = get_context_instance(CONTEXT_USER, $user->id);

    $tutors = get_users_by_capability($context, 'moodle/user:viewuseractivitiesreport', 'u.*', 'u.lastname ASC',

                                         '', '', '', '', false);

    if ($tutors) {

        print_heading("Personal Tutors");

        foreach ($tutors as $tutor) {

            print_heading("$tutor->firstname $tutor->lastname");

            if($CFG->block_ilp_student_info_allow_per_tutor_teacher_text == 1) {

                $text = block_ilp_student_info_get_text($user->id,$tutor->id,$course->id,'tutor','teacher') ;

?>

<div class="block_ilp_student_info_text"><?php echo $text->text ?></div>

<?php

                if($tutor->id == $USER->id or $access_isgod) {

                    echo block_ilp_student_info_edit_button($user->id,$tutor->id,$course->id,'tutor','teacher',$text->id) ;

                }

            }



            if($CFG->block_ilp_student_info_allow_per_tutor_student_text == 1) {

                $text = block_ilp_student_info_get_text($user->id,$tutor->id,$course->id,'tutor','student') ;

?>

<div class="block_ilp_student_info_text"><?php echo $text->text ?></div>

<?php

                if($access_isuser or $access_isgod) {

                    echo block_ilp_student_info_edit_button($user->id,$tutor->id,$course->id,'tutor','student',$text->id) ;

                }

            }



            if($CFG->block_ilp_student_info_allow_per_tutor_shared_text == 1) {

                $text = block_ilp_student_info_get_text($user->id,$tutor->id,$course->id,'tutor','shared') ;

?>

<div class="block_ilp_student_info_text"><?php echo $text->text ?></div>

<?php

                if($access_isuser or $tutor->id == $USER->id or $access_isgod) {

                    echo block_ilp_student_info_edit_button($user->id,$tutor->id,$course->id,'tutor','shared',$text->id) ;

                }

            }

        }

    }

    unset($tutors);



    // what courses are they on ?

    $courses = get_my_courses($user->id);

    foreach ($courses as $course) {

        print_heading("$course->shortname ($course->fullname)");

        // who teachers with it ?

        $context = get_context_instance(CONTEXT_COURSE, $course->id);

        $teachers = get_users_by_capability($context, 'moodle/course:update', 'u.id,u.firstname,u.lastname', 'u.lastname ASC',

                                         '', '', '', '', false);

        foreach($teachers as $teacher) {

            print_heading("$teacher->firstname $teacher->lastname");



            if($CFG->block_ilp_student_info_allow_per_teacher_teacher_text == 1) {

                $text = block_ilp_student_info_get_text($user->id,$teacher->id,$course->id,'teacher','teacher') ;

?>

<div class="block_ilp_student_info_text"><?php echo $text->text ?></div>

<?php

                if($teacher->id == $USER->id or $access_isgod) {

                    echo block_ilp_student_info_edit_button($user->id,$teacher->id,$course->id,'teacher','teacher',$text->id) ;

                }

            }



            if($CFG->block_ilp_student_info_allow_per_teacher_student_text == 1) {

                $text = block_ilp_student_info_get_text($user->id,$teacher->id,$course->id,'teacher','student') ;

?>

<div class="block_ilp_student_info_text"><?php echo $text->text ?></div>

<?php

                if($access_isuser or $access_isgod) {

                    echo block_ilp_student_info_edit_button($user->id,$teacher->id,$course->id,'teacher','student',$text->id) ;

                }

            }



            if($CFG->block_ilp_student_info_allow_per_teacher_shared_text == 1) {

                $text = block_ilp_student_info_get_text($user->id,$teacher->id,$course->id,'teacher','shared') ;

?>

<div class="block_ilp_student_info_text"><?php echo $text->text ?></div>

<?php

                if($access_isuser or $teacher->id == $USER->id or $access_isgod) {

                    echo block_ilp_student_info_edit_button($user->id,$teacher->id,$course->id,'teacher','shared',$text->id) ;

                }

            }

        }

        unset($teachers);

    }

?>

