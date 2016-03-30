<?php
if ( !defined( "IN_IWPC" ) )
{
	exit( "Access Denied" );
}
require_once( INCLUDE_PATH."admin/userAdmin.class.php" );
require_once( LANG_PATH.$SYS_ENV['language']."/lang_skin/admin/contribution_admin_view.php" );
$userInfo = new userAdmin( );
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">\r\n<HTML>\r\n<HEAD>\r\n<TITLE> </TITLE>\r\n<META NAME=\"Generator\" CONTENT=\"EditPlus\">\r\n<META NAME=\"Author\" CONTENT=\"\">\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8";
echo CHARSET;
echo "\">\r\n<META NAME=\"Description\" CONTENT=\"\">\r\n\r\n</HEAD>\r\n";
echo "<script type=\"text/javascript\" src=\"../html/helptip.js\"></script>\r\n<link type=\"text/css\" rel=\"StyleSheet\" href=\"../html/helptip.css\" />\r\n<link type=\"text/css\" rel=\"StyleSheet\" href=\"../html/style.css\" />\r\n";
echo "<style type=\"text/css\">\r\n<!--\r\n.tablebg {\r\n\tbackground-color: #F5F5F5;\r\n}\r\n-->\r\n</style>\r\n";
echo "<script language=\"javascript\">\r\nfunction mytext_zoomin(){\tmytext.style.fontSize=\"10.5pt\";}function mytext_zoomout(){\tmytext.style.fontSize=\"9pt\";}\r\n\r\nfunction MM_openBrWindow(theURL,winName,features) { \r\n  window.open(theURL,winName,features);\r\n}\r\n\r\n</script>\r\n<!--------------------------><CENTER>[ <A HREF=\"javascript:window.close();\">";
echo "{$_LANG_SKIN['close']}</A> ]</CENTER>\r\n<table width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"5\" cellspacing=\"1\"  class=\"table_border\" >\r\n<tr > \r\n              <td align=right   class=\"table_td1\">";
echo "{$_LANG_SKIN['OwnerID']}:</td>\r\n              <td class=\"table_td2\">";
echo $userInfo->getInfo( $pInfo[OwnerID], "uName" );
echo "\t\t\t  </td>\r\n</tr>\r\n<tr > \r\n              <td align=right width=\"90\" class='table_td1'>";
echo "{$_LANG_SKIN['NodeName']}:</td>\r\n              <td class='table_td2'>\r\n";
echo $pInfo[NodeName];
echo "</td>\r\n\r\n</tr>\r\n<tr > \r\n              <td align=right class='table_td1'>";
echo "{$_LANG_SKIN['SubNodeID']}:</td>\r\n              <td class='table_td2'>\r\n";
if ( is_array( $pInfo[SubNodeIDs] ) )
{
	foreach ( $pInfo[SubNodeIDs] as $key => $var )
	{
		echo $var."<br>";
	}
}
echo "\t\t  \r\n\t\t\t  \r\n\t\t\t  </td>\r\n</tr>";
foreach ( $tableInfo as $key => $var )
{
	if ( empty( $var['EnableContribution'] ) )
	{
		continue;
	}
	echo " <tr class='table_td1'><td align=right >{$var[FieldTitle]}:</td><td class='table_td2'>";
	if ( !empty( $pInfo[$var['FieldName']] ) )
	{
		if ( !empty( $SYS_ENV['ContributionViewMode'] ) )
		{
			echo "<p><A HREF='javascript:void(0);' onclick=\"doPreview('{$var['FieldName']}', this)\">[{$_LANG_SKIN['preview']}]</A></p>";
		}
		else
		{
			echo "<p><A HREF='javascript:void(0);' onclick=\"doPreview('{$var['FieldName']}', this)\">[{$_LANG_SKIN['viewcode']}]</A></p>";
		}
	}
	if ( !empty( $SYS_ENV['ContributionViewMode'] ) )
	{
		echo "<textarea class=\"content\" id=\"{$var['FieldName']}\" readonly=\"readonly\" style=\"width:100%\">".htmlspecialchars( $pInfo[$var['FieldName']] )."</textarea>";
		echo "<div id=\"{$var['FieldName']}_Preview\" style=\"display:none\"></div>";
	}
	else
	{
		echo "<textarea class=\"content\" id=\"{$var['FieldName']}\" readonly=\"readonly\" style=\"width:100%;display:none\">".htmlspecialchars( $pInfo[$var['FieldName']] )."</textarea>";
		echo "<div id=\"{$var['FieldName']}_Preview\">".$pInfo[$var['FieldName']]."</div>";
	}
	echo "</td></tr>";
}
echo "<tr > \r\n              <td align=right   class=\"table_td1\">";
echo "{$_LANG_SKIN['CreationDate']}:</td>\r\n              <td class=\"table_td2\">";
echo date( "Y-m-d H:i:s", $pInfo[CreationDate] );
echo "\t\t\t  </td>\r\n</tr>\r\n<tr > \r\n              <td align=right  class=\"table_td1\">";
echo "{$_LANG_SKIN['ModifiedDate']}:</td>\r\n              <td class=\"table_td2\">";
echo date( "Y-m-d H:i:s", $pInfo[ModifiedDate] );
echo "\t\t\t  </td>\r\n</tr>\r\n\r\n\r\n\r\n</table>\r\n<CENTER>[ <A HREF=\"javascript:window.close();\">";
echo "{$_LANG_SKIN['close']}</A> ]</CENTER>\r\n</body></html>";
?>
