<?php
/*
Template Name: cart
Template Post Type: post, page, my-post-type;
*/
get_header();
?>


      	<div class="container">
        	<div class="row">
         		<div class="col-md-12">
            		<div class="section-heading">
              			<h2>Latest Products</h2>
              			<a href="<?php echo get_permalink( woocommerce_get_page_id( 'shop' ) ) . "?showall=1"; ?>">view all products <i class="fa fa-angle-right"></i></a>
            		</div>
            	</div>
			  			<?php echo do_shortcode('[woocommerce_cart]'); ?>
        	</div>
        </div>

	
	
	
<?php
get_footer();
?>