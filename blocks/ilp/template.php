<?php 
include ('access_context.php'); 

echo '<div class="generalbox" id="ilp-profile-overview">'; 
echo '<h1>';
echo '<a href="'.$CFG->wwwroot.'/user/view.php?'.(($courseid)?'courseid='.$courseid.'&' : '').'id='.$id.'">';
echo fullname($user);
echo '</a>';

if($CFG->ilpconcern_status_per_student == 1){
	echo '<span class="main status-'.$studentstatusnum.'" style="margin-left:20px">'.(($access_isuser)? get_string('mystudentstatus', 'ilpconcern'):get_string('studentstatus', 'ilpconcern')).': '.$thisstudentstatus.'</span>';
}
echo '</h1>';

echo '<table><tr><td>';
print_user_picture($user, (($courseid)?$courseid : 1), $user->picture,100);
echo '</td><td>'.get_string('email').': '.$user->email.'<br />
	 '.get_string('address').': '.$user->address.'<br />
	 '.get_string('phone').': '.$user->phone1.'<br />
	 </td>';
echo '</tr></table>';
echo '</div>';

if($config->ilp_show_student_info == 1) {
	echo '<div class="generalbox" id="ilp-student_info-overview">'; 
	display_ilp_student_info($id,$courseid); 
	echo '</div>';
}

if($config->ilp_show_targets == 1) {
	echo '<div class="generalbox" id="ilp-target-overview">';
	display_ilptarget ($id,$courseid);
	echo '</div>';
}

if($config->ilp_show_concerns == 1) { 
	$i = 1;
	while ($i <= 6){	
		if(eval('return $CFG->ilpconcern_report'.$i.';') == 1) {
		echo '<div class="generalbox" id="ilp-concerns-overview">';
		display_ilpconcern ($id,$courseid,$i);
		echo '</div>';
		}
		$i++;
	}
}

if($config->ilp_show_personal_reports == 1) { 
	echo '<div class="generalbox" id="ilp-personal_report-overview">';
	display_ilp_personal_report($id,$courseid);
	echo '</div>';
} 

if($config->ilp_show_subject_reports == 1) { 
	echo '<div class="generalbox" id="ilp-subject_report-overview">';
	display_ilp_subject_report($id,$courseid);
	echo '</div>';
}

?>