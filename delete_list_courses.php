<?php
/**
 * Delete courses scheduled to be deleted by CRON
 * Courses to be deleted are added from this URL: https://../moodle/course/delete_old_courses/
 * @author Hernán Darío Arango C. <hernan.arango@yahoo.com>
 * Modified by monitor: Juan Felipe Orozco Escobar <juan.orozco.escobar@correounivalle.edu.co>
 * Version 0.2
 **/

define('CLI_SCRIPT', true);
require("../../config.php");

// 1. Getting all courses to delete from 'mdl_list_courses_delete' table
$sql = "select idcourse from {list_courses_delete}";
$result = $DB->get_records_sql($sql);

//// Deleting process started
$starttime = microtime();
$timenow = time();
mtrace("Cron started at: " . date('r', $timenow). "\n");

// 2. Deleting Moodle courses and registered courses from 'mdl_list_courses_delete' table
foreach ($result as $obj) {
	echo "Deleting course with ID: ". $obj->idcourse . "\n";
	delete_course($obj->idcourse);
	$DB->delete_records('list_courses_delete', array('idcourse' => $obj->idcourse));
	// If time now is >= 4am then stop the cron: leave a gap to fix_course_sortorder() call
	if(intval(date('H')) >= 4) {
		break;
	}
}

// 3. Fixing course category and course sortorder, also verifying category and course parents and paths
fix_course_sortorder();

//// Deleting process completed
mtrace("\n" . 'Cron completed at: ' . date('r', time()) . "\n" . 'Memory used: ' . display_size(memory_get_usage()));
$difftime = microtime_diff($starttime, microtime());
mtrace("Cron took " . $difftime . " seconds to finish.");
