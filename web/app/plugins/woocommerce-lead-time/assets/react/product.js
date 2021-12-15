import { render } from '@wordpress/element';
const appRoot = document.getElementById( 'wclt-product-root' );
const variableRoot = document.getElementsByClassName( 'wclt-product-variable-root' );
const variablePerStockRoot = document.getElementsByClassName( 'wclt-product-variable-perstock-root' );
const perStockRoot = document.getElementById( 'wclt-product-perstock-root' );

import Product from './containers/product';
import ProductPerStock from './containers/product-per-stock';
import ProductVariable from './containers/product-variable';
import ProductVariablePerStock from './containers/product-variable-per-stock';

import './product.scss'

if ( appRoot ) {
	render(
		<Product />,
		appRoot
	);
}

if ( perStockRoot ) {
	render(
		<ProductPerStock />,
		perStockRoot
	);
}

/**
 * Dirty way of hooking up react into variations loaded by WC.
 * We lookup for the elements with the specified class then render
 * the component for each of the elements.
 */
jQuery(document).ready(function () {
	const wclt_rendered_variations = []

	jQuery( '#woocommerce-product-data' ).on( 'woocommerce_variations_loaded', function() {
		if ( variableRoot.length > 0 ) {
			for (let element of variableRoot) {
				render(
					<ProductVariable element={ element } />,
					element
				);
			}
		}

		if ( variablePerStockRoot.length > 0 ) {
			for (let element of variablePerStockRoot) {
				wclt_rendered_variations.push( element )
				render(
					<ProductVariablePerStock element={ element } />,
					element
				);
			}
		}
	} );

	// When a new variation is added, render the react component.
	jQuery( '#woocommerce-product-data' ).on( 'woocommerce_variations_added', function() {
		for (let e of variablePerStockRoot) {
			if ( ! wclt_rendered_variations.includes( e ) ) {
				render(
					<ProductVariablePerStock element={ e } />,
					e
				);
			}
		}
	} );
});