<?php
include "function.php";
if (checkLogin()) {
	$userid = $_POST['userid'];
	$username = $_POST['username'];
	if (mysql_num_rows(mysql_query("SELECT * FROM users WHERE userid!='".$userid."' AND username='".$username."'"))) {
		// Duplicated name
		$response = array('msgid' => MESSAGE_ERROR);
		$response['reason'] = 'DUPLICATED';
		echo json_encode($response);
	} else {
		if (ereg("^[a-zA-Z0-9_]{2,10}$", $username))
		{
			mysql_query("UPDATE users SET username='".$username."' WHERE userid='".$userid."'");
			$response = array('msgid' => MESSAGE_SUCCESS);
			echo json_encode($response);
		} else {
			$response = array('msgid' => MESSAGE_ERROR);
			$response['reason'] = 'INVALID_FORMAT';
			echo json_encode($response);
		}
	}

} else {
	// Warn user to relogin
	$response = array('msgid' => MESSAGE_ERROR_NOTSIGNED);
	echo json_encode($response);
}
?>