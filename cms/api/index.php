<?php


define( "IN_SHELL", TRUE );
$VersionInfo = "CMSware api script  (version:1.0)\r\nCopyright (c) 1999-2006 Sagatec Technology.";
$URL_INTERFACE = "http://".$_SERVER['SERVER_NAME']."".$_SERVER['SCRIPT_NAME'];
$HelpInfo = "<meta name=\"keywords\" content=\"cmsware,cms- Powered By CMSware 2.6.2 Plus \" >\r\n<meta name=\"description\" content=\"cmsware- Powered By CMSware 2.6.2 Plus \" >\r\n</HEAD>\r\n<style>\r\nbody\t\t\t{   font-family: Tahoma; color: #000000; font-size: 11px  }\r\ntd\t\t\t{ font-family: Tahoma; color: #000000; font-size: 11px }\r\n\r\n</style>\r\n<H1>CMSware WEB API\r\n</H1>\r\n<H3>使用方法: {$URL_INTERFACE}?password=***&o=***&id=***&sub=***<br>\r\n</H3>\r\n<UL>\r\n\t\t<LI><B>password</B>: API访问密码\r\n\t\t<LI><B>o</B>: 调用功能\r\n\t\t<LI><B>id</B>: 调用ID\r\n\t\t<LI><B>sub</B>: 是否包含子ID\r\n\t</UL>\t\r\n注意：结点更新、采集等功能可能需要很长的执行时间，建议使用CMSware shell来执行，如果你确实想用CMSwareWebAPI来调用，请设置php.ini的max_execution_time 为更大数值，否则会导致运行超时。\r\n<BR><BR>\r\n\r\n\t<table border=1>\r\n<tr>\r\n<td>功能名称</td>\t\r\n<td>o</td>\t\r\n<td>id</td>\t\r\n<td>sub</td>\t\r\n<td>说明</td>\t\r\n\t</tr>\r\n\t\t\r\n<tr>\r\n<td>更新结点首页</td>\t\r\n<td>refreshNodeIndex</td>\t\r\n<td>NodeID[=1,2,3]</td>\t\r\n<td>0或1</td>\t\r\n<td>{$URL_INTERFACE}?password=xxx&o=refreshNodeIndex&id=1&sub=1<br>\r\n更新id为1(id=NodeID)的结点首页，多个id用英文,号分隔，sub为是否包含子结点（包含置1，否则置0）.</td>\r\n</tr>\r\n\r\n<tr>\r\n<td>更新结点附加发布</td>\t\r\n<td>refreshNodeExtra</td>\t\r\n<td>NodeID[=1,2,3]</td>\t\r\n<td>0或1</td>\t\r\n<td>{$URL_INTERFACE}?password=xxx&o=refreshNodeExtra&id=1&sub=1<br>\r\n更新id为1(id=NodeID)的结点附加发布，多个id用英文,号分隔，sub为是否包含子结点（包含置1，否则置0）.</td>\r\n</tr>\r\n\r\n\r\n<tr>\r\n<td>更新结点内容页</td>\t\r\n<td>refreshNodeContent</td>\t\r\n<td>NodeID[=1,2,3]</td>\t\r\n<td>0或1</td>\t\r\n<td>{$URL_INTERFACE}?password=xxx&o=refreshNodeContent&id=1&sub=1<br>\r\n更新id为1(id=NodeID)的结点内容页，多个id用英文,号分隔，sub为是否包含子结点（包含置1，否则置0）.</td>\r\n</tr>\r\n\r\n<tr>\r\n<td>发布结点内容页</td>\t\r\n<td>publishNodeContent</td>\t\r\n<td>NodeID[=1,2,3]</td>\t\r\n<td>0或1</td>\t\r\n<td>{$URL_INTERFACE}?password=xxx&o=publishNodeContent&id=1&sub=1<br>\r\n发布id为1(id=NodeID)的结点内容页，多个id用英文,号分隔，sub为是否包含子结点（包含置1，否则置0）.</td>\r\n</tr>\r\n\r\n\r\n<tr>\r\n<td>更新附加发布</td>\t\r\n<td>refreshExtra</td>\t\r\n<td>PublishID[=1,2,3]</td>\t\r\n<td>-</td>\t\r\n<td>{$URL_INTERFACE}?password=xxx&o=refreshExtra&id=1<br>\r\n发布id为1(id=PublishID)的结点内容页，多个id用英文,号分隔.</td>\r\n</tr>\r\n\r\n<tr>\r\n<td>更新内容页</td>\t\r\n<td>refreshContent</td>\t\r\n<td>IndexID[=1,2,3]</td>\t\r\n<td>-</td>\t\r\n<td>{$URL_INTERFACE}?password=xxx&o=refreshContent&id=1<br>\r\n更新id为1(id=IndexID)的内容页，多个id用英文,号分隔.</td>\r\n</tr>\r\n\r\n<tr>\r\n<td>发布内容页</td>\t\r\n<td>publishContent</td>\t\r\n<td>IndexID[=1,2,3]</td>\t\r\n<td>-</td>\t\r\n<td>{$URL_INTERFACE}?password=xxx&o=publishContent&id=1<br>\r\n发布id为1(id=IndexID)的内容页，多个id用英文,号分隔.</td>\r\n</tr>\r\n\r\n<tr>\r\n<td>启动结点采集</td>\t\r\n<td>collection</td>\t\r\n<td>CateID[=1,2,3]</td>\t\r\n<td>-</td>\t\r\n<td>{$URL_INTERFACE}?password=xxx&o=collection&id=1<br>\r\n启动结点id为1(id=CateID)的采集进程，多个id用英文,号分隔.</td>\r\n</tr>\r\n \r\n \r\n</table>";
require_once( "config.php" );
if ( empty( $_GET['password'] ) )
{
	exit( $HelpInfo );
}
if ( $_GET['password'] != $API_CONFIG['password'] )
{
	exit( "API Password is wrong." );
}
require_once( "common.php" );
$argv[1] = $IN['o'];
$argv[2] = $IN['id'];
$argv[3] = $IN['sub'];
if ( $API_CONFIG['enable_ip_access'] && !in_array( $IN['IP_ADDRESS'], $API_CONFIG['access_ip'] ) )
{
	exit( "Access Denied IP:".$IN['IP_ADDRESS'] );
}
require_once( INCLUDE_PATH."admin/publishAdmin.class.php" );
require_once( INCLUDE_PATH."admin/content_table_admin.class.php" );
require_once( INCLUDE_PATH."admin/tplAdmin.class.php" );
require_once( INCLUDE_PATH."admin/psn_admin.class.php" );
require_once( INCLUDE_PATH."cms.class.php" );
require_once( INCLUDE_PATH."cms.func.php" );
include_once( SETTING_DIR."cms.ini.php" );
require_once( INCLUDE_PATH."encoding/encoding.inc.php" );
require_once( INCLUDE_PATH."admin/psn_admin.class.php" );
require_once( INCLUDE_PATH."admin/plugin.class.php" );
require_once( INCLUDE_PATH."admin/publishAuthAdmin.class.php" );
require_once( INCLUDE_PATH."admin/task.class.php" );
require_once( INCLUDE_PATH."image.class.php" );
require_once( INCLUDE_PATH."admin/extra_publish_admin.class.php" );
$publish = new publishAdmin( );
$extrapublish = new extra_publish_admin( );
cleardir( SYS_PATH."sysdata/cache/", "index.html;.htaccess" );
switch ( $argv[1] )
{
case "refreshNodeIndex" :
	$Task = new Task( );
	if ( strpos( $argv[2], "," ) )
	{
		$NodeID = explode( ",", $argv[2] );
	}
	else
	{
		$NodeID = $argv[2];
	}
	$include_sub = isset( $argv[3] ) ? $argv[3] : 0;
	if ( is_array( $NodeID ) )
	{
		foreach ( $NodeID as $varNodeID )
		{
			$params = array(
				"NodeID" => intval( $varNodeID ),
				"refresh_index" => 1,
				"refresh_content" => 0,
				"refresh_extra" => 0,
				"include_sub" => $include_sub,
				"content_num" => 1
			);
			$TaskData = $Task->refreshSiteInit( $params );
			if ( count( $TaskData ) == 0 )
			{
				print "No task to run.";
			}
			else
			{
				foreach ( $TaskData as $var )
				{
					if ( $var[type] == "index" )
					{
						$IN['NodeID'] = $var[targetId];
						refreshnodeindex( $var[targetId], $publish );
					}
				}
			}
		}
	}
	else
	{
		$params = array(
			"NodeID" => intval( $NodeID ),
			"refresh_index" => 1,
			"refresh_content" => 0,
			"refresh_extra" => 0,
			"include_sub" => $include_sub,
			"content_num" => 1
		);
		$TaskData = $Task->refreshSiteInit( $params );
		if ( count( $TaskData ) == 0 )
		{
			print "No task to run.";
		}
		else
		{
			foreach ( $TaskData as $var )
			{
				if ( $var[type] == "index" )
				{
					$IN['NodeID'] = $var[targetId];
					refreshnodeindex( $var[targetId], $publish );
				}
			}
		}
	}
	break;
case "refreshNodeExtra" :
	$Task = new Task( );
	if ( strpos( $argv[2], "," ) )
	{
		$NodeID = explode( ",", $argv[2] );
	}
	else
	{
		$NodeID = $argv[2];
	}
	$include_sub = isset( $argv[3] ) ? $argv[3] : 0;
	if ( is_array( $NodeID ) )
	{
		foreach ( $NodeID as $varNodeID )
		{
			$params = array(
				"NodeID" => intval( $varNodeID ),
				"refresh_index" => 0,
				"refresh_content" => 0,
				"refresh_extra" => 1,
				"include_sub" => $include_sub,
				"content_num" => 1
			);
			$TaskData = $Task->refreshSiteInit( $params );
			if ( count( $TaskData ) == 0 )
			{
				print "No task to run.";
			}
			else
			{
				foreach ( $TaskData as $var )
				{
					if ( $var[type] == "extra" )
					{
						$IN['NodeID'] = $var[targetId];
						refreshnodeextra( $var[publishId], $var[targetId], $publish );
					}
				}
			}
		}
	}
	else
	{
		$params = array(
			"NodeID" => intval( $NodeID ),
			"refresh_index" => 0,
			"refresh_content" => 0,
			"refresh_extra" => 1,
			"include_sub" => $include_sub,
			"content_num" => 1
		);
		$TaskData = $Task->refreshSiteInit( $params );
		if ( count( $TaskData ) == 0 )
		{
			print "No task to run.";
		}
		else
		{
			foreach ( $TaskData as $var )
			{
				if ( $var[type] == "extra" )
				{
					$IN['NodeID'] = $var[targetId];
					refreshnodeextra( $var[publishId], $var[targetId], $extrapublish );
				}
			}
		}
	}
	break;
case "refreshNodeContent" :
	$Task = new Task( );
	if ( strpos( $argv[2], "," ) )
	{
		$NodeID = explode( ",", $argv[2] );
	}
	else
	{
		$NodeID = $argv[2];
	}
	$include_sub = isset( $argv[3] ) ? $argv[3] : 0;
	$content_num = isset( $argv[4] ) ? $argv[4] : 20;
	if ( is_array( $NodeID ) )
	{
		foreach ( $NodeID as $varNodeID )
		{
			$params = array(
				"NodeID" => intval( $varNodeID ),
				"refresh_index" => 0,
				"refresh_content" => 1,
				"refresh_extra" => 0,
				"include_sub" => $include_sub,
				"content_num" => 1
			);
			$TaskData = $Task->refreshSiteInit( $params );
			if ( count( $TaskData ) == 0 )
			{
				print "No task to run.";
			}
			else
			{
				foreach ( $TaskData as $var )
				{
					if ( $var[type] == "content" )
					{
						$IN['NodeID'] = $var[targetId];
						refreshnodecontent( $var[targetId], $publish, $content_num );
					}
				}
			}
		}
	}
	else
	{
		$params = array(
			"NodeID" => intval( $NodeID ),
			"refresh_index" => 0,
			"refresh_content" => 1,
			"refresh_extra" => 0,
			"include_sub" => $include_sub,
			"content_num" => 1
		);
		$TaskData = $Task->refreshSiteInit( $params );
		if ( count( $TaskData ) == 0 )
		{
			print "No task to run.";
		}
		else
		{
			foreach ( $TaskData as $var )
			{
				if ( $var[type] == "content" )
				{
					$IN['NodeID'] = $var[targetId];
					refreshnodecontent( $var[targetId], $publish, $content_num );
				}
			}
		}
	}
	break;
case "publishNodeContent" :
	$Task = new Task( );
	if ( strpos( $argv[2], "," ) )
	{
		$NodeID = explode( ",", $argv[2] );
	}
	else
	{
		$NodeID = $argv[2];
	}
	$include_sub = isset( $argv[3] ) ? $argv[3] : 0;
	if ( is_array( $NodeID ) )
	{
		foreach ( $NodeID as $varNodeID )
		{
			$params = array(
				"NodeID" => intval( $varNodeID ),
				"refresh_index" => 1,
				"refresh_content" => 0,
				"refresh_extra" => 0,
				"include_sub" => $include_sub,
				"content_num" => 1
			);
			$TaskData = $Task->refreshSiteInit( $params );
			if ( count( $TaskData ) == 0 )
			{
				print "No task to run.";
			}
			else
			{
				foreach ( $TaskData as $var )
				{
					if ( $var[type] == "index" )
					{
						$IN['NodeID'] = $var[targetId];
						publishnodecontent( $var[targetId], &$publish, $content_num = 20 );
					}
				}
			}
		}
	}
	else
	{
		$params = array(
			"NodeID" => intval( $NodeID ),
			"refresh_index" => 1,
			"refresh_content" => 0,
			"refresh_extra" => 0,
			"include_sub" => $include_sub,
			"content_num" => 1
		);
		$TaskData = $Task->refreshSiteInit( $params );
		if ( count( $TaskData ) == 0 )
		{
			print "No task to run.";
		}
		else
		{
			foreach ( $TaskData as $var )
			{
				if ( $var[type] == "index" )
				{
					$IN['NodeID'] = $var[targetId];
					publishnodecontent( $var[targetId], &$publish, $content_num = 20 );
				}
			}
		}
	}
	break;
case "refreshExtra" :
	if ( empty( $argv[2] ) )
	{
		exit( "Error: PublishID is null" );
	}
	if ( strpos( $argv[2], "," ) )
	{
		$PublishID = explode( ",", $argv[2] );
	}
	else
	{
		$PublishID = $argv[2];
	}
	if ( is_array( $PublishID ) )
	{
		foreach ( $PublishID as $varPublishID )
		{
			$PublishInfo = $extrapublish->getInfo( $varPublishID );
			refreshnodeextra( $varPublishID, $PublishInfo['NodeID'], $extrapublish );
		}
	}
	else
	{
		$PublishInfo = $extrapublish->getInfo( $PublishID );
		refreshnodeextra( $PublishID, $PublishInfo['NodeID'], $extrapublish );
	}
	break;
case "publishContent" :
	if ( empty( $argv[2] ) )
	{
		exit( "Error: IndexID is null" );
	}
	if ( strpos( $argv[2], "," ) )
	{
		$IndexID = explode( ",", $argv[2] );
	}
	else
	{
		$IndexID = $argv[2];
	}
	if ( is_array( $IndexID ) )
	{
		foreach ( $IndexID as $varIndexID )
		{
			$NodeInfo = $iWPC->loadNodeInfo( $publish->getIndexInfo( $varIndexID, "NodeID" ) );
			ob_start( );
			$publish->publish( $varIndexID );
			$contents = ob_get_contents( );
			ob_end_clean( );
			output( "[".$NodeInfo['Name']."] ".$contents );
		}
	}
	else
	{
		$NodeInfo = $iWPC->loadNodeInfo( $publish->getIndexInfo( $IndexID, "NodeID" ) );
		ob_start( );
		$publish->publish( $IndexID );
		$contents = ob_get_contents( );
		ob_end_clean( );
		output( "[".$NodeInfo['Name']."] ".$contents );
	}
	break;
case "refreshContent" :
	if ( empty( $argv[2] ) )
	{
		exit( "Error: IndexID is null" );
	}
	if ( strpos( $argv[2], "," ) )
	{
		$IndexID = explode( ",", $argv[2] );
	}
	else
	{
		$IndexID = $argv[2];
	}
	if ( is_array( $IndexID ) )
	{
		foreach ( $IndexID as $varIndexID )
		{
			$NodeInfo = $iWPC->loadNodeInfo( $publish->getIndexInfo( $varIndexID, "NodeID" ) );
			ob_start( );
			$publish->refresh( $varIndexID );
			$contents = ob_get_contents( );
			ob_end_clean( );
			output( "[".$NodeInfo['Name']."] ".$contents );
		}
	}
	else
	{
		$NodeInfo = $iWPC->loadNodeInfo( $publish->getIndexInfo( $IndexID, "NodeID" ) );
		ob_start( );
		$publish->refresh( $IndexID );
		$contents = ob_get_contents( );
		ob_end_clean( );
		output( "[".$NodeInfo['Name']."] ".$contents );
	}
	break;
case "collection" :
	if ( strpos( $argv[2], "," ) )
	{
		$CateID = explode( ",", $argv[2] );
	}
	else
	{
		$CateID = $argv[2];
	}
	$include_sub = isset( $argv[3] ) ? $argv[3] : 0;
	include( dirname( __FILE__ )."/collection.php" );
	if ( is_array( $CateID ) )
	{
		foreach ( $CateID as $varCateID )
		{
			$CateInfo = collection_cate_admin::getcateinfo( $varCateID );
			include( dirname( __FILE__ )."/collectionLogic.php" );
		}
	}
	else if ( $CateID == 0 && $include_sub == 1 )
	{
		$sql = "SELECT * FROM {$table->collection_cate} where InRunPlan=1 AND Disabled=0";
		$resultCateList = $db->Execute( $sql );
		while ( !$resultCateList->EOF )
		{
			$CateInfo = $resultCateList->fields;
			$CateID = $resultCateList->fields['CateID'];
			include( dirname( __FILE__ )."/collectionLogic.php" );
			$resultCateList->MoveNext( );
		}
	}
	else
	{
		$CateInfo = collection_cate_admin::getcateinfo( $CateID );
		include( dirname( __FILE__ )."/collectionLogic.php" );
	}
	break;
case "h" :
	exit( $HelpInfo );
	break;
case "v" :
default :
	exit( $VersionInfo );
	break;
}
?>
