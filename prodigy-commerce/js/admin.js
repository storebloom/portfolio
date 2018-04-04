/**
 * ProdigyCommerce
 *
 * @package ProdigyCommerce
 */

/* exported ProdigyCommerce */
var ProdigyCommerce = ( function( $, wp ) {
	'use strict';

	return {
		/**
		 * Holds data.
		 */
		data: {},

		/**
		 * Boot plugin.
		 *
		 * @param data
		 */
		boot: function( data ) {
			this.data = data;

			$( document ).ready( function() {
				this.init();
			}.bind( this ) );
		},

		/**
		 * Initialize plugin.
		 */
		init: function() {
			this.$metaContainer = $( '#pc-meta-box-wrap' );
			this.$searchContainer = $( '.shop-wrapper' );
			this.$imageContainer = $( '#postimagediv' );
			this.$widgetContainer = $( '.widgets-holder-wrap' );
			this.$shortcodeGen = $( '.pc-shortcode-list' );
			this.$adminContainer = $( '.wp-admin' );
			this.colorPicker();
			this.listen();
		},

		/**
		 * Event listener.
		 */
		listen: function() {
			var self = this,
				timer = '';

			this.$metaContainer.on( 'click', '.nav-tab-wrapper .nav-tab', function() {
				self.selectTab( this );
			} );

			// Send user input to page search AFTER they stop typing.
			this.$searchContainer.on( 'keyup', '#prodigy-commerce_shop-page, #prodigy-commerce_cart-page', function( e ) {
				var result = $( '#shop-page-result-wrapper' ),
					id = $( this ).attr( 'id' ),
					type = 'shop-page';

				if ( 'prodigy-commerce_cart-page' === id ) {
					result = $( '#cart-page-result-wrapper' );
					type = 'cart-page';
				}

				clearTimeout( timer );

				timer = setTimeout( function() {
					self.returnResults( $( this ).val(), type, result );
				}.bind( this ), 500 );
			} );

			// Send user input to category or tag search AFTER they stop typing.
			this.$widgetContainer.on( 'keyup', '#prodigy-commerce_widget-category, #prodigy-commerce_widget-tag', function( e ) {
				var result,
					id = $( this ).attr( 'id' ),
					type,
					value = $( this ).val();

				if ( 'prodigy-commerce_widget-category' === id ) {
					result =  $( this ).closest( 'p' ).siblings( '#category-widget-result-wrapper' );
					type = 'widget-category';
				}

				if ( 'prodigy-commerce_widget-tag' === id ) {
					result = $( this ).closest( 'p' ).siblings( '#tag-widget-result-wrapper' );
					type = 'widget-tag';
				}

				clearTimeout( timer );

				timer = setTimeout( function() {
					self.returnResults( value, type, result );
				}.bind( this ), 500 );
			} );

			// Select page to replace shop default.
			this.$searchContainer.on( 'click', '.ta-shop-page-item, .ta-cart-page-item', function() {
				var pageid = $( this ).attr( 'data-id' ),
					pagename = $( this ).html(),
					type = 'shop-page';

				if ( $( this ).hasClass( 'ta-cart-page-item' ) ) {
					type = 'cart-page';
				}

				self.selectShop( pageid, pagename, type, '' );
			} );

			// Select page to replace shop default.
			this.$widgetContainer.on( 'click', '.ta-widget-category-item, .ta-widget-tag-item', function() {
				var pageid = $( this ).attr( 'data-id' ),
					pagename = $( this ).html(),
					type,
					target = $( this ).closest( '#category-widget-result-wrapper' ).siblings( 'p' ).find( '#prodigy-commerce_widget-category' );

				if ( $( this ).hasClass( 'ta-widget-tag-item') ) {
					type = $( this ).closest( '#tag-widget-result-wrapper' );
					target = $( this ).closest( '#tag-widget-result-wrapper' ).siblings( 'p' ).find( '#prodigy-commerce_widget-tag' );
				}

				self.selectShop( pageid, pagename, type, target );
			} );

			// Force search when search icon is clicked.
			$( 'body' ).on( 'click', '.search-st-icon', function() {
				var result = $( '#shop-page-result-wrapper' ),
					type = 'shop-page',
					id = $( this ).attr( 'id' ),
					value = $( '#prodigy-commerce_shop-page' ).val();

				if ( 'pc-cart-page-search' === id ) {
					result = $( '#cart-page-result-wrapper' );
					type = 'cart-page';
					value = $( '#prodigy-commerce_cart-page' );
				}

				if ( 'pc-widget-category-search' === id ) {
					result =  $( this ).closest( 'p' ).siblings( '#category-widget-result-wrapper' );
					type = 'widget-category';
					value = $( this ).siblings( '#prodigy-commerce_widget-category' ).val();
				}

				if ( 'pc-widget-tag-search' === id ) {
					result = $( this ).closest( 'p' ).siblings( '#tag-widget-result-wrapper' );
					type = 'widget-tag';
				}

				self.returnResults( value, type, result );
			} );

			// Popout tax code legend.
			this.$metaContainer.on( 'click', '#tax-legend', function() {
				$( '#tax-legend-popout' ).show();
			} );

			// Close tax code legend.
			this.$metaContainer.on( 'click', '#close-tax-legend', function() {
				$( '#tax-legend-popout' ).hide();
			} );

			// Open media library.
			this.$imageContainer.on( 'click', '.upload-add-image', function() {
				var field = $( this ).siblings( 'input' ).attr( 'id' );

				self.additionalImages( field );
			} );

			// Open media library product cat.
			this.$adminContainer.on( 'click', '#product-cat-img', function() {
				self.categoryImages();
			} );

			// Remove additional image.
			this.$imageContainer.on( 'click', '.pc-remove-additional-image', function() {
				$( this ).parent( '.pc-additional-image' ).remove();
			} );

			// Toggle shortcode generator items.
			this.$shortcodeGen.on( 'click', '.pc-shortcode-accor-wrap', function() {
				var type = $( this ).find( '.accor-arrow' );

				self.updateAccors( type.html(), type );
			} );

			// Generate shortcode with attributes.
			this.$shortcodeGen.on( 'click', '.pc-generate-shortcode', function() {
				var shortCode = $( this ).closest( '.pc-shortcode-options' );

				self.generateShortcode( shortCode.siblings( '.pc-shortcode-accor-wrap' ).find( '.pc-the-shortcode .pc-shortcode-value' ).attr( 'id' ), shortCode );
			} );

			// Copy text from read only input fields.
			$( 'body' ).on( 'click', '#ssba-copy-shortcode', function() {
				self.copyText( $( '.ssba-buttons-shortcode' ) );
			} );

			// Copy shortcode.
			this.$shortcodeGen.on( 'click', '.pc-copy-shortcode', function( e ) {
				e.stopPropagation();

				var shortCode = $( this ).siblings( '.pc-shortcode-value' );

				self.copyText( shortCode );
			} );
		},

		/**
		 * Whenever each of the navigation tabs is clicked, check to see if it has the 'nav-tab-active'
		 * class name. If not, then mark it as active; otherwise, don't do anything (as it's already
		 * marked as active.
		 *
		 * Next, when a new tab is marked as active, the corresponding child view needs to be marked
		 * as visible. We do this by toggling the 'hidden' class attribute of the corresponding variables.
		 */
		selectTab: function( tab ) {
			var tabIndex;

			// If this tab is not active.
			if ( !$( tab ).hasClass( 'nav-tab-active' ) ) {

				// Unmark the current tab and mark the new one as active.
				$( '.nav-tab-active' ).removeClass( 'nav-tab-active' );
				$( tab ).addClass( 'nav-tab-active' );

				// Save the index of the tab that's just been marked as active. It will be 0 - 3.
				tabIndex = $( tab ).index();

				// Hide the old active content.
				this.$metaContainer
					.children( 'div:not( .inside.hidden )' )
					.addClass( 'hidden' );

				this.$metaContainer
					.children( 'div:nth-child(' + ( tabIndex ) + ')' )
					.addClass( 'hidden' );

				// And display the new content.
				this.$metaContainer
					.children( 'div:nth-child( ' + ( tabIndex + 2 ) + ')' )
					.removeClass( 'hidden' );
			}
		},

		/**
		 * Send input value and return LIKE categories/pages.
		 *
		 * @param key
		 * @param result
		 */
		returnResults: function( key, type, result ) {
			wp.ajax.post( 'return_pages', {
				key: key,
				type: type,
				nonce: this.data.nonce
			} ).always( function( results ) {
				if ( '' !== results ) {
					result.show().html( results );
				} else {
					result.hide();
				}
			}.bind( this ) );
		},

		/**
		 * Select the shop page to insert into text field.
		 *
		 * @param pageid
		 * @param pagename
		 * @param type
		 * @param target
		 */
		selectShop: function( pageid, pagename, type, target ) {
			if ( 'shop-page' === type || 'cart-page' === type ) {
				$( '#' + type + '-search-value' ).val( pagename ).attr( 'name', 'prodigy-commerce_' + type + '[' + pageid + ']' );
				$( '#prodigy-commerce_' + type ).val( pagename );
				$( '#' + type + '-result-wrapper' ).hide();
			} else {
				type.hide();
				$( target ).val( pagename ).siblings( 'input' ).val( pagename );
			}
		},


		/**
		 * Open the media libray and plae image url in new image slot.
		 *
		 * @param field
		 */
		additionalImages: function( field ) {
			var customUploader = wp.media.frames.file_frame = wp.media( {
				title: 'Add Image',
				button: {
					text: 'Add Image'
				},
				multiple: true
			} );

			customUploader.on( 'select', function() {
				var attachment = customUploader.state().get( 'selection' ).toJSON(),
					currentCount = $( '#pc-additional-image-wrap .pc-additional-image' ).length,
					imageUrl, image, imageField, additionalField, num, newCount, name, id;

				for ( var iii = 0; iii < attachment.length; iii++ ) {
					imageUrl = attachment[iii].url,
					additionalField = '<div class="pc-additional-image"><span class="pc-remove-additional-image">x</span>';
					image = '<img src="' + imageUrl + '">';
					num = iii + currentCount + 1;
					imageField = '<input type="hidden" name="_pc_product_image[' + num + ']" id="pc-product-image-' + num + '" value="' + imageUrl + '"></div>';

					$( '#pc-additional-image-wrap' ).append( additionalField + image + imageField );
				}

				newCount = $( '#pc-additional-image-wrap .pc-additional-image' ).length;

				$( '#pc-additional-image-wrap .pc-additional-image' ).each( function( index, value ) {
					name = '_pc_product_image[' + index + ']';
					id = 'pc-product-image-' + index;

					$( this ).find( 'input' ).attr( 'name', name ).attr( 'id', id );
				} );
			} );

			customUploader.open();
		},

		/**
		 * Open the media libray and place image url in new image slot for product cats.
		 */
		categoryImages: function() {
			var customUploader = wp.media.frames.file_frame = wp.media( {
				title: 'Add Image',
				button: {
					text: 'Add Image'
				},
				multiple: false
			} );

			customUploader.on( 'select', function() {
				var attachment = customUploader.state().get( 'selection' ).first().toJSON();

				$( '#product_category_image input' ).val( attachment.url );
			} );

			customUploader.open();
		},

		/**
		 * Invoke colorpicker to color setting field.
		 */
		colorPicker: function() {
			$( '#add-to-cart-color' ).wpColorPicker( {
				change: function( event, ui ) {
					$( '#add-to-cart-color' ).attr( 'value', ui.color.toString() );
				}
			} );

			$( '#add-to-cart-font-color' ).wpColorPicker( {
				change: function( event, ui ) {
					$( '#add-to-cart-font-color' ).attr( 'value', ui.color.toString() );
				}
			} );
		},

		/**
		 * Toggle the accordions.
		 *
		 * @param type
		 * @param arrow
		 */
		updateAccors: function( type, arrow ) {
			var closestButton = $( arrow ).closest( '.pc-shortcode-list-items' );

			if ( '►' === type ) {

				// Show the button configs.
				closestButton.find( '.pc-shortcode-options' ).slideDown();

				// Change the icon next to title.
				closestButton.find( '.accor-arrow' ).html( '&#9660;' );
			} else {

				// Show the button configs.
				closestButton.find( '.pc-shortcode-options' ).slideUp();

				// Change the icon next to title.
				closestButton.find( '.accor-arrow' ).html( '&#9658;' );
			}
		},

		/**
		 * Generate the short code code with selected attributes.
		 *
		 * @param shortCode
		 * @param options
		 */
		generateShortcode: function( shortCode, options ) {
			var code = options.siblings( '.pc-shortcode-accor-wrap' ).find( '.pc-the-shortcode input' ),
				count, thumb, cat, tag, cart, price, sale, id, additional, width, height, short,
				dropdown, showcount, showsubtotal, icon, showCat;

			if ( 'get-pc-products' === shortCode ) {
				count = options.find( 'ul li #get-pc-products-count' ).val();
				thumb = options.find( 'ul li #get-pc-products-thumbnail-width' ).val();
				cat = options.find( 'ul li #get-pc-products-category' ).val();
				tag = options.find( 'ul li #get-pc-products-tag' ).val();
				cart = options.find( 'ul li #get-pc-products-cart-button' ).is( ':checked' );
				price = options.find( 'ul li #get-pc-products-price' ).is( ':checked' );
				sale = options.find( 'ul li #get-pc-products-sale-items' ).is( ':checked' );
				showCat = options.find( 'ul li #get-pc-products-show-categories' ).is( ':checked' );

				if ( '' !== count ) {
					count = ' count=' + count;
				}

				if ( '' !== thumb ) {
					thumb = ' thumbnail-width=' + thumb + 'px';
				}

				if ( '' !== cat ) {
					cat = ' category=' + cat;
				}

				if ( '' !== tag ) {
					tag = ' tag=' + tag;
				}

				if ( ! cart ) {
					cart = ' cart-button=false';
				} else {
					cart = '';
				}

				if ( ! price ) {
					price = ' price=false';
				} else {
					price = '';
				}

				if ( sale ) {
					sale = ' sale-items=true';
				} else {
					sale = '';
				}

				if ( showCat ) {
					showCat = ' show-categories=true';
				} else {
					showCat = '';
				}

				code.attr( 'value', '[get-pc-products' + count + thumb + cat + tag + cart + price + sale + showCat + ']' );
			}

			if ( 'get-pc-thumbnail' === shortCode ) {
				id = options.find( 'ul li #get-pc-thumbnail-id' ).val();
				additional = options.find( 'ul li #get-pc-thumbnail-additional-images' ).is(':checked');
				width = options.find( 'ul li #get-pc-thumbnail-width' ).val();
				height = options.find( 'ul li #get-pc-thumbnail-height' ).val();

				if ( '' !== id ) {
					id = ' id=' + id;
				} else {
					id = '';
				}

				if ( additional ) {
					additional = ' additional-images=true';
				} else {
					additional = '';
				}

				if ( '' !== width ) {
					width = ' width=' + width + 'px';
				} else {
					width = '';
				}

				if ( '' !== height ) {
					height = ' height=' + height + 'px';
				} else {
					height = '';
				}

				code.attr( 'value', '[get-pc-thumbnail' + id + additional + width + height + ']' );
			}

			if ( 'get-pc-description' === shortCode ) {
				id = options.find( 'ul li #get-pc-description-id' ).val();
				short = options.find( 'ul li #get-pc-description-short' ).is(':checked');

				if ( '' !== id ) {
					id = ' id=' + id;
				} else {
					id = '';
				}

				if ( short ) {
					short = ' short=true';
				} else {
					short = '';
				}

				code.attr( 'value', '[get-pc-description' + id + short + ']' );
			}

			if ( 'get-pc-price' === shortCode ) {
				id = options.find( 'ul li #get-pc-price-id' ).val();

				if ( '' !== id ) {
					id = ' id=' + id;
				} else {
					id = '';
				}

				code.attr( 'value', '[get-pc-price' + id + ']' );
			}

			if ( 'get-pc-cart-button' === shortCode ) {
				id = options.find( 'ul li #get-pc-cart-button-id' ).val();

				if ( '' !== id ) {
					id = ' id=' + id;
				} else {
					id = '';
				}

				code.attr( 'value', '[get-pc-cart-button' + id + ']' );
			}

			if ( 'get-pc-cart' === shortCode ) {
				dropdown = options.find( 'ul li #get-pc-cart-dropdown' ).is( ':checked' );
				showcount = options.find( 'ul li #get-pc-cart-showcount' ).is( ':checked' );
				showsubtotal = options.find( 'ul li #get-pc-cart-showsubtotal' ).is( ':checked' );
				icon = options.find( 'ul li #get-pc-cart-icon' ).val();

				if ( dropdown ) {
					dropdown = ' dropdown=true';
				} else {
					dropdown = '';
				}

				if ( showcount ) {
					showcount = ' showcount=true';
				} else {
					showcount = '';
				}

				if ( showsubtotal ) {
					showsubtotal = ' showsubtotal=true';
				} else {
					showsubtotal = '';
				}
				if ( '' !== icon ) {
					icon = ' icon=' + icon;
				} else {
					icon = '';
				}

				code.attr( 'value', '[get-pc-cart' + dropdown + showcount + showsubtotal + icon + ']' );
			}
		},

		/**
		 * Copy the shortcode specified.
		 *
		 * @param shortCode
		 */
		copyText: function( shortCode ) {
			shortCode.select();
			document.execCommand( 'copy' );
		}
	};
} )( window.jQuery, window.wp );
