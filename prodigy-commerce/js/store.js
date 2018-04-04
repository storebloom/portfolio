/**
 * Store.
 *
 * @package ProdigyCommerce
 */

/* exported Store */
var Store = ( function( $ ) {
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
			this.$pdpImageConatainer = $( '.pc-pdp-image' );
			this.$paginationContainer = $( '.pc-pagination-wrap' );
			this.listen();
		},

		/**
		 * Event listener.
		 */
		listen: function() {
			var self = this;

			// Switch main pdp image when a thumbnail is clicked.
			this.$pdpImageConatainer.on( 'click', '.pdp-thumbnail img', function() {
				var newUrl = $( this ).attr( 'src' );

				self.swapMainImage( newUrl );
			} );

			// Click on page number in pagination.
			this.$paginationContainer.on( 'click', '.pc-page-number', function() {
				var pageNumber = $( this ).html();

				self.returnNewPage( pageNumber );
			} );

			// Click on next page icon.
			this.$paginationContainer.on( 'click', '.pc-next-page', function() {
				var currentPage = $( '.pc-current-page' ).html(),
					nextPage = parseInt( currentPage ) + 1 || 0,
					pageCount = $( '.pc-pagination-wrap ul li' ).length;

					if ( nextPage > pageCount ) {
						nextPage = 1;
					}

				self.returnNewPage( nextPage );
			} );
		},

		/**
		 * Swap thumbnail url with main image url.
		 *
		 * @param newUrl
		 */
		swapMainImage: function( newUrl ) {
			$( '#main-pdp-image' ).attr( 'src', newUrl );
		},

		/**
		 * Return selected page of products.
		 *
		 * @param pageNumber
		 */
		returnNewPage: function( pageNumber ) {

			// Change current page class.
			$( '.pc-pagination-wrap ul li' ).removeClass( 'pc-current-page' );
			$( "li.pc-page-number:contains('" + pageNumber + "')" ).addClass( 'pc-current-page' );

			// Get products for new page.
			wp.ajax.post( 'get_pc_products', {
				count: pageNumber,
				nonce: this.data.nonce
			} ).always( function( response ) {
				$( '.pc-product-list' ).html( response );
			} );
		}
	};
} )( window.jQuery );
