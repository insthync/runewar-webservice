<?php
include "function.php";
if (checkLogin()) {
	$userid = $_POST['userid'];
	insertStartInventory($userid);
	insertStartStats($userid);
	$targetid = $userid;
	$isTutorial = isTutorial($userid);
	if (isset($_POST['targetid']) && $_POST['targetid'] > 0) {
		$targetid = $_POST['targetid'];
		insertStartInventory($targetid);
		insertStartStats($targetid);
		$query_user = queryBattleData($targetid);
	} else {
		$query_user = queryBattleData($userid);
	}
	if (mysql_num_rows($query_user) > 0) {
		if ($targetid == $userid) {
			updateHeartNum($userid);
		}
		$data_user = mysql_fetch_assoc($query_user);
		$profile_image = $data_user['image_url'];
		if ($profile_image == null || strlen($profile_image) == 0)
			$profile_image = APPLICATION_PATH."textures/icon.png";
		$response = array('msgid' => MESSAGE_SUCCESS);
		$response['user'] = array(
			'userid' => $targetid, 
			'name' => '', 
			'level' => $data_user['level'], 
			'exp' => $data_user['exp'], 
			'gold' => $data_user['gold'], 
			'crystal' => $data_user['crystal'], 
			'heartnum' => $data_user['heartnum'],
			'date_heart_refill' => convert_datetime($data_user['date_heart_refill']),
			'profile_image' => $profile_image, 
			'usage_avatar' => array(
				0 => $data_user['avatar_char_archer'],
				1 => $data_user['avatar_char_assasin'],
				2 => $data_user['avatar_char_fighter'],
				3 => $data_user['avatar_char_knight'],
				4 => $data_user['avatar_char_hermit'],
				5 => $data_user['avatar_char_mage'],
			), 
			'usage_skill' => array(
				0 => $data_user['skill_char_archer'],
				1 => $data_user['skill_char_assasin'],
				2 => $data_user['skill_char_fighter'],
				3 => $data_user['skill_char_knight'],
				4 => $data_user['skill_char_hermit'],
				5 => $data_user['skill_char_mage'],
			),
			'used_achievement' => $data_user['used_achievement_id']
		);
		$response['friends'] = array();
		$query_friends = mysql_query("SELECT * FROM friends RIGHT JOIN users_info ON friends.userid2=users_info.userid WHERE friends.userid1='".$userid."' ORDER BY users_info.level DESC");
		while ($data_friends = mysql_fetch_assoc($query_friends)) {
			$friendid = $data_friends['userid2'];
			$data_friend = mysql_fetch_assoc(mysql_query("SELECT * FROM users RIGHT JOIN users_info ON users.userid=users_info.userid WHERE users.userid='".$friendid."'"));
			$profile_image = $data_friend['image_url'];
			if ($profile_image == null || strlen($profile_image) == 0)
				$profile_image = APPLICATION_PATH."textures/icon.png";
			$name = $data_friend['username'];
			if ($name == null || strlen($name) == 0) {
				$name = "Unknow";
			}
			$response['friends'][] = array(
				'userid' => $friendid,
				'name' => $name,
				'level' => $data_friend['level'],
				'profile_image' => $profile_image, 
				'used_achievement' => $data_friend['used_achievement_id']
			);
		}
		$response['isTutorial'] = $isTutorial;
		echo json_encode($response);
	} else {
		// Error occurs, users not found
		$response = array('msgid' => MESSAGE_ERROR);
		$response['description'] = "Error, users not found.";
		echo json_encode($response);
	}
} else {
	// Warn user to relogin
	$response = array('msgid' => MESSAGE_ERROR_NOTSIGNED);
	echo json_encode($response);
}
?>