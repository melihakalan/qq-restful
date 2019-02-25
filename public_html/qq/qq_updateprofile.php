<?php
// qq_updateprofile.php
require_once "qq_funcs.php";

if( !isset($_POST["uid"]) || !isset($_POST["sid"]) || !isset($_POST["name"]) || !isset($_POST["nick"]) || !isset($_POST["bio"]) || !isset($_POST["phone"]) || !isset($_POST["sex"]) )
	die("-2");

$uid = $_POST["uid"];
$sid = $_POST["sid"];
$name = $_POST["name"];
$nick = $_POST["nick"];
$bio = $_POST["bio"];
$phone = $_POST["phone"];
$sex = $_POST["sex"];

if($uid == -1 || !is_numeric($uid))
	die("-2");

if($sid < 100000000 || $sid > 999999999 || !is_numeric($sid))
	die("-2");

if(strlen($name) == 0 || strlen($name) > 50)
	die("-2");

if(strlen($nick) == 0 || strlen($nick) > 20 || !isAlphaNumeric($nick))
	die("-2");

if(strlen($bio) > 60)
	die("-2");

if(strlen($phone) > 20)
	die("-2");

if( $sex != 0 && $sex != 1 && $sex != 2)
	die("-2");

$newsid = processSID($uid, $sid);
if($newsid == -1)
	die("-1");
else if($newsid == 0)
	die("-3");

$conn = connectDB();

$conn->query("SET NAMES utf8mb4");
$st = $conn->prepare("select uid from user where sNick = ?");
$st->bind_param("s", $nick);
$st->execute();
$st->store_result();

if($st->num_rows > 0){
	$st->bind_result($u_uid);
	$st->fetch();
	if($u_uid != $uid){
		$st->free_result();
		$st->close();
		$conn->close();
	
		$ret = array("ret" => "900", "sid" => $newsid);
		$ret = json_encode($ret);
		die ($ret);
	}
}

$st->free_result();
$st->close();
	
$st = $conn->prepare("update user set sNick = ? where uid = ?");
$st->bind_param("si", $nick, $uid);
$st->execute();
$st->close();

$st = $conn->prepare("update user_info set sName = ?, sBio = ?, sPhone = ?, iSex = ? where uid = ?");
$st->bind_param("sssii", $name, $bio, $phone, $sex, $uid);
$st->execute();
$st->close();

$ret = array("ret" => "901", "sid" => $newsid);
$ret = json_encode($ret);
echo $ret;

$conn->close();

?>