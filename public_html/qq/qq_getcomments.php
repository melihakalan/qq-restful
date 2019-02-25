<?php
// qq_getcomments.php
require_once "qq_funcs.php";

if( !isset($_POST["uid"]) || !isset($_POST["sid"]) || !isset($_POST["qid"]) || !isset($_POST["p"]) )
	die("-2");

$uid = $_POST["uid"];
$sid = $_POST["sid"];
$qid = $_POST["qid"];
$p = $_POST["p"];

if($uid == -1 || !is_numeric($uid))
	die("-2");

if($sid < 100000000 || $sid > 999999999 || !is_numeric($sid))
	die("-2");

if(!is_numeric($qid))
	die("-2");

if(!is_numeric($p) || $p < 0)
	die("-2");

$newsid = processSID($uid, $sid);
if($newsid == -1)
	die("-1");
else if($newsid == 0)
	die("-3");

$list = getCommentList($uid, $qid, $p);

$ret = array(	"ret" => "661", "sid" => $newsid, "list" => $list );	
$ret = json_encode($ret);
echo $ret;

?>