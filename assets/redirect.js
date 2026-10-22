/**
 * In-App Browser Redirect.
 *
 * Detects in-app browsers by user agent. On Android it opens the current page in Chrome
 * with an intent link; on iOS, where no page can force Safari, it shows a short hint with a
 * "Copy link" button. Runs only in the browser, so cached pages stay identical for everyone.
 */
( function () {
	'use strict';

	var config = window.iabrConfig;
	if ( ! config || ! document.body ) {
		return;
	}

	// Order matters: Messenger and Instagram user agents can also contain Facebook tokens.
	var apps = [
		[ 'messenger', /FBAN\/Messenger|FB_IAB\/MESSENGER|MessengerForiOS|Orca-Android/i ],
		[ 'instagram', /Instagram/i ],
		[ 'facebook', /FBAN|FBAV|FB_IAB|FBIOS|FB4A/ ],
		[ 'linkedin', /LinkedInApp/i ],
		[ 'tiktok', /musical_ly|Bytedance|TikTok|trill_/i ],
		[ 'snapchat', /Snapchat/i ],
		[ 'line', /\sLine\//i ],
		[ 'twitter', /Twitter/i ],
	];

	var ua = navigator.userAgent || '';
	var test = ( /[?&]iabr_test=(android|ios)\b/.exec( window.location.search ) || [] )[ 1 ];
	var app = test ? config.apps[ 0 ] : '';
	for ( var i = 0; ! app && i < apps.length; i++ ) {
		if ( apps[ i ][ 1 ].test( ua ) ) {
			app = apps[ i ][ 0 ];
		}
	}
	if ( ! app || config.apps.indexOf( app ) === -1 ) {
		return;
	}

	var isIOS = /iPhone|iPad|iPod/.test( ua ) || ( /Macintosh/.test( ua ) && navigator.maxTouchPoints > 1 );
	var platform = test || ( /Android/i.test( ua ) ? 'android' : ( isIOS ? 'ios' : '' ) );
	if ( ! platform ) {
		return;
	}

	function store( key, value ) {
		try {
			if ( undefined === value ) {
				return window.sessionStorage.getItem( key );
			}
			window.sessionStorage.setItem( key, value );
			return value;
		} catch ( e ) {
			return null;
		}
	}

	if ( store( 'iabr_dismissed' ) ) {
		return;
	}

	function intentUrl() {
		var l = window.location;
		return 'intent://' + l.host + l.pathname + l.search + l.hash +
			'#Intent;scheme=' + l.protocol.replace( ':', '' ) +
			';package=com.android.chrome;S.browser_fallback_url=' + encodeURIComponent( l.href ) + ';end';
	}

	function copy( text, done ) {
		if ( navigator.clipboard && navigator.clipboard.writeText ) {
			navigator.clipboard.writeText( text ).then( done, function () {
				legacyCopy( text, done );
			} );
			return;
		}
		legacyCopy( text, done );
	}

	function legacyCopy( text, done ) {
		var field = document.createElement( 'textarea' );
		field.value = text;
		field.setAttribute( 'readonly', '' );
		field.className = 'iabr-offscreen';
		document.body.appendChild( field );
		field.select();
		try {
			document.execCommand( 'copy' );
			done();
		} catch ( e ) {
			// Nothing else to try; the link stays visible in the address bar.
		}
		document.body.removeChild( field );
	}

	function button( tag, className, label ) {
		var el = document.createElement( tag );
		el.className = className;
		el.textContent = label;
		if ( 'button' === tag ) {
			el.type = 'button';
		}
		return el;
	}

	function showBanner() {
		if ( document.querySelector( '.iabr-banner' ) ) {
			return;
		}
		var text = config.text;
		var banner = document.createElement( 'div' );
		banner.className = 'iabr-banner iabr-banner--' + platform;
		banner.setAttribute( 'role', 'region' );
		banner.setAttribute( 'aria-label', text.region );

		var message = document.createElement( 'p' );
		message.className = 'iabr-banner__text';
		message.textContent = 'android' === platform ? text.android : text.ios;

		var actions = document.createElement( 'div' );
		actions.className = 'iabr-banner__actions';

		var status = document.createElement( 'p' );
		status.className = 'iabr-banner__status';
		status.setAttribute( 'role', 'status' );

		var action;
		if ( 'android' === platform ) {
			action = button( 'a', 'iabr-banner__button', text.open );
			action.href = intentUrl();
		} else {
			action = button( 'button', 'iabr-banner__button', text.copy );
			action.addEventListener( 'click', function () {
				copy( window.location.href, function () {
					status.textContent = text.copied;
				} );
			} );
		}

		var close = button( 'button', 'iabr-banner__close', '×' );
		close.setAttribute( 'aria-label', text.close );

		function dismiss() {
			store( 'iabr_dismissed', '1' );
			if ( banner.parentNode ) {
				banner.parentNode.removeChild( banner );
			}
		}
		close.addEventListener( 'click', dismiss );
		banner.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' === event.key ) {
				dismiss();
			}
		} );

		actions.appendChild( action );
		banner.appendChild( close );
		banner.appendChild( message );
		banner.appendChild( actions );
		banner.appendChild( status );

		var shown = false;
		function append() {
			if ( ! shown ) {
				shown = true;
				document.body.appendChild( banner );
			}
		}
		var link = document.createElement( 'link' );
		link.rel = 'stylesheet';
		link.href = config.css;
		link.onload = append;
		document.head.appendChild( link );
		window.setTimeout( append, 1500 );
	}

	if ( 'android' === platform ) {
		var key = 'iabr_tried:' + window.location.href;
		// Redirect once per page and tab. If storage is unavailable, only show the button, so a
		// missing Chrome (the fallback URL reloads this page) can never cause a redirect loop.
		if ( 'auto' === config.androidMode && ! test && ! store( key ) && store( key, '1' ) && store( key ) ) {
			window.location.href = intentUrl();
			window.setTimeout( showBanner, 1500 );
			return;
		}
		showBanner();
	} else if ( config.iosBanner ) {
		showBanner();
	}
} )();
