<?php
session_start();
include "function.php";
// General gameplay information
$userid = $_SESSION['userid'];
$token = $_SESSION['token'];
$userdata = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE userid='".$userid."'"));
$username = $userdata['username'];
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="0" />
		<title>Rune War</title>
		<script type="text/javascript" src="js/swfobject.js"></script>
		<script type="text/javascript" src="js/jquery-1.8.0.js"></script>
		<script type="text/javascript" src="js/jquery.reveal.js"></script>
		<script type="text/javascript" src="js/script.js"></script>
		<script type="text/javascript">
			/*
			$(window).resize(function() {
				updateContainer();
			});
			var interval = setInterval(function() {
				interval = window.clearInterval(interval);
				updateContainer();
			}, 500);
			*/
			$(window).resize(function() {
				updateContainerMargin();
			});
			var interval = setInterval(function() {
				interval = window.clearInterval(interval);
				updateContainerMargin();
			}, 1000);
			achievementsVerify(<?php echo $userid; ?>, '<?php echo $token; ?>');
			updateNotify(<?php echo $userid; ?>, '<?php echo $token; ?>');
			setInterval(function()
				{
					if (!isEnd) {
						//achievementsVerify(<?php echo $userid; ?>, '<?php echo $token; ?>');
						updateNotify(<?php echo $userid; ?>, '<?php echo $token; ?>');
					}
				},
				3000
			);
			app_url = "<?php echo APPLICATION_PATH; ?>";
			// Rune War SWF
			var flashvars = {
				'filepath' : '<?php echo APPLICATION_PATH; ?>swf/runewar.swf',
				'userid' : '<?php echo $userid; ?>',
				'token' : '<?php echo $token; ?>',
				'serviceurl' : '<?php echo APPLICATION_PATH; ?>',
				'server_ip' : '<?php echo SERVER_IP; ?>',
				'server_port' : '<?php echo SERVER_PORT; ?>',
				'crossdomainurl' : '<?php echo CROSSDOMAIN_PATH; ?>',
				'list_avatars_path' : '<?php echo $list_avatar_path; ?>',
				'list_skill_path' : '<?php echo $list_skill_path; ?>',
				'list_player_exp' : '<? echo $list_player_exp; ?>'
			};
			var params = {
				'bgcolor' : '#000000',
				'allowFullScreen' : 'true',
				'wmode' : 'direct',
				'allowScriptAccess' : 'always'
			};
			var attributes = {
				'id' : 'runewar'
			};
			swfobject.embedSWF("swf/loader.swf", "rwcontent", "960", "640", "11.0.0", "swf/expressInstall.swf", flashvars, params, attributes);
		</script>
		<link type="text/css" rel="stylesheet" media="all" href="css/general.css" />
		<link type="text/css" rel="stylesheet" media="all" href="css/reveal.css" />
	</head>
	<body id="body">
		<div id="container">
			<div class="warn-msg-panel" id="warning-msg" style="display: none;">
				<img src="images/paper.png" border="0" class="ui-game-paper" />
				<img src="images/npc_tutorial.png" border="0" class="ui-game-npc" />
				<div class="warn-msg-content">&nbsp;</div>
			</div>
			<?php
			if (empty($username) || strlen($username) == 0) {
			?>
			<div class="name-change-panel">
				<img src="images/paper.png" border="0" class="ui-game-paper" />
				<img src="images/npc_tutorial.png" border="0" class="ui-game-npc" />
				<table class="name-change-form">
					<tr>
						<td colspan="2"><strong>สวัสดีท่านผู้กล้า!!</strong><br /> ข้าชื่อโจว เป็นผู้ดูแลศิลาวิเศษแห่งนี้<br /><br />ข้าจะแนะนำการใช้งานศิลาเบื้องต้นให้แก่ท่าน<br /> แต่ตอนนี้ ข้าต้องการรู้ว่าท่านชื่ออะไร ?<br /><br /><br />กรุณาระบุชื่อตัวละครของท่าน</td>
					</tr>
					<tr>
						<td width="305px">
							<input type="text" id="username" maxlength="10" class="name-change-input" />
						</td>
						<td>
							<a href="javascript:changeName(<?php echo $userid; ?>, '<?php echo $token; ?>');" class="name-change-submit">ยืนยัน</a>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<span class="name-change-hint">
								(ใช้ตัวอักษร a-z, A-Z และตัวเลข 0-9 ในการตั้งชื่อ... อ้อ!! เกือบลืมไป ตั้งชื่อได้ตั้งแต่ 2-10 ตัวอักษรนะ)
							</span>
							<br />
							<br />
							<span class="name-change-msg" id="name-change-msg">&nbsp;</span>
						</td>
					</tr>
				</table>
			</div>
			<?php
			} else {
			?>
			<div id="tmenucontainer" class="top_menu">
				<div class="menu" id="menu-requests"><a href="javascript:void(0);" onclick="callFriendRequests(<?php echo $userid; ?>, '<?php echo $token; ?>');">Friends <span class="notify">0</span></a></div>
				<div class="menu" id="menu-stats"><a href="javascript:void(0);" onclick="callStats(<?php echo $userid; ?>, '<?php echo $token; ?>');">Statistics <span class="notify">0</span></a></div>
				<div class="menu" id="menu-achievements"><a href="javascript:void(0);" onclick="callAchievements(<?php echo $userid; ?>, '<?php echo $token; ?>');">Achievements <span class="notify">0</span></a></div>
				<div class="menu" id="menu-ranking"><a href="javascript:void(0);" onclick="callRanking();">Ranking</a></div>
				<div class="clear"></div>
			</div>
			<div id="swfcontainer">
				<div id="rwcontainer" class="unseen_position">
					<div id="rwcontent">
						<a href="http://www.adobe.com/go/getflashplayer">
							<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />
						</a>
					</div>
				</div>
				<div id="preloader">
					Loading...
				</div>
			</div>
			<?php
			}
			?>
			<div id="footercontainer" class="footer">
				<div style="width:595px; margin:0 auto;">
					<div style="float:left;">
						<img src="images/three_little_pigs_logo.png" height="90" border="0">
						<a href="http://www.truedigitalplus.com/" target="_blank">
							<img src="images/true_logo.png" height="90" border="0">
						</a>
					</div>
					<iframe src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2FRuneWar&amp;width=200&amp;height=62&amp;show_faces=false&amp;colorscheme=light&amp;stream=false&amp;border_color&amp;header=false&amp;appId=117373571768326" scrolling="no" frameborder="0" style="float:left; border:none; overflow:hidden; width:200px; height:62px; margin-top:10px; margin-left:20px;" allowTransparency="true"></iframe>
					<div class="clear"></div>
				</div>
			</div>
			<div id="dialog" class="reveal-modal">
				<h1></h1>
				<p><div class="modalcontainer"></div></p>
				<a class="close-reveal-modal">&#215;</a>
			</div>
		</div>
		<form action="play2.php" method="post" id="play">
			<input type="hidden" name="userid" id="userid" />
			<input type="hidden" name="token" id="token" />
		</form>
	</body>
</html>