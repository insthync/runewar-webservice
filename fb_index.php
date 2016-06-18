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
$loginUrl = $helper->getLoginUrl('email, publish_actions, user_friends');
js_redirect($loginUrl);
?>