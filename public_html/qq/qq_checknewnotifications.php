<?php
// qq_checknewnotifications.php
require_once "qq_funcs.php";

if( !isset($_POST["uid"]) )
	die("-2");

$uid = $_POST["uid"];

if($uid == -1 || !is_numeric($uid))
	die("-2");

if(checkNewNotifications($uid) == 1 || checkNewPendings($uid) == 1 || checkNewMessages($uid) == 1)
	echo "1";
else
	echo "0";
?>