<?php 
/* Child theme generated with WPS Child Theme Generator */
            
if ( ! function_exists( 'b7ectg_theme_enqueue_styles' ) ) {            
    add_action( 'wp_enqueue_scripts', 'b7ectg_theme_enqueue_styles' );
    
    function b7ectg_theme_enqueue_styles() {
        wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
        wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'parent-style' ) );
    }
}

//require_once("wp-config.php");
//require_once("wp-load.php");
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
        'placeholder'   => __('yyyy-mm-dd'),
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

add_action( 'phpmailer_init', 'setup_phpmailer_init' );
function setup_phpmailer_init( $phpmailer ) {
    $phpmailer->Host = 'smtp.gmail.com'; // for example, smtp.mailtrap.io
    $phpmailer->Port = 465; // set the appropriate port: 465, 2525, etc.
    $phpmailer->Username = 'kavita.patel@plutustec.com'; // your SMTP username
    $phpmailer->Password = 'dkhbr@21'; // your SMTP password
    $phpmailer->SMTPAuth = 'true';
    $phpmailer->SMTPSecure = 'ssl'; // preferable but optional
    $phpmailer->IsSMTP();
}

//cron file
add_filter( 'cron_schedules', 'example_add_cron_interval' );

function example_add_cron_interval( $schedules ) {
	$schedules['sixty_seconds'] = array(
	'interval' => 86400, //[interval] => 86400 once a daily
	'display' => esc_html__( 'Every one minute' ),
	);
   
   return $schedules;
	}

 add_action( 'wp', 'launch_the_action' );
 function launch_the_action(){
	if (!wp_next_scheduled("custom__cron")) {
        wp_schedule_event(time(), "sixty_seconds", "custom__cron");
    }
 }

add_action('custom__cron','get_birthdate');
function get_birthdate() {
	global $wpdb;
	$post_id = $wpdb->get_results("SELECT * FROM wp_usermeta WHERE meta_key = 'date_of_birth'");
	//echo $wpdb->last_query;
	$array = json_decode(json_encode($post_id), true);
	//echo "<pre>";
	//print_r($array);
	foreach ($array as $arr){
		 $result=$arr['meta_value'];
		 $id=$arr['user_id'];
		//echo $id;
		//echo '<br>';
		//echo $result;
		//echo '<br>';
	}
		
		$post= $wpdb->get_results("SELECT email FROM wp_wc_customer_lookup WHERE user_id=$id");
		$email = json_decode(json_encode($post), true);
		//echo "<pre>";
		//print_r($email);
		foreach ($email as $a){
			$mail=$a['email'];
			//echo $mail;
			//echo '<br>';
		}
		$date = new DateTime($result);
		$b= $date->format('m-d'); 
		//echo $b;
		if($b==date('m-d'))
		{	
			$msg = "Hi There,
					Wish You a very happy birthday.On th occassion of your birthday we just want to give a discount coupon.
					The coupon code is '5AV8U4GK'.";
			// use wordwrap() if lines are longer than 70 characters
			$msg = wordwrap($msg,170);
			// send email
			$send=wp_mail("$mail","My subject",$msg);
			//echo "True";
			//echo "<br>";
			if($send)
			{
				//secho "send";
			}
			else{
				//echo "error";
			}
		}
	else{
		//echo "False";
	}      
}
 //add_action( 'init', 'get_birthdate' );


//offer popup
function wpb_demo_shortcode_2() { 
?>
<main>
    <div class="container-fluid bg-trasparent my-4 p-3" style="position: relative;">
        <div class="row row-cols-1 row-cols-xs-2 row-cols-sm-2 row-cols-lg-4 g-3">
		<?php
          $args = array( 'post_type' => 'offer_popup',
          'post_status' => 'publish',
          'posts_per_page' => -1,
          'order'    => 'ASC'); 
          $loop = new WP_Query( $args );
          while ( $loop->have_posts() ) : $loop->the_post(); 
      	?>
            <div class="col">
                <div class="card h-100 shadow-sm"> <img class="card-img-top" alt="" src="<?php echo the_post_thumbnail('thumbnail');?>
                    <div class="card-body">
                        <div class="clearfix mb-3"><h3><?php echo the_title();?></h3></div>
                        <h5 class="card-title"><?php echo the_content();?></h5>
                       <!-- <div class="d-grid gap-2 my-4"><a href="#" class="btn btn-warning">Check offer</a> </div>-->
                    </div>
                </div>
            </div>
			<?php
          	endwhile;
        	?>
        </div>
    </div>
</main>
<?php } ?>
<?php
add_shortcode('my_ad_code', 'wpb_demo_shortcode_2'); 
?>


<!--ajax registration form data mail-->
<?php
function invio_mail(){
	$subject='Registration Form';
	$fname=$_POST['firstname'];
	$lname=$_POST['lastname'];
	$dob=$_POST['birthdate'];
	//$radio=$_POST['inlineRadioOptions'];
	$phn=$_POST['phoneno'];
	//$comment=$_POST['comment'];
	$to = $_POST['email'];
	$body  = 'From: Sixteen clothing' ;
	$body .="<html>
	<body>
	<h3>Fistname: $fname</h3>
	<h3>Lastname: $lname</h3>
	<h3>DOB: $dob</h3>
	<h3>Phone No: $phn</h3>
	<body>
	</html>";
	 $headers = array('Content-Type: text/html; charset=UTF-8','From: kavita <kavita@plutustec.com>');
	 
	 if(isset($_POST['submit']))
	{
		wp_mail( $to, $subject, $body, $headers );
	}
}
add_action( 'wp_ajax_siteWideMessage', 'invio_mail' );
add_action( 'wp_ajax_nopriv_siteWideMessage', 'invio_mail' );
?>
<script>
 jQuery(document).ready(function() {   
    jQuery('#submit').click( function() {		
        var formData = {
            fname: jQuery('#firstName').val(),
            lname: jQuery('#lastName').val(),
			dob: jQuery('#birthdayDate').val(),
            email: jQuery('#emailAddress').val(),
			phno: jQuery('#phoneNumber').val(),
            action:'siteWideMessage'
        };
        $.ajax({
            type        : 'POST', 
            url         : "<?php echo admin_url('admin-ajax.php'); ?>",
            data        : formData,
            dataType    : 'json',
            encode      : true
        }).done(function(data) {
            console.log(data);        
        }).fail(function(data) {
            console.log(data);
        });
        e.preventDefault();     
    });

  });
</script>
<?php 
//ajax form data in mail
add_shortcode('Registration_form','contact_form_shortcode');
function contact_form_shortcode(){	
?>
<style>
	.gradient-custom {
  /* fallback for old browsers */
  background: #f093fb;

  /* Chrome 10-25, Safari 5.1-6 */
  background: -webkit-linear-gradient(to bottom right, rgba(240, 147, 251, 1), rgba(245, 87, 108, 1));

  /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
  background: linear-gradient(to bottom right, rgba(240, 147, 251, 1), rgba(245, 87, 108, 1))
}

.card-registration .select-input.form-control[readonly]:not([disabled]) {
  font-size: 1rem;
  line-height: 2.15;
  padding-left: .75em;
  padding-right: .75em;
}
.card-registration .select-arrow {
  top: 13px;
}
</style>

<section class="vh-100 gradient-custom">
  <div class="container py-5 h-100">
    <div class="row justify-content-center align-items-center h-100">
      <div class="col-12 col-lg-9 col-xl-7">
        <div class="card shadow-2-strong card-registration" style="border-radius: 15px;">
          <div class="card-body p-4 p-md-5">
            <h3 class="mb-4 pb-2 pb-md-0 mb-md-5">Registration Form</h3>
            <form method="POST" accept-charset="UTF-8" id="ajaxformid" name="frm">

              <div class="row">
                <div class="col-md-6 mb-4">

                  <div class="form-outline">
                    <input type="text" id="firstName" class="form-control form-control-lg" name="firstname" />
                    <label class="form-label" for="firstName">First Name</label>
                  </div>

                </div>
                <div class="col-md-6 mb-4">

                  <div class="form-outline">
                    <input type="text" id="lastName" class="form-control form-control-lg" name="lastname" />
                    <label class="form-label" for="lastName">Last Name</label>
                  </div>

                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-4 d-flex align-items-center">

                  <div class="form-outline datepicker w-100">
                    <input
                      type="text"
                      class="form-control form-control-lg"
                      id="birthdayDate"
					  name="birthdate"
                    />
                    <label for="birthdayDate" class="form-label">Birthday</label>
                  </div>

                </div>
                <div class="col-md-6 mb-4">

                  <h6 class="mb-2 pb-1">Gender: </h6>

                  <div class="form-check form-check-inline">
                    <input
                      class="form-check-input"
                      type="radio"
                      name="inlineRadioOptions"
                      id="femaleGender"
                      value="Female"
                      checked
                    />
                    <label class="form-check-label" for="femaleGender">Female</label>
                  </div>

                  <div class="form-check form-check-inline">
                    <input
                      class="form-check-input"
                      type="radio"
                      name="inlineRadioOptions"
                      id="maleGender"
                      value="Male"
                    />
                    <label class="form-check-label" for="maleGender">Male</label>
                  </div>

                  <div class="form-check form-check-inline">
                    <input
                      class="form-check-input"
                      type="radio"
                      name="inlineRadioOptions"
                      id="otherGender"
                      value="Other"
                    />
                    <label class="form-check-label" for="otherGender">Other</label>
                  </div>

                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-4 pb-2">

                  <div class="form-outline">
                    <input type="email" id="emailAddress" class="form-control form-control-lg" name="email" />
                    <label class="form-label" for="emailAddress">Email</label>
                  </div>

                </div>
                <div class="col-md-6 mb-4 pb-2">

                  <div class="form-outline">
                    <input type="tel" id="phoneNumber" class="form-control form-control-lg" name="phoneno"/>
                    <label class="form-label" for="phoneNumber">Phone Number</label>
                  </div>

                </div>
              </div>

             <!-- <div class="row">
                <div class="col-12">

                  <select class="select form-control-lg">
                    <option value="1" disabled>Choose option</option>
                    <option value="2">Subject 1</option>
                    <option value="3">Subject 2</option>
                    <option value="4">Subject 3</option>
                  </select>
                  <label class="form-label select-label">Choose option</label>

                </div>
              </div>-->

			  <div class="row">
                <div class="col-12">
			
				<textarea class="form-control form-control-lg" id="exampleFormControlTextarea1" rows="3" name="comment"></textarea>
				<label class="form-label" for="comment">Comment</label>
				
				</div>
			  </div>
			 
              <div class="mt-4 pt-2">
                <input class="btn btn-primary btn-lg" type="submit" value="Submit" name="submit" id="submit" class="submit" />
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  
</section>
<?php } ?>

