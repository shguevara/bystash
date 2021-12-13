<?php

// Load colors.
$bg        = get_option('woocommerce_email_background_color');
$body      = get_option('woocommerce_email_body_background_color');
$base      = get_option('woocommerce_email_base_color');
$base_text = wc_light_or_dark($base, '#202020', '#ffffff');
$text      = get_option('woocommerce_email_text_color');

$font_family        = 'Arial, "Helvetica Neue", Helvetica, sans-serif';
$font_size_base     = '18px';
$font_size_h1       = '32px';
$font_size_h2       = '18px';
$width              = '800px';
$color_headings     = get_theme_mod('type_headings_color');
$color_texts        = get_theme_mod('color_texts');
$color_primary      = get_theme_mod('color_primary');
$color_success      = get_theme_mod('color_success');
$color_links        = get_theme_mod('color_links');
$color_checkout     = get_theme_mod('color_checkout');
$color_sale         = get_theme_mod('color_sale');
$color_new_bubble   = get_theme_mod('color_new_bubble');
$color_top_seller   = get_theme_mod('color_top_seller');
$color_top_seller   = get_theme_mod('color_new_arrival');
$footer_1_bg_color  = get_theme_mod('footer_1_bg_color');
$footer_2_bg_color  = get_theme_mod('footer_2_bg_color');

$color_cta = $color_checkout;
$color_cta_text = '#fff';

// Pick a contrasting color for links.
$link_color = wc_hex_is_light($base) ? $base : $base_text;

if (wc_hex_is_light($body)) {
     $link_color = wc_hex_is_light($base) ? $base_text : $base;
}

$bg_darker_10    = wc_hex_darker($bg, 10);
$body_darker_10  = wc_hex_darker($body, 10);
$base_lighter_20 = wc_hex_lighter($base, 20);
$base_lighter_40 = wc_hex_lighter($base, 40);
$text_lighter_20 = wc_hex_lighter($text, 20);
$text_lighter_40 = wc_hex_lighter($text, 40);

?>

.copy-container {
text-align: center;
padding:0 35px;
margin: 20px 0 60px 0;
}

.big-heading {
font-size: 32px;
font-weight: bold;
text-transform: capitalize;
line-height: 1.3;
color: <?php echo $base ?>;
margin: 10px 0 18px 0 !important;
}

.cart-heading {
color: <?php echo $text ?>;
text-align: center;
font-size: 22px;
text-transform: capitalize;
font-weight: bold;
display: block;
}

.cta-button-large {
font-family: <?php echo $font_family ?>;
font-size: 20px;
font-weight: bold;
text-transform: capitalize;
text-align: center;
text-decoration: none;
color: <?php echo $color_cta_text ?>;
background-color: <?php echo $color_cta ?>;
padding: 12px 42px;
margin: 8px auto;
line-height: 21px;
border-radius: 4px 4px 4px 4px;
display: inline-block;
min-width: 195px;
}

.cta-button-medium {
font-family: <?php echo $font_family ?>;
font-size: 16px;
font-weight: bold;
text-transform: capitalize;
text-align: center;
text-decoration: none;
color: <?php echo $color_cta_text ?>;
background-color: <?php echo $color_cta ?>;
padding: 12px 5px;
margin: 16px 0 0;
line-height: 21px;
border-radius: 4px 4px 4px 4px;
display: inline-block;
min-width: 140px;
}

.total-savings {
font-weight: bold;
color: <?php echo $color_cta ?>;
text-decoration: underline;
}

.savings {
color: <?php echo $color_cta ?>;
}

.cupon .title {
text-align: center;
margin-bottom: 5px;
}

.cupon .title strong {
color: <?php echo $base ?>;
font-size: 16px;
}

.cupon .container {
text-align: center;
padding: 43px;
border: 2px dashed #848484;
font-size: 27px;
max-width: 485px;
margin: 0 auto;
}

.cupon .container.small {
padding: 20px;
font-size: 19px;
max-width: 318px;
margin: 5px auto;
}

.cupon .container strong {
font-weight: bold;
text-transform: uppercase;
color: <?php echo $color_cta ?>;
}

.cupon-text {
color: <?php echo $color_cta ?>;
text-transform: uppercase;
}

.product-title {
font-size: 18px;
font-weight: bold;
text-decoration: none;
text-transform: capitalize;
}

#wrapper {
background-color: <?php echo esc_attr($bg); ?>;
border-collapse: collapse;
mso-table-lspace: 0pt;
mso-table-rspace: 0pt;-
ms-text-size-adjust: 100%;
-webkit-text-size-adjust: 100%;
padding: 0;
margin: 0 auto;
border: 0;
}

#wrapper > table {
margin: 0 auto;
}

#template_container {
box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1) !important;
background-color: <?php echo esc_attr($body); ?>;
border: 1px solid <?php echo esc_attr($bg_darker_10); ?>;
border-radius: 3px !important;
}


#template_header {
padding: 30px 0 15px 0;
text-align: center;
}

#body_content {
background-color: <?php echo esc_attr($body); ?>;
}

#body_content td ul.wc-item-meta {
font-size: small;
margin: 1em 0 0;
padding: 0;
list-style: none;
}

#body_content td ul.wc-item-meta li {
margin: 0.5em 0 0;
padding: 0;
}

#body_content td ul.wc-item-meta li p {
margin: 0;
}

#body_content_inner {
color: <?php echo esc_attr($text); ?>;
font-family: <?php echo $font_family ?>;
word-break: break-word;
text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
padding-top: 20px;
padding-right: 20px;
padding-bottom: 10px;
padding-left: 20px;
}

.address {
padding: 12px;
color: <?php echo esc_attr($text_lighter_20); ?>;
border: 1px solid <?php echo esc_attr($body_darker_10); ?>;
}

.text {
color: <?php echo esc_attr($text); ?>;
font-family: <?php echo $font_family ?>
}

.link {
color: <?php echo esc_attr($link_color); ?>;
}

h1 {
color: <?php echo esc_attr($color_heading); ?>;
font-family: <?php echo $font_family ?>;
font-size: <?php echo $font_size_h1 ?>;
font-weight: bold;
line-height: 150%;
margin: 0;
}

h2 {
color: <?php echo esc_attr($color_heading) ?>;
display: block;
font-family: <?php echo $font_family ?>;
font-size: <?php echo $font_size_h2 ?>;
font-weight: bold;
line-height: 130%;
margin: 25px 0 0;
text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
}

h3 {
color: <?php echo esc_attr($base); ?>;
display: block;
font-family: <?php echo $font_family ?>
font-size: 16px;
font-weight: bold;
line-height: 130%;
margin: 16px 0 8px;
text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
}

a {
color: <?php echo esc_attr($link_color); ?>;
font-weight: normal;
text-decoration: underline;
}

.pre-footer-container {
font-size: 14px;
font-family: <?php echo $font_family ?>;
color: <?php echo esc_attr($base); ?>;
margin-bottom: 10px;
}

.pre-footer-container ul {
list-style: none;
padding: 0;
margin: 0;
}

.pre-footer-container li {
padding: 0;
margin: 0;
}

.pre-footer-container a {
display: block;
font-size: 22px;
font-weight: bold;
color: <?php echo $base ?>;
background-color: <?php echo $footer_1_bg_color ?>;
text-decoration: none;
padding: 12px;
margin: 0 0 10px 0;
text-align: center;
text-transform: uppercase;
}

.footer-container {
font-family: <?php echo $font_family ?>;
color: <?php echo esc_attr($base); ?>;
font-size: 12px;
background-color: <?php echo $footer_1_bg_color ?>;
padding: 1em 2.2em;
line-height: 1.5em;
text-align: center;
}

.footer-container .store-name {
font-weight: bold;
font-size: 1.2em;
color: <?php echo $base ?>;
}

.divider {
border-top-width: 6px;
border-top-color: <?php echo $footer_1_bg_color ?>;
border-top-style: solid;
}


<?php // Helper Classes 
?>
.underline {
text-decoration: underline !important;
}

.bold {
font-weight: bold !important;
}

.italic {
font-style: italic !important;
}

.strike-trough {
text-decoration: line-through !important;
}

.block {
display: block !important;
}

.center {
text-align: center; 
}

.capitalize {
     text-transform: capitalize;
}