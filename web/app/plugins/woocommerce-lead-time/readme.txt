=== WooCommerce Lead Time ===
Contributors: barn2media, andykeith
Tags: woocommerce, lead time, stock
Requires at least: 5.0
Tested up to: 5.8
Requires PHP: 7.2
Stable tag: 2.0.0
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Provide individual and global lead times for your WooCommerce products.

== Description ==

The WooCommerce Lead Time plugin lets you set a lead time either globally or for individual products.

The lead time is displayed on the single product page as follows:

 * Above the quantity and add to cart button.
 * If stock is being managed then it will appear immediately after the stock status.

You can choose to display for any or all of the following stock statuses:

 * In stock products
 * Out of stock products
 * Products on backorder

== Installation ==

1. Download the plugin file from the Order Confirmation email or the [account area](https://barn2.com/account/).
1. In the WordPress Dashboard, go to Plugins -> Add New -> Upload and select the ZIP file you just downloaded.
1. Activate the plugin.
1. Next, enter your license key under WooCommerce -> Settings -> Products -> Lead time.
1. Configure the global lead time options on this page. Add or override lead times for individual products on the Edit Product screen (under the Inventory tab).

== Frequently Asked Questions ==

Please refer to our [support page](https://barn2.com/support-center/).

== Changelog ==

= 2.0.0 =
Release date 27 September 2021

 * New: Display lead time on shop & category pages.
 * New: Added support for dynamic lead times.
 * New: Added support for lead times per stock status.
 * Tweak: Updated WPML compatibility configuration file.
 * Dev: added filter to disable the built-in theme-specific template overrides.
 * Tested up to WooCommerce 5.7.

= 1.5.4 =
Release date 20 July 2021

 * New: Added support for WooCommerce Discontinued Products plugin.
 * Dev: updated library code.
 * Tested up to WordPress 5.8 and WooCommerce 5.5.

= 1.5.3 =
Release date 20 April 2021

 * New: Added support for new navigation menus in WooCommerce Admin feature plugin.
 * Tweak: Stopped forcing lowercase output on translated lead time unit strings.
 * Tested up to WordPress 5.7 and WooCommerce 5.2.2.

= 1.5.2 =
Release date 19 January 2021

 * Tweak: Connect display stock options to all new lead time areas and WooCommerce Product Table.
 * Tweak: Support new and old versions of WooCommerce Product Table.
 * Dev: Add Composer project type.
 * Tested up to WooCommerce 4.9.

= 1.5.1 =
Release date 18 December 2020

 * Tweak: Better handling of item meta when no global prefix is present.

= 1.5 =
Release date 17 December 2020

 * New: Options to show the lead time on the single product, cart, checkout, and order details.
 * Fix: Conflict between saving product and variation lead times.
 * Tweak: Made it possible to save an empty category lead time value.
 * Tested up to WordPress 5.6 and WooCommerce 4.8.

= 1.4.1 =
Release date 1 October 2020

 * Tweak: Use WooCommerce CRUD functions for variation meta.
 * Tweak: Theme compatibility for Astra and Porto, as well as a generic fallback for <span> elements being displayed in the lead time output.

= 1.4 =
Release date 10 July 2020

 * New: New feature to set lead times for entire categories.
 * New: New function to retrieve the lead time.

= 1.3.1 =
Release date 7 July 2020

 * Tested up to WooCommerce 4.3 and WordPress 5.4.2.

= 1.3 =
Release date 3 June 2020

 * New: New option to set the lead time unit and display this after the lead time.

= 1.2 =
Release date 17 April 2020

 * New: Tested up to WooCommerce 4.1.
 * Tweak: Minor improvements to settings page.
 * Dev: Added new plugin license system and refactored some classes.

= 1.1.2 =
Release date 12 March 2020

 * Tested up to WooCommerce 4.0 and WordPress 5.4.

= 1.1.1 =
Release date 21 January 2020

 * Fully tested with WordPress 5.3.2 and WooCommerce 3.9.

= 1.1 =
Release date 17 December 2019

 * New: New option to add lead times for individual variations.

= 1.0.1 =
Release date 30 October 2019

 * Tested up to WordPress 5.3 and WooCommerce 3.8.
 * Update library code.

= 1.0 =
Release date 11 September 2019

* Initial release.