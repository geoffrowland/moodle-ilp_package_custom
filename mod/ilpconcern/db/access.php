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





$mod_ilpconcern_capabilities = array(



    'mod/ilpconcern:view' => array(



        'captype' => 'read',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'student' => CAP_ALLOW,

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),
    
    /////////////////////////////////////////////////////////////    
    
    'mod/ilpconcern:viewreport1' => array(



        'captype' => 'read',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'student' => CAP_ALLOW,

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),

        'mod/ilpconcern:viewreport2' => array(



        'captype' => 'read',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'student' => CAP_ALLOW,

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),

    'mod/ilpconcern:viewreport3' => array(



        'captype' => 'read',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'student' => CAP_ALLOW,

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),
    
    'mod/ilpconcern:viewreport4' => array(



        'captype' => 'read',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'student' => CAP_ALLOW,

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),
    
        'mod/ilpconcern:viewreport5' => array(



        'captype' => 'read',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'student' => CAP_ALLOW,

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),
    
        'mod/ilpconcern:viewreport6' => array(



        'captype' => 'read',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'student' => CAP_ALLOW,

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),
    /////////////////////////////////////////////////////////////
    'mod/ilpconcern:edit' => array(



        'captype' => 'write',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'student' => CAP_ALLOW,

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),    
    
    'mod/ilpconcern:editreport1' => array(



        'captype' => 'write',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'student' => CAP_ALLOW,

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),

        'mod/ilpconcern:editreport2' => array(



        'captype' => 'write',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'student' => CAP_ALLOW,

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),

    'mod/ilpconcern:editreport3' => array(



        'captype' => 'write',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'student' => CAP_ALLOW,

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),
    
    'mod/ilpconcern:editreport4' => array(



        'captype' => 'write',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'student' => CAP_ALLOW,

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),
    
        'mod/ilpconcern:editreport5' => array(



        'captype' => 'write',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'student' => CAP_ALLOW,

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),
    
        'mod/ilpconcern:editreport6' => array(



        'captype' => 'write',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'student' => CAP_ALLOW,

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),
        
///////////////////////////////////////////////////////////    

    'mod/ilpconcern:addownreport1' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'guest' => CAP_PROHIBIT,
            'admin' => CAP_ALLOW
        )
    ),

	'mod/ilpconcern:addownreport2' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'guest' => CAP_PROHIBIT,
            'admin' => CAP_ALLOW
        )
    ),

	'mod/ilpconcern:addownreport3' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'guest' => CAP_PROHIBIT,
            'admin' => CAP_ALLOW
        )
    ),

    'mod/ilpconcern:addownreport4' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'guest' => CAP_PROHIBIT,
            'admin' => CAP_ALLOW
        )
    ),
    
        'mod/ilpconcern:addownreport5' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'guest' => CAP_PROHIBIT,
            'admin' => CAP_ALLOW
        )
    ),
    
        'mod/ilpconcern:addownreport6' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'guest' => CAP_PROHIBIT,
            'admin' => CAP_ALLOW
        )
    ),

    'mod/ilpconcern:addreport1' => array(



        'captype' => 'write',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),

	'mod/ilpconcern:addreport2' => array(



        'captype' => 'write',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),

	'mod/ilpconcern:addreport3' => array(



        'captype' => 'write',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),

    'mod/ilpconcern:addreport4' => array(



        'captype' => 'write',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),

    'mod/ilpconcern:addreport5' => array(



        'captype' => 'write',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),
    
        'mod/ilpconcern:addreport6' => array(



        'captype' => 'write',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),

    'mod/ilpconcern:updateconcernstatus' => array(



        'captype' => 'write',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),

    'mod/ilpconcern:updatestudentstatus' => array(



        'captype' => 'write',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),

    'mod/ilpconcern:addowncomment' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'guest' => CAP_PROHIBIT,
            'user' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'mod/ilpconcern:addcomment' => array(



        'captype' => 'write',

        'contextlevel' => CONTEXT_MODULE,

        'legacy' => array(

            'student' => CAP_ALLOW,

            'teacher' => CAP_ALLOW,

            'editingteacher' => CAP_ALLOW,

            'admin' => CAP_ALLOW

        )

    ),



    'mod/ilpconcern:viewclass' => array(



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

