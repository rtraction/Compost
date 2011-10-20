<?php
function post($var){
	if(!empty($_POST[$var])){
		return $_POST[$var];
	}
	return null;
}

function get($var){
	if(!empty($_GET[$var])){
		return $_GET[$var];
	}
	return null;
}

function request($var){
	if(!empty($_REQUEST[$var])){
		return $_REQUEST[$var];
	}
	return null;
}

function cookie($var){
	if(!empty($_COOKIE[$var])){
		return $_COOKIE[$var];
	}
	return null;
}

function session($var){
	if(!empty($_SESSION[$var])){
		return $_SESSION[$var];
	}
	return null;
}