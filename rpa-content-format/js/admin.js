(function() {
	jQuery(document).ready(function($){
		$(document).on('click', '.mce-my_upload_button', upload_image_tinymce);

		function upload_image_tinymce(e) {
			e.preventDefault();
			var $input_field = $('.mce-my_input_image');
			var custom_uploader = wp.media.frames.file_frame = wp.media({
				title: 'Add Image',
				button: {
					text: 'Add Image'
				},
				multiple: false
			});
			custom_uploader.on('select', function() {
				var attachment = custom_uploader.state().get('selection').first().toJSON();
				$input_field.val(attachment.url);
			});
			custom_uploader.open();
		}
	});

	tinymce.PluginManager.add('rpatitle', function( editor, url ) {
		editor.addButton( 'rpatitle', {
			text: tinyMCE_object.button_name,
			icon: false,
			onclick: function() {
				editor.windowManager.open( {
					title: tinyMCE_object.button_title,
					body: [
						{
							type: 'textbox',
							name: 'title',
							label: 'Title',
							value: '',
						},
						{
							type   : 'combobox',
							name   : 'htag',
							label  : 'H Type',
							values : [
								{ text: 'h2', value: '2' },
								{ text: 'h3', value: '3' },
								{ text: 'h4', value: '4' },
								{ text: 'h5', value: '5' },

							]
						},
						{
							type: 'textbox',
							name: 'top',
							label: 'Top',
							value: '0',
						},
						{
							type: 'textbox',
							name: 'right',
							label: 'Right',
							value: '0',
						},
						{
							type: 'textbox',
							name: 'bottom',
							label: 'Bottom',
							value: '0',
						},
						{
							type: 'textbox',
							name: 'left',
							label: 'Left',
							value: '0',
						},
						{
							type   : 'combobox',
							name   : 'align',
							label  : 'Align',
							values : [
								{ text: 'left', value: 'left' },
								{ text: 'center', value: 'center' },
								{ text: 'right', value: 'right' },
							]
						},
					],
					onsubmit: function( e ) {
						editor.insertContent( '[rpa-title title="' + e.data.title + '" h="' + e.data.htag + '" top="' + e.data.top + '" right="' + e.data.right + '" bottom="' + e.data.bottom + '" left="' + e.data.left + '" align="' + e.data.align + '" ]');
					}
				});
			},
		});
	});

	tinymce.PluginManager.add('rpaimage', function( editor, url ) {
		editor.addButton( 'rpaimage', {
			text: tinyMCE_object.image_name,
			icon: false,
			onclick: function() {
				editor.windowManager.open( {
					title: tinyMCE_object.image_title,
					body: [
						{
							type: 'textbox',
							name: 'url',
							label: 'URL',
							value: '',
							classes: 'my_input_image',
						},
						{
							type: 'button',
							name: 'my_upload_button',
							label: '',
							text: 'Insert From Library',
							classes: 'my_upload_button',
						},
						{
							type   : 'combobox',
							name   : 'float',
							label  : 'Float',
							values : [
								{ text: 'Left', value: 'left' },
								{ text: 'Right', value: 'right' },
							]
						},
						{
							type: 'textbox',
							name: 'title',
							label: 'Image Title',
							value: '',
						},
						{
							type: 'textbox',
							name: 'width',
							label: 'Width',
							value: '100%',
						},
						{
							type: 'textbox',
							name: 'top',
							label: 'Top',
							value: '0',
						},
						{
							type: 'textbox',
							name: 'right',
							label: 'Right',
							value: '0',
						},
						{
							type: 'textbox',
							name: 'bottom',
							label: 'Bottom',
							value: '0',
						},
						{
							type: 'textbox',
							name: 'left',
							label: 'Left',
							value: '0',
						},
						{
							type   : 'combobox',
							name   : 'align',
							label  : 'Align',
							values : [
								{ text: 'left', value: 'left' },
								{ text: 'center', value: 'center' },
								{ text: 'right', value: 'right' },
							]
						},
					],
					onsubmit: function( e ) {
						editor.insertContent( '[rpa-image title="' + e.data.title + '" url="' + e.data.url + '" top="' + e.data.top + '" right="' + e.data.right + '" bottom="' + e.data.bottom + '" left="' + e.data.left + '" align="' + e.data.align + '" float="' + e.data.float + '" width="' + e.data.width + '" ]');
					}
				});
			},
		});
	});

	tinymce.PluginManager.add('rpahr', function( editor, url ) {
		editor.addButton( 'rpahr', {
			text: tinyMCE_object.hr_name,
			icon: false,
			onclick: function() {
				editor.windowManager.open( {
					title: tinyMCE_object.hr_title,
					body: [
						{
							type: 'textbox',
							name: 'color',
							label: 'Color',
							value: '',
						},
						{
							type: 'textbox',
							name: 'size',
							label: 'Size (in px)',
							value: '2',
						},
						{
							type: 'textbox',
							name: 'top',
							label: 'Top (in px)',
							value: '0',
						},
						{
							type: 'textbox',
							name: 'bottom',
							label: 'Bottom (in px)',
							value: '0',
						},
					],
					onsubmit: function( e ) {
						editor.insertContent( '[rpa-hr size="' + e.data.size + '" color="' + e.data.color + '" top="' + e.data.top + '" bottom="' + e.data.bottom + '" ]');
					}
				});
			},
		});
	});

	tinymce.PluginManager.add('vimeo', function( editor, url ) {
		editor.addButton( 'vimeo', {
			text: tinyMCE_object.vimeo_name,
			icon: false,
			onclick: function() {
				editor.windowManager.open( {
					title: tinyMCE_object.vimeo_title,
					body: [
						{
							type: 'textbox',
							name: 'id',
							label: 'ID',
							value: '',
						},
						{
							type: 'textbox',
							name: 'caption',
							label: 'Caption',
							value: '',
						},
					],
					onsubmit: function( e ) {
						editor.insertContent( '[vimeo vimeoid="' + e.data.id + '" caption="' + e.data.caption + '" ]');
					}
				});
			},
		});
	});

	tinymce.PluginManager.add('radiorpa', function( editor, url ) {
		editor.addButton( 'radiorpa', {
			text: tinyMCE_object.radio_name,
			icon: false,
			onclick: function() {
				editor.windowManager.open( {
					title: tinyMCE_object.radio_title,
					body: [
						{
							type: 'textbox',
							name: 'maintitle',
							label: 'Main Title',
							value: '',
						},
						{
							type: 'textbox',
							name: 'maincopy',
							label: 'Main Copy',
							value: '',
						},
						{
							type: 'textbox',
							name: 'titles',
							label: 'Titles',
							value: '',
						},
						{
							type: 'textbox',
							name: 'sources',
							label: 'Sources',
							value: '',
						},
					],
					onsubmit: function( e ) {
						editor.insertContent( '[radio maintitle="' + e.data.maintitle + '" maincopy="' + e.data.maincopy + '" titles="' + e.data.titles + '" sources="' + e.data.sources + '"]');
					}
				});
			},
		});
	});

	tinymce.PluginManager.add('impact', function( editor, url ) {
		editor.addButton( 'impact', {
			text: tinyMCE_object.impact_name,
			icon: false,
			onclick: function() {
				editor.windowManager.open( {
					title: tinyMCE_object.impact_title,
					body: [
						{
							type: 'textbox',
							name: 'maintitle',
							label: 'Main Title',
							value: '',
						},
						{
							type: 'textbox',
							name: 'maincopy',
							label: 'Main Copy',
							value: '',
						},
						{
							type: 'textbox',
							name: 'titles',
							label: 'Titles',
							value: '',
						},
						{
							type: 'textbox',
							name: 'copy',
							label: 'Copy',
							value: '',
						},
					],
					onsubmit: function( e ) {
						editor.insertContent( '[impact maintitle="' + e.data.maintitle + '" maincopy="' + e.data.maincopy + '" titles="' + e.data.titles + '" copy="' + e.data.copy + '"]');
					}
				});
			},
		});
	});

	tinymce.PluginManager.add('mwcc', function( editor, url ) {
		editor.addButton( 'mwcc', {
			text: tinyMCE_object.module_name,
			icon: false,
			onclick: function() {
				editor.windowManager.open( {
					title: tinyMCE_object.module_title,
					body: [
						{
							type: 'textbox',
							name: 'header',
							label: 'Header',
							value: '',
						},
						{
							type: 'textbox',
							name: 'subheader',
							label: 'Sub Header',
							value: '',
						},
						{
							type: 'textbox',
							name: 'link',
							label: 'Link',
							value: '',
						},
						{
							type: 'textbox',
							name: 'target',
							label: 'Target',
							value: '',
						},
					],
					onsubmit: function( e ) {
						editor.insertContent( '[mwcc header="' + e.data.header + '" subheader="' + e.data.subheader + '" link="' + e.data.link + '" target="' + e.data.target + '"]');
					}
				});
			},
		});
	});
})();
