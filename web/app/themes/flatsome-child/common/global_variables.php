<?php

global $woocommerce;

$site_url = str_replace('wp', '', get_site_url());
$shop_page_url = wc_get_page_permalink('shop');
$cart_page_url = $woocommerce->cart->get_cart_url();
$checkout_page_url = $woocommerce->cart->get_checkout_url();
