<?php
//error_reporting(E_ALL); 
$db = new mysqli('localhost','778276','anaaremere1','778276');
if($db->connect_errno){
	echo $db->connect_error.'<br>';
	die('Sorry, I messed up.');
}
?>