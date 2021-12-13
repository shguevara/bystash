<?php
// phpcs:ignoreFile
/**
 * Additional email styles that are added to every email template.
 * Override this template by copying it to yourtheme/automatewoo/email/
 */

if ( ! defined( 'ABSPATH' ) ) exit;

?>

<?php /** Basic */ ?>
#body_content_inner {
	font-size: 14px;
	line-height: 1.4;
}

img.aligncenter {
	margin: 0 auto 10px;
	display: block;
}

img.alignleft {
	float: left;
	margin-right: 15px;
	margin-bottom: 10px;
}

img.alignright {
	float: right;
	margin-left: 15px;
	margin-bottom: 10px;
}


<?php /** Products display */ ?>

.aw-product-grid-container .price {
	color: #555;
	font-size: 16px;
}
.aw-product-grid-container .currency-symbol {
	font-size: 14px;
}

.aw-product-grid-container .woocommerce-price-regular {
	font-weight: bold;
}

.aw-product-grid-container span {
	text-decoration: none !important;
}

.aw-product-grid-container .woocommerce-price-sale,
.aw-product-grid-container del
 {	
	text-decoration: line-through !important; 
	opacity: 0.4;
	margin-right: 3px;
} 

.aw-product-grid-item-2-col h3 a { 
	font-weight: bold; text-decoration: none;
}


table.aw-product-grid {
	width: 100%;
}

.aw-product-grid-container {
	font-size: 0px;
	margin: 10px 0 10px;
}

.aw-product-grid-item-3-col {
	width: 30.5%;
	display: inline-block;
	text-align:left;
	padding: 0 0 30px;
	vertical-align:top;
	word-wrap:break-word;
	margin-right: 4%;
	font-size: 14px;
}

.aw-product-grid-item-2-col {
	width: 46%;
	display: inline-block;
	text-align:left;
	padding: 0 0 30px;
	vertical-align:top;
	word-wrap:break-word;
	margin-right: 6%;
	font-size: 14px;
}

table.aw-product-rows  {
	margin: 10px 0;
	border-top: 2px solid #e7e2e2;
}

table.aw-product-rows td {
	padding:0;
	border-bottom: 2px solid #e7e2e2;
}

table.aw-product-rows td.image-container {
	position: relative;
    padding: 30px 0;
}

table.aw-product-rows td.image-container p {
	margin:0 !important;
}

table.aw-product-rows td.title-container {
    padding-left: 15px;
	line-height: 100%;
}

.title-container .savings {
	font-size: 15px;
}

table.aw-product-rows td.price-container {
	font-size: 16px;
}

.counter {
    position: absolute;
    background: <?php echo get_theme_mod('color_top_seller') ?>;
    padding: 5px 13px;
    font-weight: bold;
    color: #fff;
	text-shadow: 2px 2px #00000073;
}

.product-title {
	font-size:1em;
	font-weight: bold;
	text-decoration: none;
	text-transform: capitalize;
	display: block;
	margin-bottom: 12px;
	line-height: 1.1;
}

.sale-price {
	font-size: 18px;
	color: #555; 
	text-decoration: none;
}

.regular-price {
	font-size: 17px;
	text-decoration: none;
	margin-right: 3px;
	color: #555; 
	opacity:0.6;
}

.savings-price {
	padding-top: 6px;
	font-size:15px;
	color: <?php echo $cta_color ?>;
}

.savings-currency {
	font-weight: bold; 
	text-decoration: underline
}

table.aw-product-rows td.total-savings-container {
    padding-top: 24px;
	border-bottom: 0;
	font-size: 21px;
	color: <?php echo $cta_color ?>;
}

table.aw-product-rows td.total-savings-container strong {
	text-decoration:underline;
}


table.aw-product-rows td.last {
	padding-right: 0 !important;
}

table.aw-order-table img,
table.aw-product-grid img,
table.aw-product-rows td img {
	max-width: 100%;
	height: auto;
}

table.aw-product-rows h3,
table.aw-product-rows p {
	margin: 5px 0 !important;
}


table.aw-order-table {
	width: 100%;
	border: 1px solid #eee;
}

table.aw-order-table tr td,
table.aw-order-table tr th {
	text-align:left;
	vertical-align:middle;
	border: 1px solid #eee;
	font-family: Arial, Helvetica, sans-serif;
	word-wrap:break-word;
}

.aw-coupon-code,
.automatewoo-coupon {
	font-weight: bold;
	letter-spacing: 2px;
	display: inline-block;
	padding: 12px 25px 13px;
	font-size: 17px;
	color: <?php echo esc_attr( $text ); ?>;
	border: 2px solid <?php echo esc_attr( $text ); ?>
}

a.aw-btn-1,
a.automatewoo-button {
	background-color: <?php echo esc_attr( $base ); ?>;
	border-radius: 4px;
	font-weight: bold;
	display: inline-block;
	padding: 12px 35px 13px;
	margin: 8px auto;
	font-size: 14px;
	text-decoration: none !important;
	color: #ffffff !important;
	text-align: center;
}


a.automatewoo-button--wide {
	display: block;
}

a.automatewoo-button--small {
	font-size: 13px;
	padding: 6px 16px 7px;
	margin: 5px auto;
}

a.automatewoo-button--large {
	font-size: 16px;
	margin: 10px auto;
	padding: 14px 40px 15px;
}


.automatewoo-plain-email-footer {
    font-size: 75%;
    color:#999999;
}

.automatewoo-plain-email-footer a {
    color:#999999;
}


.aw-reviews-grid__item h3,
.aw-reviews-grid__item p {
	text-align: center;
}