import { render } from '@wordpress/element';
import SettingsPanel from './containers/settings-panel';

const appRoot = document.getElementById( 'wclt-root' );

import './admin.scss'

render(
	<SettingsPanel></SettingsPanel>,
	appRoot
);