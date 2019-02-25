<?php
// qq_sendmessage.php
require_once "qq_funcs.php";

if( !isset($_POST["uid"]) || !isset($_POST["sid"]) || !isset($_POST["tid"]) || !isset($_POST["msg"]) )
	die("-2");

$uid = $_POST["uid"];
$sid = $_POST["sid"];
$tid = $_POST["tid"];
$msg = $_POST["msg"];

if($uid == -1 || !is_numeric($uid))
	die("-2");

if($sid < 100000000 || $sid > 999999999 || !is_numeric($sid))
	die("-2");

if(!is_numeric($tid))
	die("-2");

if(strlen($msg) == 0 || strlen($msg) > 100)
	die("-2");

$newsid = processSID($uid, $sid);
if($newsid == -1)
	die("-1");
else if($newsid == 0)
	die("-3");

$sm = sendMessage($uid, $tid, $msg);

if($sm == 0){
	$ret = array(	"ret" => "950", "sid" => $newsid);	
	$ret = json_encode($ret);
	echo $ret;	
} else {
	$ret = array(	"ret" => "951", "sid" => $newsid);	
	$ret = json_encode($ret);
	echo $ret;
}
?>