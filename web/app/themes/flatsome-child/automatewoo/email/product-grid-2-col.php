<?php
// phpcs:ignoreFile

namespace AutomateWoo;

/**
 * Products items list
 *
 * Override this template by copying it to yourtheme/automatewoo/email/product-grid-2-col.php
 *
 * @see https://automatewoo.com/docs/email/product-display-templates/
 *
 * @var \WC_Product[] $products
 * @var Workflow $workflow
 * @var string $variable_name
 * @var string $data_type
 * @var string $data_field
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$n = 1;

?>

	<?php if ( is_array( $products ) ): ?>

		<style>
			/** don't inline this css - hack for gmail */
			.aw-product-grid .aw-product-grid-item-2-col img {
				height: auto !important;
			}
		</style>

		<table cellspacing="0" cellpadding="0" class="aw-product-grid">
			<tbody><tr><td style="padding: 0;"><div class="aw-product-grid-container">

					<?php foreach ( $products as $product ): ?>

						<div class="aw-product-grid-item-2-col" style="<?php echo ( $n % 2 ? '' : 'margin-right: 0;' ) ?>">

							<a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo \AW_Mailer_API::get_product_image( $product, 'shop_thumbnail' ) ?></a>
							<h3><a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>

							<span class="price"><?php if( $product->is_on_sale() ) { ?> <del><span class="woocommerce-price-sale "><bdi><span class="currency-symbol">$</span><?php echo round($product->get_sale_price()) ?></bdi></span></del> <?php } ?>
							<ins><span class="woocommerce-price-regular"><bdi><span class="currency-symbol">$</span><?php echo $product->get_regular_price() ?></bdi></span></ins></span>

						</div>

						<?php $n++; endforeach; ?>

				</div></td></tr></tbody>
		</table>


	<?php endif; ?>