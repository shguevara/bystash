/**
 * External dependencies
 */
import { Component } from '@wordpress/element';
import CategoryStockStatusesList from '../components/category-stock-statuses-list';
import CategoryFields from '../components/category-fields';
import CategoryFieldsTable from '../components/category-fields-table';

const CategoryConfig = WCLT_Category
const CategoryDataInputHolder = jQuery( '[name=wclt_category_react]' )

const CategoryFormatInputHolder = jQuery( '[name=wclt_lead_time_format]' )
const CategoryTextInputHolder = jQuery( '[name=wclt_lead_time]' )
const CategoryDateInputHolder = jQuery( '[name=wclt_lead_time_date]' )

class Category extends Component {

	constructor( props ) {
		super( props );
		this.updateData = this.updateData.bind( this )
        this.updateCategoryFormat = this.updateCategoryFormat.bind( this )
        this.updateCategoryText = this.updateCategoryText.bind( this )
        this.updateCategoryDate = this.updateCategoryDate.bind( this )

		this.state = {
			data: CategoryConfig.data, // holds the per stock status data
            per_stock_enabled: CategoryConfig.per_stock_enabled, // CategoryConfig.per_stock_enabled
            is_edit_page: jQuery( 'form#edittag' ).length > 0,
            category_format: jQuery( 'form#edittag' ).length > 0 ? CategoryConfig.singular_data.format : 'static', // holds the data for categories when the per stock setting is disabed 
            category_text: jQuery( 'form#edittag' ).length > 0 ? CategoryConfig.singular_data.text : '', // holds the data for categories when the per stock setting is disabed
            category_date: jQuery( 'form#edittag' ).length > 0 ? CategoryConfig.singular_data.date : '', // holds the data for categories when the per stock setting is disabed
		}
	}

    /**
     * On page load: 
     * - immediately print the json data if on the edit page
     */
	componentDidMount() {
        if ( this.state.is_edit_page === true ) {
            CategoryDataInputHolder.val( JSON.stringify( this.state.data ) )
            CategoryFormatInputHolder.val( this.state.category_format )
            CategoryTextInputHolder.val( this.state.category_text )
            CategoryDateInputHolder.val( this.state.category_date )
        }
	}

    /**
     * On update:
     * - update the json data within the hidden input holder.
     */
	componentDidUpdate() {
        CategoryDataInputHolder.val( JSON.stringify( this.state.data ) )
        CategoryFormatInputHolder.val( this.state.category_format )
        CategoryTextInputHolder.val( this.state.category_text )
        CategoryDateInputHolder.val( this.state.category_date )
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
     * Update the format when not using the per stock option.
     * @param {string} value 
     */
    updateCategoryFormat( value ) {
        this.setState( { category_format: value } );
    }

    /**
     * Update the lead text when nto using the per stock option.
     * @param {string} value 
     */
    updateCategoryText( value ) {
        this.setState( { category_text: value } );
    }

    /**
     * Update the date when not using the per stock option.
     * @param {string} value 
     */
    updateCategoryDate( value ) {
        this.setState( { category_date: value } );
    }

	render() {

		const {
			data,
            per_stock_enabled,
            is_edit_page,
            category_format,
            category_date,
            category_text
		} = this.state

        if ( is_edit_page ) {
            return per_stock_enabled 
                ? 
                <tr className="form-field lead-time-format-wrap">
                    <th scope="row" valign="top">
                        <label>
                            { WCLT_Category.labels.title }
                        </label>
                    </th>
                    <td>
                        <div id="wclt-category-editor">
                            <CategoryStockStatusesList statuses={ WCLT_Category.statuses } updateData={ this.updateData } data={ data } />
                        </div>
                    </td>
                </tr> 
                : 
                <CategoryFieldsTable
                    updateCategoryDate={ this.updateCategoryDate }
                    updateCategoryFormat={ this.updateCategoryFormat }
                    updateCategoryText={ this.updateCategoryText }
                    data={ {
                        'format': category_format,
                        'date': category_date,
                        'text': category_text
                    } }
                />
        } else {
            return (
                <div id="wclt-category-editor">
                    { 
                        per_stock_enabled 
                        ? 
                        <>
                            <h2>{ WCLT_Category.labels.title }</h2>
                            <CategoryStockStatusesList statuses={ WCLT_Category.statuses } updateData={ this.updateData } data={ data } />
                        </>
                        : 
                        <CategoryFields 
                            is_edit_page={ is_edit_page } 
                            updateCategoryDate={ this.updateCategoryDate }
                            updateCategoryFormat={ this.updateCategoryFormat }
                            updateCategoryText={ this.updateCategoryText }
                        />
                    }
                </div>
            );
        }
	}
}

export default Category
