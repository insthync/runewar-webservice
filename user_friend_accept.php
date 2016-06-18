<?php
include "function.php";
if (checkLogin()) {
	$userid = $_POST['userid'];
	$targetid = $_POST['targetid'];
	if ($userid != $targetid) {
		mysql_query("DELETE FROM friends_request WHERE userid1='".$targetid."' AND userid2='".$userid."'");
		mysql_query("DELETE FROM friends_request WHERE userid2='".$targetid."' AND userid1='".$userid."'");
		mysql_query("INSERT INTO friends (userid1, userid2, is_seen, date_done) VALUES ('".$userid."', '".$targetid."', '1', NOW())");
		mysql_query("INSERT INTO friends (userid1, userid2, is_seen, date_done) VALUES ('".$targetid."', '".$userid."', '0', NOW())");
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