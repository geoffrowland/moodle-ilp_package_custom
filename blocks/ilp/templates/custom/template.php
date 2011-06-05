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

echo '<table width="100%"><tr><td width="100px" valign="top">';
print_user_picture($user, (($courseid)?$courseid : 1), $user->picture,100);
echo '</td><td valign="top"><b>Email:</b> <a href="mailto:'.$user->email.'">'.$user->email.'</a><br />
     <b>'.get_string('address').':</b> '.$user->address.'<br />
	  <b>'.get_string('phone').':</b> '.$user->phone1.'<br />
	  </td>
     <td valign="top">';
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
//Check if inside or outside College, Lufton, Salisbury or GKN
        
        $addr = (getremoteaddr());
        $bits = explode('.', $addr); 

        // check to make sure it's not an internal (i.e College) address.
        // the following are reserved for private LANs...
        // 10.0.0.0 - 10.255.255.255
        // 172.16.0.0 - 172.31.255.255
        // 192.168.0.0 - 192.168.255.255
        // 169.254.0.0 -169.254.255.255
        // Lufton
        // 81.174.164.54
        //
        // Shaftesbury
        // 80.229.45.159
        //
        // GKN
        //
        // 80.229.39.254
        if (
        ($bits[0] == 10)
        || ($bits[0] == 172 && $bits[1] >= 16 && $bits[1] <= 31)
        || ($bits[0] == 192 && $bits[1] == 168)
        || ($bits[0] == 169 && $bits[1] == 254)
        || ($bits[0] == 81 && $bits[1] == 174 && $bits[2] == 164)
        || ($bits[0] == 80 && $bits[1] == 229 && $bits[2] == 45)
        || ($bits[0] == 80 && $bits[1] == 229 && $bits[2] == 39)
        ) {
        echo '<a href="http://bksb.campus.yeovil.net/bksb_Portal/?Reference='.$user->username.'&FirstName='.$user->firstname.'&LastName='.$user->lastname.'"); title="BKSB: Initial Assessment Results" target="_blank">';
        }else{
        echo '<a href="" title="BKSB: Assessment server not available from outside College">';
        };
echo 'Initial Assessment Results</a><br /><br />'; 
?>
        <?php require('database_lib.php'); ?>
        <?php
        while (!$rs->EOF) {
        //print_r($rs->fields);
        ////echo ('Subject: ' . $rs->fields['Subject'] . ' <br />');
        echo ('<b>Result:</b> ' . $rs->fields['Result'] . ' <br />');
        //echo ('Result notes: ' . $rs->fields['Result Notes'] . ' <br />');
        echo ('<b>Date:</b> ' . $rs->fields['DateCompleted'] . ' <br /><br />');
        $rs->MoveNext();
        };
echo '<span style="font-size: smaller">If you have completed <i>more than one</i> initial assessment for a particular subject, you are working at the level indicated by your <i>best</i> result</span';
echo '</td>';
echo '<td valign="top">';
echo '</td>';
echo '<td valign="top"><a href="http://moodle.yeovil.ac.uk/grade/report/overview/index.php?id='.$courseid.'&userid='.$user->id.'"><img title="Grades Overview" src="http://moodle.yeovil.ac.uk/pix/i/grades.gif"></a> <a href="http://moodle.yeovil.ac.uk/grade/report/overview/index.php?id='.$courseid.'&userid='.$user->id.'">Grades Overview</a></td>';
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
   $i = 5;
	while ($i <= 5){
		$ilpconcernviewreport ='mod/ilpconcern:viewreport'.$i.'';
		if(has_capability($ilpconcernviewreport, $context)){	
		if(eval('return $CFG->ilpconcern_report'.$i.';') == 1) {
		echo '<div class="generalbox" id="ilp-concerns-overview">';
		display_ilpconcern ($id,$courseid,$i);
		echo '</div>';
		}
		}
		$i++;
	} 
	$i = 2;
	while ($i <= 4){
		$ilpconcernviewreport ='mod/ilpconcern:viewreport'.$i.'';
		if(has_capability($ilpconcernviewreport, $context)){	
		if(eval('return $CFG->ilpconcern_report'.$i.';') == 1) {
		echo '<div class="generalbox" id="ilp-concerns-overview">';
		display_ilpconcern ($id,$courseid,$i);
		echo '</div>';
		}
		}
		$i++;
	}
	$i = 6;
   while ($i <= 6){
		$ilpconcernviewreport ='mod/ilpconcern:viewreport'.$i.'';
		if(has_capability($ilpconcernviewreport, $context)){	
		if(eval('return $CFG->ilpconcern_report'.$i.';') == 1) {
		echo '<div class="generalbox" id="ilp-concerns-overview">';
		display_ilpconcern ($id,$courseid,$i);
		echo '</div>';
		}
		}
		$i++;
	} 
	$i = 1;
	while ($i <= 1){
		$ilpconcernviewreport ='mod/ilpconcern:viewreport'.$i.'';
		if(has_capability($ilpconcernviewreport, $context)){	
		if(eval('return $CFG->ilpconcern_report'.$i.';') == 1) {
		echo '<div class="generalbox" id="ilp-concerns-overview">';
		display_ilpconcern ($id,$courseid,$i);
		echo '</div>';
		}
		}
		$i++;
	}	
	
}
//Progress (Subject) Report
if($config->ilp_show_subject_reports == 1) { 
	echo '<div class="generalbox" id="ilp-subject_report-overview">';
	display_ilp_subject_report($id,$courseid);
	echo '</div>';
}
//Programme Review
if($config->ilp_show_personal_reports == 1) { 
	echo '<div class="generalbox" id="ilp-personal_report-overview">';
	display_ilp_personal_report($id,$courseid);
	echo '</div>';
} 
?>