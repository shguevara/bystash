<?php
namespace Barn2\WLT_Lib\WooCommerce\Admin;

use Barn2\WLT_Lib\Registerable,
	WC_Admin_Settings;

/**
 * Additional field types for WooCommerce settings pages.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.2.5
 */
class Custom_Settings_Fields implements Registerable {

	private $fields_required;
	private $all_fields = [ 'hidden', 'color_size', 'help_note', 'multi_text', 'settings_start', 'settings_end', 'plugin_promo', 'checkbox_tooltip' ];

	public function __construct( array $fields_required = [] ) {
		if ( empty( $fields_required ) ) {
			$this->fields_required = $this->all_fields;
		} else {
			$this->fields_required = array_intersect( (array) $fields_required, $this->all_fields );
		}
	}

	public function register() {
		foreach ( $this->fields_required as $field ) {
			if ( ! has_action( "woocommerce_admin_field_{$field}" ) && method_exists( self::class, "{$field}_field" ) ) {
				add_action( "woocommerce_admin_field_{$field}", [ self::class, "{$field}_field" ] );
			}

			if ( $field === 'checkbox_tooltip' ) {
				add_filter( 'woocommerce_admin_settings_sanitize_option', [ self::class, 'sanitize_checkbox_tooltip_field' ], 10, 3 );
			}
		}
	}

	public static function hidden_field( $value ) {
		if ( empty( $value['id'] ) || ! isset( $value['default'] ) ) {
			return;
		}

		$custom_attributes = Settings_Util::get_custom_attributes( $value ); // atts are escaped
		?>
		<input type="hidden" name="<?php echo esc_attr( $value['id'] ); ?>" value="<?php echo esc_attr( $value['default'] ); ?>" <?php echo $custom_attributes; ?>/>
		<?php
	}

	public static function color_size_field( $value ) {
		$field_description = WC_Admin_Settings::get_field_description( $value );

		// Redo the description as WC runs wp_kes_post() on it which messes up any inline CSS
		if ( ! empty( $value['desc'] ) ) {
			$field_description['description'] = '<span class="description">' . $value['desc'] . '</span>';
		}

		$option_value      = WC_Admin_Settings::get_option( $value['id'], $value['default'] );
		$color_value       = isset( $option_value['color'] ) ? $option_value['color'] : '';
		$size_value        = isset( $option_value['size'] ) ? $option_value['size'] : '';
		$size_min          = isset( $value['min'] ) ? (int) $value['min'] : 0;
		$custom_attributes = Settings_Util::get_custom_attributes( $value ); // atts are escaped
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] . '[color]' ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
				<?php echo $field_description['tooltip_html']; ?>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?> color-size-field">&lrm;
				<span class="color-size-preview colorpickpreview" style="background: <?php echo esc_attr( $color_value ); ?>">&nbsp;</span>
				<input
					name="<?php echo esc_attr( $value['id'] . '[color]' ); ?>"
					id="<?php echo esc_attr( $value['id'] . '[color]' ); ?>"
					type="text"
					dir="ltr"
					value="<?php echo esc_attr( $color_value ); ?>"
					class="<?php echo esc_attr( $value['class'] ); ?> colorpick color-input"
					placeholder="<?php _e( 'Color', 'woocommerce-lead-time' ); ?>"
					<?php echo $custom_attributes; ?>
					/>&lrm;
				<div id="colorPickerDiv_<?php echo esc_attr( $value['id'] ); ?>" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>
				<input
					name="<?php echo esc_attr( $value['id'] . '[size]' ); ?>"
					id="<?php echo esc_attr( $value['id'] . '[size]' ); ?>"
					type="number"
					value="<?php echo esc_attr( $size_value ); ?>"
					class="size-input"
					min="<?php echo esc_attr( $size_min ); ?>"
					placeholder="<?php _e( 'Size', 'woocommerce-lead-time' ); ?>"
					/> <?php echo $field_description['description']; ?>
			</td>
		</tr>
		<?php
	}

	public static function help_note_field( $value ) {
		$field_description = WC_Admin_Settings::get_field_description( $value );
		?>
		<tr valign="top">
			<th scope="row" class="titledesc <?php echo esc_attr( $value['class'] ); ?>" style="padding:0;">
				<?php echo esc_html( $value['title'] ); ?>
				<?php echo $field_description['tooltip_html']; ?>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>" style="padding-top:0;padding-bottom:5px;">
				<?php echo $field_description['description']; ?>
			</td>
		</tr>
		<?php
	}

	public static function multi_text_field( $value ) {
		// Get current values
		$option_values = (array) get_option( $value['id'], $value['default'] );

		if ( empty( $option_values ) ) {
			$option_values = [ '' ];
		}

		$field_description = WC_Admin_Settings::get_field_description( $value );
		$custom_attributes = Settings_Util::get_custom_attributes( $value ); // atts are escaped
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
				<?php echo $field_description['tooltip_html']; ?>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">

				<div class="multi-field-container">
					<?php foreach ( $option_values as $i => $option_value ) : ?>
						<?php $first_field = ( $i === 0 ); ?>
						<div class="multi-field-input">
							<input
								type="text"
								name="<?php echo esc_attr( $value['id'] ); ?>[]"
								<?php
								if ( $first_field ) {
									echo 'id="' . esc_attr( $value['id'] ) . '"';
									echo ' ' . $custom_attributes;
								}
								?>
								value="<?php echo esc_attr( $option_value ); ?>"
								class="<?php echo esc_attr( $value['class'] ); ?>"
								placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
								/>
							<span class="multi-field-actions">
								<a class="multi-field-add" data-action="add" href="#"><span class="dashicons dashicons-plus"></span></a>
								<?php if ( $i > 0 ) : ?>
									<a class="multi-field-remove" data-action="remove" href="#"><span class="dashicons dashicons-minus"></span></a>
								<?php endif; ?>
							</span>
							<?php
							if ( $first_field ) {
								echo $field_description['description'];
							}
							?>
						</div>
					<?php endforeach; ?>
				</div>
			</td>
		</tr>
		<?php
	}

	public static function settings_start_field( $value ) {
		$id    = ! empty( $value['id'] ) ? sprintf( ' id="%s"', esc_attr( $value['id'] ) ) : '';
		$class = ! empty( $value['class'] ) ? sprintf( ' class="%s"', esc_attr( $value['class'] ) ) : '';

		echo "<div{$id}{$class}>";
	}

	public static function settings_end_field( $value ) {
		echo '</div>';
	}

	public static function plugin_promo_field( $value ) {
		$id      = ! empty( $value['id'] ) ? sprintf( ' id="%s"', esc_attr( $value['id'] ) ) : '';
		$content = ! empty( $value['content'] ) ? $value['content'] : '';

		echo "<div{$id}>{$content}</div>";
	}

	public static function checkbox_tooltip_field( $value ) {
		$option_value      = $value['value'];
		$description       = wp_kses_post( $value['desc'] );
		$tooltip_html      = ! empty( $value['desc_tip'] ) ? wc_help_tip( $value['desc_tip'] ) : '';
		$custom_attributes = Settings_Util::get_custom_attributes( $value ); // atts are escaped
		?>
		<?php if ( ! isset( $value['checkboxgroup'] ) || 'start' === $value['checkboxgroup'] ) : ?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<?php echo esc_html( $value['title'] ); ?>
				</th>
				<td class="forminp forminp-checkbox">
				<?php endif; ?>
				<fieldset>
					<?php if ( ! empty( $value['title'] ) ) : ?>
						<legend class="screen-reader-text"><span><?php echo esc_html( $value['title'] ); ?></span></legend>
							<?php endif; ?>
					<label for="<?php echo esc_attr( $value['id'] ); ?>">
						<input
							name="<?php echo esc_attr( $value['id'] ); ?>"
							id="<?php echo esc_attr( $value['id'] ); ?>"
							type="checkbox"
							class="<?php echo esc_attr( isset( $value['class'] ) ? $value['class'] : '' ); ?>"
							value="1"
							<?php checked( $option_value, 'yes' ); ?>
							<?php echo $custom_attributes; ?>
							/> <?php echo $description; ?>
					</label> <?php echo $tooltip_html; ?>
				</fieldset>
				<?php if ( ! isset( $value['checkboxgroup'] ) || 'end' === $value['checkboxgroup'] ) : ?>
				</td>
			</tr>
		<?php endif; ?>
		<?php
	}

	public static function sanitize_checkbox_tooltip_field( $value, $option, $raw_value ) {
		if ( 'checkbox_tooltip' !== $option['type'] ) {
			return $value;
		}

		$value = '1' === $raw_value || 'yes' === $raw_value ? 'yes' : 'no';
		return $value;
	}

}
