<?php
session_start();
include "function.php";
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphUser;

FacebookSession::setDefaultApplication(FACEBOOK_APPID, FACEBOOK_APPSECRET);

// Get User ID
$helper = new FacebookRedirectLoginHelper(APPLICATION_PATH.'play.php');
try {
	$session = $helper->getSessionFromRedirect();
} catch(FacebookRequestException $ex) {
	// When Facebook returns an error
} catch(\Exception $ex) {
	// When validation fails or other local issues
}
// Login or logout url will be needed depending on current user state.
if (!$session) {
	$loginUrl = $helper->getLoginUrl('email, publish_actions, user_friends');
	js_redirect($loginUrl);
	exit();
} else {
	$logoutUrl = $helper->getLogoutUrl($session, APPLICATION_PATH.'back.php');
	$user_profile = (new FacebookRequest($session, 'GET', '/me'))->execute()->getGraphObject(GraphUser::className());
	$facebookid = $user_profile->getProperty('id');
	$facebook_name = $user_profile->getProperty('name');
	$facebook_email = $user_profile->getProperty('email');
}

$query_users = mysql_query("SELECT * FROM users WHERE facebookid='".$facebookid."' OR email='".$facebook_email."'");
$num_users = mysql_num_rows($query_users);
if ($num_users > 0) {
	$data_users = mysql_fetch_assoc($query_users);
	$userid = $data_users['userid'];
	// If non facebook id or not equal update facebookid
	if ($data_users['facebookid'] != $facebookid) {
		mysql_query("UPDATE users SET facebookid='".$facebookid."' WHERE userid='".$userid."'");
	}
	// Set token
	$token = md5(uniqid('runewar_'));
	$process = mysql_query("UPDATE users SET token='".$token."', date_login=NOW() WHERE facebookid='".$facebookid."'");
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
		$success = true;
	} else {
		// Error occurs
		$success = false;
	}
} else {
	// Set token and Register
	if (VerifyMailAddress($facebook_email)) {
		$token = md5(uniqid("runewar_"));
		$process = mysql_query("INSERT INTO users (email, facebookid, token, heartnum, date_done, date_login, date_heart_refill) VALUES ('".$facebook_email."', '".$facebookid."', '".$token."', '".START_HEART."', NOW(), NOW(), NOW())");
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
			$success = true;
		} else {
			$success = false;
		}
	} else {
		$success = false;
	}
}

if (!$success) {
	echo "Access Denined";
	exit();
}

$fb_image_url  = "https://graph.facebook.com/".$facebookid."/picture?width=100&height=100&redirect=false";
// open connection
$ch = curl_init();
// set the url, number of POST vars, POST data
curl_setopt($ch, CURLOPT_URL, $fb_image_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// execute post, got json response
$result = curl_exec($ch);
// close connection
curl_close($ch);
if ($result != false) {
	$response = json_decode($result);
	$fb_image_url = $response->data->url;
	$file_ext = explode('?', end(explode('.', $fb_image_url)));
	$file_ext = $file_ext[0];
	$physic_path = 'profile_images/'.md5($facebookid).'.'.$file_ext;
	$url_path = APPLICATION_PATH.'profile_images/'.md5($facebookid).'.'.$file_ext;
	if (file_exists($physic_path))
		unlink($physic_path);
	file_put_contents($physic_path, file_get_contents($fb_image_url));
	$image_url = $url_path;
	mysql_query("UPDATE users SET image_url='".$image_url."' WHERE userid='".$userid."'");
} else {
	$image_url = "";
}

$ggdp_token = null;
if (isset($_SESSION['ggdp_token']))
{
	$ggdp_token = $_SESSION['ggdp_token'];
}

if (isset($_SESSION['total_de']))
{
	$total_de = $_SESSION['total_de'];
}
$userdata = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE userid='".$userid."'"));
$username = $userdata['username'];
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="0" />
		<title>Rune War</title>
		<script type="text/javascript" src="js/swfobject.js"></script>
		<script type="text/javascript" src="js/jquery-1.8.0.js"></script>
		<script type="text/javascript" src="js/jquery.reveal.js"></script>
		<script type="text/javascript" src="js/script.js"></script>
		<script type="text/javascript">
			/*
			$(window).resize(function() {
				updateContainer();
			});
			var interval = setInterval(function() {
				interval = window.clearInterval(interval);
				updateContainer();
			}, 500);
			*/
			$(window).resize(function() {
				updateContainerMargin();
			});
			var interval = setInterval(function() {
				interval = window.clearInterval(interval);
				updateContainerMargin();
			}, 1000);
			achievementsVerify(<?php echo $userid; ?>, '<?php echo $token; ?>');
			updateNotify(<?php echo $userid; ?>, '<?php echo $token; ?>');
			setInterval(function()
				{
					if (!isEnd) {
						//achievementsVerify(<?php echo $userid; ?>, '<?php echo $token; ?>');
						updateNotify(<?php echo $userid; ?>, '<?php echo $token; ?>');
					}
				},
				3000
			);
			<?php
			if (!empty($ggdp_token)) {
			?>
			ggdp_token = "<?php echo $ggdp_token; ?>";
			<?php
			}
			?>
			app_url = "<?php echo APPLICATION_PATH; ?>";
			// Rune War SWF
			var flashvars = {
				'filepath' : '<?php echo APPLICATION_PATH; ?>swf/runewar.swf',
				'userid' : '<?php echo $userid; ?>',
				'token' : '<?php echo $token; ?>',
				'serviceurl' : '<?php echo APPLICATION_PATH; ?>',
				'server_ip' : '<?php echo SERVER_IP; ?>',
				'server_port' : '<?php echo SERVER_PORT; ?>',
				'crossdomainurl' : '<?php echo CROSSDOMAIN_PATH; ?>',
				'list_avatars_path' : '<?php echo $list_avatar_path; ?>',
				'list_skill_path' : '<?php echo $list_skill_path; ?>',
				'list_player_exp' : '<?php echo $list_player_exp; ?>'
			};
			var params = {
				'bgcolor' : '#000000',
				'allowFullScreen' : 'true',
				'wmode' : 'direct',
				'allowScriptAccess' : 'always'
			};
			var attributes = {
				'id' : 'runewar'
			};
			swfobject.embedSWF("swf/loader.swf", "rwcontent", "960", "640", "11.0.0", "swf/expressInstall.swf", flashvars, params, attributes);
		</script>
		<link type="text/css" rel="stylesheet" media="all" href="css/general.css" />
		<link type="text/css" rel="stylesheet" media="all" href="css/reveal.css" />
	</head>
	<body id="body">
		<div id="container">
			<div class="warn-msg-panel" id="warning-msg" style="display: none;">
				<img src="images/paper.png" border="0" class="ui-game-paper" />
				<img src="images/npc_tutorial.png" border="0" class="ui-game-npc" />
				<div class="warn-msg-content">&nbsp;</div>
			</div>
			<?php
			if (empty($username) || strlen($username) == 0) {
			?>
			<div class="name-change-panel">
				<img src="images/paper.png" border="0" class="ui-game-paper" />
				<img src="images/npc_tutorial.png" border="0" class="ui-game-npc" />
				<table class="name-change-form">
					<tr>
						<td colspan="2"><strong>สวัสดีท่านผู้กล้า!!</strong><br /> ข้าชื่อโจว เป็นผู้ดูแลศิลาวิเศษแห่งนี้<br /><br />ข้าจะแนะนำการใช้งานศิลาเบื้องต้นให้แก่ท่าน<br /> แต่ตอนนี้ ข้าต้องการรู้ว่าท่านชื่ออะไร ?<br /><br /><br />กรุณาระบุชื่อตัวละครของท่าน</td>
					</tr>
					<tr>
						<td width="305px">
							<input type="text" id="username" maxlength="10" class="name-change-input" />
						</td>
						<td>
							<a href="javascript:changeName(<?php echo $userid; ?>, '<?php echo $token; ?>');" class="name-change-submit">ยืนยัน</a>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<span class="name-change-hint">
								(ใช้ตัวอักษร a-z, A-Z และตัวเลข 0-9 ในการตั้งชื่อ... อ้อ!! เกือบลืมไป ตั้งชื่อได้ตั้งแต่ 2-10 ตัวอักษรนะ)
							</span>
							<br />
							<br />
							<span class="name-change-msg" id="name-change-msg">&nbsp;</span>
						</td>
					</tr>
				</table>
			</div>
			<?php
			} else {
			?>
			<div id="tmenucontainer" class="top_menu">
				<div class="menu" id="menu-requests"><a href="javascript:void(0);" onclick="callFriendRequests(<?php echo $userid; ?>, '<?php echo $token; ?>');">Friends <span class="notify">0</span></a></div>
				<div class="menu" id="menu-stats"><a href="javascript:void(0);" onclick="callStats(<?php echo $userid; ?>, '<?php echo $token; ?>');">Statistics <span class="notify">0</span></a></div>
				<div class="menu" id="menu-achievements"><a href="javascript:void(0);" onclick="callAchievements(<?php echo $userid; ?>, '<?php echo $token; ?>');">Achievements <span class="notify">0</span></a></div>
				<div class="menu" id="menu-ranking"><a href="javascript:void(0);" onclick="callRanking();">Ranking</a></div>
				<?php if (!empty($ggdp_token)) { ?>
				<div id="totel_de"><?php echo $total_de; ?> <span>DE</span></div>
				<?php } ?>
				<div class="clear"></div>
			</div>
			<div id="swfcontainer">
				<div id="rwcontainer" class="unseen_position">
					<div id="rwcontent">
						<a href="http://www.adobe.com/go/getflashplayer">
							<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />
						</a>
					</div>
				</div>
				<div id="preloader">
					Loading...
				</div>
			</div>
			<?php
			}
			?>
			<div id="footercontainer" class="footer">
				<div style="width:595px; margin:0 auto;">
					<div style="float:left;">
						<img src="images/three_little_pigs_logo.png" height="90" border="0">
						<a href="http://www.truedigitalplus.com/" target="_blank">
							<img src="images/true_logo.png" height="90" border="0">
						</a>
					</div>
					<iframe src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2FRuneWar&amp;width=200&amp;height=62&amp;show_faces=false&amp;colorscheme=light&amp;stream=false&amp;border_color&amp;header=false&amp;appId=117373571768326" scrolling="no" frameborder="0" style="float:left; border:none; overflow:hidden; width:200px; height:62px; margin-top:10px; margin-left:20px;" allowTransparency="true"></iframe>
					<div class="clear"></div>
				</div>
			</div>
			<div id="dialog" class="reveal-modal">
				<h1></h1>
				<p><div class="modalcontainer"></div></p>
				<a class="close-reveal-modal">&#215;</a>
			</div>
		</div>
		<form action="play.php" method="post" id="play">
			<input type="hidden" name="userid" id="userid" />
			<input type="hidden" name="token" id="token" />
		</form>
	</body>
</html>