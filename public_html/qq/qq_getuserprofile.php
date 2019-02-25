<?php
// qq_getuserprofile.php
require_once "qq_funcs.php";

if( !isset($_POST["uid"]) || !isset($_POST["sid"]) || !isset($_POST["target_uid"]) )
	die("-2");

$uid = $_POST["uid"];
$sid = $_POST["sid"];
$target_uid = $_POST["target_uid"];

if($uid == -1 || !is_numeric($uid))
	die("-2");

if($sid < 100000000 || $sid > 999999999 || !is_numeric($sid))
	die("-2");

if( !is_numeric($target_uid) )
	die("-2");

$newsid = processSID($uid, $sid);
if($newsid == -1)
	die("-1");
else if($newsid == 0)
	die("-3");

if( checkUserBlocked($target_uid, $uid) == 1 ){
	$ret = array(	"ret" => "210", "sid" => $newsid,
					"block_state" => checkUserBlocked($uid, $target_uid),
					"report_state" => getReportState($uid, $target_uid));
	$ret = json_encode($ret);
	die($ret);
}

$ret = array(	"ret" => "211", "sid" => $newsid,
				"userinfo" => getUserInfo($target_uid),
				"following" => getFollowCount($target_uid),
				"follower" => getFollowerCount($target_uid),
				"follow_state" => getFollowState($uid, $target_uid),
				"block_state" => checkUserBlocked($uid, $target_uid),
				"subscribe_state" => getSubscribeState($uid, $target_uid),
				"report_state" => getReportState($uid, $target_uid),
				"pp" => getProfilePhotoName($target_uid)
				);
				
$ret = json_encode($ret);
echo $ret;

?>