<?php


if ( !defined( "IN_IWPC" ) )
{
	exit( "Access Denied" );
}
include_once( INCLUDE_PATH."editor/class.devedit.php" );
require_once( LANG_PATH.$SYS_ENV['language']."/lang_skin/admin/func_editor.php" );
echo "<html>\r\n<head>\r\n<title>";
echo $IN[PATH];

echo $IN[targetFile].' - ';
echo "{$_LANG_SKIN['title']}</title>\r\n<link type=\"text/css\" rel=\"StyleSheet\" href=\"../html/style.css\" />\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8";
echo CHARSET."\">\r\n";
echo "<script type=\"text/javascript\">\r\n\r\nvar ie55 = /MSIE ((5\\.[56789])|([6789]))/.test( navigator.userAgent ) &&\r\n\t\t\tnavigator.platform == \"Win32\";\r\n\r\nif ( !ie55 ) {\r\n\twindow.onerror = function () {\r\n\t\treturn true;\r\n\t};\r\n}\r\n\r\nfunction writeNotSupported() {\r\n\tif ( !ie55 ) {\r\n\t\tdocument.write( \"<p class=\\\"warning\\\">\" +\r\n\t\t\t\"This script only works in Internet Explorer 5.5\" +\r\n\t\t\t\" or greater for Windows</p>\" ";
echo ");\r\n\t}\r\n}\r\n\r\n</script>\r\n";

echo "<script type=\"text/javascript\">\r\n\r\nfunction getQueryString( sProp ) {\r\n\tvar re = new RegExp( sProp + \"=([^\\\\&]*)\", \"i\" );\r\n\tvar a = re.exec( document.location.search );\r\n\tif ( a == null )\r\n\t\treturn \"\";\r\n\treturn a[1];\r\n};\r\n\r\nfunction changeCssFile( sCssFile ) {\r\n\tvar loc = String(document.location);\r\n\tvar search = document.location.search;\r\n\tif ( search != \"\" )\r\n\t\tloc = loc.replace( search, \"\" );\r\n\tl";
echo "oc = loc + \"?css=\" + sCssFile;\r\n\tdocument.location.replace( loc );\r\n}\r\n\r\nvar cssFile = getQueryString( \"css\" );\r\nif ( cssFile == \"\" )\r\n\tcssFile = \"../../html/menu/skins/winclassic.css\";\r\n\r\ndocument.write(\"<link type=\\\"text/css\\\" rel=\\\"StyleSheet\\\" href=\\\"\" + cssFile + \"\\\" />\" );\r\n\r\n</script>\r\n\r\n";
echo "<script type=\"text/javascript\" src=\"../../html/menu/js/poslib.js\"></script>\r\n";
echo "<script type=\"text/javascript\" src=\"../../html/menu/js/scrollbutton.js\"></script>\r\n";
echo "<script type=\"text/javascript\" src=\"../../html/menu/js/menu4.js\"></script>\r\n\r\n</head>\r\n";
echo "<script src=\"../html/functions.js\" type=\"text/javascript\" language=\"javascript\"></script>\r\n";
echo "<script language=JavaScript>\r\nvar NodeID = '";
echo "\{$IN[NodeID]}';\r\nvar sId = '";
echo "{$IN['sId']}';\r\nvar PATH = '";
echo "{$IN[PATH]}';\r\nvar targetFile = '";
echo "{$IN[targetFile]}';\r\nvar o = '";
echo "{$IN[o]}';\r\n\r\n</script>\r\n<body bgcolor=threedface STYLE=\"margin:0pt;padding:0pt;border: 1px buttonhighlight;\" onload=\"document.FM.content.focus();\">\r\n";
echo "<script type=\"text/javascript\">\r\n//<![CDATA[\r\n\r\n// set css file to use for menus\r\nMenu.prototype.cssFile = cssFile;\r\n\r\nvar tmp;\r\n\r\n// Build context menu\r\nvar cMenu = new Menu();\r\n\r\nvar openItem, openNewWinItem;\r\n\r\ncMenu.add( openItem = new MenuItem( \"Open\" ) );\r\nopenItem.mnemonic = \"o\";\r\ncMenu.add( openNewWinItem = new MenuItem( \"Open in New Window\" ) );\r\nopenNewWinItem.mnemonic = \"n\";\r\nopenNewWinIte";
echo "m.target = \"_blank\";\t// open in new window\r\n\r\nvar backItem, forwardItem, refreshItem;\r\n\r\ncMenu.add( backItem = new MenuItem( \"Back\", function () { window.history.go(-1); }, \"images/back.png\" ) );\r\nbackItem.mnemonic = \"b\";\r\ncMenu.add( forwardItem = new MenuItem( \"Forward\", function () { window.history.go(1); }, \"images/forward.png\" ) );\r\nforwardItem.mnemonic = \"o\";\r\ncMenu.add( refreshItem = new Men";
echo "uItem( \"Refresh\", function () { document.location.reload(); }, \"images/refresh.png\" ) );\r\nrefreshItem.mnemonic = \"r\";\r\n\r\ncMenu.add( new MenuSeparator() );\r\n\r\n\r\ncMenu.add( new MenuSeparator() );\r\n\r\ncMenu.add( tmp = new MenuItem( \"View Source\", function () {\tdocument.location = \"view-source:\" + document.location; }, \"images/notepad.png\" ) );\r\ntmp.mnemonic = \"v\";\r\n\r\n\r\n// edit menu\r\nvar eMenu = new Me";
echo "nu()\r\n\r\nvar undoItem, cutItem, copyItem, pasteItem, deleteItem, selectAllItem;\r\n\r\n// undo is broken in IE\r\n// eMenu.add( undoItem = new MenuItem( \"Undo\", function () { document.execCommand( \"Undo\" ); }, \"images/undo.small.png\" ) );\r\n// undoItem.mnemonic = \"u\";\r\n//\r\n//\r\n// eMenu.add( new MenuSeparator() );\r\n\r\n\r\neMenu.add( cutItem = new MenuItem( \"Cut\", function () { document.execCommand( \"Cut\" ); }";
echo ", \"../../html/menu/images/cut.small.png\" ) );\r\ncutItem.mnemonic = \"t\";\r\n\r\neMenu.add( copyItem = new MenuItem( \"Copy\", function () { document.execCommand( \"Copy\" ); }, \"../../html/menu/images/copy.small.png\" ) );\r\ncopyItem.mnemonic = \"c\";\r\n\r\neMenu.add( pasteItem = new MenuItem( \"Paste\", function () { document.execCommand( \"Paste\" ); }, \"../../html/menu/images/paste.small.png\" ) );\r\npasteItem.mnemon";
echo "ic = \"p\";\r\n\r\neMenu.add( deleteItem = new MenuItem( \"Delete\", function () { document.execCommand( \"Delete\" ); }, \"../../html/menu/images/delete.small.png\" ) );\r\ndeleteItem.mnemonic = \"d\";\r\n\r\n\r\neMenu.add( new MenuSeparator() );\r\n\r\neMenu.add( searchItem = new MenuItem( \"Search\", function () { showModelessDialog(\"";
echo INCLUDE_PATH;
echo "editor/class.devedit.php?ToDo=FindReplace&DEP1=./&DEP=./\", document.FM, \"dialogWidth:385px; dialogHeight:165px; scroll:no; status:no; help:no;\" );} ) );\r\neMenu.add( new MenuSeparator() );\r\n\r\neMenu.add( selectAllItem = new MenuItem( \"Select All\", function () { document.execCommand( \"SelectAll\" ); } ) );\r\nselectAllItem.mnemonic = \"a\";\r\n\r\n\r\n\r\n\r\nvar oldOpenState = null;\t// used to only c";
echo "hange when needed\r\nvar lastKeyCode = 0;\r\n\r\nfunction rememberKeyCode() {\r\n\tlastKeyCode = window.event.keyCode;\r\n}\r\n\r\nfunction showContextMenu() {\r\n\r\n\tvar el = window.event.srcElement;\r\n\r\n\t// check for edit\r\n\tvar showEditMenu = el != null &&\r\n\t\t\t\t\t\t(el.tagName == \"INPUT\" || el.tagName == \"TEXTAREA\");\r\n\r\n\t// check for anchor\r\n\twhile ( el != null && el.tagName != \"A\" )\r\n\t\tel = el.parentNode;\r\n\r\n\tvar s";
echo "howOpenItems = el != null && el.tagName == \"A\";\r\n\r\n\tif ( showOpenItems != oldOpenState ) {\r\n\t\topenItem.visible\t\t= showOpenItems;\r\n\t\topenNewWinItem.visible\t= showOpenItems;\r\n\t\tbackItem.visible\t\t= !showOpenItems;\r\n\t\tforwardItem.visible\t\t= !showOpenItems;\r\n\t\trefreshItem.visible\t\t= !showOpenItems;\r\n\t\toldOpenState = showOpenItems;\r\n\t}\r\n\r\n\tif ( showOpenItems ) {\r\n\t\topenItem.action = openNewWinItem.actio";
echo "n = el.href;\r\n\t}\r\n\r\n\t// find left and top\r\n\tvar left, top;\r\n\r\n\tif ( showEditMenu )\r\n\t\tel = window.event.srcElement;\r\n\telse if ( !showOpenItems )\r\n\t\tel = document.documentElement;\r\n\r\n\tif ( lastKeyCode == 93 ) {\t// context menu key\r\n\t\tleft = posLib.getScreenLeft( el );\r\n\t\ttop = posLib.getScreenTop( el );\r\n\t}\r\n\telse {\r\n\t\tleft = window.event.screenX;\r\n\t\ttop = window.event.screenY;\r\n\t}\r\n\r\n\tif ( showEdi";
echo "tMenu ) {\r\n\r\n\t\t// undo is broken in IE\r\n\t\t// undoItem.disabled =\t\t\t!document.queryCommandEnabled( \"Undo\" );\r\n\t\tcutItem.disabled =\t\t\t!document.queryCommandEnabled( \"Cut\" );\r\n\t\tcopyItem.disabled =\t\t\t!document.queryCommandEnabled( \"Copy\" );\r\n\t\tpasteItem.disabled =\t\t!document.queryCommandEnabled( \"Paste\" );\r\n\t\tdeleteItem.disabled =\t\t!document.queryCommandEnabled( \"Delete\" );\r\n\t\tselectAllItem.disabled ";
echo "=\t!document.queryCommandEnabled( \"SelectAll\" );\r\n\r\n\t\teMenu.invalidate();\r\n\t\teMenu.show( left, top );\r\n\t}\r\n\telse {\r\n\t\t//cMenu.invalidate();\r\n\t\t//cMenu.show( left, top );\r\n\t}\r\n\r\n\tevent.returnValue = false;\r\n\tlastKeyCode = 0\r\n};\r\n\r\ndocument.attachEvent( \"oncontextmenu\", showContextMenu );\r\ndocument.attachEvent( \"onkeyup\", rememberKeyCode );\r\n\r\n//]]>\r\n\r\n</script>\r\n\r\n";
echo "<script type=\"text/javascript\">\r\nwriteNotSupported();\r\n</script>\r\n<form action=\"admin_setting.php?sId=";
echo "{$IN['sId']}&o=";
echo "{$IN['o']}_submit\" method=\"post\" name=\"FM\" ><!--actionFrame-->\r\n<table width=\"100%\" border=0   cellPadding=0 cellSpacing=2 >\r\n<tr><td><INPUT TYPE=\"hidden\" name=\"PATH\" value=\"";
echo "{$IN['PATH']}\">\r\n<INPUT TYPE=\"hidden\" name=\"targetFile\" value=\"";
echo "{$IN['targetFile']}\">\r\n<TEXTAREA NAME=\"content\" cols=\"111\" rows=\"37\" wrap=\"VIRTUAL\">";
echo "{$content}</TEXTAREA>\r\n </td>\r\n </tr>\r\n</table>\r\n\r\n</form>\r\n</body>\r\n</html>";
?>
