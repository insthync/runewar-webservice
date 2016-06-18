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
$success = false;
FacebookSession::setDefaultApplication(FACEBOOK_APPID, FACEBOOK_APPSECRET);

// Get User ID
$helper = new FacebookRedirectLoginHelper(APPLICATION_PATH.'fb_publish_stream.php');
try {
	$session = $helper->getSessionFromRedirect();
} catch(FacebookRequestException $ex) {
	// When Facebook returns an error
} catch(\Exception $ex) {
	// When validation fails or other local issues
}
if (!$session) {
	$loginUrl = $helper->getLoginUrl('email, publish_actions, user_friends');
	js_redirect($loginUrl);
	exit();
} else {
	if (isset($_POST['type'])) {
		$type = $_POST['type'];
		$attachment = array();
		$targetid = 0;
		if ($type == "levelup") {
			if (isset($_POST['level']) && isset($_POST['to_facebookid'])) {
				$level = $_POST['level'];
				$targetid = $_POST['to_facebookid'];
				$attachment = array(
					'picture' => APPLICATION_PATH.'icon/fb_feed_lvup.png',
					'link' => 'http://apps.facebook.com/'.FACEBOOK_APPID,
					'name' => 'Rune War',
					'caption' => 'ผู้เล่นเลื่อนเลเวลเป็นเลเวล '.$level.'!!',
					'description' => 'ผู้เล่นสามารถเอาชนะผู้คู่ต่อสู้มากมายด้วยศิลาศักดิ์สิทธิ์ในเกม Rune War จนเลื่อนเลเวลเป็นเลเวล '.$level.'.'
				);
			}
		}
		if ($type == "friend_request") {
			if (isset($_POST['username']) && isset($_POST['from_facebookid']) && isset($_POST['to_facebookid'])) {
				$username = $_POST['username'];
				$targetid = $_POST['from_facebookid'];
				$to_facebookid = $_POST['to_facebookid'];
				$attachment = array(
					'picture' => APPLICATION_PATH.'icon/fb_feed_addfriend.png',
					'link' => 'http://apps.facebook.com/'.FACEBOOK_APPID,
					'name' => 'Rune War',
					'caption' => 'ผู้เล่น '.$username.' ขอคุณเป็นเพื่อนร่วมรบใน Rune War!!',
					'description' => 'ผู้เล่น '.$username.' ต้องการคุณเป็นเพื่อนร่วมต่อสู้ด้วยศิลาศักดิ์สิทธิ์ในเกม Rune War.'
				);
			}
		}
		if ($type == "achievement_unlock") {
			if (isset($_POST['achievement_id']) && isset($_POST['to_facebookid'])) {
				$achievement_id = $_POST['achievement_id'];
				$data = mysql_fetch_assoc(mysql_query("SELECT * FROM achievements_true WHERE achievement_id='".$achievement_id."'"));
				$name = $data['name'];
				$description = $data['description'];
				$image_url = $data['image_url'];
				$targetid = $_POST['to_facebookid'];
				$attachment = array(
					'picture' => APPLICATION_PATH.'icon/achievements_true/'.$image_url,
					'link' => 'http://apps.facebook.com/'.FACEBOOK_APPID,
					'name' => 'Rune War',
					'caption' => 'ผู้เล่นปลดล็อก '.$name.'!!',
					'description' => $description
				);
			}
		}
		if (sizeof($attachment) > 0 && $targetid > 0) {
			try {
				$response = (new FacebookRequest( $session, 'POST', '/'.$targetid.'/feed', $attachment )))->execute()->getGraphObject();
				//echo "Posted with id: " . $response->getProperty('id');
			} catch(FacebookRequestException $e) {
				//echo "Exception occured, code: " . $e->getCode();
				//echo " with message: " . $e->getMessage();
			}
		}
	}
}
?>