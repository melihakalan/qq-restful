<?php
// qq_searchnick.php
require_once "qq_funcs.php";

if( !isset($_POST["uid"]) || !isset($_POST["nick"]) || !isset($_POST["p"]) )
	die("-2");

$uid = $_POST["uid"];
$nick = $_POST["nick"];
$p = $_POST["p"];

if($uid == -1 || !is_numeric($uid))
	die("-2");

if(strlen($nick) == 0)
	die("-2");

if(!is_numeric($p) || $p < 0)
	die("-2");

$ret = array(	"ret" => "731",
				"users" => searchNick($nick, $p));
$ret = json_encode($ret);
echo $ret;

?>