<?php

//

// Capability definitions for the assignment module.

//

// The capabilities are loaded into the database table when the module is

// installed or updated. Whenever the capability definitions are updated,

// the module version number should be bumped up.

//

// The system has four possible values for a capability:

// CAP_ALLOW, CAP_PREVENT, CAP_PROHIBIT, and inherit (not set).

//

//

// CAPABILITY NAMING CONVENTION

//

// It is important that capability names are unique. The naming convention

// for capabilities that are specific to modules and blocks is as follows:

//   [mod/block]/<component_name>:<capabilityname>

//

// component_name should be the same as the directory name of the mod or block.

//

// Core moodle capabilities are defined thus:

//    moodle/<capabilityclass>:<capabilityname>

//

// Examples: mod/forum:viewpost

//           block/recent_activity:view

//           moodle/site:deleteuser

//

// The variable name for the capability definitions array follows the format

//   $<componenttype>_<component_name>_capabilities

//

// For the core capabilities, the variable is $moodle_capabilities.





$mod_ilptarget_capabilities = array(



    'mod/ilptarget:view' => array(



        'captype' => 'read',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'student' => CAP_ALLOW,

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),

    'mod/ilptarget:addowntarget' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'guest' => CAP_PROHIBIT,
            'user' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'mod/ilptarget:addtarget' => array(



        'captype' => 'write',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'student' => CAP_ALLOW,

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),

    

    'mod/ilptarget:updatetargetstatus' => array(



        'captype' => 'write',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'student' => CAP_ALLOW,

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),

    'mod/ilptarget:addowncomment' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'guest' => CAP_PROHIBIT,
            'user' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'mod/ilptarget:addcomment' => array(



        'captype' => 'write',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'student' => CAP_ALLOW,

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),



    'mod/ilptarget:viewclass' => array(



        'captype' => 'read',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    )

);



?>

