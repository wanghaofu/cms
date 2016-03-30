<?php


if ( !defined( "IN_CMSWARE" ) )
{
	exit( "Access Denied" );
}
require( "../license.php" );
$license_array = $License;
unset( $License );
if ( $license_array['Module-bbsInterface'] != 1 )
{
	goback( "plugin_deny" );
}
require_once( PLUGIN_PATH."include/setting.class.php" );
$_SETTING = PluginSetting::getinfo( );
if ( !empty( $_SETTING['BBS'] ) )
{
	require_once( PLUGIN_PATH."bbs/".$_SETTING['BBS']."/bbs.config.php" );
	require_once( PLUGIN_PATH."include/access_user.class.php" );
	$AccessUser = new AccessUser( );
}
switch ( $action )
{
case "view" :
	if ( isset( $AccessUser ) )
	{
		$offset = empty( $IN['offset'] ) ? 15 : $IN['offset'];
		$num = $AccessUser->getRecordNum( );
		$pagenum = ceil( $num / $offset );
		if ( empty( $IN[Page] ) )
		{
			$Page = 1;
		}
		else
		{
			$Page = $IN[Page];
		}
		$start = ( $Page - 1 ) * $offset;
		$recordInfo[currentPage] = $Page;
		$recordInfo[pageNum] = $pagenum;
		$recordInfo[recordNum] = $num;
		$recordInfo[offset] = $offset;
		$recordInfo[from] = $start;
		$recordInfo[to] = $start + $offset;
		$TPL->assign( "uList", $AccessUser->getRecordLimit( $start, $offset ) );
	}
	$TPL->assign( "offset", $offset );
	$TPL->assign( "pagelist", pagelist( $pagenum, $Page, "{$base_url}o=access_user::view&offset={$offset}", "#000000" ) );
	$TPL->display( "access_user_view.html" );
	break;
case "add" :
	if ( !$AccessUser->userExists( $IN['UserName'] ) )
	{
		goback( "user_not_exists" );
	}
	if ( $AccessID = $AccessUser->accessDefined( $IN['UserName'] ) )
	{
		showmessage( "access_defined", "{$base_url}o=access_user::edit&AccessID={$AccessID}" );
	}
	require_once( INCLUDE_PATH."admin/site_admin.class.php" );
	$site = new site_admin( );
	$TPL->assign( "NodeInfo", $site->getAll4Tree( ) );
	$TPL->assign( "UserName", $IN['UserName'] );
	$TPL->display( "access_user_add.html" );
	break;
case "add_submit" :
	if ( !( $UserID = $AccessUser->userExists( $IN['UserName'] ) ) )
	{
		goback( "user_not_exists" );
	}
	$AccessUser->flushData( );
	$AccessUser->addData( "AccessType", 0 );
	$AccessUser->addData( "OwnerID", $UserID );
	$AccessUser->addData( "ReadIndex", "[".$IN[read_index_submit].",]" );
	$AccessUser->addData( "ReadContent", "[".$IN[read_content_submit].",]" );
	$AccessUser->addData( "PostComment", "[".$IN[post_comment_submit].",]" );
	$AccessUser->addData( "ReadComment", "[".$IN[read_comment_submit].",]" );
	$AccessUser->addData( "AuthInherit", "[".$IN[auth_inherit_submit].",]" );
	$AccessUser->addData( "Info", $IN[Info] );
	if ( $AccessUser->add( ) )
	{
		showmessage( "user_access_add_ok", "{$base_url}o=access_user::view'" );
	}
	else
	{
		showmessage( "user_access_add_fail", $referer );
	}
	break;
case "edit" :
	if ( empty( $IN[AccessID] ) )
	{
		showmessage( "", "{$base_url}o=access_user::view'" );
	}
	require_once( INCLUDE_PATH."admin/site_admin.class.php" );
	$site = new site_admin( );
	$pInfo = $AccessUser->getInfo( $IN[AccessID] );
	$TPL->assign( "pInfo", $pInfo );
	$TPL->assign( "NodeInfo", $site->getAll4Tree( ) );
	$TPL->assign( "UserName", $pInfo['UserName'] );
	$TPL->display( "access_user_edit.html" );
	break;
case "edit_submit" :
	if ( empty( $IN[AccessID] ) )
	{
		showmessage( "", "{$base_url}o=access_user::view'" );
	}
	$AccessUser->flushData( );
	$AccessUser->addData( "ReadIndex", "[".$IN[read_index_submit].",]" );
	$AccessUser->addData( "ReadContent", "[".$IN[read_content_submit].",]" );
	$AccessUser->addData( "PostComment", "[".$IN[post_comment_submit].",]" );
	$AccessUser->addData( "ReadComment", "[".$IN[read_comment_submit].",]" );
	$AccessUser->addData( "AuthInherit", "[".$IN[auth_inherit_submit].",]" );
	$AccessUser->addData( "Info", $IN[Info] );
	if ( $AccessUser->update( $IN[AccessID] ) )
	{
		showmessage( "user_access_edit_ok", "{$base_url}o=access_user::view'" );
	}
	else
	{
		showmessage( "user_access_edit_fail", $referer );
	}
	break;
case "del" :
	if ( empty( $IN[AccessID] ) )
	{
		showmessage( "", "{$base_url}o=access_user::view'" );
	}
	if ( $AccessUser->del( $IN[AccessID] ) )
	{
		showmessage( "user_access_del_ok", $referer );
	}
	else
	{
		showmessage( "user_access_del_fail", $referer );
		break;
	}
}
include( MODULES_DIR."footer.php" );
?>
