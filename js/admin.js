(function ($) {
	$(window).load(function(){

		wp.media.EditPixCoreGallery = {

			frame: function() {
				//if ( this._frame )
				//	return this._frame;
				var selection = this.select();
				// create our own media iframe
				this._frame = wp.media({
					displaySettings:    false,
					id:                 'pix_core_gallery-frame',
					title:              'PixCoreGallery',
					filterable:         'uploaded',
					frame:              'post',
					state:              'gallery-edit',
					library:            { type : 'image' },
					multiple:           true,  // Set to true to allow multiple files to be selected
					editing:            true,
					selection:          selection
				});

				// on update send our attachments ids into a post meta field
				this._frame.on( 'update', function() {
						var controller = wp.media.EditPixCoreGallery._frame.states.get('gallery-edit');
						var library = controller.get('library');
						// Need to get all the attachment ids for gallery
						var ids = library.pluck('id'),
							gallery = library.gallery;
						$(wp.media.EditPixCoreGallery.element).val( ids.join(',') );

						if ( gallery.attributes._orderbyRandom ) {
							$('#pixgalleries_random').val('true');
						}

						if ( gallery.attributes.columns ) {
							$('#pixgalleries_columns').val(gallery.attributes.columns);
						}

						// update the galllery_preview
						pix_core_gallery_ajax_preview();

						return false;
					});

				return this._frame;
			},

			init: function() {

				$('.open_gallery').on('click', function(e){
					e.preventDefault();
					wp.media.EditPixCoreGallery.element = $(this).siblings('.pix_core_gallery');
					wp.media.EditPixCoreGallery.frame().open();
				});
			},

			select: function(){
				var $this_element = $(this.element);

				var galleries_ids = $this_element.val(),
					random_order = $('#pixgalleries_random').val(),
					columns =  $('#pixgalleries_columns').val(),
					defaultPostId = wp.media.gallery.defaults.id,
					attachments, selection;

				if ( galleries_ids === '' ) {
					return;
				}

				if ( typeof random_order !== 'undefined' ) {
					random_order = ' orderby="rand"';
				} else {
					random_order = '';
				}

				if (typeof columns !== 'undefined' ) {
					columns = ' columns="'+columns+'"';
				} else {
					columns = '';
				}

				var shortcode = wp.shortcode.next( 'gallery', '[gallery'+columns+' ids="'+ galleries_ids +'"'+ random_order +']' );
				// Bail if we didn't match the shortcode or all of the content.

				if ( ! shortcode ) {
					return;
				}
				// Ignore the rest of the match object.
				shortcode = shortcode.shortcode;
				if ( _.isUndefined( shortcode.get('id') ) && ! _.isUndefined( defaultPostId ) )
					shortcode.set( 'id', null );

				attachments = wp.media.gallery.attachments( shortcode );

				selection = new wp.media.model.Selection( attachments.models, {
					props:    attachments.props.toJSON(),
					multiple: true
				});

				selection.gallery = attachments.gallery;

				// Fetch the query's attachments, and then break ties from the
				// query to allow for sorting.
				selection.more().done( function() {
					// Break ties with the query.
					selection.props.set({ query: false });
					selection.unmirror();
					selection.props.unset('orderby');
				});

				return selection;
			}
		};

		pix_core_gallery_ajax_preview();
		$( wp.media.EditPixCoreGallery.init );

		$('.clear_gallery').on('click', function(e){
			e.preventDefault();
			var gallery = $(this).siblings('.pix_core_gallery');

			gallery.val('');

			pix_core_gallery_ajax_preview();
		});
	});

	var pix_core_gallery_ajax_preview = function(){

		var ids = '';
		$('.pix_core_gallery').each(function(ev,el){

			ids = $(el).val();

			if ( ids !== '' ) {

				$.ajax( {
					type: "post",
					url: locals.ajax_url,
					data: {action: 'pix_core_gallery_preview', attachments_ids: ids},
					beforeSend: function() {
						$( '.open_pix_core_gallery i' ).removeClass( 'icon-camera-retro' );
						$( '.open_pix_core_gallery i' ).addClass( 'icon-spin icon-refresh' );
					}, //show loading just when link is clicked
					complete: function() {
						$( '.open_pix_core_gallery i' ).removeClass( 'icon-spin icon-refresh' );
						$( '.open_pix_core_gallery i' ).addClass( 'icon-camera-retro' );
					}, //stop showing loading when the process is complete
					success: function( response ) {
						var result = JSON.parse( response );
						if ( result.success ) {
							$( el ).parent().find( 'ul.preview_list' ).html( result.output );
						}
					}
				} );
			} else {
				$( el ).parent().find( 'ul.preview_list' ).html( '' );
			}
		}) ;



	};




	"use strict";
	$(function () {

		/**
		 *  Checkbox value switcher
		 *  Any checkbox should switch between value 1 and 0
		 *  Also test if the checkbox needs to hide or show something under it.
		 */
//		$('#pixtypes_form input:checkbox').each(function(i,e){
//			check_checkbox_checked(e);
//			$(e).check_for_extended_options();
//		});
//		$('#pixtypes_form').on('click', 'input:checkbox', function(){
//			check_checkbox_checked(this);
//			$(this).check_for_extended_options();
//		});
		/** End Checkbox value switcher **/

		/* Ensure groups visibility */
		$('.switch input[type=checkbox]').each(function(){

			if ( $(this).data('show_group') ) {

				var show = false;
				if ( $(this).attr('checked') ) {
					show = true
				}

				toggleGroup( $(this).data('show_group'), show);
			}
		});

		$('.switch ').on('change', 'input[type=checkbox]', function(){
			if ( $(this).data('show_group') ) {
				var show = false;
				if ( $(this).attr('checked') ) {
					show = true
				}
				toggleGroup( $(this).data('show_group'), show);
			}
		});

	});


	var toggleGroup = function( name, show ){
		var $group = $( '#' + name );

		if ( show ) {
			$group.show();
		} else {
			$group.hide();
		}
	};

	/*
	 * Useful functions
	 */

	function check_checkbox_checked( input ){ // yes the name is an ironic
		if ( $(input).attr('checked') === 'checked' ) {
			$(input).siblings('input:hidden').val('on');
		} else {
			$(input).siblings('input:hidden').val('off');
		}
	} /* End check_checkbox_checked() */

	$.fn.check_for_extended_options = function() {
		var extended_options = $(this).siblings('fieldset.group');
		if ( $(this).data('show-next') ) {
			if ( extended_options.data('extended') === true) {
				extended_options
					.data('extended', false)
					.css('height', '0');
			} else if ( (typeof extended_options.data('extended') === 'undefined' && $(this).attr('checked') === 'checked' ) || extended_options.data('extended') === false ) {
				extended_options
					.data('extended', true)
					.css('height', 'auto');
			}
		}
	};

}(jQuery));