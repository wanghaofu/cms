<?php
//该函数要返回什么东西？？
function callRemoter( $Path )
{
	return;
	global $LicenseInfo;
	global $db;
	global $db_config;
	global $table;
	global $_PatchVersion;
	$Host = "www.localhost.org";
	$Port = 80;
	$XMLData = "<Request>\r\n";
	$XMLData .= "<version>".BUILD_VERSION."</version>\r\n";
	$XMLData .= "<patch>".$_PatchVersion."</patch>\r\n";
	$XMLData .= "<URL>{$LicenseInfo['Registered-URL']}</URL>\r\n";
	$XMLData .= "<Key>{$LicenseInfo['License-key']}</Key>\r\n";
	$XMLData .= "<TransactionTime>".date( "Y-m-d H:i:s" )."</TransactionTime>\r\n";
	$XMLData .= "</Request>";
	$Request = "POST {$Path} HTTP/1.0\r\n";
	$Request .= "Host: {$Host} \r\n";
	$Request .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$Request .= "Content-Length: ".strlen( $XMLData )."\r\n\r\n";
	$Request .= $XMLData;
	$result = "";
//	$f = @fsockopen( $Host, $Port, $errno, $errstr, 2 );
	if ( $f )
	{
		@fputs( $f, $Request );
		stream_set_timeout( $f, 5 );
		while ( !feof( $f ) )
		{
			$Response .= @fread( $f, 128 );
		}
		fclose( $f );
	}
	$pattern = "/<Response>(.*)<\\/Response>/isU";
	if ( preg_match( $pattern, $Response, $matches ) )
	{
		$return = base64_decode( $matches[1] );
	}
	return $return;
}

require_once( INCLUDE_PATH."admin/logAdmin.class.php" );
session_save_path( "../sysdata" );
if ( !defined( "IN_IWPC" ) )
{
	exit( "Access Denied" );
}
if ( file_exists( "../version.php" ) )
{
	include_once( "../version.php" );
	$TPL->assign( "patch_version", $_PatchVersion );
}
//de($action   , __file__,__line__,1);
//判断流程
switch ( $action )
{
case "login" : //登陆
	if ( $sys->isBanned( ) )
	{
		$TPL->assign( "error_message", $_LANG_ADMIN['ip_block'] );
		$TPL->display( "login.html" );
		exit( );
	}
	if ( $SYS_CONFIG['enable_validcode'] )
	{
		session_start( );
		if ( isset( $_SESSION['LoginTryCount'] ) )
		{
			if ( $SYS_ENV['LoginTryTime'] * 60 < time( ) - $_SESSION['LoginTryTime'] )
			{
				$_SESSION['LoginTryCount'] = 1;
				$_SESSION['LoginTryTime'] = time( );
			}
			else
			{
				++$_SESSION['LoginTryCount'];
				if ( $SYS_ENV['LoginTryCount'] <= $_SESSION['LoginTryCount'] )
				{
					$sys->addBlockIP( $IN['IP_ADDRESS'] );
				}
			}
		}
		else
		{
			$_SESSION['LoginTryCount'] = 1;
			$_SESSION['LoginTryTime'] = time( );
		}
//		de( $_SESSION['sessionValid'],__file__,__line__,'0');
		if ( empty( $_SESSION['sessionValid'] ) )
		{
			$TPL->assign( "error_message", sprintf( $_LANG_ADMIN['validCode_error_hacker'], $SYS_ENV['LoginTryCount'] - $_SESSION['LoginTryCount'] ) );
			$TPL->display( "login.html" );
			exit( );
		}
		else
		{
			if ( !function_exists( "ImagePNG" ) )
			{
			}
			else
			{
				if ( $IN['validCode'] == $_SESSION['ValidateCode'] )
				{
				}
				else
				{
					$TPL->assign( "error_message", sprintf( $_LANG_ADMIN['validCode_error'], $SYS_ENV['LoginTryCount'] - $_SESSION['LoginTryCount'] ) );
					$TPL->display( "login.html" );
					exit( );
				}
			}
		}
	}
	
	$logAdmin = new logAdmin( );
	if ( $sys->login( $IN[username], $IN[password], $IN['IpSecurity'] ) )
	{
		$logAdmin->addLoginLog( $IN['username'], $IN['IP_ADDRESS'], TRUE );
		unset( $_SESSION['LoginTryCount'] );
		$TPL->assign( "base_url", "index.php?sId={$sys->sId}&" );
		$TPL->assign( "sId", $sys->sId );
		$TPL->display( "panel_frameset.html" );
	}
	else if ( isset( $IN[username] ) || isset( $IN[password] ) )
	{
		$TPL->assign( "error_message", sprintf( $_LANG_ADMIN['username_error'], $SYS_ENV['LoginTryCount'] - $_SESSION['LoginTryCount'] ) );
		$TPL->display( "login.html" );
		$logAdmin->addLoginLog( $IN['username'], $IN['IP_ADDRESS'], FALSE );
	}
	else
	{
		$TPL->display( "login.html" );
		$logAdmin->addLoginLog( $IN['username'], $IN['IP_ADDRESS'], FALSE );
	}
	break;
case "logout" : //退出
	if ( $sys->logout( ) )
	{
		$TPL->display( "logout.html" );
	}
	break;
case "view" :
	$TPL->assign( "session", $sys->session );
	switch ( $IN[extra] )
	{
	case "header" :
		require_once( INCLUDE_PATH."admin/plugins_admin.class.php" );
		$plugins = new PluginsAdmin( );
		$pluginsList = $plugins->getAll( );
		$TPL->assign( "pluginsList", $pluginsList );
		$TPL->assign( "pluginsMenuLength", 21 * ( count( $pluginsList ) + 1 ) - 1 );
		$TPL->display( "panel_header.html" );
		break;
	case "box" :
		$TPL->display( "panel_box.html" );
		break;
	case "initMultiThread" :
		$diableDebug = TRUE;
		$TPL->display( "panel_MultiThread.html" );
		break;
	case "taskInfo" :
		$TPL->display( "panel_taskInfo.html" );
		break;
	case "menu" :
		header( "Location:admin_tree.php?sId={$IN['sId']}&o=publish" );
		break;
	case "admin_sys" :
		$TPL->display( "panel_admin_sys.html" );
		break;
	case "workarea" :
		$TPL->assign_by_ref( "NODE_LIST", $NODE_LIST );
		include( MODULES_DIR."DM_right.php" );
		break;
	case "phpinfo" :
		if ( !$sys->isAdmin( ) )
		{
			goback( "access_deny_module_setting" );
		}
		phpinfo( );
		exit( );
		break;
	default :
		$TPL->display( "panel_frameset.html" );
		break;
	}
	break;
case "chpassword" :
	$TPL->display( "chpassword.html" );
	break;
case "chpassword_submit" :
	break;
	if ( $password != "" )
	{
		if ( $newpassword != $newpassword2 )
		{
			$TPL->assign( "error_message", $SYS_ERROR['sys_chpassword_password_not_match'] );
			$TPL->display( "chpassword.html" );
		}
		else
		{
			if ( $sys->chpassword( $password, $newpassword ) )
			{
				$TPL->assign( "error_message", $SYS_ERROR['sys_chpassword_ok'] );
			}
			else
			{
				$TPL->assign( "error_message", $SYS_ERROR['sys_chpassword_fail'] );
			}
			$TPL->display( "chpassword.html" );
		}
	}
	else
	{
		$TPL->assign( "error_message", $SYS_ERROR['sys_chpassword_password_null'] );
		$TPL->display( "chpassword.html" );
	}
	break;
case "version" :
//	exit( callremoter( "/update/version.php?o=version" ) );
	break;
case "detect_news" :
//	exit( callremoter( "/update/version.php?o=detect_news" ) );
	break;
default :
	$TPL->display( "login.html" ); //原本的
//王涛添加的 测试文件
//$TPL->display( "panel_box.html" );
//$TPL->display( "panel_admin_sys.html" );

	break;
}
?>
