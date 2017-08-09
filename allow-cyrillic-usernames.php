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
add_filter('sanitize_user', 'acu_sanitize_user', 10, 3);
?>