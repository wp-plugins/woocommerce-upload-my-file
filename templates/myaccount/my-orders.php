<?php
/**
 * My Orders
 *
 * Shows recent orders on the account page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

if ( version_compare( WOOCOMMERCE_VERSION, "2.2" ) < 0 ) {

    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => 'shop_order',
        'post_status' => 'publish'
    ) );

} else {

    $customer_orders = get_posts( apply_filters( 'woocommerce_my_account_my_orders_query', array(
    	'numberposts' => -1,
    	'meta_key'    => '_customer_user',
    	'meta_value'  => get_current_user_id(),
    	'post_type'   => wc_get_order_types( 'view-orders' ),
    	'post_status' => array_keys( wc_get_order_statuses() )
    ) ) );

}


if ( $customer_orders ) : ?>

	<h2><?php echo apply_filters( 'woocommerce_my_account_my_orders_title', __( 'Recent Orders', 'woocommerce' ) ); ?></h2>

	<table class="shop_table my_account_orders">

		<thead>
			<tr>
				<th class="order-number"><span class="nobr"><?php _e( 'Order', 'woocommerce' ); ?></span></th>
				<th class="order-date"><span class="nobr"><?php _e( 'Date', 'woocommerce' ); ?></span></th>
				<th class="order-total"><span class="nobr"><?php _e( 'Total', 'woocommerce' ); ?></span></th>
				<th class="order-files"><span class="nobr"><?php _e('Files', 'woocommerce-umf'); ?></span></th>
				<!--<th class="order-actions">&nbsp;</th>-->
				<th class="order-status" colspan=2><span class="nobr"><?php _e( 'Status', 'woocommerce' ); ?></span></th>
			</tr>
		</thead>

		<tbody><?php
			foreach ( $customer_orders as $customer_order ) {

                if ( version_compare( WOOCOMMERCE_VERSION, "2.2" ) < 0 ) {
				    $order = new WC_Order($customer_order->ID);
                } else {
                    $order = wc_get_order($customer_order->ID);
                }

				$status     = get_term_by( 'slug', $order->status, 'shop_order_status' );
				$item_count = $order->get_item_count();

				?><tr class="order">
					<td class="order-number">
					<?php if ( version_compare( WOOCOMMERCE_VERSION, "2.0.99" ) <= 0 ) {
						$viewOrderUrl=esc_url( add_query_arg('order', $order->id, get_permalink( woocommerce_get_page_id( 'view_order' ) ) ) );
					} else {
						$viewOrderUrl=$order->get_view_order_url();
					} ?>
						<a href="<?php echo $viewOrderUrl; ?>">
							<?php echo $order->get_order_number(); ?>
						</a>
					</td>


					<td class="order-date">
						<time title="<?php echo esc_attr( strtotime( $order->order_date ) ); ?>"><?php echo date_i18n( 'd-m-Y', strtotime( $order->order_date ) ); ?></time>
					</td>

					<?php if(in_array($order->status, array('on-hold','pending', 'failed','cancelled'))) { ?>
					<td class="order-total unpaid"><a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="pay"><?php echo $order->get_formatted_order_total(); ?></a></td>
					<?php } else {?>
					<td class="order-total paid" ><?php echo $order->get_formatted_order_total(); ?></td>
					<?php } ?>
					<td class="order-files <?php echo check_for_files($order->id);?>" width="10%">

					<?php if(check_for_files($order->id)=='upload') {
							echo '<a href="'.$viewOrderUrl.'">'.__( 'Upload', 'woocommerce-umf' ).'</a>';
						} elseif(check_for_files($order->id)=='blank') { echo '-'; }
					?>
					</td><td class="order-status" style="text-align:left; white-space:nowrap;">
                        <?php
                        if ( version_compare( WOOCOMMERCE_VERSION, "2.2" ) < 0 ) {
						    echo ucfirst( __( $status->name, 'woocommerce' ) );
                        } else {
                            echo wc_get_order_status_name( $order->get_status() );
                        }
                        ?>
					</td>
					<td class="order-actions" style="text-align:right; white-space:nowrap;">


						<?php
							$actions = array();

							if(check_for_files($order->id)=='upload') {

								$actions['upload'] = array(
									'url'  => $viewOrderUrl,
									'name' => __( 'Upload files', 'woocommerce-umf' )
								);
							}

							if ( in_array( $order->status, apply_filters( 'woocommerce_valid_order_statuses_for_payment', array( 'pending', 'failed' ), $order ) ) )
								$actions['pay'] = array(
									'url'  => $order->get_checkout_payment_url(),
									'name' => __( 'Pay', 'woocommerce' )
								);

							if ( in_array( $order->status, apply_filters( 'woocommerce_valid_order_statuses_for_cancel', array( 'pending', 'failed' ), $order ) ) )
								$actions['cancel'] = array(
									'url'  => $order->get_cancel_order_url(),
									'name' => __( 'Cancel', 'woocommerce' )
								);

							$actions['view'] = array(
								'url'  => $viewOrderUrl,
								'name' => __( 'View', 'woocommerce' )
							);

							$actions = apply_filters( 'woocommerce_my_account_my_orders_actions', $actions, $order );

							foreach( $actions as $key => $action ) {
								echo '<a href="' . esc_url( $action['url'] ) . '" class="umf-btn icon-' . sanitize_html_class( $key ) . ' ' . sanitize_html_class( $key ) . '"><span>' . esc_html( $action['name'] ) . '</span></a>';
							}
						?>
					</td>

				</tr><?php
			}
		?></tbody>

	</table>

<?php endif; ?>