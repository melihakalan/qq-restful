<?php
// qq_saveprivatestate.php
require_once "qq_funcs.php";

if( !isset($_POST["uid"]) || !isset($_POST["sid"]) || !isset($_POST["state"]) )
	die("-2");

$uid = $_POST["uid"];
$sid = $_POST["sid"];
$state = $_POST["state"];

if($uid == -1 || !is_numeric($uid))
	die("-2");

if($sid < 100000000 || $sid > 999999999 || !is_numeric($sid))
	die("-2");

if( $state != 1 && $state != 0 )
	die("-2");

$newsid = processSID($uid, $sid);
if($newsid == -1)
	die("-1");
else if($newsid == 0)
	die("-3");

setUserPrivate($uid, $state);

$ret = array("ret" => "261", "sid" => $newsid);	
$ret = json_encode($ret);
echo $ret;

?>