<?php
// qq_getlocationquestions.php
require_once "qq_funcs.php";

if( !isset($_POST["uid"]) || !isset($_POST["sid"]) || !isset($_POST["latitude"]) || !isset($_POST["longitude"]) || !isset($_POST["p"]) )
	die("-2");

$uid = $_POST["uid"];
$sid = $_POST["sid"];
$latitude = $_POST["latitude"];
$longitude = $_POST["longitude"];
$p = $_POST["p"];

$lat = doubleval($latitude);
$lon = doubleval($longitude);

if($uid == -1 || !is_numeric($uid))
	die("-2");

if($sid < 100000000 || $sid > 999999999 || !is_numeric($sid))
	die("-2");

if(!is_double($lat) || !is_double($lon))
	die("-2");

if(!is_numeric($p) || $p < 0)
	die("-2");

$newsid = processSID($uid, $sid);
if($newsid == -1)
	die("-1");
else if($newsid == 0)
	die("-3");

$ret = array(	"ret" => "551", "sid" => $newsid,
				"questions" => getLocationQuestions($uid, $lat, $lon, $p));
$ret = json_encode($ret);
echo $ret;

?>