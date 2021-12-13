<?php
/**
 * Order Downloads.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-downloads.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// $prev_product will be used to generate a different class name  
$prev_product = '';

?>
<section class="woocommerce-order-downloads">	
	<?php if ( isset( $show_title ) ) : ?>
		<h2 class="woocommerce-order-downloads__title"><?php esc_html_e( 'Downloads', 'woocommerce' ); ?></h2>
	<?php endif; ?>

	<table class="woocommerce-table woocommerce-table--order-downloads shop_table shop_table_responsive order_details">
		<thead>
			<tr>
				<?php foreach ( wc_get_account_downloads_columns() as $column_id => $column_name ) : ?>
				<th <?php if ( $column_id == 'download-product')  echo 'width="55%"'  ?> class="<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
				<?php endforeach; ?>
			</tr>
		</thead>

		<?php foreach ( $downloads as $download ) : ?>

			<?php
				$product_image = wp_get_attachment_image_src( get_post_thumbnail_id( $download['product_id'] ), 'thumbnail' );
			?>
			
			<tr <?php if ( $download['product_name'] == $prev_product ) {  } else { echo 'class="first"'; } ?> >

				<?php foreach ( wc_get_account_downloads_columns() as $column_id => $column_name ) : ?>
					<td class="<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">

						<?php
						if ( has_action( 'woocommerce_account_downloads_column_' . $column_id ) ) {
							do_action( 'woocommerce_account_downloads_column_' . $column_id, $download );
						} else {
							switch ( $column_id ) {
								case 'download-product':
									if ( $download['product_url'] && ( $prev_product != $download['product_name'] ) ) {
										echo '<a target="_blank" class="product-title" href="' . esc_url( $download['product_url'] ) . '">' . esc_html( $download['product_name'] ) . '</a><img class="product-image "src="'. $product_image[0] .'" />';
									} else {
										if ( $prev_product != $download['product_name'] ) {
											echo esc_html( $download['product_name'] );
										}
									}
									break;
								case 'download-file':
									echo '<a target="_blank" href="' . esc_url( $download['download_url'] ) . '" class="woocommerce-MyAccount-downloads-file button alt">' . esc_html( $download['download_name'] ) . '</a>';
									break;
								case 'download-remaining':
									echo is_numeric( $download['downloads_remaining'] ) ? esc_html( $download['downloads_remaining'] ) : esc_html__( '&infin;', 'woocommerce' );
									break;
								case 'download-expires':
									if ( ! empty( $download['access_expires'] ) ) {
										echo '<time datetime="' . esc_attr( date( 'Y-m-d', strtotime( $download['access_expires'] ) ) ) . '" title="' . esc_attr( strtotime( $download['access_expires'] ) ) . '">' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $download['access_expires'] ) ) ) . '</time>';
									} else {
										esc_html_e( 'Never', 'woocommerce' );
									}
									break;
							}
						}
						?>
					</td>
					<?php
						  $prev_product = $download['product_name'];
					?>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
		<tr>
			<td colspan="4" class="download-note"><strong>Nota:</strong> Para completar el proceso de descarga por favor asegurate de solicitar acceso en Google Drive. Si tenés más de un archivo repetí el mismo procedimiento.</td>
		</tr>
	</table>
</section>
