<?php

function block_ilp_student_info_edit_button($uid,$tid,$cid,$per,$which,$id) {

    $url = "/blocks/ilp_student_info/edit_text.php?userid=$uid" ;

    $bname = "edit_button" ;

    if ($tid) {

        $url .= '&teacherid='.$tid ;

        $bname .= "_$tid" ;

    }

    if ($cid) {

        $url .= '&courseid='.$cid ;

        $bname .= "_$cid" ;

    }

    if ($per) {

        $url .= '&per='.$per ;

        $bname .= "_$per" ;

    }

    if ($which) {

        $url .= '&which='.$which ;

        $bname .= "_$which" ;

    }

    if ($id) {

        $url .= '&id='.$id ;

    }



    $button = '

<input name="'.$bname.'" value="'.get_string('edit').'" type="button" title="'.get_string('edit').'" onclick="return openpopup(\''.$url.'\', \'popup\', \'menubar=0,location=0,scrollbars,resizable,width=750,height=500\', 0);" id="id_'.$bname.'" />

    ' ;



    //$button = '<a class="block_ilp_student_info_edit_link" target="_blank" href="'.$url.'">'.get_string('edit').'</a>' ;



    return $button ;

}





// returns text or return default

function block_ilp_student_info_get_text($uid,$tid,$cid,$per,$which) {



    global $CFG ;
	$module = 'project/ilp';
	$config = get_config($module);


    $sql =  "

SELECT t.*

FROM

{$CFG->prefix}ilp_student_info_text t,

{$CFG->prefix}ilp_student_info_per_$per

WHERE

{$which}_textid = t.id

AND student_userid = ".$uid."

    " ;



    if ($per == 'student') {

    }



    if ($per == 'teacher') {

        $sql .= "

AND teacher_userid = ".$tid."

AND courseid = ".$cid."

        " ;

    }

    if ($per == 'tutor') {

        $sql .= "

AND teacher_userid = ".$tid."

        " ;

    }



    $text = get_record_sql($sql) ;

    if ($text) {

        return $text;

    } else {

        $name = "block_ilp_student_info_default_per_{$per}_{$which}_text";

        $text->text = stripslashes($config->$name);

        $text->id = 0 ;

        return $text ;

    }

};





?>

