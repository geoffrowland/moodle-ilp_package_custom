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

function xmldb_block_ilp_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;
	
    if ($result && $oldversion < 2008052900) {

    /// Define table ilp_block to be renamed to ilp
        $table = new XMLDBTable('block_ilp');

    /// Launch rename table for ilp_block
        $result = $result && rename_table($table, 'ilp_block');
    }
	
	if ($result && $oldversion < 2008053000) {

    /// Define table ilp_module_template to be created
        $table = new XMLDBTable('ilp_module_template');

    /// Adding fields to table ilp_module_template
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('module', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null, null, null);
		$table->addFieldInfo('status', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('text', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table ilp_module_template
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Launch create table for ilp_module_template
        $result = $result && create_table($table);
    }

    return $result;
	
	
}

?>
