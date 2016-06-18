<?php
include "function.php";
if (checkLogin()) {
	$userid = $_POST['userid'];
	$targetid = $_POST['targetid'];
	if ($userid != $targetid) {
		if (mysql_num_rows(mysql_query("SELECT * FROM friends WHERE userid1='".$userid."' AND userid2='".$targetid."'")) == 0 && 
			mysql_num_rows(mysql_query("SELECT * FROM friends_request WHERE userid1='".$userid."' AND userid2='".$targetid."'")) == 0) {
			mysql_query("INSERT INTO friends_request (userid1, userid2, is_seen, date_done) VALUES ('".$userid."', '".$targetid."', '0', NOW())");
		}
		$response = array('msgid' => MESSAGE_SUCCESS);
		$response['targetid'] = $targetid;
		echo json_encode($response);
	} else {
		$response = array('msgid' => MESSAGE_ERROR);
		echo json_encode($response);
	}
} else {
	// Warn user to relogin
	$response = array('msgid' => MESSAGE_ERROR_NOTSIGNED);
	echo json_encode($response);
}
?>