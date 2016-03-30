CKEDITOR.plugins.add('cmswarelocal',
{
	init : function( editor ){
		var config = editor.config,lang = editor.lang;
		
		editor.addCommand( 'cmswarelocal', new CKEDITOR.localizeCommand() );

		config.Localize = config.Localize==null ? false : !!config.Localize,

		editor.ui.addCheckbox( 'CMSWareLocalize',
			{
				name : "cmswarelocal",
				label : config.lang_fix.localize,
				command : 'cmswarelocal',
				checked : config.Localize,
				additional : '<input name="'+ editor.element.getNameAtt() + '_ImgAutoLocalize" type="hidden" value="' + (config.Localize?1:0) +'">',
				execute : function(instance){
					document.getElementById(instance.id).firstChild.value=document.getElementById(instance.id+'_cmswarelocal').checked?1:0;
					return false;
				}
			} );
	},
	afterInit : function( editor )
	{
		// Create a hidden input in main form
	}
});

CKEDITOR.localizeCommand = function(){};
CKEDITOR.localizeCommand.prototype =
{
	/** @ignore */
	exec : function( editor,instance )
	{
	},
	startDisabled : false
};