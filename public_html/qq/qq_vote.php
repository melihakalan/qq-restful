<?php
// qq_vote.php
require_once "qq_funcs.php";

if( !isset($_POST["uid"]) || !isset($_POST["sid"]) || !isset($_POST["qid"]) || !isset($_POST["opt"]) )
	die("-2");

$uid = $_POST["uid"];
$sid = $_POST["sid"];
$qid = $_POST["qid"];
$opt = $_POST["opt"];

if($uid == -1 || !is_numeric($uid))
	die("-2");

if($sid < 100000000 || $sid > 999999999 || !is_numeric($sid))
	die("-2");

if(!is_numeric($qid))
	die("-2");

if(!is_numeric($opt) || $opt < 0 || $opt > 7)
	die("-2");

$newsid = processSID($uid, $sid);
if($newsid == -1)
	die("-1");
else if($newsid == 0)
	die("-3");

if(setUserVote($uid, $qid, $opt) == 1){
	$ret = array("ret" => "641", "sid" => $newsid, "votes" => getQuestionVotes($qid));
	$ret = json_encode($ret);
	echo $ret;
} else {
	$ret = array("ret" => "640", "sid" => $newsid);
	$ret = json_encode($ret);
	echo $ret;	
}
?>