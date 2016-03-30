<?php
function cmsParse( $txt )
{
	$txt = preg_replace( "#&lt;cms(.+?)&gt;#i", "<FONT color=#ff0000>\\0</FONT>", $txt );
	$txt = preg_replace( "#&lt;/CMS&gt;#Us", "<FONT color=#ff0000>\\0</FONT>", $txt );
	$txt = preg_replace( "#&lt;loop(.+?)&gt;#i", "<FONT color=#FF9900>\\0</FONT>", $txt );
	$txt = preg_replace( "#&lt;/Loop&gt;#i", "<FONT color=#FF9900>&lt;/loop&gt;</FONT>", $txt );
	$txt = preg_replace( "#\\[\\\$(.+?)\\]#i", "<FONT color=#6666CC>\\0</FONT>", $txt );
	$txt = preg_replace( "#\\[@(.+?)\\]#i", "<FONT color=#6699CC>\\0</FONT>", $txt );
	$txt = preg_replace( "#&lt;if(.+?)&gt;#i", "<FONT color=#990000>\\0</FONT>", $txt );
	$txt = preg_replace( "#&lt;\\/if&gt;#i", "<FONT color=#990000>\\0</FONT>", $txt );
	$txt = preg_replace( "#&lt;else(.*?)&gt;#i", "<FONT color=#990000>\\0</FONT>", $txt );
	$txt = preg_replace( "#&lt;include:(.+?)&gt;#i", "<FONT color=#ff0000>\\0</FONT>", $txt );
	$txt = preg_replace( "#&lt;get:(.+?)&gt;#i", "<FONT color=#ff0000>\\0</FONT>", $txt );
	$txt = preg_replace( "#&lt;var(.+?)&gt;#i", "<FONT color=#ff0000>\\0</FONT>", $txt );
	$txt = preg_replace( "#&lt;op(.+?)&gt;#i", "<FONT color=#ff0000>\\0</FONT>", $txt );
	$txt = preg_replace( "#&lt;debug(.+?)&gt;#i", "<FONT color=#ff0000>\\0</FONT>", $txt );
	$txt = preg_replace( "#&lt;:(.+?)&gt;#i", "<FONT color=#ff0000>\\0</FONT>", $txt );
	$txt = preg_replace( "#&lt;!--(.+?)-&gt;#s", "<FONT color=#339900>\\0</FONT>", $txt );
	$txt = preg_replace( "#\\[\\*(.+?)\\]#", "<FONT color=#6699CC>\\0</FONT>", $txt );
	$txt = preg_replace( "#&lt;\\?(.+?)\\?&gt;#is", "<FONT color=#FF9999>\\0</FONT>", $txt );
	$txt = preg_replace( "#&lt;php&gt;#i", "<FONT color=#990000>\\0</FONT>", $txt );
	$txt = preg_replace( "#&lt;\\/php&gt;#i", "<FONT color=#990000>\\0</FONT>", $txt );
	$txt = preg_replace( "#{\\\$(.+?)}#i", "<FONT color=#ff0000>\\0</FONT>", $txt );
	$txt = preg_replace( "#&lt;header:(.+?)&gt;#i", "<FONT color=#ff0000>\\0</FONT>", $txt );
	return $txt;
}

function br2none( $str )
{
	return str_replace( array( "<br>", "<br />" ), "", $str );
}

require_once( "common.php" );
define( "SYS_TPLPATH", "" );
$Url = $_REQUEST['TplUrl'];
$Url = str_replace( "..", "", $Url );
$Url = str_replace( "systemplatespathisup", "../", $Url );
$charset = $_REQUEST['charset'];
if ( $charset == "" )
{
	$charset = "GB2312";
}
if ( !( $Handle = fopen( SYS_TPLPATH.$Url, "r" ) ) )
{
	exit( "not open file:".SYS_TPLPATH.$Url );
}
$Contents = fread( $Handle, filesize( SYS_TPLPATH.$Url ) );
fclose( $Handle );
$Contents = nl2br( str_replace( array( " ", "\t" ), array( "&nbsp;", "&nbsp;&nbsp;&nbsp;&nbsp;" ), htmlentities( $Contents, ENT_QUOTES, $charset ) ) );
$Contents = cmsparse( $Contents );
$header = "<html><head><title>模板源代码查看</title>\r\n<style type=\"text/css\"><!--body {font-family: \\\"Courier New\\\", \\\"Courier\\\"; font-size: 12px;}--></style>\r\n</head><body>";
$footer = "</body></html>";
echo $header."<h4><FONT color=#339900>模板源代码查看:</FONT></h4><hr size=1>";
echo "<div style=\"white-space: nowrap;\">".$Contents."</div></h4><hr size=1>";
echo "<DIV align=center>Powered by <b>CMSware</b> 1999-2005 CMSware Ltd All rights reserved. </DIV>".$footer;
?>
