import { 
	TextControl, 
	SelectControl,
	DatePicker,
	Popover,
} from '@wordpress/components';
import moment from 'moment';
import { Icon, info, calendar } from '@wordpress/icons';
import { isEmpty, map } from 'lodash';
import { useState } from 'react';

import LabelWithWCTooltip from './label-with-wc-tooltip';

const CategoryConfig = WCLT_Category

const CategoryFields = (props) => {
	
	const {
		is_edit_page,
		updateCategoryDate,
		updateCategoryFormat,
		updateCategoryText
	} = props

	/**
	 * Format the date into a human readable format.
	 * 
	 * @param {string} date 
	 * @returns string
	 */
	const getFormattedDate = ( date ) => {
		return moment( new Date( date * 1000 ) ).format( 'MMMM Do YYYY' )
	}

	const [ format, setFormat ] = useState( 'static' );
	const [ leadTimeText, setLeadTimeText ] = useState( '' );
	const [ leadTimeDate, setLeadTimeDate ] = useState( '' );

	var pickerDate = ''

	if ( ! isEmpty( leadTimeDate ) ) {
		pickerDate = getFormattedDate( leadTimeDate )
	}

	return (
		<div className="wclt-not-per-stock">
            <SelectControl
				label={ CategoryConfig.labels.format }
				value={ format }
				options={ CategoryConfig.labels.format_options }
				onChange={ ( newFormat ) => {
					setFormat( newFormat )
					updateCategoryFormat( newFormat )
				} }
			/>
			{
				format === 'static'
				&&
					<TextControl
						label={ <LabelWithWCTooltip tooltip={ CategoryConfig.labels.text_help }>{ CategoryConfig.labels.text }</LabelWithWCTooltip> }
						value={ leadTimeText }
						onChange={ ( value ) => {
							setLeadTimeText( value )
							updateCategoryText( value )
						} }
					/>
			}
			{ 
				format === 'dynamic'
				&&
					<div className="inside">
						<LabelWithWCTooltip tooltip={ CategoryConfig.labels.date_help }>{ CategoryConfig.labels.date }</LabelWithWCTooltip>
						<DatePicker
							currentDate={ pickerDate }
							onChange={ ( newDate ) => {
								setLeadTimeDate( moment(newDate).format( 'X' ) )
								updateCategoryDate( moment(newDate).format( 'X' ) )
							} }
							onMonthPreviewed={ (v) => console.log(v) }
						/>
					</div>
			}
        </div>
	);
}

export default CategoryFields