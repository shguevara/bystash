import { 
	TextControl, 
	SelectControl,
	DatePicker,
} from '@wordpress/components';
import moment from 'moment';
import { isEmpty, map } from 'lodash';
import { useState } from 'react';

import LabelWithWCTooltip from './label-with-wc-tooltip';

const CategoryConfig = WCLT_Category

const CategoryFieldsTable = (props) => {
	
	const {
		data,
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

	const [ format, setFormat ] = useState( ! isEmpty( data.format ) ? data.format : 'static' );
	const [ leadTimeText, setLeadTimeText ] = useState( ! isEmpty( data.text ) ? data.text : '' );
	const [ leadTimeDate, setLeadTimeDate ] = useState( ! isEmpty( data.date ) ? data.date : '' );

	var pickerDate = ''

	if ( ! isEmpty( leadTimeDate ) ) {
		pickerDate = getFormattedDate( leadTimeDate )
	}

	return (
		<>
			<tr className="form-field wclt-not-per-stock">
				<th scope="row" valign="top">
					<label>
						{ CategoryConfig.labels.format }
					</label>
				</th>
				<td className="select-auto">
					<SelectControl
						value={ format }
						options={ CategoryConfig.labels.format_options }
						onChange={ ( newFormat ) => {
							setFormat( newFormat )
							updateCategoryFormat( newFormat )
						} }
					/>
				</td>
			</tr>
			{
				format === 'static'
				&&
				<tr className="form-field wclt-not-per-stock">
					<th scope="row" valign="top">
						<label>
							<LabelWithWCTooltip tooltip={ CategoryConfig.labels.text_help }>{ CategoryConfig.labels.text }</LabelWithWCTooltip>
						</label>
					</th>
					<td>
						<TextControl
							value={ leadTimeText }
							onChange={ ( value ) => {
								setLeadTimeText( value )
								updateCategoryText( value )
							} }
						/>
					</td>
				</tr>
			}
			{
				format === 'dynamic'
				&&
				<tr className="form-field wclt-not-per-stock">
					<th scope="row" valign="top">
						<label>
							<LabelWithWCTooltip tooltip={ CategoryConfig.labels.date_help }>{ CategoryConfig.labels.date }</LabelWithWCTooltip>
						</label>
					</th>
					<td className="inside">
						<DatePicker
							currentDate={ pickerDate }
							onChange={ ( newDate ) => {
								setLeadTimeDate( moment(newDate).format( 'X' ) )
								updateCategoryDate( moment(newDate).format( 'X' ) )
							} }
							onMonthPreviewed={ (v) => console.log(v) }
						/>
					</td>
				</tr>
			}
		</>
	);
}

export default CategoryFieldsTable