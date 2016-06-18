<?php
include "../function.php";
?>
<?php
$i = 0;
if (checkLogin()) {
	$userid = $_POST['userid'];
	$token = $_POST['token'];
	if (isset($_POST['achievement_id'])) {
		$achievement_id = $_POST['achievement_id'];
		if (HasTrueAchievement($userid, $achievement_id))
		{
			mysql_query("UPDATE users SET used_achievement_id='".$achievement_id."' WHERE userid='".$userid."'");
		}
	}
	$data_user = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE userid='".$userid."'"));
	if (strlen($data_user['username']) > 0) {
		$query_achievements = mysql_query("SELECT * FROM achievements_true ORDER BY achievement_id ASC");
		while ($data = mysql_fetch_assoc($query_achievements)) {
			$unlocked = false;
			if (mysql_num_rows(mysql_query("SELECT * FROM users_achievements_true WHERE userid='".$userid."' AND achievement_id='".$data['achievement_id']."'"))) {
				$unlocked = true;
			}
			$notused = false;
			if ($data_user['used_achievement_id'] == $data['achievement_id']) {
				$notused = true;
			}
	?>
	<div class="modalitem">
		<table width="100%">
			<tr>
				<td width="50px">
					<!-- Profile Image -->
					<?php
					$image = $data['image_url'];
					if ($image == null || strlen($image) == 0)
						$image = APPLICATION_PATH."textures/icon.png";
					else
						$image = APPLICATION_PATH."icon/achievements_true/".$image;
					?>
					<img src="<?php echo $image; ?>" border="0" width="50px" height="50px" />
				</td>
				<td>
					<strong><?php echo $data['name']; ?></strong>
					<?php
					if ($unlocked) {
						mysql_query("UPDATE users_achievements_true SET is_seen='1' WHERE userid='".$userid."' AND achievement_id='".$data['achievement_id']."'");
						echo "( <span style='color: #008000;'>Unlocked</span> )";
					}
					?>
					<br />
					<?php echo $data['description']; ?>
				</td>
				<td width="100px" style="text-align: right;">
					<!-- Request button -->
					<?php 
					if ($unlocked && !$notused) {
					?>
					<a href="javascript:useAchievements(<?php echo $userid; ?>, '<?php echo $token; ?>', '<?php echo $data['achievement_id'] ?>');"><div class="modalbutton">Use</div></a>
					<?php
					}
					?>
				</td>
			</tr>
		</table>
	</div>
	<?php
		}
	} else {
	?>
	<div class="modalitemnone">
		ขอสงวนสิทธิ์การใช้งานระบบ Achievements เฉพาะผู้เล่นที่ลงทะเบียนเล่นเกมผ่าน GGDP เท่านั้น<br />กรุณาลงทะเบียนเข้าร่วมเกมผ่านทางหน้าเว็บ <a href="http://www.ggdp.in.th/">http://www.ggdp.in.th/</a>
	</div>
	<?php
	}
}
?>