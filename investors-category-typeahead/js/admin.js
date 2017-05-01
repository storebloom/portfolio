/**
 * Investors Category Typeahead.
 */

// Make sure the wp object exists.
window.wp = window.wp || {};

/* exported InvestorsCategoryTypeahead */
var InvestorsCategoryTypeahead = ( function( $, wp ) {
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
		 *
		 * @param data
		 */
		init: function( data ) {
			this.$container = $( '#taxonomy-category' );
			this.addResultWrapper();
			this.listen();
		},

		/**
		 * Initiate listeners.
		 */
		listen: function() {
			var self = this;
			var timer = '';

			// Send user input to category search AFTER they stop typing.
			this.$container.on( 'keyup', 'input#cat-ta', function( e ) {
				clearTimeout( timer );

				timer = setTimeout( function() {
					self.returnCatResults( $( this ).val() );
				}.bind( this ), 500 );
			} );

			// Select a category from results selected the original.
			this.$container.on( 'click', '.ta-cat-item', function( e ) {
				self.selectOriginalCat( $( this ) );
			} );
		},

		/**
		 * Select the category chosen from the cat results.
		 *
		 * @param $category
		 */
		selectOriginalCat: function( $category ) {
			var catId = $category.data( 'cat-id' );

			// Select the category option that matches the category id.
			this.$container.find( 'option[value="' + catId + '"]' ).prop( 'selected', true );

			// Select original metabox style category if exists.
			this.$container.find( 'input[id*="in-category-' + catId + '"]' ).prop( 'checked', true );

			// Hide and clear the results.
			this.$resultWrapper.html( '' ).hide();

			$( 'input#cat-ta' ).val( '' );
		},

		/**
		 * Append the input & wrapper div that will display results.
		 */
		addResultWrapper: function() {
			this.$container.append( '<input id="cat-ta" type="text" placeholder="' + this.data.searchPlaceholder + '" />' ).append( '<ul id="cat-result-wrapper"></ul>' );
			this.$resultWrapper = $( '#cat-result-wrapper' );
		},

		/**
		 * Send input value and return LIKE categories.
		 *
		 * @param key
		 */
		returnCatResults: function( key ) {
			// Send key to search function and return results if not empty.
			wp.ajax.post( 'return_categories', {
				key: key,
				nonce: this.data.nonce,
			} ).always( function( results ) {
				if ( '' !== results ) {
					this.$resultWrapper.show().html( results );
				} else {
					this.$resultWrapper.hide();
				}
			}.bind( this ) );
		},
	};
} )( window.jQuery, window.wp );
