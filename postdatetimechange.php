<?php
/*
Plugin Name: Post Date Time Change
Plugin URI: http://wordpress.org/plugins/post-date-time-change/
Version: 2.2
Description: Collectively change the date and time of each article of post or page or media library.
Author: Katsushi Kawamori
Author URI: http://riverforest-wp.info/
Text Domain: postdatetimechange
Domain Path: /languages
*/

/*  Copyright (c) 2014- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

	load_plugin_textdomain('postdatetimechange', false, basename( dirname( __FILE__ ) ) . '/languages' );

	define("POSTDATETIMECHANGE_PLUGIN_BASE_FILE", plugin_basename(__FILE__));
	define("POSTDATETIMECHANGE_PLUGIN_BASE_DIR", dirname(__FILE__));
	define("POSTDATETIMECHANGE_PLUGIN_URL", plugins_url($path='',$scheme=null).'/post-date-time-change');

	require_once( POSTDATETIMECHANGE_PLUGIN_BASE_DIR.'/req/PostDateTimeChangeRegist.php' );
	$postdatetimechangeregist = new PostDateTimeChangeRegist();
	add_action('admin_init', array($postdatetimechangeregist, 'register_settings'));
	unset($postdatetimechangeregist);

	require_once( POSTDATETIMECHANGE_PLUGIN_BASE_DIR.'/req/PostDateTimeChangeAdmin.php' );
	$postdatetimechangeadmin = new PostDateTimeChangeAdmin();
	add_filter( 'plugin_action_links', array($postdatetimechangeadmin, 'settings_link'), 10, 2 );
	add_action( 'admin_menu', array($postdatetimechangeadmin, 'add_pages') );
	add_action( 'admin_enqueue_scripts', array($postdatetimechangeadmin, 'load_custom_wp_admin_style') );
	add_action( 'admin_footer', array($postdatetimechangeadmin, 'load_custom_wp_admin_style2') );
	unset($postdatetimechangeadmin);


?>