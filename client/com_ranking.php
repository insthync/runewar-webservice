<?php
include "../function.php";
if (isset($_GET['mode']) && !empty($_GET['mode'])) {
	$mode = $_GET['mode'];
} else {
	$mode = 0;
}

// top menu
?>
<div>
<a href="javascript:Ranking(0);">Top 10 Level</a> |
<a href="javascript:Ranking(1);">Top 10 Winner</a> |
<a href="javascript:Ranking(2);">Top 10 Looser</a>
</div>
<?php
// players list
// 
//	0 - Top 10 Level
//	1 - Top 10 Win
//	2 - Top 10 Lose
//	3 - Top 10 Collected gold

$query_str = "";
switch ($mode) {
	case 0:
		//  Top 10 Level
		$query_str .= "SELECT userid FROM users_info ORDER BY level DESC LIMIT 10";
		break;
	case 1:
		// Top 10 Win
		$query_str .= "SELECT COUNT(result_flag) AS total, userid FROM battle_result WHERE result_flag='0' AND userid!='-1' GROUP BY userid ORDER BY total DESC LIMIT 10";
		break;
	case 2:
		// Top 10 Lose
		$query_str .= "SELECT COUNT(result_flag) AS total, userid FROM battle_result WHERE result_flag='1' AND userid!='-1' GROUP BY userid ORDER BY total DESC LIMIT 10";
		break;
	case 3:
		// Top 10 Collected gold
		$query_str .= "SELECT userid FROM users_info ORDER BY gold DESC LIMIT 10";
		break;
}
$query = mysql_query($query_str);
$i = 0;
while ($data = mysql_fetch_assoc($query)) {
	$data_users = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE userid='".$data['userid']."'"));
	$name = $data_users['username'];
	if ($name == null || strlen($name) == 0) {
		$name = "Unknow";
	}
	$targetid = $data['userid'];
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
				<td width="200px">
					<!-- Name, Level -->
					<strong><?php echo $name; ?></strong><br />
					Level: <?php echo $data_users_info['level']; ?>
				</td>
				<td>
					<!-- Win, Lose -->
					Win: <?php echo $num_win; ?><br />
					Lose: <?php echo $num_lose; ?>
				</td>
			</tr>
		</table>
	</div>
<?php
	++$i;
}
if ($i == 0) {
?>
	<div class="modalitemnone">
		No records...
	</div>
<?php
}
?>