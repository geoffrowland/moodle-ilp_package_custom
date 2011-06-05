<?php // library file to get the bksb data from CIS databases
//
// This first section calls an entire table which displays in the
// right space in the ILP
//
$db = ADONewConnection('mssql'); # eg. 'mysql' or 'oci8'
//
//$db->debug = true;
//
$db->Connect('10.2.0.53:1433', 'Moodle', 'password', 'BKSB_Mini');
//$db->Connect('server_name', 'username', 'password', 'database_name');
$db->SetFetchMode(ADODB_FETCH_ASSOC);
$rs = $db->Execute('select Session_id, Result, DateCompleted from bksb_IAResults where UserName="'.$user->username.'"');
//$rs = $db->Execute('select Result, DateCompleted from bksb_IAResults where UserName="mark.timmins"');
//
//$ss = $db->Execute('select Session_id from bksb_Assessments where UserName="'.$user->username.'"');
//print_r($ss);
?>
