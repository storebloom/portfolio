/**
 * Auto Quote.
 */

/* exported AutoQuote */
var AutoQuote = ( function( $ ) {
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
			this.$container = $( '.quote-wrapper' );
			this.getQuotes();
		},
		
		/**
		 * Api call to grab quotes
		 */
		getQuotes: function() {
			var tag = document.createElement("script");
			
			tag.src = 'http://api.forismatic.com/api/1.0/?method=getQuote&format=jsonp&lang=en&jsonp=AutoQuote.parseQuote';
			
			this.$container.append( tag ).css( { 'color': this.data.color, 'font-family': this.data.font } ).addClass( 'quote-' + this.data.size );
		},
		
		/**
		 * Parses out the quote data and places it in the shortcode wrapper.
		 *
		 * @param response
		 */
		parseQuote: function( response ) {
			$( '.quote-quote' ).text( '"' + response.quoteText + '"' ).fadeIn();
			$( '.quote-author' ).text( response.quoteAuthor ).fadeIn();
		},
	};
} )( window.jQuery );
