/**
 * Investors Insert Content.
 */

/* exported InvestorsInsertContent */
var InvestorsInsertContent = (
    function( $ ) {
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
                this.inPagePromo();
            },

            /**
             * If two arrays share ANY elements return true. CASTED TO INTS.
             *
             * @param haystack
             * @param arr
             */

            arraysShareNums: function( haystack, arr ) {
                haystack = haystack.map( function( x ) {
                    return parseInt( x, 10 );
                } );

                arr = arr.map( function( x ) {
                    return parseInt( x, 10 );
                } );

                return arr.some( function( v ) {
                    return haystack.indexOf( v ) >= 0;
                } );
            },

            /**
             * Set the text being inserted into the content.
             *
             * @param paraNum
             * @param promoLink
             * @param promoText
             * @param promoClass
             */
            setPromoText: function( paraNum, promoLink, promoText, promoClass ) {
                var paragraph = $( '.single-post-content p:nth-of-type(' + paraNum + ')' );
                var pagePromoText = promoText;
                var link = promoLink;

                $( paragraph ).after( '<div class="in-page-promo"><a href="' + link + '" class="' + promoClass + '" target="_blank">' + pagePromoText + '</a></div>' );
            },

            /**
             * Insert object data into the other functions.
             */
            inPagePromo: function() {
                /* Parameters for the different user type viewing options.
                 * Val 1 = userSubType, val 2 = Paragraph count.
                 */
                var promoParameters = [
                    [],
                    [0, 5],
                    [2, 5],
                    [8, 3],
                    [10, 3],
                    [12, 3],
                ];

                for ( var i = 1, len = promoParameters.length; i < len; i++ ) {
                    var catVal = 'value' + i + 'Cat';
                    var linkVal = 'value' + i + 'Link';
                    var classVal = 'value' + i + 'Class';
                    var valVal = 'value' + i;

                    if ( ! this.arraysShareNums( this.data.investorsPromoVar.categories, this.data.investorsPromoVar[catVal] ) ) {

                        if ( 0 === promoParameters[i][0] || userSubType >= promoParameters[i][0] ) {
                            this.setPromoText(
                                promoParameters[i][1],
                                this.data.investorsPromoVar[linkVal],
                                this.data.investorsPromoVar[valVal],
                                this.data.investorsPromoVar[classVal]
                            );
                        }
                    }
                }
            },
        };
    }
)( window.jQuery );
