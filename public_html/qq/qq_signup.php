<?php
// qq_signup.php
require_once "qq_funcs.php";
require_once "qq_crypt.php";

$cryptor = new Crypt;

if( !isset($_POST["email"]) || !isset($_POST["nick"]) || !isset($_POST["name"]) || !isset($_POST["pw"]) )
	die("-2");

$email = $_POST["email"];
$nick = $_POST["nick"];
$name = $_POST["name"];
$pw = $_POST["pw"];

if(strlen($email) == 0 || strlen($email) > 50 || strpos($email, "@") === false || strpos($email, " ") !== false)
	die("-2");

if(strlen($nick) == 0 || strlen($nick) > 20 || !isAlphaNumeric($nick))
	die("-2");

if(strlen($name) == 0 || strlen($name) > 50)
	die("-2");

if(strlen($pw) < 8 || !isAlphaNumeric($pw))
	die("-2");

$pw = $cryptor->decrypt($pw);

if(strlen($pw) < 8 || strlen($pw) > 20 || !isAlphaNumeric($pw))
	die("-2");

$conn = connectDB();

if ($conn->connect_error)
    die("-1");

$conn->query("SET NAMES UTF8");
$st = $conn->prepare("select uid from user where sNick = ? OR sEmail = ?");
$st->bind_param("ss", $nick, $email);
$st->execute();
$st->store_result();

if ($st->num_rows > 0) {
	$st->free_result();
	$st->close();
	
	$ret = array("ret" => "120");
	$ret = json_encode($ret);
	echo $ret;
} else {
	
	$st->free_result();
	$st->close();
	
	$all = $conn->query("select uid from user");
	$newuid = $all->num_rows;
	
	$st = $conn->prepare("insert into user values ($newuid, ?, ?, ?, 0, 1, 0, NOW())");
	$st->bind_param("sss", $nick, $pw, $email);
	$st->execute();
	$st->close();
	
	$st = $conn->prepare("insert into user_info values ($newuid, ?, '', '', 0, 0, 0, 0, 0)");
	$st->bind_param("s", $name);
	$st->execute();
	$st->close();
	
	$conn->query("insert into user_pp values ($newuid, -1)");
	$conn->query("insert into notifications_check values ($newuid, NOW())");
	
	$ip = $_SERVER['REMOTE_ADDR'];
	$conn->query("insert into login_log values ($newuid, '$ip', NOW())");
	
	$ses = mt_rand(100000000, 999999999);
	$conn->query("update user set iSession = $ses where uid = $newuid");
	
	$ret = array("ret" => "121", "sid" => $ses, "uid" => $newuid);
	$ret = json_encode($ret);
	echo $ret;
}

$conn->close();
?>