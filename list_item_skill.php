<?php
include "function.php";

if (checkLogin()) {
	$userid = $_POST['userid'];
	$char_index = $_POST['char_index'];
	$allData = getAllSkillData($char_index);
	// Bought id
	$idList = array();
	$query_inventory = mysql_query("SELECT * FROM inventory_skill WHERE userid='".$userid."' AND char_index='".$char_index."'");
	while ($data_inventory = mysql_fetch_assoc($query_inventory)) {
		$idList[] = $data_inventory['skillid'];
	}
	$results = array();
	$allDataSize = count($allData);
	for ($i = 0; $i < $allDataSize; ++$i) {
		$id = $allData[$i]['id'];
		if (in_array($id, $idList)) {
			if (mysql_num_rows(mysql_query("SELECT ".$char_indexes[$char_index]." FROM usage_skill WHERE ".$char_indexes[$char_index]."='".$id."' AND userid='".$userid."'")))
			{
				$results[] = array(2, $allData[$i]);
			} else {
				$results[] = array(1, $allData[$i]);
			}
		} else {
			$results[] = array(0, $allData[$i]);
		}
	}
	$data_user = mysql_fetch_array(mysql_query("SELECT * FROM users_info WHERE userid='".$userid."'"));
	$response = array('msgid' => MESSAGE_SUCCESS);
	$response['results'] = $results;
	echo json_encode($response);
} else {
	// Warn user to relogin
	$response = array('msgid' => MESSAGE_ERROR_NOTSIGNED);
	echo json_encode($response);
}
?>