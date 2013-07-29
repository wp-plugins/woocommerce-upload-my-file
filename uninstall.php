<?php 
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();

delete_option('umf_fields_submitted');
delete_option('woocommerce_umf_allowed_file_types');
delete_option('woocommerce_umf_max_uploadsize');
delete_option('woocommerce_umf_status');
delete_option('woocommerce_umf_use_style');