<?php
// qq_changepwadr.php
require_once "qq_funcs.php";
require_once "qq_crypt.php";

$cryptor = new Crypt;

if( !isset($_POST["uid"]) || !isset($_POST["sid"]) || !isset($_POST["currentpw"]) || !isset($_POST["newpw"]) )
	die("-2");

$uid = $_POST["uid"];
$sid = $_POST["sid"];
$currentpw = $_POST["currentpw"];
$newpw = $_POST["newpw"];

if($uid == -1 || !is_numeric($uid))
	die("-2");

if($sid < 100000000 || $sid > 999999999 || !is_numeric($sid))
	die("-2");

if(strlen($currentpw) < 8 || !isAlphaNumeric($currentpw))
	die("-2");
$currentpw = $cryptor->decrypt($currentpw);
if(strlen($currentpw) < 8 || strlen($currentpw) > 20 || !isAlphaNumeric($currentpw))
	die("-2");

if(strlen($newpw) < 8 || !isAlphaNumeric($newpw))
	die("-2");
$newpw = $cryptor->decrypt($newpw);
if(strlen($newpw) < 8 || strlen($newpw) > 20 || !isAlphaNumeric($newpw))
	die("-2");

$newsid = processSID($uid, $sid);
if($newsid == -1)
	die("-1");
else if($newsid == 0)
	die("-3");

if( checkUserPW($uid, $currentpw) == 1 ) {
	setUserPW($uid, $newpw);
	$ret = array("ret" => "271", "sid" => $newsid);	
	$ret = json_encode($ret);
	echo $ret;
} else {
	$ret = array("ret" => "270", "sid" => $newsid);	
	$ret = json_encode($ret);
	echo $ret;
}

?>