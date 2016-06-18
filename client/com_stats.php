<?php
include "../function.php";
$i = 0;
if (checkLogin()) {
	$i = $from = 0;
	if (isset($_POST['from']))
		$from = (int)$_POST['from'];
	$userid = $_POST['userid'];
	$token = $_POST['token'];
	$query = mysql_query("SELECT * FROM battle_result WHERE userid='".$userid."' ORDER BY date_done DESC LIMIT ".$from.", 25");
	$total_size = mysql_num_rows(mysql_query("SELECT * FROM battle_result WHERE userid='".$userid."'"));
	while ($data = mysql_fetch_assoc($query)) {
		$resultid = $data['resultid'];
		$is_seen = (int)$data['is_seen'];
		if ($is_seen == 0) {
			mysql_query("UPDATE battle_result SET is_seen='1' WHERE resultid='".$resultid."'");
		}
		$data_match = mysql_fetch_assoc(mysql_query("SELECT * FROM battle_match WHERE battleid='".$data['battleid']."'"));
		?>
	<div class="modalitem <?php if ($is_seen == 0) echo "item_unseen"; ?>">
		<table width="100%">
			<tr>
				<td width="50px">
					<!-- profile img -->
					<?php
					$data_users = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE userid='".$data_match['attackerid']."'"));
					$profile_image = $data_users['image_url'];
					if ($profile_image == null || strlen($profile_image) == 0)
						$profile_image = APPLICATION_PATH."textures/icon.png";
					?>
					<img src="<?php echo $profile_image; ?>" border="0" width="50px" height="50px" />
				</td>
				<td align="center">
					<?php
					if ($data_match['attackerid'] == $userid) {
						$data_users = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE userid='".$data_match['defenderid']."'"));
						$name = $data_users['username'];
						if ($name == null || strlen($name) == 0) {
							$name = "Unknow";
						}
						if ($data_match['defenderid'] < 0) {
							$name = "AI";
						}
						if ($data['result_flag'] == 0) {
							$total_exp = 0;
							$total_gold = 0;
							$query_rewards = mysql_query("SELECT * FROM battle_reward WHERE battleid='".$data_match['battleid']."' AND userid='".$userid."'");
							while ($data_rewards = mysql_fetch_assoc($query_rewards)) {
								$total_exp += (int)$data_rewards['reward_exp'];
								$total_gold += (int)$data_rewards['reward_gold'];
							}
						?>
					You fight with <strong><?php echo $name; ?></strong> and were victorious<br />
					Rewards: +<?php echo $total_exp; ?>  exp., +<?php echo $total_gold; ?> gold.
						<?php
						} else {
						?>
					You fight with <strong><?php echo $name; ?></strong> and defeated<br />
					Rewards: -1 Heart XD
						<?php
						}
					} else {
						$data_users = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE userid='".$data_match['attackerid']."'"));
						$name = $data_users['username'];
						if ($name == null || strlen($name) == 0) {
							$name = "Unknow";
						}
						if ($data_match['attackerid'] < 0) {
							$name = "AI";
						}
						if ($data['result_flag'] == 0) {
						?>
					<strong><?php echo $name; ?></strong> fight with you and were victorious<br />
					Rewards: No Reward.
						<?php
						} else {
						?>
					<strong><?php echo $name; ?></strong> fight with you and defeated<br />
					Rewards: No Reward.
						<?php
						}
					}
					?>
				</td>
				<td width="50px">
					<!-- profile img -->
					<?php
					$data_users = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE userid='".$data_match['defenderid']."'"));
					$profile_image = $data_users['image_url'];
					if ($profile_image == null || strlen($profile_image) == 0)
						$profile_image = APPLICATION_PATH."textures/icon.png";
					?>
					<img src="<?php echo $profile_image; ?>" border="0" width="50px" height="50px" />
				</td>
			</tr>
		</table>
	</div>
		<?php
		$i++;
	}
	$next = $from + 25;
	if (($total_size - $next) > 0) {
		// Show see more button
		?>
		<div class="modalseemore">
			&raquo; <a href="javascript:void(0);" onclick="seeMoreStats(<?php echo $userid; ?>, '<?php echo $token; ?>', <?php echo $next; ?>);">See more</a> &laquo;
		</div>
		<?php
	}
}
if ($i == 0) {
?>
	<div class="modalitemnone">
		No records...
	</div>
<?php
}
?>