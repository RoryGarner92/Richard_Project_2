<?php
include "phpFuncs.php";

date_default_timezone_set('GMT');
//Page to logout. The session is also destroyed
include("session.php");

$logUser = $_SESSION['login_user'];
$logUserAgent = $_SERVER['HTTP_USER_AGENT'];
$timeStamp = date("F j, Y, g:i a");
$logIP = $_SERVER['REMOTE_ADDR'];
$label = "Log Out Successful";
$query1 = " ";
$query2 = " ";
$query3 = " ";
$query4 = " ";

session_regenerate_id();
// Finally, destroy the session.
unset($_SESSION["PreAuthSess"]);
   
   if(session_destroy()) {
	   
		$test = normalLog($label,$logUser,$logUserAgent,$timeStamp,$query1,$query2,$query3,$query4,$logIP);
	   
    	header("Location: Login.php");
   }
?>
