<?php
include "../function.php";
if (checkLogin()) {
	$userid = $_POST['userid'];
	$token = $_POST['token'];
	$ggdp_token = null;
	if (isset($_POST['ggdp_token']))
		$ggdp_token = $_POST['ggdp_token'];
	if (!empty($ggdp_token)) {
?>
	<div class="modalitem">
		<table width="100%">
			<tr>
				<td width="50px" style="text-align: center;">
					<img src="images/item_gem.png" border="0" height="50px" />
				</td>
				<td>
					<div style="padding: 5px;">
						+1 Crystal<br />
						100 DE
					</div>
				</td>
				<td style="text-align: right;">
					<!-- Buy button -->
					<a href="javascript:void(0);" onclick="buyGGDPItem(<?php echo $userid;?>, '<?php echo $token; ?>', 100244);"><div class="modalbutton">ซื้อ</div></a>
				</td>
			</tr>
		</table>
	</div>
	<div class="modalitem">
		<table width="100%">
			<tr>
				<td width="50px" style="text-align: center;">
					<img src="images/item_gem.png" border="0" height="50px" />
				</td>
				<td>
					<div style="padding: 5px;">
						+10 Crystal<br />
						950 DE
					</div>
				</td>
				<td style="text-align: right;">
					<!-- Buy button -->
					<a href="javascript:void(0);" onclick="buyGGDPItem(<?php echo $userid;?>, '<?php echo $token; ?>', 100245);"><div class="modalbutton">ซื้อ</div></a>
				</td>
			</tr>
		</table>
	</div>
	<div class="modalitem">
		<table width="100%">
			<tr>
				<td width="50px" style="text-align: center;">
					<img src="images/item_gem.png" border="0" height="50px" />
				</td>
				<td>
					<div style="padding: 5px;">
						+100 Crystal<br />
						9000 DE
					</div>
				</td>
				<td style="text-align: right;">
					<!-- Buy button -->
					<a href="javascript:void(0);" onclick="buyGGDPItem(<?php echo $userid;?>, '<?php echo $token; ?>', 100246);"><div class="modalbutton">ซื้อ</div></a>
				</td>
			</tr>
		</table>
	</div>
<?php
	} else {
?>
	<div class="modalitemnone">
		ขอสงวนสิทธิ์การใช้เงิน DE เฉพาะผู้เล่นที่ล็อกอินผ่าน GGDP เท่านั้น<br />กรุณาล็อกอินเข้าเกมผ่านทางหน้าเว็บ <a href="http://www.ggdp.in.th/">http://www.ggdp.in.th/</a>
	</div>
<?php
	}
}