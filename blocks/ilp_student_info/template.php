<?php 
include ('access_context.php'); 

// print some personal info about student.

//echo '<span class="noprint">'.print_heading("$user->firstname $user->lastname").'</span>';

if($config->ilp_show_student_info == '1' && ($view == 'info' || $view == 'all')) {
    echo '<div class="generalbox" id="ilp-student_info-overview">'; 
    display_ilp_student_info($user->id,$courseid); 
    echo '</div>';
}

if($config->ilp_show_personal_reports == 1 && ($view == 'personal' || $view == 'all')) { 
    echo '<div class="generalbox" id="ilp-personal_report-overview">';
    display_ilp_personal_report($user->id,$courseid);
    echo '</div>';
}
 
if($config->ilp_show_subject_reports == 1 && ($view == 'subject' || $view == 'all')) {
    echo '<div class="generalbox" id="ilp-subject_report-overview">';
    display_ilp_subject_report($user->id,$courseid);
    echo '</div>';  
}

?>