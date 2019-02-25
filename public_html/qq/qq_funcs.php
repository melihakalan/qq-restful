<?php
// qq_funcs.php
function isAlphaNumeric($s){
	if( preg_match('/^[A-Za-z0-9_]+$/', $s) == true )
		return true;
	else
		return false;
}

function connectDB() {
	$conn = new mysqli("localhost", "biltagne", ":D", ":D");
	return $conn;
}

function processSID($uid, $sid){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select uid from user where uid = ? and iSession = ?");
	$st->bind_param("ii", $uid, $sid);
	$st->execute();
	$st->store_result();
	
	if ($st->num_rows > 0) {
		
		$st->free_result();
		$st->close();
		
		$ses = mt_rand(100000000, 999999999);
		$st = $conn->prepare("update user set iSession = $ses where uid = ?");
		$st->bind_param("i", $uid);
		$st->execute();
		
		$st->free_result();
		$st->close();
		$conn->close();
		
		return $ses;
	}
	else{
		$st->free_result();
		$st->close();
		$conn->close();		
		
		return 0;
	}
	
}

function getMyInfo($uid){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES utf8mb4");
	$st = $conn->prepare("select sNick from user where uid = ?");
	$st->bind_param("i", $uid);
	$st->execute();
	$st->store_result();
	
	if ($st->num_rows > 0) {
		
		$st->bind_result($nick);
		$st->fetch();
		$st->free_result();
		$st->close();
		
		$st = $conn->prepare("select * from user_info where uid = ?");
		$st->bind_param("i", $uid);
		$st->execute();
		$st->store_result();
		$st->bind_result($u_uid, $u_name, $u_bio, $u_phone, $u_sex, $u_questions, $u_qp, $u_private, $u_reports);
		$st->fetch();
		
		$ret = array("nick" => $nick, "name" => $u_name, "bio" => $u_bio, "phone" => $u_phone, "sex" => $u_sex, "questions" => $u_questions, "qp" => $u_qp, "private" => $u_private);
		
		$st->free_result();
		$st->close();
		$conn->close();
		
		return $ret;
	}
	else{
		$st->free_result();
		$st->close();
		$conn->close();		
		
		return 0;
	}	
	
}

function checkUserBlocked($uid, $target_uid){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select * from user_block where uid = ? AND uTarget = ?");
	$st->bind_param("ii", $uid, $target_uid);
	$st->execute();
	$st->store_result();
	
	if ($st->num_rows > 0) {
		
		$st->free_result();
		$st->close();
		$conn->close();
		
		return 1;
		
	} else {
		
		$st->free_result();
		$st->close();
		$conn->close();
		
		return 0;
		
	}
	
}

function getUserInfo($uid){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES utf8mb4");
	$st = $conn->prepare("select sNick from user where uid = ?");
	$st->bind_param("i", $uid);
	$st->execute();
	$st->store_result();
	
	if ($st->num_rows > 0) {
		
		$st->bind_result($nick);
		$st->fetch();
		$st->free_result();
		$st->close();
		
		$st = $conn->prepare("select sName, sBio, iQuestions, iQP from user_info where uid = ?");
		$st->bind_param("i", $uid);
		$st->execute();
		$st->store_result();
		$st->bind_result($u_name, $u_bio, $u_questions, $u_qp);
		$st->fetch();
		
		$ret = array("nick" => $nick, "name" => $u_name, "bio" => $u_bio, "questions" => $u_questions, "qp" => $u_qp);
		
		$st->free_result();
		$st->close();
		$conn->close();
		
		return $ret;
	}
	else{
		$st->free_result();
		$st->close();
		$conn->close();		
		
		return 0;
	}	
	
}

function getFollowState($uid, $target_uid){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select * from follow where uid = ? AND uTarget = ?");
	$st->bind_param("ii", $uid, $target_uid);
	$st->execute();
	$st->store_result();
	
	if ($st->num_rows > 0) {
		
		$st->free_result();
		$st->close();
		$conn->close();
		
		return 1;
		
	} else {
		
		$st->free_result();
		$st->close();
		
		$st = $conn->prepare("select * from pending_follow where uid = ? AND uTarget = ?");
		$st->bind_param("ii", $uid, $target_uid);
		$st->execute();
		$st->store_result();
		
		if ($st->num_rows > 0) {
			
			$st->free_result();
			$st->close();
			$conn->close();
		
			return 2;
			
		}
		else {
		
			$st->free_result();
			$st->close();
			$conn->close();
			
			return 0;
		
		}
		
	}
	
}

function getSubscribeState($uid, $target_uid){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select * from subscribe where uid = ? AND uTarget = ?");
	$st->bind_param("ii", $uid, $target_uid);
	$st->execute();
	$st->store_result();
	
	if ($st->num_rows > 0) {
		
		$st->free_result();
		$st->close();
		$conn->close();
		
		return 1;
		
	} else {
		
		$st->free_result();
		$st->close();
		$conn->close();
		
		return 0;
		
	}
	
}

function getFollowCount($uid){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select * from follow where uid = ?");
	$st->bind_param("i", $uid);
	$st->execute();
	$st->store_result();
	
	$count = $st->num_rows;
	
	$st->free_result();
	$st->close();
	$conn->close();
		
	return $count;
}

function getFollowerCount($uid){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select * from follow where uTarget = ?");
	$st->bind_param("i", $uid);
	$st->execute();
	$st->store_result();
	
	$count = $st->num_rows;
	
	$st->free_result();
	$st->close();
	$conn->close();
		
	return $count;
}

function checkUserPrivate($uid){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select bPrivate from user_info where uid = ?");
	$st->bind_param("i", $uid);
	$st->execute();
	$st->store_result();
	
	$private = 0;
	
	if($st->num_rows > 0){
		$st->bind_result($private);
		$st->fetch();
	}
	
	$st->free_result();
	$st->close();
	$conn->close();
		
	return $private;
}

function setUserBlocked($uid, $target_uid, $state){
	
	$current_state = checkUserBlocked($uid, $target_uid);
	if($state == 1 && $current_state == 1)
		return;
	if($state == 0 && $current_state == 0)
		return;
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	
	if($state == 1){
		$st = $conn->prepare("insert into user_block values (?,?)");
		$st->bind_param("ii", $uid, $target_uid);
		$st->execute();
	} else {
		$st = $conn->prepare("delete from user_block where uid = ? AND uTarget = ?");
		$st->bind_param("ii", $uid, $target_uid);
		$st->execute();
	}
	
	$st->close();
	$conn->close();
}

function setUserSubscribed($uid, $target_uid, $state){
	
	$current_state = getSubscribeState($uid, $target_uid);
	if($state == 1 && $current_state == 1)
		return;
	if($state == 0 && $current_state == 0)
		return;
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	
	if($state == 1){
		$st = $conn->prepare("insert into subscribe values (?,?)");
		$st->bind_param("ii", $uid, $target_uid);
		$st->execute();
	} else {
		$st = $conn->prepare("delete from subscribe where uid = ? AND uTarget = ?");
		$st->bind_param("ii", $uid, $target_uid);
		$st->execute();
	}
	
	$st->close();
	$conn->close();
}

function setUserFollowState($uid, $target_uid, $state){
	
	$current_state = getFollowState($uid, $target_uid);
	
	if($state == 0 && $current_state != 1)
		return -2;
	if($state == 1 && $current_state != 0)
		return -2;
	if($state == 2 && $current_state != 2)
		return -2;
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	
	if($state == 0){	//unfollow
		$st = $conn->prepare("delete from follow where uid = ? and uTarget = ?");
		$st->bind_param("ii", $uid, $target_uid);
		$st->execute();
		$st->close();
		$conn->close();
		return 1;
	} else if ($state == 1){	//follow
		$private = checkUserPrivate($target_uid);
		if( $private == 1 ){
			$st = $conn->prepare("insert into pending_follow values (?,?,NOW())");
			$st->bind_param("ii", $uid, $target_uid);
			$st->execute();
			$st->close();
			$conn->close();
			return 2;
		} else if ( $private == 0 ) {
			$st = $conn->prepare("insert into follow values (?,?,NOW())");
			$st->bind_param("ii", $uid, $target_uid);
			$st->execute();
			$st->close();
			$conn->close();
			
			insertFollowNotification($uid, $target_uid);
			
			return 1;
		}
	} else {	//2 - cancel pending
		$st = $conn->prepare("delete from pending_follow where uid = ? and uTarget = ?");
		$st->bind_param("ii", $uid, $target_uid);
		$st->execute();
		$st->close();
		$conn->close();
		return 1;
	}
}

function getReportState($uid, $target_uid){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select * from report where uid = ? AND uTarget = ?");
	$st->bind_param("ii", $uid, $target_uid);
	$st->execute();
	$st->store_result();
	
	if ($st->num_rows > 0) {
		
		$st->free_result();
		$st->close();
		$conn->close();
		
		return 1;
		
	} else {
		
		$st->free_result();
		$st->close();
		$conn->close();
		
		return 0;
		
	}
	
}

function reportUser($uid, $target_uid){
		
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	
	$state = getReportState($uid, $target_uid);
	
	if($state == 0){
		$st = $conn->prepare("insert into report values (?, ?, NOW())");
		$st->bind_param("ii", $uid, $target_uid);
		$st->execute();
		$st->close();
		
		$st = $conn->prepare("update user_info set iReports = iReports + 1 where uid = ?");
		$st->bind_param("i", $target_uid);
		$st->execute();	
		$st->close();
	}
	$conn->close();
	
}

function getFollowInfo($uid){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select sNick from user where uid = ?");
	$st->bind_param("i", $uid);
	$st->execute();
	$st->store_result();
	
	if ($st->num_rows > 0) {
		
		$st->bind_result($nick);
		$st->fetch();
		$st->free_result();
		$st->close();
		
		$st = $conn->prepare("select sName from user_info where uid = ?");
		$st->bind_param("i", $uid);
		$st->execute();
		$st->store_result();
		$st->bind_result($u_name);
		$st->fetch();
		
		$ret = array("nick" => $nick, "name" => $u_name);
		
		$st->free_result();
		$st->close();
		$conn->close();
		
		return $ret;
	}
	else{
		$st->free_result();
		$st->close();
		$conn->close();		
		
		return 0;
	}	
	
}

function getFollowingList($uid, $target_uid, $p){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$page = $p * 50;
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select uTarget from follow where uid = ? order by date DESC limit $page, 50");
	$st->bind_param("i", $target_uid);
	$st->execute();
	$st->store_result();
	
	$list = array();
	
	if ($st->num_rows > 0) {
		
		$st->bind_result($following_id);
		
		while($st->fetch()){
			$user_info = getFollowInfo($following_id);
			$block_state = checkUserBlocked($following_id, $uid);
			$follow_state = getFollowState($uid, $following_id);
			$pp = getProfilePhotoName($following_id);
			
			$ret = array("uid" => $following_id, "info" => $user_info, "block_state" => $block_state, "follow_state" => $follow_state, "pp" => $pp);
			array_push($list, $ret);
		}
	} else {
		$st->free_result();
		$st->close();
		$conn->close();		
		return 0;
	}
	
	$st->free_result();
	$st->close();
	$conn->close();
	
	return $list;
	
}

function getFollowerList($uid, $target_uid, $p){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$page = $p * 50;
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select uid from follow where uTarget = ? order by date DESC limit $page, 50");
	$st->bind_param("i", $target_uid);
	$st->execute();
	$st->store_result();
	
	$list = array();
	
	if ($st->num_rows > 0) {
		
		$st->bind_result($follower_id);
		
		while($st->fetch()){
			$user_info = getFollowInfo($follower_id);
			$block_state = checkUserBlocked($follower_id, $uid);
			$follow_state = getFollowState($uid, $follower_id);
			$pp = getProfilePhotoName($follower_id);
			
			$ret = array("uid" => $follower_id, "info" => $user_info, "block_state" => $block_state, "follow_state" => $follow_state, "pp" => $pp);
			array_push($list, $ret);
		}
	} else {
		$st->free_result();
		$st->close();
		$conn->close();		
		return 0;
	}
	
	$st->free_result();
	$st->close();
	$conn->close();
	
	return $list;
	
}

function setUserPrivate($uid, $state) {
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	
	$st = $conn->prepare("update user_info set bPrivate = ? where uid = ?");
	$st->bind_param("ii", $state, $uid);
	$st->execute();
	
	$st->close();
	$conn->close();
	
}

function checkUserPW($uid, $pw){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select * from user where uid = ? AND sPass = ?");
	$st->bind_param("is", $uid, $pw);
	$st->execute();
	$st->store_result();
	
	$ret = $st->num_rows;
	
	$st->free_result();
	$st->close();
	$conn->close();
		
	if( $ret > 0 ){
		return 1;
	} else {
		return 0;
	}
}

function setUserPW($uid, $pw) {
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("update user set sPass = ? where uid = ?");
	$st->bind_param("si", $pw, $uid);
	$st->execute();
	
	$st->close();
	$conn->close();
	
}

function getQuestionFaved($uid, $qid){
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select * from fav where uid = ? AND iQID = ?");
	$st->bind_param("ii", $uid, $qid);
	$st->execute();
	$st->store_result();
	
	$ret = $st->num_rows;
	
	$st->free_result();
	$st->close();
	$conn->close();
		
	if( $ret > 0 ){
		return 1;
	} else {
		return 0;
	}
}

function getQuestionReposted($uid, $qid){
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select iQID from question where bActive = 1 AND uid = ? AND iRepostID = ?");
	$st->bind_param("ii", $uid, $qid);
	$st->execute();
	$st->store_result();
	
	$ret = $st->num_rows;
	
	$st->free_result();
	$st->close();
	$conn->close();
		
	if( $ret > 0 ){
		return 1;
	} else {
		return 0;
	}
}

function getQuestionReported($uid, $qid){
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select * from report_question where uid = ? AND iQID = ?");
	$st->bind_param("ii", $uid, $qid);
	$st->execute();
	$st->store_result();
	
	$ret = $st->num_rows;
	
	$st->free_result();
	$st->close();
	$conn->close();
		
	if( $ret > 0 ){
		return 1;
	} else {
		return 0;
	}
}

function getQuestionOptions($qid){
	$conn = connectDB();
	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES utf8mb4");
	$ret = $conn->query("select * from options_text where iQID = $qid");
	$optext = $ret->fetch_row();
	
	$conn->close();
	return $optext;
}

function getQuestionVotes($qid){
	$conn = connectDB();
	if ($conn->connect_error)
		return -1;
	
	$ret = $conn->query("select * from options_result where iQID = $qid");
	$votes = $ret->fetch_row();
	
	$conn->close();
	return $votes;
}

function getUserQuestions($uid, $target_uid, $p){
	date_default_timezone_set('Europe/Istanbul');
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$page = $p * 10;
	$conn->query("SET NAMES utf8mb4");
	$st = null;
	if($uid == $target_uid)
		$st = $conn->prepare("select * from question where uid = ? AND bActive = 1 order by date DESC limit $page, 10");
	else{
		if(getFollowState($uid, $target_uid) == 1)
			$st = $conn->prepare("select * from question where uid = ? AND bActive = 1 AND bAnonym = 0 order by date DESC limit $page, 10");
		else
			$st = $conn->prepare("select * from question where uid = ? AND bActive = 1 AND bAnonym = 0 AND bPrivate = 0 order by date DESC limit $page, 10");
	}
	$st->bind_param("i", $target_uid);
	$st->execute();
	$st->store_result();
	
	if ($st->num_rows > 0) {
		
		$list = array();
		$st->bind_result($q_qid, $q_uid, $q_question, $q_anonym, $q_private, $q_options, $q_reqs, $q_favs, $q_reports, $q_tags, $q_simg, $q_latitude, $q_longitude, $q_repostid, $q_active, $q_date);
		$date_now = new DateTime();
		
		while($st->fetch()){
			
			$q_reposterid = -1;
			$q_repostername = "";
			$st2 = null;
			
			if($q_qid == -1){
				$q_reposterid = $q_uid;
				$q_repostername = getUserName($q_uid);
				
				$st2 = $conn->prepare("select * from question where iQID = $q_repostid");
				$st2->execute();
				$st2->store_result();
				$st2->bind_result($q_qid, $q_uid, $q_question, $q_anonym, $q_private, $q_options, $q_reqs, $q_favs, $q_reports, $q_tags, $q_simg, $q_latitude, $q_longitude, $q_repostid, $q_active, $q_date);
				$st2->fetch();
			}
			
			$date_post = date_create_from_format('Y-m-d H:i:s', $q_date);
			$date_diff = $date_now->diff($date_post);
			
			$p_year = $date_diff->format('%y');
			$p_month = $date_diff->format('%m');
			$p_day = $date_diff->format('%d');
			$p_hour = $date_diff->format('%h');
			
			$q_locked = 0;
			
			if($p_year > 0 || $p_month > 0 || $p_day > 0)
				$q_locked = 1;
			
			$ret = array(	"uid" => $q_uid,
							"pp" => getProfilePhotoName($q_uid),
							"nick" => getUserNick($q_uid),
							"name" => getUserName($q_uid),
							"qid" => $q_qid,
							"question" => $q_question,
							"anonym" => $q_anonym,
							"private" => $q_private,
							"options" => $q_options,
							"reqs" => $q_reqs,
							"favs" => $q_favs,
							"tags" => $q_tags,
							"date" => $q_date,
							"locked" => $q_locked,
							"phour" => $p_hour,
							"s_options" => getQuestionOptions($q_qid),
							"votes" => getQuestionVotes($q_qid),
							"faved" => getQuestionFaved($uid, $q_qid),
							"reported" => getQuestionReported($uid, $q_qid),
							"voted" => isUserVoted($uid, $q_qid),
							"votenum" => getUserVote($uid, $q_qid),
							"comments" => getCommentCount($q_qid),
							"images" => getQuestionOptionImages($q_qid),
							"qimage" => $q_simg,
							"reposted" => getQuestionReposted($uid, $q_qid),
							"reposterid" => $q_reposterid,
							"repostername" => $q_repostername
			);
			array_push($list, $ret);
			
			if($st2 != null){
				$st2->free_result();
				$st2->close();
				$st2 = null;
			}
		}
		
		$st->free_result();
		$st->close();
	} else {
		$st->free_result();
		$st->close();
		$conn->close();
		return 0;
	}
	
	$conn->close();
	
	return $list;
	
}

function removeQuestion($uid, $qid){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("update question set bActive = 0 where uid = ? AND iQID = ?");
	$st->bind_param("ii", $uid, $qid);
	$st->execute();
	$st->close();
	
	if($conn->affected_rows > 0){
		$st = $conn->prepare("update question set bActive = 0 where iRepostID = ?");
		$st->bind_param("i", $qid);
		$st->execute();
		$st->close();
		
		$st = $conn->prepare("update user_info set iQuestions = iQuestions - 1 where uid = ?");
		$st->bind_param("i", $uid);
		$st->execute();
		$st->close();
	}
	
	$conn->close();
}

function removeRepost($uid, $qid){
	
	if(getQuestionReposted($uid, $qid) == 0)
		return;
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("update question set bActive = 0 where uid = ? AND iRepostID = ?");
	$st->bind_param("ii", $uid, $qid);
	$st->execute();
	$st->close();
	
	$st = $conn->prepare("update question set iReqs = iReqs - 1 where iQID = ?");
	$st->bind_param("i", $qid);
	$st->execute();
	$st->close();
	
	$conn->close();
}

function setFavQuestion($uid, $qid){
	
	if(getQuestionFaved($uid, $qid) == 1)
		return;
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("insert into fav values (?,?)");
	$st->bind_param("ii", $uid, $qid);
	$st->execute();
	
	$st->close();
	$st = $conn->prepare("update question set iFavs = iFavs + 1 where iQID = ?");
	$st->bind_param("i", $qid);
	$st->execute();
	
	$st->close();
	$conn->close();
	
	insertFavNotification($uid, $qid);
}

function setUnfavQuestion($uid, $qid){
	
	if(getQuestionFaved($uid, $qid) == 0)
		return;
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("delete from fav where uid = ? AND iQID = ?");
	$st->bind_param("ii", $uid, $qid);
	$st->execute();
	
	$st->close();
	$st = $conn->prepare("update question set iFavs = iFavs - 1 where iQID = ?");
	$st->bind_param("i", $qid);
	$st->execute();
	
	$st->close();
	$conn->close();
}

function setReportQuestion($uid, $qid){
	
	if(getQuestionReported($uid, $qid) == 1)
		return;
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("insert into report_question values (?,?)");
	$st->bind_param("ii", $uid, $qid);
	$st->execute();
	
	$st->close();
	$st = $conn->prepare("update question set iReports = iReports + 1 where iQID = ?");
	$st->bind_param("i", $qid);
	$st->execute();
	
	$st->close();
	$conn->close();
}

function repostQuestion($uid, $qid){
	
	if(getQuestionReposted($uid, $qid) == 1)
		return;
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("insert into question values(-1, ?, \"\", 0, 0, 0, 0, 0, 0, \"\", 0, 0, 0, ?, 1, NOW())");
	$st->bind_param("ii", $uid, $qid);
	$st->execute();
	
	$st->close();
	$st = $conn->prepare("update question set iReqs = iReqs + 1 where iQID = ?");
	$st->bind_param("i", $qid);
	$st->execute();
	
	$st->close();
	$conn->close();
	
	insertReqNotification($uid, $qid);
}

function getUserNick($uid){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;

	$nick = null;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select sNick from user where uid = ?");
	$st->bind_param("i", $uid);
	$st->execute();
	$st->store_result();
	$st->bind_result($nick);
	$st->fetch();
	
	$st->close();
	$conn->close();
	
	return $nick;
}

function getUserName($uid){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;

	$name = null;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select sName from user_info where uid = ?");
	$st->bind_param("i", $uid);
	$st->execute();
	$st->store_result();
	$st->bind_result($name);
	$st->fetch();
	
	$st->close();
	$conn->close();
	
	return $name;
}

function check_base64_image($base64) {
    $img = imagecreatefromstring(base64_decode($base64));
    if (!$img) {
        return false;
    }

	$n = mt_rand(100000000, 999999999);
    imagejpeg($img, "temp//tmp" . $n . ".jpg");
    $info = getimagesize("temp//tmp" . $n . ".jpg");

    unlink("temp//tmp" . $n . ".jpg");

    if ($info[0] > 0 && $info[1] > 0 && $info['mime']) {
        return true;
    }

    return false;
}

function getProfilePhotoNo($uid){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$ppno = -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select pp from user_pp where uid = ?");
	$st->bind_param("i", $uid);
	$st->execute();
	$st->store_result();
	$st->bind_result($ppno);
	$st->fetch();
	
	$st->close();
	$conn->close();
	
	return $ppno;
}

function setProfilePhotoNo($uid, $ppno){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("update user_pp set pp = ? where uid = ?");
	$st->bind_param("ii", $ppno, $uid);
	$st->execute();
	
	$st->close();
	$conn->close();
}

function saveProfilePhoto($uid, $photo64){
	
	$ppno = getProfilePhotoNo($uid);
	$ppno++;
	
	$photo = base64_decode($photo64);
	file_put_contents("pp//pp".$uid."_".$ppno.".jpg", $photo);
	
	$pthumb = new \Eventviva\ImageResize("pp//pp".$uid."_".$ppno.".jpg");
	$pthumb->resizeToBestFit(64, 64);
	$pthumb->save("pp//pp".$uid."_".$ppno."_thumb.jpg");
	
	if($ppno > 0){
		unlink("pp//pp".$uid."_".($ppno - 1).".jpg");
		unlink("pp//pp".$uid."_".($ppno - 1)."_thumb.jpg");
	}
	
	setProfilePhotoNo($uid, $ppno);
}

function removeProfilePhoto($uid){
	
	$ppno = getProfilePhotoNo($uid);
	
	if($ppno == -1)
		return;
	
	unlink("pp//pp".$uid."_".$ppno.".jpg");
	unlink("pp//pp".$uid."_".$ppno."_thumb.jpg");
	setProfilePhotoNo($uid, -1);
}

function getProfilePhotoName($uid){
	
	$ppno = getProfilePhotoNo($uid);
	
	if($ppno == -1)
		return -1;
	
	$name = "pp".$uid."_".$ppno.".jpg";
	return $name;
}

function getFavList($uid, $qid, $p){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$page = $p * 50;
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select uid from fav where iQID = ? limit $page, 50");
	$st->bind_param("i", $qid);
	$st->execute();
	$st->store_result();
	
	$list = array();
	
	if ($st->num_rows > 0) {
		
		$st->bind_result($faver);
		
		while($st->fetch()){
			$user_info = getFollowInfo($faver);
			$block_state = checkUserBlocked($faver, $uid);
			$follow_state = getFollowState($uid, $faver);
			$pp = getProfilePhotoName($faver);
			
			$ret = array("uid" => $faver, "info" => $user_info, "block_state" => $block_state, "follow_state" => $follow_state, "pp" => $pp);
			array_push($list, $ret);
		}
	} else {
		$st->free_result();
		$st->close();
		$conn->close();		
		return 0;
	}
	
	$st->free_result();
	$st->close();
	$conn->close();
	
	return $list;
}

function getRepostList($uid, $qid, $p){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$page = $p * 50;
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select uid from question where bActive = 1 AND iRepostID = ? limit $page, 50");
	$st->bind_param("i", $qid);
	$st->execute();
	$st->store_result();
	
	$list = array();
	
	if ($st->num_rows > 0) {
		
		$st->bind_result($reposter);
		
		while($st->fetch()){
			$user_info = getFollowInfo($reposter);
			$block_state = checkUserBlocked($reposter, $uid);
			$follow_state = getFollowState($uid, $reposter);
			$pp = getProfilePhotoName($reposter);
			
			$ret = array("uid" => $reposter, "info" => $user_info, "block_state" => $block_state, "follow_state" => $follow_state, "pp" => $pp);
			array_push($list, $ret);
		}
	} else {
		$st->free_result();
		$st->close();
		$conn->close();		
		return 0;
	}
	
	$st->free_result();
	$st->close();
	$conn->close();
	
	return $list;
}

function isUserVoted($uid, $qid){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select * from votes where uid = ? AND iQID = ?");
	$st->bind_param("ii", $uid, $qid);
	$st->execute();
	$st->store_result();
	
	if($st->num_rows > 0){
		$st->close();
		$conn->close();
		return 1;
	} else {
		$st->close();
		$conn->close();
		return 0;		
	}
}

function getUserVote($uid, $qid){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$num = -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select iVoteNum from votes where uid = ? AND iQID = ?");
	$st->bind_param("ii", $uid, $qid);
	$st->execute();
	$st->store_result();
	$st->bind_result($num);
	$st->fetch();
	
	if($st->num_rows > 0){
		$st->close();
		$conn->close();
		return $num;
	} else {
		$st->close();
		$conn->close();
		return -1;		
	}
}

function setUserVote($uid, $qid, $opt) {
	
	if(isUserVoted($uid, $qid) == 1)
		return 0;
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("insert into votes values (?,?,?,NOW())");
	$st->bind_param("iii", $uid, $qid, $opt);
	$st->execute();
	$st->close();
	
	$opcolumn = "iOpt".($opt+1);
	$st = $conn->prepare("update options_result set $opcolumn = $opcolumn + 1 where iQID = ?");
	$st->bind_param("i", $qid);
	$st->execute();
	$st->close();
	
	$conn->close();
	
	return 1;
}

function getFollowingQuestions($uid, $p){
	date_default_timezone_set('Europe/Istanbul');
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$page = $p * 10;
	$conn->query("SET NAMES utf8mb4");
	$st = $conn->prepare("select * from question where (uid = ? OR uid IN (select uTarget from follow where uid = ?)) AND bActive = 1 order by date DESC limit $page, 10");
	$st->bind_param("ii", $uid, $uid);
	$st->execute();
	$st->store_result();
	
	if ($st->num_rows > 0) {
		
		$list = array();
		$st->bind_result($q_qid, $q_uid, $q_question, $q_anonym, $q_private, $q_options, $q_reqs, $q_favs, $q_reports, $q_tags, $q_simg, $q_latitude, $q_longitude, $q_repostid, $q_active, $q_date);
		$date_now = new DateTime();
		
		while($st->fetch()){
			
			$q_reposterid = -1;
			$q_repostername = "";
			$st2 = null;
			
			if($q_qid == -1){
				$q_reposterid = $q_uid;
				$q_repostername = getUserName($q_uid);
				
				$st2 = $conn->prepare("select * from question where iQID = $q_repostid");
				$st2->execute();
				$st2->store_result();
				$st2->bind_result($q_qid, $q_uid, $q_question, $q_anonym, $q_private, $q_options, $q_reqs, $q_favs, $q_reports, $q_tags, $q_simg, $q_latitude, $q_longitude, $q_repostid, $q_active, $q_date);
				$st2->fetch();
			}
			
			$date_post = date_create_from_format('Y-m-d H:i:s', $q_date);
			$date_diff = $date_now->diff($date_post);
			
			$p_year = $date_diff->format('%y');
			$p_month = $date_diff->format('%m');
			$p_day = $date_diff->format('%d');
			$p_hour = $date_diff->format('%h');
			
			$q_locked = 0;
			
			if($p_year > 0 || $p_month > 0 || $p_day > 0)
				$q_locked = 1;
			
			$ret = array(	"uid" => $q_uid,
							"pp" => getProfilePhotoName($q_uid),
							"nick" => getUserNick($q_uid),
							"name" => getUserName($q_uid),
							"qid" => $q_qid,
							"question" => $q_question,
							"anonym" => $q_anonym,
							"private" => $q_private,
							"options" => $q_options,
							"reqs" => $q_reqs,
							"favs" => $q_favs,
							"tags" => $q_tags,
							"date" => $q_date,
							"locked" => $q_locked,
							"phour" => $p_hour,
							"s_options" => getQuestionOptions($q_qid),
							"votes" => getQuestionVotes($q_qid),
							"faved" => getQuestionFaved($uid, $q_qid),
							"reported" => getQuestionReported($uid, $q_qid),
							"voted" => isUserVoted($uid, $q_qid),
							"votenum" => getUserVote($uid, $q_qid),
							"comments" => getCommentCount($q_qid),
							"images" => getQuestionOptionImages($q_qid),
							"qimage" => $q_simg,
							"reposted" => getQuestionReposted($uid, $q_qid),
							"reposterid" => $q_reposterid,
							"repostername" => $q_repostername
			);
			array_push($list, $ret);
			
			if($st2 != null){
				$st2->free_result();
				$st2->close();
				$st2 = null;
			}
		}
		
		$st->free_result();
		$st->close();
	} else {
		$st->free_result();
		$st->close();
		$conn->close();
		return 0;
	}
	
	$conn->close();
	
	return $list;
	
}

function getTrendQuestions($uid, $p){
	date_default_timezone_set('Europe/Istanbul');
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$page = $p * 10;
	$conn->query("SET NAMES utf8mb4");
	$st = $conn->prepare("select * from question where iQID IN (select iQID from (select iQID, COUNT(*) cnt from votes where iQID IN (select iQID from question where bActive = 1 AND bPrivate = 0 AND iQID <> -1 AND (select TIMESTAMPDIFF(DAY, date, NOW())) = 0) GROUP BY iQID order by cnt DESC) as ids ) limit $page, 10");
	$st->execute();
	$st->store_result();
	
	if ($st->num_rows > 0) {
		
		$list = array();
		$st->bind_result($q_qid, $q_uid, $q_question, $q_anonym, $q_private, $q_options, $q_reqs, $q_favs, $q_reports, $q_tags, $q_simg, $q_latitude, $q_longitude, $q_repostid, $q_active, $q_date);
		$date_now = new DateTime();
		
		while($st->fetch()){
			
			$date_post = date_create_from_format('Y-m-d H:i:s', $q_date);
			$date_diff = $date_now->diff($date_post);
			
			$p_year = $date_diff->format('%y');
			$p_month = $date_diff->format('%m');
			$p_day = $date_diff->format('%d');
			$p_hour = $date_diff->format('%h');
			
			$ret = array(	"uid" => $q_uid,
							"pp" => getProfilePhotoName($q_uid),
							"nick" => getUserNick($q_uid),
							"name" => getUserName($q_uid),
							"qid" => $q_qid,
							"question" => $q_question,
							"anonym" => $q_anonym,
							"private" => $q_private,
							"options" => $q_options,
							"reqs" => $q_reqs,
							"favs" => $q_favs,
							"tags" => $q_tags,
							"date" => $q_date,
							"locked" => "0",
							"phour" => $p_hour,
							"s_options" => getQuestionOptions($q_qid),
							"votes" => getQuestionVotes($q_qid),
							"faved" => getQuestionFaved($uid, $q_qid),
							"reported" => getQuestionReported($uid, $q_qid),
							"voted" => isUserVoted($uid, $q_qid),
							"votenum" => getUserVote($uid, $q_qid),
							"comments" => getCommentCount($q_qid),
							"images" => getQuestionOptionImages($q_qid),
							"qimage" => $q_simg,
							"reposted" => getQuestionReposted($uid, $q_qid)
			);
			array_push($list, $ret);
		}
		
		$st->free_result();
		$st->close();
	} else {
		$st->free_result();
		$st->close();
		$conn->close();
		return 0;
	}
	
	$conn->close();
	
	return $list;
	
}

function getAllQuestions($uid, $p){
	date_default_timezone_set('Europe/Istanbul');
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$page = $p * 10;
	$conn->query("SET NAMES utf8mb4");
	$st = $conn->prepare("select * from question q JOIN (select sTag, cnt from trending_tags) t ON (q.sTags <> \"\" AND q.bActive = 1 AND q.iQID <> -1 AND (q.bPrivate = 0 OR q.uid = ? OR exists(select * from follow f where f.uid = ? AND f.uTarget = q.uid)) AND (select TIMESTAMPDIFF(DAY, q.date, NOW())) = 0 AND q.sTags LIKE CONCAT('%', t.sTag, '%')) GROUP BY iQID order by t.cnt DESC, q.date DESC limit $page,10");
	$st->bind_param("ii", $uid, $uid);
	$st->execute();
	$st->store_result();
	
	if ($st->num_rows > 0) {
		
		$list = array();
		$st->bind_result($q_qid, $q_uid, $q_question, $q_anonym, $q_private, $q_options, $q_reqs, $q_favs, $q_reports, $q_tags, $q_simg, $q_latitude, $q_longitude, $q_repostid, $q_active, $q_date, $q_temp1, $q_temp2);
		$date_now = new DateTime();
		
		while($st->fetch()){
			
			$date_post = date_create_from_format('Y-m-d H:i:s', $q_date);
			$date_diff = $date_now->diff($date_post);
			
			$p_year = $date_diff->format('%y');
			$p_month = $date_diff->format('%m');
			$p_day = $date_diff->format('%d');
			$p_hour = $date_diff->format('%h');
			
			$ret = array(	"uid" => $q_uid,
							"pp" => getProfilePhotoName($q_uid),
							"nick" => getUserNick($q_uid),
							"name" => getUserName($q_uid),
							"qid" => $q_qid,
							"question" => $q_question,
							"anonym" => $q_anonym,
							"private" => $q_private,
							"options" => $q_options,
							"reqs" => $q_reqs,
							"favs" => $q_favs,
							"tags" => $q_tags,
							"date" => $q_date,
							"locked" => "0",
							"phour" => $p_hour,
							"s_options" => getQuestionOptions($q_qid),
							"votes" => getQuestionVotes($q_qid),
							"faved" => getQuestionFaved($uid, $q_qid),
							"reported" => getQuestionReported($uid, $q_qid),
							"voted" => isUserVoted($uid, $q_qid),
							"votenum" => getUserVote($uid, $q_qid),
							"comments" => getCommentCount($q_qid),
							"images" => getQuestionOptionImages($q_qid),
							"qimage" => $q_simg,
							"reposted" => getQuestionReposted($uid, $q_qid)
			);
			array_push($list, $ret);
		}
		
		$st->free_result();
		$st->close();
	} else {
		$st->free_result();
		$st->close();
		$conn->close();
		return 0;
	}
	
	$conn->close();
	
	return $list;
	
}

function getInboxList($uid, $p){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$page = $p * 50;
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select iChatID, uid, uTarget from chat where bActive = 1 AND (uid = ? OR uTarget = ?) order by datelastmsg DESC limit $page, 50");
	$st->bind_param("ii", $uid, $uid);
	$st->execute();
	$st->store_result();
	
	$list = array();
	
	if ($st->num_rows > 0) {
		
		$st->bind_result($cid, $cuid, $ctid);
		
		while($st->fetch()){
			$target = $cuid;
			if($uid == $cuid)
				$target = $ctid;
			
			$msgcount = getMessageCount($cid);
			$user_info = getFollowInfo($target);
			$block_state = checkUserBlocked($target, $uid);
			$pp = getProfilePhotoName($target);
			
			$ret = array("cid" => $cid, "uid" => $target, "info" => $user_info, "block_state" => $block_state, "pp" => $pp, "msgcount" => $msgcount);
			array_push($list, $ret);
		}
	} else {
		$st->free_result();
		$st->close();
		$conn->close();
		return 0;
	}
	
	$st->free_result();
	$st->close();
	$conn->close();
	
	return $list;
}

function getMessageList($uid, $tid, $p) {
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$page = $p * 20;
	$conn->query("SET NAMES utf8mb4");
	if($p == 0)
		$st = $conn->prepare("select uid, uTarget, sMessage, date from (select * from messages where iChatID IN (select iChatID from chat where bActive = 1 AND ((uid = ? AND uTarget = ?) OR (uTarget = ? AND uid = ?)) order by datelastmsg DESC) order by date DESC limit $page,20) tmp order by date ASC");
	else
		$st = $conn->prepare("select uid, uTarget, sMessage, date from (select * from messages where iChatID IN (select iChatID from chat where bActive = 1 AND ((uid = ? AND uTarget = ?) OR (uTarget = ? AND uid = ?)) order by datelastmsg DESC) order by date DESC limit $page,20) tmp order by date DESC");
	$st->bind_param("iiii", $uid, $tid, $uid, $tid);
	$st->execute();
	$st->store_result();
	
	$list = array();
	
	if ($st->num_rows > 0) {
		
		$st->bind_result($m_uid, $m_tid, $m_msg, $m_date);
		
		while($st->fetch()){
			$ret = array("uid" => $m_uid, "tid" => $m_tid, "msg" => $m_msg, "date" => $m_date);
			array_push($list, $ret);
		}
	} else {
		$st->free_result();
		$st->close();
		$conn->close();
		return 0;
	}
	
	$st->free_result();
	$st->close();
	$conn->close();
	
	return $list;
}

function sendMessage($uid, $tid, $msg) {
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;

	if(checkUserBlocked($tid, $uid) == 1)
		return 0;
	
	$conn->query("SET NAMES utf8mb4");
	$st = $conn->prepare("select iChatID from chat where bActive = 1 AND ((uid = ? AND uTarget = ?) OR (uTarget = ? AND uid = ?))");
	$st->bind_param("iiii", $uid, $tid, $uid, $tid);
	$st->execute();
	$st->store_result();
	
	if ($st->num_rows > 0) {
		$st->bind_result($cid);
		$st->fetch();
		
		$st->free_result();
		$st->close();
		$st = $conn->prepare("insert into messages values ($cid, ?, ?, ?, NOW())");
		$st->bind_param("iis", $uid, $tid, $msg);
		$st->execute();
		
		$st->close();
		$st = $conn->prepare("update chat set datelastmsg = NOW() where iChatID = $cid");
		$st->execute();		
	} else {
		$st->free_result();
		$st->close();
		$st = $conn->prepare("select * from chat");
		$st->execute();
		$st->store_result();
		$ncid = $st->num_rows;
		
		$st->free_result();
		$st->close();
		$st = $conn->prepare("insert into chat values ($ncid, ?, ?, 1, NOW())");
		$st->bind_param("ii", $uid, $tid);
		$st->execute();	
		
		$st->close();
		$st = $conn->prepare("insert into messages values ($ncid, ?, ?, ?, NOW())");
		$st->bind_param("iis", $uid, $tid, $msg);
		$st->execute();
	}
	
	$st->close();
	$conn->close();	
	return 1;
}

function removeChat($uid, $cid) {
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("update chat set bActive = 0 where iChatID = ? AND (uid = ? OR uTarget = ?)");
	$st->bind_param("iii", $cid, $uid, $uid);
	$st->execute();	
	
	$st->close();
	$conn->close();	
}

function getMessageCount($cid) {
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select * from messages where iChatID = ?");
	$st->bind_param("i", $cid);
	$st->execute();	
	$st->store_result();
	
	$cnt = $st->num_rows;
	
	$st->free_result();
	$st->close();
	$conn->close();	
	
	return $cnt;
}

function getQuestion($uid, $qid) {
	date_default_timezone_set('Europe/Istanbul');
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES utf8mb4");
	$st = $conn->prepare("select * from question where iQID = ?");
	$st->bind_param("i", $qid);
	$st->execute();
	$st->store_result();
	
	if ($st->num_rows > 0) {
		
		$st->bind_result($q_qid, $q_uid, $q_question, $q_anonym, $q_private, $q_options, $q_reqs, $q_favs, $q_reports, $q_tags, $q_simg, $q_latitude, $q_longitude, $q_repostid, $q_active, $q_date);
		$st->fetch();
		
		$date_now = new DateTime();	
		$date_post = date_create_from_format('Y-m-d H:i:s', $q_date);
		$date_diff = $date_now->diff($date_post);
			
		$p_year = $date_diff->format('%y');
		$p_month = $date_diff->format('%m');
		$p_day = $date_diff->format('%d');
		$p_hour = $date_diff->format('%h');
		
		$q_locked = 0;
			
		if($p_year > 0 || $p_month > 0 || $p_day > 0)
			$q_locked = 1;
			
		$ret = array(	"uid" => $q_uid,
						"pp" => getProfilePhotoName($q_uid),
						"nick" => getUserNick($q_uid),
						"name" => getUserName($q_uid),
						"qid" => $q_qid,
						"question" => $q_question,
						"anonym" => $q_anonym,
						"private" => $q_private,
						"options" => $q_options,
						"reqs" => $q_reqs,
						"favs" => $q_favs,
						"tags" => $q_tags,
						"date" => $q_date,
						"locked" => $q_locked,
						"phour" => $p_hour,
						"s_options" => getQuestionOptions($q_qid),
						"votes" => getQuestionVotes($q_qid),
						"faved" => getQuestionFaved($uid, $q_qid),
						"reported" => getQuestionReported($uid, $q_qid),
						"voted" => isUserVoted($uid, $q_qid),
						"votenum" => getUserVote($uid, $q_qid),
						"comments" => getCommentCount($q_qid),
						"images" => getQuestionOptionImages($q_qid),
						"qimage" => $q_simg,
						"reposted" => getQuestionReposted($uid, $q_qid)
		);
		
		$st->free_result();
		$st->close();
		$conn->close();
		return $ret;
	} else {
		$st->free_result();
		$st->close();
		$conn->close();
		return 0;
	}	
}

function getQuestionOwner($qid){
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$owner = -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select uid from question where iQID = ?");
	$st->bind_param("i", $qid);
	$st->execute();
	$st->store_result();
	$st->bind_result($owner);
	$st->fetch();
	
	$st->free_result();
	$st->close();
	$conn->close();
	
	return $owner;	
}

function sendComment($uid, $qid, $votenum, $comment) {
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES utf8mb4");
	$st = $conn->prepare("select * from question where iQID = ? AND bActive = 1");
	$st->bind_param("i", $qid);
	$st->execute();
	$st->store_result();
	
	if ($st->num_rows > 0) {
		$st->free_result();
		$st->close();
		
		$st = $conn->prepare("select * from comment");
		$st->execute();
		$st->store_result();
		$ncid = $st->num_rows;
		
		$st->free_result();
		$st->close();
		
		$st = $conn->prepare("insert into comment values ($ncid, ?, ?, ?, ?, 0, 1, NOW())");
		$st->bind_param("iiis", $uid, $qid, $votenum, $comment);
		$st->execute();		
		$st->close();
		$conn->close();
		
		if(getQuestionOwner($qid) != $uid)
			insertCommentNotification($uid, $qid);
		
		return $ncid;
	} else {
		$st->free_result();
		$st->close();
		$conn->close();
		return -1;
	}
}

function getComment($cid) {
	date_default_timezone_set('Europe/Istanbul');
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES utf8mb4");
	$st = $conn->prepare("select * from comment where iCommentID = ?");
	$st->bind_param("i", $cid);
	$st->execute();
	$st->store_result();
	
	if ($st->num_rows > 0) {
		
		$st->bind_result($c_cid, $c_uid, $c_qid, $c_votenum, $c_comment, $c_reports, $c_active, $c_date);
		$st->fetch();
			
		$ret = array(	"cid" => $c_cid,
						"uid" => $c_uid,
						"qid" => $c_qid,
						"votenum" => $c_votenum,
						"comment" => $c_comment,
						"date" => $c_date
		);
		
		$st->free_result();
		$st->close();
		$conn->close();
		return $ret;
	} else {
		$st->free_result();
		$st->close();
		$conn->close();
		return 0;
	}	
}

function getCommentList($uid, $qid, $p) {
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$page = $p * 20;
	$conn->query("SET NAMES utf8mb4");
	$st = $conn->prepare("select * from comment where iQID = ? AND bActive = 1 order by date DESC limit $page,20");
	$st->bind_param("i", $qid);
	$st->execute();
	$st->store_result();
	
	$list = array();
	
	if ($st->num_rows > 0) {
		
		$st->bind_result($c_cid, $c_uid, $c_qid, $c_votenum, $c_comment, $c_reports, $c_active, $c_date);
		
		while($st->fetch()){
			$ret = array(	"cid" => $c_cid,
							"uid" => $c_uid,
							"qid" => $c_qid,
							"votenum" => $c_votenum,
							"comment" => $c_comment,
							"nick" => getUserNick($c_uid),
							"pp" => getProfilePhotoName($c_uid),
							"date" => $c_date
			);
			array_push($list, $ret);
		}
	} else {
		$st->free_result();
		$st->close();
		$conn->close();
		return 0;
	}
	
	$st->free_result();
	$st->close();
	$conn->close();
	
	return $list;
}

function getCommentCount($qid) {
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select * from comment where iQID = ? AND bActive = 1");
	$st->bind_param("i", $qid);
	$st->execute();
	$st->store_result();
	
	$count = $st->num_rows;
	
	$st->free_result();
	$st->close();
	$conn->close();
	
	return $count;
}

function removeComment($uid, $cid){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("update comment set bActive = 0 where uid = ? AND iCommentID = ?");
	$st->bind_param("ii", $uid, $cid);
	$st->execute();
	$st->close();
	$conn->close();
}

function getFavQuestions($uid, $tid, $p){
	date_default_timezone_set('Europe/Istanbul');
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$page = $p * 10;
	$conn->query("SET NAMES utf8mb4");
	$st = $conn->prepare("select * from question where bActive = 1 AND iQID <> -1 AND iQID IN (select iQID from fav where uid = ?) order by date DESC limit $page,10");
	$st->bind_param("i", $tid);
	$st->execute();
	$st->store_result();
	
	if ($st->num_rows > 0) {
		
		$list = array();
		$st->bind_result($q_qid, $q_uid, $q_question, $q_anonym, $q_private, $q_options, $q_reqs, $q_favs, $q_reports, $q_tags, $q_simg, $q_latitude, $q_longitude, $q_repostid, $q_active, $q_date);
		$date_now = new DateTime();
		
		while($st->fetch()){
			
			$date_post = date_create_from_format('Y-m-d H:i:s', $q_date);
			$date_diff = $date_now->diff($date_post);
			
			$p_year = $date_diff->format('%y');
			$p_month = $date_diff->format('%m');
			$p_day = $date_diff->format('%d');
			$p_hour = $date_diff->format('%h');
			
			$q_locked = 0;
			
			if($p_year > 0 || $p_month > 0 || $p_day > 0)
				$q_locked = 1;
			
			$ret = array(	"uid" => $q_uid,
							"pp" => getProfilePhotoName($q_uid),
							"nick" => getUserNick($q_uid),
							"name" => getUserName($q_uid),
							"qid" => $q_qid,
							"question" => $q_question,
							"anonym" => $q_anonym,
							"private" => $q_private,
							"options" => $q_options,
							"reqs" => $q_reqs,
							"favs" => $q_favs,
							"tags" => $q_tags,
							"date" => $q_date,
							"locked" => $q_locked,
							"phour" => $p_hour,
							"s_options" => getQuestionOptions($q_qid),
							"votes" => getQuestionVotes($q_qid),
							"faved" => getQuestionFaved($uid, $q_qid),
							"reported" => getQuestionReported($uid, $q_qid),
							"voted" => isUserVoted($uid, $q_qid),
							"votenum" => getUserVote($uid, $q_qid),
							"comments" => getCommentCount($q_qid),
							"images" => getQuestionOptionImages($q_qid),
							"qimage" => $q_simg,
							"reposted" => getQuestionReposted($uid, $q_qid)
			);
			array_push($list, $ret);
		}
		
		$st->free_result();
		$st->close();
	} else {
		$st->free_result();
		$st->close();
		$conn->close();
		return 0;
	}
	
	$conn->close();
	
	return $list;
	
}

function saveOptionImages($qid, $img1, $img2, $img3, $img4, $img5, $img6, $img7, $img8){
	
	$simg1 = "0";
	$simg2 = "0";
	$simg3 = "0";
	$simg4 = "0";
	$simg5 = "0";
	$simg6 = "0";
	$simg7 = "0";
	$simg8 = "0";
	
	if($img1 !== "0"){
		$img = base64_decode($img1);
		$simg1 = "q".$qid."_0.jpg";
		$thumb1 = "q".$qid."_0_thumb.jpg";
		file_put_contents("opimg//".$simg1, $img);
		
		$rimg1 = new \Eventviva\ImageResize("opimg//".$simg1);
		$rimg1->resizeToBestFit(64, 64);
		$rimg1->save("opimg//".$thumb1);
	}

	if($img2 !== "0"){
		$img = base64_decode($img2);
		$simg2 = "q".$qid."_1.jpg";
		$thumb2 = "q".$qid."_1_thumb.jpg";
		file_put_contents("opimg//".$simg2, $img);
		
		$rimg2 = new \Eventviva\ImageResize("opimg//".$simg2);
		$rimg2->resizeToBestFit(64, 64);
		$rimg2->save("opimg//".$thumb2);
	}
	
	if($img3 !== "0"){
		$img = base64_decode($img3);
		$simg3 = "q".$qid."_2.jpg";
		$thumb3 = "q".$qid."_2_thumb.jpg";
		file_put_contents("opimg//".$simg3, $img);
		
		$rimg3 = new \Eventviva\ImageResize("opimg//".$simg3);
		$rimg3->resizeToBestFit(64, 64);
		$rimg3->save("opimg//".$thumb3);
	}
	
	if($img4 !== "0"){
		$img = base64_decode($img4);
		$simg4 = "q".$qid."_3.jpg";
		$thumb4 = "q".$qid."_3_thumb.jpg";
		file_put_contents("opimg//".$simg4, $img);
		
		$rimg4 = new \Eventviva\ImageResize("opimg//".$simg4);
		$rimg4->resizeToBestFit(64, 64);
		$rimg4->save("opimg//".$thumb4);
	}
	
	if($img5 !== "0"){
		$img = base64_decode($img5);
		$simg5 = "q".$qid."_4.jpg";
		$thumb5 = "q".$qid."_4_thumb.jpg";
		file_put_contents("opimg//".$simg5, $img);
		
		$rimg5 = new \Eventviva\ImageResize("opimg//".$simg5);
		$rimg5->resizeToBestFit(64, 64);
		$rimg5->save("opimg//".$thumb5);
	}
	
	if($img6 !== "0"){
		$img = base64_decode($img6);
		$simg6 = "q".$qid."_5.jpg";
		$thumb6 = "q".$qid."_5_thumb.jpg";
		file_put_contents("opimg//".$simg6, $img);
		
		$rimg6 = new \Eventviva\ImageResize("opimg//".$simg6);
		$rimg6->resizeToBestFit(64, 64);
		$rimg6->save("opimg//".$thumb6);
	}
	
	if($img7 !== "0"){
		$img = base64_decode($img7);
		$simg7 = "q".$qid."_6.jpg";
		$thumb7 = "q".$qid."_6_thumb.jpg";
		file_put_contents("opimg//".$simg7, $img);
		
		$rimg7 = new \Eventviva\ImageResize("opimg//".$simg7);
		$rimg7->resizeToBestFit(64, 64);
		$rimg7->save("opimg//".$thumb7);
	}
	
	if($img8 !== "0"){
		$img = base64_decode($img8);
		$simg8 = "q".$qid."_7.jpg";
		$thumb8 = "q".$qid."_7_thumb.jpg";
		file_put_contents("opimg//".$simg8, $img);
		
		$rimg8 = new \Eventviva\ImageResize("opimg//".$simg8);
		$rimg8->resizeToBestFit(64, 64);
		$rimg8->save("opimg//".$thumb8);
	}
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("insert into options_img values ($qid, ?, ?, ?, ?, ?, ?, ?, ?)");
	$st->bind_param("ssssssss", $simg1, $simg2, $simg3, $simg4, $simg5, $simg6, $simg7, $simg8);
	$st->execute();
	
	$st->close();
	$conn->close();
}

function getQuestionOptionImages($qid){
	$conn = connectDB();
	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$ret = $conn->query("select * from options_img where iQID = $qid");
	
	if($ret->num_rows > 0)
		$opimg = $ret->fetch_row();
	else
		$opimg = array(strval($qid), "0", "0", "0", "0", "0", "0", "0", "0");
	
	$conn->close();
	return $opimg;
}

function getLocationQuestions($uid, $lat, $lon, $p){
	date_default_timezone_set('Europe/Istanbul');
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$page = $p * 10;
	$conn->query("SET NAMES utf8mb4");
	$st = $conn->prepare("select * from question q where bActive = 1 AND iQID <> -1 AND (bPrivate = 0 OR uid = ? OR exists(select * from follow f where f.uid = ? AND f.uTarget = q.uid)) AND (((acos(sin((?*pi()/180)) * sin((latitude*pi()/180))+cos((?*pi()/180)) * cos((latitude*pi()/180)) * cos(((?- longitude)*pi()/180))))*180/pi())*60*1.1515*1.609344) <= 3.0 order by date desc limit $page, 10");
	$st->bind_param("iiddd", $uid, $uid, $lat, $lat, $lon);
	$st->execute();
	$st->store_result();
	
	if ($st->num_rows > 0) {
		
		$list = array();
		$st->bind_result($q_qid, $q_uid, $q_question, $q_anonym, $q_private, $q_options, $q_reqs, $q_favs, $q_reports, $q_tags, $q_simg, $q_latitude, $q_longitude, $q_repostid, $q_active, $q_date);
		$date_now = new DateTime();
		
		while($st->fetch()){
			
			$date_post = date_create_from_format('Y-m-d H:i:s', $q_date);
			$date_diff = $date_now->diff($date_post);
			
			$p_year = $date_diff->format('%y');
			$p_month = $date_diff->format('%m');
			$p_day = $date_diff->format('%d');
			$p_hour = $date_diff->format('%h');
			
			$q_locked = 0;
			
			if($p_year > 0 || $p_month > 0 || $p_day > 0)
				$q_locked = 1;
			
			$ret = array(	"uid" => $q_uid,
							"pp" => getProfilePhotoName($q_uid),
							"nick" => getUserNick($q_uid),
							"name" => getUserName($q_uid),
							"qid" => $q_qid,
							"question" => $q_question,
							"anonym" => $q_anonym,
							"private" => $q_private,
							"options" => $q_options,
							"reqs" => $q_reqs,
							"favs" => $q_favs,
							"tags" => $q_tags,
							"date" => $q_date,
							"locked" => $q_locked,
							"phour" => $p_hour,
							"s_options" => getQuestionOptions($q_qid),
							"votes" => getQuestionVotes($q_qid),
							"faved" => getQuestionFaved($uid, $q_qid),
							"reported" => getQuestionReported($uid, $q_qid),
							"voted" => isUserVoted($uid, $q_qid),
							"votenum" => getUserVote($uid, $q_qid),
							"comments" => getCommentCount($q_qid),
							"images" => getQuestionOptionImages($q_qid),
							"qimage" => $q_simg,
							"reposted" => getQuestionReposted($uid, $q_qid)
			);
			array_push($list, $ret);
		}
		
		$st->free_result();
		$st->close();
	} else {
		$st->free_result();
		$st->close();
		$conn->close();
		return 0;
	}
	
	$conn->close();
	
	return $list;
	
}

function getTrendingTags() {
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES utf8mb4");
	$st = $conn->prepare("select sTag from trending_tags");
	$st->execute();
	$st->store_result();
	
	$tags = array();
	
	if ($st->num_rows > 0) {
		
		$st->bind_result($tag);
		
		while($st->fetch()){
			$ret = array("tag" => $tag);
			array_push($tags, $ret);
		}
	} else {
		$st->free_result();
		$st->close();
		$conn->close();
		return 0;
	}
	
	$st->free_result();
	$st->close();
	$conn->close();
	
	return $tags;
}

function getTagQuestions($uid, $tag, $p) {
	date_default_timezone_set('Europe/Istanbul');
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$page = $p * 10;
	$conn->query("SET NAMES utf8mb4");
	$st = $conn->prepare("select * from question q where bActive = 1 AND iQID <> -1 AND (bPrivate = 0 OR uid = ? OR exists(select * from follow f where f.uid = ? AND f.uTarget = q.uid)) AND sTags <> \"\" AND (sTags LIKE ? OR sTags LIKE ? OR sTags LIKE ?) order by date DESC limit $page,10");
	$param1 = "%".$tag;
	$param2 = "%".$tag."#%";
	$param3 = "%".$tag." #%";
	$st->bind_param("iisss", $uid, $uid, $param1, $param2, $param3);
	$st->execute();
	$st->store_result();
	
	if ($st->num_rows > 0) {
		
		$list = array();
		$st->bind_result($q_qid, $q_uid, $q_question, $q_anonym, $q_private, $q_options, $q_reqs, $q_favs, $q_reports, $q_tags, $q_simg, $q_latitude, $q_longitude, $q_repostid, $q_active, $q_date);
		$date_now = new DateTime();
		
		while($st->fetch()){
			
			$date_post = date_create_from_format('Y-m-d H:i:s', $q_date);
			$date_diff = $date_now->diff($date_post);
			
			$p_year = $date_diff->format('%y');
			$p_month = $date_diff->format('%m');
			$p_day = $date_diff->format('%d');
			$p_hour = $date_diff->format('%h');
			
			$q_locked = 0;
			
			if($p_year > 0 || $p_month > 0 || $p_day > 0)
				$q_locked = 1;
			
			$ret = array(	"uid" => $q_uid,
							"pp" => getProfilePhotoName($q_uid),
							"nick" => getUserNick($q_uid),
							"name" => getUserName($q_uid),
							"qid" => $q_qid,
							"question" => $q_question,
							"anonym" => $q_anonym,
							"private" => $q_private,
							"options" => $q_options,
							"reqs" => $q_reqs,
							"favs" => $q_favs,
							"tags" => $q_tags,
							"date" => $q_date,
							"locked" => $q_locked,
							"phour" => $p_hour,
							"s_options" => getQuestionOptions($q_qid),
							"votes" => getQuestionVotes($q_qid),
							"faved" => getQuestionFaved($uid, $q_qid),
							"reported" => getQuestionReported($uid, $q_qid),
							"voted" => isUserVoted($uid, $q_qid),
							"votenum" => getUserVote($uid, $q_qid),
							"comments" => getCommentCount($q_qid),
							"images" => getQuestionOptionImages($q_qid),
							"qimage" => $q_simg,
							"reposted" => getQuestionReposted($uid, $q_qid)
			);
			array_push($list, $ret);
		}
		
		$st->free_result();
		$st->close();
	} else {
		$st->free_result();
		$st->close();
		$conn->close();
		return 0;
	}
	
	$conn->close();
	
	return $list;
	
}

function searchTag($tag, $p) {
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$page = $p * 10;
	$conn->query("SET NAMES utf8mb4");
	$st = $conn->prepare("select sTags, count(*) cnt from question where bActive = 1 AND sTags <> \"\" AND sTags like ? group by sTags order by cnt DESC limit $page,10");
	$param1 = "%".$tag."%";
	$st->bind_param("s", $param1);
	$st->execute();
	$st->store_result();
	
	$tags = array();
	
	if ($st->num_rows > 0) {
		
		$st->bind_result($tag, $cnt);
		
		while($st->fetch()){
			$ret = array("tag" => $tag, "cnt" => $cnt);
			array_push($tags, $ret);
		}
	} else {
		$st->free_result();
		$st->close();
		$conn->close();
		return 0;
	}
	
	$st->free_result();
	$st->close();
	$conn->close();
	
	return $tags;
}

function searchNick($nick, $p) {
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$page = $p * 10;
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select uid from user where bActive = 1 AND sNick like ? limit $page, 10");
	$param1 = $nick."%";
	$st->bind_param("s", $param1);
	$st->execute();
	$st->store_result();
	
	$list = array();
	
	if ($st->num_rows > 0) {
		
		$st->bind_result($s_uid);
		
		while($st->fetch()){
			$user_info = getFollowInfo($s_uid);
			$pp = getProfilePhotoName($s_uid);
			
			$ret = array("uid" => $s_uid, "info" => $user_info, "pp" => $pp);
			array_push($list, $ret);
		}
	} else {
		$st->free_result();
		$st->close();
		$conn->close();
		return 0;
	}
	
	$st->free_result();
	$st->close();
	$conn->close();
	
	return $list;	
}

function searchName($name, $p) {
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$page = $p * 10;
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select ui_uid from (select user_info.sName, user_info.uid as ui_uid, user.uid as u_uid, user.bActive from user_info, user where user_info.sName like ? AND user_info.uid = user.uid AND user.bActive = 1) as users limit $page, 10");
	$param1 = $name."%";
	$st->bind_param("s", $param1);
	$st->execute();
	$st->store_result();
	
	$list = array();
	
	if ($st->num_rows > 0) {
		
		$st->bind_result($s_uid);
		
		while($st->fetch()){
			$user_info = getFollowInfo($s_uid);
			$pp = getProfilePhotoName($s_uid);
			
			$ret = array("uid" => $s_uid, "info" => $user_info, "pp" => $pp);
			array_push($list, $ret);
		}
	} else {
		$st->free_result();
		$st->close();
		$conn->close();
		return 0;
	}
	
	$st->free_result();
	$st->close();
	$conn->close();
	
	return $list;	
}

function insertFollowNotification($uid, $target_uid){
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("insert into notifications values(?, ?, 10, NULL, NOW())");
	$st->bind_param("ii", $target_uid, $uid);
	$st->execute();
	$st->close();
	$conn->close();	
}

function insertSubscribeNotification($uid, $qid) {
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("insert into notifications (uid, uid2, type, param, date) select s.uid, ?, 12, ?, NOW() from subscribe as s where s.uTarget = ?");
	$st->bind_param("iii", $uid, $qid, $uid);
	$st->execute();
	$st->close();
	$conn->close();	
}

function insertFavNotification($uid, $qid){
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("insert into notifications (uid, uid2, type, param, date) select q.uid, ?, 20, ?, NOW() from question as q where q.iQID = ?");
	$st->bind_param("iii", $uid, $qid, $qid);
	$st->execute();
	$st->close();
	$conn->close();		
}

function insertCommentNotification($uid, $qid){
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("insert into notifications (uid, uid2, type, param, date) select q.uid, ?, 21, ?, NOW() from question as q where q.iQID = ?");
	$st->bind_param("iii", $uid, $qid, $qid);
	$st->execute();
	$st->close();
	$conn->close();		
}

function insertReqNotification($uid, $qid){
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("insert into notifications (uid, uid2, type, param, date) select q.uid, ?, 22, ?, NOW() from question as q where q.iQID = ?");
	$st->bind_param("iii", $uid, $qid, $qid);
	$st->execute();
	$st->close();
	$conn->close();		
}

function insertAcceptNotification($uid, $target_uid){
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("insert into notifications values(?, ?, 13, NULL, NOW())");
	$st->bind_param("ii", $target_uid, $uid);
	$st->execute();
	$st->close();
	$conn->close();	
}

function updateNotifications($uid){
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("update notifications_check set date = NOW() where uid = ?");
	$st->bind_param("i", $uid);
	$st->execute();
	$st->close();
	$conn->close();		
}

function updateNotificationsTask($uid){
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("update notifications_check_task set date = NOW() where uid = ?");
	$st->bind_param("i", $uid);
	$st->execute();
	$st->close();
	$conn->close();		
}

function updateMessagesCheck($uid){
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("update messages_check set date = NOW() where uid = ?");
	$st->bind_param("i", $uid);
	$st->execute();
	$st->close();
	$conn->close();		
	
	updateMessagesCheckTask($uid);
}

function updateMessagesCheckTask($uid){
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("update messages_check_task set date = NOW() where uid = ?");
	$st->bind_param("i", $uid);
	$st->execute();
	$st->close();
	$conn->close();		
}

function getPendingCount($uid){
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select * from pending_follow where uTarget = ?");
	$st->bind_param("i", $uid);
	$st->execute();
	$st->store_result();
	
	$count = $st->num_rows;
	
	$st->free_result();
	$st->close();
	$conn->close();
		
	return $count;
}

function getNewPendingList($uid){
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select * from pending_follow where uTarget = ? AND date > (select date from notifications_check_task where uid = ?) order by date desc");
	$st->bind_param("ii", $uid, $uid);
	$st->execute();
	$st->store_result();
	
	$list = array();
	
	if ($st->num_rows > 0) {
		
		$st->bind_result($p_uid, $p_target, $p_date);
		
		while($st->fetch()){
			$ret = array("uid" => $p_uid, "date" => $p_date, "name" => getUserName($p_uid), "pp" => getProfilePhotoName($p_uid));
			array_push($list, $ret);
		}
	} else {
		$st->free_result();
		$st->close();
		$conn->close();
		updateNotificationsTask($uid);
		return 0;
	}
	
	$st->free_result();
	$st->close();
	$conn->close();
	updateNotificationsTask($uid);
	
	return $list;	
}

function getNewMessages($uid){
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select iChatID, uid, date from messages where uTarget = ? AND date > (select date from messages_check_task where uid = ?) order by date desc");
	$st->bind_param("ii", $uid, $uid);
	$st->execute();
	$st->store_result();
	
	$list = array();
	
	if ($st->num_rows > 0) {
		
		$st->bind_result($m_cid, $m_uid, $m_date);
		
		while($st->fetch()){
			$ret = array("cid" => $m_cid, "uid" => $m_uid, "date" => $m_date, "name" => getUserName($m_uid), "pp" => getProfilePhotoName($m_uid));
			array_push($list, $ret);
		}
	} else {
		$st->free_result();
		$st->close();
		$conn->close();
		updateMessagesCheckTask($uid);
		return 0;
	}
	
	$st->free_result();
	$st->close();
	$conn->close();
	updateMessagesCheckTask($uid);
	
	return $list;	
}

function getNotifications($uid){
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select * from notifications where type <> 12 AND type <> 30 AND uid = ? order by date desc limit 0,50");
	$st->bind_param("i", $uid);
	$st->execute();
	$st->store_result();
	
	$list = array();
	
	if ($st->num_rows > 0) {
		
		$st->bind_result($n_uid, $n_uid2, $n_type, $n_param, $n_date);
		$date_last = getNotificationsLastCheck($uid);
		
		while($st->fetch()){
			
			$new = 0;
			$date_notification = date_create_from_format('Y-m-d H:i:s', $n_date);
			if($date_notification > $date_last)
				$new = 1;
			
			$ret = array("uid" => $n_uid, "uid2" => $n_uid2, "type" => $n_type, "param" => $n_param, "date" => $n_date, "new" => $new, "name" => getUserName($n_uid2), "pp" => getProfilePhotoName($n_uid2) );
			array_push($list, $ret);
		}
	} else {
		$st->free_result();
		$st->close();
		$conn->close();
		updateNotificationsTask($uid);
		return 0;
	}
	
	$st->free_result();
	$st->close();
	$conn->close();
	updateNotificationsTask($uid);
	
	return $list;
}

function getNotificationsLastCheck($uid){
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$date = null;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select date from notifications_check where uid = ?");
	$st->bind_param("i", $uid);
	$st->execute();
	$st->store_result();
	$st->bind_result($date);
	$st->fetch();
	
	$st->close();
	$conn->close();
	
	$dt = date_create_from_format('Y-m-d H:i:s', $date);
	return $dt;	
}

function checkNewNotifications($uid){
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select uid from notifications where uid = ? AND date > (select date from notifications_check_task where uid = ?)");
	$st->bind_param("ii", $uid, $uid);
	$st->execute();
	$st->store_result();
	
	if($st->num_rows > 0){
		$st->close();
		$conn->close();
		return 1;
	}else {
		$st->close();
		$conn->close();
		return 0;		
	}
}

function checkNewPendings($uid){
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select uTarget from pending_follow where uTarget = ? AND date > (select date from notifications_check_task where uid = ?)");
	$st->bind_param("ii", $uid, $uid);
	$st->execute();
	$st->store_result();
	
	if($st->num_rows > 0){
		$st->close();
		$conn->close();
		return 1;
	}else {
		$st->close();
		$conn->close();
		return 0;		
	}
}

function checkNewMessages($uid){
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select uid from messages where uTarget = ? AND date > (select date from messages_check_task where uid = ?)");
	$st->bind_param("ii", $uid, $uid);
	$st->execute();
	$st->store_result();
	
	if($st->num_rows > 0){
		$st->close();
		$conn->close();
		return 1;
	}else {
		$st->close();
		$conn->close();
		return 0;		
	}	
}

function checkMessages($uid){
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select uid from messages where uTarget = ? AND date > (select date from messages_check where uid = ?)");
	$st->bind_param("ii", $uid, $uid);
	$st->execute();
	$st->store_result();
	
	if($st->num_rows > 0){
		$st->close();
		$conn->close();
		return 1;
	}else {
		$st->close();
		$conn->close();
		return 0;		
	}	
}

function getNewNotifications($uid){
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select * from notifications where uid = ? AND date > (select date from notifications_check_task where uid = ?) order by date asc");
	$st->bind_param("ii", $uid, $uid);
	$st->execute();
	$st->store_result();
	
	$list = array();
	
	if ($st->num_rows > 0) {
		
		$st->bind_result($n_uid, $n_uid2, $n_type, $n_param, $n_date);
		
		while($st->fetch()){
			$ret = array("uid" => $n_uid, "uid2" => $n_uid2, "type" => $n_type, "param" => $n_param, "date" => $n_date, "name" => getUserName($n_uid2), "pp" => getProfilePhotoName($n_uid2) );
			array_push($list, $ret);
		}
	} else {
		$st->free_result();
		$st->close();
		$conn->close();
		return 0;
	}
	
	$st->free_result();
	$st->close();
	$conn->close();
	
	return $list;	
}

function getPendingUserList($uid, $p){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$page = $p * 50;
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select uid from pending_follow where uTarget = ? limit $page, 50");
	$st->bind_param("i", $uid);
	$st->execute();
	$st->store_result();
	
	$list = array();
	
	if ($st->num_rows > 0) {
		
		$st->bind_result($pending);
		
		while($st->fetch()){
			$user_info = getFollowInfo($pending);
			$pp = getProfilePhotoName($pending);
			
			$ret = array("uid" => $pending, "info" => $user_info, "pp" => $pp);
			array_push($list, $ret);
		}
	} else {
		$st->free_result();
		$st->close();
		$conn->close();		
		return 0;
	}
	
	$st->free_result();
	$st->close();
	$conn->close();
	
	return $list;
}

function acceptPending($uid, $tid){
	if(getFollowState($tid, $uid) != 2)
		return;
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("delete from pending_follow where uid = ? AND uTarget = ?");
	$st->bind_param("ii", $tid, $uid);
	$st->execute();
	
	$st->close();
	$st = $conn->prepare("insert into follow values (?,?,NOW())");
	$st->bind_param("ii", $tid, $uid);
	$st->execute();
	
	$st->close();
	$conn->close();	
	
	insertAcceptNotification($uid, $tid);
}

function rejectPending($uid, $tid){
	if(getFollowState($tid, $uid) != 2)
		return;
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("delete from pending_follow where uid = ? AND uTarget = ?");
	$st->bind_param("ii", $tid, $uid);
	$st->execute();
	
	$st->close();
	$conn->close();	
}

function getUserID($nick){
	
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;

	$id = -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("select uid from user where sNick = ?");
	$st->bind_param("s", $nick);
	$st->execute();
	$st->store_result();
	if($st->num_rows > 0){
		$st->bind_result($id);
		$st->fetch();
	}
	
	$st->close();
	$conn->close();
	
	return $id;
}

function checkTaggedUsers($uid, $qtext, $qid){
	$spos = 0;
	$epos = 0;
	
	$nicks = array();
	
	while(true){
		$spos = strpos($qtext, '@', $spos);
		if($spos == FALSE)
			break;
		else {
			$epos = strpos($qtext, ' ', $spos);
			if($epos == TRUE){
				$len = $epos - ($spos + 1);
				$nick = substr($qtext, $spos + 1, $len);
			} else {
				$nick = substr($qtext, $spos + 1);
			}
			array_push($nicks, $nick);
			$spos++;
		}
	}
	
	foreach($nicks as $n){
		$id = getUserID($n);
		if($n != -1){
			insertTaggedNotification($id, $uid, $qid);
		}
	}
}

function insertTaggedNotification($uid, $uid2, $qid) {
	$conn = connectDB();

	if ($conn->connect_error)
		return -1;
	
	$conn->query("SET NAMES UTF8");
	$st = $conn->prepare("insert into notifications values(?, ?, 23, ?, NOW())");
	$st->bind_param("iii", $uid, $uid2, $qid);
	$st->execute();
	$st->close();
	$conn->close();	
}

?>
