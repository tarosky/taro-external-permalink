/*!
 * Media selector.
 *
 * @handle tsep-media-selector
 * @deps jquery
 * @package tsep
 */

const $ = jQuery;

$( document ).ready( function() {

	let MediaFrame;

	$( '#tsep-media-chooser' ).click( function( e ) {
		e.preventDefault();

		if ( ! MediaFrame ) {
			MediaFrame = wp.media( {
				title: 'Select Media',
				multiple : false,
				library : {
					type : 'image',
				}
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
