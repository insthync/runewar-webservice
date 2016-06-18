<?php
include "function.php";
if (checkLogin()) {
	$userid = $_POST['userid'];
	$query_user = queryBattleData($userid);
	if (mysql_num_rows($query_user) > 0) {
		$data_user = mysql_fetch_assoc($query_user);
		if (strlen($data_user['trueid']) > 0) {
			// Bring an user information
			$username = $data_user['username'];
			$fight_won = (int)$data_user['fight_won'];
			$fight_killed = (int)$data_user['fight_killed'];
			$spent_gold = (int)$data_user['spent_gold'];
			$spent_crystal = (int)$data_user['spent_crystal'];
			$trueid = $data_user['trueid'];
			$unlock_list = array();
			// Checking for achievements unlock
			// Check for first login
			
			$achievement_id = 100051;
			if (!HasTrueAchievement($userid, $achievement_id) && strlen($username) > 0) {
				if (UnlockTrueAchievement($userid, $trueid, $achievement_id))
					$unlock_list[] = $achievement_id;
			}
			// Check for Defender Lv.1
			$achievement_id = 100053;
			if (!HasTrueAchievement($userid, $achievement_id) && $fight_won >= 20) {
				if (UnlockTrueAchievement($userid, $trueid, $achievement_id))
					$unlock_list[] = $achievement_id;
			}
			// Check for Defender Lv.2
			$achievement_id = 100054;
			if (!HasTrueAchievement($userid, $achievement_id) && $fight_won >= 80) {
				if (UnlockTrueAchievement($userid, $trueid, $achievement_id))
					$unlock_list[] = $achievement_id;
			}
			// Check for Defender Lv.3
			$achievement_id = 100055;
			if (!HasTrueAchievement($userid, $achievement_id) && $fight_won >= 180) {
				if (UnlockTrueAchievement($userid, $trueid, $achievement_id))
					$unlock_list[] = $achievement_id;
			}
			// Check for Defender Lv.4
			$achievement_id = 100056;
			if (!HasTrueAchievement($userid, $achievement_id) && $fight_won >= 300) {
				if (UnlockTrueAchievement($userid, $trueid, $achievement_id))
					$unlock_list[] = $achievement_id;
			}
			// Check for Killer Lv.1
			$achievement_id = 100057;
			if (!HasTrueAchievement($userid, $achievement_id) && $fight_killed >= 100) {
				if (UnlockTrueAchievement($userid, $trueid, $achievement_id))
					$unlock_list[] = $achievement_id;
			}
			// Check for Killer Lv.2
			$achievement_id = 100058;
			if (!HasTrueAchievement($userid, $achievement_id) && $fight_killed >= 300) {
				if (UnlockTrueAchievement($userid, $trueid, $achievement_id))
					$unlock_list[] = $achievement_id;
			}
			// Check for Killer Lv.3
			$achievement_id = 100059;
			if (!HasTrueAchievement($userid, $achievement_id) && $fight_killed >= 600) {
				if (UnlockTrueAchievement($userid, $trueid, $achievement_id))
					$unlock_list[] = $achievement_id;
			}
			// Check for Killer Lv.4
			$achievement_id = 100060;
			if (!HasTrueAchievement($userid, $achievement_id) && $fight_killed >= 1000) {
				if (UnlockTrueAchievement($userid, $trueid, $achievement_id))
					$unlock_list[] = $achievement_id;
			}
			// Check for Gold Spender Lv.1
			$achievement_id = 100067;
			if (!HasTrueAchievement($userid, $achievement_id) && $spent_gold >= 5000) {
				if (UnlockTrueAchievement($userid, $trueid, $achievement_id))
					$unlock_list[] = $achievement_id;
			}
			// Check for Gold Spender Lv.2
			$achievement_id = 100068;
			if (!HasTrueAchievement($userid, $achievement_id) && $spent_gold >= 10000) {
				if (UnlockTrueAchievement($userid, $trueid, $achievement_id))
					$unlock_list[] = $achievement_id;
			}
			// Check for Gold Spender Lv.3
			$achievement_id = 100069;
			if (!HasTrueAchievement($userid, $achievement_id) && $spent_gold >= 20000) {
				if (UnlockTrueAchievement($userid, $trueid, $achievement_id))
					$unlock_list[] = $achievement_id;
			}
			// Check for Gold Spender Lv.4
			$achievement_id = 100070;
			if (!HasTrueAchievement($userid, $achievement_id) && $spent_gold >= 50000) {
				if (UnlockTrueAchievement($userid, $trueid, $achievement_id))
					$unlock_list[] = $achievement_id;
			}
			// Check for Crystal Spender Lv.1
			$achievement_id = 100071;
			if (!HasTrueAchievement($userid, $achievement_id) && $spent_crystal >= 100) {
				if (UnlockTrueAchievement($userid, $trueid, $achievement_id))
					$unlock_list[] = $achievement_id;
			}
			// Check for Crystal Spender Lv.2
			$achievement_id = 100072;
			if (!HasTrueAchievement($userid, $achievement_id) && $spent_crystal >= 400) {
				if (UnlockTrueAchievement($userid, $trueid, $achievement_id))
					$unlock_list[] = $achievement_id;
			}
			// Check for Crystal Spender Lv.3
			$achievement_id = 100073;
			if (!HasTrueAchievement($userid, $achievement_id) && $spent_crystal >= 900) {
				if (UnlockTrueAchievement($userid, $trueid, $achievement_id))
					$unlock_list[] = $achievement_id;
			}
			// Check for Crystal Spender Lv.4
			$achievement_id = 100074;
			if (!HasTrueAchievement($userid, $achievement_id) && $spent_crystal >= 1600) {
				if (UnlockTrueAchievement($userid, $trueid, $achievement_id))
					$unlock_list[] = $achievement_id;
			}
			$response = array('msgid' => MESSAGE_SUCCESS);
			$response['user'] = $data_user;
			$response['unlock_list'] = $unlock_list;
			echo json_encode($response);
		} else {
			// Error, no true id 
			$response = array('msgid' => MESSAGE_ERROR);
			$response['description'] = "Error, didn't GGDP user.";
			echo json_encode($response);
		}
	} else {
		// Error occurs, users not found
		$response = array('msgid' => MESSAGE_ERROR);
		$response['description'] = "Error, users not found.";
		echo json_encode($response);
	}
}
?>