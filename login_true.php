<?php
include "function.php";
if (isset($_POST['trueid']) && strlen($_POST['trueid']) > 0) {
	$trueid = $_POST['trueid'];
} else {
	echo "Access denied";
	exit();
}
// Set token and Register
$true_service_url = TRUE_PATH ."GetUserProfile/". TRUE_APPID ."/". $trueid ."/". TRUE_APPSECRET;
$xml = file_get_contents($true_service_url);
if ($xmldata = simplexml_load_string($xml)) {
	$email = $xmldata->GetUserProfileResult->children("http://schemas.datacontract.org/2004/07/RestService")->Email;
	$query_users = mysql_query("SELECT * FROM users WHERE trueid='".$trueid."' OR email='".$email."'");
	$num_users = mysql_num_rows($query_users);
	if ($num_users > 0) {
		$data_users = mysql_fetch_assoc($query_users);
		$userid = $data_users['userid'];
		// If non true id or not equal update trueid
		if ($data_users['trueid'] != $trueid) {
			mysql_query("UPDATE users SET trueid='".$trueid."' WHERE userid='".$userid."'");
		}
		// Set token
		$token = md5(uniqid('runewar_'));
		$process = mysql_query("UPDATE users SET token='".$token."', date_login=NOW() WHERE trueid='".$trueid."'");
		// Then login
		if ($process) {
			// Insert stats data;
			insertStartStats($userid);
			// Insert data to inventory
			insertStartInventory($userid);
			// If battle no result, set to lose (battle offline mode)
			$query_battle = mysql_query("SELECT * FROM battle_match WHERE attackerid='".$userid."' AND is_end='0'");
			while ($data_battle = mysql_fetch_assoc($query_battle)) {
				// set to lose
				$battleid = $data_battle['battleid'];
				$data_battle_getting_defenderid = mysql_fetch_assoc(mysql_query("SELECT defenderid FROM battle_match WHERE battleid='".$battleid."'"));
				mysql_query("UPDATE battle_match SET is_end='1' WHERE battleid='".$battleid."'");
				mysql_query("INSERT INTO battle_result (battleid, userid, result_flag, is_seen, date_done) VALUES ('".$battleid."', '".$userid."', '".$battle_result_flags['lose']."', ".$battle_result_is_seen['yes'].", NOW())");
				mysql_query("INSERT INTO battle_result (battleid, userid, result_flag, is_seen, date_done) VALUES ('".$battleid."', '".$data_battle_getting_defenderid['defenderid']."', '".$battle_result_flags['win']."', ".$battle_result_is_seen['no'].", NOW())");
			}
			$response = array('msgid' => MESSAGE_SUCCESS);
			$response['userid'] = $userid;
			$response['token'] = $token;
			echo json_encode($response);
		} else {
			// Error occurs
			$response = array('msgid' => MESSAGE_ERROR);
			echo json_encode($response);
		}
	} else {
		if (VerifyMailAddress($email)) {
			$token = md5(uniqid("runewar_"));
			$process = mysql_query("INSERT INTO users (email, trueid, token, heartnum, date_done, date_login, date_heart_refill) VALUES ('".$email."', '".$trueid."', '".$token."', '".START_HEART."', NOW(), NOW(), NOW())");
			// Then login
			if ($process) {
				$userid = mysql_insert_id();
				// Insert user information
				mysql_query("INSERT INTO users_info (userid, level, exp, gold, crystal) VALUES ('".$userid."', '1', '0', '".START_GOLD."', '0')");
				// Insert stats data;
				insertStartStats($userid);
				// Insert data to inventory
				insertStartInventory($userid);
				// Insert usages
				mysql_query("INSERT INTO usage_avatar (userid, char_archer, char_assasin, char_fighter, char_knight, char_hermit, char_mage) VALUES ('".$userid."','1','1','1','1','1','1')");
				mysql_query("INSERT INTO usage_skill (userid, char_archer, char_assasin, char_fighter, char_knight, char_hermit, char_mage) VALUES ('".$userid."','1','1','1','1','1','1')");
				$response = array('msgid' => MESSAGE_SUCCESS);
				$response['userid'] = $userid;
				$response['token'] = $token;
				echo json_encode($response);
			} else {
				// Error occurs
				$response = array('msgid' => MESSAGE_ERROR);
				echo json_encode($response);
			}
		} else {
			// Error occurs
			$response = array('msgid' => MESSAGE_ERROR);
			$response['description'] = "Error, invalid email.";
			echo json_encode($response);
		}
	}
} else {
	// Error occurs
	$response = array('msgid' => MESSAGE_ERROR);
	$response['description'] = "Error, can't load xml.";
	echo json_encode($response);
}
?>