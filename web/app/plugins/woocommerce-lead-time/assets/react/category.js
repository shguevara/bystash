import { render } from '@wordpress/element';
import Category from './containers/category';
const appRoot = document.getElementById( 'wclt-category-root' );

import './category.scss'

render(
	<Category />,
	appRoot
);