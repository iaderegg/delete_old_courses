<?php
/**
 * Insert/Delete courses to 'mdl_list_courses_delete' table
 * @author Hernán Darío Arango C. <hernan.arango@yahoo.com>
 * Modified by Monitor: Juan Felipe Orozco Escobar <juan.orozco.escobar@correounivalle.edu.co>
 * Version: 0.2
 **/

require("../../config.php");

$idCurso = optional_param('id', 0, PARAM_INT);
$course = $DB->get_record('list_courses_delete', array('idcourse' => $idCurso), 'id');

if($course){

	$sql = "DELETE FROM mdl_list_courses_delete where idcourse=?";
	$params = array($idCurso);

	if($DB->execute($sql, $params)){
		return "paso";
	}
	else{
		return "fallo";
	}
} else {

	$sql = "INSERT INTO mdl_list_courses_delete(idcourse) VALUES (?)";
	$params = array($idCurso);

	if($DB->execute($sql, $params)){
		return "paso";
	}	else {
		return "fallo";
	}
}
