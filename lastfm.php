<?php
/*
Plugin Name: LastFM Covers
Plugin URI: http://www.4mj.it/lightbox-js-v20-wordpress/
Description: Display covers of the last LastFM Tracks played by an user
Version: 1.0
Author: Jonathan Boyer
Author URI: http://www.grafikart.fr
Text Domain: lastfm
*/
 
$pluginDir = WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/';
$pluginURL = WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)).'/';

/**
 *	Set the Language
 **/
function SetLang() {
	load_plugin_textdomain( 'lastfm', $pluginDir."lang",plugin_basename(dirname(__FILE__)).'/lang' );
}

/**
 * 	Load the widget
 */
add_action( 'widgets_init', 'lastfm_load_widgets' );
function lastfm_load_widgets() {
	SetLang();
	include("lastfm.widget.php");
	register_widget( 'LastFM_Widget' );
}

/**
 *	Add CSS/Js into the head
 **/
add_action('wp_head', 'lastfm_head');
function lastfm_head(){
	$files = WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/lastfm/';
	?>
	<link rel="stylesheet" href="<?php echo $files; ?>theme/style.css" type="text/css" media="screen" />
	<?php if(get_option("lastfm_jquery",true)): ?>
		<script type="text/javascript" src="<?php echo $files; ?>js/jquery.js"></script>
	<?php endif; ?>
	<script type="text/javascript" src="<?php echo $files; ?>js/lastfm.js"></script>
	<?php
}

/*
 * 	Admin actions
 **/
add_action('admin_menu', 'lastfm_admin_actions'); 
function lastfm_admin() {  
    include('lastfm.admin.php');  
}  
   
function lastfm_admin_actions() {  
    add_options_page(__("LastFM Configuration","lastfm"),__("LastFM Configuration","lastfm"), 1,'lastfm-covers', "lastfm_admin");  
}  


?>