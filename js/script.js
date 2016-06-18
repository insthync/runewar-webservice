var fb_app_id;
var app_url;
var ggdp_token;
var loading = false;
var isEnd = false;
var ggdp_dialog;
function alertFromFlash(text) {
	alert(text);
}

function contentLoadPC(msg) {
	$("#preloader").html(msg);
}

function showRW() {
	$("#preloader").css("display", "none");
	//$("#runewar").css("visibility", "visible");
	$("#rwcontainer").removeClass("unseen_position");
}

function updateRW(userid, token) {
	$.post('user_general.php', 
		{
			'userid' : userid,
			'token' : token
		}, 
		function(data) {
			var jdata = $.parseJSON(data);
			var flash =	document.getElementById("runewar");
			flash.updateUserInfo(jdata.user);
		}
	);
}

function updateContainer() {
	var container_h = $("#container").height();
	var set_w = container_h * 1.5;
	$("#container").css("width", set_w + "px");
}

function updateContainerMargin() {
	var document_h = $(document).height();
	var container_h = $("#container").height();
	var set_margin_top = (document_h - container_h) / 2;
	set_margin_top = set_margin_top > 0 ? set_margin_top : 0;
	$("#container").css("margin-top", set_margin_top + "px");
}

function callAchievements(userid, token) {
	$('#dialog').reveal();
	$("#rwcontainer").addClass("unseen_position");
	//$("#rwcontainer").removeClass("unseen_position");
	$('#dialog .modalcontainer').html("<div class='modalloading'></div>");
	$('#dialog h1').html("Achievements");
	$.post('client/com_achievements.php', 
		{
			'userid' : userid,
			'token' : token
		}, 
		function(data) {
			$('#dialog div').html("");
			$('#dialog div').html(data);
		}
	);
}

function useAchievements(userid, token, achievement_id) {
	$('#dialog .modalcontainer').append("<div class='modalloading'></div>");
	$.post('client/com_achievements.php', 
		{
			'userid' : userid,
			'token' : token,
			'achievement_id' : achievement_id
		}, 
		function(data) {
			$('#dialog div').html("");
			$('#dialog div').html(data);
		}
	);
}

function requestFriend(userid, token, targetid) {
	$.post('user_friend_request.php',
		{
			'userid' : userid,
			'token' : token,
			'targetid' : targetid
		},
		function(data) {
			//alert(data);
			// Get self user data to check if facebook id defined
			
			var jdata = $.parseJSON(data);
			$.post('user_general.php', 
				{
					'userid' : userid,
					'token' : token
				}, 
				function(data1) {
					var jdata1 = $.parseJSON(data1);
					if (jdata1.msgid == 0) {
						var userdata1 = jdata1.user;
						var username1 = userdata1.username;
						var facebookid1 = userdata1.facebookid;
						if (facebookid1 != undefined && facebookid1 > 0) {
							// Get target user data to check if facebook id defined
							$.post('user_general.php', 
								{
									'userid' : userid,
									'token' : token,
									'targetid' : targetid
								}, 
								function(data2) {
									var jdata2 = $.parseJSON(data2);
									if (jdata2.msgid == 0) {
										var userdata2 = jdata2.user;
										var facebookid2 = userdata2.facebookid;
										if (facebookid2 != undefined && facebookid2 > 0) {
											// Now 2 facebook user is defined so user possible to send request
											
											// calling the API ...
											/*
											$.post('facebook/publish_stream.php', {
												'type' : 'friend_request',
												'username' : username1,
												'from_facebookid' : facebookid1,
												'to_facebookid' : facebookid2
											});
											*/
											FB.ui({
												method : 'feed',
												link : 'http://apps.facebook.com/' + fb_app_id,
												redirect_uri : app_url + '/blank.php',
												picture : app_url + 'icon/fb_feed_addfriend.png',
												from : '' + facebookid1,
												to : '' + facebookid2,
												name : 'Rune War',
												caption : 'ผู้เล่นขอคุณเป็นเพื่อนร่วมรบใน Rune War!!',
												description : 'ผู้เล่นต้องการคุณเป็นเพื่อนร่วมต่อสู้ด้วยศิลาศักดิ์สิทธิ์ในเกม Rune War.'
											},
											function (response) {
												// Callback after feed posted...
												console.log('publishStory UI response: ', response);
											});
											
										}
									}
								}
							);
						}
					}
				}
			);
			$("#userid_" + targetid).fadeOut(400, function() {
				$("#userid_" + targetid).remove();
			});
		}
	);
}

function acceptFriend(userid, token, targetid) {
	$.post('user_friend_accept.php',
		{
			'userid' : userid,
			'token' : token,
			'targetid' : targetid
		},
		function(data) {
			//alert(data);
			var jdata = $.parseJSON(data);
			$("#userid_" + targetid).fadeOut(400, function() {
				$("#userid_" + targetid).remove();
			});
		}
	);
}

function declineFriend(userid, token, targetid) {
	$.post('user_friend_decline.php',
		{
			'userid' : userid,
			'token' : token,
			'targetid' : targetid
		},
		function(data) {
			//alert(data);
			var jdata = $.parseJSON(data);
			$("#userid_" + targetid).fadeOut(400, function() {
				$("#userid_" + targetid).remove();
			});
		}
	);
}

function callBuyHeart(userid, token) {
	$('#dialog').reveal();
	$('#rwcontainer').addClass("unseen_position");
	//$("#rwcontainer").removeClass("unseen_position");
	$('#dialog .modalcontainer').html("<div class='modalloading'></div>");
	$('#dialog h1').html("Buy Heart");
	$.post('client/com_buyheart.php', 
		{
			'userid' : userid,
			'token' : token
		}, 
		function(data) {
			$('#dialog .modalcontainer').html("");
			$('#dialog .modalcontainer').html(data);
		}
	);
}

function buyHeart(userid, token, purse_type, quantity) {
	$('#dialog .modalcontainer').html("<div class='modalloading'></div>");
	$.post('client/com_buyheart.php', 
		{
			'userid' : userid,
			'token' : token,
			'purse_type' : purse_type,
			'quantity' : quantity
		}, 
		function(data) {
			$('#dialog .modalcontainer').html("");
			$('#dialog .modalcontainer').html(data);
			updateRW(userid, token);
		}
	);
}

function callBuyGem(userid, token) {
	$('#dialog').reveal();
	$('#rwcontainer').addClass("unseen_position");
	//$("#rwcontainer").removeClass("unseen_position");
	$('#dialog .modalcontainer').html("<div class='modalloading'></div>");
	$('#dialog h1').html("Buy Crystal");
	$.post('client/com_buygem.php', 
		{
			'userid' : userid,
			'token' : token,
			'ggdp_token' : ggdp_token
		}, 
		function(data) {
			$('#dialog .modalcontainer').html("");
			$('#dialog .modalcontainer').html(data);
		}
	);
}

function callAddFriends(userid, token) {
	$('#dialog').reveal();
	$('#rwcontainer').addClass("unseen_position");
	//$("#rwcontainer").removeClass("unseen_position");
	$('#dialog .modalcontainer').html("<div class='modalloading'></div>");
	$('#dialog h1').html("Add Friends");
	$.post('client/com_addfriends.php', 
		{
			'userid' : userid,
			'token' : token
		}, 
		function(data) {
			$('#dialog .modalcontainer').html("");
			$('#dialog .modalcontainer').html(data);
		}
	);
}

function seeMoreAddFriends(userid, token, from) {
	$('#dialog .modalcontainer').html("<div class='modalloading'></div>");
	$.post('client/com_addfriends.php', 
		{
			'userid' : userid,
			'token' : token
		}, 
		function(data) {
			$('#dialog .modalcontainer').html("");
			$('#dialog .modalcontainer').append(data);
		}
	);
}

function searchAddFriends(userid, token, divid) {
	var search = $('#' + divid).val();
	$('#dialog .modalcontainer').html("<div class='modalloading'></div>");
	$.post('client/com_addfriends.php', 
		{
			'userid' : userid,
			'token' : token,
			'search' : search
		}, 
		function(data) {
			$('#dialog .modalcontainer').html("");
			$('#dialog .modalcontainer').append(data);
		}
	);
}

function callFriendRequests(userid, token) {
	$('#dialog').reveal();
	$('#rwcontainer').addClass("unseen_position");
	//$("#rwcontainer").removeClass("unseen_position");
	$('#dialog .modalcontainer').html("<div class='modalloading'></div>");
	$('#dialog h1').html("Friend Requests");
	$.post('client/com_friendrequests.php', 
		{
			'userid' : userid,
			'token' : token
		}, 
		function(data) {
			$('#dialog .modalcontainer').html("");
			$('#dialog .modalcontainer').html(data);
		}
	);
}

function seeMoreFriendRequests(userid, token, from) {
	$('.modalseemore').remove();
	$('#dialog .modalcontainer').append("<div class='modalloading'></div>");
	$.post('client/com_friendrequests.php', 
		{
			'userid' : userid,
			'token' : token,
			'from' : from
		}, 
		function(data) {
			$('.modalloading').remove();
			$('#dialog .modalcontainer').append(data);
		}
	);
}

function callRanking(mode) {
	$('#dialog').reveal();
	$('#rwcontainer').addClass("unseen_position");
	//$("#rwcontainer").removeClass("unseen_position");
	$('#dialog .modalcontainer').html("<div class='modalloading'></div>");
	$('#dialog h1').html("Ranking");
	$.get('client/com_ranking.php', 
		{
			'mode' : mode
		}, 
		function(data) {
			$('#dialog .modalcontainer').html("");
			$('#dialog .modalcontainer').html(data);
		}
	);
}

function Ranking(mode) {
	$('#dialog .modalcontainer').html("<div class='modalloading'></div>");
	$.get('client/com_ranking.php', 
		{
			'mode' : mode
		}, 
		function(data) {
			$('#dialog .modalcontainer').html("");
			$('#dialog .modalcontainer').html(data);
		}
	);
}

function callStats(userid, token) {
	$('#dialog').reveal();
	$('#rwcontainer').addClass("unseen_position");
	//$("#rwcontainer").removeClass("unseen_position");
	$('#dialog .modalcontainer').html("<div class='modalloading'></div>");
	$('#dialog h1').html("Fight Statistics");
	$.post('client/com_stats.php', 
		{
			'userid' : userid,
			'token' : token
		}, 
		function(data) {
			$('#dialog .modalcontainer').html("");
			$('#dialog .modalcontainer').html(data);
		}
	);
}

function seeMoreStats(userid, token, from) {
	$('.modalseemore').remove();
	$('#dialog .modalcontainer').append("<div class='modalloading'></div>");
	$.post('client/com_stats.php', 
		{
			'userid' : userid,
			'token' : token,
			'from' : from
		}, 
		function(data) {
			$('.modalloading').remove();
			$('#dialog .modalcontainer').append(data);
		}
	);
}

function updateNotify(userid, token) {
	$.post('user_notify_number.php', 
		{
			'userid' : userid,
			'token' : token
		}, 
		function(data) {
			var jdata = $.parseJSON(data);
			if (jdata.msgid == 0) {
				// If success
				$('#menu-requests .notify').html(jdata.friend_request);
				$('#menu-stats .notify').html(jdata.battle_result);
				$('#menu-achievements .notify').html(jdata.achievement_unlock);
			}
			if (jdata.msgid == 3) {
				// If have to re-login
				$('#tmenucontainer').remove();
				$('#swfcontainer').remove();
				$('#warning-msg .warn-msg-content').html("<strong>คำเตือน</strong><br /><br />ขณะนี้มีผู้เล่นอื่นกำลังล็อกอินไอดีของท่านซ้อนอยู่<br /><br /><span style='font-size: 17pt;'>กรุณาทำการล็อกอินใหม่ !!</span>");
				$('#warning-msg').css('display', 'block');
				isEnd = true;
			}
		}
	);
}

function changeName(userid, token) {
	var username = $('#username').val();
	$("#name-change-msg").css("color", "#000");
	$("#name-change-msg").html("กรุณารอสักครู่...");
	$.post('user_changename.php',
		{
			'userid' : userid,
			'token' : token,
			'username' : username
		},
		function(data) {
			var jdata = $.parseJSON(data);
			if (jdata.msgid == 0) {
				// Reload
				$("#name-change-msg").html("");
				$('#userid').val(userid);
				$('#token').val(token);
				$('#play').submit();
			} else if (jdata.msgid == 1) {
				var reason = jdata.reason;
				if (reason == "INVALID_FORMAT") {
					$("#name-change-msg").css("color", "#f00");
					$("#name-change-msg").html("* ต้องใช้ตัวอักษร a-z, A-Z ตัวเลข 0-9 และมีความยาวตั้งแต่ 2-10 ตัวอักษร ในการตั้งชื่อ *");
				}
				if (reason == "DUPLICATED") {
					$("#name-change-msg").css("color", "#f00");
					$("#name-change-msg").html("* ชื่อตัวละครซ้ำ คิดชื่อที่เด็ดๆ กว่านี้หน่อยสิ :D *");
				}
			} else {
				// Wrong user token !
			}
		}
	);
}

function shareLevelUp(userid, token) {
	$.post('user_general.php', 
		{
			'userid' : userid,
			'token' : token
		}, 
		function(data) {
			var jdata = $.parseJSON(data);
			if (jdata.msgid == 0) {
				// If success
				var userdata = jdata.user;
				var level = userdata.level;
				var facebookid = userdata.facebookid;
				if (facebookid != undefined && facebookid > 0) {
					// Share to facebook
					
					// calling the API ...
					/*
					$.post('facebook/publish_stream.php', {
						'type' : 'levelup',
						'level' : level,
						'to_facebookid' : facebookid
					});
					*/
					
					FB.ui({
						method : 'feed',
						link : 'http://apps.facebook.com/' + fb_app_id,
						redirect_uri : app_url + '/blank.php',
						picture : app_url + 'icon/fb_feed_lvup.png',
						name : 'Rune War',
						caption : 'ผู้เล่นเลื่อนเลเวลเป็นเลเวล ' + level + '!!',
						description : 'ผู้เล่นสามารถเอาชนะผู้คู่ต่อสู้มากมายด้วยศิลาศักดิ์สิทธิ์ในเกม Rune War จนเลื่อนเลเวลเป็นเลเวล ' + level + '.'
					},
					function callback(response) {
						// Callback after feed posted...
						console.log('publishStory UI response: ', response);
					});
					
				}
			}
		}
	);
}


function achievementsVerify(userid, token) {
	$.post('achievements_true.php', 
		{
			'userid' : userid,
			'token' : token
		}, 
		function(data) {
			var jdata = $.parseJSON(data);
			if (jdata.msgid == 0) {
				var unlock_list = jdata.unlock_list;
				var userdata = jdata.user;
				var facebookid = userdata.facebookid;
				if (facebookid != undefined && facebookid > 0) {
					for (var i = 0; i < unlock_list.length; ++i) {
						$.post('facebook/publish_stream.php', {
							'type' : 'achievement_unlock',
							'achievement_id' : unlock_list[i],
							'to_facebookid' : facebookid
						});
					}
				}
			}
		}
	);
}

function buyGGDPItem(userid, token, itemid) {
	
	var location = "http://ggdp.in.th/GGDP_BuyItem.aspx?token=" + ggdp_token + "&appid=100004&itemid=" + itemid;
	var winWidth = 800;
	var winHeight = 600;
	var posLeft = ( screen.width - winWidth ) / 2;
	var posTop = ( screen.height - winHeight ) / 2;
	if (ggdp_dialog == undefined || ggdp_dialog.closed) {
		ggdp_dialog = window.open( location, '_blank', 'width=' + winWidth + ',height=' + winHeight +',top=' + posTop + ',left=' + posLeft +
			',resizable=no,scrollbars=no,toolbar=no,titlebar=no,' +
			'location=no,directories=no,status=no,menubar=no,copyhistory=no');
	} else {
		ggdp_dialog.focus();
	}
	var interval = setInterval(function() {
		if (ggdp_dialog.closed) {
			interval = window.clearInterval(interval);
			updateRW(userid, token);
		}
	}, 1000);
}