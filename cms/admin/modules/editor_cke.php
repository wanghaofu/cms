<?php echo   '<script src='.INCLUDE_PATH.'/ckeditor-4.6/ckeditor.js></script>'?>

<?php
/**
 * // Create a class instance.
 * $CKEditor = new CKEditor();
 * // Path to the CKEditor directory.
 * $CKEditor->basePath = INCLUDE_PATH.'/ckeditor-4.6/';
 * config.width = "100%";
 * config.height = 500;
 * config.skin = "v2";
 * config.image_previewText = "desc";
 * config.filebrowserImageUploadUrl = "./upload_cke.php?sId={$IN['sId']}&type=img&o=upload&mode=one&NodeID={$IN['NodeID']}";
 * config.filebrowserBrowseUrl = "./admin_select.php?sId={$IN['sId']}&o=psn_picker&psn=";
 * config.filebrowserWindowWidth = "600";
 * config.filebrowserWindowHeight = "266";
 * config.filebrowserImageBrowseLinkUrl = false;
 * config.filebrowserFlashUploadUrl = "./upload_cke.php?sId={$IN['sId']}&type=flash&o=upload&mode=picker&changeName=1&NodeID={$IN['NodeID']}";
 * config.filebrowserAttachfileUploadUrl = "./upload_cke.php?sId={$IN['sId']}&type=attach&o=upload&mode=picker&changeName=1&NodeID={$IN['NodeID']}";
 * config.lang_fix = array(
 * "localize" => "图片本地化",
 * "pagebreak" => "插入分页符",
 * "editpagebreak" => "编辑分页符",
 * "pagebreaktitle" => "插入/编辑分页符",
 * "pagetitle" => "分页子标题",
 * "insertimage" => "插入图片",
 * "browseServer" => "发布点PSN选择",
 * "attachtitle" => "插入文件",
 * "attachfile" => "插入附件下载",
 * "attachfiletitle" => "插入附件",
 * "attachfileurl" => "链接地址",
 * "attachfiletxt" => "显示文件名",
 * "attachfileurlempty" => "链接地址不能为空",
 * "attachmedia" => "插入/修改多媒体",
 * "editattachmedia" => "修改多媒体",
 * "attachmediavars" => "多媒体变量",
 * "media" => array(
 * "video" => "视频高度方案",
 * "audio" => "音频高度方案",
 * "autostart" => "自动开始",
 * "enablecontextmenu" => "允许右键菜单",
 * "clicktoplay" => "允许鼠标点击播放/暂停",
 * "showcontrols" => "显示控制栏",
 * "showstatusbar" => "显示状态",
 * "showdisplay" => "显示多媒体信息",
 * "loop" => "循环播放",
 * )
 * );
 * config.Localize = false;
 * config.removePlugins = "pagebreak";
 * config.videopreset = array(300,250);
 * config.audiopreset = array(300,68);
 * config.downicon = TplVarsAdmin::getValue('PUBLISH_URL') . "/images/icon/%s.gif";
 * config.extraPlugins = "checkbox,cmswareforms,cmswarelocal,addhtml,cmswarecss,cmswarepagebreak,cmswareattach";
 * if(!isset($IsIE)){
 * if(preg_match("/MSIE ([0-9\.]+)/is",$_SERVER["HTTP_USER_AGENT"],$match)){
 * $match[1] = intval($match[1]);
 * $IsIE = $match[1] < 8 ? true : false;
 * }else{
 * $IsIE = false;
 * }
 * }
 * config.toolbar = array(
 * array( 'Maximize','Cut','Copy','Paste','PasteText','PasteFromWord','Find','-','Undo','Redo','-','RemoveFormat','-','Bold','Italic','Underline','Strike','-','NumberedList','BulletedList','Outdent','Indent','-','Subscript','Superscript','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','PageBreak',($IsIE ? NULL : '-'),'CMSWareLocalize' ),
 * '/',
 * array( 'Font','FontSize','Format','Link','Unlink','Anchor','-','TextColor','BGColor','-','Table','CMSWareForms','-','Image','Flash','CMSWareAttach','HorizontalRule','SpecialChar','-','Source'),
 * array( 'CMSWareCss' )
 * );
 * config.enterMode = "CKEDITOR.ENTER_BR";
 * config.shiftEnterMode = "CKEDITOR.ENTER_P";
 * config.contentsCss = "<CKEditor folder>/cmsware.css";
 * config.image_plus = array(
 * "changeName" => array(
 * 'type' => 'checkbox',
 * 'id' => 'changeName',
 * 'default' => true,
 * 'label' => '上传后重命名'
 * ),
 * "makeMinibox" => array(
 * 'type' => 'vbox',
 * 'padding' => 0,
 * 'width' => '200px',
 * 'style' => 'width:200px',
 * 'children'=> array(
 * array(
 * 'type' => 'checkbox',
 * 'id' => 'makeMini',
 * 'default' => false,
 * 'label' => '生成缩略图'
 * ),
 * array(
 * 'type' => 'hbox',
 * 'widths' => array( '100px', '100px' ),
 * 'align' => 'left',
 * 'children'=> array(
 * array(
 * 'type' => 'text',
 * 'id' => 'width',
 * 'label' => '长',
 * 'default' => '160'
 * ),
 * array(
 * 'type' => 'text',
 * 'id' => 'height',
 * 'validate' => false,
 * 'label' => '宽',
 * 'default' => '120',
 * )
 * ),
 * )
 * )
 * ),
 * "mkHTML" => array(
 * 'type' => 'vbox',
 * 'padding' => 1,
 * 'width' => '200px',
 * 'style' => 'width:200px',
 * 'children'=> array(
 * array(
 * 'type' => 'checkbox',
 * 'id' => 'mkHTML',
 * 'default' => false,
 * 'label' => '大图生成网页',
 * ),
 * array(
 * 'type' => 'text',
 * 'id' => 'img_title',
 * 'label' => '图片标题'
 * ),
 * array(
 * 'type' => 'textarea',
 * 'id' => 'img_intro',
 * 'label' => '图片简介'
 * ),
 * )
 * )
 * );
 * if(!isset($CKEditor_loaded)){
 * $CKEditor->addGlobalEventHandler('ariaWidget', "function( ev )
 * {
 * if(!ev.data.getFrameDocument){return;}
 * var doc=ev.data.getFrameDocument();
 * if(!doc.getBody){return;}
 * var body=doc.getBody();
 * if(body.data('cssappend')!='1'){
 * body.setAttribute('data-cssappend','1');
 * if(doc.$.createStyleSheet){
 * var sheet = doc.$.createStyleSheet();
 * sheet.addRule('.cke_label','font-size:12px;');
 * }else{
 * var style=doc.createElement('style'),
 * cssText=doc.createText('.cke_label{font-size:12px;}');
 * style.setAttribute('type','text/css');
 * style.append(cssText);
 * style.insertAfter(body.getFirst());
 * }
 * }
 * },null,null,1");
 * $CKEditor->addGlobalEventHandler('dialogDefinition', "function( ev )
 * {
 * var dialogName = ev.data.name;
 * var dialogDefinition = ev.data.definition;
 * var editor=ev.editor,plus=editor.config.image_plus,i,elements=false;
 * if ( dialogName == 'image' )
 * {
 * dialogDefinition.contents[0].elements[0].children[0].children[1].label=editor.config.lang_fix.browseServer;
 * for(i=0;i<dialogDefinition.contents.length;i+=1){
 * if(dialogDefinition.contents[i].id=='Upload'){
 * elements=dialogDefinition.contents[i].elements;
 * break;
 * }
 * }
 * if(!elements){return false;}
 * elements[0].id = 'uploadFile';
 * i=elements.length-1;
 * elements[i]['for'][1] = 'uploadFile';
 * elements[i].onClick = function( evt ){
 * var d = this.getDialog(), f = d.getContentElement( 'Upload', 'uploadButton' )['for'],
 * action = d.definition.getContents(f[0]).get(f[1]).action, i;
 * var params={
 * 'changeName' : 0,
 * 'makeMini' : 0,
 * 'width' : 0,
 * 'height' : 0,
 * 'mkHTML' : 0
 * };
 * if( d.getContentElement( 'Upload', 'changeName' ).getValue() == true ){
 * params.changeName = 1;
 * }
 * if( d.getContentElement( 'Upload', 'makeMini' ).getValue() == true ){
 * params.makeMini = 1;
 * params.width = d.getContentElement( 'Upload', 'width' ).getValue();
 * params.height = d.getContentElement( 'Upload', 'height' ).getValue();
 * }
 * if( d.getContentElement( 'Upload', 'mkHTML' ).getValue() == true ){
 * params.mkHTML = 1;
 * }
 * for(i in params){
 * action += params[i] ==null ? '' : '&' + i + '=' + params[i];
 * }
 * d.getContentElement( 'Upload', 'uploadFile' ).getInputElement().getParent().$.action = action;
 * return true;
 * }
 * elements[i] = {
 * type : 'vbox',
 * width : '240px',
 * style : 'width:240px;',
 * padding : 0,
 * children :
 * [
 * {
 * type : 'hbox',
 * widths : [ '140px', '100px' ],
 * align : 'left',
 * children :
 * [
 * elements[i],
 * plus.changeName
 * ]
 * }
 * ]
 * };
 * plus.makeMinibox.children[0].onChange = function(){
 * var d = this.getDialog(),v = this.getValue();
 * var cmd = v==true ? 'enable' : 'disable';
 * d.getContentElement( 'Upload', 'width' )[cmd]();
 * d.getContentElement( 'Upload', 'width' ).getInputElement().$.disabled=!v;
 * d.getContentElement( 'Upload', 'height' )[cmd]();
 * d.getContentElement( 'Upload', 'height' ).getInputElement().$.disabled=!v;
 * d.getContentElement( 'Upload', 'mkHTML' )[cmd]();
 * d.getContentElement( 'Upload', 'mkHTML' ).getInputElement().$.disabled=!v;
 * v = v && d.getContentElement( 'Upload', 'mkHTML' ).getValue();
 * cmd = v==true ? 'enable' : 'disable';
 * d.getContentElement( 'Upload', 'img_title' )[cmd]();
 * d.getContentElement( 'Upload', 'img_title' ).getInputElement().$.disabled=!v;
 * d.getContentElement( 'Upload', 'img_intro' )[cmd]();
 * d.getContentElement( 'Upload', 'img_intro' ).getInputElement().$.disabled=!v;
 * return;
 * }
 * elements.push(plus.makeMinibox);
 * plus.mkHTML.children[0].onChange = function(){
 * var d = this.getDialog(),v = this.getValue();
 * var cmd = v==true ? 'enable' : 'disable';
 * d.getContentElement( 'Upload', 'img_title' )[cmd]();
 * d.getContentElement( 'Upload', 'img_title' ).getInputElement().$.disabled=!v;
 * d.getContentElement( 'Upload', 'img_intro' )[cmd]();
 * d.getContentElement( 'Upload', 'img_intro' ).getInputElement().$.disabled=!v;
 * return;
 * }
 * elements.push(plus.mkHTML);
 * dialogDefinition.onFocus = function()
 * {
 * this.getContentElement( 'Upload', 'width' ).disable();
 * this.getContentElement( 'Upload', 'width' ).getInputElement().$.disabled=true;
 * this.getContentElement( 'Upload', 'height' ).disable();
 * this.getContentElement( 'Upload', 'height' ).getInputElement().$.disabled=true;
 * this.getContentElement( 'Upload', 'mkHTML' ).disable();
 * this.getContentElement( 'Upload', 'mkHTML' ).getInputElement().$.disabled=true;
 * this.getContentElement( 'Upload', 'img_title' ).disable();
 * this.getContentElement( 'Upload', 'img_title' ).getInputElement().$.disabled=true;
 * this.getContentElement( 'Upload', 'img_intro' ).disable();
 * this.getContentElement( 'Upload', 'img_intro' ).getInputElement().$.disabled=true;
 * return;
 * };
 * }
 * else if ( dialogName == 'flash' )
 * {
 * dialogDefinition.contents[0].elements[0].children[0].children[1].label=editor.config.lang_fix.browseServer;
 * }
 * },null,null,1");
 * }*
 */
// $CKEditor->editor("data_".$var["FieldName"], $pInfo[$var["FieldName"]]);
// $CKEditor_loaded = true;

?>

<textarea name="data_<?php echo $var["FieldName"] ?>"
	id="data_<?php echo $var["FieldName"] ?>" rows="10" cols="80">
               <?php  echo htmlspecialchars($pInfo[$var["FieldName"]]) ?>
            </textarea>
<script>

CKEDITOR.editorConfig = function( config ) {
//     config.language = 'fr';
    config.uiColor = '#AADC6E';

   config.width = "100%";
   config.height = 500;
   config.skin = "v2";
//    config.image_previewText = "desc";

   
//    config.filebrowserImageUploadUrl = "./upload_cke.php?sId={$IN['sId']}&type=img&o=upload&mode=one&NodeID={$IN['NodeID']}";
//    config.filebrowserBrowseUrl = "./admin_select.php?sId={$IN['sId']}&o=psn_picker&psn=";
//    config.filebrowserWindowWidth = "600";
//    config.filebrowserWindowHeight = "266";
//    config.filebrowserImageBrowseLinkUrl = false;
//    config.filebrowserFlashUploadUrl = "./upload_cke.php?sId={$IN['sId']}&type=flash&o=upload&mode=picker&changeName=1&NodeID={$IN['NodeID']}";
//    config.filebrowserAttachfileUploadUrl = "./upload_cke.php?sId={$IN['sId']}&type=attach&o=upload&mode=picker&changeName=1&NodeID={$IN['NodeID']}"  
//     config.lang_fix ={
// 					"localize" => "图片本地化",
// 					"pagebreak"  : "插入分页符",
// 					"editpagebreak" : "编辑分页符",
// 					"pagebreaktitle" : "插入/编辑分页符",
// 					"pagetitle" : "分页子标题",
// 					"insertimage" : "插入图片",
// 					"browseServer" : "发布点PSN选择",
// 					"attachtitle" : "插入文件",
// 					"attachfile" : "插入附件下载",
// 					"attachfiletitle" : "插入附件",
// 					"attachfileurl" : "链接地址",
// 					"attachfiletxt" : "显示文件名",
// 					"attachfileurlempty" : "链接地址不能为空",
// 					"attachmedia" : "插入/修改多媒体",
// 					"editattachmedia" : "修改多媒体",
// 					"attachmediavars" : "多媒体变量",
// 					"media" : {
// 						"video" : "视频高度方案",
// 						"audio" : "音频高度方案",
// 						"autostart" : "自动开始",
// 						"enablecontextmenu" : "允许右键菜单",
// 						"clicktoplay" : "允许鼠标点击播放/暂停",
// 						"showcontrols" : "显示控制栏",
// 						"showstatusbar" : "显示状态",
// 						"showdisplay" : "显示多媒体信息",
// 						"loop" : "循环播放",
// 					}
//     };


config.Localize = false;
config.removePlugins = "pagebreak";
config.videopreset = array(300,250);
config.audiopreset = array(300,68);
config.downicon = "<?php TplVarsAdmin::getValue('PUBLISH_URL') ?>/images/icon/%s.gif";
config.extraPlugins = "checkbox,cmswareforms,cmswarelocal,addhtml,cmswarecss,cmswarepagebreak,cmswareattach";

config.enterMode = "CKEDITOR.ENTER_BR";
config.shiftEnterMode = "CKEDITOR.ENTER_P";
// config.contentsCss = "<CKEditor folder>/cmsware.css";
// config.image_plus = {
// 			"changeName" : {
// 				'type'    : 'checkbox',
// 				'id'      : 'changeName',
// 				'default' : true,
// 				'label'   : '上传后重命名'
// 			},
// 			"makeMinibox" : {
// 				'type'    : 'vbox',
// 				'padding' : 0,
// 				'width'   : '200px',
// 				'style'   : 'width:200px',
// 				'children': {
// 					{
// 						'type'    : 'checkbox',
// 						'id'      : 'makeMini',
// 						'default' : false,
// 						'label'   : '生成缩略图'
// 					},
// 					{
// 						'type'    : 'hbox',
// 						'widths'  : { '100px', '100px' },
// 						'align'   : 'left',
// 						'children': {
// 							{
// 								'type'     : 'text',
// 								'id'       : 'width',
// 								'label'    : '长',
// 								'default'  : '160'
// 							},
// 							{
// 								'type'     : 'text',
// 								'id'       : 'height',
// 								'validate' : false,
// 								'label'    : '宽',
// 								'default'  : '120',
// 							}
// 						},
// 					}
// 				}
// 			},
// 			"mkHTML" : {
// 				'type'    : 'vbox',
// 				'padding' : 1,
// 				'width'   : '200px',
// 				'style' 	: 'width:200px',
// 				'children': {
// 					{
// 						'type'    : 'checkbox',
// 						'id'      : 'mkHTML',
// 						'default' : false,
// 						'label'   : '大图生成网页',
// 					},
// 					{
// 						'type'    : 'text',
// 						'id'      : 'img_title',
// 						'label'   : '图片标题'
// 					},
// 					{
// 						'type'    : 'textarea',
// 						'id'      : 'img_intro',
// 						'label'   : '图片简介'
// 					},
// 				}
// 			}
// 		}
	
};


    // Replace the <textarea id="editor1"> with a CKEditor
    // instance, using default configuration.
 CKEDITOR.replace( 'data_<?php echo $var["FieldName"] ?>', {
	filebrowserBrowseUrl: <?php echo "'./admin_select.php?sId={$IN['sId']}&o=psn_picker&psn='"?>,
	filebrowserUploadUrl: <?php echo "'./upload_cke.php?sId={$IN['sId']}&type=img&o=upload&mode=one&NodeID={$IN['NodeID']}'" ?>,
	filebrowserWindowWidth : "600",
    filebrowserWindowHeight : "266",
    filebrowserImageBrowseLinkUrl : false,
    filebrowserFlashUploadUrl : <?php echo "'./upload_cke.php?sId={$IN['sId']}&type=flash&o=upload&mode=picker&changeName=1&NodeID={$IN['NodeID']}'" ?>,
    filebrowserAttachfileUploadUrl : <?php echo "'./upload_cke.php?sId={$IN['sId']}&type=attach&o=upload&mode=picker&changeName=1&NodeID={$IN['NodeID']}'" ?> 
});

</script>


