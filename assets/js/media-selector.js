/*!
 * Media selector.
 *
 * @handle tsep-media-selector
 * @deps jquery, wp-i18n
 * @package tsep
 */

const $ = jQuery;
const { __ } = wp.i18n;

$( document ).ready( function() {

	let MediaFrame;

	$( '#tsep-media-chooser' ).click( function( e ) {
		e.preventDefault();

		if ( ! MediaFrame ) {
			MediaFrame = wp.media( {
				title: __( 'Select Media to Link', 'tsep' ),
				multiple : false,
			} );
			MediaFrame.on( 'close', function() {
				MediaFrame.state().get( 'selection' ).each( function( attachment ) {
					$( 'input[name="external-permalink"]' ). val( attachment.attributes.url );
				});
			} );
		}

		MediaFrame.open();
	} );

} );
