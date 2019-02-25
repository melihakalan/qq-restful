<?php
// qq_getuserfavquestions.php
require_once "qq_funcs.php";

if( !isset($_POST["uid"]) || !isset($_POST["sid"]) || !isset($_POST["tid"]) || !isset($_POST["p"]) )
	die("-2");

$uid = $_POST["uid"];
$sid = $_POST["sid"];
$tid = $_POST["tid"];
$p = $_POST["p"];

if($uid == -1 || !is_numeric($uid))
	die("-2");

if($sid < 100000000 || $sid > 999999999 || !is_numeric($sid))
	die("-2");

if(!is_numeric($tid))
	die("-2");

if(!is_numeric($p) || $p < 0)
	die("-2");

$newsid = processSID($uid, $sid);
if($newsid == -1)
	die("-1");
else if($newsid == 0)
	die("-3");

$ret = array(	"ret" => "261", "sid" => $newsid,
				"questions" => getFavQuestions($uid, $tid, $p));
$ret = json_encode($ret);
echo $ret;

?>