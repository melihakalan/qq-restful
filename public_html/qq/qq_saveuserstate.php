<?php
// qq_saveuserstate.php
require_once "qq_funcs.php";

if( !isset($_POST["uid"]) || !isset($_POST["sid"]) || !isset($_POST["target_uid"]) || !isset($_POST["blocked"]) || !isset($_POST["subscribed"]) )
	die("-2");

$uid = $_POST["uid"];
$sid = $_POST["sid"];
$target_uid = $_POST["target_uid"];
$blocked = $_POST["blocked"];
$subscribed = $_POST["subscribed"];

if($uid == -1 || !is_numeric($uid))
	die("-2");

if($sid < 100000000 || $sid > 999999999 || !is_numeric($sid))
	die("-2");

if( !is_numeric($target_uid) )
	die("-2");

if( $blocked != 0 && $blocked != 1)
	die("-2");

if( $subscribed != 0 && $subscribed != 1)
	die("-2");

$newsid = processSID($uid, $sid);
if($newsid == -1)
	die("-1");
else if($newsid == 0)
	die("-3");

setUserBlocked($uid, $target_uid, $blocked);
setUserSubscribed($uid, $target_uid, $subscribed);

$ret = array("ret" => "221", "sid" => $newsid);	
$ret = json_encode($ret);
echo $ret;

?>