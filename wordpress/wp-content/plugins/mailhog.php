<?php
/**
 * Plugin Name: MailHog
 * Description: Catches all outgoing mail and redirects to mailhog:1025.
 * Author: Tyler Kowalchuk
 * Version: 1.0
 * Requires PHP: 7.4
 * Text Domain: mailhog
 */

// set from email since "wordpress@localhost" is an invalid domain
// php >= 7.4
add_filter( 'wp_mail_from', fn($email) => is_email($email) ? $email : 'wordpress@localhost.dev' );

// php < 7.4
//add_filter( 'wp_mail_from', function($email){
//	return is_email($email) ? $email : 'wordpress@localhost.dev';
//});

// redirect all emails to mailhog
// host and port defined in docker-compose.yml
add_action( 'phpmailer_init', function ( $php_mailer ) {
	$php_mailer->Host     = 'mailhog';
	$php_mailer->Port     = 1025;
	$php_mailer->IsSMTP();
}, 10 );

// FYI, this could also be done without creating this plugin.
// Plugins like "WP Mail SMTP" can do this as well, but require
// configuring the Host, Port, From, TLS, etc. Our plugin will
// allow us, as a "business", to have a consistent development environment
// without having to configure a pre-built plugin for each installation.