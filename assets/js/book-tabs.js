( function () {
	var root = document.querySelector( '[data-book-tabs]' );
	if ( ! root ) {
		return;
	}
	var tabs = Array.prototype.slice.call( root.querySelectorAll( '[role="tab"]' ) );
	var panels = Array.prototype.slice.call( root.querySelectorAll( '[role="tabpanel"]' ) );

	function select( tab, focus ) {
		tabs.forEach( function ( item ) {
			var active = item === tab;
			item.setAttribute( 'aria-selected', active ? 'true' : 'false' );
			item.tabIndex = active ? 0 : -1;
		} );
		panels.forEach( function ( panel ) {
			panel.hidden = panel.id !== tab.getAttribute( 'aria-controls' );
		} );
		if ( focus ) {
			tab.focus();
		}
	}

	tabs.forEach( function ( tab, index ) {
		tab.addEventListener( 'click', function () {
			select( tab, false );
		} );
		tab.addEventListener( 'keydown', function ( event ) {
			var next = null;
			if ( 'ArrowRight' === event.key ) {
				next = tabs[ ( index + 1 ) % tabs.length ];
			} else if ( 'ArrowLeft' === event.key ) {
				next = tabs[ ( index - 1 + tabs.length ) % tabs.length ];
			} else if ( 'Home' === event.key ) {
				next = tabs[ 0 ];
			} else if ( 'End' === event.key ) {
				next = tabs[ tabs.length - 1 ];
			}
			if ( next ) {
				event.preventDefault();
				select( next, true );
			}
		} );
	} );

	root.classList.add( 'is-enhanced' );
	select( tabs[ 0 ], false );
}() );
