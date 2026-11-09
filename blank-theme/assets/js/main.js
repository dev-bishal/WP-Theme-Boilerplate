/**
 * Blank Theme — front-end script.
 * Toggles the primary menu on small screens.
 */
( function () {
	const nav = document.getElementById( 'site-navigation' );
	if ( ! nav ) {
		return;
	}

	const button = nav.querySelector( '.menu-toggle' );
	if ( ! button ) {
		return;
	}

	button.addEventListener( 'click', function () {
		const open = nav.classList.toggle( 'toggled' );
		button.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
	} );
} )();
