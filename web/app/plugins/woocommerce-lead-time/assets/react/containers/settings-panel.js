/**
 * External dependencies
 */
import { Component, useState } from '@wordpress/element';
import { 
	ToggleControl,
	TextControl,
	SelectControl,
	DatePicker,
} from '@wordpress/components';
import AdminStockStatusesList from '../components/admin-stock-statuses-list';
import { has, isEmpty } from 'lodash';
import moment from 'moment';

const defaultGlobalPrefix = jQuery( '[name=wclt_prefix]' )
const defaultGlobalLeadTime = jQuery( '[name=wclt_global_time]' )
const defaultGlobalFormat = jQuery( '[name=wclt_global_format]' )
const defaultGlobalDate = jQuery( '[name=wclt_global_date]' )
const hiddenInputValueHolder = jQuery( '#wclt_react_data' )
const hiddenInputLeadTimePerStockHolder = jQuery( '[name=wclt_lead_per_stock_status]' )

class SettingsPanel extends Component {

	constructor( props ) {
		super( props );
		this.updateData = this.updateData.bind( this )

		this.state = {
			lead_time_per_stock: false,
			data: {},
			lead_time_prefix: wclt_admin_settings.data.prefix,
			lead_time_format: wclt_admin_settings.data.format,
			lead_time_text: wclt_admin_settings.data.text,
			lead_time_date: wclt_admin_settings.data.date,
		}
	}

	/**
	 * On page load: 
	 * detect the option's status and update the state.
	 * prepare the data object containing the stock statuses details.
	 */
	componentDidMount() {
		const settings = wclt_admin_settings.data

		if ( has( settings, 'lead_per_stock' ) && settings.lead_per_stock === true ) {
			this.setState( { lead_time_per_stock: true } )
		}

		this.setState( { data: settings.statuses } )

	}

	/**
	 * When the lead time per stock status option is enabled, show/hide fields.
	 */
	componentDidUpdate() {
		if ( this.state.lead_time_per_stock === true ) {
			hiddenInputLeadTimePerStockHolder.val( 'yes' )
		} else {
			hiddenInputLeadTimePerStockHolder.val( 'no' )
		}

		hiddenInputValueHolder.val( JSON.stringify( this.state.data ) )

		defaultGlobalPrefix.val( this.state.lead_time_prefix )
		defaultGlobalLeadTime.val( this.state.lead_time_text )
		defaultGlobalFormat.val( this.state.lead_time_format )
		defaultGlobalDate.val( this.state.lead_time_date )

		jQuery( '.woocommerce-help-tip' ).tipTip( {
            'attribute': 'data-tip',
            'fadeIn': 50,
            'fadeOut': 50,
            'delay': 200,
            'keepAlive': true,
			'defaultPosition': "bottom",
        } );
	}

	/**
	 * Format the date into a human readable format.
	 * 
	 * @param {string} date 
	 * @returns string
	 */
	getFormattedDate = ( date ) => {
		return moment( new Date( date * 1000 ) ).format( 'MMMM Do YYYY' )
	}

	/**
	 * Update individual stock status settings data.
	 * 
	 * @param {string} status_id 
	 * @param {string} property 
	 * @param {mixed} value 
	 */
	updateData( status_id, property, value ) {
		this.setState( prevState => ({
			data: {
				...prevState.data,
				[status_id]: {
					...prevState.data[status_id],
					[ property ]: value
				}
			}
		}))
	}

	/**
	 * Toggle the lead time per stock setting.
	 */
	toggleLeadTimePerStock() {
		this.setState( prevState => ({
			lead_time_per_stock: ! prevState.lead_time_per_stock
		}));
	}

	render() {

		const {
			lead_time_per_stock,
			data,
			lead_time_prefix,
			lead_time_text,
			lead_time_format,
			lead_time_date,
		} = this.state

		var pickerDate = ''

		if ( ! isEmpty( lead_time_date ) ) {
			pickerDate = this.getFormattedDate( lead_time_date )
		}

		return (
			<>
				<tr valign="top">
					<th scope="row" className="titledesc">{ wclt_admin_settings.labels.lead_time_per_stock_title }</th>
					<td>
						<fieldset>
							<label htmlFor="wclt_display_lead_time_per_stock">
								<input 
									name="wclt_display_lead_time_per_stock" 
									id="wclt_display_lead_time_per_stock" 
									type="checkbox" 
									checked={ lead_time_per_stock } 
									onChange={ (v) => {
										this.toggleLeadTimePerStock()
									} }/>
								{ wclt_admin_settings.labels.lead_time_per_stock }
							</label>
						</fieldset>
						{ lead_time_per_stock && <AdminStockStatusesList 
							statuses={ wclt_admin_settings.statuses }
							updateData={ this.updateData }
							data={ data }
						/> }
					</td>
				</tr>
				{ 
					! lead_time_per_stock && 
					<>
						<tr valign="top">
							<th scope="row" className="titledesc">
								<label>
									{ wclt_admin_settings.labels.prefix }
									<span className="woocommerce-help-tip" data-tip={ wclt_admin_settings.labels.prefix_help }></span>
								</label>
							</th>
							<td>
								<TextControl
									value={ lead_time_prefix }
									onChange={ ( value ) => this.setState( { lead_time_prefix: value } ) }
								/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row" className="titledesc">
								<label>{ wclt_admin_settings.labels.format }</label>
							</th>
							<td className="select-auto">
								<SelectControl
									value={ lead_time_format }
									options={ wclt_admin_settings.labels.format_options }
									onChange={ ( newFormat ) => this.setState( { lead_time_format: newFormat } ) }
								/>
							</td>
						</tr>
						{ 
							lead_time_format === 'static' && 
							<tr valign="top">
								<th scope="row" className="titledesc">
									<label>
										{ wclt_admin_settings.labels.text }
										<span className="woocommerce-help-tip" data-tip={ wclt_admin_settings.labels.text_help }></span>
									</label>
								</th>
								<td>
									<TextControl
										value={ lead_time_text }
										onChange={ ( value ) => this.setState( { lead_time_text: value } ) }
									/>
								</td>
							</tr> 
						}
						{ 
							lead_time_format === 'dynamic' && 
							<tr valign="top">
								<th scope="row" className="titledesc">
									<label>
										{ wclt_admin_settings.labels.date }
										<span className="woocommerce-help-tip" data-tip={ wclt_admin_settings.labels.date_help }></span>
									</label>
								</th>
								<td className="inside cal-container">
									<DatePicker
										currentDate={ pickerDate }
										onChange={ ( newDate ) => this.setState( { lead_time_date: moment(newDate).format( 'X' ) } ) }
										onMonthPreviewed={ (v) => console.log(v) }
									/>
								</td>
							</tr>	
						}
					</>
				}
			</>
		);
	}
}

export default SettingsPanel
