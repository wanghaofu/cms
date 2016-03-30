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
	require_once( PLUGIN_PATH."include/access_group.class.php" );
	$AccessGroup = new AccessGroup( );
}
switch ( $action )
{
case "view" :
	if ( isset( $AccessGroup ) )
	{
		$TPL->assign( "gList", $AccessGroup->getAll( ) );
	}
	$TPL->display( "access_group_view.html" );
	break;
case "add" :
	require_once( INCLUDE_PATH."admin/site_admin.class.php" );
	$site = new site_admin( );
	$TPL->assign( "NodeInfo", $site->getAll4Tree( ) );
	$TPL->assign( "GroupName", $IN['GroupName'] );
	$TPL->assign( "GroupID", $IN['GroupID'] );
	$TPL->display( "access_group_add.html" );
	break;
case "add_submit" :
	if ( empty( $IN[GroupID] ) )
	{
		showmessage( "", "{$base_url}o=access_group::view'" );
	}
	$AccessGroup->flushData( );
	$AccessGroup->addData( "AccessType", 1 );
	$AccessGroup->addData( "OwnerID", $IN[GroupID] );
	$AccessGroup->addData( "ReadIndex", "[".$IN[read_index_submit].",]" );
	$AccessGroup->addData( "ReadContent", "[".$IN[read_content_submit].",]" );
	$AccessGroup->addData( "PostComment", "[".$IN[post_comment_submit].",]" );
	$AccessGroup->addData( "ReadComment", "[".$IN[read_comment_submit].",]" );
	$AccessGroup->addData( "AuthInherit", "[".$IN[auth_inherit_submit].",]" );
	$AccessGroup->addData( "Info", $IN[Info] );
	if ( $AccessGroup->add( ) )
	{
		showmessage( "group_access_add_ok", "{$base_url}o=access_group::view'" );
	}
	else
	{
		showmessage( "group_access_add_fail", $referer );
	}
	break;
case "edit" :
	if ( empty( $IN[AccessID] ) )
	{
		showmessage( "", "{$base_url}o=access_group::view'" );
	}
	require_once( INCLUDE_PATH."admin/site_admin.class.php" );
	$site = new site_admin( );
	$pInfo = $AccessGroup->getInfo( $IN[AccessID] );
	$TPL->assign( "pInfo", $pInfo );
	$TPL->assign( "NodeInfo", $site->getAll4Tree( ) );
	$TPL->assign( "GroupName", $pInfo['GroupName'] );
	$TPL->display( "access_group_edit.html" );
	break;
case "edit_submit" :
	if ( empty( $IN[AccessID] ) )
	{
		showmessage( "", "{$base_url}o=access_group::view'" );
	}
	$AccessGroup->flushData( );
	$AccessGroup->addData( "ReadIndex", "[".$IN[read_index_submit].",]" );
	$AccessGroup->addData( "ReadContent", "[".$IN[read_content_submit].",]" );
	$AccessGroup->addData( "PostComment", "[".$IN[post_comment_submit].",]" );
	$AccessGroup->addData( "ReadComment", "[".$IN[read_comment_submit].",]" );
	$AccessGroup->addData( "AuthInherit", "[".$IN[auth_inherit_submit].",]" );
	$AccessGroup->addData( "Info", $IN[Info] );
	if ( $AccessGroup->update( $IN[AccessID] ) )
	{
		showmessage( "group_access_edit_ok", "{$base_url}o=access_group::view'" );
	}
	else
	{
		showmessage( "group_access_edit_fail", $referer );
		break;
	}
}
include( MODULES_DIR."footer.php" );
?>
