<?php
/*
Plugin Name: WooCommerce Upload My File
Plugin URI: http://wordpress.geev.nl/product/woocommerce-upload-my-file/
Description: This plugin provides the possibility to upload files in WooCommerce after ordering. - Free Version
Version: 0.1
Author: Geev vormgeeving
Author URI: http://www.geev.nl/
*/
/*  Copyright 2012  Geev  (email : info@geev.nl)

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
?>