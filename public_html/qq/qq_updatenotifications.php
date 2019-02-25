<?php
// qq_updatenotifications.php
require_once "qq_funcs.php";

if( !isset($_POST["uid"]) || !isset($_POST["sid"]) )
	die("-2");

$uid = $_POST["uid"];
$sid = $_POST["sid"];

if($uid == -1 || !is_numeric($uid))
	die("-2");

if($sid < 100000000 || $sid > 999999999 || !is_numeric($sid))
	die("-2");

$newsid = processSID($uid, $sid);
if($newsid == -1)
	die("-1");
else if($newsid == 0)
	die("-3");

updateNotifications($uid);

$ret = array("ret" => "801", "sid" => $newsid);
$ret = json_encode($ret);
echo $ret;

?>