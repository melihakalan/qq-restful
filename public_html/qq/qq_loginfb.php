<?php
// qq_loginfb.php
require_once "qq_funcs.php";

if( !isset($_POST["email"]) || !isset($_POST["fbuid"]) || !isset($_POST["fbtoken"]) )
	die("-2");

$email = $_POST["email"];
$fbuid = $_POST["fbuid"];
$fbtoken = $_POST["fbtoken"];

if(strlen($email) == 0 || strlen($email) > 50 || strpos($email, "@") === false | strpos($email, " ") !== false)
	die("-2");

$fbret = file_get_contents("https://graph.facebook.com/me?fields=id,email&access_token=".$fbtoken);
$fbret = mb_convert_encoding($fbret, 'HTML-ENTITIES', "UTF-8");
$fbret = json_decode($fbret, true);
if($fbret["id"] != $fbuid || $fbret["email"] != $email)
	die("-2");

$conn = connectDB();

if ($conn->connect_error)
    die("-1");

$conn->query("SET NAMES UTF8");
$st = $conn->prepare("select uid, bActive from user where sEmail = ?");
$st->bind_param("s", $email);
$st->execute();
$st->store_result();

if ($st->num_rows > 0) {
	$st->bind_result($u_uid, $u_active);
	$st->fetch();
	if($u_active == 0){	//banned
		$ret = array("ret" => "132");
		$ret = json_encode($ret);
		echo $ret;
	}
	else
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		$conn->query("insert into login_log values ($u_uid, '$ip', NOW())");
		
		$ses = mt_rand(100000000, 999999999);
		$conn->query("update user set iSession = $ses where uid = $u_uid");
		
		$ret = array("ret" => "131", "sid" => $ses, "uid" => $u_uid);
		$ret = json_encode($ret);
		echo $ret;
	}
} else {
	$ret = array("ret" => "130");
	$ret = json_encode($ret);
	echo $ret;
}

$st->free_result();
$st->close();
$conn->close();
?>