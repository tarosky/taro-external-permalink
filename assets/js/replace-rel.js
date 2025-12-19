/*!
 * Replace rel on automatic approach
 *
 * @handle tsep-replace-rel
 * @package tsep
 */

const { tsepUrls } = window;

if ( 0 < tsepUrls.length ) {
	for ( const { url, original } of tsepUrls ) {
		// Change link of unchanged URL.
		document.querySelectorAll( `a[href="${ url }"]` ).forEach( ( link ) => {
			if ( 0 > [ 'prev', 'next' ].indexOf( link.rel ) ) {
				link.target = '_blank';
				link.rel = 'noopener noreferrer';
			} else {
				link.href = original;
			}
		} );
	}
}

