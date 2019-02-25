<?php
// qq_getlaunchinfo.php
require_once "qq_funcs.php";

if( !isset($_POST["uid"]) || !isset($_POST["sid"]) )
	die("-2");

$uid = $_POST["uid"];
$sid = $_POST["sid"];

if($uid == -1 || !is_numeric($uid))
	die("-2");

if($sid < 100000000 || $sid > 999999999 || !is_numeric($sid))
	die("-2");

$newsid = processSID($uid, $sid);
if($newsid == -1)
	die("-1");
else if($newsid == 0)
	die("-3");

$ret = array(	"ret" => "141", "sid" => $newsid,
				"myinfo" => getMyInfo($uid),
				"following" => getFollowCount($uid),
				"follower" => getFollowerCount($uid),
				"pp" => getProfilePhotoName($uid));
$ret = json_encode($ret);
echo $ret;

?>