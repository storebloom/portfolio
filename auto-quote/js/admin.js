/**
 * Auto Quote Admin.
 */

/* exported AutoQuote */
var AutoQuoteAdmin = ( function( $ ) {
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
			this.$container = $( '.wrap' );
			this.$previewContainer = $( '.quote-wrapper' );
			this.colorPicker();
			this.getQuotes();
			this.listen();
		},
		
		/**
		 * Initiate listeners.
		 */
		listen: function() {
			var self = this;
			
			this.$container.on( 'change', '#auto-quote-font, #auto-quote-size', function() {
				self.previewQuote();
			} );
		},
		
		/**
		 * Invoke colorpicker to color setting field.
		 * Also update the preview on color change.
		 */
		colorPicker: function() {
			$( '#auto-quote-color' ).wpColorPicker( {
				change: function( event, ui ) {
					$( '#auto-quote-color' ).attr( 'value', ui.color.toString() );
					
					this.previewQuote();
				}.bind( this )
			} );
		},
		
		/**
		 * Trigger the quote style preview.
		 */
		previewQuote: function() {
			var color = $( '#auto-quote-color' ).val(),
			    size  = $( '#auto-quote-size option:selected' ).val(),
			    font  = $( '#auto-quote-font option:selected' ).val();
			
			this.$previewContainer.css( { 'color': color, 'font-family': font } ).removeClass( 'quote-small quote-medium quote-large quote-xlarge' ).addClass( 'quote-' + size );
		},
		
		/**
		 * Api call to grab quotes
		 */
		getQuotes: function() {
			var tag = document.createElement("script");
			
			tag.src = 'http://api.forismatic.com/api/1.0/?method=getQuote&format=jsonp&lang=en&jsonp=AutoQuoteAdmin.parseQuote';
			
			this.$previewContainer.append( tag ).css( { 'color': this.data.color, 'font-family': this.data.font } ).addClass( 'quote-' + this.data.size );
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
