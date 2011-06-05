<?php  //$Id: upgrade.php,v 1.1.2.1 2008/01/04 18:32:36 mchurch Exp $

// This file keeps track of upgrades to 
// the file manager block.
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the functions defined in lib/ddllib.php

function xmldb_block_ilp_student_info_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

    if ($result && $oldversion < 2008052900) {

    /// Define table ilp_block to be renamed to ilp
        $table1 = new XMLDBTable('block_ilp_student_info_per_student');
		$table2 = new XMLDBTable('block_ilp_student_info_per_teacher');
		$table3 = new XMLDBTable('block_ilp_student_info_per_tutor');
		$table4 = new XMLDBTable('block_ilp_student_info_text');

    /// Launch rename table for ilp_block
        $result = $result && rename_table($table1, 'ilp_student_info_per_student') && rename_table($table2, 'ilp_student_info_per_teacher') && rename_table($table3, 'ilp_student_info_per_tutor') && rename_table($table4, 'ilp_student_info_text');
    }

    return $result;
}

?>
