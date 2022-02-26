<?php
/*
Template Name: my account
Template Post Type: post, page, my-post-type;
*/
get_header();
?>

  
      	<div class="container">
        	<div class="row">
            <div id="customer_login" style="margin-top:200px;">
			  			<?php echo do_shortcode('[woocommerce_my_account]'); ?>
        	</div>
        </div>
        </div>

	
	
<?php
get_footer();
?>