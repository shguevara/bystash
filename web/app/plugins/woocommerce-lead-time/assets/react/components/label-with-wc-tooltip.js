import { isEmpty } from 'lodash';
import { useEffect } from 'react';

const LabelWithWCTooltip = (props) => {
    
	const {
		tooltip,
	} = props

    useEffect(() => {
        jQuery( '.woocommerce-help-tip' ).tipTip( {
            'attribute': 'data-tip',
            'fadeIn': 50,
            'fadeOut': 50,
            'delay': 200,
            'keepAlive': true,
			'defaultPosition': "bottom",
        } );
    });

	return (
		<span className="label-with-tooltip">
            { props.children }

            { ! isEmpty( tooltip ) && <span className="woocommerce-help-tip" data-tip={ tooltip }></span> }
        </span>
	);
}

export default LabelWithWCTooltip