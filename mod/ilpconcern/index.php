<?php 



/*

 * @copyright &copy; 2007 University of London Computer Centre

 * @author http://www.ulcc.ac.uk, http://moodle.ulcc.ac.uk

 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License

 * @package ILP

 * @version 1.0

 */



/// Replace concerns with the name of your module



    require_once("../../config.php");

    require_once("lib.php");



    $id = required_param('id', PARAM_INT);   // course



    if (! $course = get_record("course", "id", $id)) {

        error("Course ID is incorrect");

    }



    require_login($course->id);



    add_to_log($course->id, "concerns", "view all", "index.php?id=$course->id", "");





/// Get all required stringsconcerns



    $strconcerns = get_string("modulenameplural", "ilpconcern");

    $strconcerns  = get_string("modulename", "ilpconcern");





/// Print the header



    if ($course->category) {

        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";

    } else {

        $navigation = '';

    }



    print_header("$course->shortname: $strconcerns", "$course->fullname", "$navigation $strconcerns", "", "", true, "", navmenu($course));



/// Get all the appropriate data



    if (! $concerns = get_all_instances_in_course("ilpconcern", $course)) {

        notice("There are no concerns", "../../course/view.php?id=$course->id");

        die;

    }



/// Print the list of instances (your module will probably extend this)



    $timenow = time();

    $strname  = get_string("name");

    $strweek  = get_string("week");

    $strtopic  = get_string("topic");



    if ($course->format == "weeks") {

        $table->head  = array ($strweek, $strname);

        $table->align = array ("center", "left");

    } else if ($course->format == "topics") {

        $table->head  = array ($strtopic, $strname);

        $table->align = array ("center", "left", "left", "left");

    } else {

        $table->head  = array ($strname);

        $table->align = array ("left", "left", "left");

    }



    foreach ($concerns as $concerns) {

        if (!$concerns->visible) {

            //Show dimmed if the mod is hidden

            $link = "<a class=\"dimmed\" href=\"view.php?id=$concerns->coursemodule\">$concerns->name</a>";

        } else {

            //Show normal if the mod is visible

            $link = "<a href=\"view.php?id=$concerns->coursemodule\">$concerns->name</a>";

        }



        if ($course->format == "weeks" or $course->format == "topics") {

            $table->data[] = array ($concerns->section, $link);

        } else {

            $table->data[] = array ($link);

        }

    }



    echo "<br />";



    print_table($table);



/// Finish the page



    print_footer($course);



?>

