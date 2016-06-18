<?php
include "function.php";
$userid = $_GET['userid'];
$query_user = queryBattleData($userid);
if ($data_user = mysql_fetch_assoc($query_user)) {
	$response = array('msgid' => MESSAGE_SUCCESS);
	$profile_image = $data_user['image_url'];
	if ($profile_image == null || strlen($profile_image) == 0)
		$profile_image = APPLICATION_PATH."textures/icon.png";
	$name = $data_user['username'];
	if ($name == null || strlen($name) == 0) {
		$name = "Unknow";
	}
	$response['info'] = array(
		'userid' => $userid,
		'name' => $name,
		'level' => $data_user['level'],
		'profile_image' => $profile_image, 
		'used_achievement' => $data_user['used_achievement_id']
	);
	echo json_encode($response);
} else {
	$response = array('msgid' => MESSAGE_ERROR);
	echo json_encode($response);
}
?>