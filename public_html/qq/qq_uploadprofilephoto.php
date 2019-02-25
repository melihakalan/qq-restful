<?php
// qq_uploadprofilephoto.php
require_once "ImageResize.php";
require_once "qq_funcs.php";

if( !isset($_POST["uid"]) || !isset($_POST["sid"]) || !isset($_POST["photo64"]) )
	die("-2");

$uid = $_POST["uid"];
$sid = $_POST["sid"];
$photo64 = $_POST["photo64"];

if($uid == -1 || !is_numeric($uid))
	die("-2");

if($sid < 100000000 || $sid > 999999999 || !is_numeric($sid))
	die("-2");

if(check_base64_image($photo64) == false)
	die("-2");

$newsid = processSID($uid, $sid);
if($newsid == -1)
	die("-1");
else if($newsid == 0)
	die("-3");

saveProfilePhoto($uid, $photo64);

$ret = array("ret" => "911", "sid" => $newsid, "pp" => getProfilePhotoName($uid));
$ret = json_encode($ret);
echo $ret;

?>