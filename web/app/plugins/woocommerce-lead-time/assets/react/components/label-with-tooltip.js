import { useState } from 'react';

import { 
    Dashicon,
    Popover,
    Button
} from '@wordpress/components';

const LabelWithTooltip = (props) => {
	const {
		tooltip,
	} = props

    const [ openTooltip, setOpenTooltip ] = useState( false );

	return (
		<span className="label-with-tooltip">
            { props.children }
            <Button
				isLink
				onClick={ () => setOpenTooltip( ! openTooltip ) }
				className="tooltip-btn"
            >
				<Dashicon icon="editor-help"></Dashicon>
			</Button>
            { openTooltip && (
				<Popover className="content-popover" onClose={ () => setOpenTooltip( false ) } position="bottom center">
					{ tooltip }
				</Popover>
			) }
        </span>
	);
}

export default LabelWithTooltip