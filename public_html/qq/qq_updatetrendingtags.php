<?php
// qq_updatetrendingtags.php
require_once "qq_funcs.php";

$conn = connectDB();

if ($conn->connect_error)
	die("-1");

$conn->query("SET NAMES UTF8");
$st = $conn->prepare("select sTags from question where iQID <> -1 AND sTags <> \"\" AND bActive = 1 AND (select TIMESTAMPDIFF(DAY, date, NOW())) = 0");
$st->execute();
$st->store_result();

if($st->num_rows == 0){
	$st->free_result();
	$st->close();
	$conn->close();	
	return;
}

$livetags = "";
$st->bind_result($tags);
while($st->fetch()) {
	$livetags .= $tags;
}

$st->free_result();
$st->close();

$alltags = explode("#", $livetags);
$nullremoved = array_filter( $alltags, 'strlen' );
$trimmed = array_map('trim', $nullremoved);
array_walk($trimmed, function(&$value, $key) { $value = "#".$value; });
$trends = array_count_values($trimmed);

$i = 0;
$conn->query("TRUNCATE TABLE trending_tags");
foreach($trends as $key => $value){
	$conn->query("insert into trending_tags values ('$key', $value)");
	$i++;
	if($i == 10)
		break;
}

$conn->close();	
?>