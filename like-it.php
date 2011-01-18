<?php
/*
Plugin Name: Like-it
Plugin URI: http://nyordanov.com/projects
Description: Like-it allows post readers mark their approval of a post by clicking the Like-it button, instead of posting yet another "I like this post" comment
Version: 2.2
Author: Nikolay Yordanov
Author URI: http://nyordanov.com
License: GPLv2

This program is free software; you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by 
the Free Software Foundation; version 2 of the License.

This program is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
GNU General Public License for more details. 

You should have received a copy of the GNU General Public License 
along with this program; if not, write to the Free Software 
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA 
*/

if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

require_once 'widget.php';

global $likeit_table;
global $likeit_dbVersion;
global $wpdb;

$likeit_table = $wpdb->prefix . 'likeit';
$likeit_dbVersion = '1.0';

// create database and save default options

register_activation_hook(__FILE__, 'likeit_activate');
function likeit_activate() {
	global $wpdb, $likeit_dbVersion;
	
	$likeit_table = $wpdb->prefix . 'likeit';
	
	if($wpdb->get_var("show tables like '$likeit_table'") != $likeit_table) {
		$sql = "CREATE TABLE  $likeit_table  (
			id INT(20) NOT NULL AUTO_INCREMENT,
			time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			post_id INT(20) NOT NULL,
			ip VARCHAR(15) NOT NULL,
			UNIQUE KEY id (id)
		);";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);

		update_option("likeit_dbVersion", $likeit_dbVersion);
	}
	
	add_option('likeit-text', 'Like!', '', 'yes');
	add_option('likeit-autodisplay', 'on', '', 'yes');
	add_option('likeit-per-page', '30', '', 'yes');
}

// delete table during uninstall
register_uninstall_hook(__FILE__, 'likeit_uninstall');
function likeit_uninstall() {
	global $wpdb, $likeit_table;
	
	if(defined(WP_UNINSTALL_PLUGIN)) {
		$wpdb->query("DELETE TABLE IF EXISTS $likeit_table");
		delete_option('likeit-text');
		delete_option('likeit-autodisplay');
	}
}

// add menus

add_action('admin_menu', 'likeit_config_page');
function likeit_config_page() {
	if(function_exists('add_menu_page'))
		add_menu_page( __('Like-it'), __('Like-it'), 'manage_options', 'likeit', 'likeit_stats' );
	if(function_exists('add_submenu_page')) {
		add_submenu_page( 'likeit', __('Like-it Stats'), __('Like-it Stats'), 'manage_options', 'likeit', 'likeit_stats');
		add_submenu_page( 'likeit', __('Like-it Configuration'), __('Like-it Configuration'), 'manage_options', 'likeit_conf', 'likeit_conf');
	}
}

// statistics page

function likeit_stats() {
	global $wpdb, $likeit_table;

	$page = (isset($_GET['paged'])) ? intval($_GET['paged']) : 1;
	$likeit_per_page = get_option('likeit-per-page');
	$from = ($page-1) * $likeit_per_page;
	$likes = $wpdb->get_results("SELECT * FROM $likeit_table LIMIT $from, $likeit_per_page");
	
	foreach($likes as &$like) {
		$ipinfo_url = 'http://ipinfodb.com/ip_query.php?ip='.$like->ip.'&output=json&timezone=false';
		if(function_exists('curl_init')) {
			$request = curl_init();
			curl_setopt($request, CURLOPT_URL, $ipinfo_url);
			curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
			$like->ip_info = curl_exec($request);
			curl_close($request);
		}
		else
			$like->ip_info = file_get_contents($ipinfo_url);

		$like->ip_info = json_decode($like->ip_info);
		
		$like->post_url = get_permalink($like->post_id);
		$like->post_title = get_post($like->post_id)->post_title;
		
		$like->post_liked_count = likeit_get_count_by_post_id($like->post_id);
	}

	$total_likes = $wpdb->get_var("SELECT COUNT(*) FROM $likeit_table");
	
	require('tpl/stats.php');
}

// plugin config page 

function likeit_conf() {
	
	if( isset($_POST['likeit-text']) ) {
        update_option( 'likeit-text', $_POST['likeit-text'] );
		update_option( 'likeit-autodisplay', $_POST['likeit-autodisplay'] );
		
		$updated = true;
	}
	
	require('tpl/config.php');
}

add_action('admin_init', 'likeit_register_settings');
function likeit_register_settings() {
	register_setting( 'likeit_options', 'likeit-text' );
}

// add javascript
add_action('wp_print_scripts', 'likeit_scripts');
function likeit_scripts() {
	wp_enqueue_script( 'likeit', plugin_dir_url(__FILE__). 'content/like-it.js', array('jquery'), '0.1');
	wp_localize_script( 'likeit', 'likeit', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}

// add css
add_action('wp_print_styles', 'likeit_styles');
function likeit_styles() {
	wp_enqueue_style('likeit', plugin_dir_url(__FILE__).'content/like-it.css', false, '0.1', 'all');
}

// add filter to echo the Like-it button
add_filter('the_content', 'likeit_button_filter');
function likeit_button_filter($content) {
	if( !(is_page() || is_feed()) && get_option('likeit-autodisplay') == 'on' )
		$content .= likeit_get_button();
	return $content;
}

// template function to echo the button
function likeit_button() {
	echo likeit_get_button();
}

// generate the Like-it button
function likeit_get_button() {
	$canvote = likeit_can_vote(get_the_ID(), $_SERVER['REMOTE_ADDR']) ? 'likeit-canvote' : 'likeit-voted';
	$text = stripslashes(get_option('likeit-text'));
	$id = get_the_ID();
	$count = likeit_get_count_by_post_id($id);
	
	$button = <<<BUTTON
	<div class="likeit-button $canvote">
		<div class="likeit-text" id="likeit_$id">$text</div><div class="likeit-count"><span>$count</span></div>
	</div>
BUTTON;
	
	return $button;
}

// get like count for a post_id
function likeit_get_count_by_post_id($post_id) {
	global $wpdb, $likeit_table;
	
	return intval($wpdb->get_var("SELECT COUNT(id) FROM $likeit_table WHERE post_id = $post_id"));
}

// can this IP like this post (false if already voted)
function likeit_can_vote($post_id, $ip) {
	global $wpdb, $likeit_table;
	
	return $wpdb->get_var("SELECT COUNT(id) FROM $likeit_table WHERE post_id = $post_id AND ip = '$ip'") == 0;
}

// save a new vote
add_action('wp_ajax_nopriv_likeit_register_vote', 'likeit_register_vote');
add_action('wp_ajax_likeit_register_vote', 'likeit_register_vote');
function likeit_register_vote() {
	global $wpdb, $likeit_table;
	
	$id = intval($_POST['id']);
	$ip = $_SERVER['REMOTE_ADDR'];
	
 	if(likeit_can_vote($id, $ip))
		$wpdb->query("INSERT INTO $likeit_table (post_id, ip) VALUES ($id, '$ip')");
	
	echo likeit_get_count_by_post_id($id);
	exit;
}
