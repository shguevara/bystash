<?php
/*
Template name: Page - Best Sellers / Full Width
*/
get_header(); ?>

<?php do_action('flatsome_before_page'); ?>

<div id="content" role="main" class="content-area">

	<?php
	$args = array(
		'post_type' => 'product',
		'meta_key' => 'total_sales',
		'orderby' => 'meta_value_num',
		'posts_per_page' => 10,
	);
	$loop = new WP_Query($args);
	while ($loop->have_posts()) : $loop->the_post();
		global $product; ?>
		<div>
			<a href="<?php the_permalink(); ?>" id="id-<?php the_id(); ?>" title="<?php the_title(); ?>">

				<?php if (has_post_thumbnail($loop->post->ID))
					echo get_the_post_thumbnail($loop->post->ID, 'shop_catalog');
				else echo '<img src="' . woocommerce_placeholder_img_src() . '" alt="product placeholder Image" width="65px" height="115px" />'; ?>

				<h3><?php the_title(); ?></h3>
			</a>
		</div>
	<?php endwhile; ?>
	<?php wp_reset_query(); ?>

</div>

<?php do_action('flatsome_after_page'); ?>

<?php get_footer(); ?>