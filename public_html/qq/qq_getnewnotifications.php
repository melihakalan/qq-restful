<?php
// qq_getnewnotifications.php
require_once "qq_funcs.php";

if( !isset($_POST["uid"]) )
	die("-2");

$uid = $_POST["uid"];

if($uid == -1 || !is_numeric($uid))
	die("-2");

$list = getNewNotifications($uid);

$ret = array(	"ret" => "820", "list" => $list, "pendingcount" => getPendingCount($uid), "pendinglist" => getNewPendingList($uid), "newmessages" => getNewMessages($uid) );	
$ret = json_encode($ret);
echo $ret;

?>