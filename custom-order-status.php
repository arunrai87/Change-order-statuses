<?php
/*
Plugin Name: Change order statuses
Plugin URI: http://devaprai.blogspot.in/
Description: Gives WooCommerce store administrators the ability to Change custom order statuses.
Version: 1.0
Author: Arunendra Pratap Rai
Author URI: http://devaprai.blogspot.in/
*/
add_action( 'admin_menu', 'CustomOrderStatus' );
function CustomOrderStatus(){
// add_menu_page( 'custom order Status', 'Change Order Status', 'manage_options', 'custompage', 'CustomOrderStatusHtml', plugins_url( 'myplugin/images/icon.png' ),99); 
add_submenu_page( 'woocommerce', 'Change Order Status', 'Change Order Status', 'manage_options', 'changer-oder-status', 'CustomOrderStatusHtml' );
}
 
function CustomOrderStatusHtml(){
global $woocommerce;
$getStatuses = unserialize(get_option( 'payment_gateway_array' ));
$statuses = array('pending','failed','on-hold','processing','completed','refunded','cancelled');
?>	
<form name="orderform" action="" method="post">
<tr valign="top">
	<td class="forminp" colspan="2">
		<table class="widefat" cellspacing="2">
			<thead>
				<tr>
					<th>Payment Gateway</th>
						<th>Select status</th>
				</tr>
			</thead>
				<tbody>
					<?php		 $i = 0;	
						foreach ( $woocommerce->payment_gateways->payment_gateways() as $gateway ) { ?>
					<tr>	
						<td><?php echo $gateway->get_title(); ?>
						<input type="hidden" name="paymethod[]" value="<?php echo $gateway->id;?>"/>
						</td>
							<td>							
								<select id='order-StatusSelect' name="order_select[]">
									<option value='-1'>Please Select A Order Status</option>
										<?php
										foreach($statuses as $status) { ?>
										<option <?php echo ($status->slug == $getStatuses[$i]['order_select']) ? ' selected="selected" ' : ''; ?> value='<?php echo  $status->slug;?>'><?php echo $status->slug;?></option>
										<?php } ?>
								</select>
							</td>
			
					</tr>
			<?php $i++; } ?>
				</tbody>
		</table>
	</td>
</tr>
<input class="button-primary" type="submit" value="save changes" name="submitform"/>
</form>
<?php }	
if(isset($_REQUEST['submitform']))
{
global $wpdb;
$paymethod = $_REQUEST['paymethod'];
$order_select = $_REQUEST['order_select'];
$statusTable = array();
$i = 0;
foreach($paymethod as $_paymethod)
{
	$statusTable[$i]['paymentmethod'] = $_paymethod;
	$statusTable[$i]['order_select'] = $order_select[$i];
	$i++;
}
update_option( 'payment_gateway_array', serialize($statusTable));
}
add_action( 'woocommerce_thankyou', 'updateOrderStatus' );
function updateOrderStatus($order_id) {
    global $woocommerce;
	$getStatuses = unserialize(get_option( 'payment_gateway_array' ));
     if (!$order_id )
        return;
	$billing_paymethod = get_post_meta($order_id,'_payment_method',true);
	$order = new WC_Order( $order_id );	
foreach($getStatuses as $options):	
	if($billing_paymethod == $options['paymentmethod'])
	{
       $order->update_status( $options['order_select'] );
	}
endforeach;	
}
?>
