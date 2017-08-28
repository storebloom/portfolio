/**
 * Rpa Branching
 */

/* exported ShareButtons */
var RpaBranching = ( function( $, wp ) {
	'use strict';

	return {
		/**
		 * Holds data.
		 */
		data: {},

		/**
		 * Boot plugin.
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
			this.$container = $( '#wp-content-wrap' );
			this.$editArea = $( '.wp-editor-area' );

			$( '#wp-content-media-buttons' ).before( '<small id="load-save-message"></small>' );
			this.addButton();
			this.listen();
		},

		/**
		 * Initiate listeners.
		 */
		listen: function() {
			var self = this;

			// Get or save branch content.
			this.$container.on( 'click', '.branching-button', function() {
				self.updateBranch( $( this ).html() );
			} );
		},

		/**
		 * Add save/load button to post editor.
		 */
		addButton: function() {
			$( '#wp-content-media-buttons' ).prepend( '<button id="save-branch" type="button" class="button branching-button">Save Branch</button>' );

			// Determine if the button should load or save the branch content.
			if ( this.data.branch_saved ) {
				$( '#wp-content-media-buttons' ).prepend( '<button id="load-branch" type="button" class="button branching-button">Load Branch</button>' );
			}
		},

		/**
		 * Update the current post's branch content.
		 *
		 * @param type
		 */
		updateBranch: function( type ) {
			var action = 'save_branch',
				message = 'Saved Successfully',
				content = this.$editArea.val().replace(/\r\n|\r|\n/g,"\n");

			if ( 'Load Branch' === type ) {
				action = 'load_branch';
				message = 'Loaded Successfully';
			}

			// Load or save branch content.
			wp.ajax.post( action, {
				id: this.data.id,
				content: content,
				nonce: this.data.nonce,
			} ).always( function( results ) {
				if ( 'saved' !== results ) {
					this.$editArea.val( results );
				}

				$( '#load-save-message' ).html( message ).fadeIn( 600 ).delay( 600 ).fadeOut( 600 );
			}.bind( this ) );
		}
	};
} )( window.jQuery, window.wp );