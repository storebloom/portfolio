/**
 * Top Story.
 */

// Make sure the wp object exists.
window.wp = window.wp || {};

/* exported TopStory */
var TopStory = ( function( $, wp ) {
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
		init: function() {
			this.$container = $( '.form-table' );
			this.$resultWrapper = $( '.top-story-result-wrapper' );
			this.$optionWrapper = $( '#top-story-value' );

			this.listen();
		},

		/**
		 * Initiate listeners.
		 */
		listen: function() {
			var self = this,
				timer = '';

			// Send user input to post search AFTER they stop typing.
			this.$container.on( 'keyup', 'input#top-story-value', function() {
				clearTimeout( timer );

				timer = setTimeout( function() {
					self.returnPostResults( $( this ).val() );
				}.bind( this ), 500 );
			} );

			// Select a post to add to option field.
			this.$resultWrapper.on( 'click', '.top-story-item', function() {
				self.$optionWrapper.val( $( this ).html() );
				self.$resultWrapper.hide();
			} );
		},

		/**
		 * Send input value and return LIKE categories.
		 *
		 * @param key
		 */
		returnPostResults: function( key ) {

			// Send key to search function and return results if not empty.
			wp.ajax.post( 'return_posts', {
				key: key,
				nonce: this.data.nonce,
			} ).always( function( results ) {
				if ( '' !== results ) {
					this.$resultWrapper.show();
					$( '.top-story-results' ).html( results );
				} else {
					this.$resultWrapper.hide();
				}
			}.bind( this ) );
		},
	};
} )( window.jQuery, window.wp );
