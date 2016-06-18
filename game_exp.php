<?php
	include "function.php";
	$arr = array();
	$arr['player_exp'] = $list_player_exp;
	$arr['gain_exp'] = $list_gain_exp;
	echo json_encode($arr);
?>