/**
 * External dependencies
 */
import { Component } from '@wordpress/element';
import { 
	DatePicker,
} from '@wordpress/components';
import moment from 'moment';
import { isEmpty } from 'lodash';

import LabelWithWCTooltip from '../components/label-with-wc-tooltip';

class ProductVariable extends Component {

	constructor( props ) {
		super( props );

		this.state = {
			is_open: null,
			date: '',
		}
	}

	/**
	 * When the metabox heading title "h3" is clicked,
	 * determine if the component should be displayed.
	 * 
	 * We need to do this because unfortunately the datepicker layout is broken
	 * when the element is not visible/loaded into the dom.
	 * 
	 * So we force the refresh of the component when clicking the heading.
	 */
	componentDidMount() {
		const {
			element
		} = this.props

		const instance = this
		const loopID = jQuery( element ).data( 'loop-id' )
		const metaboxHeading = jQuery( element ).parent().parent().parent().parent().parent().find('h3')
		const wcltFieldsWrapper = jQuery( element ).parent()
		const formatField = wcltFieldsWrapper.find( 'select' ) 
		const leadTimeTextField = wcltFieldsWrapper.find( 'input[type=text]' ).get(0)
		const hiddenDateTimeStamp = wcltFieldsWrapper.find( 'input[type=hidden]' ).get(0)

		metaboxHeading.on( "click", () => {
			const initialFormatValue = formatField.find('option:selected').val() || 'static'
			this.setState( { date: jQuery(hiddenDateTimeStamp).val() } )

            if ( initialFormatValue === 'dynamic' ) {
				this.setState( { is_open: true } )
				jQuery( leadTimeTextField ).parent().hide()
			} else {
				this.setState( { is_open: false } )
				jQuery( leadTimeTextField ).parent().show()
			}
        });

		formatField.on( "change", function() {
			if ( this.value === 'static' ) {
				instance.setState( { is_open: false } )
				jQuery( leadTimeTextField ).parent().show()
			} else {
				instance.setState( { is_open: true } )
				jQuery( leadTimeTextField ).parent().hide()
			}
		});
	}

	/**
	 * Simulate the need to update variations.
	 * 
	 * When the date is changed into the react state, the timestamp is stored into an hidden input field,
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

		const wcltFieldsWrapper = jQuery( element ).parent()
		const formatField = wcltFieldsWrapper.find( 'select' )

		if ( prevState.is_open !== this.state.is_open ) {
			formatField.trigger('change')
		}

		if ( prevState.date !== this.state.date ) {
			formatField.trigger('change')
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

	render() {

		const {
			element
		} = this.props

		const {
			is_open,
			date
		} = this.state

		var pickerDate = ''

		if ( ! isEmpty( date ) ) {
			pickerDate = this.getFormattedDate( date )
		}

		const wcltFieldsWrapper = jQuery( element ).parent()
		const hiddenDateTimeStamp = wcltFieldsWrapper.find( 'input[type=hidden]' ).get(0)

        return(
            <>
				{ is_open &&
					<div className="form-field wclt-variation-wrapper">
						<label>{ WCLT_Product.labels.date }</label>
						<LabelWithWCTooltip tooltip={ WCLT_Product.labels.date_help }></LabelWithWCTooltip>
						<div className="wclt-picker-wrapper">
							<DatePicker
								currentDate={ pickerDate }
								onChange={ ( newDate ) => {
									this.setState( { date: moment(newDate).format( 'X' ) } )
									jQuery(hiddenDateTimeStamp).val( moment(newDate).format( 'X' ) )
								} }
								onMonthPreviewed={ (v) => console.log(v) }
							/>
						</div>
					</div> 
				}
			</>
        )
	}
}

export default ProductVariable