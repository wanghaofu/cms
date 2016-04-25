(function()
{
function attachfileDialog( editor )
{
	var config = editor.config;
	return {
		title : config.lang_fix.attachfiletitle,
		minWidth : 420,
		minHeight : 100,
		contents : [
			{
				id : 'info',
				label : editor.lang.common.generalTab,
				accessKey : 'I',
				elements :
				[
					{
						type : 'vbox',
						padding : 0,
						children :
						[
							{
								type : 'hbox',
								widths : [ '300px', '110px' ],
								align : 'right',
								children :
								[
									{
										id : 'href',
										type : 'text',
										label : config.lang_fix.attachfileurl,
										required : true,
										commit : function( param )
										{
											param.href=this.getValue();
										},
										validate : CKEDITOR.dialog.validate.notEmpty( config.lang_fix.attachfileurlempty )
									},
									{
										id : 'browse',
										type : 'button',
										filebrowser : 'info:href',
										hidden : true,
										// v-align with the 'src' field.
										// TODO: We need something better than a fixed size here.
										style : 'display:inline-block;margin-top:10px;',
										label : config.lang_fix.browseServer
									}
								]
							}
						]
					},
					{
						type : 'text',
						id : 'filename',
						width : '200px',
						label : config.lang_fix.attachfiletxt,
						commit : function( param )
						{
							param.filename=this.getValue();
						},
						required: true
					}
				]
			},
			{
				id : 'Upload',
				hidden : true,
				filebrowser : 'uploadButton',
				label : editor.lang.common.upload,
				elements :
				[
					{
						type : 'file',
						id : 'upload',
						label : editor.lang.common.upload,
						size : 38
					},
					{
						type : 'fileButton',
						id : 'uploadButton',
						label : editor.lang.common.uploadSubmit,
						filebrowser : 'info:href',
						'for' : [ 'Upload', 'upload' ]
					}
				]
			}
		],
		onOk : function()
		{
			var param = {};
			this.commitContent(param);
			if( param.filename == '' ){
				var n = param.href.split("/");
				param.filename = n[n.length-1];
			}
			n = param.filename.split(".");
			var suffix = false;
			if(/^[a-z0-9\_]+$/i.test(n[n.length-1]))
			{
				suffix = n[n.length-1];
			}
			var element=editor.document.createElement( 'a' ),
				logo = editor.document.createElement( 'img' ),
				txt = editor.document.createText( param.filename );
			element.setAttribute( 'href' , param.href )
			logo.setAttribute( 'src' , config.downicon.replace("%s","down") );
			if( suffix )
			{
				var logo_ = document.createElement( 'img' );
				logo_.width=logo_.height=0;
				logo_.onload = function(){logo.setAttribute( 'src', logo_.src );document.body.removeChild(logo_);}
				logo_.src = config.downicon.replace("%s",suffix);
				document.body.appendChild(logo_);
			}
			element.append(logo);
			element.append(txt);
			editor.insertElement( element );
		},
		onFocus : function()
		{
			this.getContentElement( 'info', 'href' ).focus();
		}
	};
}

	CKEDITOR.dialog.add( 'attachfile', function( editor )
		{
			return attachfileDialog( editor );
		});
})()