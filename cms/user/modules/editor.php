<?php   
if(!defined('IN_IWPC')) {
 	exit('Access Denied');
}
include_once(INCLUDE_PATH."editor/class.devedit.php");
?>
<html>
<head>
<title></title>
<link type="text/css" rel="StyleSheet" href="../html/style.css" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<script src="ui.php?sId=<?=$IN['sId']?>&o=functions.js" type="text/javascript" language="javascript"></script>
<SCRIPT language=JavaScript>
var NodeID = '<?=$IN['NodeID']?>';
var IndexID = '0';
var CateID = '<?=$IN['CateID']?>';
var sId = '<?=$IN['sId']?>';
var PUBLISH_URL="<?=$PUBLISH_URL?>";

var FieldDefaultValue = "";
var FieldInputTpl = "";


function setContentLinkValue(fieldName)
{
	eval("obj = document.FM.data_" + fieldName);
 	var returnValue;

	with(obj) {
 		for(i=0; i <  obj.length ; i++){
			if(i==0) {
				returnValue = options[i].value;
			} else {
				returnValue = returnValue + ',' + options[i].value;
			}
 		} 
 		
	}
	if(returnValue == 'undefined') {
		returnValue = '';
	}
 	return returnValue;

}
function add(fieldName, param_index_id,param_title) {
	eval("obj = document.FM.data_" + fieldName);
	with(obj) {
		length=obj.length
		/*if(data.length > 24) {
			var data1 = "..." + data.substr(data.length-24 ,24)
		} else {
			var data1 = data
		}*/
 
		options[length]=new Option(param_title,param_index_id)
		
	}
	
}
function picker_content_add(fieldName, param_title) {
 	eval("obj = document.FM." + fieldName);

	if(obj.value != '') {
		obj.value=obj.value + ',' + param_title;
	
	} else {
		obj.value=  param_title;
	
	}
		
	
}
function InputPicker(action, form, element)
{
	
	switch(action) {
		case 'color':
			var arr = showModalDialog("../html/color.htm","color","dialogWidth:200pt;dialogHeight:175pt;help:0;status:0");	break;
		case 'date':
			showCalendar(element, 'y-mm-dd');
			break;
		case 'upload':
			var arr = showModalDialog('upload.php?sId='+ sId +'&o=display&mode=one&type=img_picker&NodeID=' + NodeID,"color","dialogWidth:390px;dialogHeight:120px;help:0;status:0;scroll:no");
			break;
		case 'upload_attach':
			var returnValue = showModalDialog('upload.php?sId='+ sId +'&o=display&mode=one&type=attach&NodeID=' + NodeID,"color","dialogWidth:390px;dialogHeight:120px;help:0;status:0;scroll:no");
			var arr = returnValue['url'];
			break;
		case 'tpl':
			with(form){

				eval("var varlue1=" + element + ".value;")

			}

			var arr = showModalDialog("admin_select.php?sId=<?=$IN['sId']?>&o=tpl&tpl=" + varlue1,"color","dialogWidth:428px;dialogHeight:266px;help:0;status:0;scroll:no");
			break;	
		case 'psn':
			with(form){

				eval("var varlue1=" + element + ".value;")

			}
			
			var info = showModalDialog("admin_select.php?sId=<?=$IN['sId']?>&o=psn_picker&psn=" + varlue1 ,"color","dialogWidth:600px;dialogHeight:266px;help:0;status:0;scroll:no");
			if(info['filename'] != null) {
				var arr= '{PSN-URL:'+ info['PSNID'] + "}" +info['filename'];
			}
			break;
		case 'content':
			var leftPos = screen.availWidth / 2 
			var topPos = screen.availHeight / 2 
			var MyWIN = window.open("admin_publish.php?sId=" + sId + "&o=picker_content&extra=ui_init&IndexID=" + IndexID + "&fieldName=" + element + "&NodeID=" + NodeID,'','width=500,height=380,scrollbars=no,resizable=yes,titlebar=0,top=' + topPos + ',left=' + leftPos);
			break;
		case 'url_content':
			var leftPos = screen.availWidth / 2 ;
			var topPos = screen.availHeight / 2 ;
			
			if(FieldInputTpl != "") {
				var sizeArray = FieldInputTpl.split("*");
			} else {
				var sizeArray = new Array();
				sizeArray[0] = 500;
				sizeArray[1] = 380;
			}

			 
			var MyWIN = window.open("ui.php?sId="+ sId + "&o=picker_url_content&extra=ui_init&IndexID=" + IndexID + "&fieldName=" + element + "&NodeID=" + NodeID + "&url=" + urlencode(FieldDefaultValue),'','width=' + sizeArray[0] +',height='  + sizeArray[1]+',scrollbars=no,resizable=yes,titlebar=0,top=' + topPos + ',left=' + leftPos);
			break;
	}

	if(arr != null && action != 'content' ) {
		with(form){

			eval(element + ".value= '" +  arr + "'")

		}


	}

}

</script>
<body bgcolor=threedface STYLE="margin:0pt;padding:0pt;border: 1px buttonhighlight;">
<form action="admin_contribution.php?sId=<?=$IN['sId']?>&o=<?=$IN['o']?>_submit&CateID=<?=$IN['CateID']?>&ContributionID=<?=$pInfo['ContributionID']?>&NodeID=<?=$IN['NodeID']?>" method="post" name="FM" ><!--actionFrame-->
<table width="100%" border=0   cellPadding=0 cellSpacing=5 >
<?php
if($IN[o] == 'add') {
echo "<tr class='tablelist'> 
              <td align=right width=75>直接投稿：</td>
              <td ><input type='checkbox' name='isContribution' value='1' checked></td>
</tr>";
		  
}
?>
<tr class='tablelist'> 
              <td align=right width=75>投稿主节点：</td>
              <td ><select  name="TargetNodeID" id = "TargetNodeID">
<?php
foreach($NODE_LIST as $key=>$var) {
	if($var[WorkFlow] == 0 || $var[TableID]!=$CateInfo[TableID]) continue;

	if($pInfo[NodeID] == $var[NodeID] || $IN[NodeID] == $var[NodeID]) {
		echo "<option value='{$var[NodeID]}' selected>".str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $var[cHeader])." - &nbsp;{$var[Name]}</option>";
	
	} elseif($IN[o] == 'add' && $CateInfo[NodeID] ==  $var[NodeID]) {
		echo "<option value='{$var[NodeID]}' selected>".str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $var[cHeader])." - &nbsp;{$var[Name]}</option>";
	
	} else {
		echo "<option value='{$var[NodeID]}'>".str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $var[cHeader])." - &nbsp;{$var[Name]}</option>";	
	}
}

 ?>
 </select></td>
</tr>
<tr class='tablelist'> 
              <td align=right ></td>
              <td valign=top>
			<table>
			<tr>
			<td>投稿虚链接节点：</td>
			<td> 投稿索引链接节点</td>
			</tr>
			<tr>
			<td>
<select  name="SubTargetNodeID[]" id = "SubTargetNodeID"  size="10" multiple>
<option value='' ><?echo $_LANG_SKIN['null']; ?></option>
<?php
foreach($NODE_LIST as $key=>$var) {
	if($var[WorkFlow] == 0 || $var[TableID]!=$CateInfo[TableID]) continue;

	if(in_array($var[NodeID], $pInfo[SubNodeIDs]) && $IN[o] == 'edit') {
		echo "<option value='{$var[NodeID]}' selected>".str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $var[cHeader])." - &nbsp;{$var[Name]}</option>";
	
	} elseif($IN[o] == 'add' && in_array($var[NodeID], $CateInfo[SubNodeIDs])) {
		echo "<option value='{$var[NodeID]}' selected>".str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $var[cHeader])." - &nbsp;{$var[Name]}</option>";
	
	} else {
		echo "<option value='{$var[NodeID]}'>".str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $var[cHeader])." - &nbsp;{$var[Name]}</option>";	
	}
}

 ?>
 </select>	

</td>
			<td> 
<select  name="IndexTargetNodeID[]" id = "IndexTargetNodeID"  size="10" multiple>
<option value='' ><?echo $_LANG_SKIN['null']; ?></option>
<?php
foreach($NODE_LIST as $key=>$var) {
	if($var[WorkFlow] == 0 || $var[TableID]!=$CateInfo[TableID]) continue;

	if(in_array($var[NodeID], $pInfo[IndexNodeIDs]) && $IN[o] == 'edit') {
		echo "<option value='{$var[NodeID]}' selected>".str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $var[cHeader])." - &nbsp;{$var[Name]}</option>";
	
	} elseif($IN[o] == 'add' && in_array($var[NodeID], $CateInfo[IndexNodeIDs])) {
		echo "<option value='{$var[NodeID]}' selected>".str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $var[cHeader])." - &nbsp;{$var[Name]}</option>";
	
	} else {
		echo "<option value='{$var[NodeID]}'>".str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $var[cHeader])." - &nbsp;{$var[Name]}</option>";	
	}
}

 ?>
 </select>	
</td>
			</tr>
			</table>

 
			  
			  </td>
</tr>
<?php
//--------------------------------------------------------
foreach( $tableInfo as $key=>$var) {
	if(empty($var['EnableContribution'])) continue;

	if(($var[FieldInput] == 'text' || $var[FieldInput] == 'textaera'|| $var[FieldInput] == 'RichEditor') && $var[FieldInputPicker] != 'url_content' && $IN[o] == 'add' && !empty($var[FieldInputTpl])) {
		
		require_once INCLUDE_PATH."admin/psn_admin.class.php";
		$psn = new psn_admin();
		$psnInfo[PSN] = 'file::'.$SYS_ENV['templatePath'];

		$pathInfo = pathinfo($var[FieldInputTpl]);
		$psn->connect($psnInfo[PSN]);
		$content = $psn->read($pathInfo['dirname'], $pathInfo['basename']);
		$psn->close();

		$pInfo[$var['FieldName']] = $content;
	}


		echo " <tr class='tablelist'> 
              <td align=right  width=80>{$var[FieldTitle]}:</td>
              <td valign='middle'>";
	
	

	if($var[FieldInput] == 'text' && $var[FieldType] != 'contentlink') { //单行文本
		if($var[FieldInputFilter] == 'num_letter') {
			echo "<input name='data_{$var[FieldName]}' type='text' value='{$pInfo[$var['FieldName']]}' size=100%     onkeyup=\"value=value.replace(/[\W]/g,'') \" onbeforepaste=\"clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))\">";
		
		} elseif($var[FieldInputFilter] == 'num') {
			echo "<input name='data_{$var[FieldName]}' type='text' value='{$pInfo[$var['FieldName']]}' size=100%   onkeydown=\"onlyNum();\">";
		
		}else {
			echo "<input name='data_{$var[FieldName]}' type='text' value='{$pInfo[$var['FieldName']]}' size=100%  >";
		
		}

		if(!empty($var[selectValue])) {
				echo "&nbsp;&nbsp;&nbsp;&nbsp;<select name='{$var[FieldName_select]}'  onchange=\"if(this.options[this.selectedIndex].value != '') { this.form.data_{$var[FieldName]}.value= this.options[this.selectedIndex].value;}\"> 				<option value=''>可选值:</option>";

				foreach($var[selectValue] as $var) {
					echo "<option value='$var'>$var</option>";			
				}
				echo "</select>";
		
		} elseif(!empty($var[FieldInputPicker])) {
			if($var[FieldInputPicker] == 'upload') {
 				echo ' <img  style="position: relative;left:0px;top: 4px;" src="../html/images/menu_open.gif" class="Dtoolbutton" onmouseover="this.className=\'Dtoolbutton\';" onmouseout="this.className=\'Dtoolbutton\';" onclick="this.className=\'Ctoolbutton\';commonInputPicker(\'img\', \'document.FM\', \'data_'.$var[FieldName].'\')" title="" hspace="0" vspace="0">';
			} elseif($var[FieldInputPicker] == 'upload_attach') {
 				 	echo ' <img  style="position: relative;left:0px;top: 4px;" src="../html/images/menu_open.gif" class="Dtoolbutton" onmouseover="this.className=\'Dtoolbutton\';" onmouseout="this.className=\'Dtoolbutton\';" onclick="this.className=\'Ctoolbutton\';commonInputPicker(\'attach\', \'document.FM\', \'data_'.$var[FieldName].'\')" title="" hspace="0" vspace="0">';
			} else {
				//echo "&nbsp;<input name=\"button5\" type='button' tabindex='13' value='...' onclick=\"InputPicker('{$var[FieldInputPicker]}',this.form,'data_{$var[FieldName]}')\">";
				echo "&nbsp;<input name=\"button5\" type='button' tabindex='13' value='...' onclick=\"FieldInputTpl='{$var[FieldInputTpl]}';FieldDefaultValue='{$var[FieldDefaultValue]}';InputPicker('{$var[FieldInputPicker]}',this.form,'data_{$var[FieldName]}')\">";			
			}

		
		}



	} elseif($var[FieldInput] == 'textaera' && $var[FieldType] != 'contentlink') { //多行文本

		if($var[FieldInputFilter] == 'num_letter') {
			echo "<textarea name='data_{$var[FieldName]}' class='button' id='{$var[FieldName]}' style='height:150;width=100%;overflow:auto; background-color:#FFFFFF;' onkeyup=\"value=value.replace(/[\W]/g,'') \" onbeforepaste=\"clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))\">{$pInfo[$var['FieldName']]}</textarea>";
		
		} elseif($var[FieldInputFilter] == 'num') {
			echo "<textarea name='data_{$var[FieldName]}' class='button' id='{$var[FieldName]}' style='height:150;width=100%;overflow:auto; background-color:#FFFFFF;' onkeydown=\"onlyNum();\">{$pInfo[$var['FieldName']]}</textarea>";
		
		} else {
			echo "<textarea name='data_{$var[FieldName]}' class='button' id='{$var[FieldName]}' style='height:150;width=100%;overflow:auto; background-color:#FFFFFF;' >{$pInfo[$var['FieldName']]}</textarea>";
		
		}

	} elseif($var[FieldInput] == 'checkbox' && $var[FieldType] != 'contentlink') { //多选
		foreach($var[selectValue] as $key=>$varln) {
			if(strpos('hll'.$pInfo[$var['FieldName']], $varln)) {
				echo "<input type='checkbox' name='data_{$var[FieldName]}[]' value='{$varln}' id='{$var[FieldName]}_{$key}' checked ><label for='{$var[FieldName]}_{$key}'>{$varln}</label>";
			
			} else {
				echo "<input type='checkbox' name='data_{$var[FieldName]}[]' value='{$varln}' id='{$var[FieldName]}_{$key}' ><label for='{$var[FieldName]}_{$key}'  >{$varln}</label> ";
			
			}
		}
	} elseif($var[FieldInput] == 'radio' && $var[FieldType] != 'contentlink') { //单选
		foreach($var[selectValue] as $key=>$varln) {

			if($pInfo[$var['FieldName']] == $varln) {
				echo "<input type='radio' name='data_{$var[FieldName]}' value='{$varln}' id='{$var[FieldName]}_{$key}' checked ><label for='{$var[FieldName]}_{$key}'>{$varln}</label>";
			
			} else {
				echo "<input type='radio' name='data_{$var[FieldName]}' value='{$varln}' id='{$var[FieldName]}_{$key}' ><label for='{$var[FieldName]}_{$key}'  >{$varln}</label> ";
			
			}
		}

	} elseif($var[FieldInput] == 'select' && $var[FieldType] != 'contentlink') { //下拉菜单选择
		echo "<select name='data_{$var[FieldName]}'>";

		foreach($var[selectValue] as $keyIn=>$varIn) {
			if($pInfo[$var['FieldName']] == $varIn) {
				echo "<option value='{$varIn}' selected>{$varIn}</option>";
			} else {
				echo "<option value='{$varIn}'>{$varIn}</option>";
			}
		
		}

		echo "</select>";

	} elseif($var[FieldInput] == 'password' && $var[FieldType] != 'contentlink') { //密码
		echo "<input name='data_{$var[FieldName]}' type='password' >";

	} elseif($var[FieldType] == 'contentlink') { //
	
		echo "<table   border=0  cellPadding=2 cellSpacing=0 ><tr><td ><select name='data_{$var[FieldName]}' name='select' size='10'>";
		
		$Links = explode(',', $pInfo[$var['FieldName']]);
		//print_r($Links);
 		if(!empty($Links[0])) {
			foreach($Links as $keyIn=>$varIn) {
				$info = $publish->editor_getContentInfo($varIn);
				$info_key = $CONTENT_MODEL_INFO[$var['TableID']]['TitleField'];
 				echo "<option  value='{$info['IndexID']}' >{$info[$info_key]}</option>";
			}
		}
		
		
 
		echo "</select><INPUT TYPE='hidden' name='data_{$var[FieldName]}_value'></td>";
		echo "<td class='line_height'>&nbsp;<input name='button5' type='button' tabindex='13' value='×' onclick=del(this.form.data_{$var[FieldName]})><br><br>&nbsp;<input name='button5' type='button' tabindex='13' value='∧' onclick=moveUp(this.form.data_{$var[FieldName]})><br>&nbsp;<input name='button5' type='button' tabindex='13' value='∨' onclick=moveDown(this.form.data_{$var[FieldName]})><br><br>&nbsp;<input name=\"button5\" type='button' tabindex='13' value='...' onclick=editContentLink('{$var[FieldName]}')>";
		echo "</td><td>&nbsp;<input name='button5' type='button' tabindex='13' value='&nbsp;Go&nbsp;' onclick=GoSelect(this.form.data_{$var[FieldName]})></td></tr></table>";

	
	} elseif($var[FieldInput] == 'RichEditor') { //可视化编辑器
	$LibType = true;
	// Create a new DevEdit class object
	$myDE = new devedit;
	
	$myDE->Libtype=$LibType;
	
	// Set the name of this DevEdit class
	$myDE->SetName("data_{$var[FieldName]}");

	// Set the path to the de folder
	//$DevEditPath_Full = "/admin/editor";
	SetDevEditPath( INCLUDE_PATH."editor");
	//$DevEditPath = "editor/";
	$a = pathinfo($_SERVER["PHP_SELF"]);
	$myDE->AdminPath = $a[dirname];
	$myDE->sId = $IN['sId'];
 	// Set the path to the folder that contains the flash files for the flash manager
	//$myDE->SetFlashPath("/icms/site_image/$parent/flash");

	// These are the functions that you can call to hide varions buttons,
	// lists and tab buttons. By default, everything is enabled

	//$myDE->HideFullScreenButton();		//隐藏全屏按钮
	//$myDE->HideBoldButton();		隐藏粗体按钮
	//$myDE->HideUnderlineButton();		隐藏下划线按钮
	//$myDE->HideItalicButton();		隐藏斜体按钮
	//$myDE->HideStrikethroughButton();
	//$myDE->HideNumberListButton();
	//$myDE->HideBulletListButton();
	//$myDE->HideDecreaseIndentButton();
	//$myDE->HideIncreaseIndentButton();
	//$myDE->HideLeftAlignButton();
	//$myDE->HideCenterAlignButton();
	//$myDE->HideRightAlignButton();
	//$myDE->HideJustifyButton();
	//$myDE->HideHorizontalRuleButton();
	//$myDE->HideLinkButton();
	//$myDE->HideAnchorButton();
	//$myDE->HideMailLinkButton();
	//$myDE->HideHelpButton();
	//$myDE->HideFontList();
	//$myDE->HideSizeList();
	$myDE->HideSaveButton();
	//$myDE->HideFormatList();
	$myDE->HideStyleList();
	//$myDE->HideForeColorButton();
	//$myDE->HideBackColorButton();
	//$myDE->HideTableButton();
	//$myDE->HideFormButton();
	//$myDE->HideImageButton();
	//$myDE->HideFlashButton();
	//$myDE->DisableFlashUploading();
	//$myDE->DisableFlashDeleting();
	//$myDE->DisableInsertFlashFromWeb();
	//$myDE->HideTextBoxButton();
	//$myDE->HideSymbolButton();
	$myDE->HidePropertiesButton();		//隐藏页面属性按钮
	//$myDE->HideCleanHTMLButton();
	//$myDE->HidePositionAbsoluteButton();
	$myDE->HideSpellingButton();		//隐藏拼写检查按钮
	//$myDE->HideRemoveTextFormattingButton();
	//$myDE->HideSuperScriptButton();
	//$myDE->HideSubScriptButton();
	//$myDE->HideGuidelinesButton();
	//$myDE->DisableSourceMode();
	//$myDE->DisablePreviewMode();
	//$myDE->DisableImageUploading();
	//$myDE->DisableImageDeleting();
	//$myDE->DisableXHTMLFormatting();
	//$myDE->DisableSingleLineReturn();
	//$myDE->DisableInsertImageFromWeb();
	
	//If you want to use the spell checker, then you can set
	//the spelling language to DE_AMERICAN, DE_BRITISH or DE_CANADIAN,
	//DE_FRENCH, DE_SPANISH, DE_GERMAN, DE_ITALIAN, DE_PORTUGESE,
	//DE_DUTCH, DE_NORWEGIAN, DE_SWEDISH or DE_DANISH
	$myDE->SetLanguage(DE_AMERICAN);

	//We can specify a list of fonts for the font drop down. If we don't,
	//then a default list will show
	//$myDE->SetFontList("Arial,Verdana");

	//We can specify a list of font sizes for the font size drop down. If we don't,
	//then a default list will show
	//$myDE->SetFontSizeList("8,10");

	//How do we want images to be inserted into our HTML content?
	//DE_PATH_TYPE_FULL will insert a image in this format: http://www.mysite.com/test.html
	//DE_PATH_TYPE_ABSOLUTE will insert a image in this format: /myimage.gif
	$myDE->SetPathType(DE_PATH_TYPE_FULL);
	
	//Are we editing a full HTML page, or just a snippet of HTML?
	//DE_DOC_TYPE_HTML_PAGE means we're editing a complete HTML page
	//DE_DOC_TYPE_SNIPPET means we're editing a snippet of HTML
	$myDE->SetDocumentType(DE_DOC_TYPE_HTML_PAGE);
	
	//Do we want images to appear in the image manager as thumbnails or just in rows?
	//DE_IMAGE_TYPE_ROW means just list in a tabular format, without a thumbnail
	//DE_IMAGE_TYPE_THUMBNAIL means list in 4-per-line thumbnail mode
	$myDE->SetImageDisplayType(DE_IMAGE_TYPE_THUMBNAIL);
	
	//Do we want flash files to appear in the flash manager as thumbnails or just in rows?
	//DE_FLASH_TYPE_ROW means just list in a tabular format, without a thumbnail
	//DE_FLASH_TYPE_THUMBNAIL means list in 4-per-line thumbnail mode
	$myDE->SetFlashDisplayType(DE_FLASH_TYPE_THUMBNAIL);
	
	//Show table guidelines as dashed
	$myDE->EnableGuidelines();
	
	//If the user isnt running Internet Explorer, then a <textarea> tag will be shown.
	//This function will set the rows and cols of that <textarea>
	$myDE->SetTextAreaDimensions(60, 90);

	// Add some custom links that will appear in the link manager
	//$myDE->AddCustomLink("DevEdit", "http://www.devedit.com");
	//$myDE->AddCustomLink("Interspire", "http://www.interspire.com", "_new");

	$val = "";

	if($myDE->GetValue(false) == ""){
		/*$Tplsql = "SELECT TplPath FROM $tbl_article_type WHERE Parent=$parent and Id=$LibType";
		$Tplrow = DBQueryAndFetchRow($Tplsql);
		if ($Tplrow[TplPath] && !$row[Content]){
		$tpl = fopen("../templates/".$parent."_tpl/$Tplrow[TplPath]", "r");
		$tpl = fread($tpl, 200000);
		$val = $tpl;
		} else {
		$val = $row[Content];
		}*/
		$val = $pInfo[$var[FieldName]];
	}else{
		$val = $myDE->GetValue(false);
	}
	//Set the initial HTML value of our control
	$myDE->SetValue($val);
	
	// Use the LoadHTMLFromMySQLQuery function to load a value based on a query
	// $errDesc = "";
	// $myDE->LoadHTMLFromMySQLQuery("localhost", "testdatabase", "admin", "password", "select bContent from blah limit 1", $errDesc);
	// if($errDesc != "")
	// { echo "An error occured: $errDesc"; }
	
	// Use the LoadFromFile function to load a complete text file
	// $errDesc = "";
	// $myDE->LoadFromFile("mysite.html", $errDesc);
	// if($errDesc != "")
	// { echo "An error occured: $errDesc"; }
	
	// Use the SaveToFile function to save the contents of the DevEdit control to a text file
	// $errDesc = "";
	// $myDE->SaveToFile("c:\test.html", $errDesc);
	// if($errDesc != "")
	// { echo "An error occured: $errDesc"; }
	
	// Use the AddCustomInsert function to add some custom inserts
	//$myDE->AddCustomInsert("DevEdit Logo", "<img src='http://www.devedit.com/images/logo.gif'>");
	//$myDE->AddCustomInsert("Red Text", "<font face='verdana' color='red' size='3'><b>Red Text</b></font>");

	// Use the AddImageLibrary function to add image libraries
	//$myDE->AddImageLibrary("图片库 #1", "/icms/publish/test_images");
	//$myDE->AddImageLibrary("图片库 #2", "/icms/publish/test_images/sub");

	// Use the AddFlashLibrary function to add flash libraries
	//$myDE->AddFlashLibrary("Flash库 #1", "/icms/publish/test_flash");
	//$myDE->AddFlashLibrary("Flash库 #2", "/icms/publish/test_flash/sub");

	//Display the DevEdit control. This *MUST* be called between <form> and </form> tags
	$myDE->ShowControl("100%", "500",'');	
	//Display the rest of the form

	}      
echo " </td> </tr>";
}
?>
 </td>
 </tr>
</table>

</form>
</body>
</html>