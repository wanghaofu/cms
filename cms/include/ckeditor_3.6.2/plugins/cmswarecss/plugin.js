CKEDITOR.plugins.add('cmswarecss',
{
	init : function( editor ){
		var config = editor.config;
		editor.addCommand( 'cmswarecss', new CKEDITOR.CMSWareCss() );
		
		var css=[];
		css.push('<style type="text/css">');
		css.push('.cke_skin_', config.skin ,' .cke_top,.cke_skin_', config.skin ,' .cke_bottom,.cke_shared .cke_skin_', config.skin ,'{background-color:#d6d3ce;}');
		css.push('.cke_skin_', config.skin ,' .cke_button a,.cke_skin_', config.skin ,' .cke_button a:hover,.cke_skin_', config.skin ,' .cke_button a:focus,.cke_skin_', config.skin ,' .cke_button a:active,.cke_skin_', config.skin ,' .cke_button a.cke_off{border:solid 1px #d6d3ce;}');
		css.push('.cke_skin_', config.skin ,' .cke_button a,.cke_skin_', config.skin ,' .cke_button a.cke_off{background-color:#d6d3ce;}');
		css.push('.cke_skin_', config.skin ,' .cke_path a,.cke_skin_', config.skin ,' .cke_path .cke_empty{border:solid 1px #d6d3ce;background-color:#d6d3ce;}');
		css.push('.cke_skin_', config.skin ,' .cke_rcombo .cke_label{background-color:#d6d3ce}');
		css.push('.cke_checkbox{position:relative;margin-top:0px;height:20px;}');
		css.push('.cke_checkbox div{position:absolute;height:20px;}');
		css.push('.cke_left{left:0px;top:0px}');
		css.push('.cke_skin_', config.skin ,' .cke_checkbox .cke_left a{display:block;margin-top:4px;cursor:default}');
		css.push('.cke_skin_', config.skin ,' *,.cke_skin_', config.skin ,' a:hover,.cke_skin_', config.skin ,' a:link,.cke_skin_', config.skin ,' a:visited,.cke_skin_', config.skin ,' a:active{font-size:12px;}');
		css.push('.cke_panel_grouptitle{font-size:12px;}');
		css.push('.cke_colorblock{font-size:12px;}');
		css.push('.cke_skin_', config.skin ,' .cke_hc a.cke_toolbox_collapser span{font-size:12px;}');
		css.push('.cke_skin_', config.skin ,' textarea.cke_source{font-size:12px;}');
		css.push('.cke_skin_', config.skin ,' .cke_menuitem a span .cke_label{font-size:12px;}');
		css.push('</style>');
		css=css.join('');
		
		editor.ui.addHTML( 'CMSWareCss',
			{
				name : "cmswarecss",
				label : "cmswarecss",
				command : 'cmswarecss',
				html : css
			} );
	},
	afterInit : function( editor )
	{
		// Create a hidden input in main form
	}
});

CKEDITOR.CMSWareCss = function(){};
CKEDITOR.CMSWareCss.prototype =
{
	/** @ignore */
	exec : function( editor,instance )
	{
	},
	startDisabled : false
};