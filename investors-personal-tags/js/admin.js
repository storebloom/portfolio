/**
 * Investors Personal Tags.
 */

/* exported InvestorsPersonalTags */
var InvestorsPersonalTags = ( function( $, wp ) {
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
			this.$container = $( '#taxonomy-post_tag' );
			this.listen();
			this.renderTab();
			this.renderTags();
		},

		/**
		 * Initiate listeners.
		 */
		listen: function() {
			var self = this;

			// Add tag to personal tags.
			this.$container.on( 'click', '#post_tag-all button', function() {
				self.updateTag( $( this ), 'add_tag' );
			} );

			// Remove tag from personal tags.
			this.$container.on( 'click', '#post_tag-personal button', function() {
				self.updateTag( $( this ), 'remove_tag' );
			} );

			// Mirror checkbox status on check.
			this.$container.on( 'click', ':checkbox', function() {
				self.mirrorCheckboxStatus( $( this ) );
			} );
		},

		/**
		 * Render user tab.
		 */
		renderTab: function( ) {
			var $personalTab;

			// Clone tab.
			$personalTab = this.$container
				.find( '#post_tag-tabs li:nth-child(2)' )
				.clone();

			// Append cloned tab and change text.
			this.$container
				.find( '#post_tag-tabs' )
				.append( $personalTab )
				.find( 'li:last-of-type a' )
				.prop( 'href', '#post_tag-personal' )
				.text( this.data.tabName );
		},

		/**
		 * Render user tags.
		 */
		renderTags: function( ) {
			var self = this,
				$tags;

			// Reset.
			this.$container
				.find( '#post_tag-personal' )
				.remove();

			// Clone tags container.
			$tags = this.$container
				.find( '#post_tag-all' )
				.clone();

			// Append cloned tags and only keep personal tags.
			this.$container
				.find( '#post_tag-all' )
				.after( $tags )
				.prop( 'id', 'post_tag-personal' )
				.hide()
				.find( 'li' ).each( function() {
					// Prefix ids to avoid duplicates.
					$( this )
						.prop( 'id', 'personal_' + $( this ).prop( 'id' ) )
						.find( 'input' )
						.prop( 'id', 'personal-' + $( this ).find( 'input' ).prop( 'id' ) );

					if ( $.inArray( parseInt( $( this ).prop( 'id' ).replace( /\D/g, '' ) ), self.data.userTags ) < 0 ) {
						$( this ).remove();
					} else {
						self.addButton( $( this ), '-' );
					}
				} );

			// Update tag buttons.
			this.$container
				.find( '#post_tag-all' )
				.find( 'li' ).each( function() {
					if ( $.inArray( parseInt( $( this ).prop( 'id' ).replace( /\D/g, '' ) ), self.data.userTags ) < 0 ) {
						self.addButton( $( this ), '+' );
					} else {
						$( this ).find( 'button, .spinner' ).remove();
					}
				} );

			// Show tags if the personal tags tab is active.
			if ( this.$container.find( '#post_tag-tabs li:last-of-type' ).hasClass( 'tabs' ) ) {
				this.$container.find( '#post_tag-personal' ).show();
			}
		},

		/**
		 * Update tag (add/remove).
		 */
		updateTag: function( $button, action ) {
			// Add spinner.
			$button
				.hide()
				.after( $( '<span class="spinner"></span>' )
					.css( {
						'visibility': 'visible',
						'float': 'none',
						'width': '20px',
						'background-size': '18px',
						'margin': '-1px 0 0'
					} )
				);

			// Update backend user data and re-render UI.
			wp.ajax.post( action, {
				tag: parseInt( $button.parents( 'li' ).prop( 'id' ).replace( /\D/g, '' ) ),
				nonce: this.data.nonce
			} ).always( function( response ) {
				this.data.userTags = response;
				this.renderTags();
			}.bind( this ) );
		},

		/**
		 * Update tag button.
		 */
		addButton: function( $tag, text ) {
			// Reset.
			$tag
				.find( 'button, .spinner' )
				.remove();

			// Add/update button.
			$tag
				.find( 'label input' )
				.after( $( '<button type="button" class="button">' + text + '</button> ' )
					.css( {
						'font-size': '12px',
						'line-height': '14px',
						'height': '18px',
						'padding': '0 5px',
						'margin': '4px 0 0 0'
					} )
				);
		},

		/**
		 * Mirror checkbox status.
		 */
		mirrorCheckboxStatus: function( $checkbox ) {
			var val = $checkbox.prop( 'checked' ),
				id = parseInt( $checkbox.prop( 'id' ).replace( /\D/g, '' ) );

			this.$container.find( 'input[id*="' + id + '"]' ).prop( 'checked', val );
		}
	};
} )( window.jQuery, window.wp );
