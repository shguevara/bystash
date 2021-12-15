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

const AdminStockStatusesList = (props) => {
	
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

					const [ leadTimePrefix, setLeadTimePrefix ] = useState( data[ status_id ].prefix );
					const [ format, setFormat ] = useState( data[ status_id ].format );
					const [ leadTimeText, setLeadTimeText ] = useState( data[ status_id ].text );
					const [ leadTimeDate, setLeadTimeDate ] = useState( data[ status_id ].date );

					var pickerDate = null

					if ( ! isEmpty( leadTimeDate ) ) {
						pickerDate = getFormattedDate( leadTimeDate )
					}

					return (
						<PanelBody key={status_id} title={status_label} initialOpen={false}>
							<PanelRow className="alt-no-flex-panel no-label-margin">
								<TextControl
									label={ <LabelWithWCTooltip tooltip={ wclt_admin_settings.labels.prefix_help }>{ wclt_admin_settings.labels.prefix }</LabelWithWCTooltip> }
									value={ leadTimePrefix }
									onChange={ ( value ) => {
										setLeadTimePrefix( value )
										updateData( status_id, 'prefix', value )
									} }
								/>
							</PanelRow>
							<PanelRow>
								<SelectControl
									label={ wclt_admin_settings.labels.format }
									value={ format }
									options={ wclt_admin_settings.labels.format_options }
									onChange={ ( newFormat ) => {
										setFormat( newFormat )
										updateData( status_id, 'format', newFormat )
									} }
								/>
							</PanelRow>

							{
								format === 'static'
								&&
								<PanelRow className="alt-no-flex-panel no-label-margin">
									<TextControl
										label={ <LabelWithWCTooltip tooltip={ wclt_admin_settings.labels.text_help }>{ wclt_admin_settings.labels.text }</LabelWithWCTooltip> }
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

									<LabelWithWCTooltip tooltip={ wclt_admin_settings.labels.date_help }>{ wclt_admin_settings.labels.date }</LabelWithWCTooltip>

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

export default AdminStockStatusesList
