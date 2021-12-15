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

const statuses = WCLT_Product.statuses
const labels = WCLT_Product.labels

import LabelWithWCTooltip from '../components/label-with-wc-tooltip';

class ProductVariablePerStock extends Component {

	constructor( props ) {
		super( props );

		this.state = {
			data: null
		}
	}

	/**
	 * When the metabox heading title "h3" is clicked,
	 * preload the data accordingly.
	 */
	componentDidMount() {
		const {
			element
		} = this.props

		const loopID = jQuery( element ).data( 'loop-id' )
		const metaboxHeading = jQuery( element ).parent().parent().parent().parent().parent().find('h3')

		metaboxHeading.on( "click", () => {
			
			const hiddenInput = jQuery( '#wclt_variation_react_data_'+loopID )
			const initialValue = JSON.parse( hiddenInput.val() )

			if ( has( initialValue, 'data' ) ) {
				this.setState({ data: initialValue.data })
			}

        });

	}

	/**
	 * Simulate the need to update variations.
	 * 
	 * When the data is changed into the react state, the timestamp is stored into an hidden input field,
	 * however WC does not recognise that the hidden input has been changed,
	 * therefore we fake trigger a change to the dropdown to let WC know
	 * that variations have been changed and an update into the database is required.
	 * 
	 * @param {object} prevProps 
	 * @param {object} prevState 
	 */
	 componentDidUpdate( prevProps, prevState ) {
		const {
			element
		} = this.props

		const loopID = jQuery( element ).data( 'loop-id' )
		const stockStatusField = jQuery( '#variable_stock_status'+loopID )
		const hiddenInput = jQuery( '#wclt_variation_react_data_'+loopID )

		if ( prevState.data !== this.state.data ) {
			stockStatusField.trigger('change')
			hiddenInput.val( JSON.stringify( this.state.data ) )
		}
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
			data
		} = this.state

        return(
            <Panel>
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
								<PanelRow className="alt-no-flex-panel no-label-margin wclt-variation-wrapper">
                                    <div className="wclt-form-field">
                                        <LabelWithWCTooltip>{ labels.format }</LabelWithWCTooltip>
										<SelectControl
											value={ format }
											options={ labels.format_options }
											onChange={ ( newFormat ) => {
												this.updateData( status_id, 'format', newFormat )
											} }
										/>
									</div>
                                    { format === 'static' &&
										<div className="wclt-form-field">
                                            <LabelWithWCTooltip tooltip={ labels.text_help }>{ labels.text }</LabelWithWCTooltip>
											<TextControl
												value={ text }
												onChange={ (value) => this.updateData( status_id, 'text', value ) }
											/>
										</div>
									}
                                    { format === 'dynamic' && 
										<div className="wclt-form-field date-field">
											<LabelWithWCTooltip tooltip={ labels.date_help }>{ labels.date }</LabelWithWCTooltip>
											<DatePicker
												currentDate={ pickerDate }
												onChange={ ( newDate ) => {
													this.updateData( status_id, 'date', moment(newDate).format( 'X' ) )
												} }
												onMonthPreviewed={ (v) => console.log(v) }
											/>
										</div>
									}
								</PanelRow>
							</PanelBody>
						)
					})
				}
			</Panel>
        )
	}
}

export default ProductVariablePerStock