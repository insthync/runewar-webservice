<?php
include "function.php";
if (checkLogin()) {
	$userid = $_POST['userid'];
	$num_friend_req = mysql_num_rows(mysql_query("SELECT * FROM friends_request WHERE userid2='".$userid."' AND is_seen='0'"));
	$num_battle_res = mysql_num_rows(mysql_query("SELECT * FROM battle_result WHERE userid='".$userid."' AND is_seen='0'"));
	$num_achievement_unlock = mysql_num_rows(mysql_query("SELECT * FROM users_achievements_true WHERE userid='".$userid."' AND is_seen='0'"));
	$response = array('msgid' => MESSAGE_SUCCESS);
	$response['friend_request'] = $num_friend_req;
	$response['battle_result'] = $num_battle_res;
	$response['achievement_unlock'] = $num_achievement_unlock;
	echo json_encode($response);
} else {
	// Warn user to relogin
	$response = array('msgid' => MESSAGE_ERROR_NOTSIGNED);
	echo json_encode($response);
}
?>