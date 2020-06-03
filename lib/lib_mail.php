<?php

/**
 * Lib for email messages.
 * @author Iader E. Garcia Gomez <iader.garcia@correounivalle.edu.co>
 * Version: 0.1
 **/

function delete_old_courses_send_email( $usernameTo, $usernameFrom, $coursesToDelete, $coursesDeleted) {

    $fromUser = core_user::get_user_by_username(
                                        $usernameFrom,
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
                                        alternatename'
                                    );

    $toUser = core_user::get_user_by_username(
                                        $usernameTo,
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
                                        alternatename'
                                    );

    
    $subject = "Notificación sobre cursos pendientes por borrar en el Campus Virtual";

    $textToSendHtml = "El módulo de eliminación de cursos ha detectado que el día de hoy quedan cursos pendientes por borrar.<br><br>";
    $textToSendHtml .= "Cantidad de cursos pendientes: " . $coursesToDelete . "<br>";
    $textToSendHtml .= "Cantidad de cursos borrados: ". $coursesDeleted ."<br><br>";
    $textToSendHtml .= "Este mensaje ha sido generado automáticamente, por favor no responda a este mensaje.";

    $textToSend = html_to_text($textToSendHtml);

    echo $textToSend;

    $completeFilePath = "/home/admincampus/";

    if (intval(date('H')) >= 1 && intval(date('H')) < 4) {
        $nameFile = 'log_delete_courses_0000.log';
    } elseif (intval(date('H')) >= 7) {
        $nameFile = 'log_delete_courses_0400.log';
    }

    $resultSendMessage = email_to_user($toUser, $fromUser, $subject, $textToSend, $textToSendHtml, $completeFilePath, $nameFile, true);
}
