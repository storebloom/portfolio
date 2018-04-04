/**
 * Cart
 *
 * @package ProdigyCommerce
 */

/* exported Cart */
var Cart = ( function( $, wp ) {
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
			this.$cartShortcode = $( '.pc-cart-shortcode-wrap' );
			this.listen();
		},

		/**
		 * Event listener.
		 */
		listen: function() {
			var self = this;

			// If user clicks an add to cart button.
			$( 'body' ).on( 'click', '.pc-add-to-cart button', function() {
				var id = $( this ).attr( 'id' );

				self.updateCart( id, '', 'POST', 1 );

				if ( self.data.toCart ) {
					window.location.href = self.data.cartUrl;
				}
			} );

			// Toggle to open and close dropdown cart.
			$( 'body' ).on( 'mouseenter', '.pc-cart-shortcode-wrap', function() {
				$( this ).find( '.pc-cart-dropdown' ).slideDown();
			} ).on( 'mouseleave', '.pc-cart-shortcode-wrap', function() {
				$( this ).find( '.pc-cart-dropdown' ).slideUp();
			} );

			// Remove item from cart.
			$( 'body' ).on( 'click', '.pc-line-item-delete', function() {
				var id = $( this ).attr( 'id' );

				self.updateCart( '', id, 'REMOVE', 0 );
				$( this ).closest( 'li' ).remove();
			} );

			// Update quantity.
			$( 'body' ).on( 'change', '.pc-cart-quantity-control', function() {
				var value = $( this ).val(),
					id = $( this ).parent( '.pc-cart-item-price' ).siblings( '.pc-line-item-delete' ).attr( 'id' ),
					extid = $( this ).attr( 'id' );

				self.updateCart( extid, id, 'UPDATE', value, this );
			} );
		},

		/**
		 * Update current cart.
		 *
		 * @param extid
		 * @param id
		 * @param method
		 * @param quantity
		 */
		updateCart: function( extid, id, method, quantity ) {
			var newQuantity;
			this.cartLoading( 'start' );

			// Item is already in cart.  Update quantity.
			if ( 'POST' === method && 1 === $( '.pc-cart-item-price #' + extid ).length ) {
				newQuantity = $( '#' + extid ).val();
				id = $( '.pc-cart-item-price #' + extid ).closest( '.pc-cart-item-price' ).siblings( '.pc-line-item-delete' ).attr( 'id' );
				method = 'UPDATE';
				quantity = parseInt( newQuantity ) + 1;
			}

			// Check if current cart cookie exists.
			if ( 'POST' === method && this.getCookie( 'pc_cart' ) !== '' ) {
				method = 'PUT';
			}

			// Send product id to the cart function.
			wp.ajax.post( 'update_cart', {
				extid: extid,
				id: id,
				method: method,
				quantity: quantity,
				nonce: this.data.nonce
			} ).always( function( response ) {
				this.reloadCart();
			}.bind( this ) );
		},

		/**
		 * Reload Cart
		 */
		reloadCart: function() {

			// Send product id to the cart function.
			wp.ajax.post( 'get_cart_data', {
				nonce: this.data.nonce
			} ).always( function( response ) {
				$( '.pc-cart-count' ).removeClass( 'pc-empty-cart' );
				$( '.pc-cart-dropdown' ).html( response );
				$( '.pc-cart-shortcode-wrapper' ).html( response );
				this.updateCount();
			}.bind( this ) );
		},

		/**
		 * Add Item to actual cart.
		 *
		 * @param id
		 * @param showsubtotal
		 */
		addItemToCart: function( extid, id, showsubtotal ) {
			var quantity = $( '#' + extid ).val(),
				exists = $( '.pc-cart-item-price #' + extid ),
				variable;

			if ( true === this.data.variable ) {
				variable = $.each( '.pc-variable-wrap pc-variable-select option:selected', function( index, value ) {

				} );
			}

			this.cartLoading( 'start' );

			if ( 0 === exists.length ) {

				// Get html for cart.
				wp.ajax.post( 'add_item_html', {
					extid: extid,
					id: id,
					variable: variable,
					showsubtotal: showsubtotal,
					nonce: this.data.nonce
				} ).always( function ( response ) {
					if ( 0 < $( '.pc-cart-dropdown ul li' ).length ) {
						$( '.pc-cart-dropdown' ).find( '.pc-cart-line-items' ).prepend( response );
					} else {
						$( '.pc-cart-dropdown' ).html( response );
					}
				}.bind( this ) );
			} else {
				$( '#' + extid ).val( parseInt( quantity ) + 1 );
			}
		},

		/**
		 * Check cookie value.
		 *
		 * @param cname
		 */
		getCookie: function( cname ) {
			var name = cname + '=';
			var decodedCookie = decodeURIComponent( document.cookie );
			var ca = decodedCookie.split( ';' );
			for ( var i = 0; i < ca.length; i++ ) {
				var c = ca[i];
				while ( c.charAt( 0 ) === ' ' ) {
					c = c.substring( 1 );
				}
				if ( c.indexOf( name ) === 0 ) {
					return c.substring( name.length, c.length );
				}
			}
			return '';
		},

		/**
		 * Update cart count.
		 *
		 */
		updateCount: function() {
			var newQuantity = 0,
				newTotal = 0;

			$( '.pc-cart-line-items' ).first().find( 'li' ).each( function() {
				newQuantity += parseInt( $( this ).find( '.pc-cart-quantity-control' ).val() ) || 0;
				newTotal += parseFloat( $( this ).find( '.pc-cart-quantity-control' ).siblings( 'div' ).html().replace( '$', '' ) ) * parseInt( $( this ).find( '.pc-cart-quantity-control' ).val() ) || 0;
			} );

			$( '.pc-cart-count' ).html( newQuantity );
			$( '.pc-cart-subtotal span' ).html( 'Subtotal: $' + newTotal.toFixed(2) );
			this.cartLoading( 'end' );
		},

		/**
		 * Show spinner.
		 *
		 * @param action
		 */
		cartLoading: function( action ) {
			if ( 'start' === action ) {
				$( '.pc-cart-spinner' ).show();
				$( '.pc-cart-dropdown-icon, .pc-cart-count' ).css( 'opacity', '0' );
			} else {
				$( '.pc-cart-spinner' ).hide();
				$( '.pc-cart-dropdown-icon, .pc-cart-count' ).css( 'opacity', '1' );
			}
		}
	};
} )( window.jQuery, window.wp );