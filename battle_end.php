<?php
// This file use in battle offline mode
include "function.php";
if (checkLogin()) {
	$result_flag = $_POST['result_flag'];
	$battleid = $_POST['battleid'];
	$userid = $_POST['userid'];
	$attackerkills = (int)$_POST['attackerkills'];
	$defenderkills = (int)$_POST['defenderkills'];
	$max_combo = 1;
	$query_battle = mysql_query("SELECT * FROM battle_match WHERE battleid='".$battleid."' AND attackerid='".$userid."'");
	$num_battle = mysql_num_rows($query_battle);
	if ($num_battle > 0) {
		$data_battle = mysql_fetch_array($query_battle);
		if ($result_flag == $battle_result_flags['lose']) {
			$result_flag_2 = $battle_result_flags['win'];
		} else if ($result_flag == $battle_result_flags['win']) {
			$result_flag_2 = $battle_result_flags['lose'];
		} else {
			$result_flag_2 = $battle_result_flags['draw'];
		}
		// Insert battle result data
		$attackerid = $data_battle['attackerid'];
		$defenderid = $data_battle['defenderid'];
		mysql_query("UPDATE battle_match SET is_end='1' WHERE battleid='".$battleid."'");
		mysql_query("INSERT INTO battle_result (battleid, userid, result_flag, is_seen, date_done) VALUES ('".$battleid."', '".$attackerid."', '".$result_flag."', ".$battle_result_is_seen['yes'].", NOW())");
		mysql_query("INSERT INTO battle_result (battleid, userid, result_flag, is_seen, date_done) VALUES ('".$battleid."', '".$defenderid."', '".$result_flag_2."', ".$battle_result_is_seen['no'].", NOW())");
		// Rewards and heart use
		$rewards = array();
		// Stats
		$data_stats = mysql_fetch_assoc(mysql_query("SELECT * FROM users_stats WHERE userid='".$attackerid."'"));
		$fight_killed = (int)$data_stats['fight_killed'];
		$fight_won = (int)$data_stats['fight_won'];
		if ($result_flag == $battle_result_flags['win']) {
			// Add exp 
			$player_exp_array = split(',', $list_player_exp);
			$gain_exp_array = split(',', $list_gain_exp);
			$data_player = mysql_fetch_assoc(mysql_query("SELECT * FROM users_info WHERE userid='".$attackerid."'"));
			$player_level = $level = (int)$data_player['level'];
			$player_exp = (int)$data_player['exp'];
			$player_gold = (int)$data_player['gold'];
			if ($defenderid > 0) {
				// Player characters
				$data_player2 = mysql_fetch_assoc(mysql_query("SELECT * FROM users_info WHERE userid='".$defenderid."'"));
				$level = $data_player2['level'];
			} else {
				// NPCs
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
					break;
					case -8:
						// Tutorial
						$level = 1;
					break;
				}
				if ($level <= 0) {
					$level = $player_level;
				}
			}
			if ($level > count($gain_exp_array) - 1) {
				$level = count($gain_exp_array) - 1;
			}
			$gain_exp = $gain_exp_array[$level];
			$gain_gold = 100 + ($max_combo * 5);
			$player_exp += $gain_exp;
			$player_gold += $gain_gold;
			while ($player_level < count($player_exp_array) && $player_exp >= $player_exp_array[$player_level]) {
				// Level up !!
				$player_level += 1;
			}
			// Update new exp, level
			mysql_query("UPDATE users_info SET exp='".$player_exp."', level='".$player_level."', gold='".$player_gold."' WHERE userid='".$attackerid."'");
			// Set rewards
			mysql_query("INSERT INTO battle_reward (battleid, userid, reward_exp, reward_gold) VALUES ('".$battleid."', '".$attackerid."', '".$gain_exp."', '".$gain_gold."')");
			$rewards[] = array('exp' => $gain_exp, 'gold' => $gain_gold);
			// Update stats
			$fight_won += 1;
		} else {
			$data_player = mysql_fetch_assoc(mysql_query("SELECT heartnum FROM users WHERE userid='".$attackerid."'"));
			$heartnum = (int)$data_player['heartnum'] - 1;
			if ($heartnum < 0) {
				$heartnum = 0;
			}
			mysql_query("UPDATE users SET heartnum='".$heartnum."' WHERE userid='".$attackerid."'");
		}
		$data_player = mysql_fetch_assoc(mysql_query("SELECT heartnum FROM users WHERE userid='".$attackerid."'"));
		$heartnum = (int)$data_player['heartnum'];
		if ($heartnum >= 5) {
			mysql_query("UPDATE users SET date_heart_refill=NOW() WHERE userid='".$attackerid."'");
		}
		// Update stats
		$fight_killed += $attackerkills;
		mysql_query("UPDATE users_stats SET fight_killed='".$fight_killed."', fight_won='".$fight_won."' WHERE userid='".$attackerid."'");
		// Data response
		if (isTutorial($userid)) {
			$data_player = mysql_fetch_assoc(mysql_query("SELECT trueid, facebookid FROM users WHERE userid='".$userid."'"));
			dismissTutorial($userid, $data_player['trueid'], $data_player['facebookid']);
		}
		$response = array('msgid' => MESSAGE_SUCCESS);
		$response['result_flag'] = $result_flag;
		$response['rewards'] = $rewards;
		echo json_encode($response);
	} else {
		// Player try to hack ?
		$response = array('msgid' => MESSAGE_ERROR);
		echo json_encode($response);
	}
} else {
	// Warn user to relogin
	$response = array('msgid' => MESSAGE_ERROR_NOTSIGNED);
	echo json_encode($response);
}
?>