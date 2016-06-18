<?php
// This file use in battle offline mode
include "function.php";
if (checkLogin()) {
	$attackerid = $userid = $_POST['userid'];
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
	$isTutorial = isTutorial($userid);
	if (mysql_num_rows($query_attacker) > 0 && mysql_num_rows($query_defender) > 0 && $attackerid != $defenderid || $defenderid < 0) {
		$data_attacker = mysql_fetch_assoc($query_attacker);
		$data_defender = mysql_fetch_assoc($query_defender);
		if ($data_attacker['heartnum'] > 0) {
			$query = mysql_query("INSERT INTO battle_match (attackerid, defenderid, token, date_done) VALUES ('".$attackerid."', '".$defenderid."', '".$token."', NOW())");
			if ($query) {
				$battleid = mysql_insert_id();
				// Sending all character information
				$profile_image = $data_attacker['image_url'];
				if ($profile_image == null || strlen($profile_image) == 0)
					$profile_image = APPLICATION_PATH."textures/icon.png";
				$response = array('msgid' => MESSAGE_SUCCESS);
				$response['battleid'] = $battleid;
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
				if ($defenderid < 0) {
					$level = $data_attacker['level'];
					switch ($defenderid) {
						case -1:
							// level less than player 3 level
							$level -= 3;
						break;
						case -2:
							// level less than player 2 level
							$level -= 2;
						break;
						case -3:
							// level less than player 1 level
							$level -= 1;
						break;
						case -4:
							// level equal to player
							$level -= 0;
						break;
						case -5:
							// level more than player 1 level
							$level += 1;
						break;
						case -6:
							// level more than player 2 level
							$level += 2;
						break;
						case -7:
							// level more than player 3 level
							$level += 3;
						case -8:
							// Tutorial
							$level = 1;
						break;
					}
					if ($level <= 0) {
						$level = $data_attacker['level'];
					}
					$profile_image = APPLICATION_PATH."textures/icon.png";
					$response['defender'] = array(
						'userid' => $defenderid, 
						'name' => 'AI', 
						'level' => $level, 
						'exp' => 0, 
						'gold' => 0, 
						'crystal' => 0, 
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
						)
					);
				} else {
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
				}
				//$response['debug_data_attacker'] = $data_attacker;
				//$response['debug_data_defender'] = $data_defender;
				$response['isTutorial'] = $isTutorial;
				echo json_encode($response);
			} else {
				// Error occurs
				$response = array('msgid' => MESSAGE_ERROR);
				$response['description'] = "Error occurs while query match.";
				echo json_encode($response);
			}
		} else {
			// Error occurs
			$response = array('msgid' => MESSAGE_ERROR);
			$response['description'] = "Error occurs while query match.";
			echo json_encode($response);
		}
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