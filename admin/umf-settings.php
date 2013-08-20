<?php /**
* WordPress Settings Page
*/
function woocommerce_umf_page() {
// Check the user capabilities
	if ( !current_user_can( 'manage_woocommerce' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'woocommerce-umf' ) );
	}
// Save the field values
	if ( isset( $_POST['umf_fields_submitted'] ) && $_POST['umf_fields_submitted'] == 'submitted' ) {
		delete_option('woocommerce_umf_use_style');
		foreach ( $_POST as $key => $value ) {
			if ( get_option( $key ) != $value ) {
				update_option( $key, $value );
			} else {
				add_option( $key, $value, '', 'no' );
			}
		}
	}
?>
<?php wp_enqueue_script("jquery-ui-tabs"); ?>
<style>#poststuff h2.nav-tab-wrapper{padding-bottom:0px;}.tab{display:none;}.tab.active{display:block;}table td p {padding:0px !important;}</style>
<script>
jQuery(document).ready(function(){	
 //jQuery( "#tabs" ).tabs();
	var active_tab = window.location.hash.replace('#top#','');
	if ( active_tab == '' )
		active_tab = 'general';
	jQuery('#'+active_tab).addClass('active');
	jQuery('#'+active_tab+'-tab').addClass('nav-tab-active');
	
	jQuery('.nav-tab-wrapper a').click(function() {
		jQuery('.nav-tab-wrapper a').removeClass('nav-tab-active');
		jQuery('.tab').removeClass('active');
	
		var id = jQuery(this).attr('id').replace('-tab','');
		jQuery('#'+id).addClass('active');
		jQuery(this).addClass('nav-tab-active');
	});
});
</script>
<div class="wrap">
	<div id="icon-options-general" class="icon32"></div>
	<h2><?php _e( 'WooCommerce - Upload My File', 'woocommerce-umf' ); ?></h2>
	<?php if ( isset( $_POST['umf_fields_submitted'] ) && $_POST['umf_fields_submitted'] == 'submitted' ) { ?>
	<div id="message" class="updated fade"><p><strong><?php _e( 'Your settings have been saved.', 'woocommerce-umf' ); ?></strong></p></div>
	<?php } ?>
	<div id="content">
		<form method="post" action="" id="umf_settings">
			<input type="hidden" name="umf_fields_submitted" value="submitted">
			<div id="poststuff">
				<div style="float:left; width:72%; padding-right:3%;">
					<div id="tabs">
					<h2 class=nav-tab-wrapper>
					<a id=general-tab class="nav-tab" href="#top#general"><?php _e( 'General', 'woocommerce-umf' ); ?></a>
					</h2>
					<div id="general" class="tab">
					
						<div class="inside umf-settings">
							<table class="form-table">
								<tr><td colspan=2><h2 style="margin:0px;padding:0px;"><?php _e( 'General Settings', 'woocommerce-umf' ); ?></h2></td></tr>
								<tr>
    								<th>
    									<label for="woocommerce_umf_allowed_file_types"><b><?php _e( 'Allowed file types:', 'woocommerce-umf' ); ?></b></label>
    								</th>
    								<td>
    									<input type=text name="woocommerce_umf_allowed_file_types" class="regular-text" value="<?php if(!get_option( 'woocommerce_umf_allowed_file_types' )) { echo 'jpg,png'; } else { echo stripslashes(get_option( 'woocommerce_umf_allowed_file_types' )); }?>"/><br />
    									<span class="description"><?php
    										echo __( 'Specify which file types are allowed for uploading, seperate by commas.', 'woocommerce-umf' );
    									?></span>
    								</td>
    							</tr>
								<tr>
    								<th>
    									<label for="woocommerce_umf_max_uploadsize"><b><?php _e( 'Max. upload size:', 'woocommerce-umf' ); ?></b></label>
    								</th>
    								<td>
    									<input type=text name="woocommerce_umf_max_uploadsize" class="short" value="<?php if(!get_option( 'woocommerce_umf_max_uploadsize' )) { echo ini_get('upload_max_filesize'); } else { echo stripslashes(get_option( 'woocommerce_umf_max_uploadsize' )); }?>"/><br />
    									<span class="description"><?php
    										echo __( 'Specify maximum upload size for all files in MegaBytes. Cannot exceed max. PHP upload size.', 'woocommerce-umf' ).'<br>';
											echo __( 'Note: recommended max. upload size below 8MB.', 'woocommerce-umf' );
    									?></span>
    								</td>
    							</tr>

								<tr>
    								<th>
    									<label for="woocommerce_umf_status"><b><?php _e( 'Required status(es):', 'woocommerce-umf' ); ?></b></label>
    								</th>
    								<td>
										
										<?php $statusname=get_option( 'woocommerce_umf_status' );
										$statuses = get_terms( 'shop_order_status', array( 'hide_empty' => false ) );
		$values = array();
		$i=0;
		foreach( $statuses as $status ) {
			$values[ $status->slug ] = $status->name;
			?>
			<input type=checkbox name="woocommerce_umf_status[<?php echo $i;?>]"  value="<?php echo $status->name;?>" <?php if(isset($statusname[$i]) && $statusname[$i]==$status->name) { echo 'checked';}?>> <?php _e($status->name,'woocommerce');?><br>
			<?php $i++;
		} ?>

    									<span class="description"><?php
    										echo __( 'Specify which order statuses will allow customers to upload files.', 'woocommerce-umf' );
    									?></span>
    								</td>
    							</tr>
																<tr>
    								<th>
    									<b><?php _e( 'Styling', 'woocommerce-umf' );?></b>
    								</th>
    								<td>
										<input id=woocommerce_umf_use_style type=checkbox <?php if(get_option( 'woocommerce_umf_use_style')=='on') { echo 'checked';}?> name="woocommerce_umf_use_style"> <label for="woocommerce_umf_use_style"><?php _e( 'Enable WooCommerce Upload My File CSS:', 'woocommerce-umf' ); ?></label><br>
    									<span class="description">
											<?php _e( 'We\'ve made some default styling for the frontend. Do you want to use it?', 'woocommerce-umf' );?>
										</span>
    								</td>
    							</tr>
								<tr><td colspan=2><h2 style="margin:0px;padding:0px;color:#999;"><?php _e( 'PRO Settings', 'woocommerce-umf' ); ?></h2></td></tr>
								<tr style="color:#999;">
    								<th>
    									<label for="woocommerce_umf_default_enable"><b style="color:#999;"><?php _e( 'Default enable upload:', 'woocommerce-umf' ); ?> </b></label>
    								</th>
    								<td>
    									<input type="radio" name="" disabled value="0"> <?php _e( 'No' );?>&nbsp;
										<input type="radio" name="" disabled value="1" checked> <?php _e( 'Yes' );?>
    									<br><span class="description"><?php
    										echo __( 'Default enable file upload for all products', 'woocommerce-umf' );
    									?></span>
    								</td>
    							</tr>
								<tr style="color:#999;">
    								<th>
    									<label for="woocommerce_umf_default_upload_limit"><b style="color:#999;"><?php _e( 'Default upload limit:', 'woocommerce-umf' ); ?> </b></label>
    								</th>
    								<td>
    									<input type="text" name="" value="1"  disabled /><br />
    									<span class="description"><?php
    										echo __( 'Specify the default upload limit.', 'woocommerce-umf' );
    									?></span>
    								</td>
    							</tr>
								
    							<tr style="color:#999;">
    								<th>
    									<label for="woocommerce_umf_upload_path"><b style="color:#999;"><?php _e( 'Upload path:', 'woocommerce-umf' ); ?> </b></label>
    								</th>
    								<td>
									<?php $upload_dir=wp_upload_dir();?>
    									<input type=text name="" disabled class="regular-text" value=""/><br />
    									<span class="description"><?php
    										echo __( 'Upload path for new uploads. Subfolders with the order-ID will be created within this directory.', 'woocommerce-umf' );
    									?></span>
    								</td>
    							</tr>
								<tr style="color:#999;">
    								<th>
    									<label for="woocommerce_umf_whitelist"><b style="color:#999;"><?php _e( 'Whitelist or Blacklist files:', 'woocommerce-umf' ); ?> </b></label>
    								</th>
    								<td>
										<select name="" disabled class="select">
											<option value="whitelist" selected><?php echo _e( 'whitelist', 'woocommerce-umf' );?></option>
											<option value="blacklist"><?php echo _e( 'blacklist', 'woocommerce-umf' );?></option>
										</select><br>
    									<span class="description"><?php
    										echo __( 'Specify if you want to whitelist or blacklist file types.', 'woocommerce-umf' );
    									?></span>
    								</td>
    							</tr>
								
								<tr>
									<td colspan=2>
										<p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php _e( 'Save Changes', 'woocommerce-umf' ); ?>" /></p>
									</td>
								</tr>
							</table>
						</div>
					</div>
					</div>
				</div>
				<div style="float:right; width:25%;">
					<div class="postbox">
						<h3><?php _e( 'Buy Pro!', 'woocommerce-umf' ); ?></h3>
						<div class="inside umf-preview">
							<p><?php echo __( 'Check out our ', 'woocommerce-umf' ); ?> <a href="http://wordpress.geev.nl/product/woocommerce-upload-my-file/">website</a> <?php _e('to find out more about WooCommerce Upload My File Pro.', 'woocommerce-umf' );?></p>
							<p><?php _e('For only &euro; 29,00 you will get a lot of features and access to our support section.', 'woocommerce-umf' );?></p>
							<p><?php _e('A couple of features:', 'woocommerce-umf' );?>
							<ul style="list-style:square;padding-left:20px;margin-top:-10px;"><li><strong><?php _e('New', 'woocommerce-umf' );?></strong>: <?php _e('Specify upload titles per product.', 'woocommerce-umf' );?></li><li><strong><?php _e('New', 'woocommerce-umf' );?></strong>: <?php _e('File preview', 'woocommerce-umf' );?></li><li><strong><?php _e('New', 'woocommerce-umf' );?></strong>: <?php _e('Preview thumbnails of uploaded files', 'woocommerce-umf' );?></li><li><?php _e('Allow more than one upload per product', 'woocommerce-umf' );?></li><li><?php _e('White or blacklist file types', 'woocommerce-umf' );?></li><li><?php _e('Let users delete files', 'woocommerce-umf' );?></li><li><?php _e('Default enable file upload for products', 'woocommerce-umf' );?></li><li><?php _e('Define your own upload path', 'woocommerce-umf' );?></li></ul>
						</div>
					</div>
					<div class="postbox">
						<h3><?php _e( 'Show Your Love', 'woocommerce-umf' ); ?></h3>
						<div class="inside umf-preview">
							<p><?php echo sprintf(__( 'This plugin is developed by %s, a Dutch graphic design and webdevelopment company.', 'woocommerce-umf' ),'Geev vormgeeving'); ?></p>
							<p><?php _e( 'If you are happy with this plugin please show your love by liking us on Facebook', 'woocommerce-umf' ); ?></p>
							<iframe src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fgeevvormgeeving&amp;width=220&amp;height=62&amp;show_faces=false&amp;colorscheme=light&amp;stream=false&amp;border_color&amp;header=false" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100%; height:62px;" allowTransparency="true"></iframe>
							<p><?php _e( 'Or', 'woocommerce-umf' ); ?></p>
							<ul style="list-style:square;padding-left:20px;margin-top:-10px;">
								<li><a href="http://wordpress.org/extend/plugins/woocommerce-upload-my-file/" target=_blank title="Woocommerce Upload My File"><?php _e( 'Rate the plugin 5&#9733; on WordPress.org', 'woocommerce-umf' ); ?></a></li>
								<li><a href="http://wordpress.geev.nl/product/woocommerce-upload-my-file/" target=_blank title="Woocommerce Upload My File"><?php _e( 'Blog about it & link to the plugin page', 'woocommerce-umf' ); ?></a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<?php }