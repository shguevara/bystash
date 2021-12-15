/**
 * External dependencies
 */
import { Component } from '@wordpress/element';
import {
	SelectControl,
	TextControl,
	DatePicker,
	Panel, 
	PanelBody, 
	PanelRow,
} from '@wordpress/components';
import { has, isEmpty, map } from 'lodash';

import LabelWithWCTooltip from '../components/label-with-wc-tooltip';

const hiddenInputDataHolder = jQuery( '[name=wclt-product-perstock-data]' )

const statuses = WCLT_Product.statuses
const labels = WCLT_Product.labels

class ProductPerStock extends Component {

	constructor( props ) {
		super( props );
		this.state = {
			data: WCLT_Product.data
		}
	}

	/**
	 * Pre-load the state into the hidden input on page load.
	 */
	componentDidMount() {
		hiddenInputDataHolder.val( JSON.stringify( this.state.data ) )
	}

	/**
	 * Each time the state is updated, save the data into the hidden input.
	 */
	componentDidUpdate() {
		hiddenInputDataHolder.val( JSON.stringify( this.state.data ) )
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

	render() {

		const {
			data,
		} = this.state

        return(
            <>
				{
					map( statuses, ( status_label, status_id ) => {

						const format = has( data, status_id + '.format' ) ? data[status_id].format : 'static';
						const text = has( data, status_id + '.text' ) ? data[status_id].text : '';
						const date = has( data, status_id + '.date' ) ? data[status_id].date : '';

						var pickerDate = null
						if ( ! isEmpty( date ) ) {
							pickerDate = this.getFormattedDate( date )
						}

						return (
							<PanelBody key={status_id} title={ labels.block_prefix + ' - ' + status_label } initialOpen={false}>
								<PanelRow className="alt-no-flex-panel no-label-margin">
								<div className="wclt-dynamic">
									<div className="fields-stock">
										<div className="form-field">
											<label htmlFor="">{ labels.format }</label>
											<SelectControl
												value={ format }
												options={ labels.format_options }
												onChange={ ( newFormat ) => {
													this.updateData( status_id, 'format', newFormat )
												} }
											/>
										</div>
										{ format === 'static' &&
										<div className="form-field">
											<label htmlFor="">{ labels.text }</label>
											<TextControl
												value={ text }
												onChange={ (value) => this.updateData( status_id, 'text', value ) }
											/>
											<LabelWithWCTooltip tooltip={ labels.text_help }></LabelWithWCTooltip>
										</div>
										}
										{ format === 'dynamic' && 
											<div className="form-field date-field">
												<label htmlFor="">{ labels.date }</label>
												<DatePicker
													currentDate={ pickerDate }
													onChange={ ( newDate ) => {
														this.updateData( status_id, 'date', moment(newDate).format( 'X' ) )
													} }
													onMonthPreviewed={ (v) => console.log(v) }
												/>
												<LabelWithWCTooltip tooltip={ labels.date_help }></LabelWithWCTooltip>
											</div>
										}
									</div>
								</div>
								</PanelRow>
							</PanelBody>
						)
					})
				}
			</>
        )
	}
}

export default ProductPerStock