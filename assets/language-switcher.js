/**
 * Ploetner Dev Blocks: close the language switcher on outside click or Escape.
 * The switcher itself is a plain <details> element and works without this.
 */
( function () {
	const selector = 'details.ploetner-lang';

	document.addEventListener( 'click', function ( event ) {
		document.querySelectorAll( selector + '[open]' ).forEach( function ( el ) {
			if ( ! el.contains( event.target ) ) {
				el.removeAttribute( 'open' );
			}
		} );
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' !== event.key ) {
			return;
		}
		document.querySelectorAll( selector + '[open]' ).forEach( function ( el ) {
			el.removeAttribute( 'open' );
			const summary = el.querySelector( 'summary' );
			if ( summary ) {
				summary.focus();
			}
		} );
	} );
} )();
