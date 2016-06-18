<?php
include "function.php";
if (checkLogin()) {
	$userid = $_POST['userid'];
	$char_index = $_POST['char_index'];
	$skillid = $_POST['skillid'];
	if (checkSkillUsable($userid, $char_index, $skillid)) {
		$query = mysql_query("UPDATE usage_skill SET ".$char_indexes[$char_index]."='".$skillid."' WHERE userid='".$userid."'");
		if ($query) {
			$response = array('msgid' => MESSAGE_SUCCESS);
			echo json_encode($response);
		} else {
			// Error occurs
			$response = array('msgid' => MESSAGE_ERROR);
			echo json_encode($response);
		}
	} else {
		// Skill not found
		$response = array('msgid' => MESSAGE_ERROR_NOTFOUND);
		echo json_encode($response);
	}
} else {
	// Warn user to relogin
	$response = array('msgid' => MESSAGE_ERROR_NOTSIGNED);
	echo json_encode($response);
}
?>