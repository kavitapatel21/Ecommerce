<?php

if (!defined('ABSPATH')) exit;  

//Check whether WPML is active
$wpml_active = function_exists('icl_object_id');
$wpml_regstr = function_exists('icl_register_string');
$wpml_trnslt = function_exists('icl_translate');

//Obtain the settings
$waapico_settings = get_option('waapico_settings');
global $waapico_logger;

function waapico_field($var)
{
    global $waapico_settings;
    return isset($waapico_settings[$var]) ? $waapico_settings[$var] : '';
}

//Utility function for registering string to WPML
function waapico_register_string($str)
{
    global $waapico_settings, $wpml_active, $wpml_regstr, $waapico_plugin_domn;
    if ($wpml_active) {
        ($wpml_regstr) ?
            icl_register_string($waapico_plugin_domn, $str, $waapico_settings[$str]) :
            do_action('wpml_register_single_string', $waapico_plugin_domn, $str, $waapico_settings[$str]);
    }
}

//Utility function to fetch string from WPML
function waapico_fetch_string($str)
{
    global $waapico_settings, $wpml_active, $wpml_trnslt, $waapico_plugin_domn;
    if ($wpml_active) {
        return ($wpml_trnslt) ?
            icl_translate($waapico_plugin_domn, $str, $waapico_settings[$str]) :
            apply_filters('wpml_translate_single_string', $waapico_settings[$str], $waapico_plugin_domn, $str);
    }
    return waapico_field($str);
}

//Add phone field to Shipping Address
add_filter('woocommerce_checkout_fields', 'waapico_add_shipping_phone_field');
function waapico_add_shipping_phone_field($fields)
{
    if (!isset($fields['shipping']['shipping_phone'])) {
        $fields['shipping']['shipping_phone'] = array(
            'label' => __('Mobile Phone', 'woocommerce'),
            'placeholder' => _x('Mobile Phone', 'placeholder', 'woocommerce'),
            'required' => false,
            'class' => array('form-row-wide'),
            'clear' => true
        );
    }
    return $fields;
}

//Display shipping phone field on order edit page
add_action('woocommerce_admin_order_data_after_shipping_address', 'waapico_display_shipping_phone_field', 10, 1);
function waapico_display_shipping_phone_field($order)
{
    echo '<p><strong>' . __('Shipping Phone') . ':</strong> ' . get_post_meta($order->get_id(), '_shipping_phone', true) . '</p>';
}

//Change label of billing phone field
add_filter('woocommerce_checkout_fields', 'waapico_phone_field_label');
function waapico_phone_field_label($fields)
{
    $fields['billing']['billing_phone']['label'] = 'Mobile Phone';
    return $fields;
}

//Initialize the plugin
add_action('init', 'waapico_initialize');
function waapico_initialize()
{
    waapico_register_string('msg_new_order');
    waapico_register_string('msg_pending');
    waapico_register_string('msg_on_hold');
    waapico_register_string('msg_processing');
    waapico_register_string('msg_completed');
    waapico_register_string('msg_cancelled');
    waapico_register_string('msg_refunded');
    waapico_register_string('msg_failure');
    waapico_register_string('msg_custom');
}

//Add settings page to woocommerce admin menu 
add_action('admin_menu', 'waapico_admin_menu', 20);
function waapico_admin_menu()
{
    global $waapico_plugin_domn;
    add_submenu_page('woocommerce', __('WooCommerce Whatsapp Notification Settings', $waapico_plugin_domn), __('WooCommerce Whatsapp Notifications', $waapico_plugin_domn), 'manage_woocommerce', $waapico_plugin_domn, $waapico_plugin_domn . '_tab');
    function waapico_tab()
    {
        include('settings-page.php');
    }
}

//Add screen id for enqueuing WooCommerce scripts
add_filter('woocommerce_screen_ids', 'waapico_screen_id');
function waapico_screen_id($screen)
{
    global $waapico_plugin_domn;
    $screen[] = 'woocommerce_page_' . $waapico_plugin_domn;
    return $screen;
}

//Set the options
add_action('admin_init', 'waapico_regiser_settings');
function waapico_regiser_settings()
{
    register_setting('waapico_settings_group', 'waapico_settings');
}

//Schedule notifications for new order
if (waapico_field('use_msg_new_order') == 1)
    add_action('woocommerce_new_order', 'waapico_owner_notification', 20);
    //add_action('woocommerce_thankyou', 'waapico_owner_notification', 20);
function waapico_owner_notification($order_id)
{
	
	$order = wc_get_order( $order_id );

$order_id  = $order->get_id(); // Get the order ID


$order_status  = $order->get_status(); // Get the order status 
	
	waapico_log_message( 'DG Order Status  #131 ' . $order_status );
	if($order_status == 'pending'){
		if (waapico_field('use_msg_pending') == 1){
			$pphone = $order->get_billing_phone();
			$template = apply_filters('waapico_new_order_template', waapico_fetch_string('msg_pending'), $order_id);
			$message = empty($template) ? false : waapico_process_variables($template, $order);
			waapico_log_message( 'Pending Order message at #137 ' . $message );
			//Send the SMS
			if (!empty($message)){
				$phonep = ''; 
				waapico_log_message( 'Here at 1');
				$phonep = waapico_process_phone($order, trim($pphone), false, false);
				waapico_send_sms($phonep, $message);
			}
		}
	}
	
	
	
	
    if (waapico_field('mnumber') == '')
        return;
    $order = new WC_Order($order_id);
    $template = apply_filters('waapico_new_order_template', waapico_fetch_string('msg_new_order'), $order_id);
    $message = waapico_process_variables($template, $order);
    if (empty($message))
        return;
	
				waapico_log_message( 'Here at 2');
    $owners_phone = waapico_process_phone($order, waapico_field('mnumber'), false, true);
	
	waapico_log_message( 'Owner message send ' . $owners_phone . ' M:' .$message);
	
    waapico_send_sms($owners_phone, $message);
    $additional_numbers = apply_filters('waapico_additional_numbers', waapico_field('addnumber'), $order_id);
    if (!empty($additional_numbers)) {
        $numbers = array_filter(explode(",", $additional_numbers));
        foreach ($numbers as $number) {
			
				waapico_log_message( 'Here at 3');
            $phone = waapico_process_phone($order, trim($number), false, true);
            waapico_send_sms($phone, $message);
        }
    }
}
//Schedule notifications for new order by Dishit for Admin orders
if (waapico_field('use_msg_new_order') == 1){
    //add_action('woocommerce_new_order', 'waapico_owner_notification', 20);
    add_action('save_post_shop_order', 'waapico_admin_order_notification', 20);
    //add_action('woocommerce_process_shop_order_meta', 'waapico_admin_order_notification', 20);
    //add_action('wp_insert_post', 'waapico_admin_order_notification', 20);
    //add_action('woocommerce_checkout_update_order_meta', 'waapico_admin_order_notification', 20);
	
}
function waapico_admin_order_notification($order_id)
{
	
	if( !is_admin() )
        return; 
	
	
	// Don’t run if $post_id doesn’t exist OR post type is not order OR update is true
    if ( ! $order_id || get_post_type( $order_id ) != 'shop_order') {
        return;
    }
	
    $order = wc_get_order( $order_id );
	$order_status  = $order->get_status(); // Get the order status 
	
	
	if($order_status != 'auto-draft'){
		waapico_log_message( 'Order Status : This shoild shoot message: ' . $order_status . ' - ' . json_encode($_REQUEST) . ' - ' . $_REQUEST['order_status'] . ' -NO.- ' . $_REQUEST['_billing_phone']);
	}
	else{
		waapico_log_message( 'Not in yet: ' . $order_status . ' - ' . json_encode($_REQUEST) . ' - ' . $_REQUEST['order_status']);
	}
	
	
	if(isset($_REQUEST) && $_REQUEST['order_status']=='wc-pending'){
		if($order_status == 'pending'){
			if (waapico_field('use_msg_pending') == 1){
				$pphone = $_REQUEST['_billing_phone'];
				$pname = $_REQUEST['_billing_first_name'];
				
				/* $template_new = apply_filters('waapico_new_order_template', waapico_fetch_string('msg_new_order'), $order_id);
				$message_new = empty($template_new) ? false : waapico_process_variables($template_new, $order);
				$message_new = str_replace("%billing_first_name%", $pname, $message_new); //Standard fields
				waapico_log_message( 'Pending Order message from admin panel ' . $message_new );
				//Send the SMS
				if (!empty($message_new)){
					$phonep = ''; 
					$phonep = waapico_process_phone($order, trim($pphone), false, true);
					waapico_send_sms($phonep, $message_new);
				} */
				
				$template = apply_filters('waapico_new_order_template', waapico_fetch_string('msg_pending'), $order_id);
				$message = empty($template) ? false : waapico_process_variables($template, $order);
				$message = str_replace("%billing_first_name%", $pname, $message); //Standard fields
				waapico_log_message( 'Pending Order message from admin panel ' . $message );
				//Send the SMS
				if (!empty($message)){
					$phonep = ''; 
					
				waapico_log_message( 'Here at 4');
					$phonep = waapico_process_phone($order, trim($pphone), false, true);
					waapico_send_sms($phonep, $message);
				}
			}
		}
	}
}

//Schedule notification for abandoned cart
if (waapico_field('use_msg_abandon') == 1) {
    if (waapico_field('abandon_checkout') == 1) {
        add_action( 'woocommerce_after_checkout_form', 'waapico_checkout_page_js' );   
    }
    if (waapico_field('abandon_place_order') == 1) {
        add_action( 'woocommerce_new_order', 'waapico_new_order_abandon', 1000 );
    }
    add_action( 'woocommerce_thankyou', 'waapico_remove_abandon_entry' );
    add_action( 'waapico_cron_hook', 'waapico_send_abandon_notifications' );
}

function waapico_checkout_page_js() {
?><script>jQuery(function($){
var prev_phone = false;
function validate_waapico_data() {
    var name = $('#billing_first_name').val(),
        phone = $('#billing_phone').val(),
        country = $('#billing_country').val();
    if (name.length && phone.length && country.length) {
        const url = '<?php echo admin_url("admin-ajax.php"); ?>';
        if (prev_phone) {
            $.post(url, {'action': 'waapico_del_checkout', 'phone': prev_phone});
        }
        var data = {
            'action' : 'waapico_reg_checkout',
            'country' : country,
            'phone' : phone,
            'name' : name,
        };
        $.post(url, data, function(res) {
            prev_phone = res.data.billing_phone || prev_phone;
        });
    }
}
validate_waapico_data();
$('#billing_first_name,#billing_phone,#billing_country').change(validate_waapico_data);
});</script><?php        
}

function waapico_new_order_abandon( $order_id ) {
    global $wpdb, $waapico_db_table;
    $order = wc_get_order( $order_id );
    $country = $order->get_billing_country();
    $phone = $order->get_billing_phone();
    $name = $order->get_billing_first_name();
    $billing_phone = waapico_sanitize_phone_number( $country, $phone );
    if ( empty( $billing_phone ) ) return;
    $wpdb->replace( $waapico_db_table, ['billing_phone' => $billing_phone, 'first_name' => $name, 'order_id' => $order_id], ['%s', '%s', '%d'] );
    waapico_log_message( 'Updated waapico_db for billing phone ' . $billing_phone );
}

function waapico_remove_abandon_entry( $order_id ) {
    global $wpdb, $waapico_db_table;
    $order = wc_get_order( $order_id );
    $country = $order->get_billing_country();
    $phone = $order->get_billing_phone();
    $billing_phone = waapico_sanitize_phone_number( $country, $phone );
    if ( empty( $billing_phone ) ) return;
    $wpdb->delete( $waapico_db_table, ['billing_phone' => $billing_phone] );
    waapico_log_message( 'Deleted waapico_db for billing phone ' . $billing_phone );
}

add_action('wp_ajax_waapico_reg_checkout', 'waapico_reg_checkout_callback');
add_action('wp_ajax_nopriv_waapico_reg_checkout', 'waapico_reg_checkout_callback');
function waapico_reg_checkout_callback() {
    global $wpdb, $waapico_db_table;
    if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'waapico_reg_checkout') {
        $country = sanitize_text_field( $_REQUEST['country'] );
        $phone = sanitize_text_field( $_REQUEST['phone'] );
        $name = sanitize_text_field( $_REQUEST['name'] );
        $billing_phone = waapico_sanitize_phone_number( $country, $phone );
        if ( empty( $billing_phone ) ) die();
        $wpdb->replace( $waapico_db_table, ['billing_phone' => $billing_phone, 'first_name' => $name] );
        waapico_log_message( 'Updated waapico_db for billing phone ' . $billing_phone );
        wp_send_json_success( ['billing_phone' => $billing_phone] );
    }
    die();
}

add_action('wp_ajax_waapico_del_checkout', 'waapico_del_checkout_callback');
add_action('wp_ajax_nopriv_waapico_del_checkout', 'waapico_del_checkout_callback');
function waapico_del_checkout_callback() {
    global $wpdb, $waapico_db_table;
    if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'waapico_del_checkout') {
        $billing_phone = sanitize_text_field( $_REQUEST['phone'] );
        if ( empty( $billing_phone ) ) die();
        $wpdb->delete( $waapico_db_table, ['billing_phone' => $billing_phone] );
        waapico_log_message( 'Deleted waapico_db for billing phone ' . $billing_phone );
    }
    die();
}
function waapico_send_abandon_notifications() {
    waapico_log_message( 'Initiated scheduled event: waapico_send_abandon_notifications' );
    waapico_send_bulk_notifications( 'msg_abandon', waapico_field('abandon_delay') );
    $n = waapico_field('abandon_reminders_count');
    for ($i=0; $i<$n; $i++) {
        $c = "abandon_reminder_" . $i;
        $t = $c . '_template';
        $d = waapico_field($c . '_duration') ?: 0;
        $k = waapico_field($c . '_time_unit') ?: 1;
        waapico_send_bulk_notifications( $t, $d * $k, $i+1 );
    }
}

function waapico_send_bulk_notifications( $template_id, $delay_mins, $reminder_id=0 ) {
    global $wpdb, $waapico_db_table;
    if ( empty( $template_id ) || empty( $delay_mins ) ) return;
    $template = waapico_fetch_string( $template_id );
    if ( empty( $template ) ) return;
    $flag = $reminder_id ? "reminder_{$reminder_id}_sent" : 'msg_sent';
    $rows = $wpdb->get_results( "SELECT billing_phone, first_name, order_id FROM $waapico_db_table WHERE register_ts <= CURRENT_TIMESTAMP - INTERVAL $delay_mins MINUTE AND $flag = 0 ORDER BY register_ts" );
    if ( empty( $rows ) ) return;
    foreach ( $rows as $row ) {
        $billing_phone = $row->billing_phone;
        if ( empty($billing_phone) ) continue;
        $order = null;
        $additional_data = ['first_name' => $row->first_name, 'cart_link' => wc_get_cart_url()];
        if ( $row->order_id ) {
            $order = wc_get_order( $row->order_id );
            if ( $order ) {
                $additional_data['cart_link'] = $order->get_checkout_payment_url();
            }
        }
        $message = waapico_process_variables($template, $order, $additional_data);
        waapico_send_sms($billing_phone, $message);
        waapico_log_message( "$flag for billing phone $billing_phone" );
        $wpdb->update( $waapico_db_table, [$flag => 1], ['billing_phone' => $billing_phone], ['%d'] );
    }
}

add_filter('woocommerce_cod_process_payment_order_status', 'waapico_cod_order_status', 1);
function waapico_cod_order_status($status)
{
    return waapico_field('otp_pre_status');
}

add_action('woocommerce_thankyou', 'waapico_otp_verify_order', 1);
add_action('woocommerce_view_order', 'waapico_otp_verify_order', 1);
function waapico_otp_verify_order($order_id)
{
    $otp_cod = waapico_field('otp_cod');
    $otp_bacs = waapico_field('otp_bacs');
    $otp_cheque = waapico_field('otp_cheque');
    $payment_method = get_post_meta($order_id, '_payment_method', true);
    $otp_verified = get_post_meta($order_id, 'otp_verified', true);
    if ((($otp_cod && ($payment_method == 'cod')) || ($otp_bacs && ($payment_method == 'bacs')) || ($otp_cheque && ($payment_method == 'cheque'))) && ('Yes' != $otp_verified)) {
        $phone = get_post_meta($order_id, '_billing_phone', true);
        update_post_meta($order_id, 'otp_verified', 'No');
        waapico_send_new_order_otp($order_id, $phone);
        waapico_display_otp_verification($order_id, $phone);
    }
}

//Verify OTP via AJAX
add_action('wp_ajax_waapico_verify_otp', 'waapico_verify_otp_callback');
add_action('wp_ajax_nopriv_waapico_verify_otp', 'waapico_verify_otp_callback');
function waapico_verify_otp_callback()
{
    if (isset($_POST['action']) && $_POST['action'] == 'waapico_verify_otp') {
        $data = ['error' => true, 'message' => 'OTP could not be verified', 'verification_failure' => true];
        if (isset($_POST['order_id'])) {
            $order_id = $_POST['order_id'];
            $otp_submitted = $_POST['otp'];
            $otp_stored = get_post_meta($order_id, 'otp_value', true);
            if ($otp_stored == $otp_submitted) {
                update_post_meta($order_id, 'otp_verified', 'Yes');
                $pre_status = waapico_field('otp_pre_status');
                $post_status = waapico_field('otp_post_status');
                $order = wc_get_order($order_id);
                $order->update_status($post_status);
                $data = ['success' => true, 'message' => "Thank You! Your order #$order_id has been confirmed.", 'otp_verified' => true];
            }
        }
        wp_send_json($data);
    }
    die();
}

function waapico_sanitize_phone_number($country, $number) {
    $intl_prefix = waapico_country_prefix($country);
    $phone = str_replace(array('+', '-'), '', filter_var($number, FILTER_SANITIZE_NUMBER_INT));
    $phone = ltrim($phone, '0');
    preg_match("/(\d{1,4})[0-9.\- ]+/", $phone, $prefix);
    if (strpos($prefix[1], $intl_prefix) !== 0) {
        $phone = $intl_prefix . $phone;
    }
    /* if (strpos($prefix[1], "+") !== 0 ) {
        $phone = "+" . $phone;
    } */
    return $phone;
}

//Request OTP resend via AJAX
add_action('wp_ajax_waapico_resend_otp', 'waapico_resend_otp_callback');
add_action('wp_ajax_nopriv_waapico_resend_otp', 'waapico_resend_otp_callback');
function waapico_resend_otp_callback()
{
    if (isset($_POST['action']) && $_POST['action'] == 'waapico_resend_otp') {
        $data = ['error' => true, 'message' => 'Failed to send OTP'];
        if (isset($_POST['order_id'])) {
            $order_id = $_POST['order_id'];
            $otp_verified = get_post_meta($order_id, 'otp_verified', true);
            if ($otp_verified != 'Yes') {
                $phone = get_post_meta($order_id, '_billing_phone', true);
                waapico_send_new_order_otp($order_id, $phone);
                $data = ['success' => true, 'message' => "OTP Sent to $phone for order #$order_id"];
            }
        }
        wp_send_json($data);
    }
    die();
}

//Request OTP send via AJAX
add_action('wp_ajax_waapico_send_otp', 'waapico_send_otp_callback');
add_action('wp_ajax_nopriv_waapico_send_otp', 'waapico_send_otp_callback');
function waapico_send_otp_callback()
{
    if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'waapico_send_otp') {
        $data = ['error' => true, 'message' => 'Failed to generate OTP. Ensure that you have entered the correct number.', 'number' => NULL];
        $country_code = sanitize_text_field($_REQUEST['country']);
        $billing_phone = sanitize_text_field($_REQUEST['phone']);
        if (!empty($country_code) && !empty($billing_phone)) {
            $user_phone = waapico_sanitize_phone_number( $country_code, $billing_phone );
            $transient_id = 'OTP_REG_' . $user_phone;
            $otp_number = get_transient( $transient_id ) ?: waapico_generate_otp();
            set_transient( $transient_id, $otp_number, 600 );
            $message = waapico_process_variables(waapico_fetch_string('msg_otp_checkout'), $order, ['otp' => $otp_number]);
            waapico_send_otp($user_phone, $message);
            $data = ['success' => true, 'message' => "OTP sent successfully to $user_phone", 'number' => $user_phone];
        }
        wp_send_json($data);
    }
    die();
}


function waapico_generate_otp()
{
    return mt_rand(100000, 999999);
}

function waapico_send_new_order_otp($order_id, $phone)
{
    $order = wc_get_order($order_id);
	
				waapico_log_message( 'Here at 5');
    $phone = waapico_process_phone($order, $phone);
    $otp_number = waapico_generate_otp();
    $template = apply_filters('waapico_new_order_otp_template', waapico_fetch_string('msg_otp_new_order'), $order_id);
    $message = waapico_process_variables($template, $order, ['otp' => $otp_number]);
    waapico_send_otp($phone, $message);
    update_post_meta($order_id, 'otp_value', $otp_number);
}

add_action('woocommerce_before_order_notes', 'waapico_otp_order_checkout');
function waapico_otp_order_checkout() {
    if (waapico_field('require_checkout_otp')) { ?>
    <h3>OTP Verification</h3>
    <div id='su-otp-verification-block' style='background:#EEE;padding:10px;border-radius:5px'>
        <div class='waapico-notifications'>
            <div class="woocommerce-info">
            An OTP has been sent to your Billing Phone. You need to enter the OTP below before you can place your order.
            </div>
        </div>
        <center>
        <label style='font-weight:bold;color:#000'>OTP </label>
        <input id='waapico-otp-field' size='6' style='letter-spacing:5px;font-weight:bold;padding:10px' name='waapico_order_otp'/>
        <input id='waapico_resend_otp_btn' type='button' class='button alt' value='Resend OTP'/>
        </center>
        <p>Please make sure you are in a good mobile signal zone. Resend button will get activated in 30 seconds. Please request again if you have not received the OTP in next 30 seconds.</p>
    </div>
    <script>
    jQuery(function($){
        var otp_failure_count = 0,
            otp_resend_count = 0,
            country = '',
            phone = '',
            url = '<?php echo admin_url("admin-ajax.php"); ?>';
        function waapico_resend_otp() {
            if (country == '' || phone == '') return;
            var data = {
                'action' : 'waapico_send_otp',
                'country' : country,
                'phone' : phone
            };
            $.get(url, data, function(res){
                $('#su-otp-verification-block').show();
                if (res.success) {
                    disableResendOTP();
                    otp_resend_count++;
                } else {
                    otp_failure_count++;
                }
                $('.waapico-notifications > .woocommerce-info').text(res.message);
            });
        }
        function enableResendOTP() {
            if (otp_resend_count < 3) {
                $('#waapico_resend_otp_btn').prop('disabled', false);
            }
        }
        function disableResendOTP() {
            $('#waapico_resend_otp_btn').prop('disabled', true);
            setTimeout(enableResendOTP, 30000);
        }
        $('#waapico_resend_otp_btn').click(waapico_resend_otp);
        $('input[name="billing_phone"]').change(function(){
            phone = $(this).val().trim();
            if (phone != '') waapico_resend_otp();
        }).change();
        $('select[name="billing_country"]').change(function(){
            country = $(this).val().trim();
            if (country != '') waapico_resend_otp();
        }).change();
    });
    </script>
    <?php }
}

add_action('woocommerce_checkout_process','waapico_validate_order_otp');
function waapico_validate_order_otp() {
    if (waapico_field('require_checkout_otp')) {
        $country_code = sanitize_text_field($_POST['billing_country']);
        $billing_phone = sanitize_text_field($_REQUEST['billing_phone']);
        if (!empty($country_code) && !empty($billing_phone)) {
            $otp = sanitize_text_field($_POST['waapico_order_otp']) ?? NULL;
            if (!$otp) {
                wc_add_notice( __( 'OTP Verification is required.' ), 'error' );
                return;
            }
            $user_phone = waapico_sanitize_phone_number( $country_code, $billing_phone );
            $transient_id = 'OTP_REG_' . $user_phone;
            $otp_number = get_transient($transient_id);
            if ($otp_number && $otp_number == $otp) {
                return;
            } else {
                wc_add_notice( __( 'OTP Verification failed. Please enter the correct OTP.' ), 'error' );
            }
        }
    }
}

function waapico_display_otp_verification($order_id, $phone)
{
    ?>
    <script type='text/javascript'>
    jQuery(function($){
        var otp_failure_count = 0,
            otp_resend_count = 0;
        function showSpinner() {
            $('.waapico-notifications').html('<center><img src="<?= admin_url("images/spinner-2x.gif") ?>"/></center>');
        }
        function process_json_response(response) {
            var jsonobj = JSON.parse(JSON.stringify(response));
            if (jsonobj.error) {
                $('.waapico-notifications').html('<div class="woocommerce-error">'+jsonobj.message+'</div>');
                if (jsonobj.verification_failure) {
                    otp_failure_count++;
                    if (otp_failure_count > 3) {
                        $('.waapico-notifications').append('<br/><h3>It seems that there is a difficulty in verifying your order. Please call our support number to verify your order.</h3>');
                    }
                }
            } else {
                if (jsonobj.otp_verified) {
                    $('#su-otp-verification-block').html('<h3>'+jsonobj.message+'</h3>');
                } else {
                    $('.waapico-notifications').html('<div class="woocommerce-message">'+jsonobj.message+'</div>');
                    otp_resend_count++;
                }
            }
        }
        function waapico_verify_otp() {
            showSpinner();
            var data = {
                'action' : 'waapico_verify_otp',
                'order_id' : <?= $order_id ?>,
                'otp' : document.getElementById('waapico-otp-field').value
            };
            $.post(
                "<?php echo admin_url("admin-ajax.php"); ?>",
                data,
                process_json_response
            );
        }
        function waapico_resend_otp() {
            showSpinner();
            var data = {
                'action' : 'waapico_resend_otp',
                'order_id' : <?= $order_id ?>
            };
            $.post(
                "<?php echo admin_url("admin-ajax.php"); ?>",
                data,
                process_json_response
            );
            disableResendOTP();
        }
        function enableResendOTP() {
            if (otp_resend_count < 3) {
                $('#waapico_resend_otp_btn').prop('disabled', false);
            }
        }
        function disableResendOTP() {
            $('#waapico_resend_otp_btn').prop('disabled', true);
            setTimeout(enableResendOTP, 30000);
        }
        $('p.woocommerce-thankyou-order-received, ul.woocommerce-thankyou-order-details').hide();
        $('#waapico_verify_otp_btn').click(waapico_verify_otp);
        $('#waapico_resend_otp_btn').click(waapico_resend_otp);
        disableResendOTP();
    });
    </script>
    <div id='su-otp-verification-block' style='background:#EEE;padding:10px;border-radius:5px'>
        <h3>OTP Verification</h3>
        <div class='waapico-notifications'>
            <div class="woocommerce-info">
            OTP sent to mobile no: <?= $phone ?> for order #<?= $order_id ?>. Your order will be confirmed upon completion of OTP verification.
            </div>
        </div>
        <center>
        <label style='font-weight:bold;color:#000'>OTP </label>
        <input id='waapico-otp-field' size='6' style='letter-spacing:5px;font-weight:bold;padding:10px'/>
        <input id='waapico_verify_otp_btn' type='button' class='button' value='Verify'/>
        <input id='waapico_resend_otp_btn' type='button' class='button alt' value='Resend OTP'/>
        </center>
        <p>Please make sure you are in a good mobile signal zone. Resend button will get activated in 30 seconds. Please request again if you have not received the OTP in next 30 seconds.</p>
    </div>
    <?php
}
    
//Schedule notifications for order status change
add_action('woocommerce_order_status_changed', 'waapico_process_status', 10, 3);
function waapico_process_status($order_id, $old_status, $status)
{
    $order = new WC_Order($order_id);
    $shipping_phone = false;
    $phone = $order->get_billing_phone();

    //If have to send messages to shipping phone
    if (waapico_field('alt_phone') == 1) {
        $phone = get_post_meta($order->get_id(), '_shipping_phone', true);
        $shipping_phone = true;
    }
    
    //Remove old 'wc-' prefix from the order status
    $status = str_replace('wc-', '', $status);
    
    //Sanitize the phone number
	
				waapico_log_message( 'Here at 6');
    $phone = waapico_process_phone($order, $phone, $shipping_phone);
    
    //Get the message corresponding to order status
    $template = "";
	waapico_log_message( 'Order Status change after manual change ' . $status . ' Orderid: ' .  $order->get_id());
    switch ($status) {
        case 'pending':
            if (waapico_field('use_msg_pending') == 1)
                $template = apply_filters('waapico_new_order_template', waapico_fetch_string('msg_pending'), $order_id);
            break;
        case 'on-hold':
            if (waapico_field('use_msg_on_hold') == 1)
                $template = apply_filters('waapico_on_hold_template', waapico_fetch_string('msg_on_hold'), $order_id);
            break;
        case 'processing':
            if (waapico_field('use_msg_processing') == 1)
                $template = apply_filters('waapico_processing_template', waapico_fetch_string('msg_processing'), $order_id);
            break;
        case 'completed':
            if (waapico_field('use_msg_completed') == 1)
                $template = apply_filters('waapico_completed_template', waapico_fetch_string('msg_completed'), $order_id);
            break;
        case 'cancelled':
            if (waapico_field('use_msg_cancelled') == 1)
                $template = apply_filters('waapico_cancelled_template', waapico_fetch_string('msg_cancelled'), $order_id);
            break;
        case 'refunded':
            if (waapico_field('use_msg_refunded') == 1)
                $template = apply_filters('waapico_refunded_template', waapico_fetch_string('msg_refunded'), $order_id);
            break;
        case 'failed':
            if (waapico_field('use_msg_failure') == 1)
                $template = apply_filters('waapico_failure_template', waapico_fetch_string('msg_failure'), $order_id);
            break;
        default:
            if (waapico_field('use_msg_custom') == 1)
                $template = apply_filters('waapico_custom_template', waapico_fetch_string('msg_custom'), $order_id);
    }

    //Process the template
    $message = empty($template) ? false : waapico_process_variables($template, $order);
    
    //Send the SMS
    if (!empty($message))
        waapico_send_sms($phone, $message);
}

function waapico_message_encode($message)
{
    return urlencode(html_entity_decode($message, ENT_QUOTES, "UTF-8"));
}

function waapico_process_phone($order, $phone, $shipping = false, $owners_phone = false)
{
    //Sanitize phone number
    $phone = str_replace(array('+', '-'), '', filter_var($phone, FILTER_SANITIZE_NUMBER_INT));
    $phone = ltrim($phone, '0');
     
    //Obtain country code prefix
    $country = WC()->countries->get_base_country();
    if (!$owners_phone) {
        $country = $shipping ? $order->get_shipping_country() : $order->get_billing_country();
    }
	
	waapico_log_message( 'Country message at #748 ' . $country );
	
    $intl_prefix = waapico_country_prefix($country);

    //Check for already included prefix
    preg_match("/(\d{1,4})[0-9.\- ]+/", $phone, $prefix);
    
    //If prefix hasn't been added already, add it
    if (strpos($prefix[1], $intl_prefix) !== 0) {
        $phone = $intl_prefix . $phone;
    }
    
    /* //Prefix '+' as required
    if ( strpos( $prefix[1], "+" ) !== 0 ) {
        $phone = "+" . $phone;
    } */

    return $phone;
}


function waapico_process_variables($message, $order=null, $additional_data=[])
{
    $sms_strings = array("id", "status", "prices_include_tax", "tax_display_cart", "display_totals_ex_tax", "display_cart_ex_tax", "order_date", "modified_date", "customer_message", "customer_note", "post_status", "shop_name", "note", "order_product");
    $waapico_variables = array("order_key", "billing_first_name", "billing_last_name", "billing_company", "billing_address_1", "billing_address_2", "billing_city", "billing_postcode", "billing_country", "billing_state", "billing_email", "billing_phone", "shipping_first_name", "shipping_last_name", "shipping_company", "shipping_address_1", "shipping_address_2", "shipping_city", "shipping_postcode", "shipping_country", "shipping_state", "shipping_method", "shipping_method_title", "payment_method", "payment_method_title", "order_discount", "cart_discount", "order_tax", "order_shipping", "order_shipping_tax", "order_total", "order_currency");
    $specials = array("order_date", "modified_date", "shop_name", "id", "order_product", 'signature');
    $order_variables = $order ? get_post_custom($order->get_id()) : []; //WooCommerce 2.1
    $custom_variables = explode("\n", str_replace(array("\r\n", "\r"), "\n", waapico_field('variables')));
    $additional_variables = array_keys($additional_data);
    $new_line = 'nl';

    if (empty($order)) {
        $order = new WC_Order();
    }

    preg_match_all("/%(.*?)%/", $message, $search);
    foreach ($search[1] as $variable) {
        $variable = strtolower($variable);

        if ($variable == $new_line) {
            $message = str_replace("%" . $variable . "%", PHP_EOL, $message);
        }

        if (!in_array($variable, $sms_strings) && !in_array($variable, $waapico_variables) && !in_array($variable, $specials) && !in_array($variable, $custom_variables) && !in_array($variable, $additional_variables)) {
            continue;
        }

        if (!in_array($variable, $specials)) {
            if (in_array($variable, $sms_strings)) {
                $message = str_replace("%" . $variable . "%", $order->$variable, $message); //Standard fields
            } else if (in_array($variable, $waapico_variables) && isset($order_variables["_" . $variable])) {
                $message = str_replace("%" . $variable . "%", $order_variables["_" . $variable][0], $message); //Meta fields
            } else if (in_array($variable, $custom_variables) && isset($order_variables[$variable])) {
                $message = str_replace("%" . $variable . "%", $order_variables[$variable][0], $message);
            }
            if (in_array($variable, $additional_variables) && isset($additional_data[$variable])) {
                $message = str_replace("%" . $variable . "%", $additional_data[$variable], $message);
            }
        } else if ($variable == "order_date" || $variable == "modified_date") {
            $message = str_replace("%" . $variable . "%", date_i18n(woocommerce_date_format(), strtotime($order->$variable)), $message);
        } else if ($variable == "shop_name") {
            $message = str_replace("%" . $variable . "%", get_bloginfo('name'), $message);
        } else if ($variable == "id") {
            $message = str_replace("%" . $variable . "%", $order->get_order_number(), $message);
        } else if ($variable == "order_product") {
            $products = $order->get_items();
            $quantity = $products[key($products)]['name'];
            if (strlen($quantity) > 10) {
                $quantity = substr($quantity, 0, 10) . "...";
            }
            if (count($products) > 1) {
                $quantity .= " (+" . (count($products) - 1) . ")";
            }
            $message = str_replace("%" . $variable . "%", $quantity, $message);
        } else if ($variable == "signature") {
            $message = str_replace("%" . $variable . "%", waapico_field('signature'), $message);
        }
    }
    return $message;
}

function waapico_country_prefix($country = '')
{
    $countries = array(
        'AC' => '247',
        'AD' => '376',
        'AE' => '971',
        'AF' => '93',
        'AG' => '1268',
        'AI' => '1264',
        'AL' => '355',
        'AM' => '374',
        'AO' => '244',
        'AQ' => '672',
        'AR' => '54',
        'AS' => '1684',
        'AT' => '43',
        'AU' => '61',
        'AW' => '297',
        'AX' => '358',
        'AZ' => '994',
        'BA' => '387',
        'BB' => '1246',
        'BD' => '880',
        'BE' => '32',
        'BF' => '226',
        'BG' => '359',
        'BH' => '973',
        'BI' => '257',
        'BJ' => '229',
        'BL' => '590',
        'BM' => '1441',
        'BN' => '673',
        'BO' => '591',
        'BQ' => '599',
        'BR' => '55',
        'BS' => '1242',
        'BT' => '975',
        'BW' => '267',
        'BY' => '375',
        'BZ' => '501',
        'CA' => '1',
        'CC' => '61',
        'CD' => '243',
        'CF' => '236',
        'CG' => '242',
        'CH' => '41',
        'CI' => '225',
        'CK' => '682',
        'CL' => '56',
        'CM' => '237',
        'CN' => '86',
        'CO' => '57',
        'CR' => '506',
        'CU' => '53',
        'CV' => '238',
        'CW' => '599',
        'CX' => '61',
        'CY' => '357',
        'CZ' => '420',
        'DE' => '49',
        'DJ' => '253',
        'DK' => '45',
        'DM' => '1767',
        'DO' => '1809',
        'DO' => '1829',
        'DO' => '1849',
        'DZ' => '213',
        'EC' => '593',
        'EE' => '372',
        'EG' => '20',
        'EH' => '212',
        'ER' => '291',
        'ES' => '34',
        'ET' => '251',
        'EU' => '388',
        'FI' => '358',
        'FJ' => '679',
        'FK' => '500',
        'FM' => '691',
        'FO' => '298',
        'FR' => '33',
        'GA' => '241',
        'GB' => '44',
        'GD' => '1473',
        'GE' => '995',
        'GF' => '594',
        'GG' => '44',
        'GH' => '233',
        'GI' => '350',
        'GL' => '299',
        'GM' => '220',
        'GN' => '224',
        'GP' => '590',
        'GQ' => '240',
        'GR' => '30',
        'GT' => '502',
        'GU' => '1671',
        'GW' => '245',
        'GY' => '592',
        'HK' => '852',
        'HN' => '504',
        'HR' => '385',
        'HT' => '509',
        'HU' => '36',
        'ID' => '62',
        'IE' => '353',
        'IL' => '972',
        'IM' => '44',
        'IN' => '91',
        'IO' => '246',
        'IQ' => '964',
        'IR' => '98',
        'IS' => '354',
        'IT' => '39',
        'JE' => '44',
        'JM' => '1876',
        'JO' => '962',
        'JP' => '81',
        'KE' => '254',
        'KG' => '996',
        'KH' => '855',
        'KI' => '686',
        'KM' => '269',
        'KN' => '1869',
        'KP' => '850',
        'KR' => '82',
        'KW' => '965',
        'KY' => '1345',
        'KZ' => '7',
        'LA' => '856',
        'LB' => '961',
        'LC' => '1758',
        'LI' => '423',
        'LK' => '94',
        'LR' => '231',
        'LS' => '266',
        'LT' => '370',
        'LU' => '352',
        'LV' => '371',
        'LY' => '218',
        'MA' => '212',
        'MC' => '377',
        'MD' => '373',
        'ME' => '382',
        'MF' => '590',
        'MG' => '261',
        'MH' => '692',
        'MK' => '389',
        'ML' => '223',
        'MM' => '95',
        'MN' => '976',
        'MO' => '853',
        'MP' => '1670',
        'MQ' => '596',
        'MR' => '222',
        'MS' => '1664',
        'MT' => '356',
        'MU' => '230',
        'MV' => '960',
        'MW' => '265',
        'MX' => '52',
        'MY' => '60',
        'MZ' => '258',
        'NA' => '264',
        'NC' => '687',
        'NE' => '227',
        'NF' => '672',
        'NG' => '234',
        'NI' => '505',
        'NL' => '31',
        'NO' => '47',
        'NP' => '977',
        'NR' => '674',
        'NU' => '683',
        'NZ' => '64',
        'OM' => '968',
        'PA' => '507',
        'PE' => '51',
        'PF' => '689',
        'PG' => '675',
        'PH' => '63',
        'PK' => '92',
        'PL' => '48',
        'PM' => '508',
        'PR' => '1787',
        'PR' => '1939',
        'PS' => '970',
        'PT' => '351',
        'PW' => '680',
        'PY' => '595',
        'QA' => '974',
        'QN' => '374',
        'QS' => '252',
        'QY' => '90',
        'RE' => '262',
        'RO' => '40',
        'RS' => '381',
        'RU' => '7',
        'RW' => '250',
        'SA' => '966',
        'SB' => '677',
        'SC' => '248',
        'SD' => '249',
        'SE' => '46',
        'SG' => '65',
        'SH' => '290',
        'SI' => '386',
        'SJ' => '47',
        'SK' => '421',
        'SL' => '232',
        'SM' => '378',
        'SN' => '221',
        'SO' => '252',
        'SR' => '597',
        'SS' => '211',
        'ST' => '239',
        'SV' => '503',
        'SX' => '1721',
        'SY' => '963',
        'SZ' => '268',
        'TA' => '290',
        'TC' => '1649',
        'TD' => '235',
        'TG' => '228',
        'TH' => '66',
        'TJ' => '992',
        'TK' => '690',
        'TL' => '670',
        'TM' => '993',
        'TN' => '216',
        'TO' => '676',
        'TR' => '90',
        'TT' => '1868',
        'TV' => '688',
        'TW' => '886',
        'TZ' => '255',
        'UA' => '380',
        'UG' => '256',
        'UK' => '44',
        'US' => '1',
        'UY' => '598',
        'UZ' => '998',
        'VA' => '379',
        'VA' => '39',
        'VC' => '1784',
        'VE' => '58',
        'VG' => '1284',
        'VI' => '1340',
        'VN' => '84',
        'VU' => '678',
        'WF' => '681',
        'WS' => '685',
        'XC' => '991',
        'XD' => '888',
        'XG' => '881',
        'XL' => '883',
        'XN' => '857',
        'XN' => '858',
        'XN' => '870',
        'XP' => '878',
        'XR' => '979',
        'XS' => '808',
        'XT' => '800',
        'XV' => '882',
        'YE' => '967',
        'YT' => '262',
        'ZA' => '27',
        'ZM' => '260',
        'ZW' => '263'
    );

    return ($country == '') ? $countries : (isset($countries[$country]) ? $countries[$country] : '');
}

function waapico_remote_get($url)
{
    $response = wp_remote_get($url, array('timeout' => 15));
    if (is_wp_error($response)) {
        $response = $response->get_error_message();
    } elseif (is_array($response)) {
        $response = $response['body'];
    }
    return $response;
}

function waapico_send_sms($phone, $message)
{
    $aid = waapico_field('aid');
	if($aid == ''){
		$aid = 'magecomp.com@gmail.com';
	}
    $pin = waapico_field('pin');
    $sender = waapico_field('sender');
	waapico_log_message( 'waapico_send_sms called');
    waapico_send_sms_text($phone, $message, $aid, $pin, $sender);
	waapico_log_message( 'waapico_send_sms called after');
}

function waapico_send_otp($phone, $message)
{
    $aid = waapico_field('otp_aid');
	if($aid == ''){
		$aid = 'magecomp.com@gmail.com';
	}
    $pin = waapico_field('otp_pin');
    $sender = waapico_field('otp_sender');

    //Send transactional SMS if required fields are missing
    if (empty($aid) || empty($pin) || empty($sender)) {
        waapico_send_sms($phone, $message);
    } else {
        waapico_send_sms_text($phone, $message, $aid, $pin, $sender);
    }
}

function waapico_send_sms_text($phone, $message, $aid, $pin, $sender)
{
	waapico_log_message( 'waapico_send_sms_text');
    global $woocommerce, $waapico_plugin_domn;
	waapico_log_message( 'waapico_send_sms_text '.$phone.' - '.$message.' - '.$aid.' - '.$pin.' - '.$sender);
    //Don't send the SMS if required fields are missing
    if (empty($phone) || empty($message) || empty($aid) || empty($pin) || empty($sender))
        return;
    //Send the SMS by calling the API
    waapico_log_message( 'Everytime msg send : ' . $phone . ' Message: ' . $message );
    $message = waapico_message_encode($message);
    switch(waapico_field('api')) {
        case 1:
            $fetchurl = "http://wa.magecomp.com/api/send.php?client_id=$pin&instance=$sender&type=text&number=$phone&message=$message"; break;
        case 2:
            $fetchurl = "http://wa.magecomp.com/api/send.php?client_id=$pin&instance=$sender&type=text&number=$phone&message=$message"; break;
        default:
            $fetchurl = "http://wa.magecomp.com/api/send.php?client_id=$pin&instance=$sender&type=text&number=$phone&message=$message";
    }
	waapico_log_message( 'DG Send url : ' . $fetchurl );
    $response = waapico_remote_get($fetchurl);
    
    //Log the response
    if (1 == waapico_field('log_sms')) {
        $log_txt = __('Mobile number: ', $waapico_plugin_domn) . $phone . PHP_EOL;
        $log_txt .= __('Message: ', $waapico_plugin_domn) . $message . PHP_EOL;
        $log_txt .= __('Gateway response: ', $waapico_plugin_domn) . $response . PHP_EOL;
        waapico_log_message($log_txt);
    }
}

function waapico_log_message( $message ) {
    global $waapico_plugin_domn, $waapico_logger;
    if ($waapico_logger == NULL)
        $waapico_logger = class_exists( 'WC_Logger' ) ? new WC_Logger() : $woocommerce->logger();
    $waapico_logger->add($waapico_plugin_domn, $message);
}

/**
 * User registration OTP mechanism
 */

if (waapico_field('otp_user_reg') == 1) {
    add_action('register_form', 'waapico_register_form');
    // add_action('woocommerce_register_form_start', 'waapico_register_form');
    add_filter('registration_errors', 'waapico_registration_errors', 10, 3);
    add_action('woocommerce_register_post', 'waapico_wc_registration_errors', 10, 3);
    add_action('user_register', 'waapico_user_register');
    add_action('woocommerce_created_customer', 'waapico_user_register');
    if (waapico_field('otp_user_reg_wc') == 1)
        add_action('woocommerce_register_form', 'waapico_register_form');
}

function waapico_sanitize_data($data)
{
    $data = (!empty($data)) ? sanitize_text_field($data) : '';
    $data = preg_replace('/[^0-9]/', '', $data);
    return ltrim($data, '0');
}

function waapico_country_name($country='') {
    $countries = array(
		"AL" => 'Albania',
		"DZ" => 'Algeria',
		"AS" => 'American Samoa',
		"AD" => 'Andorra',
		"AO" => 'Angola',
		"AI" => 'Anguilla',
		"AQ" => 'Antarctica',
		"AG" => 'Antigua and Barbuda',
		"AR" => 'Argentina',
		"AM" => 'Armenia',
		"AW" => 'Aruba',
		"AU" => 'Australia',
		"AT" => 'Austria',
		"AZ" => 'Azerbaijan',
		"BS" => 'Bahamas',
		"BH" => 'Bahrain',
		"BD" => 'Bangladesh',
		"BB" => 'Barbados',
		"BY" => 'Belarus',
		"BE" => 'Belgium',
		"BZ" => 'Belize',
		"BJ" => 'Benin',
		"BM" => 'Bermuda',
		"BT" => 'Bhutan',
		"BO" => 'Bolivia',
		"BA" => 'Bosnia and Herzegovina',
		"BW" => 'Botswana',
		"BV" => 'Bouvet Island',
		"BR" => 'Brazil',
		"BQ" => 'British Antarctic Territory',
		"IO" => 'British Indian Ocean Territory',
		"VG" => 'British Virgin Islands',
		"BN" => 'Brunei',
		"BG" => 'Bulgaria',
		"BF" => 'Burkina Faso',
		"BI" => 'Burundi',
		"KH" => 'Cambodia',
		"CM" => 'Cameroon',
		"CA" => 'Canada',
		"CT" => 'Canton and Enderbury Islands',
		"CV" => 'Cape Verde',
		"KY" => 'Cayman Islands',
		"CF" => 'Central African Republic',
		"TD" => 'Chad',
		"CL" => 'Chile',
		"CN" => 'China',
		"CX" => 'Christmas Island',
		"CC" => 'Cocos [Keeling] Islands',
		"CO" => 'Colombia',
		"KM" => 'Comoros',
		"CG" => 'Congo - Brazzaville',
		"CD" => 'Congo - Kinshasa',
		"CK" => 'Cook Islands',
		"CR" => 'Costa Rica',
		"HR" => 'Croatia',
		"CU" => 'Cuba',
		"CY" => 'Cyprus',
		"CZ" => 'Czech Republic',
		"CI" => 'Côte d’Ivoire',
		"DK" => 'Denmark',
		"DJ" => 'Djibouti',
		"DM" => 'Dominica',
		"DO" => 'Dominican Republic',
		"NQ" => 'Dronning Maud Land',
		"DD" => 'East Germany',
		"EC" => 'Ecuador',
		"EG" => 'Egypt',
		"SV" => 'El Salvador',
		"GQ" => 'Equatorial Guinea',
		"ER" => 'Eritrea',
		"EE" => 'Estonia',
		"ET" => 'Ethiopia',
		"FK" => 'Falkland Islands',
		"FO" => 'Faroe Islands',
		"FJ" => 'Fiji',
		"FI" => 'Finland',
		"FR" => 'France',
		"GF" => 'French Guiana',
		"PF" => 'French Polynesia',
		"TF" => 'French Southern Territories',
		"FQ" => 'French Southern and Antarctic Territories',
		"GA" => 'Gabon',
		"GM" => 'Gambia',
		"GE" => 'Georgia',
		"DE" => 'Germany',
		"GH" => 'Ghana',
		"GI" => 'Gibraltar',
		"GR" => 'Greece',
		"GL" => 'Greenland',
		"GD" => 'Grenada',
		"GP" => 'Guadeloupe',
		"GU" => 'Guam',
		"GT" => 'Guatemala',
		"GG" => 'Guernsey',
		"GN" => 'Guinea',
		"GW" => 'Guinea-Bissau',
		"GY" => 'Guyana',
		"HT" => 'Haiti',
		"HM" => 'Heard Island and McDonald Islands',
		"HN" => 'Honduras',
		"HK" => 'Hong Kong SAR China',
		"HU" => 'Hungary',
		"IS" => 'Iceland',
		"IN" => 'India',
		"ID" => 'Indonesia',
		"IR" => 'Iran',
		"IQ" => 'Iraq',
		"IE" => 'Ireland',
		"IM" => 'Isle of Man',
		"IL" => 'Israel',
		"IT" => 'Italy',
		"JM" => 'Jamaica',
		"JP" => 'Japan',
		"JE" => 'Jersey',
		"JT" => 'Johnston Island',
		"JO" => 'Jordan',
		"KZ" => 'Kazakhstan',
		"KE" => 'Kenya',
		"KI" => 'Kiribati',
		"KW" => 'Kuwait',
		"KG" => 'Kyrgyzstan',
		"LA" => 'Laos',
		"LV" => 'Latvia',
		"LB" => 'Lebanon',
		"LS" => 'Lesotho',
		"LR" => 'Liberia',
		"LY" => 'Libya',
		"LI" => 'Liechtenstein',
		"LT" => 'Lithuania',
		"LU" => 'Luxembourg',
		"MO" => 'Macau SAR China',
		"MK" => 'Macedonia',
		"MG" => 'Madagascar',
		"MW" => 'Malawi',
		"MY" => 'Malaysia',
		"MV" => 'Maldives',
		"ML" => 'Mali',
		"MT" => 'Malta',
		"MH" => 'Marshall Islands',
		"MQ" => 'Martinique',
		"MR" => 'Mauritania',
		"MU" => 'Mauritius',
		"YT" => 'Mayotte',
		"FX" => 'Metropolitan France',
		"MX" => 'Mexico',
		"FM" => 'Micronesia',
		"MI" => 'Midway Islands',
		"MD" => 'Moldova',
		"MC" => 'Monaco',
		"MN" => 'Mongolia',
		"ME" => 'Montenegro',
		"MS" => 'Montserrat',
		"MA" => 'Morocco',
		"MZ" => 'Mozambique',
		"MM" => 'Myanmar [Burma]',
		"NA" => 'Namibia',
		"NR" => 'Nauru',
		"NP" => 'Nepal',
		"NL" => 'Netherlands',
		"AN" => 'Netherlands Antilles',
		"NT" => 'Neutral Zone',
		"NC" => 'New Caledonia',
		"NZ" => 'New Zealand',
		"NI" => 'Nicaragua',
		"NE" => 'Niger',
		"NG" => 'Nigeria',
		"NU" => 'Niue',
		"NF" => 'Norfolk Island',
		"KP" => 'North Korea',
		"VD" => 'North Vietnam',
		"MP" => 'Northern Mariana Islands',
		"NO" => 'Norway',
		"OM" => 'Oman',
		"PC" => 'Pacific Islands Trust Territory',
		"PK" => 'Pakistan',
		"PW" => 'Palau',
		"PS" => 'Palestinian Territories',
		"PA" => 'Panama',
		"PZ" => 'Panama Canal Zone',
		"PG" => 'Papua New Guinea',
		"PY" => 'Paraguay',
		"YD" => 'People\'s Democratic Republic of Yemen',
		"PE" => 'Peru',
		"PH" => 'Philippines',
		"PN" => 'Pitcairn Islands',
		"PL" => 'Poland',
		"PT" => 'Portugal',
		"PR" => 'Puerto Rico',
		"QA" => 'Qatar',
		"RO" => 'Romania',
		"RU" => 'Russia',
		"RW" => 'Rwanda',
		"RE" => 'Réunion',
		"BL" => 'Saint Barthélemy',
		"SH" => 'Saint Helena',
		"KN" => 'Saint Kitts and Nevis',
		"LC" => 'Saint Lucia',
		"MF" => 'Saint Martin',
		"PM" => 'Saint Pierre and Miquelon',
		"VC" => 'Saint Vincent and the Grenadines',
		"WS" => 'Samoa',
		"SM" => 'San Marino',
		"SA" => 'Saudi Arabia',
		"SN" => 'Senegal',
		"RS" => 'Serbia',
		"CS" => 'Serbia and Montenegro',
		"SC" => 'Seychelles',
		"SL" => 'Sierra Leone',
		"SG" => 'Singapore',
		"SK" => 'Slovakia',
		"SI" => 'Slovenia',
		"SB" => 'Solomon Islands',
		"SO" => 'Somalia',
		"ZA" => 'South Africa',
		"GS" => 'South Georgia and the South Sandwich Islands',
		"KR" => 'South Korea',
		"ES" => 'Spain',
		"LK" => 'Sri Lanka',
		"SD" => 'Sudan',
		"SR" => 'Suriname',
		"SJ" => 'Svalbard and Jan Mayen',
		"SZ" => 'Swaziland',
		"SE" => 'Sweden',
		"CH" => 'Switzerland',
		"SY" => 'Syria',
		"ST" => 'São Tomé and Príncipe',
		"TW" => 'Taiwan',
		"TJ" => 'Tajikistan',
		"TZ" => 'Tanzania',
		"TH" => 'Thailand',
		"TL" => 'Timor-Leste',
		"TG" => 'Togo',
		"TK" => 'Tokelau',
		"TO" => 'Tonga',
		"TT" => 'Trinidad and Tobago',
		"TN" => 'Tunisia',
		"TR" => 'Turkey',
		"TM" => 'Turkmenistan',
		"TC" => 'Turks and Caicos Islands',
		"TV" => 'Tuvalu',
		"UM" => 'U.S. Minor Outlying Islands',
		"PU" => 'U.S. Miscellaneous Pacific Islands',
		"VI" => 'U.S. Virgin Islands',
		"UG" => 'Uganda',
		"UA" => 'Ukraine',
		"SU" => 'Union of Soviet Socialist Republics',
		"AE" => 'United Arab Emirates',
		"GB" => 'United Kingdom',
		"US" => 'United States',
		"ZZ" => 'Unknown or Invalid Region',
		"UY" => 'Uruguay',
		"UZ" => 'Uzbekistan',
		"VU" => 'Vanuatu',
		"VA" => 'Vatican City',
		"VE" => 'Venezuela',
		"VN" => 'Vietnam',
		"WK" => 'Wake Island',
		"WF" => 'Wallis and Futuna',
		"EH" => 'Western Sahara',
		"YE" => 'Yemen',
		"ZM" => 'Zambia',
		"ZW" => 'Zimbabwe',
		"AX" => 'Åland Islands',
	);

    return ($country == '') ? $countries : (isset($countries[$country]) ? $countries[$country] : '');
}

function waapico_register_form()
{
    global $waapico_plugin_domn, $woocommerce;
    $country_code = sanitize_text_field($_POST['country_code']) ?: (class_exists('WC_Countries') ? WC_Countries::get_base_country() : 'IN');
    $phone_number = waapico_sanitize_data($_POST['phone_number']);
    $registration_otp = waapico_sanitize_data($_POST['registration_otp']);
    ?>
        <style>#su_send_otp_link{float:right;display:none;font-size:large;font-weight:bold}#su_send_otp_link::placeholder{text-align:right}</style>
        <p class="message" id="su_register_msg">OTP will be sent to your phone number.</p>
        <p>
            <label for="country_code"><?php _e('Country', $waapico_plugin_domn) ?><br />
                <select name="country_code" id="country_code" class="input">
                <?php foreach(waapico_country_name() as $code => $name) {
                    echo "<option value='$code' ", selected($country_code, $code), ">$name</option>";
                } ?>
                </select>
            </label>
        </p>
        <p>
            <label for="phone_number"><?php _e('Phone Number', $waapico_plugin_domn) ?><br />
                <input type="text" name="phone_number" id="phone_number" class="input" value="<?php echo esc_attr($phone_number); ?>" size="20" placeholder="Phone Number"/>
            </label>
        </p>
        <p>
            <label for="registration_otp"><?php _e('Registration OTP', $waapico_plugin_domn) ?> <a id="su_send_otp_link"><?php _e('Send OTP', $waapico_plugin_domn)?></a><br />
                <input type="text" name="registration_otp" id="registration_otp" class="input" value="<?php echo esc_attr($registration_otp); ?>" size="25" placeholder="Click on Send OTP link &uarr;"/>
            </label>
        </p>
        <script>
        document.querySelector("input#phone_number").onchange = function(){
            document.querySelector("a#su_send_otp_link").style.display = this.value.trim() == "" ? 'none' : 'inline';
        };
        document.querySelector("a#su_send_otp_link").onclick = function(){
            var request = new XMLHttpRequest(),
                url = '<?php echo admin_url("admin-ajax.php?action=waapico_reg_otp"); ?>' + '&country=' + document.querySelector("select#country_code").value + '&phone=' + document.querySelector("input#phone_number").value;
            this.innerHTML = 'Re-send OTP';
            this.style.display = 'none';
            setTimeout(function() {
                document.querySelector("a#su_send_otp_link").style.display = 'inline';
            }, 30000);
            request.open('POST', url, true);
            request.onload = function() {
                if (request.status == 200) {
                    var response = request.responseText;
                    console.log('Response', response);
                    var jsonobj = JSON.parse(response);
                    document.querySelector("p#su_register_msg").innerHTML = jsonobj.message;
                }
            };
            request.send();
        };
        </script>
    <?php
}

//OTP Ajax
add_action('wp_ajax_nopriv_waapico_reg_otp', 'waapico_reg_otp_callback');
function waapico_reg_otp_callback()
{
    if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'waapico_reg_otp') {
        $data = ['error' => true, 'message' => 'Failed to send OTP. Ensure that you have included the ISD code in the number.'];
        $country_code = sanitize_text_field($_REQUEST['country']);
        $billing_phone = waapico_sanitize_data($_REQUEST['phone']);
        if (!empty($country_code) && !empty($billing_phone)) {
            $user_phone = waapico_country_prefix($country_code) . $billing_phone;
            $user_id = waapico_get_user_by_phone($user_phone);
            if (!empty($user_id)) {
                $data['message'] = 'This phone number is linked to an already registered user account.';
            } else {
                $transient_id = 'OTP_REG_' . $country_code . '_' . $billing_phone;
                $otp_number = get_transient($transient_id);
                if ($otp_number == false) {
                    $otp_number = waapico_generate_otp();
                    set_transient($transient_id, $otp_number, 120);
                }
                $message = waapico_process_variables(waapico_fetch_string('msg_otp_register'), null, ['otp' => $otp_number]);
                waapico_send_otp($user_phone, $message);
                $data = ['success' => true, 'message' => "Registraion OTP has been sent to $user_phone"];
            }
        }
        wp_send_json($data);
    }
    die();
}


function waapico_registration_errors($errors, $username, $user_email)
{
    global $waapico_plugin_domn;
    $country_code = sanitize_text_field($_POST['country_code']);
    $phone_number = waapico_sanitize_data($_POST['phone_number']);
    $registration_otp = waapico_sanitize_data($_POST['registration_otp']);

    if (empty($country_code)) {
        $errors->add('country_code_error', __('Country name is required.', $waapico_plugin_domn));
    }

    if (empty($phone_number)) {
        $errors->add('phone_number_error', __('Numeric Phone Number is required.', $waapico_plugin_domn));
    }

    if (!empty($country_code) && !empty($phone_number)) {
        $user_phone = waapico_country_prefix($country_code) . $phone_number;
        $billing_phone_otp = 'OTP_REG_' . $country_code . '_' . $phone_number;
        $stored_phone_otp = get_transient($billing_phone_otp);
        $registration_otp = waapico_sanitize_data($_POST['registration_otp']);
        if (empty($registration_otp)) {
            $errors->add('registration_otp_error', __('Registration OTP is required.', $waapico_plugin_domn));
        } elseif ($registration_otp !== $stored_phone_otp) {
            $errors->add('registration_otp_error', __('Registration OTP is invalid.', $waapico_plugin_domn));
        }
    }

    return $errors;
}

function waapico_wc_registration_errors($username, $email, $errors)
{
    waapico_registration_errors($errors, $username, $user_email);
}

function waapico_user_register($user_id)
{
    $country_code = sanitize_text_field($_POST['country_code']);
    $phone_number = waapico_sanitize_data($_POST['phone_number']);
    if (!empty($country_code) && !empty($phone_number)) {
        $billing_phone = waapico_country_prefix($country_code) . $phone_number;
        $billing_phone_otp = 'OTP_REG_' . $country_code . '_' . $phone_number;
        delete_transient($billing_phone_otp);
        update_user_meta($user_id, 'billing_phone', $billing_phone);
        update_user_meta($user_id, 'billing_country', $country_code);
    }
}

/**
 * User login through OTP
 */

add_shortcode('waapico_otp_login', 'waapico_otp_login');
function waapico_otp_login($atts, $content = null)
{
    ob_start();
    $country_code = sanitize_text_field($_POST['country_code']) ?: (class_exists('WC_Countries') ? WC_Countries::get_base_country() : 'IN');
    $phone_number = waapico_sanitize_data($_POST['phone_number']);
    $login_otp = waapico_sanitize_data($_POST['login_otp']);
    ?>
<div id="waapico-otp-login-form">
    <div class='waapico-notifications'>
        <div class="woocommerce-info">
        An OTP will be sent to your registered mobile no. You will be logged-in upon completion of OTP verification.
        </div>
    </div>
    <div class="woocommerce-form">
        <p>
            <label for="waapico-phone-number"><?php _e('Phone Number', $waapico_plugin_domn) ?>
                <select name="country_code" id="waapico-country-code" class="input">
                <?php foreach(waapico_country_name() as $code => $name) {
                    echo "<option value='$code' ", selected($country_code, $code), ">$name</option>";
                } ?>
                </select>
                <input type="text" id="waapico-phone-number" class="input" value="<?php echo esc_attr($phone_number); ?>" size="25"/>
                <a class="button" id="waapico_resend_otp_btn">Send OTP</a>
            </label>
        </p>
        <p class="otp_block">
            <label for="waapico-otp-field"><?php _e('OTP', $waapico_plugin_domn) ?>
                <input type="text" id="waapico-otp-field" class="input" value="<?php echo esc_attr($login_otp); ?>" size="25"/>
                <a class="button" id="waapico_verify_otp_btn">Verify & Login</a>
            </label>
        </p>
    </div>
</div>
<script type="text/javascript">
    var otp_failure_count = 0,
        otp_resend_count = 0;
    function showSpinner() {
        document.querySelector('.waapico-notifications').innerHTML = '<center><img src="<?= admin_url("images/spinner-2x.gif") ?>"/></center>';
    }
    function process_json_response(response) {
        var jsonobj = JSON.parse(response);
        if (jsonobj.error) {
            document.querySelector('.waapico-notifications').innerHTML = '<div class="woocommerce-error">'+jsonobj.message+'</div>';
            if (jsonobj.verification_failure) {
                otp_failure_count++;
                if (otp_failure_count > 3) {
                    document.querySelector('.waapico-notifications').innerHTML += '<br/><h3>It seems that there is some difficulty in logging you in. Please try again later.</h3>';
                }
            }
        } else {
            if (jsonobj.otp_verified) {
                // window.location.reload();
                window.location = '<?= esc_url(home_url("/")) ?>';
            } else {
                document.querySelector('.waapico-notifications').innerHTML = '<div class="woocommerce-message">'+jsonobj.message+'</div>';
                otp_resend_count++;
            }
        }
    }
    function waapico_make_ajax_post(data) {
        var request = new XMLHttpRequest();
        request.open('POST', '<?php echo admin_url("admin-ajax.php"); ?>', true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;');
        request.onload = function() {
            if (request.status == 200) {
                process_json_response(request.responseText);
            }
        };
        request.send(data);
    }
    function waapico_verify_otp() {
        var country = document.getElementById('waapico-country-code').value;
        var phone = document.getElementById('waapico-phone-number').value;
        var otp = document.getElementById('waapico-otp-field').value;
        if (country.trim() == '') {
            document.querySelector('.waapico-notifications').innerHTML = 'Please select your country.';
            return;
        }
        if (phone.trim() == '') {
            document.querySelector('.waapico-notifications').innerHTML = 'Please enter the registered phone number.';
            return;
        }
        if (otp.trim() == '') {
            document.querySelector('.waapico-notifications').innerHTML = 'Please enter a valid OTP.';
            return;
        }
        showSpinner();
        waapico_make_ajax_post("action=waapico_verify_otp_login&country="+country+"&phone="+phone+"&otp="+otp);
    }
    function waapico_resend_otp() {
        var country = document.getElementById('waapico-country-code').value;
        var phone = document.getElementById('waapico-phone-number').value;
        if (country.trim() == '') {
            document.querySelector('.waapico-notifications').innerHTML = 'Please select your country.';
            return;
        }
        if (phone.trim() == '') {
            document.querySelector('.waapico-notifications').innerHTML = 'Please enter the registered phone number.';
            return;
        }
        disableResendOTP();
        showSpinner();
        waapico_make_ajax_post("action=waapico_send_otp_login&country="+country+"&phone="+phone)
    }
    function enableResendOTP() {
        if (otp_resend_count < 3) {
            document.querySelector('#waapico_resend_otp_btn').text = 'Resend OTP';
            document.querySelector('#waapico_resend_otp_btn').style.visibility = 'visible';
        }
    }
    function disableResendOTP() {
        document.querySelector('#waapico_resend_otp_btn').style.visibility = 'hidden';
        setTimeout(enableResendOTP, 30000);
    }
    document.querySelector('#waapico_resend_otp_btn').addEventListener('click', waapico_resend_otp);
    document.querySelector('#waapico_verify_otp_btn').addEventListener('click', waapico_verify_otp);
</script>
<?php
return ob_get_clean();
}

function waapico_get_user_by_phone($phone_number)
{
    return reset(
        get_users(
            array(
                'meta_key' => 'billing_phone',
                'meta_value' => $phone_number,
                'number' => 1,
                'fields' => 'ids',
                'count_total' => false
            )
        )
    );
}

//Request OTP via AJAX
add_action('wp_ajax_nopriv_waapico_send_otp_login', 'waapico_send_otp_login_callback');
function waapico_send_otp_login_callback()
{
    if (isset($_POST['action']) && $_POST['action'] == 'waapico_send_otp_login') {
        $data = ['error' => true, 'message' => 'Failed to send OTP. Ensure that you have included the ISD code in the number.'];
        $country_code = sanitize_text_field($_POST['country']);
        $billing_phone = waapico_sanitize_data($_POST['phone']);
        if (!empty($country_code) && !empty($billing_phone)) {
            $billing_phone = waapico_country_prefix($country_code) . $billing_phone;
            $user_id = waapico_get_user_by_phone($billing_phone);
            if (!empty($user_id)) {
                $transient_id = 'OTP_LOGIN_' . $user_id;
                $otp_number = get_transient($transient_id);
                if ($otp_number == false) {
                    $otp_number = waapico_generate_otp();
                    set_transient($transient_id, $otp_number, 120);
                }
                $message = waapico_process_variables(waapico_fetch_string('msg_otp_login'), null, ['otp' => $otp_number]);
                waapico_send_otp($billing_phone, $message);
                $data = ['success' => true, 'message' => "OTP Sent to $billing_phone for login"];
            }
        }
        wp_send_json($data);
    }
    die();
}

add_action('wp_ajax_nopriv_waapico_verify_otp_login', 'waapico_verify_otp_login_callback');
function waapico_verify_otp_login_callback()
{
    if (isset($_POST['action']) && $_POST['action'] == 'waapico_verify_otp_login') {
        $data = ['error' => true, 'message' => 'OTP could not be verified', 'verification_failure' => true];
        $country_code = sanitize_text_field($_POST['country']);
        $billing_phone = waapico_sanitize_data($_POST['phone']);
        $user_otp = waapico_sanitize_data($_POST['otp']);
        if (!empty($country_code) && !empty($billing_phone) && !empty($user_otp)) {
            $billing_phone = waapico_country_prefix($country_code) . $billing_phone;
            $user_id = waapico_get_user_by_phone($billing_phone);
            if (!empty($user_id)) {
                $transient_id = 'OTP_LOGIN_' . $user_id;
                $otp_number = get_transient($transient_id);
                if ($otp_number == $user_otp) {
                    delete_transient($transient_id);
                    wp_clear_auth_cookie();
                    wp_set_current_user($user_id);
                    wp_set_auth_cookie($user_id);
                    $data = ['success' => true, 'message' => "Congrats! Your login is successful.", 'otp_verified' => true];
                }
            }
        }
        wp_send_json($data);
    }
    die();
}

// Add link on default login form
if (waapico_field('otp_user_log') == 1)
    add_action('login_form', 'waapico_disply_otp_login_option');
function waapico_disply_otp_login_option()
{
    ?>
    <p><a href="#waapico-login-form-popup">Login with OTP</a></p>
    <style>#waapico-login-form-popup{background:rgba(0,0,0,.5);position:absolute;top:0;left:0;width:100vw;height:100vh;overflow:hidden;display:none}#waapico-login-form-popup:target{display:flex;justify-content:center;align-items:center}#waapico-login-form-popup .close_btn{position:absolute;text-decoration:none;top:1vh;right:1vw;color:#fff;font-size:3em}#waapico-otp-login-form{background:#fff;min-width:50%;max-width:90%;padding:5%}</style>
    <div id="waapico-login-form-popup">
        <?= do_shortcode('[waapico_otp_login]') ?>
        <a href="#" class="close_btn">&times;</a>
    </div>
<?php

}