<?php
include "../function.php";
$i = 0;
if (checkLogin()) {
	$i = 0;
	$userid = $_POST['userid'];
	$token = $_POST['token'];
	?>
	<div>
	<input type="text" id="search_for_addfriends" />
	<input type="button" value="Search" onclick="searchAddFriends(<?php echo $userid; ?>, '<?php echo $token;?>', 'search_for_addfriends');" />
	</div>
	<?php
	$list_friends = array();
	$list_requests = array();
	$query_friends = mysql_query("SELECT * FROM friends WHERE userid1='".$userid."'");
	while ($data_friends = mysql_fetch_assoc($query_friends)) {
		$list_friends[] = $data_friends['userid2'];
	}
	$query_requests = mysql_query("SELECT * FROM friends_request WHERE userid1='".$userid."'");
	while ($data_requests = mysql_fetch_assoc($query_requests)) {
		$list_requests[] = $data_requests['userid2'];
	}
	if (isset($_POST['search']) && strlen($_POST['search']) > 0) {
		$search = $_POST['search'];
		$query_users = mysql_query("SELECT * FROM users WHERE username LIKE '%".$search."%' OR email LIKE '%".$search."%'");
	} else {
		$query_users = mysql_query("SELECT * FROM users ORDER BY RAND() LIMIT 0, 10");
	}
	$total_size = mysql_num_rows(mysql_query("SELECT * FROM users"));
	while ($data_users = mysql_fetch_assoc($query_users)) {
		$targetid = $data_users['userid'];
		$name = $data_users['username'];
		if ($name == null || strlen($name) == 0) {
			$name = "Unknow";
		}
		if (!in_array($targetid, $list_friends) && !in_array($targetid, $list_requests) && $targetid != $userid) {
			$data_users_info = mysql_fetch_assoc(mysql_query("SELECT * FROM users_info WHERE userid='".$targetid."'"));
			$num_win = mysql_num_rows(mysql_query("SELECT * FROM battle_result WHERE userid='".$targetid."' AND result_flag='0'"));
			$num_lose = mysql_num_rows(mysql_query("SELECT * FROM battle_result WHERE userid='".$targetid."' AND result_flag='1'"));
			?>
	<div class="modalitem" id="userid_<?php echo $targetid; ?>">
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
					<strong><?php echo $name ?></strong><br />
					Level: <?php echo $data_users_info['level']; ?>
				</td>
				<td width="100px">
					<!-- Win, Lose -->
					Win: <?php echo $num_win; ?><br />
					Lose: <?php echo $num_lose; ?>
				</td>
				<td style="text-align: right;">
					<!-- Request button -->
					<a href="javascript:requestFriend(<?php echo $userid; ?>, '<?php echo $token; ?>', <?php echo $targetid; ?>);"><div class="modalbutton">Request</div></a>
				</td>
			</tr>
		</table>
	</div>
			<?php
			$i++;
		}
	}
	?>
	<div class="modalseemore">
		&raquo; <a href="javascript:void(0);" onclick="seeMoreAddFriends(<?php echo $userid; ?>, '<?php echo $token; ?>');">Re-Random</a> &laquo;
	</div>
	<?php
}
if ($i == 0) {
?>
	<div class="modalitemnone">
		No records...
	</div>
<?php
}
?>