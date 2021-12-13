<?php
// phpcs:ignoreFile

namespace AutomateWoo;

/**
 * Products items list
 *
 * Override this template by copying it to yourtheme/automatewoo/email/product-rows.php
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

?>

<?php if ( is_array( $products ) ): ?>

	<table cellspacing="0" cellpadding="0" style="width: 100%;" class="aw-product-rows">
		<tbody>

		<?php
			$total_savings = 0;
			$total_regular_price = 0;
		?>

		<?php foreach ( $products as $product ):

			$regular_price = round($product->get_regular_price());

			if( $product->is_on_sale() ) {
					$sale_price = round($product->get_sale_price());
					$savings = round($regular_price - $sale_price);
					$total_savings = $total_savings + $savings;
					$total_regular_price = $total_regular_price + $regular_price;					
				}
			?>
		
			<tr>
				<td class="image-container" width="38%">
					<a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo \AW_Mailer_API::get_product_image( $product ) ?></a>
				</td>
				<td class="title-container">
					<a class="product-title" href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
					<p class="price">
						<?php if( $product->is_on_sale() ) { ?><del><span  class="regular-price"><span class="currency-symbol">$</span><?php echo $regular_price ?></span></del> <?php } ?>
						<span class="sale-price"><span class="currency-symbol">$</span><?php if( $product->is_on_sale() ) { echo $sale_price; } else { echo $regular_price;} ?></span>
					</p>
					<?php if( $product->is_on_sale() ) { ?><div class="savings">(Ahorras <span class="savings-currency">$<?php echo $savings ?>!</span>)</div><?php } ?>
				</td>
			</tr>
		<?php endforeach; ?>

		<?php if ( count( $products ) > 1 ) { ?>
			<tr>
				<td colspan="3" align="center" class="total-savings-container"><span class="savings">Ahorras un total de <strong>$<?php echo $total_savings ?>!</strong></span></td>
			</tr>
		<?php } ?>
		

	</tbody></table>

<?php endif; ?>