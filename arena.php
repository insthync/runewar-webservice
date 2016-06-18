<?php
include "function.php";
if (checkLogin()) {
	$userid = $_POST['userid'];
	$query_user = queryBattleData($userid);
	$isTutorial = isTutorial($userid);
	if (mysql_num_rows($query_user) > 0) {
		$data_user = mysql_fetch_assoc($query_user);
		$response = array('msgid' => MESSAGE_SUCCESS);
		$response['targets'] = array();
		//$query_friends = mysql_query("SELECT * FROM friends WHERE userid1='".$userid."' ORDER BY RAND()");
		//while ($data_friends = mysql_fetch_assoc($query_friends)) {
			//$friendid = $data_friends['userid2'];
			//$data_friend = mysql_fetch_assoc(mysql_query("SELECT * FROM users RIGHT JOIN users_info ON users.userid=users_info.userid WHERE users.userid='".$friendid."'"));
		$query_users = mysql_query("SELECT * FROM users_info WHERE userid!='".$userid."' ORDER BY RAND()");
		while ($data_users = mysql_fetch_assoc($query_users)) {
			$friendid = $data_users['userid'];
			$data_friend = mysql_fetch_assoc(mysql_query("SELECT * FROM users RIGHT JOIN users_info ON users.userid=users_info.userid WHERE users.userid='".$friendid."'"));
			if (abs((int)$data_user['level'] - (int)$data_friend['level']) <= 3) {
				$profile_image = $data_friend['image_url'];
				if ($profile_image == null || strlen($profile_image) == 0)
					$profile_image = APPLICATION_PATH."textures/icon.png";
				$name = $data_friend['username'];
				if ($name == null || strlen($name) == 0) {
					$name = "Unknow";
				}
				$response['targets'][] = array(
					'userid' => $friendid,
					'name' => $name,
					'level' => $data_friend['level'],
					'profile_image' => $profile_image, 
					'used_achievement' => $data_friend['used_achievement_id']
				);
			}
		}
		$response['isTutorial'] = $isTutorial;
		echo json_encode($response);
	} else {
		// Error occurs, users not found
		$response = array('msgid' => MESSAGE_ERROR);
		$response['description'] = "Error, users not found.";
		echo json_encode($response);
	}
}
?>