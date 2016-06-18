<?php
include "function.php";
if (checkLogin()) {
	$userid = $_POST['userid'];
	$char_index = $_POST['char_index'];
	$avatarid = $_POST['avatarid'];
	if (checkAvatarUsable($userid, $char_index, $avatarid)) {
		$query = mysql_query("UPDATE usage_avatar SET ".$char_indexes[$char_index]."='".$avatarid."' WHERE userid='".$userid."'");
		if ($query) {
			$response = array('msgid' => MESSAGE_SUCCESS);
			echo json_encode($response);
		} else {
			// Error occurs
			$response = array('msgid' => MESSAGE_ERROR);
			echo json_encode($response);
		}
	} else {
		// Avatar not found
		$response = array('msgid' => MESSAGE_ERROR_NOTFOUND);
		echo json_encode($response);
	}
} else {
	// Warn user to relogin
	$response = array('msgid' => MESSAGE_ERROR_NOTSIGNED);
	echo json_encode($response);
}
?>