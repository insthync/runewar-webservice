<?php
session_start();
include "function.php";
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8"/>
		<title>Rune War</title>
	</head>
	<body>
		<div style="margin:0 auto;width:300px;text-align:center;">
			<img src="textures/logo.png" width="100%" /><br />
			<strong>Login / Register</strong><br /><br />
			<form action="index.php" method="post">
				Email: <br />
				<input type="email" name="email" required /><br />
				<br />
				Password: <br />
				<input type="password" name="password" required /><br />
				<br />
				<input type="submit" value="Register / Login" />
			</form>
			<br />
			<a href="fb_index.php">Login with Facebook</a>
		</div>
<?php
if (isset($_POST['email']) && $_POST['password']) {
	$email = $_POST['email'];
	$password = md5($_POST['password']);
	$image_url = "";
	$query_users = mysql_query("SELECT * FROM users WHERE email='".$email."' AND password='".$password."'");
	$num_users = mysql_num_rows($query_users);
	if ($num_users > 0) {
		$data_users = mysql_fetch_assoc($query_users);
		$userid = $data_users['userid'];
		// Set token
		$token = md5(uniqid('runewar_'));
		$process = mysql_query("UPDATE users SET token='".$token."', date_login=NOW() WHERE userid='".$userid."'");
		// Then login
		if ($process) {
			// Insert stats data;
			insertStartStats($userid);
			// Insert data to inventory
			insertStartInventory($userid);
			mysql_query("UPDATE users SET image_url='".$image_url."' WHERE userid='".$userid."'");
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
			//$response = array('msgid' => MESSAGE_SUCCESS);
			//$response['userid'] = $userid;
			//$response['token'] = $token;
			//echo json_encode($response);
			$success = true;
		} else {
			// Error occurs
			//$response = array('msgid' => MESSAGE_ERROR);
			//echo json_encode($response);
			$success = false;
		}
	} else {
		// Set token and Register
		if (VerifyMailAddress($email)) {
			$token = md5(uniqid("runewar_"));
			$process = mysql_query("INSERT INTO users (password, email, token, heartnum, date_done, date_login, date_heart_refill) VALUES ('".$password."', '".$email."', '".$token."', '".START_HEART."', NOW(), NOW(), NOW())");
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
				//$response = array('msgid' => MESSAGE_SUCCESS);
				//$response['userid'] = $userid;
				//$response['token'] = $token;
				//echo json_encode($response);
				$success = true;
			} else {
				// Error occurs
				//$response = array('msgid' => MESSAGE_ERROR);
				//echo json_encode($response);
				$success = false;
			}
		} else {
			// Error occurs
			//$response = array('msgid' => MESSAGE_ERROR);
			//$response['description'] = "Error, invalid email.";
			//echo json_encode($response);
			$success = false;
		}
	}
}
if ($success) {
	if (isset($_SESSION['userid'])) {
		unset($_SESSION['userid']);
	}
	if (isset($_SESSION['token'])) {
		unset($_SESSION['token']);
	}
	$_SESSION['userid'] = $userid;
	$_SESSION['token'] = $token;
	header("Location: play2.php");
} else {
	echo "";
}
?>
	</body>
</html>