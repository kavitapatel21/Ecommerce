<?php
/*
Template Name: order tracking
Template Post Type: post, page, my-post-type;
*/
get_header();
?>


      	<div class="container">
        	<div class="row">
				<div class="woocommerce" style="margin-top:200px;>
         		
			  			<?php echo do_shortcode('[woocommerce_order_tracking]'); ?>
				</div>
        	</div>
        </div>

	
	
	
<?php
get_footer();
?>