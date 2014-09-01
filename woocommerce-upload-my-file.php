<?php
/*
Plugin Name: WooCommerce Upload My File
Plugin URI: http://wordpress.geev.nl/product/woocommerce-upload-my-file/
Description: This plugin provides the possibility to upload files in WooCommerce after ordering. - Free Version
Version: 0.3.4
Author: Geev vormgeeving
Author URI: http://wordpress.geev.nl/
*/
/*  Copyright 2012  Geev  (email : wordpress@geev.nl)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
require_once('inc/umf-funct.php');

/* Check if WooCommerce is active */
if (is_woocommerce_active()) {
	load_plugin_textdomain('woocommerce-umf', false, dirname( plugin_basename( __FILE__ ) ) . '/languages');
	require_once('admin/umf-settings.php');
	add_action('admin_menu', 'woocommerce_umf_admin_menu');
	add_action('add_meta_boxes', 'woocommerce_umf_add_box');
	add_action( 'save_post', 'save_meta_settings' );
	add_action( 'woocommerce_view_order','upload_files_field' );
	add_action('woocommerce_email_after_order_table', 'umf_mail');
	
	// If frontend styling is on, load styles in footer
	if(get_option( 'woocommerce_umf_use_style')=='on') {
		add_action('wp_footer','woo_umf_styles');
	}
		
	/* template overrides for WooCommerce*/
	function umf_path() { return untrailingslashit( plugin_dir_path( __FILE__ ) ); }
		add_filter( 'woocommerce_locate_template', 'umf_template_override', 10, 3 );
	function umf_template_override( $template, $template_name, $template_path ) {
		$_template = $template;
		if ( ! $template_path ) $template_path = $woocommerce->template_url;
		$plugin_path  = umf_path() . '/templates/';
		$template = locate_template(
		array($template_path . $template_name,$template_name));
		if ( ! $template && file_exists( $plugin_path . $template_name ) )
			$template = $plugin_path . $template_name;
		if ( ! $template )
			$template = $_template;
		return $template;
	}
} else {
/* if WooCommerce is not active show admin message */
add_action('admin_notices', 'showAdminMessages');   
}

/**
* Settings link on plugin page 
*/
function umf_plugin_links($links) { 
  $settings_link = '<a href="admin.php?page=woocommerce_umf">Settings</a>'; 
  $premium_link = '<a href="http://wordpress.geev.nl/product/woocommerce-upload-my-file/" title="Buy Pro" target=_blank>Buy Pro</a>'; 
  array_unshift($links, $settings_link,$premium_link); 
  return $links; 
}
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'umf_plugin_links' );


$file   = basename( __FILE__ );
$folder = basename( dirname( __FILE__ ) );
$hook = "in_plugin_update_message-{$folder}/{$file}";
add_action( $hook, 'geev_umf_update_message', 10, 2 ); 

function geev_umf_update_message( $plugin_data, $r )
{
	$readme=file_get_contents('http://plugins.svn.wordpress.org/woocommerce-upload-my-file/tags/'.$r->new_version.'/readme.txt');
	
	$upgrade_notice=explode('== Upgrade Notice ==',$readme);
	$upgrade_notice=explode('== Usage ==',$upgrade_notice[1]);
	$upgrade_notice=explode('|',$upgrade_notice[0]);
	if($upgrade_notice[1]!="") {
		echo '<p>'.trim($upgrade_notice[1]).'</p>';
	}
}
?>