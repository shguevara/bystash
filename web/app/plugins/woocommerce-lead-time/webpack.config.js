const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );
const WooCommerceDependencyExtractionWebpackPlugin = require( '@woocommerce/dependency-extraction-webpack-plugin' );

module.exports = {
	...defaultConfig,
	plugins: [
		...defaultConfig.plugins.filter(
			( plugin ) =>
				plugin.constructor.name !== 'DependencyExtractionWebpackPlugin'
		),
		new WooCommerceDependencyExtractionWebpackPlugin(),
	],
	entry: {
		'wc-lead-time-admin-settings': './assets/react/admin.js',
		'wc-lead-time-category': './assets/react/category.js',
		'wc-lead-time-product': './assets/react/product.js'
	},
	output: {
		...defaultConfig.output,
		path: path.resolve( process.cwd(), 'assets/react/build' ),
	},
};
