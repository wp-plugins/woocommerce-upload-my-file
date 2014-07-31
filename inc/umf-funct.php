<?php

if ( ! class_exists( 'WOO_Check' ) )
	require_once 'class-check-woocommerce.php';

/**
 * WC Detection
 */
if ( ! function_exists( 'is_woocommerce_active' ) ) {
	function is_woocommerce_active() {
		return WOO_Check::woocommerce_active_check();
	}
}

/*admin messages*/
if(!function_exists('showMessage')) {
function showMessage($message, $errormsg = false)
{
	if ($errormsg) { echo '<div id="message" class="error">';}
	else {echo '<div id="message" class="updated fade">';}
	echo "<p>$message</p></div>";
}
}
function showAdminMessages() {showMessage(__( 'WooCommerce is not active. Please activate plugin before using WooCommerce Upload My File plugin.', 'woocommerce_umf'), true);}

/* WordPress Administration Menu
Toont een nieuw menu item als submenu van WooCommerce
*/
function woocommerce_umf_admin_menu() {
	$page = add_submenu_page('woocommerce', __( 'Upload My File', 'woocommerce-umf' ), __( 'Upload My File', 'woocommerce-umf' ), 'manage_woocommerce', 'woocommerce_umf', 'woocommerce_umf_page' );
}

/* Add meta boxes to pages
Toont een nieuwe box op de product pagina en op de bestel pagina.
*/
function woocommerce_umf_add_box() {
	add_meta_box( 'woocommerce-umf-box-product', __( 'Upload Files', 'woocommerce-umf' ), 'woocommerce_umf_box_product', 'product', 'side', 'default' );
	add_meta_box( 'woocommerce-umf-box-order-detail', __( 'Uploaded Files', 'woocommerce-umf' ), 'woocommerce_umf_box_order_detail', 'shop_order', 'side', 'default' );
}

function is_localhost() {
    $whitelist = array( '127.0.0.1', '::1' );
    if( in_array( $_SERVER['REMOTE_ADDR'], $whitelist) )
        return true;
}

/* Inhoud van de box op de order-detail pagina*/
function woocommerce_umf_box_order_detail($post) {
	$order=new WC_Order($post->ID);

	$j=1;
	/* per product een formulier met gegevens */
	foreach ( $order->get_items() as $order_item ) {
		$max_upload_count=0;
		$max_upload_count=get_max_upload_count($order,$order_item['product_id']);
		if($max_upload_count!=0){
		$item_meta = new WC_Order_Item_Meta( $order_item['item_meta'] );
		$forproduct=$order_item['name'].' ('.$item_meta->display($flat=true,$return=true).')';
		echo '<strong>';
		printf( __('File for product: %s:', 'woocommerce-umf'), $forproduct);
		echo '</strong><br>';

		/* Controle of er al een bestand is geupload */
		$i=1;
		$upload_count=0;
		echo '<ul>';
		while ($i <= $max_upload_count) {
			echo '<li>';
			$name = get_post_meta( $post->ID, '_woo_umf_uploaded_file_name_' . $j, true );

            if (is_localhost()) {

                $url = get_post_meta( $post->ID, '_woo_umf_uploaded_file_path_' . $j, true );

            } else {

                $url = home_url( str_replace( ABSPATH, '', get_post_meta( $post->ID, '_woo_umf_uploaded_file_path_' . $j, true ) ) );

            }
			$forproduct = get_post_meta( $post->ID, '_woo_umf_uploaded_product_name_' . $j, true );
			/* geen bestand geupload, dus toon upload velden */
			if( !empty( $url ) && !empty( $name ) ) {
				printf( '<a href="%s" target="_blank">%s</a>', $url, $name );
				$upload_count++;
			} else {
				echo '<span style="color:red;">';
				printf( __('File #%s has not been uploaded.', 'woocommerce-umf'), $i );
				echo '</span>';
			}
		$i++;
		$j++;
			echo '</li>';
		}
		echo '</ul>';
		/* toon aantal nog aan te leveren bestanden */
		$upload_count=$max_upload_count-$upload_count;
		echo '<p>';
		printf( __('Files to be uploaded for this item: %s', 'woocommerce-umf'), $upload_count );
		echo '</p>';
		}
	}
}

/* Inhoud van de box op de product bewerk pagina*/
function woocommerce_umf_box_product($post) {
	wp_nonce_field( 'woo_umf_nonce', 'woo_umf_nonce' );
	echo '<p>';
	echo '<label for="_woo_umf_enable">' . __('Enable', 'woocommerce-umf' ) . ': </label> ';
	echo '<input type="hidden" name="_woo_umf_enable" value="0" />';
	$myarray=get_post_meta( $post->ID, '_woo_umf_enable');
		$checked=checked( get_post_meta( $post->ID, '_woo_umf_enable', true ), 1, false );
	echo '<input type="checkbox" id="_woo_umf_enable" class="checkbox" name="_woo_umf_enable" value="1" ' . $checked . ' />';
	echo '</p>';
}

/* Instellingen bewaren*/
function save_meta_settings( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( !isset( $_POST['woo_umf_nonce'] ) || !wp_verify_nonce( $_POST['woo_umf_nonce'], 'woo_umf_nonce' ) ) return;
	update_post_meta( $post_id, '_woo_umf_enable', (int) $_POST['_woo_umf_enable'] );
}

/* functie om de producten te tonen*/
function woocommerce_umf_get_product( $product_id, $args = array() ) {
  $product = null;
  if ( version_compare( WOOCOMMERCE_VERSION, "2.0.0" ) >= 0 ) {
    // WC 2.0
    $product = get_product( $product_id, $args );
  } else {
    // old style, get the product or product variation object
    if ( isset( $args['parent_id'] ) && $args['parent_id'] ) {
      $product = new WC_Product_Variation( $product_id, $args['parent_id'] );
    } else {
      // get the regular product, but if it has a parent, return the product variation object
      $product = new WC_Product( $product_id );
      if ( $product->get_parent() ) {
        $product = new WC_Product_Variation( $product->id, $product->get_parent() );
      }
    }
  }
  return $product;
}

/* functie om de product eigenschappen te tonen*/
if (!function_exists('woocommerce_umf_get_product_meta')) {
function woocommerce_umf_get_product_meta( $product, $field_name ) {

  if ( version_compare( WOOCOMMERCE_VERSION, "2.0.0" ) >= 0 ) {
    // even in WC >= 2.0 product variations still use the product_custom_fields array apparently
    if ( $product->variation_id && isset( $product->product_custom_fields[ '_' . $field_name ][0] ) && $product->product_custom_fields[ '_' . $field_name ][0] !== '' ) {
      return $product->product_custom_fields[ '_' . $field_name ][0];
    }
    // use magic __get
    return maybe_unserialize( $product->$field_name );
  } else {
    // use product custom fields array

    // variation support: return the value if it's defined at the variation level
    if ( isset( $product->variation_id ) && $product->variation_id ) {

      if ( ( $value = get_post_meta( $product->variation_id, '_' . $field_name, true ) ) !== '' ) return $value;
      // otherwise return the value from the parent
      return get_post_meta( $product->id, '_' . $field_name, true );
    }

    // regular product
    return isset( $product->product_custom_fields[ '_' . $field_name ][0] ) ? $product->product_custom_fields[ '_' . $field_name ][0] : null;
  }
}
}

function get_max_upload_count($order,$order_item=0) {
	$max_upload_count=0;
	//product specifiek
	if( (( is_array( get_option( 'woocommerce_umf_status' ) ) && in_array( $order->status, get_option( 'woocommerce_umf_status' ) ) ) ) || $order->status == get_option( 'woocommerce_umf_status' ) ) {
		if($order_item!=0) {
			$product = woocommerce_umf_get_product($order_item);
			if( woocommerce_umf_get_product_meta($product,'woo_umf_enable') == 1) {
				$max_upload_count=1;
			}
		} else {
		// order totaal
		foreach ( $order->get_items() as $order_item ) {
			$product = woocommerce_umf_get_product($order_item['product_id']);
			$limit=1;
			if( woocommerce_umf_get_product_meta($product,'woo_umf_enable') == 1 && $limit > 0 ) {
				$max_upload_count+=$limit;
			}
		}
		}
	}
	return $max_upload_count;
}

/* functie controleert of bestanden zijn geupload of niet */
function check_for_files( $order_id ) {
	$upload=false;
	$order = new WC_Order( $order_id );
	if( (( is_array( get_option( 'woocommerce_umf_status' ) ) && in_array( $order->status, get_option( 'woocommerce_umf_status' ) ) ) ) || $order->status == get_option( 'woocommerce_umf_status' ) ) {
	if(get_max_upload_count( $order ) >0 ) {
		for ($i = 1; $i <= get_max_upload_count($order); $i++) {
			$name = get_post_meta( $order_id, '_woo_umf_uploaded_file_name_' . $i, true );
			if( empty( $name ) ) {$upload = true;}
		}
		if($upload==true) {return 'upload';} else { return 'done'; }
	}
	} else { return 'blank';}
}


/* show upload fields on order detail page
Deze functie toont de upload velden op de order-detail pagina van de klant
*/
function upload_files_field( $order_id ) {
	$order = new WC_Order( $order_id );

	/* upload handler */
	if( isset( $_FILES ) && isset($_POST['upload']) && $_POST['upload']) {

		$upload_dir=wp_upload_dir();
		$path = trailingslashit( trailingslashit( $upload_dir['basedir'].'/umf/' ) . $order_id );

		foreach( $_FILES as $key => $umf_file ) {
			if( empty( $umf_file['name'] ) ) continue;
			wp_mkdir_p( $path );
			$umf_filepath = $path . $order_id.'_'.$key.'_'.$umf_file['name'];
			$ext = strtolower( pathinfo( $umf_filepath, PATHINFO_EXTENSION ) );
			$doctypes = explode( ',', get_option( 'woocommerce_umf_allowed_file_types' ) );
			foreach($doctypes as $k => $v) { $doctypes[$k] = strtolower( trim( $v ) ); }
				if( in_array( $ext, $doctypes ) ) $typeallow = true;
					else $typeallow = false;
			if($typeallow==false) {
				$upload_error.= '<li>' . sprintf( __( 'The %s file type is not allowed.', 'woocommerce-umf'), $ext ) . '</li>';
			}
			$filesize=(int)get_option( 'woocommerce_umf_max_uploadsize' );
			$max_upload = (int)(ini_get('upload_max_filesize'));
			$max_post = (int)(ini_get('post_max_size'));
			$memory_limit = (int)(ini_get('memory_limit'));
			$max_mb = min($max_upload, $max_post, $memory_limit);
			if($filesize > $max_mb) { $filesize=$max_mb;}
			if($_FILES[$key]["size"]< ($filesize*1024*1024))
				{$sizeallow=true; } else {$sizeallow=false; $upload_error.= '<li">' . sprintf( __( 'The file "%s" is to big.', 'woocommerce-umf'), $umf_file['name'] ) . '</li>';}

			if( $typeallow == true && $sizeallow == true ) {
				if( copy( $umf_file['tmp_name'], $umf_filepath ) ) {
					$success=true;
					update_post_meta( $order_id, '_woo_umf_uploaded_file_name_' . $key, $umf_file['name'] );
					update_post_meta( $order_id, '_woo_umf_uploaded_file_path_' . $key, $umf_filepath );
					update_post_meta( $order_id, '_woo_umf_uploaded_product_name_' . $key, $_POST['uploaded_product_name'][$key] );
				} else {
					$upload_error.= '<li>' . __( 'There was an error while uploading your file(s).', 'woocommerce-umf') . '</li>';
				}
			}
		if($success==true && isset($upload_error) && $upload_error!="") {
		  echo '<p class="woocommerce-info woo-umf-success updated">' . __( 'There was a problem while uploading your files.', 'woocommerce-umf') . '<br>' . __( 'Not all files have been successfully uploaded.', 'woocommerce-umf') . '</p>';
		}
		if($success==true && isset($upload_error) && $upload_error=="") {
		  echo '<p class="woocommerce-message woo-umf-success success">' . __( 'Your file(s) were uploaded successfully.', 'woocommerce-umf') . '</p>';
		}
		if(isset($upload_error) && $upload_error!="") {
		  echo '<div class="woocommerce-error woo-umf-error error"><ul>';
		  echo $upload_error;
		  echo '</ul></div>';
		}
		}
	}

	/* wanneer er meer dan 1 bestand geupload kan worden, ga verder */
	if(get_max_upload_count($order) > 0) {
	echo '<h2>' . __( 'Upload files', 'woocommerce-umf' ) . '</h2>';

	/*begin form*/
	echo '<form enctype="multipart/form-data" action="" method="POST" class=woo-umf-form>';

		$j=1;
		/* per product een formulier met gegevens */
		foreach ( $order->get_items() as $order_item ) {
			$max_upload_count=0;
			$max_upload_count=get_max_upload_count($order,$order_item['product_id']);
			$item_meta = new WC_Order_Item_Meta( $order_item['item_meta'] );
			if($max_upload_count!=0){
			    // start fieldset per product item
		        echo '<fieldset class="woo-umf-product">';
				if($item_meta->display($flat=true,$return=true)!="") {$variation='<span class=woo-umf-variations>'.$item_meta->display($flat=true,$return=true).'</span>';} else {$variation='';}
  		        echo '<legend>'.$order_item['name'].' '.$variation.'</span></legend>';
			/* Controle of er al een bestand is geupload */
			$i=1;
			$upload_count=0;
			while ($i <= $max_upload_count) {
				$name = get_post_meta( $order_id, '_woo_umf_uploaded_file_name_' . $j, true );
				/* geen bestand geupload, dus toon upload velden */
				if($name=="") {
					echo '<div class=woo-umf-item-box><input type="file" name="'.$j.'" /> <span class="umf-btn umf-icon-info"><em>'.woo_um_get_allowed_filetypes($order_item['product_id']).'</em></span></div>';
					echo '<input type="hidden" value="'.strip_tags($order_item['name'].' - '.$item_meta->display($flat=true,$return=true)).'" name="uploaded_product_name['.$j.']">';
					$upload=true;
				} else {
				/* alle bestanden zijn geupload, toon bestandsnamen */
					echo '<p>'.$name.'</p>';
					$upload_count++;
				}
			$i++;
			$j++;
			}
			echo '</fieldset>';
			}
		}

	/* knoppen */
		if( isset($upload) ) {	echo '<input type="submit" name=upload class="button uploadfiles" value="' . __( 'Upload', 'woocommerce-umf' ) . '" />';}

	/* upload info */
		echo '<legend>';
		$max_upload = (int)(ini_get('upload_max_filesize'));
		$max_post = (int)(ini_get('post_max_size'));
		$memory_limit = (int)(ini_get('memory_limit'));
		$max_mb = min($max_upload, $max_post, $memory_limit);

		if(get_option( 'woocommerce_umf_max_uploadsize' )) {$upload_mb=(int)(get_option( 'woocommerce_umf_max_uploadsize' ));} else { $upload_mb = $max_mb;	}
		if($upload_mb > $max_mb) { $upload_mb = $max_mb;}

		echo '<p>' . sprintf( __( 'Max upload size per file: %s', 'woocommerce-umf' ), $upload_mb ) . 'MB. ';
		echo sprintf( __( 'Max total upload size: %s', 'woocommerce-umf' ), $max_mb ) . 'MB</p>';
		echo '</legend>';

	echo '</form>';
	/* eind form*/
	}
}
/**
* woo_umf_styles
* Add basic frontend styling if is choosen in admin
* Since 0.2
*/
function woo_umf_styles() {
	wp_enqueue_style('woo-umf-style', plugins_url('css/woo-umf.css',dirname(__FILE__)));
}
/**
* Get allowed or disallowed filetypes and corresponding language strings
* @since 0.2
*/
function woo_um_get_allowed_filetypes($product_id) {
	if(get_post_meta($product_id, '_woo_umf_filetypes',true )=="") {
	  $filetypes = get_option( 'woocommerce_umf_allowed_file_types' );
	} else {
	  $filetypes = get_post_meta( $product_id, '_woo_umf_filetypes', true );
	}
	return __('Allowed filetypes:','woocommerce-umf').' '.$filetypes;
}

/**
 * attach email link to emails, this removes the email templates inside /woocommerce-upload-my-file/templates/
 * @since 0.1
 */
function umf_mail($order) {
	$count=get_max_upload_count($order);
	if($count>0) {
		if(check_for_files($order->id)=='upload') {
			echo '<h2>'._n('Upload file','Upload files', $count, 'woocommerce-umf' ).'</h2>';

			if ( version_compare( WOOCOMMERCE_VERSION, "2.1" ) <= 0 ) {
				echo '<p><a href="'. esc_url( add_query_arg('order', $order->id,get_permalink(woocommerce_get_page_id('view_order'))) ).'" ><span>'._n( 'Login to upload your file and attach it to your order.', 'Login to upload your files and attach them to your order.', $count, 'woocommerce-umf' ).'</span></a>';
			} else {
				echo '<p><a href="'. esc_url( $order->get_view_order_url() ).'" ><span>'._n( 'Login to upload your file and attach it to your order.', 'Login to upload your files and attach them to your order.', $count, 'woocommerce-umf' ).'</span></a>';
			}
		}
	}
}
?>