<?php
include "../function.php";
if (checkLogin()) {
	$userid = $_POST['userid'];
	$token = $_POST['token'];
?>
	<div class="modalitem">
		<table width="100%">
			<tr>
				<td width="50px" style="text-align: center;">
					<img src="images/item_heart.png" border="0" height="50px" />
				</td>
				<td>
					<div style="padding: 5px;">
						+1 Heart<br />
						<img src="images/icon_gem.png" border="0" height="16px" /> 3
						&nbsp;
						<img src="images/icon_money.png" border="0" height="16px" /> 250
					</div>
				</td>
				<td style="text-align: right;">
					<!-- Buy button -->
					<a href="javascript:buyHeart(<?php echo $userid; ?>, '<?php echo $token; ?>', <?php echo $purse_types['gold']; ?>, 1);"><div class="modalbutton"><img src="images/icon_money.png" border="0" height="16px" /> ซื้อด้วยทอง</div></a>
					<a href="javascript:buyHeart(<?php echo $userid; ?>, '<?php echo $token; ?>', <?php echo $purse_types['crystal']; ?>, 1);"><div class="modalbutton"><img src="images/icon_gem.png" border="0" height="16px" /> ซื้อด้วยคริสตัล</div></a>
				</td>
			</tr>
		</table>
	</div>
	<div class="modalitem">
		<table width="100%">
			<tr>
				<td width="50px" style="text-align: center;">
					<img src="images/item_heart.png" border="0" height="50px" />
				</td>
				<td>
					<div style="padding: 5px;">
						+3 Heart<br />
						<img src="images/icon_gem.png" border="0" height="16px" /> 8
						&nbsp;
						<img src="images/icon_money.png" border="0" height="16px" /> 700
					</div>
				</td>
				<td style="text-align: right;">
					<!-- Buy button -->
					<a href="javascript:buyHeart(<?php echo $userid; ?>, '<?php echo $token; ?>', <?php echo $purse_types['gold']; ?>, 3);"><div class="modalbutton"><img src="images/icon_money.png" border="0" height="16px" /> ซื้อด้วยทอง</div></a>
					<a href="javascript:buyHeart(<?php echo $userid; ?>, '<?php echo $token; ?>', <?php echo $purse_types['crystal']; ?>, 3);"><div class="modalbutton"><img src="images/icon_gem.png" border="0" height="16px" /> ซื้อด้วยคริสตัล</div></a>
				</td>
			</tr>
		</table>
	</div>
	<div class="modalitem">
		<table width="100%">
			<tr>
				<td width="50px" style="text-align: center;">
					<img src="images/item_heart.png" border="0" height="50px" />
				</td>
				<td>
					<div style="padding: 5px;">
						+5 Heart<br />
						<img src="images/icon_gem.png" border="0" height="16px" /> 12
						&nbsp;
						<img src="images/icon_money.png" border="0" height="16px" /> 1150
					</div>
				</td>
				<td style="text-align: right;">
					<!-- Buy button -->
					<a href="javascript:buyHeart(<?php echo $userid; ?>, '<?php echo $token; ?>', <?php echo $purse_types['gold']; ?>, 5);"><div class="modalbutton"><img src="images/icon_money.png" border="0" height="16px" /> ซื้อด้วยทอง</div></a>
					<a href="javascript:buyHeart(<?php echo $userid; ?>, '<?php echo $token; ?>', <?php echo $purse_types['crystal']; ?>, 5);"><div class="modalbutton"><img src="images/icon_gem.png" border="0" height="16px" /> ซื้อด้วยคริสตัล</div></a>
				</td>
			</tr>
		</table>
	</div>
	<div style="color: #f00">
<?php
	$user_info = mysql_fetch_assoc(mysql_query("SELECT * FROM users_info WHERE userid='".$userid."'"));
	$data_stats = mysql_fetch_assoc(mysql_query("SELECT * FROM users_stats WHERE userid='".$userid."'"));
	$spent_gold = (int)$data_stats['spent_gold'];
	$spent_crystal = (int)$data_stats['spent_crystal'];
	if (isset($_POST['quantity']) && isset($_POST['purse_type'])) {
		$quantity = (int)$_POST['quantity'];
		$price_gold = -1;
		$price_crystal = -1;
		switch ($quantity) {
			case 1:
				$price_gold = 250;
				$price_crystal = 3;
			break;
			case 3:
				$price_gold = 700;
				$price_crystal = 8;
			break;
			case 5:
				$price_gold = 1150;
				$price_crystal = 12;
			break;
		}
		$purse_type = $_POST['purse_type'];
		switch ($purse_type) {
			case $purse_types['gold']:
				$spent_gold += $price_gold;
				if ($price_gold >= 0) {
					if ($user_info['crystal'] >= $price_gold) {
						$changed_gold = $user_info['gold'] - $price_gold;
						$data_player = mysql_fetch_assoc(mysql_query("SELECT heartnum FROM users WHERE userid='".$userid."'"));
						$heartnum = (int)$data_player['heartnum'] + $quantity;
						mysql_query("UPDATE users_stats SET spent_gold='".$spent_gold."', spent_crystal='".$spent_crystal."' WHERE userid='".$userid."'");
						$query1 = mysql_query("UPDATE users SET heartnum='".$heartnum."' WHERE userid='".$userid."'");
						$query2 = mysql_query("UPDATE users_info SET gold='".$changed_gold."' WHERE userid='".$userid."'");
					} else {
						// Not enough gold
						echo "มีทองไม่พอ :(";
					}
				}
			break;
			case $purse_types['crystal']:
				$spent_crystal += $price_crystal;
				if ($price_crystal >= 0) {
					if ($user_info['crystal'] >= $price_crystal) {
						$changed_crystal = $user_info['crystal'] - $price_crystal;
						$data_player = mysql_fetch_assoc(mysql_query("SELECT heartnum FROM users WHERE userid='".$userid."'"));
						$heartnum = (int)$data_player['heartnum'] + $quantity;
						mysql_query("UPDATE users_stats SET spent_gold='".$spent_gold."', spent_crystal='".$spent_crystal."' WHERE userid='".$userid."'");
						$query1 = mysql_query("UPDATE users SET heartnum='".$heartnum."' WHERE userid='".$userid."'");
						$query2 = mysql_query("UPDATE users_info SET crystal='".$changed_crystal."' WHERE userid='".$userid."'");
					} else {
						// Not enough crystal
						echo "มีคริสตัลไม่พอ :(";
					}
				}
			break;
		}
	}
	?>
	</div>
	<?php
}
?>