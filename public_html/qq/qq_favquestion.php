<?php
// qq_favquestion.php
require_once "qq_funcs.php";

if( !isset($_POST["uid"]) || !isset($_POST["sid"]) || !isset($_POST["qid"]) || !isset($_POST["state"]) )
	die("-2");

$uid = $_POST["uid"];
$sid = $_POST["sid"];
$qid = $_POST["qid"];
$state = $_POST["state"];

if($uid == -1 || !is_numeric($uid))
	die("-2");

if($sid < 100000000 || $sid > 999999999 || !is_numeric($sid))
	die("-2");

if(!is_numeric($qid))
	die("-2");

if($state != 0 && $state != 1)
	die("-2");

$newsid = processSID($uid, $sid);
if($newsid == -1)
	die("-1");
else if($newsid == 0)
	die("-3");

if($state == 1)
	setFavQuestion($uid, $qid);
else
	setUnfavQuestion($uid, $qid);

$ret = array("ret" => "611", "sid" => $newsid);
$ret = json_encode($ret);
echo $ret;

?>