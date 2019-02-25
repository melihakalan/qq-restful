<?php
// qq_searchtag.php
require_once "qq_funcs.php";

if( !isset($_POST["uid"]) || !isset($_POST["tag"]) || !isset($_POST["p"]) )
	die("-2");

$uid = $_POST["uid"];
$tag = $_POST["tag"];
$p = $_POST["p"];

if($uid == -1 || !is_numeric($uid))
	die("-2");

if(strlen($tag) == 0)
	die("-2");

if(!is_numeric($p) || $p < 0)
	die("-2");

$ret = array(	"ret" => "721",
				"tags" => searchTag($tag, $p));
$ret = json_encode($ret);
echo $ret;

?>