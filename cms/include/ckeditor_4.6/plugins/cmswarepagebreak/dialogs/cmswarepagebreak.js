(function()
{

function pagebreakDialog( editor, isEdit )
{
	var plugin = CKEDITOR.plugins.cmswarepagebreak, config = editor.config;
	return {
		title : isEdit ? config.lang_fix.editpagebreak : config.lang_fix.pagebreak,
		minWidth : 350,
		minHeight : 100,
		contents : [
			{
				id : 'info',
				elements :
				[
					{
						type : 'text',
						id : 'pageTitle',
						style : 'width: 100%;',
						label : config.lang_fix.pagetitle,
						required: true,
						setup : function( element )
						{
							if ( isEdit )
								this.setValue( CKEDITOR.tools.trim(element.getText().slice( 6, -1 )) );
						},
						commit : function( element )
						{
							var text = '[Page:' + this.getValue() + ' ]';

							CKEDITOR.plugins.cmswarepagebreak.createPagebreak( editor, element, text );
						}
					}
				]
			},
		],
		onShow : function()
		{
			if ( isEdit )
				this._element = CKEDITOR.plugins.cmswarepagebreak.getSelectedPagebreak( editor );

			this.setupContent( this._element );					
		},
		onOk : function()
		{
			this.commitContent( this._element );
			delete this._element;		
		},
		onFocus : function()
		{
			this.getContentElement( 'info', 'pageTitle' ).focus();
		}
	};
}

	CKEDITOR.dialog.add( 'addpagebreak', function( editor )
		{
			return pagebreakDialog( editor );
		});

	CKEDITOR.dialog.add( 'editpagebreak', function( editor )
		{
			return pagebreakDialog( editor, 1 );
		});
})()