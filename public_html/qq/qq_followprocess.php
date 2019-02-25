<?php
// qq_followprocess.php
require_once "qq_funcs.php";

if( !isset($_POST["uid"]) || !isset($_POST["sid"]) || !isset($_POST["target_uid"]) || !isset($_POST["op"]) )
	die("-2");

$uid = $_POST["uid"];
$sid = $_POST["sid"];
$target_uid = $_POST["target_uid"];
$op = $_POST["op"];

if($uid == -1 || !is_numeric($uid))
	die("-2");

if($sid < 100000000 || $sid > 999999999 || !is_numeric($sid))
	die("-2");

if( !is_numeric($target_uid) )
	die("-2");

if( $op != 0 && $op != 1 && $op != 2)
	die("-2");

$newsid = processSID($uid, $sid);
if($newsid == -1)
	die("-1");
else if($newsid == 0)
	die("-3");

$state = setUserFollowState($uid, $target_uid, $op);

$ret = array("ret" => "231", "sid" => $newsid, "state" => $state);	
$ret = json_encode($ret);
echo $ret;

?>