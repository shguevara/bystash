/**
 * AutomateWoo main - loaded on every admin page
 */

var AutomateWoo, AW = {};

(function($) {

	AW.init = function() {

		AW.params = automatewooLocalizeScript;

		AW.initTooltips();
		AW.initWorkflowStatusSwitch();
		AW.initShowHide();
		AW.initHoverableDates();

        $(document.body).on('wc-enhanced-select-init', function() {
			AW.initEnhancedSelects();
		});
	};


	/**
	 * Init tool tips
	 */
	AW.initTooltips = function () {
		$( '.automatewoo-help-tip, .automatewoo-tiptip' ).tipTip({
			attribute: 'data-tip',
			fadeIn: 50,
			fadeOut: 50,
			delay: 200
		});
	};


	/**
	 * Ajax search search box
	 */
	AW.initEnhancedSelects = function() {

		$( 'select.automatewoo-json-search' ).filter( ':not(.enhanced)' ).each( function() {
			var select2_args = {
				allowClear:  $( this ).data( 'allow_clear' ) ? true : false,
				placeholder: $( this ).data( 'placeholder' ),
				minimumInputLength: '1',
				escapeMarkup: function( m ) {
					return m;
				},
				ajax: {
					url: AW.params.url.ajax,
					dataType: 'json',
					quietMillis: 250,
					data: function( params ) {

						var data = {
							term: params.term,
							action: $( this ).data( 'action' )
						};

						// pass in sibling field data
						var sibling = $(this).data('pass-sibling');
						if ( sibling ) {
							var $sibling = $('[name="'+ sibling+ '"]');

							if ( $sibling.length ) {
								data['sibling'] = $sibling.val()
							}
						}

						return data;
					},
					processResults: function( data ) {
						var terms = [];
						if ( data ) {
							$.each( data, function( id, text ) {
								terms.push( { id: id, text: text } );
							});
						}
						return {
							results: terms
						};
					},
					cache: true
				}
			};

			$( this ).select2( select2_args ).addClass( 'enhanced' );
		});

	};


	AW.initWorkflowStatusSwitch = function() {

		$('.aw-switch.js-toggle-workflow-status').on( 'click', function(){

			var $switch, state, new_state;

			$switch = $(this);

			if ( $switch.is('.aw-loading') )
				return;

			state = $switch.attr( 'data-aw-switch' );
			new_state = state === 'on' ? 'off' : 'on';

			$switch.addClass('aw-loading');
			$switch.attr( 'data-aw-switch', new_state );

			$.post( ajaxurl, {
				action: 'aw_toggle_workflow_status',
				workflow_id: $switch.attr( 'data-workflow-id' ),
				new_state: new_state
			}, function() {
				$switch.removeClass('aw-loading');
			});

		});
	};


	/**
	 * @param float
	 * @return string
	 */
	AW.price = function( float ) {

		var price = float.toFixed(2);
		var symbol = AW.params.locale.currency_symbol;

		switch ( AW.params.locale.currency_position ) {
			case 'right':
				price = price + symbol;
				break;
			case 'right_space':
				price = price + ' ' + symbol;
				break;
			case 'left':
				price = symbol + price;
				break;
			case 'left_space':
			default:
				price = symbol + ' ' + price;
				break;
		}

		return price;
	};


	AW.block = function( $el ) {
        $el.block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });
	};


	/**
	 * Show / hide logic with data attributes
	 */
	AW.initShowHide = function() {

		var update = function( $el ) {
			var id = $el.data( 'automatewoo-bind' );
			var value = $el.val();
			var is_checkbox = $el.is('input[type="checkbox"]');

			$('[data-automatewoo-show]').each(function() {
				if ( is_checkbox && $(this).data('automatewoo-show') === id ) {
					if ( $el.is(':checked') ) {
                        $(this).show();
					}
					else {
                        $(this).hide();
					}
				}
				else {
                    var logic = $(this).data('automatewoo-show').split('=');

                    if ( logic[0] !== id ) {
                        return;
                    }

                    var possible_values = logic[1].split('|');

                    if ( possible_values.indexOf( value ) !== -1 ) {
                        $(this).show();
                    }
                    else {
                        $(this).hide();
                    }
				}
			});


            $('[data-automatewoo-hide]').each(function() {
                if ( is_checkbox && $(this).data('automatewoo-hide') === id ) {
                    if ( $el.is(':checked') ) {
                        $(this).hide();
                    }
                    else {
                        $(this).show();
                    }
                }
                else {
                    var logic = $(this).data('automatewoo-hide').split('=');

                    if ( logic[0] !== id ) {
                        return;
                    }

                    var possible_values = logic[1].split('|');

                    if ( possible_values.indexOf( value ) !== -1 ) {
                        $(this).hide();
                    }
                    else {
                        $(this).show();
                    }
				}
            });
		};


		$(document).on( 'change', '[data-automatewoo-bind]', function() {
			update( $(this) );
		});

		$('[data-automatewoo-bind]').each(function() {
			update( $(this) );
		});

	};

	AW.initHoverableDates = function() {
		var selector = '.automatewoo-hoverable-date';

		$( document.body )
			.on( 'mouseenter', selector, function() {
				$( this ).text( $( this ).data( 'automatewoo-date-no-diff' ) );
			} )
			.on( 'mouseleave', selector, function() {
				$( this ).text( $( this ).data( 'automatewoo-date-with-diff' ) );
			} )
		;
	};

	$(function() {
		AW.init();
	});


})( jQuery );



jQuery(function($) {


	AutomateWoo = {

		_email_preview_window: null,


		init: function() {
			this.init_notice_dismiss();
			this.init_date_pickers();
		},


		notices: {

			success: function( message, $location ) {
				if ( ! $location.length ) return;
				$location.before('<div class="automatewoo-notice updated fade"><p><strong>' + message + '</strong></p></div>');
			},

			error: function( message, $location ) {
				if ( ! $location.length ) return;
				$location.before('<div class="automatewoo-notice error fade"><p><strong>' + message + '</strong></p></div>');
			},

			clear_all: function() {
				$('.automatewoo-notice').slideUp();
			}

		},



		init_notice_dismiss: function(){

			$('.aw-notice-system-error').on('click', '.notice-dismiss', function(){
				$.ajax({
					url: ajaxurl,
					data: { action: 'aw_dismiss_system_error_notice' }
				});
			});

			$('[data-automatewoo-dismissible-notice]').on('click', '.notice-dismiss', function () {
				var $notice = $(this).parents('[data-automatewoo-dismissible-notice]');

				$.post({
					url: ajaxurl,
					data: {
						action: 'automatewoo_remove_notice',
						notice: $notice.data('automatewoo-dismissible-notice'),
						nonce: AW.params.nonces.remove_notice
					}
				});
			});
		},

		init_date_pickers: function() {
			$( '.automatewoo-date-picker' ).datepicker({
				dateFormat: 'yy-mm-dd',
				numberOfMonths: 1,
				showButtonPanel: true
			});
		},


		isEmailPreviewOpen: function() {
			return this._email_preview_window && ! this._email_preview_window.closed;
		},


		openLoadingEmailPreview: function() {
			this.openPreviewWindow( AW.params.url.admin + 'admin.php?page=automatewoo-preview&action=loading' )
		},


        /**
		 * @param type
		 * @param args
         */
		open_email_preview: function( type, args ) {
			var request = {
				page: 'automatewoo-preview',
				action: 'preview-ui',
				type: type,
				args: args
			};

			this.openPreviewWindow( AW.params.url.admin + 'admin.php?' + $.param( request ) );
		},


		/**
		 * @param url
         */
		openPreviewWindow: function( url ) {
			this._email_preview_window = window.open( url, 'automatewoo_preview', 'titlebar=no,toolbar=no,height=768,width=860,resizable=yes,status=no' );
		}


	};

	AutomateWoo.init();


	$( '.automatewoo-before-after-day-field-group__field--type' ).
		on( 'change', function() {
			const $type = $( this );
			const $days = $type.siblings(
				'.automatewoo-before-after-day-field-group__field--days',
			);

			if ( $type.val() === 'on_the_day' ) {
				$days.hide();
			}
			else {
				$days.show();
			}
		} ).
		trigger( 'change' );

});
