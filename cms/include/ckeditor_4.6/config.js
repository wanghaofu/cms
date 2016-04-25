/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	
	// %REMOVE_START%
	// The configuration options below are needed when running CKEditor from source files.
	config.plugins = 'dialogui,dialog,about,a11yhelp,dialogadvtab,basicstyles,bidi,blockquote,clipboard,button,panelbutton,panel,floatpanel,colorbutton,colordialog,templates,menu,contextmenu,div,resize,toolbar,elementspath,enterkey,entities,popup,filebrowser,find,fakeobjects,flash,floatingspace,listblock,richcombo,font,forms,format,horizontalrule,htmlwriter,iframe,wysiwygarea,image,indent,indentblock,indentlist,smiley,justify,menubutton,language,link,list,liststyle,magicline,maximize,newpage,pagebreak,pastetext,pastefromword,preview,print,removeformat,save,selectall,showblocks,showborders,sourcearea,specialchar,scayt,stylescombo,tab,table,tabletools,undo,wsc';
	config.skin = 'moono';
	// %REMOVE_END%

	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.lang_fix = {
			"localize" : "图片本地化",
			"pagebreak"  : "插入分页符",
			"editpagebreak" : "编辑分页符",
			"pagebreaktitle" : "插入/编辑分页符",
			"pagetitle" : "分页子标题",
			"insertimage" : "插入图片",
			"browseServer" : "发布点PSN选择",
			"attachtitle" : "插入文件",
			"attachfile" : "插入附件下载",
			"attachfiletitle" : "插入附件",
			"attachfileurl" : "链接地址",
			"attachfiletxt" : "显示文件名",
			"attachfileurlempty" : "链接地址不能为空",
			"attachmedia" : "插入/修改多媒体",
			"editattachmedia" : "修改多媒体",
			"attachmediavars" : "多媒体变量",
			"media" : {
				"video" : "视频高度方案",
				"audio" : "音频高度方案",
				"autostart" : "自动开始",
				"enablecontextmenu" : "允许右键菜单",
				"clicktoplay" : "允许鼠标点击播放/暂停",
				"showcontrols" : "显示控制栏",
				"showstatusbar" : "显示状态",
				"showdisplay" : "显示多媒体信息",
				"loop" : "循环播放",
			}			};	
	config.image_plus = {
			'changeName' : {
					'type' : 'checkbox',
					'id' : 'changeName',
					'default' : true,
					'label' : '上传后重命名'
			},
			"makeMinibox" : {
					'type'    : 'vbox',
					'padding' : 0,
					'width'   : '200px',
					'style' : 'width:200px',
					'children': [
						{	'type':'checkbox',
							'id':'makeMini',
							'default':false,
							'label'   : '生成缩略图'
						},
						{
							'type'    : 'hbox',
							'widths'  : [ '100px', '100px' ],
							'align'   : 'left',
							'children': [
								{
									'type'     : 'text',
									'id'       : 'width',
									'label'    : '长',
									'default'  : '160'
								},
								{
									'type'     : 'text',
									'id'       : 'height',
									'validate' : false,
									'label'    : '宽',
									'default'  : '120',
								}
							],
						}
			]
			},
				"mkHTML" : {
						'type'    : 'vbox',
						'padding' : 1,
						'width'   : '200px',
						'style' 	: 'width:200px',
						'children': [
							{
								'type'    : 'checkbox',
								'id'      : 'mkHTML',
								'default' : false,
								'label'   : '大图生成网页',
							},
							{
								'type'    : 'text',
								'id'      : 'img_title',
								'label'   : '图片标题'
							},
							{
								'type'    : 'textarea',
								'id'      : 'img_intro',
								'label'   : '图片简介'
							},
						]
				}
			
	};
	
//  config.language = 'fr';
//  config.uiColor = '#AADC6E';

config.width = "100%";
config.height = 500;
// config.skin = "v2";
config.Localize = false;
config.removePlugins = "pagebreak"; //移除插件
//config.videopreset = array(300,250);
//config.audiopreset = array(300,68);
//config.downicon = "<?php TplVarsAdmin::getValue('PUBLISH_URL') ?>/images/icon/%s.gif";
//扩展插件！
//config.extraPlugins = "checkbox,cmswareforms,cmswarelocal,addhtml,cmswarecss,cmswarepagebreak,cmswareattach";
//config.extraPlugins = "checkbox,cmswareforms,cmswarelocal,addhtml,cmswarecss,cmswarepagebreak,cmswareattach";

//config.enterMode = "CKEDITOR.ENTER_BR";
//config.shiftEnterMode = "CKEDITOR.ENTER_P";
config.toolbarGroups = [
             		{ name: 'tools', groups: [ 'tools' ] },
             		{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
             		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
             		{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
             		{ name: 'forms', groups: [ 'forms' ] },
             		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
             		'/',
             		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
             		{ name: 'styles', groups: [ 'styles' ] },
             		{ name: 'colors', groups: [ 'colors' ] },
             		{ name: 'insert', groups: [ 'insert' ] },
             		{ name: 'links', groups: [ 'links' ] },
             		{ name: 'others', groups: [ 'others' ] },
             	];

config.removeButtons = 'ShowBlocks,Save,Templates,NewPage,Print,Preview,Replace,SelectAll,RemoveFormat,NumberedList,BulletedList,Indent,Outdent,Blockquote,CreateDiv,BidiLtr,Language,BidiRtl,Smiley,Iframe,About';
};


CKEDITOR.on('ariaWidget', function( ev )
		{
	if(!ev.data.getFrameDocument){return;}
	var doc=ev.data.getFrameDocument();
	if(!doc.getBody){return;}
	var body=doc.getBody();
	if(body.data('cssappend')!='1'){
		body.setAttribute('data-cssappend','1');
		if(doc.$.createStyleSheet){
			var sheet = doc.$.createStyleSheet();
			sheet.addRule('.cke_label','font-size:12px;');
		}else{
			var style=doc.createElement('style'),
				cssText=doc.createText('.cke_label{font-size:12px;}');
			style.setAttribute('type','text/css');
			style.append(cssText);
			style.insertAfter(body.getFirst());
		}
	}
},null,null,1);

CKEDITOR.on('dialogDefinition', function( ev )
		{
	var dialogName = ev.data.name;
	var dialogDefinition = ev.data.definition;
	var editor=ev.editor,plus=editor.config.image_plus,i,elements=false;
	if ( dialogName == 'image' )
	{
		dialogDefinition.contents[0].elements[0].children[0].children[1].label=editor.config.lang_fix.browseServer;
		for(i=0;i<dialogDefinition.contents.length;i+=1){
			if(dialogDefinition.contents[i].id=='Upload'){
				elements=dialogDefinition.contents[i].elements;
				break;
			}
		}
		if(!elements){return false;}
		elements[0].id = 'uploadFile';
		i=elements.length-1;
		elements[i]['for'][1] = 'uploadFile';
		elements[i].onClick = function( evt ){
			var d = this.getDialog(), f = d.getContentElement( 'Upload', 'uploadButton' )['for'],
			action = d.definition.getContents(f[0]).get(f[1]).action, i;
			var params={
				'changeName' : 0,
				'makeMini'   : 0,
				'width'      : 0,
				'height'     : 0,
				'mkHTML'     : 0
			};
			if( d.getContentElement( 'Upload', 'changeName' ).getValue() == true ){
				params.changeName = 1;
			}
			if( d.getContentElement( 'Upload', 'makeMini' ).getValue() == true ){
				params.makeMini = 1;
				params.width = d.getContentElement( 'Upload', 'width' ).getValue();
				params.height = d.getContentElement( 'Upload', 'height' ).getValue();
			}
			if( d.getContentElement( 'Upload', 'mkHTML' ).getValue() == true ){
				params.mkHTML = 1;
			}
			for(i in params){
				action += params[i] ==null ? '' : '&' + i + '=' + params[i];
			}
			d.getContentElement( 'Upload', 'uploadFile' ).getInputElement().getParent().$.action = action;
			return true;
		}
		elements[i] = {
			type : 'vbox',
			width : '240px',
			style : 'width:240px;',
			padding : 0,
			children :
			[
				{
					type : 'hbox',
					widths : [ '140px', '100px' ],
					align : 'left',
					children :
					[
						elements[i],
						plus.changeName
					]
				}
			]
		};
		plus.makeMinibox.children[0].onChange = function(){
			var d = this.getDialog(),v = this.getValue();
			var cmd = v==true ? 'enable' : 'disable';
			d.getContentElement( 'Upload', 'width' )[cmd]();
			d.getContentElement( 'Upload', 'width' ).getInputElement().$.disabled=!v;
			d.getContentElement( 'Upload', 'height' )[cmd]();
			d.getContentElement( 'Upload', 'height' ).getInputElement().$.disabled=!v;
			d.getContentElement( 'Upload', 'mkHTML' )[cmd]();
			d.getContentElement( 'Upload', 'mkHTML' ).getInputElement().$.disabled=!v;
			v = v && d.getContentElement( 'Upload', 'mkHTML' ).getValue();
			cmd = v==true ? 'enable' : 'disable';
			d.getContentElement( 'Upload', 'img_title' )[cmd]();
			d.getContentElement( 'Upload', 'img_title' ).getInputElement().$.disabled=!v;
			d.getContentElement( 'Upload', 'img_intro' )[cmd]();
			d.getContentElement( 'Upload', 'img_intro' ).getInputElement().$.disabled=!v;
			return;
		}
		elements.push(plus.makeMinibox);
		plus.mkHTML.children[0].onChange = function(){
			var d = this.getDialog(),v = this.getValue();
			var cmd = v==true ? 'enable' : 'disable';
			d.getContentElement( 'Upload', 'img_title' )[cmd]();
			d.getContentElement( 'Upload', 'img_title' ).getInputElement().$.disabled=!v;
			d.getContentElement( 'Upload', 'img_intro' )[cmd]();
			d.getContentElement( 'Upload', 'img_intro' ).getInputElement().$.disabled=!v;
			return;
		}
		elements.push(plus.mkHTML);
		dialogDefinition.onFocus = function()
		{
			this.getContentElement( 'Upload', 'width' ).disable();
			this.getContentElement( 'Upload', 'width' ).getInputElement().$.disabled=true;
			this.getContentElement( 'Upload', 'height' ).disable();
			this.getContentElement( 'Upload', 'height' ).getInputElement().$.disabled=true;
			this.getContentElement( 'Upload', 'mkHTML' ).disable();
			this.getContentElement( 'Upload', 'mkHTML' ).getInputElement().$.disabled=true;
			this.getContentElement( 'Upload', 'img_title' ).disable();
			this.getContentElement( 'Upload', 'img_title' ).getInputElement().$.disabled=true;
			this.getContentElement( 'Upload', 'img_intro' ).disable();
			this.getContentElement( 'Upload', 'img_intro' ).getInputElement().$.disabled=true;
			return;
		};
	}
	else if ( dialogName == 'flash' )
	{
		dialogDefinition.contents[0].elements[0].children[0].children[1].label=editor.config.lang_fix.browseServer;
	}
},null,null,1);







