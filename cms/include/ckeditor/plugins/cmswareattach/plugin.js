(function(){
var title = "cmswareattach",title_ = "CMSWareAttach";
var cssifyLength = CKEDITOR.tools.cssLength;

function isMediaEmbed( element )
{
	var attributes = element.attributes;

	return ( attributes.type == 'application/x-mplayer2' );
}

function createFakeElement( editor, realElement )
{
	var fakeElement = editor.createFakeParserElement( realElement, 'cke_media', 'media', true ),
		fakeStyle = fakeElement.attributes.style || '';

	var width = realElement.attributes.width,
		height = realElement.attributes.height;

	if ( typeof width != 'undefined' )
		fakeStyle = fakeElement.attributes.style = fakeStyle + 'width:' + cssifyLength( width ) + ';';

	if ( typeof height != 'undefined' )
		fakeStyle = fakeElement.attributes.style = fakeStyle + 'height:' + cssifyLength( height ) + ';';

	return fakeElement;
}

CKEDITOR.plugins.add(title,
{
	requires : [ 'panelbutton', 'floatpanel', 'styles', 'fakeobjects' ],
	
	init : function( editor ){
		var config = editor.config,lang = editor.lang;

		CKEDITOR.dialog.add( 'attachfile', this.path + 'dialogs/attachfile.js' );
		editor.addCommand( 'attachfile', new CKEDITOR.dialogCommand( 'attachfile' ) );

		CKEDITOR.dialog.add( 'attachmedia', this.path + 'dialogs/attachmedia.js' );
		editor.addCommand( 'attachmedia', new CKEDITOR.dialogCommand( 'attachmedia' ) );

		editor.ui.add( title_, CKEDITOR.UI_PANELBUTTON,
			{
				label : title_,
				title : config.lang_fix.attachtitle,
				icon : this.path + 'button_attach.gif',
				modes : { wysiwyg : 1 },

				panel :
				{
					css : editor.skin.editor.css,
					attributes : { role : 'listbox', 'aria-label' : title }
				},

				onBlock : function( panel, block )
				{
					block.autoSize = true;
					block.element.addClass( 'cke_attachblock' );
					block.element.setHtml( renderButtons( panel ) );
					// The block should not have scrollbars (#5933, #6056)
					block.element.getDocument().getBody().setStyle( 'overflow', 'hidden' );
					CKEDITOR.ui.fire( 'ready', this );
				}
			});

			editor.addCss(
				'img.cke_media' +
				'{' +
					'background-image: url(' + CKEDITOR.getUrl( this.path + 'images/placeholder.png' ) + ');' +
					'background-position: center center;' +
					'background-repeat: no-repeat;' +
					'border: 1px solid #a9a9a9;' +
					'width: 80px;' +
					'height: 80px;' +
				'}'
			);
			
			editor.on( 'doubleclick', function( evt )
				{
					var element = evt.data.element;

					if ( element.is( 'img' ) && element.data( 'cke-real-element-type' ) == 'media' )
						evt.data.dialog = 'attachmedia';
				});

			if ( editor.addMenuItems )
			{
				editor.addMenuItems(
					{
						media :
						{
							label : config.lang_fix.editattachmedia,
							icon : this.path + 'button_attach.gif',
							command : 'attachmedia',
							group : 'textfield'
						}
					});
			}
			// If the "contextmenu" plugin is loaded, register the listeners.
			if ( editor.contextMenu )
			{
				editor.contextMenu.addListener( function( element, selection )
					{
						if ( element && element.is( 'img' ) && !element.isReadOnly()
								&& element.data( 'cke-real-element-type' ) == 'media' )
							return { 'media' : CKEDITOR.TRISTATE_OFF };
					});
			}
				
		function renderButtons( panel )
		{
			var output = [], elements = ['attachfile','attachmedia'];
			var clickFn = [], name,name_;
			output.push('<table role="presentation" cellspacing=0 cellpadding=0 width="100%" class="cke_panel_table">');
			clickFn.push(CKEDITOR.tools.addFunction(function(){editor.execCommand( 'attachfile' );}));
			clickFn.push(CKEDITOR.tools.addFunction(function(){editor.execCommand( 'attachmedia' );}));
			for(var i=0;i<elements.length;i+=1){
				name = elements[i];
				name_ = config.lang_fix[name];
				output.push('<tr class="cke_skin_' , editor.config.skin , '"><td class="cke_button"><a class="cke_button_' , name , '" _cke_focus=1 hidefocus=true' +
					' title="', name , '"' +
					' onclick="CKEDITOR.tools.callFunction(', clickFn[i], ');return false;"' +
					' href="javascript:void(\'', name, '\')"' +
					' role="option" aria-posinset="1" aria-setsize="10">' +
					'<table role="presentation" cellspacing=0 cellpadding=0 width="100%">' +
						'<tr>' +
							'<td align=left><div class="cke_inline_label">&nbsp;&nbsp;',
								name_,
							'</div></td>' +
						'</tr>' +
					'</table>' +
					'</a>' +
					'</td>' +
				'</tr>' );
			}
			output.push('</table>');
			editor.focus();
			panel.hide();
			editor.fire( 'saveSnapshot' );
			output.push('<style type="text/css">');
			output.push('.cke_skin_', config.skin ,' .cke_button a .cke_inline_label{font-size:12px;width:100px;cursor:default}');
			output.push('.cke_attachblock{background-color:#f7f3e7}');
			output.push('</style>');
			return output.join('');
		}
	},

	afterInit : function( editor )
	{
		var dataProcessor = editor.dataProcessor,
			dataFilter = dataProcessor && dataProcessor.dataFilter;

		if ( dataFilter )
		{
			dataFilter.addRules(
				{
					elements :
					{
						'cke:embed' : function( element )
						{
							if ( !isMediaEmbed( element ) )
								return null;

							return createFakeElement( editor, element );
						}
					}
				},
				5);
		}
	}
});})();