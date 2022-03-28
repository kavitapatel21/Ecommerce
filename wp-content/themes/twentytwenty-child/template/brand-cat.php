<?php
/*
Template Name: brand-cat
Template Post Type: post, page, my-post-type;
*/
get_header();
?>
<?php
function get_request_param( $key = '' ) {
    $value = false;

    if ( ! $key ) {
        return $value;
    }

    if ( isset( $_POST[$key] ) ) {
        $value = $_POST[$key];
    } elseif ( isset( $_GET[$key] ) ) {
        $value = $_GET[$key];
    }

    return $value;
}

$args = array(
    'post_type'      => 'custom_products',
    'posts_per_page' => -1,
);

$tax_query  = array();
$categories = get_terms( 'category', 'orderby=name' );

if ( ! empty( $choices = get_request_param( 'choices' ) ) ) {
    $term_ids = explode(',', $choices);

    $tax_query[] = array(
        'taxonomy' => 'category',
        'field'    => 'term_id',
        'terms'    => $term_ids
    );

    $args['tax_query'] = $tax_query;
}

$query = new WP_Query( $args );
?>
<div  style="padding-top: 100px;" >
<?php
if ( ! empty( $categories ) ) : ?>
    <form action="?" method="post" class="form-filter">
        <?php foreach ( $categories as $category ) : ?>
            <div class="checkbox">
                <input type="checkbox" name="category[]" data-category="<?php echo esc_attr( $category->term_id ); ?>" id="<?php echo esc_attr( $category->slug ); ?>">

                <label for="<?php echo esc_attr( $category->slug ); ?>">
                    <?php echo esc_html( $category->name ); ?>
                </label>
            </div><!-- /.checkbox -->
        <?php endforeach; ?>
    </form><!-- /.form-filter -->
<?php endif; ?>
</div>
<div class="container" style="padding-top: 50px;" >
  <div class="row">
        <?php while ( $query->have_posts() ) : $query->the_post(); ?>
        <div class="col-md-3">
  <?php $url = wp_get_attachment_url( get_post_thumbnail_id($post->ID), 'thumbnail' ); ?>  
  <img src="<?php echo $url ?>" width="200" height="200" style="padding-top: 20px;" alt=""/>
  <h2 style="padding-top: 5px;text-align:center;"><?php the_title(); ?></h2>
  </div> 
        <?php endwhile; ?>
        </div>
</div>

<script>
    ;(function(window, document, $) {
    var $win = $(window);
    var $doc = $(document);

    $doc.on('change', '.form-filter', function() {
        var choices = '';

        $('.form-filter input:checked').each(function() {
            if ( choices === '' ) {
                choices += $(this).data('category');
            } else {
                choices += ',' + $(this).data('category');
            }
        });

        $.ajax({
            url: window.location.href,
            type: 'GET',
            data: {
                'choices' : choices,
            },
            success: function(response) {
                var newPosts = $(response).filter('.filter-output').html();
                $('.filter-output').html(newPosts);
            }
        });
    });
})(window, document, window.jQuery);
        </script>
<?php
get_footer();
?>