<?php
/*
Template Name: checkout
Template Post Type: post, page, my-post-type;
*/
get_header();
?>


      	<div class="container">
        	<div class="row">
         		
			  			<?php echo do_shortcode('[woocommerce_checkout]'); ?>
        	</div>
        </div>

	
	
	
<?php
get_footer();
?>