<?php
function dir_writeable( $dir )
{
	if ( !is_dir( $dir ) )
	{
		@mkdir( $dir, 511 );
	}
	if ( is_dir( $dir ) )
	{
		if ( $fp = @fopen( "{$dir}/%%test.test", "w" ) )
		{
			@fclose( $fp );
			@unlink( "{$dir}/%%test.test" );
			$writeable = 1;
		}
		else
		{
			$writeable = 0;
		}
	}
	return $writeable;
}

function is_safe_mode( )
{
	$dir = "./sysdata/%%install/";
	@mkdir( $dir, 511 );
	if ( is_dir( $dir ) )
	{
		if ( $fp = @fopen( "{$dir}/%%test.test", "w" ) )
		{
			@fclose( $fp );
			@unlink( "{$dir}/%%test.test" );
			@rmdir( $dir );
			$safe_mode = 0;
		}
		else
		{
			$safe_mode = 1;
		}
	}
	else
	{
		$safe_mode = 1;
	}
	return $safe_mode;
}

function mysql5x_varchar( $s )
{
	return "varchar(".$s.")";
}

function runquery( $sql )
{
	global $db;
	global $output;
	global $db_config;
	global $CMSWARE_ADMIN_URL;
	global $CMSWARE_ADMIN_HOST;
	global $CMSWARE_PUB_URL;
	$sql = str_replace( "{cmsware_admin}", $CMSWARE_ADMIN_URL, $sql );
	$sql = str_replace( "{cmsware_admin_host}", $CMSWARE_ADMIN_HOST, $sql );
	$sql = str_replace( "{cmsware_pub}", $CMSWARE_PUB_URL, $sql );
	$serverVersion = mysql_get_server_info( );
	$mysql_version = explode( ".", $serverVersion );
	if ( 4 < $mysql_version[0] )
	{
		$sql = str_replace( "''", "NULL", $sql );
		$sql = str_replace( "NOT NULL default ''", "default NULL", $sql );
		$sql = str_replace( "NOT NULL", "", $sql );
	}
	if ( $mysql_version[0] == 4 && 0 < $mysql_version[1] || 4 < $mysql_version[0] )
	{
		if ( $db_config['db_charset'] == "gb2312" )
		{
			$db_config['db_charset'] = "gbk";
		}
		mysql_query( "SET NAMES '".$db_config['db_charset']."' " );
	}
	$ret = array( );
	$num = 0;
	foreach ( explode( ";\r\n", trim( $sql ) ) as $query )
	{
		$queries = explode( "\r\n", trim( $query ) );
		foreach ( $queries as $query )
		{
			$ret[$num] .= $query[0] == "#" ? NULL : $query;
		}
		++$num;
	}
	unset( $sql );
	foreach ( $ret as $query )
	{
		if ( $query )
		{
			if ( substr( $query, 0, 12 ) == "CREATE TABLE" )
			{
				$name = preg_replace( "/CREATE TABLE ([a-z0-9_]+) .*/is", "\\1", $query );
				if ( $mysql_version[0] == 4 && 0 < $mysql_version[1] || 4 < $mysql_version[0] )
				{
					$query .= " DEFAULT CHARSET=".$db_config['db_charset']." ";
				}
				$output .= "建立数据表 ".$name." ... <font color=\"#0000EE\">成功</font><br>";
			}
			$pattern = "/ALTER TABLE[\\s]+`([^`]*)`[\\s]+(DROP|ADD|CHANGE)[\\s]+[^`]+`([^`]*)`/isU";
			preg_match( $pattern, $query, $matches );
			if ( !empty( $matches[0] ) )
			{
				$exists = mysql_field_exists( $matches[1], $matches[3] );
				$action = strtoupper( $matches[2] );
				if ( $action == "ADD" && !$exists )
				{
					$Que = mysql_query( $query );
				}
				else if ( $action == "DROP" && $exists )
				{
					$Que = mysql_query( $query );
				}
				else if ( $action == "CHANGE" && $exists )
				{
					$Que = mysql_query( $query );
				}
				else
				{
					continue;
				}
			}
			else
			{
				$Que = mysql_query( $query );
			}
			if ( !$Que )
			{
				halt1( "MySQL Query Error", $query );
			}
		}
	}
	return $Que;
}

function mysql_field_exists( $table, $fieldname )
{
	global $db_config;
	$fields = mysql_list_fields( $db_config['db_name'], $table );
	$columns = mysql_num_fields( $fields );
	$return = FALSE;
	$i = 0;
	for ( ;	$i < $columns;	++$i	)
	{
		$field_name = mysql_field_name( $fields, $i );
		if ( $field_name == $fieldname )
		{
			$return = TRUE;
			break;
		}
	}
	return $return;
}

function halt1( $message = "", $sql = "" )
{
	$timestamp = time( );
	$errmsg = "";
	$dberror = mysql_error( );
	$dberrno = mysql_errno( );
	if ( $message )
	{
		$errmsg = "<b>SYS info</b>: {$message}\n\n";
	}
	$errmsg .= "<b>Time</b>: ".gmdate( "Y-n-j g:ia", $timestamp + $GLOBALS['timeoffset'] * 3600 )."\n";
	$errmsg .= "<b>Script</b>: ".$GLOBALS[PHP_SELF]."\n\n";
	if ( $sql )
	{
		$errmsg .= "<b>SQL</b>: ".htmlspecialchars( $sql )."\n";
	}
	$errmsg .= "<b>Error</b>:  {$dberror}\n";
	$errmsg .= "<b>Errno.</b>:  {$dberrno}";
	echo "</table></table></table></table></table>\n";
	echo "<p style=\"font-family: Verdana, Tahoma; font-size: 11px; background: #FFFFFF;\">";
	echo nl2br( $errmsg );
	echo "</p>";
}

function unwriteable( $msg )
{
	global $_writeable;
	$_writeable = FALSE;
	print "<B>".$msg."</B>  不可写。<br/>";
}
ini_set(display_errors ,1); 
define( "DEBUG_MODE", "2" );
define( "Error_Display", "html" );
set_magic_quotes_runtime( 0 );
$phpVersion = substr( phpversion( ), 0, 1 );
if ( $phpVersion == 5 )
{
	define( "PHP_VERSION_5", TRUE );
}
else
{
	define( "PHP_VERSION_5", FALSE );
}

define( "INCLUDE_PATH", "./include/" );
define( "KTPL_DIR", INCLUDE_PATH."lib/kTemplate/" );
define( "KDB_DIR", INCLUDE_PATH."lib/kDB/" );
define( "SYS_PATH", "./" );
define( "ROOT_PATH", "./" );
define( "CACHE_DIR", "./sysdata/" );
define( "LIB_PATH", INCLUDE_PATH."lib/" );
define( "LANG_PATH", ROOT_PATH."language/" );

require_once( LIB_PATH."file.class.php" );
require_once( SYS_PATH."config.php" );
require_once( KTPL_DIR."kTemplate.class.php" );
require_once( INCLUDE_PATH."data.class.php" );
require_once( INCLUDE_PATH."functions.php" );
require_once( INCLUDE_PATH."file.class.php" );
require_once( INCLUDE_PATH."Error.php" );
define( "ADMIN_DIR", "./".ADMIN_NAME."/" );

$SYS_CONFIG['language'] = empty( $SYS_CONFIG['language'] ) ? "chinese_gb" : $SYS_CONFIG['language'];
require_once( LANG_PATH.$SYS_CONFIG['language']."/charset.inc.php" );
header( "Content-Type: text/html; charset=".CHARSET );
if ( file_exists( CACHE_DIR."install.lock" ) )
{
	echo "警告!安装功能已被禁止，请删除CMSware系统目录中的'".CACHE_DIR."install.lock'文件和CWPS目录中/tmp/install.lock文件以继续安装.";
	exit( );
}

$charset = CHARSET;
new Error( );
require_once( KDB_DIR."kDB.php" );
$version = "CMS 2.8.5 ";
require_once( SYS_PATH."license.php" );
if ( strpos( $License['Product-name'], "Plus" ) !== FALSE )
{
	$version .= "Plus ";
}
else if ( strpos( $License['Product-name'], "Pro" ) !== FALSE )
{
	$version .= "Pro ";
}
else if ( strpos( $License['Product-name'], "Free" ) !== FALSE )
{
	$version .= "Free ";
}
else
{
	$version .= "ST ";
}
if ( isset( $PHP_SELF ) )
{
	$GLOBALS['_SERVER']['PHP_SELF'] = $PHP_SELF;
}
$info = pathinfo( $_SERVER['PHP_SELF'] );
$info['dirname'] = $info['dirname'] == "\\" ? "" : $info['dirname'];
if ( $info['dirname'] == "/" )
{
	$info['dirname'] = "";
}
$at_tmp = pathinfo( $info['dirname'] );
$at_tmp['dirname'] = $at_tmp['dirname'] == "\\" ? "" : $at_tmp['dirname'];
if ( $at_tmp['dirname'] == "/" )
{
	$at_tmp['dirname'] = "";
}
$cmsware_install_dir = $at_tmp['basename'];
if ( empty( $cmsware_install_dir ) )
{
	$cmsware_install_dir = ".";
}
if ( $_SERVER['SERVER_PORT'] != 80 )
{
	$CMSWARE_ADMIN_URL = "http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$info['dirname']."/";
	$CMSWARE_ADMIN_HOST = $_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'];
	$CMSWARE_PUB_URL = $_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$at_tmp['dirname']."/";
}
else
{
	$CMSWARE_ADMIN_URL = "http://".$_SERVER['SERVER_NAME'].$info['dirname']."/";
	$CMSWARE_ADMIN_HOST = $_SERVER['SERVER_NAME'];
	$CMSWARE_PUB_URL = "http://".$_SERVER['SERVER_NAME'].$at_tmp['dirname']."/";
}
$IN = parse_incoming( );
$TPL = new kTemplate( );
$TPL->template_dir = SYS_PATH."skin/install/";
$TPL->compile_dir = SYS_PATH."sysdata/templates_c/";
$TPL->cache_dir = SYS_PATH."sysdata/cache/";
$TPL->assign( "version", $version );
if ( $SYS_CONFIG['ftp_mode'] === 1 )
{
	$SYS_CONFIG['ftp_cms_admin_path'] = File::_ftp_realpath( $SYS_CONFIG['ftp_cms_admin_path'], "../" );
}
$_writeable = TRUE;
if ( !dir_writeable( "./sysdata/templates_c" ) )
{
	printf( "<b>安装错误：</b>目录./sysdata/templates_c不可写,安装前请设置以下目录为可写：<br>" );
	printf( "<UL>" );
	printf( "<LI>./sysdata</li>" );
	printf( "<LI>./sysdata/templates_c</li>" );
	printf( "<LI>./sysdata/sysinfo</li>" );
	printf( "<LI>./sysdata/automini</li>" );
	printf( "<LI>./templatesv" );
	printf( "<LI>./backup</li>" );
	printf( "<LI>./resource</li>" );
	printf( "<LI>./www</li>" );
	printf( "</UL>" );
	exit( );
}
if ( !is_writeable( ROOT_PATH."config.php" ) )
{
	exit( "<b>安装错误：</b>系统配置文件 config.php 不可写" );
}
if ( !is_writable( ROOT_PATH."setting/cms.ini.php" ) )
{
	unwriteable( "/setting/cms.ini.php" );
}
if ( !is_writable( ROOT_PATH."setting/crawler.ini.php" ) )
{
	unwriteable( "/setting/crawler.ini.php" );
}
if ( !is_writable( ROOT_PATH."backup" ) )
{
	unwriteable( "/backup/" );
}
if ( !is_writable( ROOT_PATH."resource" ) )
{
	unwriteable( "/resource/" );
}
if ( !is_writable( ROOT_PATH."sysdata" ) )
{
	unwriteable( "/sysdata/" );
}
if ( !is_writable( ROOT_PATH."sysdata/automini" ) )
{
	unwriteable( "/sysdata/automini/" );
}
if ( !is_writable( ROOT_PATH."sysdata/cache" ) )
{
	unwriteable( "/sysdata/cache/" );
}
if ( !is_writable( ROOT_PATH."sysdata/sysinfo" ) )
{
	unwriteable( "/sysdata/sysinfo/" );
}
if ( !is_writable( ROOT_PATH."sysdata/templates_c" ) )
{
	unwriteable( "/sysdata/templates_c/" );
}
if ( !is_writable( ROOT_PATH."sysdata/tmp" ) )
{
	unwriteable( "/sysdata/tmp/" );
}
if ( !is_writable( ROOT_PATH."publish/tmp" ) && is_dir( ROOT_PATH."publish/tmp" ) )
{
	unwriteable( "/publish/tmp/" );
}
if ( file_exists( "../cwps" ) )
{
	if ( !is_writable( "../cwps/config.php" ) )
	{
		unwriteable( "../cwps/config.php" );
	}
	if ( !is_writable( "../cwps/tmp" ) )
	{
		unwriteable( "../cwps/tmp" );
	}
	if ( !is_writable( "../cwps/tmp/templates_c" ) )
	{
		unwriteable( "../cwps/tmp/templates_c" );
	}
	if ( !is_writable( "../cwps/tmp/cache" ) )
	{
		unwriteable( "../cwps/tmp/cache" );
	}
}
if ( !$_writeable )
{
	exit( "请设置以上目录/文件为可写。" );
}
if ( isset( $_SERVER['SERVER_SOFTWARE'] ) && preg_match( "/Microsoft-IIS/i", $_SERVER['SERVER_SOFTWARE'] ) && is_dir( ROOT_PATH."templates/demo_iis" ) )
{
	exit( "检测到你的服务器环境是IIS不支持PATHINFO静态化动态URL功能， 请删除或改名现在的{cmsware}templates/demo目录， 然后把{cmsware}templates/demo_iis目录改名为demo， 然后再继续回到这里重新安装" );
}
if ( $IN[o] == "init" )
{
	$TPL->display( "installshow.html" );
	exit( );
}
if ( !isset( $IN[step] ) )
{
	$IN[step] = 0;
}
switch ( $IN[step] )
{
case "0" :
	if ( is_safe_mode( ) && !file_exists( CACHE_DIR.".ftp" ) )
	{
		$error_msg = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">\r\n<HTML>\r\n<HEAD>\r\n<title>CMSware 安装向导</title>\r\n<META NAME=\"Generator\" CONTENT=\"EditPlus\">\r\n<META NAME=\"Author\" CONTENT=\"\">\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\r\n<META NAME=\"Description\" CONTENT=\"\">\r\n</HEAD>\r\n\r\n<style type=\"text/css\">\r\ntd{FONT-FAMILY: \"Verdana\", \"仿宋体\";  font-size: 12px} \r\n\r\ntr{FONT-FAMILY: \"Verdana\", \"仿宋体\"; font-size: 12px} \r\n\r\n\r\n\r\n.button{\r\n\theight:18;\r\n\tbackground-color:#FFFFFF;\r\n\tborder: 1 solid #CCCCCC;\r\n\tfont-family:\"sans-serif\";\r\n\tfont-size: 12px;\r\n}\r\n.fixline {\r\n\tborder: 1px dotted #999999;\r\n}\r\n.subbutton {\r\n\tfont-family: \"Arial\", \"Helvetica\", \"sans-serif\";\r\n\tfont-size: 12px;\r\n\tbackground-color: #C6D8F0;\r\n\tborder-top-width: 1.5px;\r\n\tborder-right-width: 1.5px;\r\n\tborder-bottom-width: 1.5px;\r\n\tborder-left-width: 1.5px;\r\n\tborder-top-style: solid;\r\n\tborder-right-style: solid;\r\n\tborder-bottom-style: solid;\r\n\tborder-left-style: solid;\r\n\tborder-top-color: #FFFFFF;\r\n\tborder-right-color: #000000;\r\n\tborder-bottom-color: #000000;\r\n\tborder-left-color: #FFFFFF;\r\n\theight: 22px;\r\n\tpadding-top: 3px;\r\n\twidth: 66px;\r\n}\r\n\r\na {FONT-FAMILY: \"Verdana\", \"宋体\"; TEXT-DECORATION: none;FONT-SIZE: 12px; color: #000000} \r\n\r\na:hover {COLOR: #000000; TEXT-DECORATION: underline;FONT-SIZE:12px;} \r\n#description { font-family:  \"sans-serif\";color:#000000; font-size:12px }\r\n</style>\r\n<script type=\"text/javascript\" src=\"html/images/helptip.js\"></script>\r\n<link type=\"text/css\" rel=\"StyleSheet\" href=\"html/images/helptip.css\" />\r\n<script  type=\"text/javascript\" language=\"javascript\" src=\"html/title_fade.js\"></script>\r\n<script src=\"html/functions.js\" type=\"text/javascript\" language=\"javascript\"></script>\r\n<body bgcolor=\"\" TOPMARGIN=\"0\" LEFTMARGIN=\"0\">\r\n<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n  <tr>\r\n    <td width=\"80%\" height=\"70\" bgcolor=\"#2D66B5\"><img src=\"html/images/install.gif\" ></td>\r\n    <td><table width=\"100%\" height=\"70\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\r\n        <tr>\r\n          <td bgcolor=\"#2E68B8\">&nbsp;</td>\r\n          <td bgcolor=\"#4D86D2\">&nbsp;</td>\r\n          <td bgcolor=\"#739FDB\">&nbsp;</td>\r\n          <td bgcolor=\"#9ABAE4\">&nbsp;</td>\r\n          <td bgcolor=\"#C6D8F0\">&nbsp;</td>\r\n        </tr>\r\n      </table></td>\r\n  </tr>\r\n</table>\r\n<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n  <tr>\r\n    <td height=\"40\">\r\n\t</td>\r\n  </tr>\r\n</table>\r\n<table width=\"80%\" border=\"0\" align=\"center\" cellpadding=\"8\" cellspacing=\"0\" class=\"fixline\">\r\n   <form action=\"?step=ftp_mode\" method=\"post\">\r\n <tr> \r\n    <td>\r\n\t<H2><FONT COLOR=\"#FF0000\">警告！你的系统运行在安全限制模式。</FONT><A HREF=\"?step=phpinfo\" target=\"_blank\">&lt;?PHP信息查看?&gt;</A></H2>\r\n\t<p><B>安全限制模式</B> 并不一定和服务器php配置文件php.ini的safe_mode=on有关.<br>主要原因可能是Apache的运行User/Group和你FTP上传的文件的User/Group不符合,导致php创建的目录php本身无法具备写权限.<br>（比如Apache运行的User和Group为nobody,而你用ftp上传的文件的User/Group可能是ftpusername,就可能导致此问题.请使用chown命令将上传的php文件设置为nobody的用户和组）,如下命令:<ul><li>  chown -R nobody:nobody    &nbsp;[你的cmsware目录] </li></ul>如果你使用的是php运行环境限制比较多的虚拟主机，可能无法使用chown命令，这时候需要使用CMSware的FTP模式来运行 ,如果你的空间支持FTP模块，请按下列提示设置你的ftp帐号，即可正常使用CMSware.</p>\r\n\r\n\t <p><strong>CMS管理目录相对FTP根目录的路径: </strong>这个的设置要特别注意,不然会导致无法使用CMSware,看准了是CMSware管理目录,也就是admin目录相对FTP根目录的路径。<br/><br/>比如你登陆ftp后显示的目录结构如下 <br/><strong>/cgi-bin </strong><br/><strong>/other </strong><br/><strong>/www </strong><br/>你的CMSware管理目录位于 <strong>/www/cms/admin </strong><br/>则设置你的CMS管理目录相对FTP根目录的路径为 <strong>/www/cms/admin </strong> </p>\r\n\t<!--{{{ FTP Setting -->\r\n\t <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\r\n          <tr> \r\n            <td align=\"center\" bgcolor=\"#ececec\"> <table width=\"100%\" border=\"0\" cellpadding=\"4\" cellspacing=\"1\" bgcolor=\"#2E68B8\">\r\n                <tr> \r\n                  <td colspan=\"2\" bgcolor=\"#2E68B8\" ><font color=\"#FFFFFF\">&nbsp;<B>FTP帐号设置</B>（你的系统运行在安全限制模式，建议改用FTP模式运行CMSware）：</font></td>\r\n                </tr>\r\n                <tr> \r\n                  <td bgcolor=\"#ececec\" width=\"28%\">FTP服务器地址:</td>\r\n                  <td bgcolor=\"#ececec\"><input name=\"ftp_host\" type=\"text\" class=\"button\" id=\"ftp_host\" value=\"{$_SERVER['HTTP_HOST']}\">&nbsp;&nbsp;端口号:<input name=\"ftp_port\" type=\"text\" class=\"button\" id=\"ftp_port\" value=\"21\" size=6></td>\r\n                 </tr>\r\n                <tr> \r\n                  <td bgcolor=\"#ececec\">FTP用户名:</td>\r\n                  <td bgcolor=\"#ececec\"><input name=\"ftp_username\" type=\"text\" class=\"button\" id=\"ftp_username\" value=\"\"></td>\r\n                 </tr>\r\n                <tr> \r\n                  <td bgcolor=\"#ececec\">FTP密码:</td>\r\n                  <td bgcolor=\"#ececec\"><input name=\"ftp_password\" type=\"password\" class=\"button\" id=\"ftp_password\" value=\"\"></td>\r\n                 </tr>\r\n                <tr> \r\n                  <td bgcolor=\"#ececec\"> CMS管理目录相对FTP根目录的路径:</td>\r\n                  <td bgcolor=\"#ececec\"><input name=\"ftp_cms_admin_path\" type=\"text\" class=\"button\" id=\"ftp_cms_admin_path\" value=\"\">\r\n                   </td>\r\n                 </tr>\r\n               \r\n              </table></td>\r\n          </tr>\r\n        </table>\r\n\t <!--FTP Setting }}}-->\r\n </td>\r\n  </tr>\r\n</table>\r\n<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n  <tr> \r\n    <td height=\"20\">&nbsp;</td>\r\n  </tr>\r\n</table>\r\n<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n  <tr>\r\n    <td align=\"center\"  > \r\n       \r\n      <input name=\"Submit\" type=\"submit\" class=\"subbutton\" value=\" 继 续 \" >\r\n    </td>\r\n\t\r\n  </tr>\r\n </form>\r\n\t  </table>\r\n</body>\r\n</html>";
		echo $error_msg;
	}
	else
	{
		$TPL->display( "install.html" );
	}
	break;
case "phpinfo" :
	phpinfo( );
	break;
case "ftp_mode" :
	if ( $config_handle = fopen( "./install/config.ini", "r" ) )
	{
		$config_php = fread( $config_handle, filesize( "./install/config.ini" ) );
		$config_php = str_replace( "[ftp_mode]", 1, $config_php );
		$config_php = str_replace( "[ftp_host]", $IN['ftp_host'], $config_php );
		$config_php = str_replace( "[ftp_port]", $IN['ftp_port'], $config_php );
		$config_php = str_replace( "[ftp_username]", $IN['ftp_username'], $config_php );
		$config_php = str_replace( "[ftp_password]", $IN['ftp_password'], $config_php );
		$config_php = str_replace( "[ftp_cms_admin_path]", $IN['ftp_cms_admin_path'], $config_php );
		$config_php = str_replace( "[cmsware_db_host]", $db_config['db_host'], $config_php );
		$config_php = str_replace( "[cmsware_db_user]", $db_config['db_user'], $config_php );
		$config_php = str_replace( "[cmsware_db_password]", $db_config['db_password'], $config_php );
		$config_php = str_replace( "[cmsware_db_name]", $db_config['db_name'], $config_php );
		$config_php = str_replace( "[cmsware_db_table_pre]", $db_config['table_pre'], $config_php );
		$config_php = str_replace( "[cmsware_db_charset]", $db_config['db_charset'], $config_php );
		$enable_validcode = extension_loaded( "gd" ) ? 1 : 0;
		$config_php = str_replace( "[enable_validcode]", $enable_validcode, $config_php );
		$handle = fopen( "config.php", "w" );
		@flock( $handle, 3 );
		fwrite( $handle, $config_php );
		fclose( $handle );
		$handle = fopen( CACHE_DIR.".ftp", "w" );
		@flock( $handle, 3 );
		fwrite( $handle, "1" );
		fclose( $handle );
	}
	else
	{
		exit( "Unable to read ./install/config.ini" );
	}
	header( "Location: install.php " );
	break;
case "1" :
	$operation = "<td bgcolor=\"#ececec\">".PHP_OS."</td>\r\n\t\t\t\t\t\t<td bgcolor=\"#ececec\"><FONT size=1 COLOR=#009900>√</FONT></td>";
	$webserver = "<td bgcolor=\"#ececec\">".$_SERVER['SERVER_SOFTWARE']."</td>\r\n\t\t\t\t\t\t<td bgcolor=\"#ececec\"><FONT size=1 COLOR=#009900>√</FONT></td>";
	$php = "<td bgcolor=\"#ececec\">".phpversion( )."</td>\r\n\t\t\t\t\t\t<td bgcolor=\"#ececec\"><FONT size=1 COLOR=#009900>√</FONT></td>";
	if ( extension_loaded( "ftp" ) )
	{
		$ftp = "<td bgcolor=\"#ececec\">PHP FTP module</td><td bgcolor=\"#ececec\"><FONT size=1 COLOR=#009900>√</FONT></td>";
	}
	else
	{
		$ftp = "<td bgcolor=\"#ececec\"></td><td bgcolor=\"#ececec\"><FONT  COLOR=#FF0000>×</FONT></td>";
	}
	if ( extension_loaded( "gd" ) )
	{
		if ( function_exists( "gd_info" ) )
		{
			$gd_info = gd_info( );
		}
		$gd = "<td bgcolor=\"#ececec\">".$gd_info['GD Version']."</td><td bgcolor=\"#ececec\"><FONT size=1 COLOR=#009900>√</FONT></td>";
	}
	else
	{
		$gd = "<td bgcolor=\"#ececec\">".$gd_info['GD Version']."</td><td bgcolor=\"#ececec\"><FONT  COLOR=#FF0000>×</FONT></td>";
	}
	if ( @ini_get( file_uploads ) )
	{
		$max_size = @ini_get( upload_max_filesize );
		$upload = "<td bgcolor=\"#ececec\">最大允许 ".$max_size."</td>\r\n\t\t\t\t\t\t<td bgcolor=\"#ececec\"><FONT size=1 COLOR=#009900>√</FONT></td>";
	}
	else
	{
		$upload = "<td bgcolor=\"#ececec\">不允许上传附件</td>\r\n\t\t\t\t\t\t<td bgcolor=\"#ececec\"><font color=red><FONT  COLOR=#FF0000>×</FONT></font></td>";
	}
	if ( is_writable( "config.php" ) )
	{
		$config_writable = 1;
	}
	else
	{
		$config_writable = 0;
	}
	$TPL->assign( "operation", $operation );
	$TPL->assign( "webserver", $webserver );
	$TPL->assign( "php", $php );
	$TPL->assign( "mysql", $mysql );
	$TPL->assign( "zend", $zend );
	$TPL->assign( "gd", $gd );
	$TPL->assign( "ftp", $ftp );
	$TPL->assign( "save_path", $save_path );
	$TPL->assign( "safe_mode", $safe_mode );
	$TPL->assign( "templatespath", $templatespath );
	$TPL->assign( "Resourcepath", $Resourcepath );
	$TPL->assign( "smartypath", $TPLpath );
	$TPL->assign( "backuppath", $backuppath );
	$TPL->assign( "installpath", $installpath );
	$TPL->assign( "upload", $upload );
	$TPL->display( "install1.html" );
	break;
case "installtype" :
	$TPL->display( "install_type.html" );
	break;
case "2" :
	$TPL->assign( "installType", $IN['installType'] );
	$TPL->assign( "installCWPS", $IN['installCWPS'] );
	$TPL->assign( "db_host", $db_config[db_host] );
	$TPL->assign( "db_user", $db_config[db_user] );
	$TPL->assign( "db_password", $db_config[db_password] );
	$TPL->assign( "db_name", $db_config[db_name] );
	$TPL->assign( "db_charset", $db_config[db_charset] );
	$TPL->assign( "sys_language", $SYS_CONFIG['language'] );
	$TPL->assign( "table_header", $db_config[table_pre] );
	$TPL->display( "install2.html" );
	break;
case "3" :
	$TPL->assign( "installType", $IN['installType'] );
	$TPL->assign( "installCWPS", $IN['installCWPS'] );
	$db = new kDB( $db_config['db_driver'] );
	$db_config['db_driver'] = "db";
	$db_config['db_type'] = "mysql";
	$db_config['db_host'] = $IN[database_host];
	$db_config['db_user'] = $IN[database_user];
	$db_config['db_password'] = $IN[database_password];
	$db_config['db_name'] = $IN[database_name];
	$db_config['table_pre'] = $IN[database_header];
	$db_config['db_charset'] = $db_config['db_charset'];
	$db_config['table_content_pre'] = "content";
	$db_config['table_contribution_pre'] = "contribution";
	$db_config['table_collection_pre'] = "collection";
	$server_host = $_SERVER['HTTP_HOST'];
	if ( $IN['installType'] == "typical" )
	{
		if ( isset( $_SERVER['SERVER_SOFTWARE'] ) && preg_match( "/Microsoft-IIS/i", $_SERVER['SERVER_SOFTWARE'] ) && file_exists( SYS_PATH."install/typical_install_iis.sql" ) )
		{
			$sql_file = "./install/typical_install_iis.sql";
		}
		else
		{
			$sql_file = "./install/typical_install.sql";
		}
	}
	else
	{
		$sql_file = "./install/base.sql";
	}
	if ( mysql_connect( $db_config['db_host'], $db_config['db_user'], $db_config['db_password'] ) )
	{
		if ( mysql_select_db( $db_config['db_name'] ) )
		{
			if ( $sql_handle = fopen( $sql_file, "r" ) )
			{
				$table_header = $db_config['table_pre'];
				$sql_query = fread( $sql_handle, filesize( $sql_file ) );
				fclose( $sql_handle );
				$sql_query = str_replace( "{\$table_header}", $table_header, $sql_query );
			}
			else
			{
				exit( "Unable to read  {$sql_file} " );
			}
			if ( !$IN['overwrite'] == 1 )
			{
				$sysExit = FALSE;
				$sql = "SHOW TABLES  FROM {$IN[database_name]}";
				$recordSet = mysql_query( $sql );
				$pattern = "/^{$IN[database_header]}.+/is";
				while ( $recordSet && ( $row = mysql_fetch_array( $recordSet, MYSQL_BOTH ) ) )
				{
					if ( preg_match( $pattern, $row[0] ) )
					{
						$sysExit = TRUE;
						break;
					}
				}
				if ( $sysExit )
				{
					echo "<script>\n if(confirm(\" 系统检测到你已经安装了CMSware,\\n继续安装将覆盖掉已安装的内容(包括CMSware和CWPS)，是否继续? \")){window.location=\"install.php?overwrite=1&step=3&database_host={$IN[database_host]}&database_name={$IN[database_name]}&database_header={$IN[database_header]}&database_user={$IN[database_user]}&database_password={$IN[database_password]}&installType={$IN[installType]}&installCWPS={$IN[installCWPS]}\";\r\n\t\t\t\t\t\t\t\t}else {\r\n\t\t\t\t\t\t\t\t\thistory.go(-1);\r\n\t\t\t\t\t\t\t\t}</script>\n";
					exit( );
				}
			}
			if ( runquery( $sql_query ) )
			{
				if ( $config_handle = fopen( "./install/config.ini", "r" ) )
				{
					$config_php = fread( $config_handle, filesize( "./install/config.ini" ) );
					$config_php = str_replace( "[cmsware_db_host]", $db_config['db_host'], $config_php );
					$config_php = str_replace( "[cmsware_db_user]", $db_config['db_user'], $config_php );
					$config_php = str_replace( "[cmsware_db_password]", $db_config['db_password'], $config_php );
					$config_php = str_replace( "[cmsware_db_name]", $db_config['db_name'], $config_php );
					$config_php = str_replace( "[cmsware_db_table_pre]", $db_config['table_pre'], $config_php );
					$config_php = str_replace( "[cmsware_db_charset]", $db_config['db_charset'], $config_php );
					$config_php = str_replace( "[ftp_mode]", $SYS_CONFIG['ftp_mode'], $config_php );
					$config_php = str_replace( "[ftp_host]", $SYS_CONFIG['ftp_host'], $config_php );
					$config_php = str_replace( "[ftp_port]", $SYS_CONFIG['ftp_port'], $config_php );
					$config_php = str_replace( "[ftp_username]", $SYS_CONFIG['ftp_username'], $config_php );
					$config_php = str_replace( "[ftp_password]", $SYS_CONFIG['ftp_password'], $config_php );
					$config_php = str_replace( "[ftp_cms_admin_path]", $SYS_CONFIG['ftp_cms_admin_path'], $config_php );
					$enable_validcode = extension_loaded( "gd" ) ? 1 : 0;
					$config_php = str_replace( "[enable_validcode]", $enable_validcode, $config_php );
					$handle = fopen( "config.php", "w" );
					@flock( $handle, 3 );
					fwrite( $handle, $config_php );
					fclose( $handle );
				}
				else
				{
					exit( "Unable to read ./install/config.ini" );
				}
				if ( $IN['installCWPS'] == "1" )
				{
					$cwps_install_sql_file = "../cwps/install/base.sql";
					if ( $sql_query_handle = fopen( $cwps_install_sql_file, "r" ) )
					{
						$sql_query = fread( $sql_query_handle, filesize( $cwps_install_sql_file ) );
						$sql_query = str_replace( "{\$table_header}", $db_config['table_pre']."cwps_", $sql_query );
						$sql_query = str_replace( "{CWPSPassword}", md5( $_SERVER['REMOTE_ADDR'].date( "Y-m-d" ) ), $sql_query );
						$sql_query = str_replace( "{ServerIP}", $_SERVER['SERVER_ADDR'], $sql_query );
						if ( runquery( $sql_query ) && ( $config_handle = fopen( "../cwps/install/config.ini", "r" ) ) )
						{
							$config_php = fread( $config_handle, filesize( "../cwps/install/config.ini" ) );
							$config_php = str_replace( "[cmsware_db_host]", $db_config['db_host'], $config_php );
							$config_php = str_replace( "[cmsware_db_user]", $db_config['db_user'], $config_php );
							$config_php = str_replace( "[cmsware_db_password]", $db_config['db_password'], $config_php );
							$config_php = str_replace( "[cmsware_db_name]", $db_config['db_name'], $config_php );
							$config_php = str_replace( "[cmsware_db_table_pre]", $db_config['table_pre']."cwps_", $config_php );
							$config_php = str_replace( "[cmsware_db_charset]", $db_config['db_charset'], $config_php );
							$enable_validcode = extension_loaded( "gd" ) ? 1 : 0;
							$config_php = str_replace( "[enable_validcode]", $enable_validcode, $config_php );
							$config_php = str_replace( "[sys_url]", $CMSWARE_PUB_URL."cwps", $config_php );
							$config_php = str_replace( "[html_url]", $CMSWARE_PUB_URL."cwps/html", $config_php );
							$config_php = str_replace( "[base_dir]", str_replace( "\\", "\\\\", realpath( "../cwps/" ) ), $config_php );
							$handle = fopen( "../cwps/config.php", "w" );
							@flock( $handle, 3 );
							fwrite( $handle, $config_php );
							fclose( $handle );
							$handle = fopen( "../cwps/tmp/install.lock", "w" );
							@flock( $handle, 3 );
							fwrite( $handle, "Install locked!" );
							fclose( $handle );
						}
					}
					else
					{
						exit( "Unable to read  {$cwps_install_sql_file} " );
					}
				}
				$TPL->assign( "output", $output );
				$TPL->display( "install3.html" );
			}
			else
			{
				$TPL->assign( "errmsg", "错误！数据表建立失败" );
				$TPL->display( "installmsg.html" );
			}
		}
		else if ( $IN[autoCreateDB] == "1" )
		{
			if ( mysql_query( "CREATE  DATABASE `".$db_config['db_name']."`" ) )
			{
				$output .= "自动创建数据库 <b>".$db_config['db_name']."</b> ... <font color=\"#0000EE\">成功 </font><br>";
				if ( mysql_select_db( $db_config['db_name'] ) )
				{
					if ( $sql_handle = fopen( $sql_file, "r" ) )
					{
						$table_header = $db_config['table_pre'];
						$sql_query = fread( $sql_handle, filesize( $sql_file ) );
						fclose( $sql_handle );
						$sql_query = str_replace( "{\$table_header}", $table_header, $sql_query );
					}
					else
					{
						exit( "Unable to read {$sql_file} " );
					}
					if ( $output .= runquery( $sql_query ) )
					{
						if ( $config_handle = fopen( "./install/config.ini", "r" ) )
						{
							$config_php = fread( $config_handle, filesize( "./install/config.ini" ) );
							$config_php = str_replace( "[cmsware_db_host]", $db_config['db_host'], $config_php );
							$config_php = str_replace( "[cmsware_db_user]", $db_config['db_user'], $config_php );
							$config_php = str_replace( "[cmsware_db_password]", $db_config['db_password'], $config_php );
							$config_php = str_replace( "[cmsware_db_name]", $db_config['db_name'], $config_php );
							$config_php = str_replace( "[cmsware_db_table_pre]", $db_config['table_pre'], $config_php );
							$config_php = str_replace( "[cmsware_db_charset]", $db_config['db_charset'], $config_php );
							$config_php = str_replace( "[ftp_mode]", $SYS_CONFIG['ftp_mode'], $config_php );
							$config_php = str_replace( "[ftp_host]", $SYS_CONFIG['ftp_host'], $config_php );
							$config_php = str_replace( "[ftp_port]", $SYS_CONFIG['ftp_port'], $config_php );
							$config_php = str_replace( "[ftp_username]", $SYS_CONFIG['ftp_username'], $config_php );
							$config_php = str_replace( "[ftp_password]", $SYS_CONFIG['ftp_password'], $config_php );
							$config_php = str_replace( "[ftp_cms_admin_path]", $SYS_CONFIG['ftp_cms_admin_path'], $config_php );
							$enable_validcode = extension_loaded( "gd" ) ? 1 : 0;
							$config_php = str_replace( "[enable_validcode]", $enable_validcode, $config_php );
							$handle = fopen( "config.php", "w" );
							@flock( $handle, 3 );
							fwrite( $handle, $config_php );
							fclose( $handle );
						}
						else
						{
							exit( "Unable to read ./install/config.ini" );
						}
						if ( $IN['installCWPS'] == "1" )
						{
							$cwps_install_sql_file = "../cwps/install/base.sql";
							if ( $sql_query_handle = fopen( $cwps_install_sql_file, "r" ) )
							{
								$sql_query = fread( $sql_query_handle, filesize( $cwps_install_sql_file ) );
								$sql_query = str_replace( "{\$table_header}", $db_config['table_pre']."cwps_", $sql_query );
								$sql_query = str_replace( "{CWPSPassword}", md5( $_SERVER['REMOTE_ADDR'].date( "Y-m-d" ) ), $sql_query );
								$sql_query = str_replace( "{ServerIP}", $_SERVER['SERVER_ADDR'], $sql_query );
								if ( runquery( $sql_query ) && ( $config_handle = fopen( "../cwps/install/config.ini", "r" ) ) )
								{
									$config_php = fread( $config_handle, filesize( "../cwps/install/config.ini" ) );
									$config_php = str_replace( "[cmsware_db_host]", $db_config['db_host'], $config_php );
									$config_php = str_replace( "[cmsware_db_user]", $db_config['db_user'], $config_php );
									$config_php = str_replace( "[cmsware_db_password]", $db_config['db_password'], $config_php );
									$config_php = str_replace( "[cmsware_db_name]", $db_config['db_name'], $config_php );
									$config_php = str_replace( "[cmsware_db_table_pre]", $db_config['table_pre']."cwps_", $config_php );
									$config_php = str_replace( "[cmsware_db_charset]", $db_config['db_charset'], $config_php );
									$enable_validcode = extension_loaded( "gd" ) ? 1 : 0;
									$config_php = str_replace( "[enable_validcode]", $enable_validcode, $config_php );
									$config_php = str_replace( "[sys_url]", $CMSWARE_PUB_URL."cwps", $config_php );
									$config_php = str_replace( "[html_url]", $CMSWARE_PUB_URL."cwps/html", $config_php );
									$config_php = str_replace( "[base_dir]", str_replace( "\\", "\\\\", realpath( "../cwps/" ) ), $config_php );
									$handle = fopen( "../cwps/config.php", "w" );
									@flock( $handle, 3 );
									fwrite( $handle, $config_php );
									fclose( $handle );
								}
							}
							else
							{
								exit( "Unable to read  {$cwps_install_sql_file} " );
							}
						}
						$TPL->assign( "output", $output );
						$TPL->display( "install3.html" );
					}
					else
					{
						$TPL->assign( "errmsg", "错误！数据表建立失败" );
						$TPL->display( "installmsg.html" );
					}
				}
			}
			else
			{
				$TPL->assign( "errmsg", "Create Database <b>{$new_db_name}</b> Failed! May be Your MySQL account have not the Create Database privilege,please contact to your administrator<br>无法创建数据库 <b>{$new_db_name}</b>,可能你的MySQL账号没有创建数据库的权限，请联系管理员！ " );
				$TPL->display( "installmsg.html" );
			}
		}
		else
		{
			$TPL->assign( "errmsg", "错误！无法连接数据库{$db_config['db_name']},请返回重新设置你的数据库连接参数" );
			$TPL->display( "installmsg.html" );
			exit( );
		}
	}
	break;
case "4" :
	$TPL->assign( "installType", $IN['installType'] );
	$TPL->assign( "installCWPS", $IN['installCWPS'] );
	if ( mysql_connect( $db_config['db_host'], $db_config['db_user'], $db_config['db_password'] ) )
	{
		mysql_query( "SET NAMES '".$db_config['db_charset']."' " );
		if ( mysql_select_db( $db_config['db_name'] ) )
		{
			$user_pass = md5( $IN[password] );
			$sql = "INSERT INTO {$db_config['table_pre']}user VALUES (NULL, 1, '{$IN[root]}', '{$user_pass}', '{$IN[root]}', 0, 0, 0, 0, 0, 0);\r\n";
			$sqlcwpsok = FALSE;
			if ( $IN['installCWPS'] == "1" )
			{
				$sqlcwps = "INSERT INTO {$db_config['table_pre']}cwps_user VALUES ('1', 2, '{$IN[root]}', '{$user_pass}', '', '', 'admin@admin.com', '{$IN[root]}', 0, '0000-00-00', '', '', 1, ".time( ).", NULL, ',,', 6, ',,', ',,');\r\n";
				$sqlcwps2 = "INSERT INTO {$db_config['table_pre']}cwps_user_extra values(1,'-','0');\r\n";
				if ( mysql_query( $sqlcwps ) && mysql_query( $sqlcwps2 ) )
				{
					$sqlcwpsok = TRUE;
				}
			}
			if ( mysql_query( $sql ) && $sqlcwpsok )
			{
				$sqlcwps = "UPDATE `{$db_config['table_pre']}plugin_oas_setting` SET `value`='{$CMSWARE_PUB_URL}cwps/soap.php' WHERE `key`='CWPS_Address';";
				mysql_query( $sqlcwps );
				$sqlcwps = "UPDATE `{$db_config['table_pre']}plugin_oas_setting` SET `value`='".md5( $_SERVER['REMOTE_ADDR'].date( "Y-m-d" ) )."' WHERE `key`='CWPS_TransactionAccessKey';";
				mysql_query( $sqlcwps );
				$sqlcwps = "UPDATE `{$db_config['table_pre']}plugin_oas_setting` SET `value`='{$CMSWARE_PUB_URL}cwps' WHERE `key`='CWPS_RootURL';";
				mysql_query( $sqlcwps );
				$sqlcwps = "UPDATE `{$db_config['table_pre']}plugin_oas_setting` SET `value`='{$CMSWARE_ADMIN_URL}oas' WHERE `key`='OAS_RootURL';";
				mysql_query( $sqlcwps );
				$sqlcwps = "UPDATE `{$db_config['table_pre']}plugin_oas_setting` SET `value`='{$IN[root]}' WHERE `key`='CWPS_AdminUserName';";
				mysql_query( $sqlcwps );
				$sqlcwps = "UPDATE `{$db_config['table_pre']}plugin_oas_setting` SET `value`='{$IN[password]}' WHERE `key`='CWPS_AdminPassword';";
				mysql_query( $sqlcwps );
				$TPL->assign( "URL", substr( $CMSWARE_PUB_URL, 0, -1 ) );
				$TPL->assign( "output", "管理员建立成功！" );
				$TPL->display( "install4.html" );
			}
			else
			{
				$TPL->assign( "errmsg", "管理员建立失败".mysql_error( ) );
				$TPL->display( "installmsg.html" );
			}
		}
	}
	break;
case "5" :
	$TPL->assign( "installType", $IN['installType'] );
	$TPL->assign( "installCWPS", $IN['installCWPS'] );
	if ( mysql_connect( $db_config['db_host'], $db_config['db_user'], $db_config['db_password'] ) )
	{
		mysql_query( "SET NAMES '".$db_config['db_charset']."' " );
		if ( mysql_select_db( $db_config['db_name'] ) )
		{
			$sql = "INSERT INTO {$db_config['table_pre']}psn VALUES (NULL, '{$IN[Name]}', 'relate::{$IN[PATH]}', '{$IN[URL]}', ' ',' ');";
			if ( mysql_query( $sql ) )
			{
				$sql = "INSERT INTO `{$db_config['table_pre']}tpl_vars` VALUES (1, '前台动态程序URL', 'PUBLISH_URL', '{$CMSWARE_PUB_URL}publish/', 1, '');";
				mysql_query( $sql );
				$sql = "INSERT INTO `{$db_config['table_pre']}tpl_vars` VALUES (2, 'PublishAPI接口URL', 'PUBLISHAPI_URL', '{$CMSWARE_PUB_URL}publishapi/', 1, NULL);";
				mysql_query( $sql );
				$sql = "INSERT INTO `{$db_config['table_pre']}tpl_vars` VALUES (3, '网站名称', 'SITE_NAME', '{$IN[Name]}', 1, NULL);";
				mysql_query( $sql );
				$sql = "INSERT INTO `{$db_config['table_pre']}tpl_vars` VALUES (4, '网站首页URL', 'SITE_URL', '{$CMSWARE_PUB_URL}', 1, '');";
				mysql_query( $sql );
				$sql = "INSERT INTO `{$db_config['table_pre']}tpl_vars` VALUES (5, '模板资源URL', 'SKIN_URL', '{$CMSWARE_PUB_URL}skin/', 1, NULL);";
				mysql_query( $sql );
				$sql = "INSERT INTO `{$db_config['table_pre']}tpl_vars` VALUES (6, 'OAS的URL', 'OAS_URL', '{$CMSWARE_PUB_URL}oas/', 1, '');";
				mysql_query( $sql );
				if ( $config_handle = fopen( "./install/publish.config.ini", "r" ) )
				{
					$config_php = fread( $config_handle, filesize( "./install/publish.config.ini" ) );
					$config_php = str_replace( "[cmsware_publish_root_path]", "../".$cmsware_install_dir."/", $config_php );
					$handle = fopen( "../publish/config.php", "w" );
					@flock( $handle, 3 );
					fwrite( $handle, $config_php );
					fclose( $handle );
				}
				else
				{
					exit( "Unable to read ./install/publish.config.ini" );
				}
				if ( $config_handle = fopen( "./install/publishapi.config.ini", "r" ) )
				{
					$config_php = fread( $config_handle, filesize( "./install/publishapi.config.ini" ) );
					$config_php = str_replace( "[cmsware_publishapi_root_path]", "../".$cmsware_install_dir."/", $config_php );
					$config_php = str_replace( "[cmsware_publishapi_name]", $IN[Name], $config_php );
					$handle = fopen( "../publishapi/config.ini.php", "w" );
					@flock( $handle, 3 );
					fwrite( $handle, $config_php );
					fclose( $handle );
				}
				else
				{
					exit( "Unable to read ./install/publishapi.config.ini" );
				}
				$oas_main_domain = "";
				$TPL->assign( "oas_cwps_url", $CMSWARE_PUB_URL."cwps/" );
				$TPL->assign( "oas_oas_url", $CMSWARE_PUB_URL."oas/" );
				$TPL->assign( "oas_oasid", "22" );
				$TPL->assign( "oas_transactionaccessKey", md5( $_SERVER['REMOTE_ADDR'].date( "Y-m-d" ) ) );
				$TPL->assign( "oas_main_domain", $oas_main_domain );
				$TPL->assign( "output", "网站发布PSN建立成功！" );
				$TPL->display( "install5.html" );
			}
			else
			{
				$TPL->assign( "errmsg", "网站发布PSN建立失败".mysql_error( ) );
				$TPL->display( "installmsg.html" );
			}
		}
	}
	break;
case "6" :
	$TPL->assign( "installType", $IN['installType'] );
	$TPL->assign( "installCWPS", $IN['installCWPS'] );
	if ( $config_handle = fopen( "./install/oas.config.ini", "r" ) )
	{
		$config_php = fread( $config_handle, filesize( "./install/oas.config.ini" ) );
		$config_php = str_replace( "[oas_cwps_url]", $IN['oas_cwps_url'], $config_php );
		$config_php = str_replace( "[oas_oas_url]", $IN['oas_oas_url'], $config_php );
		$config_php = str_replace( "[oas_oasid]", $IN['oas_oasid'], $config_php );
		$config_php = str_replace( "[oas_charset]", $db_config['db_charset'], $config_php );
		$config_php = str_replace( "[oas_transactionaccessKey]", $IN['oas_transactionaccessKey'], $config_php );
		$config_php = str_replace( "[oas_main_domain]", $IN['oas_main_domain'], $config_php );
		$handle = fopen( "../oas/oas.config.php", "w" );
		@flock( $handle, 3 );
		fwrite( $handle, $config_php );
		fclose( $handle );
	}
	else
	{
		exit( "Unable to read ./install/oas.config.ini" );
	}
	if ( $IN['installType'] == "typical" )
	{
		if ( file_exists( CACHE_DIR."Cache_SYS_ENV.php" ) )
		{
			unlink( CACHE_DIR."Cache_SYS_ENV.php" );
		}
		if ( file_exists( CACHE_DIR."Cache_PSN.php" ) )
		{
			unlink( CACHE_DIR."Cache_PSN.php" );
		}
		if ( file_exists( CACHE_DIR."Cache_CateList.php" ) )
		{
			unlink( CACHE_DIR."Cache_CateList.php" );
		}
		if ( file_exists( CACHE_DIR.".ftp" ) )
		{
			unlink( CACHE_DIR.".ftp" );
		}
		$TPL->assign( "output", "<font color=red>恭喜</font>，思维系统安装完毕！<br /><br />如果你安装了CWPS，请进入CWPS后台({$CMSWARE_PUB_URL}cwps/index.php)设置相应oas的IP等设置" );
		$TPL->display( "install_end.html" );
	}
	else
	{
		$TPL->assign( "output", "OAS配置成功！" );
		$TPL->display( "install6.html" );
	}
	break;
case "7" :
	if ( mysql_connect( $db_config['db_host'], $db_config['db_user'], $db_config['db_password'] ) )
	{
		if ( mysql_select_db( $db_config['db_name'] ) )
		{
			if ( $IN[Model_News] == "1" )
			{
				if ( $sql_handle = fopen( "./install/model_news.sql", "r" ) )
				{
					$table_header = $db_config['table_pre'];
					$sql_query = fread( $sql_handle, filesize( "./install/model_news.sql" ) );
					fclose( $sql_handle );
					$sql_query = str_replace( "{\$table_header}", $table_header, $sql_query );
				}
				else
				{
					exit( "Unable to read ./install/model_news.sql" );
				}
				if ( $output .= runquery( $sql_query ) )
				{
					$output .= "创建新闻系统模型... <font color=#0000EE>成功</font><br>";
				}
			}
			if ( $IN[Model_Download] == "1" )
			{
				if ( $sql_handle = fopen( "./install/model_download.sql", "r" ) )
				{
					$table_header = $db_config['table_pre'];
					$sql_query = fread( $sql_handle, filesize( "./install/model_download.sql" ) );
					fclose( $sql_handle );
					$sql_query = str_replace( "{\$table_header}", $table_header, $sql_query );
				}
				else
				{
					exit( "Unable to read ./install/model_download.sql" );
				}
				if ( $output .= runquery( $sql_query ) )
				{
					$output .= "创建下载系统模型... <font color=#0000EE>成功</font><br>";
				}
			}
			if ( $IN[Plugin_bbsInterface] == "1" )
			{
				if ( $sql_handle = fopen( "./install/plugin_bbsi.sql", "r" ) )
				{
					$table_header = $db_config['table_pre'];
					$sql_query = fread( $sql_handle, filesize( "./install/plugin_bbsi.sql" ) );
					fclose( $sql_handle );
					$sql_query = str_replace( "{\$table_header}", $table_header, $sql_query );
				}
				else
				{
					exit( "Unable to read ./install/plugin_bbsi.sql" );
				}
				if ( $output .= runquery( $sql_query ) )
				{
					$output .= "添加会员接口插件... <font color=#0000EE>成功</font><br>";
				}
			}
			if ( $IN[Plugin_FullText] == "1" )
			{
				if ( $sql_handle = fopen( "./install/plugin_fulltext.sql", "r" ) )
				{
					$table_header = $db_config['table_pre'];
					$sql_query = fread( $sql_handle, filesize( "./install/plugin_fulltext.sql" ) );
					fclose( $sql_handle );
					$sql_query = str_replace( "{\$table_header}", $table_header, $sql_query );
				}
				else
				{
					exit( "Unable to read ./install/plugin_fulltext.sql" );
				}
				if ( $output .= runquery( $sql_query ) )
				{
					$output .= "添加全文检索插件... <font color=#0000EE>成功</font><br>";
				}
			}
			$TPL->assign( "output", $output );
			$TPL->display( "install7.html" );
		}
		else
		{
			exit( "无法选择表" );
		}
	}
	else
	{
		exit( "无法连接数据库" );
	}
	break;
case "8" :
	if ( mysql_connect( $db_config['db_host'], $db_config['db_user'], $db_config['db_password'] ) )
	{
		if ( mysql_select_db( $db_config['db_name'] ) )
		{
			if ( $IN[Collection] == "1" )
			{
				if ( $sql_handle = fopen( "./install/collection.sql", "r" ) )
				{
					$table_header = $db_config['table_pre'];
					$sql_query = fread( $sql_handle, filesize( "./install/collection.sql" ) );
					fclose( $sql_handle );
					$sql_query = str_replace( "{\$table_header}", $table_header, $sql_query );
				}
				else
				{
					exit( "Unable to read ./install/collection.sql" );
				}
				if ( $output = runquery( $sql_query ) )
				{
					$output = "创建采集演示分类... <font color=#0000EE>成功</font><br>";
				}
			}
			$TPL->assign( "output", $output );
			$TPL->display( "install8.html" );
		}
		else
		{
			exit( "无法选择表" );
		}
	}
	else
	{
		exit( "无法连接数据库" );
	}
	break;
case "9" :
	if ( file_exists( CACHE_DIR."Cache_SYS_ENV.php" ) )
	{
		unlink( CACHE_DIR."Cache_SYS_ENV.php" );
	}
	if ( file_exists( CACHE_DIR."Cache_PSN.php" ) )
	{
		unlink( CACHE_DIR."Cache_PSN.php" );
	}
	if ( file_exists( CACHE_DIR."Cache_CateList.php" ) )
	{
		unlink( CACHE_DIR."Cache_CateList.php" );
	}
	if ( file_exists( CACHE_DIR.".ftp" ) )
	{
		unlink( CACHE_DIR.".ftp" );
	}
	$TPL->assign( "output", "<font color=red>恭喜</font>，思维CMSware系统安装完毕！<br /><br />如果你安装了CWPS，请进入CWPS后台设置相应cmswareoas的IP" );
	$TPL->display( "install_end.html" );
	break;
case "end" :
	if ( !empty( $IN['delInstaller'] ) )
	{
		if ( file_exists( SYS_PATH."install.php" ) )
		{
			unlink( SYS_PATH."install.php" );
		}
		if ( file_exists( SYS_PATH."update.php" ) )
		{
			unlink( SYS_PATH."update.php" );
		}
	}
	$handle = fopen( "./sysdata/install.lock", "w" );
	@flock( $handle, 3 );
	fwrite( $handle, "lock the cmsware installer" );
	fclose( $handle );
	$handle = fopen( "./sysdata/.install", "w" );
	@flock( $handle, 3 );
	fwrite( $handle, "First Login cmsware!" );
	fclose( $handle );
	if ( $IN['installCWPS'] == 1 )
	{
		$handle = fopen( "../cwps/tmp/install.lock", "w" );
		@flock( $handle, 3 );
		fwrite( $handle, "cwps Install locked!" );
		fclose( $handle );
		if ( file_exists( "../cwps/tmp/cache.SoapAction.php" ) )
		{
			@unlink( "../cwps/tmp/cache.SoapAction.php" );
		}
		if ( file_exists( "../cwps/tmp/cache.SoapOAS.php" ) )
		{
			@unlink( "../cwps/tmp/cache.SoapOAS.php" );
		}
		if ( file_exists( "../cwps/tmp/config.Setting.php" ) )
		{
			@unlink( "../cwps/tmp/config.Setting.php" );
		}
	}
	header( "Location: ./index.php" );
default :
	$TPL->display( "install.html" );
	break;
}
echo "<br>\r\n<div align=center><font id='description'>Powered by <a href=\"http://www.localhost.com\" target=\"_blank\" id='description'>";
echo "<?=";
echo "\$version?></a> <BR> &nbsp; Copyright &copy; <a href=\"http://www.localhost.com\" target=\\\"_blank\\\" id='description'>Lonmo Technology Ltd</a> 2002-";
echo "<?=";
echo "date('Y')?> ,All rights reserved.</font> </div>";
?>
