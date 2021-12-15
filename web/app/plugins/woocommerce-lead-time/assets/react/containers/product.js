/**
 * External dependencies
 */
import { Component } from '@wordpress/element';
import { 
	DatePicker,
} from '@wordpress/components';
import moment from 'moment';
import { isEmpty } from 'lodash';

const hiddenDateElement = jQuery( '#_wclt_lead_time_date' );
const initialDateTimeStamp = hiddenDateElement.val()
const leadTimeTextField = jQuery( '#_wclt_lead_time' );

import LabelWithWCTooltip from '../components/label-with-wc-tooltip'

class Product extends Component {

	constructor( props ) {
		super( props );

		this.state = {
			is_open: null,
			date: initialDateTimeStamp,
		}
	}

	/**
	 * Dirty way of rendering the calendar.
	 * 
	 * Because we're using the DatePicker component, the calendar does not display
	 * properly because the inventory tab is not initially visible, therefore
	 * the calendar's styling is broken because it's not in the DOM.
	 */
	componentDidMount() {
		const instance = this

		jQuery('.inventory_tab a').on( "click", function() {
            const formatElement = jQuery( '#_wclt_lead_time_format' )
			const initialFormatValue = jQuery( '#_wclt_lead_time_format option:selected' ).val()
			
			if ( initialFormatValue === 'dynamic' ) {
				instance.setState( { is_open: true } )
				leadTimeTextField.parent().hide()
			} else {
				instance.setState( { is_open: false } )
				leadTimeTextField.parent().show()
			}

			formatElement.on( "change", function() {
				if ( this.value === 'static' ) {
					instance.setState( { is_open: false } )
					leadTimeTextField.parent().show()
				} else {
					instance.setState( { is_open: true } )
					leadTimeTextField.parent().hide()
				}
			});
        });
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

	render() {
		const {
			is_open,
			date
		} = this.state

		var pickerDate = ''

		if ( ! isEmpty( date ) ) {
			pickerDate = this.getFormattedDate( date )
		}

        return(
            <>
				{ is_open && 
					<div className="form-field" style={ { padding: '5px 20px 5px 162px' } }>
						<label htmlFor="_wclt_lead_time_date">{ WCLT_Product.labels.date }</label>

						<div className="wclt-picker-wrapper">
							<DatePicker
								currentDate={ pickerDate }
								onChange={ ( newDate ) => {
									this.setState( { date: moment(newDate).format( 'X' ) } )
									hiddenDateElement.val( moment(newDate).format( 'X' ) )
								} }
								onMonthPreviewed={ (v) => console.log(v) }
							/>
							<LabelWithWCTooltip tooltip={ WCLT_Product.labels.date_help }></LabelWithWCTooltip>
						</div>
					</div> 
				}
			</>
        )
	}
}

export default Product