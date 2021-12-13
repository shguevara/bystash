<?php
global $post, $product, $woocommerce_loop;

// Get Type.
$type             = get_theme_mod( 'related_products', 'slider' );
$repeater_classes = array();

if ( $type == 'hidden' ) return;
if ( $type == 'grid' ) $type = 'row';

 if ( get_theme_mod('category_force_image_height' ) ) $repeater_classes[] = 'has-equal-box-heights';
 if ( get_theme_mod('equalize_product_box' ) ) $repeater_classes[] = 'equalize-box';

$repater['type']         = $type;
$repater['columns']      = get_theme_mod( 'related_products_pr_row', 5 );
$repater['columns__md']  = get_theme_mod( 'related_products_pr_row_tablet', 3 );
$repater['columns__sm']  = get_theme_mod( 'related_products_pr_row_mobile', 2 );
$repater['class']        = implode( ' ', $repeater_classes );
$repater['slider_style'] = 'reveal';
$repater['row_spacing']  = 'small';

$label = __( 'Customers also purchased these products', 'wc_recommender' );
$label = get_option( 'wc_recommender_label_rbph', $label );


$simularity_scores = woocommerce_recommender_get_simularity( $product->get_id(), $activity_types );
$related           = array();

if ( $simularity_scores ) {
	$related = array_keys( $simularity_scores );
}

if ( sizeof( $related ) == 0 ) {
	return;
}

$args = apply_filters( 'woocommerce_related_products_args', array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => 1,
	'posts_per_page'      => - 1,
	'orderby'             => $orderby,
	'post__in'            => $related,
	'include'             => $related,
) );


$woocommerce_loop['columns'] = $columns;

$products = get_posts( $args );

if ( $products && is_array( $products ) && count( $products ) ) :
	woocommerce_recommender_sort_posts( $products, $simularity_scores );

	if ( $posts_per_page ) {
		$parts = array_chunk( $products, $posts_per_page );
		$products = $parts[0];
	}
	?>

	<div style="clear:both;"></div>
	<div class="related related-products-wrapper product-section products-customers-purchased-others">

	<?php
		$heading = apply_filters( 'woocommerce_recommendation_engine_label_purchased_together', $label );

		if ( $heading ) :
			?>
			<h3 class="product-section-title container-width product-section-title-related pt-half pb-half uppercase">
				<?php echo esc_html( $heading ); ?>
			</h3>
	<?php endif; ?>	


	<?php get_flatsome_repeater_start( $repater ); ?>


		<?php
		foreach ( $products as $post ) :
			setup_postdata( $post );
			?>
			<?php wc_get_template_part( 'content', 'product' ); ?>
		<?php endforeach; // end of the loop.   ?>
		<?php wp_reset_postdata(); ?>

	<?php get_flatsome_repeater_end( $repater ); ?>			

    </div>
	<?php
endif;
wp_reset_postdata();
?>