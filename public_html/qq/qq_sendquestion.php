<?php
// qq_sendquestion.php
require_once "ImageResize.php";
require_once "qq_funcs.php";

if( !isset($_POST["uid"]) || !isset($_POST["sid"]) || !isset($_POST["qtext"]) || !isset($_POST["anonym"]) || !isset($_POST["private"]) || !isset($_POST["opcount"]) || !isset($_POST["tags"]) || !isset($_POST["qimage"])
	|| !isset($_POST["op1"]) || !isset($_POST["op3"]) || !isset($_POST["op3"]) || !isset($_POST["op4"]) || !isset($_POST["op5"]) || !isset($_POST["op6"]) || !isset($_POST["op7"]) || !isset($_POST["op8"])
	|| !isset($_POST["img1"]) || !isset($_POST["img2"]) || !isset($_POST["img3"]) || !isset($_POST["img4"]) || !isset($_POST["img5"]) || !isset($_POST["img6"]) || !isset($_POST["img7"]) || !isset($_POST["img8"])
	|| !isset($_POST["latitude"]) || !isset($_POST["longitude"]))
	die("-2");

$uid = $_POST["uid"];
$sid = $_POST["sid"];
$qtext = $_POST["qtext"];
$anonym = $_POST["anonym"];
$private = $_POST["private"];
$opcount = $_POST["opcount"];
$tags = $_POST["tags"];
$qimage = $_POST["qimage"];
$op1 = $_POST["op1"];
$op2 = $_POST["op2"];
$op3 = $_POST["op3"];
$op4 = $_POST["op4"];
$op5 = $_POST["op5"];
$op6 = $_POST["op6"];
$op7 = $_POST["op7"];
$op8 = $_POST["op8"];
$img1 = $_POST["img1"];
$img2 = $_POST["img2"];
$img3 = $_POST["img3"];
$img4 = $_POST["img4"];
$img5 = $_POST["img5"];
$img6 = $_POST["img6"];
$img7 = $_POST["img7"];
$img8 = $_POST["img8"];
$latitude = $_POST["latitude"];
$longitude = $_POST["longitude"];

if($uid == -1 || !is_numeric($uid))
	die("-2");

if($sid < 100000000 || $sid > 999999999 || !is_numeric($sid))
	die("-2");

if(strlen($qtext) == 0 || strlen($qtext) > 120)
	die("-2");

if($anonym != 0 && $anonym != 1)
	die("-2");

if($private != 0 && $private != 1)
	die("-2");

if(!is_numeric($opcount) || $opcount < 0 || $opcount > 8)
	die("-2");

if(strlen($tags) > 50)
	die("-2");

if($qimage !== "0" && check_base64_image($qimage) == false)
	die("-2");

if(strlen($op1) > 30 || strlen($op2) > 30 || strlen($op3) > 30 || strlen($op4) > 30 || strlen($op5) > 30 || strlen($op6) > 30 || strlen($op7) > 30 || strlen($op8) > 30)
	die("-2");

if( ($img1 !== "0" && check_base64_image($img1) == false) || ($img2 !== "0" && check_base64_image($img2) == false) || ($img3 !== "0" && check_base64_image($img3) == false) || ($img4 !== "0" && check_base64_image($img4) == false) || ($img5 !== "0" && check_base64_image($img5) == false) || ($img6 !== "0" && check_base64_image($img6) == false) || ($img7 !== "0" && check_base64_image($img7) == false) || ($img8 !== "0" && check_base64_image($img8) == false) )
	die("-2");

if( $latitude !== "0" && !is_double(doubleval($latitude)))
	die("-2");

if( $longitude !== "0" && !is_double(doubleval($longitude)))
	die("-2");

$newsid = processSID($uid, $sid);
if($newsid == -1)
	die("-1");
else if($newsid == 0)
	die("-3");

$conn = connectDB();

$conn->query("SET NAMES utf8mb4");
$st = $conn->prepare("update user_info set iQuestions = iQuestions + 1 where uid = ?");
$st->bind_param("i", $uid);
$st->execute();
$st->close();
	
$allq = $conn->query("select iQID from question");
$newqid = $allq->num_rows;

$simg = "0";
if($qimage !== "0"){
	$img = base64_decode($qimage);
	$simg = "q".$newqid.".jpg";
	file_put_contents("qimg//".$simg, $img);
}
	
$st = $conn->prepare("insert into question values($newqid, ?, ?, ?, ?, ?, 0, 0, 0, ?, ?, ?, ?, -1, 1, NOW())");
$st->bind_param("isiiissss", $uid, $qtext, $anonym, $private, $opcount, $tags, $simg, $latitude, $longitude);
$st->execute();
$st->close();

$st = $conn->prepare("insert into options_text values ($newqid, ?, ?, ?, ?, ?, ?, ?, ?)");
$st->bind_param("ssssssss", $op1, $op2, $op3, $op4, $op5, $op6, $op7, $op8);
$st->execute();
$st->close();
	
$conn->query("insert into options_result values ($newqid, 0, 0, 0, 0, 0, 0, 0, 0)");

saveOptionImages($newqid, $img1, $img2, $img3, $img4, $img5, $img6, $img7, $img8);

insertSubscribeNotification($uid, $newqid);
checkTaggedUsers($uid, $qtext, $newqid);

$retq = getQuestion($uid, $newqid);

$ret = array("ret" => "201", "sid" => $newsid, "question" => $retq);
$ret = json_encode($ret);
echo $ret;

$conn->close();
?>