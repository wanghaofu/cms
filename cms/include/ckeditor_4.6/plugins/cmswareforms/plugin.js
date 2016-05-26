(function(){
	var title = "cmswareforms",title_ = "CMSWareForms";
	CKEDITOR.plugins.add(title,
	{
		requires : [ 'panelbutton', 'floatpanel', 'styles' ],
		
		icons:'cmswareforms',
		init : function( editor ){
			var config = editor.config,lang = editor.lang;
	
			var clickFn;
			
				editor.ui.add( title_, CKEDITOR.UI_PANELBUTTON,
					{
						label : title_,
						title : lang.common.form,
						className : 'cke_button_form',
						modes : { wysiwyg : 1 },
	
						panel :
						{
							css : editor.skin.editor.css,
							attributes : { role : 'listbox', 'aria-label' : title }
						},
	
						onBlock : function( panel, block )
						{
							block.autoSize = true;
							block.element.addClass( 'cke_formsblock' );
							block.element.setHtml( renderButtons( panel ) );
							// The block should not have scrollbars (#5933, #6056)
							block.element.getDocument().getBody().setStyle( 'overflow', 'hidden' );
							CKEDITOR.ui.fire( 'ready', this );
						}
					});
					
			function renderButtons( panel )
			{
				var output = [], elements = ['form','checkbox','radio','textField','textarea','select','button','imageButton','hiddenField'];
				var clickFn = [], name,name_,name__;
				output.push('<table role="presentation" cellspacing=0 cellpadding=0 width="100%" class="cke_panel_table">');
				clickFn.push(CKEDITOR.tools.addFunction(function(){editor.execCommand( 'form' );}));
				clickFn.push(CKEDITOR.tools.addFunction(function(){editor.execCommand( 'checkbox' );}));
				clickFn.push(CKEDITOR.tools.addFunction(function(){editor.execCommand( 'radio' );}));
				clickFn.push(CKEDITOR.tools.addFunction(function(){editor.execCommand( 'textfield' );}));
				clickFn.push(CKEDITOR.tools.addFunction(function(){editor.execCommand( 'textarea' );}));
				clickFn.push(CKEDITOR.tools.addFunction(function(){editor.execCommand( 'select' );}));
				clickFn.push(CKEDITOR.tools.addFunction(function(){editor.execCommand( 'button' );}));
				clickFn.push(CKEDITOR.tools.addFunction(function(){editor.execCommand( 'imagebutton' );}));
				clickFn.push(CKEDITOR.tools.addFunction(function(){editor.execCommand( 'hiddenfield' );}));
				for(var i=0;i<elements.length;i+=1){
					name = elements[i];
					name_ = name.toLowerCase();
					name__ = lang.common[name] == null ? lang.common[name_] : lang.common[name];
					output.push('<tr class="cke_skin_' , editor.config.skin , '"><td class="cke_button"><a class="cke_button_' , name_ , '" _cke_focus=1 hidefocus=true' +
						' title="', name__ , '"' +
						' onclick="CKEDITOR.tools.callFunction(', clickFn[i], ');return false;"' +
						' href="javascript:void(\'', name__, '\')"' +
						' role="option" aria-posinset="1" aria-setsize="10">' +
						'<table role="presentation" cellspacing=0 cellpadding=0 width="100%">' +
							'<tr>' +
								'<td>' +
									'<span class="cke_icon"></span>' +
								'</td>' +
								'<td align=left><div class="cke_inline_label">&nbsp;&nbsp;',
									name__,
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
				output.push('.cke_formsblock{background-color:#f7f3e7}');
				output.push('</style>');
	//output.push('<a onclick="this.parentNode.lastChild.innerText=parent.window.document.body.innerHTML">debug</a><textarea></textarea>');
				return output.join('');
			}
		}
	});
})();