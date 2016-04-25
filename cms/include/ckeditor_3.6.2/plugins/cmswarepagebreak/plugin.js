(function()
{
CKEDITOR.plugins.add( 'cmswarepagebreak',
{
	init : function( editor )
	{
		var pluginName = 'cmswarepagebreak';

		// Register the dialog.
		CKEDITOR.dialog.add( 'addpagebreak', this.path + 'dialogs/cmswarepagebreak.js' );
		CKEDITOR.dialog.add( 'editpagebreak', this.path + 'dialogs/cmswarepagebreak.js' );

		// Register the command.
		editor.addCommand( 'addpagebreak', 
			{
				exec : function()
				{
					if( CKEDITOR.plugins.cmswarepagebreak.getSelectedPagebreak( editor ) )
					editor.openDialog( 'editpagebreak' );
					else
					editor.openDialog( 'addpagebreak' );
				},
				modes : { wysiwyg:1, source:1 }
			});
		editor.addCommand( 'editpagebreak', new CKEDITOR.dialogCommand( 'editpagebreak' ) );

		editor.on( 'doubleclick', function( evt )
			{
				if ( CKEDITOR.plugins.cmswarepagebreak.getSelectedPagebreak( editor ) )
					evt.data.dialog = 'editpagebreak';
			});
		editor.addCss(
			'.cke_pagebreak' +
			'{' +
			'font-size: 20px;' +
			'font-weight: bold;' +
			'clear: both;' +
			'display: block;' +
			'cursor: default;' +
			'}'
		);

		editor.on( 'contentDom', function()
			{
				editor.document.getBody().on( 'resizestart', function( evt )
					{
						if ( editor.getSelection().getSelectedElement().data( 'cke-pagebreak' ) )
							evt.data.preventDefault();
					});
				editor.document.getBody().on( 'mousemove', function( evt )
					{
						if ( editor.getSelection() && editor.getSelection().getSelectedElement() && editor.getSelection().getSelectedElement().data( 'cke-pagebreak' ) ){
							evt.data.preventDefault();
						}
					});
			});

		editor.ui.addButton( 'PageBreak',
		{
			label : editor.config.lang_fix.pagebreaktitle,
			command :'addpagebreak',
			className : 'cke_button_pagebreak'
		});

		// If the "menu" plugin is loaded, register the menu items.
		if ( editor.addMenuItems )
		{
			editor.addMenuItems(
				{
					addpagebreak :
					{
						label : editor.config.lang_fix.pagebreak,
						command : 'addpagebreak',
						group : 'textfield',
						className : 'cke_button_pagebreak'
					},
					editpagebreak :
					{
						label : editor.config.lang_fix.editpagebreak,
						command : 'editpagebreak',
						group : 'textfield',
						className : 'cke_button_pagebreak'
					}
				});
			if( editor.plugins.image ){
				editor.addMenuItems(
					{
						insertimage :
						{
							label : editor.config.lang_fix.insertimage,
							command : 'image',
							group : 'image',
							className : 'cke_button_image'
						}
					});
			}
		}

		// If the "contextmenu" plugin is loaded, register the listeners.
		if ( editor.contextMenu )
		{
			editor.contextMenu.addListener( function( element, selection )
				{
					var range = selection.getRanges()[ 0 ];
					range.shrink( CKEDITOR.SHRINK_TEXT );
					var node = range.startContainer;
					while( node && !( node.type == CKEDITOR.NODE_ELEMENT && node.data( 'cke-pagebreak' ) ) )
						node = node.getParent();
					if( node )
						return { editpagebreak : CKEDITOR.TRISTATE_OFF };
					var retval = CKEDITOR.TRISTATE_OFF;
					try { retval = editor.document.$.queryCommandEnabled( 'cut' ) ? CKEDITOR.TRISTATE_OFF : CKEDITOR.TRISTATE_DISABLED; }catch( er ){}

					if ( !element || retval )
						return null;

					if( editor.plugins.image ) {
						return { addpagebreak : CKEDITOR.TRISTATE_OFF, insertimage : CKEDITOR.TRISTATE_OFF  };
					}
					return { addpagebreak : CKEDITOR.TRISTATE_OFF };
				});
		}

	},
		afterInit : function( editor )
		{
			var dataProcessor = editor.dataProcessor,
				dataFilter = dataProcessor && dataProcessor.dataFilter,
				htmlFilter = dataProcessor && dataProcessor.htmlFilter;

			if ( dataFilter )
			{
				function pagebreakrule( element )
				{
		  		if( element.children ){
		  			var child = element.children[ element.children.length-1 ];
		  			if( child.name == 'font' && child.attributes.color=='#888888' && child.children && child.children.length==1 ){
		  				if(child.children[ 0 ].type == CKEDITOR.NODE_TEXT && /^\[Page\:.* ?\]$/i.test(child.children[ 0 ].value) ){
		  					element.name = 'span';
			  				element.attributes.contenteditable = 'false';
			  				element.attributes['class'] = 'cke_pagebreak';
			  				element.attributes['data-cke-pagebreak'] = 1;
			  				element.attributes['resizable'] = false;
		  				}
		  			}
		  		}
				}
				dataFilter.addRules(
				{
				  elements :
				  {
				  	'h3' : pagebreakrule,
				  	'span' : pagebreakrule
				  }
				});
			}
		}
	
});
})();

CKEDITOR.plugins.cmswarepagebreak =
{
	createPagebreak : function( editor, oldElement, text )
	{
		var element = new CKEDITOR.dom.element( 'h3', editor.document  ),
				font = new CKEDITOR.dom.element( 'font', editor.document  );
		text && font.setText( text );
		element.setAttributes(
			{
				'data-cke-pagebreak'	: 1,
				'class'          			: 'cke_pagebreak'
			}
		);
		font.setAttributes(
			{
				'color'           : '#888888'
			}
		);
		element.append(font);

		if ( oldElement )
		{
			if ( CKEDITOR.env.ie )
			{
				element.insertAfter( oldElement );
				// Some time is required for IE before the element is removed.
				setTimeout( function()
					{
						oldElement.remove();
						element.focus();
					}, 10 );
			}
			else
			{
				element.replace( oldElement );
			}
		}
		else
		{
			if(CKEDITOR.env.webkit)
			{
				var p = new CKEDITOR.dom.element( 'p', editor.document );
				p.append(element);
				editor.insertElement( p );
				var p2 = new CKEDITOR.dom.element( 'p', editor.document );
				p2.append(new CKEDITOR.dom.element( 'br' ));
				p2.insertAfter( p );
				p2.focus();
			}
			editor.insertElement( element );
		}
		element.renameNode( 'span' );
		element.setAttribute('contentEditable',false);

		return null;
	},

	getSelectedPagebreak : function( editor )
	{
		var range = editor.getSelection().getRanges()[ 0 ];
		range.shrink( CKEDITOR.SHRINK_TEXT );
		var node = range.startContainer;
		while( node && !( node.type == CKEDITOR.NODE_ELEMENT && node.data( 'cke-pagebreak' ) ) )
			node = node.getParent();
		return node;
	}
};
