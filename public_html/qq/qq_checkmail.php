<?php
// qq_checkmail.php
require_once "qq_funcs.php";

if(!isset($_POST["email"]))
	die("-2");

$email = $_POST["email"];
if(strlen($email) == 0 || strlen($email) > 50 || strpos($email, "@") === false || strpos($email, " ") !== false)
	die("-2");

$conn = connectDB();

if ($conn->connect_error)
	die("-1");

$conn->query("SET NAMES UTF8");
$st = $conn->prepare("select sNick from user where sEmail = ?");
$st->bind_param("s", $email);
$st->execute();
$st->store_result();

if ($st->num_rows > 0) {
	$ret = array("ret" => "101");
	$ret = json_encode($ret);
	echo $ret;
} else {
	$ret = array("ret" => "100");
	$ret = json_encode($ret);
	echo $ret;
}

$st->free_result();
$st->close();
$conn->close();
?>