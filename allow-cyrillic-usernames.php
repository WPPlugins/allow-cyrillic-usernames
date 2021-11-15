<?php
/*
Plugin Name: Allow Cyrillic Usernames
Plugin URI: https://wordpress.org/plugins/allow-cyrillic-usernames/
Description: Allows users to register with Cyrillic usernames.
Author: Sergey Biryukov
Author URI: http://sergeybiryukov.ru/
Version: 0.1
Text Domain: allow-cyrillic-usernames
*/ 

function acu_sanitize_user($username, $raw_username, $strict) {
	$username = wp_strip_all_tags( $raw_username );
	$username = remove_accents( $username );
	// Kill octets
	$username = preg_replace( '|%([a-fA-F0-9][a-fA-F0-9])|', '', $username );
	$username = preg_replace( '/&.+?;/', '', $username ); // Kill entities

	// If strict, reduce to ASCII and Cyrillic characters for max portability.
	if ( $strict )
		$username = preg_replace( '|[^a-zа-я0-9 _.\-@]|iu', '', $username );

	$username = trim( $username );
	// Consolidate contiguous whitespace
	$username = preg_replace( '|\s+|', ' ', $username );

	return $username;
}

function acu_pre_user_create($data, $update, $id, $userdata) {
	if (mb_strlen($data['user_nicename']) > 50) {
		$data['user_nicename'] = mb_substr(cyrlat($data['user_login']),0,50);
	}
	return $data;
}

function cyrlat($text) {
	$cyr = [
		'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п',
		'р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
		'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П',
		'Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я'
	];
	$lat = [
			'a','b','v','g','d','e','io','zh','z','i','y','k','l','m','n','o','p',
			'r','s','t','u','f','h','ts','ch','sh','sht','a','i','y','e','yu','ya',
			'A','B','V','G','D','E','Io','Zh','Z','I','Y','K','L','M','N','O','P',
			'R','S','T','U','F','H','Ts','Ch','Sh','Sht','A','I','Y','e','Yu','Ya'
	];
	return str_replace($cyr, $lat, $text);
}

add_filter('sanitize_user', 'acu_sanitize_user', 10, 3);
add_filter('wp_pre_insert_user_data', 'acu_pre_user_create', 10, 4);
?>
