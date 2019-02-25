<?php
// qq_getidfromnick.php
require_once "qq_funcs.php";

if( !isset($_POST["uid"]) || !isset($_POST["sid"]) || !isset($_POST["nick"]) )
	die("-2");

$uid = $_POST["uid"];
$sid = $_POST["sid"];
$nick = $_POST["nick"];

if($uid == -1 || !is_numeric($uid))
	die("-2");

if($sid < 100000000 || $sid > 999999999 || !is_numeric($sid))
	die("-2");

if(strlen($nick) == 0 || strlen($nick) > 20 || !isAlphaNumeric($nick))
	die("-2");

$id = getUserID($nick);
if($id == -1)
	die("-2");

$newsid = processSID($uid, $sid);
if($newsid == -1)
	die("-1");
else if($newsid == 0)
	die("-3");

$ret = array("ret" => "999", "sid" => $newsid, "id" => $id);	
$ret = json_encode($ret);
echo $ret;

?>