<?php
require_once( "common.php" );
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

switch ( $IN[o] )
{
case "stopFirstPublish" :
	if ( file_exists( "../sysdata/.install" ) )
	{
		unlink( "../sysdata/.install" );
	}
	break;
case "refreshSite" :
	$Task = new Task( );
	if ( $IN[mode] == "running" )
	{
		$TaskData = $Task->taskSessionGet( $IN[TaskID] );
		$TaskNum = count( $TaskData );
		if ( $TaskNum == 0 )
		{
			$Task->taskSessionEnd( $IN[TaskID] );
			Task::addtaskinfo( $_LANG_ADMIN['admin_task_refreshSite_finish'], $IN[TaskID] );
			Task::endtask( $IN[TaskID] );
		}
		else
		{
			$current_task = array_shift( $TaskData );
			if ( $current_task[type] == "index" )
			{
				Task::refreshindex( $current_task[targetId] );
			}
			else if ( $current_task[type] == "content" )
			{
				Task::refreshcontent( $current_task[targetId], $IN['content_num'] );
			}
			else if ( $current_task[type] == "extra" )
			{
				Task::refreshextra( $current_task[publishId], $current_task[targetId] );
			}
			$TaskData = serialize( $TaskData );
			$Task->flushData( );
			$Task->addData( "TaskData", $TaskData );
			$Task->addData( "TaskTime", time( ) );
			$Task->taskSessionUpdate( $IN[TaskID] );
			showmessage( "index_refresh_ok_refreshpage", $base_url."o=refreshSite&mode=running&TaskID=".$IN['TaskID']."&content_num=".$IN['content_num'], rand( 0, 5 ) );
		}
	}
	else
	{
		cleardir( SYS_PATH."sysdata/cache/", "index.html;.htaccess" );
		$params = array(
			"NodeID" => intval( $IN['NodeID'] ),
			"refresh_index" => intval( $IN['refresh_index'] ),
			"refresh_content" => intval( $IN['refresh_content'] ),
			"refresh_extra" => intval( $IN['refresh_extra'] ),
			"include_sub" => intval( $IN['include_sub'] ),
			"content_num" => intval( $IN['content_num'] )
		);
		$Refresh_Task = $Task->refreshSiteInit( $params );
		$TaskID = Auth::makesessionkey( );
		$TaskData = serialize( $Refresh_Task );
		$Task->flushData( );
		$Task->addData( "TaskID", $TaskID );
		$Task->addData( "TaskData", $TaskData );
		$Task->addData( "TaskTime", time( ) );
		if ( $Task->taskSessionAdd( ) )
		{
			exit( $TaskID );
		}
		else
		{
			exit( "0" );
		}
	}
	break;
case "unpublishSite" :
	Task::publishcontent( $current_task[targetId], $IN['content_num'] );
	break;
case "publishSite" :
	$Task = new Task( );
	if ( $IN[mode] == "running" )
	{
		$TaskData = $Task->taskSessionGet( $IN[TaskID] );
		$TaskNum = count( $TaskData );
		if ( $TaskNum == 0 )
		{
			if ( file_exists( "../sysdata/.refresh" ) )
			{
				unlink( "../sysdata/.refresh" );
				$data = "<SCRIPT src=\"../html/functions.js\" type=text/javascript></SCRIPT>\r\n\t<script>\r\n\tvar returnValue = CMSware_send(\"admin_task.php?sId={$IN[sId]}&o=refreshSite&NodeID=0&refresh_index=1&refresh_content=1&refresh_extra=1&include_sub=1&content_num=10\");\r\n\t\t\t\t\r\n\t//alert(returnValue);\r\n\ttop.CrawlerTaskFrame.addThread(\"admin_task.php?sId={$IN[sId]}&o=refreshSite&mode=running&TaskID=\"+returnValue+\"&NodeID=0&content_num=10\", returnValue);\r\n\r\n\ttop.TaskInfoFrame.addInfo('restart refresh site...', returnValue)\t\t\r\n\t</script>";
				echo $data;
			}
			$Task->taskSessionEnd( $IN[TaskID] );
			Task::addtaskinfo( $_LANG_ADMIN['admin_task_publishSite_finish'], $IN[TaskID] );
			Task::endtask( $IN[TaskID] );
		}
		else
		{
			$current_task = array_shift( $TaskData );
			if ( $current_task[type] == "content" )
			{
				Task::publishcontent( $current_task[targetId], $IN['content_num'] );
			}
			$TaskData = serialize( $TaskData );
			$Task->flushData( );
			$Task->addData( "TaskData", $TaskData );
			$Task->addData( "TaskTime", time( ) );
			$Task->taskSessionUpdate( $IN[TaskID] );
			showmessage( "index_publish_ok_publishpage", $base_url."o=publishSite&mode=running&TaskID=".$IN['TaskID']."&content_num=".$IN['content_num'], rand( 0, 5 ) );
		}
	}
	else
	{
		cleardir( SYS_PATH."sysdata/cache/", "index.html;.htaccess" );
		if ( file_exists( "../sysdata/.install" ) )
		{
			unlink( "../sysdata/.install" );
			$handle = fopen( "../sysdata/.refresh", "w" );
			@flock( $handle, 3 );
			fwrite( $handle, "lock the installer" );
			fclose( $handle );
		}
		$params = array(
			"NodeID" => intval( $IN['NodeID'] ),
			"include_sub" => intval( $IN['include_sub'] ),
			"content_num" => intval( $IN['content_num'] )
		);
		$Refresh_Task = $Task->publishSiteInit( $params );
		$TaskID = Auth::makesessionkey( );
		$TaskData = serialize( $Refresh_Task );
		$Task->flushData( );
		$Task->addData( "TaskID", $TaskID );
		$Task->addData( "TaskData", $TaskData );
		$Task->addData( "TaskTime", time( ) );
		if ( $Task->taskSessionAdd( ) )
		{
			exit( $TaskID );
		}
		else
		{
			exit( "0" );
		}
	}
	break;
case "refreshIndex" :
	$publish = new publishAdmin( );
	$publish->NodeInfo = $iWPC->loadNodeInfo( $IN[NodeID] );
	switch ( $publish->NodeInfo[PublishMode] )
	{
	case "0" :
		Task::endtask( $IN[TaskID] );
		exit( );
		break;
	case "2" :
	case "3" :
		$publish->refreshIndex( $IN[NodeID], "", "" );
		Task::endtask( $IN[TaskID] );
		exit( );
		break;
	}
	if ( $IN[pageRefresh] == "yes" )
	{
		$tplname = $publish->NodeInfo[IndexTpl];
		$filename = $publish->NodeInfo[IndexName];
		$filename = str_replace( "{NodeID}", $publish->NodeInfo['NodeID'], $filename );
		foreach ( $publish->NodeInfo as $key => $var )
		{
			$filename = str_replace( "{".$key."}", $var, $filename );
		}
		if ( preg_match( "/\\{(.*)\\}/isU", $filename, $match ) )
		{
			eval( "\$fun_string = {$match['1']};" );
			$filename = str_replace( $match[0], $fun_string, $filename );
		}
		$SYS_ENV[tpl_pagelist][page] = $IN[page];
		$SYS_ENV[tpl_pagelist][filename] = $filename;
		$filename = preg_replace( "/\\.([A-Za-z0-9]+)\$/isU", "_".$IN[page].".\\1", $filename );
		$filename = formatpublishfile( $filename );
		if ( $publish->refreshIndex( $IN[NodeID], $tplname, $filename ) )
		{
			if ( $SYS_ENV[tpl_pagelist][run] == "yes" )
			{
				Task::addtaskinfo( sprintf( $_LANG_ADMIN['admin_task_refreshIndex_continue'], $filename, $SYS_ENV[tpl_pagelist][page], $publish->NodeInfo[Name] ), $IN[TaskID] );
				showmessage( "index_refresh_ok_refreshpage", $base_url."o=refreshIndex&NodeID=".$IN[NodeID]."&pageRefresh=yes&page=".$SYS_ENV[tpl_pagelist][page]."&extra=".$extra."&TaskID=".$IN[TaskID], rand( 0, 5 ) );
			}
			else
			{
				Task::addtaskinfo( sprintf( $_LANG_ADMIN['admin_task_refreshIndex_finish'], $filename, $publish->NodeInfo[Name] ), $IN[TaskID] );
				Task::endtask( $IN[TaskID] );
			}
		}
	}
	else
	{
		$tplname = $publish->NodeInfo['IndexTpl'];
		$filename = $publish->NodeInfo['IndexName'];
		$filename = str_replace( "{NodeID}", $publish->NodeInfo['NodeID'], $filename );
		foreach ( $publish->NodeInfo as $key => $var )
		{
			$filename = str_replace( "{".$key."}", $var, $filename );
		}
		if ( preg_match( "/\\{(.*)\\}/isU", $filename, $match ) )
		{
			eval( "\$fun_string = {$match['1']};" );
			$filename = str_replace( $match[0], $fun_string, $filename );
		}
		$filename = formatpublishfile( $filename );
		$SYS_ENV[tpl_pagelist][filename] = $filename;
		if ( $publish->refreshIndex( $IN['NodeID'], $tplname, $filename ) )
		{
			if ( $SYS_ENV[tpl_pagelist][run] == "yes" )
			{
				Task::addtaskinfo( sprintf( $_LANG_ADMIN['admin_task_refreshIndex_start'], $filename, $publish->NodeInfo['Name'] ), $IN['TaskID'] );
				showmessage( "index_refresh_ok_goto_refreshpage", $base_url."o=refreshIndex&NodeID=".$IN[NodeID]."&pageRefresh=yes&page=".$SYS_ENV[tpl_pagelist][page]."&extra=".$extra."&TaskID=".$IN[TaskID], rand( 0, 5 ) );
			}
			else
			{
				Task::addtaskinfo( sprintf( $_LANG_ADMIN['admin_task_refreshIndex_finish'], $filename, $publish->NodeInfo['Name'] ), $IN['TaskID'] );
				Task::endtask( $IN[TaskID] );
			}
		}
		else
		{
			Task::addtaskinfo( sprintf( $_LANG_ADMIN['admin_task_refreshIndex_fail'], $filename, $publish->NodeInfo['Name'] ), $IN['TaskID'] );
			Task::endtask( $IN[TaskID] );
		}
	}
	break;
case "refreshExtra" :
	require_once( INCLUDE_PATH."admin/extra_publish_admin.class.php" );
	$extrapublish = new extra_publish_admin( );
	$NodeInfo = $iWPC->loadNodeInfo( $IN[NodeID] );
	$PublishInfo = $extrapublish->getInfo( $IN[PublishID] );
	if ( $IN[pageRefresh] == "yes" )
	{
		$filename = $PublishInfo[PublishFileName];
		$filename = str_replace( "{NodeID}", $NodeInfo['NodeID'], $filename );
		$filename = str_replace( "{PublishID}", $PublishInfo['PublishID'], $filename );
		$SYS_ENV[tpl_pagelist][page] = $IN[page];
		$SYS_ENV[tpl_pagelist][filename] = $filename;
		if ( preg_match( "/\\{Page\\}/isU", $filename ) )
		{
			$filename = str_replace( "{Page}", $IN[page], $filename );
		}
		else
		{
			$filename = preg_replace( "/\\.([A-Za-z0-9]+)\$/isU", "_".$IN[page].".\\1", $filename );
		}
		if ( $extrapublish->refresh( $PublishInfo, $NodeInfo, $filename ) )
		{
			if ( $SYS_ENV[tpl_pagelist][run] == "yes" )
			{
				Task::addtaskinfo( sprintf( $_LANG_ADMIN['admin_task_refreshExtra_continue'], $PublishInfo['PublishName'], $filename, $SYS_ENV[tpl_pagelist][page], $NodeInfo[Name] ), $IN[TaskID] );
				showmessage( "index_refresh_ok_refreshpage", $base_url."o=refreshExtra&PublishID=".$IN[PublishID]."&NodeID=".$IN[NodeID]."&pageRefresh=yes&page=".$SYS_ENV[tpl_pagelist][page]."&extra=".$extra."&TaskID=".$IN[TaskID], rand( 0, 5 ) );
			}
			else
			{
				Task::addtaskinfo( sprintf( $_LANG_ADMIN['admin_task_refreshExtra_finished'], $PublishInfo['PublishName'], $filename, $NodeInfo[Name] ), $IN[TaskID] );
				Task::endtask( $IN[TaskID] );
			}
		}
	}
	else
	{
		$filename = $PublishInfo[PublishFileName];
		$filename = str_replace( "{NodeID}", $NodeInfo['NodeID'], $filename );
		$filename = str_replace( "{PublishID}", $PublishInfo['PublishID'], $filename );
		if ( preg_match( "/\\{Page\\}/isU", $filename ) )
		{
			$filename = str_replace( "{Page}", "", $filename );
		}
		$SYS_ENV[tpl_pagelist][filename] = $filename;
		if ( $extrapublish->refresh( $PublishInfo, $NodeInfo, $filename ) )
		{
			if ( $SYS_ENV[tpl_pagelist][run] == "yes" )
			{
				Task::addtaskinfo( sprintf( $_LANG_ADMIN['admin_task_refreshExtra_start'], $PublishInfo['PublishName'], $filename, $NodeInfo[Name] ), $IN[TaskID] );
				showmessage( "index_refresh_ok_goto_refreshpage", $base_url."o=refreshExtra&PublishID=".$IN[PublishID]."&NodeID=".$IN[NodeID]."&pageRefresh=yes&page=".$SYS_ENV[tpl_pagelist][page]."&extra=".$extra."&TaskID=".$IN[TaskID], rand( 0, 5 ) );
			}
			else
			{
				Task::addtaskinfo( sprintf( $_LANG_ADMIN['admin_task_refreshExtra_finished'], $PublishInfo['PublishName'], $filename, $NodeInfo[Name] ), $IN[TaskID] );
				Task::endtask( $IN[TaskID] );
			}
		}
		else
		{
			Task::addtaskinfo( sprintf( $_LANG_ADMIN['admin_task_refreshExtra_fail'], $PublishInfo['PublishName'], $filename, $NodeInfo[Name] ), $IN[TaskID] );
			Task::endtask( $IN[TaskID] );
		}
	}
	break;
case "refreshContent" :
	$publish = new publishAdmin( );
	$publish->NodeInfo = $iWPC->loadNodeInfo( $IN[NodeID] );
	if ( empty( $IN[Page] ) )
	{
		$Page = 0;
	}
	else
	{
		$Page = $IN[Page];
	}
	$start = $Page * $IN[offset];
	$IndexIDs = $publish->getPublishLimit( $IN[NodeID], $start, $IN[offset] );
	$count = count( $IndexIDs );
	foreach ( $IndexIDs as $var )
	{
		$publish->refresh( $var[IndexID] );
	}
	++$Page;
	if ( $count < $IN[offset] )
	{
		Task::addtaskinfo( sprintf( $_LANG_ADMIN['admin_task_refreshContent_continue'], $start, $start + $count, $publish->NodeInfo[Name] ), $IN[TaskID] );
		Task::addtaskinfo( sprintf( $_LANG_ADMIN['admin_task_refreshContent_finish'], $publish->NodeInfo[Name] ), $IN[TaskID] );
		Task::endtask( $IN[TaskID] );
	}
	else
	{
		Task::addtaskinfo( sprintf( $_LANG_ADMIN['admin_task_refreshContent_continue'], $start, $start + $count, $publish->NodeInfo[Name] ), $IN[TaskID] );
		showmessage( "index_refresh_ok_refreshpage", $base_url."o=refreshContent&NodeID=".$IN[NodeID]."&Page=".$Page."&offset=".$IN[offset]."&TaskID=".$IN[TaskID], rand( 0, 5 ) );
	}
	break;
case "publishContent" :
	$publish = new publishAdmin( );
	$publish->NodeInfo = $iWPC->loadNodeInfo( $IN[NodeID] );
	if ( empty( $IN[Page] ) )
	{
		$Page = 0;
	}
	else
	{
		$Page = $IN[Page];
	}
	$start = 0;
	$IndexIDs = $publish->getUnPublishLimit( $IN[NodeID], $start, $IN[offset] );
	$count = count( $IndexIDs );
	foreach ( $IndexIDs as $key => $var )
	{
		$publish->publish( $var[IndexID] );
		if ( $key == 0 )
		{
			$publishIndexs = $var[IndexID];
		}
		else
		{
			$publishIndexs .= ",".$var[IndexID];
		}
	}
	++$Page;
	if ( $count < $IN[offset] )
	{
		Task::addtaskinfo( sprintf( $_LANG_ADMIN['admin_task_publishContent_continue'], $publishIndexs, $publish->NodeInfo[Name] ), $IN[TaskID] );
		Task::addtaskinfo( "Publishing content at ".$publish->NodeInfo[Name]." finished.", $IN[TaskID] );
		Task::endtask( $IN[TaskID] );
	}
	else
	{
		Task::addtaskinfo( sprintf( $_LANG_ADMIN['admin_task_publishContent_continue'], $publishIndexs, $publish->NodeInfo[Name] ), $IN[TaskID] );
		showmessage( "index_publish_ok_publish", $base_url."o=publishContent&NodeID=".$IN[NodeID]."&Page=".$Page."&offset=".$IN[offset]."&TaskID=".$IN[TaskID], rand( 0, 5 ) );
	}
	break;
case "rePublishContent_publish" :
	$publish = new publishAdmin( );
	$publish->NodeInfo = $iWPC->loadNodeInfo( $IN[NodeID] );
	if ( empty( $IN[Page] ) )
	{
		$Page = 0;
	}
	else
	{
		$Page = $IN[Page];
	}
	$IN[offset] = empty( $IN[offset] ) ? 20 : $IN[offset];
	$start = 0;
	$IndexIDs = $publish->getUnPublishLimit( $IN[NodeID], $start, $IN[offset] );
	$count = count( $IndexIDs );
	foreach ( $IndexIDs as $key => $var )
	{
		$publish->publish( $var[IndexID] );
		if ( $key == 0 )
		{
			$publishIndexs = $var[IndexID];
		}
		else
		{
			$publishIndexs .= ",".$var[IndexID];
		}
	}
	++$Page;
	if ( $count < $IN[offset] )
	{
		showmsg( sprintf( $_LANG_ADMIN['admin_task_rePublishContent_publish_finish'], $publishIndexs, $publish->NodeInfo[Name] ), $IN[referer], 1 );
	}
	else
	{
		showmsg( sprintf( $_LANG_ADMIN['admin_task_rePublishContent_publish_continue'], $publishIndexs, $publish->NodeInfo[Name] ), $base_url."o=rePublishContent_publish&NodeID=".$IN[NodeID]."&Page=".$Page."&offset=".$IN[offset]."&referer=".urlencode( $IN[referer] ), 1 );
	}
	break;
case "rePublishContent_unpublish" :
	$publish = new publishAdmin( );
	$publish->NodeInfo = $iWPC->loadNodeInfo( $IN[NodeID] );
	if ( empty( $IN[Page] ) )
	{
		$Page = 0;
	}
	else
	{
		$Page = $IN[Page];
	}
	
	$IN[offset] = empty( $IN[offset] ) ? 20 : $IN['offset'];
	$start = 0;
	$IndexIDs = $publish->getPublishLimit( $IN['NodeID'], $start, $IN['offset'] );
	$count = count( $IndexIDs );
	$IN['unpublishmode'] = empty( $IN['unpublishmode'] ) ? 0 : 1;
	foreach ( $IndexIDs as $key => $var )
	{
		$publish->unpublish( $var['IndexID'], $IN['unpublishmode'] );
		if ( $key == 0 )
		{
			$publishIndexs = $var['IndexID'];
		}
		else
		{
			$publishIndexs .= ",".$var['IndexID'];
		}
	}
	++$Page;
	if ( $count < $IN[offset] )
	{
		if ( $IN['type'] == "nopublish" )
		{
			showmsg( sprintf( $_LANG_ADMIN['admin_task_rePublishContent_unpublish_finish_nopublish'], $publishIndexs, $publish->NodeInfo['Name'] ), $IN[referer], 1 );
		}
		else
		{
			showmsg( sprintf( $_LANG_ADMIN['admin_task_rePublishContent_unpublish_finish'], $publishIndexs, $publish->NodeInfo['Name'] ), $base_url."o=rePublishContent_publish&NodeID=".$IN['NodeID']."&Page=".$Page."&offset=".$IN['offset']."&referer=".urlencode( $IN['referer'] ), 1 );
		}
	}
	else
	{
		showmsg( sprintf( $_LANG_ADMIN['admin_task_rePublishContent_unpublish_continue'], $publishIndexs, $publish->NodeInfo['Name'] ), $base_url."o=rePublishContent_unpublish&NodeID=".$IN['NodeID']."&Page=".$Page."&offset=".$IN[offset]."&referer=".urlencode( $IN['referer'] )."&type=".$IN['type']."&unpublishmode=".$IN['unpublishmode'], 1 );
	}
	break;
}
?>
