<?php
// This file use in battle offline mode
include "function.php";
if (checkLogin()) {
	$attackerid = $_POST['attackerid'];
	$defenderid = $_POST['defenderid'];
	if ($attackerid > 0) {
		insertStartInventory($attackerid);
		insertStartStats($attackerid);
	}
	if ($defenderid > 0) {
		insertStartInventory($defenderid);
		insertStartStats($defenderid);
	}
	$token = $_POST['token'];
	$query_attacker = queryBattleData($attackerid);
	$query_defender = queryBattleData($defenderid);
	if (mysql_num_rows($query_attacker) > 0 && mysql_num_rows($query_defender) > 0 && $attackerid != $defenderid) {
		$data_attacker = mysql_fetch_assoc($query_attacker);
		$data_defender = mysql_fetch_assoc($query_defender);
		// Sending all character information
		// Attacker data
		$profile_image = $data_attacker['image_url'];
		if ($profile_image == null || strlen($profile_image) == 0)
			$profile_image = APPLICATION_PATH."textures/icon.png";
		$response = array('msgid' => MESSAGE_SUCCESS);
		$response['attacker'] = array(
			'userid' => $attackerid, 
			'name' => '', 
			'level' => $data_attacker['level'], 
			'exp' => $data_attacker['exp'], 
			'gold' => $data_attacker['gold'], 
			'crystal' => $data_attacker['crystal'], 
			'profile_image' => $profile_image, 
			'usage_avatar' => array(
				0 => $data_attacker['avatar_char_archer'],
				1 => $data_attacker['avatar_char_assasin'],
				2 => $data_attacker['avatar_char_fighter'],
				3 => $data_attacker['avatar_char_knight'],
				4 => $data_attacker['avatar_char_hermit'],
				5 => $data_attacker['avatar_char_mage'],
			), 
			'usage_skill' => array(
				0 => $data_attacker['skill_char_archer'],
				1 => $data_attacker['skill_char_assasin'],
				2 => $data_attacker['skill_char_fighter'],
				3 => $data_attacker['skill_char_knight'],
				4 => $data_attacker['skill_char_hermit'],
				5 => $data_attacker['skill_char_mage'],
			),
			'used_achievement' => $data_attacker['used_achievement_id']
		);
		// Defender data
		$profile_image = $data_defender['image_url'];
		if ($profile_image == null || strlen($profile_image) == 0)
			$profile_image = APPLICATION_PATH."textures/icon.png";
		$response['defender'] = array(
			'userid' => $defenderid, 
			'name' => '', 
			'level' => $data_defender['level'], 
			'exp' => $data_defender['exp'], 
			'gold' => $data_defender['gold'], 
			'crystal' => $data_defender['crystal'], 
			'profile_image' => $profile_image, 
			'usage_avatar' => array(
				0 => $data_defender['avatar_char_archer'],
				1 => $data_defender['avatar_char_assasin'],
				2 => $data_defender['avatar_char_fighter'],
				3 => $data_defender['avatar_char_knight'],
				4 => $data_defender['avatar_char_hermit'],
				5 => $data_defender['avatar_char_mage'],
			), 
			'usage_skill' => array(
				0 => $data_defender['skill_char_archer'],
				1 => $data_defender['skill_char_assasin'],
				2 => $data_defender['skill_char_fighter'],
				3 => $data_defender['skill_char_knight'],
				4 => $data_defender['skill_char_hermit'],
				5 => $data_defender['skill_char_mage'],
			), 
			'used_achievement' => $data_defender['used_achievement_id']
		);
		echo json_encode($response);
	} else {
		// Error occurs, users not found
		$response = array('msgid' => MESSAGE_ERROR);
		$response['description'] = "Error, users not found.";
		$response['attackerid'] = $attackerid;
		$response['defenderid'] = $defenderid;
		echo json_encode($response);
	}
} else {
	// Warn user to relogin
	$response = array('msgid' => MESSAGE_ERROR_NOTSIGNED);
	echo json_encode($response);
}
?>