<?php
include "../function.php";
$i = 0;
if (checkLogin()) {
	$i = $from = 0;
	if (isset($_POST['from']))
		$from = (int)$_POST['from'];
	$userid = $_POST['userid'];
	$token = $_POST['token'];
	$query_requests = mysql_query("SELECT * FROM friends_request WHERE userid2='".$userid."' LIMIT ".$from.", 25");
	$total_size = mysql_num_rows(mysql_query("SELECT * FROM friends_request WHERE userid2='".$userid."'"));
	while ($data_requests = mysql_fetch_assoc($query_requests)) {
		$requestid = $data_requests['requestid'];
		$is_seen = (int)$data_requests['is_seen'];
		if ($is_seen == 0) {
			mysql_query("UPDATE friends_request SET is_seen='1' WHERE requestid='".$requestid."'");
		}
		$data_users = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE userid='".$data_requests['userid1']."'"));
		$name = $data_users['username'];
		if ($name == null || strlen($name) == 0) {
			$name = "Unknow";
		}
		$targetid = $data_users['userid'];
		$data_users_info = mysql_fetch_assoc(mysql_query("SELECT * FROM users_info WHERE userid='".$targetid."'"));
		$num_win = mysql_num_rows(mysql_query("SELECT * FROM battle_result WHERE userid='".$targetid."' AND result_flag='0'"));
		$num_lose = mysql_num_rows(mysql_query("SELECT * FROM battle_result WHERE userid='".$targetid."' AND result_flag='1'"));
		?>
	<div class="modalitem <?php if ($is_seen == 0) echo "item_unseen"; ?>" id="userid_<?php echo $targetid; ?>">
		<table width="100%">
			<tr>
				<td width="50px">
					<!-- Profile Image -->
					<?php
					$profile_image = $data_users['image_url'];
					if ($profile_image == null || strlen($profile_image) == 0)
						$profile_image = APPLICATION_PATH."textures/icon.png";
					?>
					<img src="<?php echo $profile_image; ?>" border="0" width="50px" height="50px" />
				</td>
				<td width="160px">
					<!-- Name, Level -->
					<strong><?php echo $name; ?></strong><br />
					Level: <?php echo $data_users_info['level']; ?>
				</td>
				<td width="100px">
					<!-- Win, Lose -->
					Win: <?php echo $num_win; ?><br />
					Lose: <?php echo $num_lose; ?>
				</td>
				<td style="text-align: right;">
					<!-- Request button -->
					<a href="javascript:acceptFriend(<?php echo $userid; ?>, '<?php echo $token; ?>', <?php echo $targetid; ?>);"><div class="modalbutton">Accept</div></a>
					<a href="javascript:declineFriend(<?php echo $userid; ?>, '<?php echo $token; ?>', <?php echo $targetid; ?>);"><div class="modalbutton">Decline</div></a>
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
			&raquo; <a href="javascript:void(0);" onclick="seeMoreFriendRequests(<?php echo $userid; ?>, '<?php echo $token; ?>', <?php echo $next; ?>);">See more</a> &laquo;
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