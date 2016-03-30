<?php
//\\(\$IN\[['a-zA-Z]+\])\
//"echo\s+\\(\$_LANG_SKIN\[['_a-zA-Z]+\]);\s+\? >""
if ( !defined( "IN_IWPC" ) )
{
	exit( "Access Denied" );
}
include_once( INCLUDE_PATH."editor/class.devedit.php" );
require_once( LANG_PATH.$SYS_ENV['language']."/lang_skin/admin/contribution_editor.php" );
echo "<html>\r\n<head>\r\n<title></title>\r\n<link type=\"text/css\" rel=\"StyleSheet\" href=\"../html/style.css\" />\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8";
echo "echo CHARSET;?>\">\r\n</head>\r\n";
echo "<script src=\"ui.php?sId=";
echo "{$IN['sId']}&o=functions.js\" type=\"text/javascript\" language=\"javascript\"></script>\r\n";
echo "<SCRIPT language=JavaScript>\r\nvar NodeID = '";
echo "{$IN[NodeID]}';\r\nvar sId = '";
echo "{$IN['sId']}';\r\n</script>\r\n<body bgcolor=threedface STYLE=\"margin:0pt;padding:0pt;border: 1px buttonhighlight;\">\r\n<form action=\"admin_contribution.php?sId=";
echo "{$IN['sId']}&o=";
echo "{$IN['o']}_submit&NodeID=";
echo "{$IN['NodeID']}&ContributionID=";
echo "{$pInfo['ContributionID']}\" method=\"post\" name=\"FM\" ><!--actionFrame-->\r\n<table width=\"100%\" border=0   cellPadding=0 cellSpacing=5 >\r\n<tr class='tablelist'> \r\n              <td align=right width=75>";
echo "{$_LANG_SKIN['OwnerName']}:</td>\r\n              <td >";
echo "{$pInfo[OwnerName]}</td>\r\n</tr>\r\n\r\n<tr class='tablelist'> \r\n              <td align=right width=75>";
echo "{$_LANG_SKIN['TargetNodeID']}:</td>\r\n              <td >";
echo "<select  name=\"TargetNodeID\" id = \"TargetNodeID\">\r\n";
foreach ( $NODE_LIST as $key => $var )
{
	if ( $pInfo[NodeID] == $var[NodeID] )
	{
		echo "<option value='{$var[NodeID]}' selected>".str_repeat( "&nbsp;&nbsp;&nbsp;&nbsp;", $var[cHeader] )." - &nbsp;{$var[Name]}</option>";
	}
	else if ( $IN[o] == "add" && $CateInfo[NodeID] == $var[NodeID] )
	{
		echo "<option value='{$var[NodeID]}' selected>".str_repeat( "&nbsp;&nbsp;&nbsp;&nbsp;", $var[cHeader] )." - &nbsp;{$var[Name]}</option>";
	}
	else
	{
		echo "<option value='{$var[NodeID]}'>".str_repeat( "&nbsp;&nbsp;&nbsp;&nbsp;", $var[cHeader] )." - &nbsp;{$var[Name]}</option>";
	}
}
echo " </select></td>\r\n</tr>\r\n<tr class='tablelist'> \r\n              <td align=right >";
echo "{$_LANG_SKIN['SubTargetNodeID']}:</td>\r\n              <td>\r\n\r\n<table>\r\n\t\t\t<tr>\r\n\t\t\t<td>";
echo "{$_LANG_SKIN['SubTargetSubNodeID']}:</td>\r\n\t\t\t<td>";
echo "{$_LANG_SKIN['SubTargetIndexNodeID']}:</td>\r\n\t\t\t</tr>\r\n\t\t\t<tr>\r\n\t\t\t<td>\r\n";
echo "<select  name=\"SubTargetNodeID[]\" id = \"SubTargetNodeID\"  size=\"10\" multiple>\r\n<option value='' >";
echo "{$_LANG_SKIN['null']}</option>\r\n";
foreach ( $NODE_LIST as $key => $var )
{
	if ( in_array( $var[NodeID], $pInfo[SubNodeIDs] ) )
	{
		echo "<option value='{$var[NodeID]}' selected>".str_repeat( "&nbsp;&nbsp;&nbsp;&nbsp;", $var[cHeader] )." - &nbsp;{$var[Name]}</option>";
	}
	else if ( $IN[o] == "add" && in_array( $var[NodeID], $CateInfo[SubNodeIDs] ) )
	{
		echo "<option value='{$var[NodeID]}' selected>".str_repeat( "&nbsp;&nbsp;&nbsp;&nbsp;", $var[cHeader] )." - &nbsp;{$var[Name]}</option>";
	}
	else
	{
		echo "<option value='{$var[NodeID]}'>".str_repeat( "&nbsp;&nbsp;&nbsp;&nbsp;", $var[cHeader] )." - &nbsp;{$var[Name]}</option>";
	}
}
echo " </select>\t\t\t  \r\n\r\n\r\n</td>\r\n\t\t\t<td> \r\n\r\n\r\n\r\n\r\n ";
echo "<select  name=\"IndexTargetNodeID[]\" id = \"IndexTargetNodeID\"  size=\"10\" multiple>\r\n<option value='' >";
echo "{$_LANG_SKIN['null']}</option>\r\n";
foreach ( $NODE_LIST as $key => $var )
{
	if ( in_array( $var[NodeID], $pInfo[IndexNodeIDs] ) )
	{
		echo "<option value='{$var[NodeID]}' selected>".str_repeat( "&nbsp;&nbsp;&nbsp;&nbsp;", $var[cHeader] )." - &nbsp;{$var[Name]}</option>";
	}
	else if ( $IN[o] == "add" && in_array( $var[NodeID], $CateInfo[IndexNodeIDs] ) )
	{
		echo "<option value='{$var[NodeID]}' selected>".str_repeat( "&nbsp;&nbsp;&nbsp;&nbsp;", $var[cHeader] )." - &nbsp;{$var[Name]}</option>";
	}
	else
	{
		echo "<option value='{$var[NodeID]}'>".str_repeat( "&nbsp;&nbsp;&nbsp;&nbsp;", $var[cHeader] )." - &nbsp;{$var[Name]}</option>";
	}
}
echo " </select>\t\t\t  \r\n\r\n</td>\r\n\t\t\t</tr>\r\n\t\t\t</table>\r\n\r\n\r\n\t\t\t  \r\n\t\t\t  </td>\r\n</tr>\r\n\r\n";
foreach ( $tableInfo as $key => $var )
{
	if ( empty( $var['EnableContribution'] ) )
	{
		continue;
	}
	echo " <tr class='tablelist'> \r\n              <td align=right width=70>{$var[FieldTitle]}:</td>\r\n              <td >";
	if ( $var[FieldInput] == "text" )
	{
		echo "<input name='data_{$var[FieldName]}' type='text' value='{$pInfo[$var['FieldName']]}' size=80%>";
		if ( !empty( $var[selectValue] ) )
		{
			echo "&nbsp;&nbsp;&nbsp;&nbsp;<select name='{$var[FieldName_select]}'  onchange=\"if(this.options[this.selectedIndex].value != '') { this.form.data_{$var[FieldName]}.value= this.options[this.selectedIndex].value;}\"> \t\t\t\t<option value=''>可选值:</option>";
			foreach ( $var[selectValue] as $var )
			{
				echo "<option value='{$var}'>{$var}</option>";
			}
			echo "</select>";
		}
	}
	else if ( $var[FieldInput] == "textaera" )
	{
		echo "<textarea name='data_{$var[FieldName]}' class='button' id='{$var[FieldName]}' style='height:70;width=80%;overflow:auto; background-color:#FFFFFF;scrollbar-face-color: #FFFFFF;scrollbar-highlight-color: #FFFFFF;scrollbar-shadow-color: #cccccc;scrollbar-3dlight-color: #cccccc;scrollbar-arrow-color:  #cccccc;scrollbar-track-color: #FFFFFF;scrollbar-darkshadow-color: #cccccc;' >{$pInfo[$var['FieldName']]}</textarea>";
	}
	else if ( $var[FieldInput] == "checkbox" )
	{
		foreach ( $var[selectValue] as $key => $var )
		{
			if ( strpos( "hll".$pInfo[$var['FieldName']], $var ) )
			{
				echo "<input type='checkbox' name='data_{$var[FieldName]}[]' value='{$var}' id='{$var[FieldName]}_{$key}' checked ><label for='{$var[FieldName]}_{$key}'>{$var}</label>";
			}
			else
			{
				echo "<input type='checkbox' name='data_{$var[FieldName]}[]' value='{$var}' id='{$var[FieldName]}_{$key}' ><label for='{$var[FieldName]}_{$key}'  >{$var}</label> ";
			}
		}
	}
	else if ( $var[FieldInput] == "radio" )
	{
		foreach ( $var[selectValue] as $key => $var )
		{
			if ( $pInfo[$var['FieldName']] == $var )
			{
				echo "<input type='checkbox' name='data_{$var[FieldName]}[]' value='{$var}' id='{$var[FieldName]}_{$key}' checked ><label for='{$var[FieldName]}_{$key}'>{$var}</label>";
			}
			else
			{
				echo "<input type='checkbox' name='data_{$var[FieldName]}[]' value='{$var}' id='{$var[FieldName]}_{$key}' ><label for='{$var[FieldName]}_{$key}'  >{$var}</label> ";
			}
		}
	}
	else if ( $var[FieldInput] == "select" )
	{
		echo "<select name='data_{$var[FieldName]}'>";
		foreach ( $var[selectValue] as $keyIn => $varIn )
		{
			if ( $pInfo[$var['FieldName']] == $varIn )
			{
				echo "<option value='{$varIn}' selected>{$varIn}</option>";
			}
			else
			{
				echo "<option value='{$varIn}'>{$varIn}</option>";
			}
		}
		echo "</select>";
	}
	else if ( $var[FieldInput] == "password" )
	{
		echo "<input name='data_{$var[FieldName]}' type='password' >";
	}
	else if ( $var[FieldInput] == "RichEditor" )
	{
		$LibType = TRUE;
		$myDE = new devedit( );
		$myDE->Libtype = $LibType;
		$myDE->SetName( "data_{$var[FieldName]}" );
		setdeveditpath( INCLUDE_PATH."editor" );
		$a = pathinfo( $_SERVER['PHP_SELF'] );
		$myDE->AdminPath = $a[dirname];
		$myDE->sId = $IN['sId'];
		$myDE->HideSaveButton( );
		$myDE->HideStyleList( );
		$myDE->HidePropertiesButton( );
		$myDE->HideSpellingButton( );
		$myDE->SetLanguage( DE_AMERICAN );
		$myDE->SetPathType( DE_PATH_TYPE_FULL );
		$myDE->SetDocumentType( DE_DOC_TYPE_HTML_PAGE );
		$myDE->SetImageDisplayType( DE_IMAGE_TYPE_THUMBNAIL );
		$myDE->SetFlashDisplayType( DE_FLASH_TYPE_THUMBNAIL );
		$myDE->EnableGuidelines( );
		$myDE->SetTextAreaDimensions( 60, 90 );
		$val = "";
		if ( $myDE->GetValue( FALSE ) == "" )
		{
			$val = $pInfo[$var[FieldName]];
		}
		else
		{
			$val = $myDE->GetValue( FALSE );
		}
		$myDE->SetValue( $val );
		$myDE->ShowControl( "100%", "500", "" );
	}
}
echo " </td>\r\n </tr>\r\n</table>\r\n\r\n</form>\r\n</body>\r\n</html>";
?>
