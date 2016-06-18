<?php
include "function.php";
if (checkLogin()) {
	$avatarid = $_POST['avatarid'];
	$char_index = $_POST['char_index'];
	$avatardata = getAvatarData($avatarid, $char_index);
	$avatarprice_gold = $avatardata['price_gold'];
	$avatarprice_crystal = $avatardata['price_crystal'];
	$userid = $_POST['userid'];
	$user_info = mysql_fetch_assoc(mysql_query("SELECT * FROM users_info WHERE userid='".$userid."'"));
	$data_stats = mysql_fetch_assoc(mysql_query("SELECT * FROM users_stats WHERE userid='".$userid."'"));
	$spent_gold = (int)$data_stats['spent_gold'];
	$spent_crystal = (int)$data_stats['spent_crystal'];
	$purse_type = $_POST['purse_type'];
	switch ($purse_type) {
		case $purse_types['gold']:
			$spent_gold += $avatarprice_gold;
			if ($avatarprice_gold >= 0) {
				if ($user_info['gold'] >= $avatarprice_gold) {
					$changed_gold = $user_info['gold'] - $avatarprice_gold;
					// Set money
					mysql_query("UPDATE users_stats SET spent_gold='".$spent_gold."', spent_crystal='".$spent_crystal."' WHERE userid='".$userid."'");
					$query1 = mysql_query("INSERT INTO inventory_avatar (userid, char_index, avatarid, date_done) VALUES ('".$userid."', '".$char_index."', '".$avatarid."', NOW())");
					$query2 = mysql_query("UPDATE users_info SET gold='".$changed_gold."' WHERE userid='".$userid."'");
					// Add to inventory
					if ($query1 && $query2) {
						$response = array('msgid' => MESSAGE_SUCCESS);
						$response['gold'] = $changed_gold;
						echo json_encode($response);
					} else {
						// Error occurs
						$response = array('msgid' => MESSAGE_ERROR);
						echo json_encode($response);
					}
				} else {
					// Warn user no enough money
					$response = array('msgid' => MESSAGE_ERROR_GOLD_NOTENOUGH);
					echo json_encode($response);
				}
			} else {
				// Warn user unable to buy with gold
				$response = array('msgid' => MESSAGE_ERROR_GOLD_NOTABLE);
				echo json_encode($response);
			}
		break;
		case $purse_types['crystal']:
			$spent_crystal += $avatarprice_crystal;
			if ($avatarprice_crystal >= 0) {
				if ($user_info['crystal'] >= $avatarprice_crystal) {
					$changed_crystal = $user_info['crystal'] - $avatarprice_crystal;
					// Set money
					mysql_query("UPDATE users_stats SET spent_gold='".$spent_gold."', spent_crystal='".$spent_crystal."' WHERE userid='".$userid."'");
					$query1 = mysql_query("INSERT INTO inventory_avatar (userid, char_index, avatarid, date_done) VALUES ('".$userid."', '".$char_index."', '".$avatarid."', NOW())");
					$query2 = mysql_query("UPDATE users_info SET crystal='".$changed_crystal."' WHERE userid='".$userid."'");
					// Add to inventory
					if ($query1 && $query2) {
						$response = array('msgid' => MESSAGE_SUCCESS);
						$response['crystal'] = $changed_crystal;
						echo json_encode($response);
					} else {
						// Error occurs
						$response = array('msgid' => MESSAGE_ERROR);
						echo json_encode($response);
					}
				} else {
					// Warn user no enough crystal
					$response = array('msgid' => MESSAGE_ERROR_CRYSTAL_NOTENOUGH);
					echo json_encode($response);
				}
			} else {
				// Warn user unable to buy with crystal
				$response = array('msgid' => MESSAGE_ERROR_CRYSTAL_NOTABLE);
				echo json_encode($response);
			}
		break;
	}
} else {
	// Warn user to relogin
	$response = array('msgid' => MESSAGE_ERROR_NOTSIGNED);
	echo json_encode($response);
}
?>