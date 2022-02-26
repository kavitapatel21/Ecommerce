<?php 
/* Child theme generated with WPS Child Theme Generator */
            
if ( ! function_exists( 'b7ectg_theme_enqueue_styles' ) ) {            
    add_action( 'wp_enqueue_scripts', 'b7ectg_theme_enqueue_styles' );
    
    function b7ectg_theme_enqueue_styles() {
        wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
        wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'parent-style' ) );
    }
}

// Submit Data contact us page
add_action('wp_ajax_data_submit' , 'data_submit');
add_action('wp_ajax_nopriv_data_submit','data_submit');
function data_submit()
{
global $wpdb;
$name = $_POST['name'];
$email = $_POST['email'];
$message = $_POST['message'];
$wpdb->insert("wp_formentry", array(
   "name" => $name,
   "email" => $email,
   "message" => $message,
));
wp_die();
}

/* valid form contact us page*/
function twentytwentyone_add_child_class() {
	?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>	
	<script>
	jQuery(document).ready(function () {
		jQuery("#contact").validate({
	        rules: {
	            name: {
	                required:true,
	                maxlength: 20,
	            },
	            email: {
	                required:true,
	                email: true,
					remote: {
           			url: '<?php echo get_stylesheet_directory_uri();?>/template/email.php',
            		type: "post",
       						}
	            },
	        },
			messages: {
            name: "Please enter your fullname",
            email: {
		    required: "Please enter your email address.",
                    email: "Please enter a valid email address.",
                    remote: "email is already exist."
                 },
     },
	    });
		jQuery('#form-submit').click( function()
    {			
		 	var valid = jQuery("#contact").valid();
            var name = jQuery("#name").val();
		 	var email = jQuery("#email").val();
            var message= jQuery("#message").val();
		 	//console.log(valid);
		 	if (valid === true) {		 		
			    jQuery.ajax({
			        url:'<?php echo admin_url( 'admin-ajax.php' ); ?>',
			        type: 'POST',
			        dataType: 'json',	        
			        data:{
			        	action: 'data_submit',
			        	name : name,
						email : email,
						message : message,
			        }, 
			        complete: function () {
						//jQuery('.success').text('Your message was sent successfully');	
                        $('#success').html("<div>Your message was sent successfully</div>").delay(1000).fadeOut(1000);		
					}
			    });
		 	}
		});
	});
	</script>
	<?php
}
add_action( 'wp_footer', 'twentytwentyone_add_child_class' );

/*custom logo*/
add_theme_support( 'custom-logo' );


/*custom favicon*/
function kia_add_favicon(){ ?>
    <!-- Custom Favicons -->
    <link rel="shortcut icon" href="/favicon-32x32.png"/>
    <?php }
add_action('wp_head','kia_add_favicon');

function reigel_woocommerce_checkout_fields( $checkout_fields = array() ) {

    $checkout_fields['order']['date_of_birth'] = array(
        'type'          => 'text',
        'class'         => array('my-field-class form-row-wide'),
        'label'         => __('Date of Birth'),
        'placeholder'   => __('dd/mm/yyyy'),
        'required'      => true, 
        );

    return $checkout_fields;
}
add_filter( 'woocommerce_checkout_fields', 'reigel_woocommerce_checkout_fields' );
function reigel_woocommerce_checkout_update_user_meta( $customer_id, $posted ) {
    if (isset($posted['date_of_birth'])) {
        $dob = sanitize_text_field( $posted['date_of_birth'] );
        update_user_meta( $customer_id, 'date_of_birth', $dob);
    }
}
add_action( 'woocommerce_checkout_update_user_meta', 'reigel_woocommerce_checkout_update_user_meta', 10, 2 );

//cron file
function get_birthdate( ) {
    
    date_default_timezone_set('America/New_York');
    $d=date('d-m');
    global $wpdb;
    //$post_id = $wpdb->get_results("SELECT * FROM wp_usermeta WHERE ( DATE_FORMAT(meta_value,'%d-%m') = DATE_FORMAT('$d','%d-%m')");
   // $post_id = $wpdb->get_results("SELECT DATE(meta_value) as date, MONTH(meta_value) as month from wp_usermeta where meta_value = date('d-m')");
   $post_id = $wpdb->get_results("SELECT * FROM wp_usermeta WHERE (date(meta_value,'d-m') = date('d-m'))");
    echo $wpdb->last_result;
    $to = 'kavita.patel@plutustec.com';
    $subject = 'The subject';
    $body = 'The email body content';
    $headers = array('Content-Type: text/html; charset=UTF-8');
    wp_mail( $to, $subject, $body, $headers );
 }
 add_action( 'init', 'get_birthdate' );

 
