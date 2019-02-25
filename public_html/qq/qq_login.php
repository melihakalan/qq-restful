<?php
// qq_login.php
require_once "qq_funcs.php";
require_once "qq_crypt.php";

$cryptor = new Crypt;

if( !isset($_POST["email"]) || !isset($_POST["pw"]) )
	die("-2");

$email = $_POST["email"];
$pw = $_POST["pw"];

if(strlen($email) == 0 || strlen($email) > 50 || strpos($email, "@") === false | strpos($email, " ") !== false)
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
$st = $conn->prepare("select uid, bActive from user where sEmail = ? and sPass = ?");
$st->bind_param("ss", $email, $pw);
$st->execute();
$st->store_result();

if ($st->num_rows > 0) {
	$st->bind_result($u_uid, $u_active);
	$st->fetch();
	if($u_active == 0){	//banned
		$ret = array("ret" => "112");
		$ret = json_encode($ret);
		echo $ret;
	}
	else
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		$conn->query("insert into login_log values ($u_uid, '$ip', NOW())");
		
		$ses = mt_rand(100000000, 999999999);
		$conn->query("update user set iSession = $ses where uid = $u_uid");
		
		$ret = array("ret" => "111", "sid" => $ses, "uid" => $u_uid);
		$ret = json_encode($ret);
		echo $ret;
	}
} else {
	$ret = array("ret" => "110");
	$ret = json_encode($ret);
	echo $ret;
}

$st->free_result();
$st->close();
$conn->close();
?>