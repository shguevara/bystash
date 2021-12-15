import { 
	Panel, 
	PanelBody, 
	PanelRow, 
	TextControl, 
	SelectControl,
	DatePicker,
	Popover,
	Button,
} from '@wordpress/components';
import moment from 'moment';
import { Icon, info, calendar } from '@wordpress/icons';
import { isEmpty, map } from 'lodash';
import { useState } from 'react';

import LabelWithWCTooltip from './label-with-wc-tooltip';

const CategoryConfig = WCLT_Category

const CategoryStockStatusesList = (props) => {
	
	const {
		statuses,
		updateData,
		data
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

	return (
		<Panel>
			{
				map( statuses, ( status_label, status_id ) => {

					const [ format, setFormat ] = useState( ! isEmpty( data[ status_id ].format ) ? data[ status_id ].format : 'static' );
					const [ leadTimeText, setLeadTimeText ] = useState( ! isEmpty( data[ status_id ].text ) ? data[ status_id ].text : '' );
					const [ leadTimeDate, setLeadTimeDate ] = useState( ! isEmpty( data[ status_id ].date ) ? data[ status_id ].date : '' );

					var pickerDate = null

					if ( ! isEmpty( leadTimeDate ) ) {
						pickerDate = getFormattedDate( leadTimeDate )
					}

					return (
						<PanelBody key={status_id} title={status_label} initialOpen={false}>
							<PanelRow>
								<SelectControl
									label={ CategoryConfig.labels.format }
									value={ format }
									options={ CategoryConfig.labels.format_options }
									onChange={ ( newFormat ) => {
										setFormat( newFormat )
										updateData( status_id, 'format', newFormat )
									} }
								/>
							</PanelRow>

							{
								format === 'static'
								&&
								<PanelRow>
									<TextControl
										label={ <LabelWithWCTooltip tooltip={ CategoryConfig.labels.text_help }>{CategoryConfig.labels.text}</LabelWithWCTooltip> }
										value={ leadTimeText }
										onChange={ ( value ) => {
											setLeadTimeText( value )
											updateData( status_id, 'text', value )
										} }
									/>
								</PanelRow>
							}
							{ 
								format === 'dynamic'
								&&
								<PanelRow className="alt-no-flex-panel">

									<LabelWithWCTooltip tooltip={ CategoryConfig.labels.date_help }>{CategoryConfig.labels.date}</LabelWithWCTooltip>

									<DatePicker
										currentDate={ pickerDate }
										onChange={ ( newDate ) => {
											setLeadTimeDate( moment(newDate).format( 'X' ) )
											updateData( status_id, 'date', moment(newDate).format( 'X' ) )
										} }
										onMonthPreviewed={ (v) => console.log(v) }
									/>

								</PanelRow>
							}
							
						</PanelBody>
					)
				})
			}
		</Panel>
	);
}

export default CategoryStockStatusesList
