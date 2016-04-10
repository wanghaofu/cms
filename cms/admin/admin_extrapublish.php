<?php
require_once( "common.php" );
require_once( INCLUDE_PATH."admin/publishAdmin.class.php" );
require_once( INCLUDE_PATH."admin/content_table_admin.class.php" );
require_once( INCLUDE_PATH."admin/tplAdmin.class.php" );
require_once( INCLUDE_PATH."admin/psn_admin.class.php" );
require_once( INCLUDE_PATH."cms.class.php" );
require_once( INCLUDE_PATH."cms.func.php" );
require_once( INCLUDE_PATH."admin/extra_publish_admin.class.php" );
require_once( INCLUDE_PATH."admin/site_admin.class.php" );
$publish = new publishAdmin( );
$extrapublish = new extra_publish_admin( );
$site = new site_admin( );
if ( empty( $IN[NodeID] ) )
{
	goback( "error_NodeID_null" );
}
$publish->NodeInfo = $iWPC->loadNodeInfo( $IN[NodeID] );
// de($publish->NodeInfo);
// if ( !$site->canAccess( $publish->NodeInfo, "Manage" ) )
// {
// 	goback( sprintf( $_LANG_ADMIN['site_permission_deny_manage'], $publish->NodeInfo['Name'] ), 1 );
// }

// if ( !$site->canAccess( $publish->NodeInfo, "Read" ) )
// {
//     goback( sprintf( $_LANG_ADMIN['site_permission_deny_manage'], $publish->NodeInfo['Name'] ), 1 );
// }

switch ( $IN[o] )
{
case "list" :
    if ( !$site->canAccess( $publish->NodeInfo, "Read" ) )
    {
        goback( sprintf( $_LANG_ADMIN['site_permission_deny_manage'], $publish->NodeInfo['Name'] ), 1 );
    }
	$TPL->assign( "list", $extrapublish->getAll( $IN[NodeID] ) );
	$TPL->assign_by_ref( "NodeInfo", $publish->NodeInfo );
	$TPL->display( "extrapublish_list.html" );
	break;
case "view" :
    if ( !$site->canAccess( $publish->NodeInfo, "Read" ) )
    {
        goback( sprintf( $_LANG_ADMIN['site_permission_deny_manage'], $publish->NodeInfo['Name'] ), 1 );
    }
	header( "Location: ".$extrapublish->getView( $IN[PublishID] ) );
	break;
case "add" :
    if ( !$site->canAccess( $publish->NodeInfo, "Manage" ) )
    {
        goback( sprintf( $_LANG_ADMIN['site_permission_deny_manage'], $publish->NodeInfo['Name'] ), 1 );
    }
	$TPL->assign_by_ref( "NodeInfo", $publish->NodeInfo );
	$TPL->display( "extrapublish_add.html" );
	break;
case "add_submit" :
    if ( !$site->canAccess( $publish->NodeInfo, "Manage" ) )
    {
        goback( sprintf( $_LANG_ADMIN['site_permission_deny_manage'], $publish->NodeInfo['Name'] ), 1 );
    }
	$extrapublish->flushData( );
	$extrapublish->filterData( $IN );
	$extrapublish->addData( "NodeID", $IN[NodeID] );
	if ( $extrapublish->add( ) )
	{
		showmessage( "add_extra_publish_ok", $referer );
	}
	else
	{
		showmessage( "add_extra_publish_fail", $referer );
	}
	break;
case "edit" :
    if ( !$site->canAccess( $publish->NodeInfo, "Manage" ) )
    {
        goback( sprintf( $_LANG_ADMIN['site_permission_deny_manage'], $publish->NodeInfo['Name'] ), 1 );
    }
	$TPL->assign_by_ref( "NodeInfo", $publish->NodeInfo );
	$TPL->assign( "PublishInfo", $extrapublish->getInfo( $IN[PublishID] ) );
	$TPL->display( "extrapublish_edit.html" );
	break;
case "edit_submit" :
    if ( !$site->canAccess( $publish->NodeInfo, "Manage" ) )
    {
        goback( sprintf( $_LANG_ADMIN['site_permission_deny_manage'], $publish->NodeInfo['Name'] ), 1 );
    }
	if ( empty( $IN[PublishID] ) )
	{
		_goto( "list" );
	}
	$extrapublish->flushData( );
	$extrapublish->filterData( $IN );
	if ( $extrapublish->update( $IN[PublishID] ) )
	{
		showmessage( "edit_extra_publish_ok", $referer );
	}
	else
	{
		showmessage( "edit_extra_publish_fail", $referer );
	}
	break;
case "del" :
    if ( !$site->canAccess( $publish->NodeInfo, "Write" ) )
    {
        goback( sprintf( $_LANG_ADMIN['site_permission_deny_write'], $publish->NodeInfo['Name'] ), 1 );
    }
	if ( !empty( $IN[PublishID] ) && $extrapublish->del( $IN[PublishID] ) )
	{
		showmessage( "del_extra_publish_ok", $referer );
	}
	else
	{
		showmessage( "del_extra_publish_fail", $referer );
	}
	break;
case "refreshIndex" :
    if ( !$site->canAccess( $publish->NodeInfo, "Manage" ) )
    {
        goback( sprintf( $_LANG_ADMIN['site_permission_deny_manage'], $publish->NodeInfo['Name'] ), 1 );
    }
	if ( $IN[pageRefresh] == "yes" )
	{
		$tplname = $publish->NodeInfo[IndexTpl];
		$filename = $publish->NodeInfo[IndexName];
		$filename = str_replace( "{NodeID}", $publish->NodeInfo['NodeID'], $filename );
		if ( preg_match( "/\\{(.*)\\}/isU", $filename, $match ) )
		{
			eval( "\$fun_string = {$match['1']};" );
			$filename = str_replace( $match[0], $fun_string, $filename );
		}
		$SYS_ENV[tpl_pagelist][page] = $IN[page];
		$SYS_ENV[tpl_pagelist][filename] = $filename;
		$filename = str_replace( ".", "_{$IN[page]}.", $filename );
		if ( $publish->refreshIndex( $IN[NodeID], $tplname, $filename ) )
		{
			if ( $SYS_ENV[tpl_pagelist][run] == "yes" )
			{
				showmessage( "index_refresh_ok_refreshpage", $base_url."o=refreshIndex&NodeID={$IN[NodeID]}&pageRefresh=yes&page={$SYS_ENV[tpl_pagelist][page]}&extra={$extra}" );
			}
			else
			{
				showmessage( "index_refresh_ok", $base_url."o=viewIndex&NodeID={$IN[NodeID]}" );
			}
		}
	}
	else
	{
		$tplname = $publish->NodeInfo[IndexTpl];
		$filename = $publish->NodeInfo[IndexName];
		$filename = str_replace( "{NodeID}", $publish->NodeInfo['NodeID'], $filename );
		if ( preg_match( "/\\{(.*)\\}/isU", $filename, $match ) )
		{
			eval( "\$fun_string = {$match['1']};" );
			$filename = str_replace( $match[0], $fun_string, $filename );
		}
		$SYS_ENV[tpl_pagelist][filename] = $filename;
		if ( $publish->refreshIndex( $IN[NodeID], $tplname, $filename ) )
		{
			if ( $SYS_ENV[tpl_pagelist][run] == "yes" )
			{
				showmessage( "index_refresh_ok_goto_refreshpage", $base_url."o=refreshIndex&NodeID={$IN[NodeID]}&pageRefresh=yes&page={$SYS_ENV[tpl_pagelist][page]}&extra={$extra}" );
			}
			else
			{
				showmessage( "index_refresh_ok", $base_url."o=viewIndex&NodeID={$IN[NodeID]}" );
			}
		}
		else
		{
			showmessage( "index_refresh_fail", $base_url."o=viewIndex&NodeID={$IN[NodeID]}" );
		}
	}
	break;
case "viewIndex" :
    if ( !$site->canAccess( $publish->NodeInfo, "Read" ) )
    {
        goback( sprintf( $_LANG_ADMIN['site_permission_deny_read'], $publish->NodeInfo['Name'] ), 1 );
    }
	switch ( $publish->NodeInfo['PublishMode'] )
	{
	case "0" :
		return TRUE;
		break;
	case "1" :
		$url = str_replace( "{NodeID}", $publish->NodeInfo['NodeID'], $publish->NodeInfo['IndexName'] );
		if ( preg_match( "/\\{(.*)\\}/isU", $url, $match ) )
		{
			eval( "\$fun_string = {$match['1']};" );
			$url = str_replace( $match[0], $fun_string, $url );
		}
		$location = $publish->getHtmlURL( $url );
		header( "Location: {$location} " );
		break;
	case "2" :
	case "3" :
		$url = str_replace( "{NodeID}", $publish->NodeInfo['NodeID'], $publish->NodeInfo['IndexPortalURL'] );
		$url = str_replace( "{Page}", 0, $url );
		header( "Location: {$url} " );
		break;
	}
}
?>
