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

require(__DIR__."../../../config.php");

// 1. Getting all courses to delete from 'mdl_list_courses_delete' table
$sql = "select idcourse from {list_courses_delete}";
$result = $DB->get_records_sql($sql);

//// Deleting process started
$starttime = microtime();
$timenow = time();
mtrace("Cron started at: " . date('r', $timenow). "\n");

// 2. Deleting Moodle courses and registered courses from 'mdl_list_courses_delete' table
$counter = 0;
foreach ($result as $obj) {
	echo "\n" . 'Deleting course with ID: ' . $obj->idcourse . "\n";
	echo 'Started at: ' . date('H:i:s') . "\n";
	echo 'Memory usage: ' . display_size(memory_get_usage()) . "\n";
	delete_course($obj->idcourse);
	echo 'Deleted at: ' . date('H:i:s') . "\n";
	echo 'Memory usage: ' . display_size(memory_get_usage());
	$DB->delete_records('list_courses_delete', array('idcourse' => $obj->idcourse));
	$counter += 1;

	// If time now is >= 2am then stop the cron: leave a gap to fix_course_sortorder() call
	if(intval(date('H')) >= 1 && intval(date('H')) < 5 ) {

		global $DB;

		$coursesToDelete = $DB->count_records('list_courses_delete');

		#If the number of courses to be deleted is greater than zero, send an email notification
		if(intval($coursesToDelete) > 0) {
			$fromUser = core_user::get_user_by_username('administrador', 
                                            'id, 
                                            firstname, 
                                            lastname, 
                                            username, 
                                            email, 
                                            maildisplay, 
                                            mailformat,
                                            firstnamephonetic,
                                            lastnamephonetic,
                                            middlename,
                                            alternatename');
			$toUser1 = core_user::get_user_by_username('66996031', 
														'id, 
														firstname, 
														lastname, 
														username, 
														email, 
														maildisplay, 
														mailformat,
														firstnamephonetic,
														lastnamephonetic,
														middlename,
														alternatename');
			$toUser2 = core_user::get_user_by_username('1144132883', 
														'id, 
														firstname, 
														lastname, 
														username, 
														email, 
														maildisplay, 
														mailformat,
														firstnamephonetic,
														lastnamephonetic,
														middlename,
														alternatename');

			$subject = "Notificación sobre cursos pendientes por borrar en el Campus Virtual";

			$textToSendHtml = "El módulo de eliminación de cursos ha detectado que el día de hoy quedan cursos pendientes por borrar.<br><br>";
			$textToSendHtml .= "Cantidad de cursos pendientes: ".$coursesToDelete."<br><br>";
			$textToSendHtml .= "Este mensaje ha sido generado automáticamente, por favor no responda a este mensaje.";

			$textToSend = html_to_text($textToSendHtml);

			$resultSendMessage1 = email_to_user($toUser1, $fromUser, $subject, $textToSend, $textToSendHtml, ", ", true);
			$resultSendMessage2 = email_to_user($toUser2, $fromUser, $subject, $textToSend, $textToSendHtml, ", ", true);
		}

		break;
	} elseif (intval(date('H')) >= 7) {
		global $DB;

		$coursesToDelete = $DB->count_records('list_courses_delete');

		#If the number of courses to be deleted is greater than zero, send an email notification
		if(intval($coursesToDelete) > 0) {
			$fromUser = core_user::get_user_by_username('administrador', 
                                            'id, 
                                            firstname, 
                                            lastname, 
                                            username, 
                                            email, 
                                            maildisplay, 
                                            mailformat,
                                            firstnamephonetic,
                                            lastnamephonetic,
                                            middlename,
                                            alternatename');
			$toUser1 = core_user::get_user_by_username('66996031', 
														'id, 
														firstname, 
														lastname, 
														username, 
														email, 
														maildisplay, 
														mailformat,
														firstnamephonetic,
														lastnamephonetic,
														middlename,
														alternatename');
			$toUser2 = core_user::get_user_by_username('1144132883', 
														'id, 
														firstname, 
														lastname, 
														username, 
														email, 
														maildisplay, 
														mailformat,
														firstnamephonetic,
														lastnamephonetic,
														middlename,
														alternatename');

			$subject = "Notificación sobre cursos pendientes por borrar en el Campus Virtual";

			$textToSendHtml = "El módulo de eliminación de cursos ha detectado que el día de hoy quedan cursos pendientes por borrar.<br><br>";
			$textToSendHtml .= "Cantidad de cursos pendientes: ".$coursesToDelete."<br><br>";
			$textToSendHtml .= "Este mensaje ha sido generado automáticamente, por favor no responda a este mensaje.";

			$textToSend = html_to_text($textToSendHtml);

			$resultSendMessage1 = email_to_user($toUser1, $fromUser, $subject, $textToSend, $textToSendHtml, ", ", true);
			$resultSendMessage2 = email_to_user($toUser2, $fromUser, $subject, $textToSend, $textToSendHtml, ", ", true);
		}

		break;
	}
}

// 3. Fixing course category and course sortorder, also verifying category and course parents and paths
fix_course_sortorder();

//// Deleting process completed
mtrace("\n" . 'Cron completed at: ' . date('r', time()) . "\n" . 'Memory used: ' . display_size(memory_get_usage()));
$difftime = microtime_diff($starttime, microtime());
mtrace("Cron took " . $difftime . " seconds to finish.");
echo "\nDeleted courses: " .$counter;
