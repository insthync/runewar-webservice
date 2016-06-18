<?php
	include "message.php";
	// General
	define('APPLICATION_PATH', 'http://127.0.0.1/runewar/');
	define('CROSSDOMAIN_PATH', 'http://127.0.0.1/crossdomain.xml');
	define('SERVER_IP', '127.0.0.1');
	define('SERVER_PORT', '5501');

	define('FACEBOOK_APPID', '');
	define('FACEBOOK_APPSECRET', '');
	define('FACEBOOK_SDK_V4_SRC_DIR','/Facebook/');
	// Database connection
	define('DB_HOST', 'localhost');
	define('DB_USERNAME', 'root');
	define('DB_PASSWORD', 'password');
	define('DB_NAME', 'runewar');
	// Game starter
	define('START_HEART', 5);
	define('START_GOLD', 3000);
	$sql_conn = mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD);
	$is_sql_selected = mysql_select_db(DB_NAME);
	
	if (!$sql_conn || !$is_sql_selected) {
		echo "Can't connect to server.";
		exit();
	} else {
		// Set database to use utf8 charset
		mysql_query("SET NAMES 'utf8'");
	}
	
	$list_avatar_path = APPLICATION_PATH .'xml/list_char_archer_avatar.xml'
		.','. APPLICATION_PATH .'xml/list_char_assasin_avatar.xml'
		.','. APPLICATION_PATH .'xml/list_char_fighter_avatar.xml'
		.','. APPLICATION_PATH .'xml/list_char_knight_avatar.xml'
		.','. APPLICATION_PATH .'xml/list_char_hermit_avatar.xml'
		.','. APPLICATION_PATH .'xml/list_char_mage_avatar.xml';
		
	$list_skill_path = APPLICATION_PATH .'xml/list_char_archer_skill.xml'
		.','. APPLICATION_PATH .'xml/list_char_assasin_skill.xml'
		.','. APPLICATION_PATH .'xml/list_char_fighter_skill.xml'
		.','. APPLICATION_PATH .'xml/list_char_knight_skill.xml'
		.','. APPLICATION_PATH .'xml/list_char_hermit_skill.xml'
		.','. APPLICATION_PATH .'xml/list_char_mage_skill.xml';
		
	//$list_player_exp = '0,83,174,276,388,512,650,801,969,1154,1358,1584,1833,2107,2411,2746,3115,3523,3973,4470,5018,5624,6294,7028,7842,8740,9730,10824,12031,13363,14833,16456,18247,20224,22406';
	$list_player_exp = '0,83,257,533,921,1433,2083,2884,3853,5007,6365,7949,9782,11889,14300,17046,20161,23684,27657,32127,37145,42769,49063,56091,63933,72673,82403,93227,105258,118621,133454,149910,168157,188381,210787';
	
	$list_gain_exp = '83,83,91,102,112,124,138,151,168,185,204,226,249,274,304,335,369,408,450,497,548,606,667,737,814,898,990,1094,1207,1332,1470,1623,1791,1977,2182';
	
	$purse_types = array(
		'gold' => 0,
		'crystal' => 1
	);
		
	$battle_result_flags = array(
		'win' => 0,
		'lose' => 1,
		'draw' => 2
	);
		
	$battle_result_is_seen = array(
		'no' => 0,
		'yes' => 1
	);
	
	$char_indexes = array(
		'0' => 'char_archer',
		'1' => 'char_assasin',
		'2' => 'char_fighter',
		'3' => 'char_knight',
		'4' => 'char_hermit',
		'5' => 'char_mage'
	);
		
	function checkLogin() {
		$userid = $_POST['userid'];
		$token = $_POST['token'];
		$query = mysql_query("SELECT userid FROM users WHERE userid='".$userid."' AND token='".$token."'");
		return (mysql_num_rows($query) > 0);
	}
	
	function queryBattleData($userid) {
		return mysql_query("SELECT users.userid, users.username, users.trueid, users.facebookid, users.image_url, users.heartnum, users.used_achievement_id, users.date_heart_refill, users_info.level, users_info.exp, users_info.gold, users_info.crystal,
			usage_avatar.char_archer AS avatar_char_archer, usage_avatar.char_assasin AS avatar_char_assasin, usage_avatar.char_fighter AS avatar_char_fighter, usage_avatar.char_knight AS avatar_char_knight, usage_avatar.char_hermit AS avatar_char_hermit, usage_avatar.char_mage AS avatar_char_mage, 
			usage_skill.char_archer AS skill_char_archer, usage_skill.char_assasin AS skill_char_assasin, usage_skill.char_fighter AS skill_char_fighter, usage_skill.char_knight AS skill_char_knight, usage_skill.char_hermit AS skill_char_hermit, usage_skill.char_mage AS skill_char_mage, 
			users_stats.spent_gold, users_stats.spent_crystal, users_stats.fight_killed, users_stats.fight_won 
			FROM users RIGHT JOIN users_info ON users.userid=users_info.userid 
			RIGHT JOIN usage_avatar ON users.userid=usage_avatar.userid 
			RIGHT JOIN usage_skill ON users.userid=usage_skill.userid 
			RIGHT JOIN users_stats ON users.userid=users_stats.userid 
			WHERE users.userid='".$userid."'");
	}
	
	function getAvatarData($avatarid, $char_index) {
		$list_filepath = "xml/list_".$GLOBALS['char_indexes'][$char_index]."_avatar.xml";
		$retval = NULL;
		if (filesize($list_filepath) > 0) {
			$xmldata = simplexml_load_file($list_filepath);
			$avatar = $xmldata->xpath("//avatar[@id='".$avatarid."']");
			$filepath = $avatar[0]->attributes()->path;
			if (filesize($filepath) > 0) {
				$xmldata = simplexml_load_file($filepath);
				$name = $xmldata->avatar;
				$description = $xmldata->description;
				$expire = $xmldata->expire;
				$icon = $xmldata->icon;
				$price = $xmldata->price;
				$retval = array(
					'name' => $name->__toString(),
					'description' => $description->__toString(),
					'expire' => $expire->__toString(),
					'price_gold' => $price->attributes()->gold->__toString(),
					'price_crystal' => $price->attributes()->crystal->__toString(),
					'icon' => $icon->__toString()
				);
			} else {
				$retval = false;
			}
		} else {
			$retval = false;
		}
		return $retval;
	}
	
	function getSkillData($skillid, $char_index) {
		$list_filepath = "xml/list_".$GLOBALS['char_indexes'][$char_index]."_skill.xml";
		$retval = NULL;
		if (filesize($list_filepath) > 0) {
			$xmldata = simplexml_load_file($list_filepath);
			$skill = $xmldata->xpath("//skill[@id='".$skillid."']");
			$filepath = $skill[0]->attributes()->path;
			if (filesize($filepath) > 0) {
				$xmldata = simplexml_load_file($filepath);
				$name = $xmldata->skill;
				$description = $xmldata->description;
				$expire = $xmldata->expire;
				$icon = $xmldata->icon;
				$price = $xmldata->price;
				$retval = array(
					'name' => $name->__toString(),
					'description' => $description->__toString(),
					'expire' => $expire->__toString(),
					'price_gold' => $price->attributes()->gold->__toString(),
					'price_crystal' => $price->attributes()->crystal->__toString(),
					'icon' => $icon->__toString()
				);
			} else {
				$retval = false;
			}
		} else {
			$retval = false;
		}
		return $retval;
	}
	
	function getAllAvatarData($char_index) {
		$list_filepath = "xml/list_".$GLOBALS['char_indexes'][$char_index]."_avatar.xml";
		$retval = NULL;
		if (filesize($list_filepath) > 0) {
			$retval = array();
			$xmldata = simplexml_load_file($list_filepath);
			foreach ($xmldata->children() as $avatar) {
				$filepath = $avatar->attributes()->path;
				$id = $avatar->attributes()->id;
				if (filesize($filepath) > 0) {
					$xmldata = simplexml_load_file($filepath);
					$name = $xmldata->avatar;
					$description = $xmldata->description;
					$expire = $xmldata->expire;
					$icon = $xmldata->icon;
					$price = $xmldata->price;
					$retval[] = array(
						'id' => $id->__toString(),
						'name' => $name->__toString(),
						'description' => $description->__toString(),
						'expire' => $expire->__toString(),
						'price_gold' => $price->attributes()->gold->__toString(),
						'price_crystal' => $price->attributes()->crystal->__toString(),
						'icon' => $icon->__toString()
					);
				}
			}
		} else {
			$retval = false;
		}
		return $retval;
	}
	
	function getAllSkillData($char_index) {
		$list_filepath = "xml/list_".$GLOBALS['char_indexes'][$char_index]."_skill.xml";
		$retval = NULL;
		if (filesize($list_filepath) > 0) {
			$retval = array();
			$xmldata = simplexml_load_file($list_filepath);
			foreach ($xmldata->children() as $skill) {
				$filepath = $skill->attributes()->path;
				$id = $skill->attributes()->id;
				if (filesize($filepath) > 0) {
					$xmldata = simplexml_load_file($filepath);
					$name = $xmldata->skill;
					$description = $xmldata->description;
					$expire = $xmldata->expire;
					$icon = $xmldata->icon;
					$price = $xmldata->price;
					$retval[] = array(
						'id' => $id->__toString(),
						'name' => $name->__toString(),
						'description' => $description->__toString(),
						'expire' => $expire->__toString(),
						'price_gold' => $price->attributes()->gold->__toString(),
						'price_crystal' => $price->attributes()->crystal->__toString(),
						'icon' => $icon->__toString()
					);
				}
			}
		} else {
			$retval = false;
		}
		return $retval;
	}
	
	function checkAvatarUsable($userid, $char_index, $avatarid) {
		$query = mysql_query("SELECT * FROM inventory_avatar WHERE userid='".$userid."' AND char_index='".$char_index."' AND avatarid='".$avatarid."'");
		return (mysql_num_rows($query) > 0);
	}
	
	function checkSkillUsable($userid, $char_index, $skillid) {
		$query = mysql_query("SELECT * FROM inventory_skill WHERE userid='".$userid."' AND char_index='".$char_index."' AND skillid='".$skillid."'");
		return (mysql_num_rows($query) > 0);
	}
	
	function updateHeartNum($userid) {
		$query = mysql_query("SELECT * FROM users WHERE userid='".$userid."'");
		if (mysql_num_rows($query) > 0) {
			$data = mysql_fetch_assoc($query);
			$heartnum = (int)$data['heartnum'];
			$timestamp_heart_refill = convert_datetime($data['date_heart_refill']);
			$timestamp_current = time();
			$dif_time = $timestamp_current - $timestamp_heart_refill;
			// Timestamp divide by a minute (60 second)
			$min_time = $dif_time / 60;
			// Minute time divide by 30 (30 minutes)
			$refill_time = (int)($min_time / 30);
			if ($refill_time > 0) {
				if ($heartnum < 5) {
					$heartnum += $refill_time;
					if ($heartnum > 5)
						$heartnum = 5;
				}
				mysql_query("UPDATE users SET heartnum='".$heartnum."', date_heart_refill=NOW() WHERE userid='".$userid."'");
			} else {
				mysql_query("UPDATE users SET date_heart_refill=NOW() WHERE userid='".$userid."'");
			}
		}
	}
	
	// Helpers
	function VerifyMailAddress($address) 
	{
		if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $address))
			return true;
		else
			return false;
	}
	
	function convert_datetime($str) {

		list($date, $time) = explode(' ', $str); 
		list($year, $month, $day) = explode('-', $date); 
		list($hour, $minute, $second) = explode(':', $time); 
		 
		$timestamp = mktime($hour, $minute, $second, $month, $day, $year); 
		 
		return $timestamp; 
	}

	function aasort(&$array, $key) {
		$sorter = array();
		$ret = array();
		reset($array);
		foreach ($array as $ii => $va) {
			$sorter[$ii] = $va[$key];
		}
		asort($sorter);
		foreach ($sorter as $ii => $va) {
			$ret[$ii] = $array[$ii];
		}
		$array = $ret;
	}
	
	function curl_get_contents($url) {
		// create a new cURL resource
		$ch = curl_init();

		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// grab URL and pass it to the browser
		$result = curl_exec($ch);

		// close cURL resource, and free up system resources
		curl_close($ch);
		
		return $result;
	}
	
	function insertStartInventory($userid) {
		// Insert avatar inventory
		if (mysql_num_rows(mysql_query("SELECT * FROM inventory_avatar WHERE userid='".$userid."' AND char_index='0' AND avatarid='1'")) == 0)
			mysql_query("INSERT INTO inventory_avatar (userid, char_index, avatarid, date_done) VALUES ('".$userid."', 0, 1, NOW())");
		if (mysql_num_rows(mysql_query("SELECT * FROM inventory_avatar WHERE userid='".$userid."' AND char_index='1' AND avatarid='1'")) == 0)
			mysql_query("INSERT INTO inventory_avatar (userid, char_index, avatarid, date_done) VALUES ('".$userid."', 1, 1, NOW())");
		if (mysql_num_rows(mysql_query("SELECT * FROM inventory_avatar WHERE userid='".$userid."' AND char_index='2' AND avatarid='1'")) == 0)
			mysql_query("INSERT INTO inventory_avatar (userid, char_index, avatarid, date_done) VALUES ('".$userid."', 2, 1, NOW())");
		if (mysql_num_rows(mysql_query("SELECT * FROM inventory_avatar WHERE userid='".$userid."' AND char_index='3' AND avatarid='1'")) == 0)
			mysql_query("INSERT INTO inventory_avatar (userid, char_index, avatarid, date_done) VALUES ('".$userid."', 3, 1, NOW())");
		if (mysql_num_rows(mysql_query("SELECT * FROM inventory_avatar WHERE userid='".$userid."' AND char_index='4' AND avatarid='1'")) == 0)
			mysql_query("INSERT INTO inventory_avatar (userid, char_index, avatarid, date_done) VALUES ('".$userid."', 4, 1, NOW())");
		if (mysql_num_rows(mysql_query("SELECT * FROM inventory_avatar WHERE userid='".$userid."' AND char_index='5' AND avatarid='1'")) == 0)
			mysql_query("INSERT INTO inventory_avatar (userid, char_index, avatarid, date_done) VALUES ('".$userid."', 5, 1, NOW())");
		// Insert skill inventory
		if (mysql_num_rows(mysql_query("SELECT * FROM inventory_skill WHERE userid='".$userid."' AND char_index='0' AND skillid='1'")) == 0)
			mysql_query("INSERT INTO inventory_skill (userid, char_index, skillid, date_done) VALUES ('".$userid."', 0, 1, NOW())");
		if (mysql_num_rows(mysql_query("SELECT * FROM inventory_skill WHERE userid='".$userid."' AND char_index='1' AND skillid='1'")) == 0)
			mysql_query("INSERT INTO inventory_skill (userid, char_index, skillid, date_done) VALUES ('".$userid."', 1, 1, NOW())");
		if (mysql_num_rows(mysql_query("SELECT * FROM inventory_skill WHERE userid='".$userid."' AND char_index='2' AND skillid='1'")) == 0)
			mysql_query("INSERT INTO inventory_skill (userid, char_index, skillid, date_done) VALUES ('".$userid."', 2, 1, NOW())");
		if (mysql_num_rows(mysql_query("SELECT * FROM inventory_skill WHERE userid='".$userid."' AND char_index='3' AND skillid='1'")) == 0)
			mysql_query("INSERT INTO inventory_skill (userid, char_index, skillid, date_done) VALUES ('".$userid."', 3, 1, NOW())");
		if (mysql_num_rows(mysql_query("SELECT * FROM inventory_skill WHERE userid='".$userid."' AND char_index='4' AND skillid='1'")) == 0)
			mysql_query("INSERT INTO inventory_skill (userid, char_index, skillid, date_done) VALUES ('".$userid."', 4, 1, NOW())");
		if (mysql_num_rows(mysql_query("SELECT * FROM inventory_skill WHERE userid='".$userid."' AND char_index='5' AND skillid='1'")) == 0)
			mysql_query("INSERT INTO inventory_skill (userid, char_index, skillid, date_done) VALUES ('".$userid."', 5, 1, NOW())");
	}
	
	function insertStartStats($userid) {
		if (mysql_num_rows(mysql_query("SELECT * FROM users_stats WHERE userid='".$userid."'")) == 0)
			mysql_query("INSERT INTO users_stats(userid, spent_gold, spent_crystal, fight_killed, fight_won) VALUES ('".$userid."', '0', '0', '0', '0')");
	}
	
	function HasTrueAchievement($userid, $achievement_id) {
		if (mysql_num_rows(mysql_query("SELECT * FROM users_achievements_true WHERE userid='".$userid."' AND achievement_id='".$achievement_id."'")) > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	function UnlockTrueAchievement($userid, $trueid, $achievement_id) {
		$success = false;
		$query = mysql_query("SELECT * FROM achievements_true WHERE achievement_id='".$achievement_id."'");
		if (mysql_num_rows($query) > 0) {
			$data = mysql_fetch_assoc($query);
			$unlock_status = $data['unlock_status'];
			if (strlen($trueid) > 0) {
				/*
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, TRUE_PATH.'AchievementUnlock');
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/xml; charset=utf-8', 'SOAPAction: '.TRUE_PATH.'AchievementUnlock'));
				curl_setopt($ch, CURLOPT_POSTFIELDS, "<RequestData xmlns=\"http://www.eysnap.com/mPlayer\"><details>".TRUE_APPID."|".TRUE_APPSECRET."|".$achievement_id."|".$trueid."|".$unlock_status."</details></RequestData>");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$result = curl_exec($ch);
				curl_close($ch);
				$response = simplexml_load_string($result);
				if ($result != false && $response->AchievementUnlockMsg == "success") {
					if (mysql_num_rows(mysql_query("SELECT * FROM users_achievements_true WHERE userid='".$userid."' AND achievement_id='".$achievement_id."'")) == 0) {
						if (mysql_query("INSERT INTO users_achievements_true (userid, achievement_id, is_seen, date_done) VALUES ('".$userid."', '".$achievement_id."', '0', NOW())")) {
							$success = true;
						}
					}
				}
				*/
				if (mysql_num_rows(mysql_query("SELECT * FROM users_achievements_true WHERE userid='".$userid."' AND achievement_id='".$achievement_id."'")) == 0) {
					if (mysql_query("INSERT INTO users_achievements_true (userid, achievement_id, is_seen, date_done) VALUES ('".$userid."', '".$achievement_id."', '0', NOW())")) {
						$success = true;
					}
				}
			} else {
				// Unlock without true service usages
				if (mysql_num_rows(mysql_query("SELECT * FROM users_achievements_true WHERE userid='".$userid."' AND achievement_id='".$achievement_id."'")) == 0) {
					if (mysql_query("INSERT INTO users_achievements_true (userid, achievement_id, is_seen, date_done) VALUES ('".$userid."', '".$achievement_id."', '0', NOW())")) {
						$success = true;
					}
				}
			}
		}
		return $success;
	}
	
	function isTutorial($userid) {
		if (HasTrueAchievement($userid, 100052)) {
			return false;
		} else {
			return true;
		}
	}
	
	function dismissTutorial($userid, $trueid, $facebookid) {
		if (UnlockTrueAchievement($userid, $trueid, 100052)) {
			if (strlen($facebookid) > 0) {
				$ch_data = array('type' => 'achievement_unlock', 'achievement_id' => 100052, 'to_facebookid' => $facebookid);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, APPLICATION_PATH.'fb_publish_stream.php');
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($ch_data));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$result = curl_exec($ch);
				curl_close($ch);
			}
			return true;
		} else {
			return false;
		}
	}

	function js_redirect($url) {
	?>
	<!DOCTYPE html>
	<html lang="en">
		<head>
			<meta charset="utf-8"/>
			<title>Rune War</title>
			<script type="text/javascript" src="js/jquery-1.8.0.js"></script>
			<script type="text/javascript">
				$(document).ready(function() {
					window.top.location.href = "<?php echo $url; ?>";
				});
			</script>
		</head>
		<body>
		</body>
	</html>
	<?php
	}
	include 'Facebook/Entities/AccessToken.php';
	include 'Facebook/Entities/SignedRequest.php';
	
	include 'Facebook/HttpClients/FacebookHttpable.php';
	include 'Facebook/HttpClients/FacebookCurl.php';
	include 'Facebook/HttpClients/FacebookCurlHttpClient.php';
	include 'Facebook/HttpClients/FacebookGuzzleHttpClient.php';
	include 'Facebook/HttpClients/FacebookStream.php';
	include 'Facebook/HttpClients/FacebookStreamHttpClient.php';

	include 'Facebook/FacebookSDKException.php';
	include 'Facebook/FacebookRequestException.php';
	include 'Facebook/FacebookClientException.php';
	include 'Facebook/FacebookAuthorizationException.php';
	include 'Facebook/FacebookOtherException.php';
	include 'Facebook/FacebookPermissionException.php';
	include 'Facebook/FacebookThrottleException.php';
	include 'Facebook/FacebookServerException.php';

	include 'Facebook/FacebookSignedRequestFromInputHelper.php';
	include 'Facebook/FacebookCanvasLoginHelper.php';
	include 'Facebook/FacebookJavaScriptLoginHelper.php';
	include 'Facebook/FacebookRedirectLoginHelper.php';
	include 'Facebook/FacebookPageTabHelper.php';

	include 'Facebook/FacebookRequest.php';
	include 'Facebook/FacebookResponse.php';
	include 'Facebook/FacebookSession.php';
	
	include 'Facebook/GraphObject.php';
	include 'Facebook/GraphAlbum.php';
	include 'Facebook/GraphLocation.php';
	include 'Facebook/GraphPage.php';
	include 'Facebook/GraphSessionInfo.php';
	include 'Facebook/GraphUser.php';
	include 'Facebook/GraphUserPage.php';
?>