<?php
include "function.php";
if (checkLogin()) {
	$userid = $_POST['userid'];
	if (isset($_POST['targetid']) && $_POST['targetid'] > 0)
		$userid = $_POST['targetid'];
	$query_user = queryBattleData($userid);
	$data_user = mysql_fetch_assoc($query_user);
	$response = array('msgid' => MESSAGE_SUCCESS);
	$response['user'] = $data_user;
	echo json_encode($response);
} else {
	// Warn user to relogin
	$response = array('msgid' => MESSAGE_ERROR_NOTSIGNED);
	echo json_encode($response);
}
?>