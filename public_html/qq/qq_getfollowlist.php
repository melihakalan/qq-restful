<?php
// qq_getfollowlist.php
require_once "qq_funcs.php";

if( !isset($_POST["uid"]) || !isset($_POST["sid"]) || !isset($_POST["target_uid"]) || !isset($_POST["op"]) || !isset($_POST["p"]) )
	die("-2");

$uid = $_POST["uid"];
$sid = $_POST["sid"];
$target_uid = $_POST["target_uid"];
$op = $_POST["op"];
$p = $_POST["p"];

if($uid == -1 || !is_numeric($uid))
	die("-2");

if($sid < 100000000 || $sid > 999999999 || !is_numeric($sid))
	die("-2");

if( !is_numeric($target_uid) )
	die("-2");

if( $op != 1 && $op != 2 )
	die("-2");

if(!is_numeric($p) || $p < 0)
	die("-2");

$newsid = processSID($uid, $sid);
if($newsid == -1)
	die("-1");
else if($newsid == 0)
	die("-3");

if( $op == 1 ) {	// following list
	$list = getFollowingList($uid, $target_uid, $p);
} else {
	$list = getFollowerList($uid, $target_uid, $p);
}

$ret = array(	"ret" => "251", "sid" => $newsid, "list" => $list );	
$ret = json_encode($ret);
echo $ret;

?>