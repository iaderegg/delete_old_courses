<?php

/**
 * Delete courses scheduled to be deleted by CRON
 * Courses to be deleted are added from this URL: https://../moodle/course/delete_old_courses/
 * @author Hernán Darío Arango C. <hernan.arango@yahoo.com>
 * Modified by monitor: Juan Felipe Orozco Escobar <juan.orozco.escobar@correounivalle.edu.co>
 * Modified by monitor: Iader E. Garcia Gomez <iader.garcia@correounivalle.edu.co>
 * Version 0.3
 **/

define('CLI_SCRIPT', true);

ini_set('max_execution_time', 14400);

require(__DIR__ . "../../../config.php");
require(__DIR__ . "/lib/lib_mail.php");

global $DB;

// 1. Getting all courses to delete from 'mdl_list_courses_delete' table
$sql = "select idcourse from {list_courses_delete}";
$result = $DB->get_records_sql($sql);

//// Deleting process started
$starttime = microtime();
$timenow = time();
mtrace("Cron started at: " . date('r', $timenow) . "\n");

// 2. Deleting Moodle courses and registered courses from 'mdl_list_courses_delete' table
$counter = 0;
foreach ($result as $obj) {

    // If time now is >= 2am then stop the cron: leave a gap to fix_course_sortorder() call
    if (intval(date('H')) >= 1 && intval(date('H')) < 4) {
        break;
    } elseif (intval(date('H')) >= 7) {
        break;
    }

    echo "\n" . 'Deleting course with ID: ' . $obj->idcourse . "\n";
    echo 'Started at: ' . date('H:i:s') . "\n";
    echo 'Memory usage: ' . display_size(memory_get_usage()) . "\n";
    delete_course($obj->idcourse);
    echo 'Deleted at: ' . date('H:i:s') . "\n";
    echo 'Memory usage: ' . display_size(memory_get_usage());
    $DB->delete_records('list_courses_delete', array('idcourse' => $obj->idcourse));
    $counter += 1;

    
}

// 3. Fixing course category and course sortorder, also verifying category and course parents and paths
fix_course_sortorder();

$coursesToDelete = $DB->count_records('list_courses_delete');

delete_old_courses_send_email( '66996031' , 'administrador', $coursesToDelete, $counter );
delete_old_courses_send_email( '1144132883' , 'administrador', $coursesToDelete, $counter );

//// Deleting process completed
mtrace("\n" . 'Cron completed at: ' . date('r', time()) . "\n" . 'Memory used: ' . display_size(memory_get_usage()));
$difftime = microtime_diff($starttime, microtime());
mtrace("Cron took " . $difftime . " seconds to finish.");
echo "\nDeleted courses: " . $counter;

