<?php
// qq_sendcomment.php
require_once "qq_funcs.php";

if( !isset($_POST["uid"]) || !isset($_POST["sid"]) || !isset($_POST["qid"]) || !isset($_POST["votenum"]) || !isset($_POST["comment"]) )
	die("-2");

$uid = $_POST["uid"];
$sid = $_POST["sid"];
$qid = $_POST["qid"];
$votenum = $_POST["votenum"];
$comment = $_POST["comment"];

if($uid == -1 || !is_numeric($uid))
	die("-2");

if($sid < 100000000 || $sid > 999999999 || !is_numeric($sid))
	die("-2");

if( !is_numeric($qid) )
	die("-2");

if( $votenum < -1 || $votenum > 7)
	die("-2");

if( strlen($comment) == 0 || strlen($comment) > 100)
	die("-2");

$newsid = processSID($uid, $sid);
if($newsid == -1)
	die("-1");
else if($newsid == 0)
	die("-3");

$cid = sendComment($uid, $qid, $votenum, $comment);
checkTaggedUsers($uid, $comment, $qid);

if($cid == -1) {
	$ret = array(	"ret" => "666", "sid" => $newsid);	
	$ret = json_encode($ret);
	echo $ret;	
} else {
	$ret = array(	"ret" => "665", "sid" => $newsid, "comment" => getComment($cid) );	
	$ret = json_encode($ret);
	echo $ret;
}

?>