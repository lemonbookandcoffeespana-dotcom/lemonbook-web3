( function () {
	'use strict';

	var dialog = document.querySelector( '[data-book-hold-dialog]' );
	if ( ! dialog || typeof dialog.showModal !== 'function' ) {
		return;
	}

	var itemIdField = dialog.querySelector( '[data-book-hold-item-id]' );
	var nameField = dialog.querySelector( '[data-book-hold-name]' );
	var form = dialog.querySelector( 'form' );
	var status = dialog.querySelector( '[data-form-status]' );

	var close = function () {
		dialog.close();
	};
	dialog.querySelectorAll( '[data-close]' ).forEach( function ( button ) {
		button.addEventListener( 'click', close );
	} );
	dialog.addEventListener( 'click', function ( event ) {
		if ( event.target === dialog ) {
			close();
		}
	} );

	document.querySelectorAll( '[data-book-hold-trigger]' ).forEach( function ( trigger ) {
		trigger.addEventListener( 'click', function () {
			var itemId = trigger.getAttribute( 'data-item-id' ) || '';
			var bookName = trigger.getAttribute( 'data-book-name' ) || '';
			if ( itemIdField ) {
				itemIdField.value = itemId;
			}
			if ( nameField ) {
				nameField.textContent = bookName;
			}
			if ( form ) {
				form.reset();
				if ( itemIdField ) {
					itemIdField.value = itemId;
				}
			}
			if ( status ) {
				status.textContent = '';
				status.classList.remove( 'is-success', 'is-error' );
			}
			dialog.showModal();
		} );
	} );
}() );
