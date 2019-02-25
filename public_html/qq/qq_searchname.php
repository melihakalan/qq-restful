<?php
// qq_searchname.php
require_once "qq_funcs.php";

if( !isset($_POST["uid"]) || !isset($_POST["name"]) || !isset($_POST["p"]) )
	die("-2");

$uid = $_POST["uid"];
$name = $_POST["name"];
$p = $_POST["p"];

if($uid == -1 || !is_numeric($uid))
	die("-2");

if(strlen($name) == 0)
	die("-2");

if(!is_numeric($p) || $p < 0)
	die("-2");

$ret = array(	"ret" => "741",
				"users" => searchName($name, $p));
$ret = json_encode($ret);
echo $ret;

?>