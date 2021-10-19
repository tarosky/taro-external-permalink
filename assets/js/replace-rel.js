/*!
 * Replace rel on automatic approach
 *
 * @handle tsep-replace-rel
 * @package tsep
 */


const { tsepUrls } = window;

if ( 0 < tsepUrls.length ) {
	for ( const url of tsepUrls ) {
		document.querySelectorAll( `a[href="${url}"]` ).forEach( ( link ) => {
			link.target = '_blank';
			link.rel = 'noopener noreferrer';
		} );
	}
}

