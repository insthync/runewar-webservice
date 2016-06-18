var socket;
var serverHost;
var serverPort;

function startNodeClient() {
	socket = io.connect(serverHost + ':' + serverPort);
	
	socket.on('loggedIn', function(data) {
		// Tell flash that logged in.
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbLoggedIn(jdata);
	});
	
	socket.on('playerInfoReceived', function(data) {
		// Tell flash that player information received.
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbPlayerInfoReceived(jdata);
	});
	
	socket.on('playerInfoError', function(data) {
		// Tell flash that player information received.
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbPlayerInfoError(jdata);
	});
	
	socket.on('returnOnlineStatus', function(data) {
		// Tell flash a online status information.
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbReturnOnlineStatus(jdata);
	});
	
	socket.on('receiveRequest', function(data) {
		// Tell flash that receive a request
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbReceiveRequest(jdata);
	});
	
	socket.on('requestUnable', function(data) {
		// Tell flash that sent request is unable
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbRequestUnable(jdata);
	});
	
	socket.on('requestExpire', function(data) {
		// Tell flash that player's request is expire
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbRequestExpire(jdata);
	});
	
	socket.on('initAsHost', function(data) {
		// Tell flash that init game as host
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbInitAsHost(jdata);
	});
	
	socket.on('initAsClient', function(data) {
		// Tell flash that init game as client
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbInitAsClient(jdata);
	});
	
	socket.on('requestDeclined', function(data) {
		// Tell flash that sent request is decline
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbRequestDeclined(jdata);
	});
	
	socket.on('loaded', function(data) {
		// Tell flash that game content from host and client is loaded
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbLoaded(jdata);
	});
	
	socket.on('countDown', function(data) {
		// Tell flash that what current countdown is
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbCountDown(jdata);
	});
	
	socket.on('gameStart', function(data) {
		// Tell flash that countdown ended and game start
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbGameStart(jdata);
	});
	
	socket.on('spawnCharacter', function(data) {
		// Tell flash that spawn new character from host
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbSpawnCharacter(jdata);
	});
	
	socket.on('appendCharacter', function(data) {
		// tell flash that append new character to field
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbAppendCharacter(jdata);
	});
	
	socket.on('updateEntity', function(data) {
		// tell flash that update any character
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbUpdateEntity(jdata);
	});
	
	socket.on('appendEntityDamage', function(data) {
		// tell flash that append any character's damage
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbAppendEntityDamage(jdata);
	});
	
	socket.on('characterAttackingTo', function(data) {
		// tell flash that append any character's damage
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbCharacterAttackingTo(jdata);
	});
	
	socket.on('cannonTo', function(data) {
		// tell flash that meteor attacking any character
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbCannonTo(jdata);
	});
	
	socket.on('meteorTo', function(data) {
		// tell flash that meteor attacking any character
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbMeteorTo(jdata);
	});
	
	socket.on('stunTo', function(data) {
		// tell flash that stun to any player's characters
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbStunTo(jdata);
	});
	
	socket.on('healTo', function(data) {
		// tell flash that heal to any players's base
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbHealTo(jdata);
	});
	
	socket.on('cannon', function(data) {
		// tell flash that calling cannon
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbCannon(jdata);
	});
	
	socket.on('meteor', function(data) {
		// tell flash that calling meteor
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbMeteor(jdata);
	});
	
	socket.on('stun', function(data) {
		// tell flash that calling stun
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbStun(jdata);
	});
	
	socket.on('heal', function(data) {
		// tell flash that calling heal
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbHeal(jdata);
	});
	
	socket.on('gameEnd', function(data) {
		// tell flash that calling game end
		var jdata = $.parseJSON(data);
		var flash =	document.getElementById("runewar");
		flash.cbGameEnd(jdata);
	});
}

function playerInfo(data) {
	if (socket != undefined)
		socket.emit('playerInfo', data);
}

function getOnlineStatus(data) {
	if (socket != undefined)
		socket.emit('getOnlineStatus', data);
}

function sendRequest(data) {
	if (socket != undefined)
		socket.emit('sendRequest', data);
}

function cancelRequest(data) {
	if (socket != undefined)
		socket.emit('cancelRequest', data);
}

function acceptRequest(data) {
	if (socket != undefined)
		socket.emit('acceptRequest', data);
}

function declineRequest(data) {
	if (socket != undefined)
		socket.emit('declineRequest', data);
}

function loaded(data) {
	if (socket != undefined)
		socket.emit('loaded', data);
}

function countDown(data) {
	if (socket != undefined)
		socket.emit('countDown', data);
}

function gameStart(data) {
	if (socket != undefined)
		socket.emit('gameStart', data);
}

function spawnCharacter(data) {
	if (socket != undefined)
		socket.emit('spawnCharacter', data);
}

function appendCharacter(data) {
	if (socket != undefined)
		socket.emit('appendCharacter', data);
}

function updateEntity(data) {
	if (socket != undefined)
		socket.emit('updateEntity', data);
}

function appendEntityDamage(data) {
	if (socket != undefined)
		socket.emit('appendEntityDamage', data);
}

function appendPlayerDamage(data) {
	if (socket != undefined)
		socket.emit('appendPlayerDamage', data);
}

function characterAttackingTo(data) {
	if (socket != undefined)
		socket.emit('characterAttackingTo', data);
}

function cannonTo(data) {
	if (socket != undefined)
		socket.emit('cannonTo', data);
}

function meteorTo(data) {
	if (socket != undefined)
		socket.emit('meteorTo', data);
}

function stunTo(data) {
	if (socket != undefined)
		socket.emit('stunTo', data);
}

function healTo(data) {
	if (socket != undefined)
		socket.emit('healTo', data);
}

function cannon(data) {
	if (socket != undefined)
		socket.emit('cannon', data);
}

function meteor(data) {
	if (socket != undefined)
		socket.emit('meteor', data);
}

function stun(data) {
	if (socket != undefined)
		socket.emit('stun', data);
}

function heal(data) {
	if (socket != undefined)
		socket.emit('heal', data);
}

function gameEnd(data) {
	if (socket != undefined)
		socket.emit('gameEnd', data);
}