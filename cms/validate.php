<?php
function CMSwareDetector( )
{
	global $LicenseInfo;
	/*$fp = fsockopen( "validation.cmsware.com", 80, $errno, $errstr, 5 );
	if ( $fp )
	{
		$send = PHP_OS." - - ".$_SERVER['HTTP_HOST']." - - ".$_SERVER['SERVER_SOFTWARE']." - - ".$_SERVER['SERVER_NAME']." - - ".$_SERVER['SERVER_ADDR']." - - ".$LicenseInfo['Registered-URL']." - - ".$LicenseInfo['License-key'];
		$send = urlencode( $send );
		$Request = "GET /index.php?o=cmsware&send={$send} HTTP/1.0\r\n";
		$Request .= "Host: validation.cmsware.com\r\n";
		$Request .= "Connection: Close\r\n";
		$Request .= "\r\n";
		fputs( $fp, $Request );
		fclose( $fp );
	}*/
}

function LicenseVerify( $force = 0 )
{
	global $LicenseInfo;
	global $db;
	global $db_config;
	global $table;
	restore_error_handler( );
//	$Host = "validation.cmsware.com";
	$Host = "sesoe.com";
	$Path = "/license.php";  //配置文件
	$Port = 80;
	//从本地数据库查询
	//获取类似事件的东西重要的东西
	$result = $db->getRow( "SELECT * from {$table->sys} WHERE varName='tasktimeout' " );
	if ( !isset( $result['varName'] ) )
	{
		//如果不存在则插入为0 并设结果值为0
		$db->query( "Insert into {$table->sys} VALUES('','tasktimeout','0') " );
		$result['varValue'] = 0;
	}
	
	$lastTime = $result['varValue'];
	unset( $result );
	$offetTime = abs( time( ) - $lastTime );

	//172899什么意思呢 force 什么意思
	if ( 172800 < $offetTime || $force == 1 )
	{
		
		//远程文件读取并验证过程
		//用版权配置文件传送过去
/*		$XMLData = "<Request>\r\n";
		$XMLData .= "<URL>{$LicenseInfo['Registered-URL']}</URL>\r\n";
		$XMLData .= "<Key>{$LicenseInfo['License-key']}</Key>\r\n";
		$XMLData .= "<TransactionTime>".date( "Y-m-d H:i:s" )."</TransactionTime>\r\n";
		$XMLData .= "</Request>";*/
		// 配置发送地址
/*		$Request = "POST {$Path} HTTP/1.0\r\n";
		$Request .= "Host: {$Host} \r\n";
		$Request .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$Request .= "Content-Length: ".strlen( $XMLData )."\r\n\r\n";
		$Request .= $XMLData;
		$result = "";*/
		//打开远程返回文件
//		$f = @fsockopen( $Host, $Port, $errno, $errstr, 2 );
//		//读取文件并把值给$Response
/*		if ( $f )
		{
			@fputs( $f, $Request );
			stream_set_timeout( $f, 5 );
			while ( !feof( $f ) )
			{
				$Response .= @fread( $f, 128 );
			}
			fclose( $f );
		}*/
		//设置模式
//		$pattern = "/<Response>\r\n<Valid>(.*)<\\/Valid>\r\n<\\/Response>/isU";
		//在返回的字符串中用正则查找相关信息
/*~~~~~~~~~~~~~~~~~~~~~	增加处理逻辑 wangtao */
//$db->query( "update {$table->sys} set varValue='start' WHERE varName='openTask' " );	
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	/*	if ( preg_match( $pattern, $Response, $matches ) )
		{
			//如果为 -1 的话 将任务开始舍为1
			if ( $matches[1] == "-1" )
			{
				$db->query( "update {$table->sys} set varValue='start' WHERE varName='openTask' " );
			}
//			否则将开始字段舍为0 即不开启
			else if ( $matches[1] == "1" )
			{
				$db->query( "update {$table->sys} set varValue='0' WHERE varName='openTask' " );
			}
			else
			{
//				$ip = gethostbyname( "validation.cmsware.com" );
				if ( $ip != "218.75.46.116" )
				{
					exit( $invalid_ip_info );
				}
			}
		}*/
	}
		$db->query( "update {$table->sys} set varValue='start' WHERE varName='openTask' " );
		$db->query( "update {$table->sys} set varValue='".time()."' WHERE varName='tasktimeout' " ); //该处的时间应该设置的比较晚些
}

error_reporting( "E_ALL & ~E_NOTICE" );
$die_info = "<html>\r\n<head>\r\n<title>Invalid License File</title>\r\n</head>\r\n<body bgcolor=\"#FFFFFF\">\r\n<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"600\" align=\"center\" height=\"85%\">\r\n  <tr align=\"center\" valign=\"middle\">\r\n    <td>\r\n    <table cellpadding=\"10\" cellspacing=\"0\" border=\"0\" width=\"80%\" align=\"center\" style=\"font-family: Verdana, Tahoma; color: #666666; font-size: 10px\">\r\n    <tr>\r\n      <td valign=\"middle\"  bgcolor=\"#cccccc\">\r\n        <br><b style=\"font-size: 11px\">Your License was Invalid</b>\r\n        <br><br>Shifting to a new server, changing host name or modifing your licence file, may invalidate your license. If you have trouble in activation, please contact to your CMS provider for more information.\r\n        <br>\r\n\t\tError:0x00000001 \r\n\t\t<br>\r\n      </td>\r\n    </tr>\r\n    </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n</body>\r\n</html>";
$expire_info = "<html>\r\n<head>\r\n<title>CMS Expired</title>\r\n</head>\r\n<body bgcolor=\"#FFFFFF\">\r\n<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"600\" align=\"center\" height=\"85%\">\r\n  <tr align=\"center\" valign=\"middle\">\r\n    <td>\r\n    <table cellpadding=\"10\" cellspacing=\"0\" border=\"0\" width=\"80%\" align=\"center\" style=\"font-family: Verdana, Tahoma; color: #666666; font-size: 10px\">\r\n    <tr>\r\n      <td valign=\"middle\"  bgcolor=\"#cccccc\">\r\n        <br><b style=\"font-size: 11px\">Your Product have expired!</b>\r\n        <br><br>Your License have expired. If you want to continue to use this Product , please contact to your CMS provider for more information.\r\n        <br>\r\n\t\tError:0x00000002\r\n\t\t<br>\r\n\t\t\r\n      </td>\r\n    </tr>\r\n    </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n</body>\r\n</html>";
$invalid_info = "<html>\r\n<head>\r\n<title>Invalid License File</title>\r\n</head>\r\n<body bgcolor=\"#FFFFFF\">\r\n<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"600\" align=\"center\" height=\"85%\">\r\n  <tr align=\"center\" valign=\"middle\">\r\n    <td>\r\n    <table cellpadding=\"10\" cellspacing=\"0\" border=\"0\" width=\"80%\" align=\"center\" style=\"font-family: Verdana, Tahoma; color: #666666; font-size: 10px\">\r\n    <tr>\r\n      <td valign=\"middle\"  bgcolor=\"#cccccc\">\r\n        <br><b style=\"font-size: 11px\">Your License was Invalid</b>\r\n        <br><br>If you have trouble in activation, please contact to your CMS provider for more information.\r\n        <br>\r\n\t\t\t\tError:0x00000003\r\n\t\t<br>\r\n\r\n      </td>\r\n    </tr>\r\n    </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n</body>\r\n</html>";
$invalid_ip_info = "<html>\r\n<head>\r\n<title>Invalid License File</title>\r\n</head>\r\n<body bgcolor=\"#FFFFFF\">\r\n<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"600\" align=\"center\" height=\"85%\">\r\n  <tr align=\"center\" valign=\"middle\">\r\n    <td>\r\n    <table cellpadding=\"10\" cellspacing=\"0\" border=\"0\" width=\"80%\" align=\"center\" style=\"font-family: Verdana, Tahoma; color: #666666; font-size: 10px\">\r\n    <tr>\r\n      <td valign=\"middle\"  bgcolor=\"#cccccc\">\r\n        <br><b style=\"font-size: 11px\">Your License was Invalid</b>\r\n        <br><br>If you have trouble in activation, please contact to your CMS provider for more information.\r\n        <br>\r\n\t\t\t\tError:0x00000004\r\n\t\t<br>\r\n\r\n      </td>\r\n    </tr>\r\n    </table>\r\n    </td>\r\n  </tr>\r\n</table>\r\n</body>\r\n</html>";

require( "../license.php" );
$license_array = $License;
unset( $License );
if ( !$license_array )
{
	exit( "License File Not Found" );
}
$encoder_key1 = "";
$encoder_key1 .= $_SERVER['HTTP_HOST'];
$encoder_key1 .= $license_array['Product-name'];
$encoder_key1 .= $license_array['Registered-to'];
$encoder_key1 .= $license_array['Registered-Date'];
$encoder_key1 .= $license_array['Expired-Time'];
$encoder_key1 .= $license_array['Licence-issued'];
$encoder_key1 .= $license_array['Node-num'];
$encoder_key1 .= $license_array['ContentModel-num'];
$encoder_key1 .= $license_array['RemotePSN-num'];
$encoder_key1 .= $license_array['Publish-Marker'];
$encoder_key1 .= $license_array['Publish-Title-Marker'];

$key = strtoupper( md5( "cmsware zy :)".$encoder_key1 ) ).substr( strtoupper( md5( $encoder_key1 ) ), 1, 8 );
$registerTime = strtotime( $license_array['Registered-Date'] );

if ( empty( $license_array['Expired-Time'] ) )
{
}
else
{
}
$_REGISTER_USER = $license_array['Registered-to'];
$_REGISTER_URL = $license_array['Registered-URL'];
$LicenseInfo = $license_array;
unset( $license_array );
//在系统数据中包含一些文件  ！！！控制登陆
$filename = "../sysdata/Cache_Tmp.php";
if ( !file_exists( $filename ) )
{
	cmswaredetector( );
	if ( $fp = fopen( $filename, "w+" ) )
	{
		fwrite( $fp, "" );
		fclose( $fp );
	}
}
else
{
	$oldtime = filemtime( $filename );
	if ( $oldtime + 1296000 < time( ) )
	{
		cmswaredetector( );
		touch( $filename );
	}
}
unset( $filename );


?>