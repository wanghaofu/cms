<?php


if ( !defined( "IN_CMSWARE" ) )
{
	exit( "Access Denied" );
}
require( "../license.php" );
$license_array = $License;
unset( $License );
if ( !$sys->isAdmin( ) )
{
	goback( "plugin_deny" );
}
if ( $license_array['Module-FullText'] != 1 )
{
	goback( "plugin_deny" );
}
require_once( PLUGIN_PATH."include/setting.class.php" );
require_once( INCLUDE_PATH."admin/site_admin.class.php" );
$site = new site_admin( );
switch ( $action )
{
case "menu" :
	$TPL->display( "menu.html" );
	break;
case "setting" :
	$TPL->assign( "ContentModels", PluginSetting::getall( ) );
	$TPL->display( "setting.html" );
	break;
case "setting_add" :
	$TPL->assign( "setting", PluginSetting::getinfo( $IN['TableID'] ) );
	$TPL->display( "setting_add.html" );
	break;
case "setting_edit" :
	$setting = new PluginSetting( );
	include_once( CACHE_DIR."Cache_ContentModel.php" );
	$TPL->assign( "ContentModel", $CONTENT_MODEL_INFO[$IN['TableID']]['Model'] );
	$TPL->assign( "FullText", $setting->getAllFullText( $IN['TableID'] ) );
	$TPL->assign( "setting", PluginSetting::getinfo( $IN['TableID'] ) );
	$TPL->display( "setting_edit.html" );
	break;
case "setting_submit" :
	$setting = new PluginSetting( );
	$setting->flushData( );
	$setting->addData( "TableID", $IN['TableID'] );
	$setting->filterData( $IN );
	if ( $setting->update( ) )
	{
		showmessage( "update_setting_ok", $referer );
	}
	else
	{
		showmessage( "update_setting_fail", $referer );
	}
	break;
case "fulltext_add" :
	include_once( CACHE_DIR."Cache_ContentModel.php" );
	$TPL->assign( "ContentModel", $CONTENT_MODEL_INFO[$IN['TableID']]['Model'] );
	$TPL->assign( "fieldNum", $IN['fieldNum'] );
	$TPL->assign( "TableID", $IN['TableID'] );
	$TPL->display( "fulltext_add.html" );
	break;
case "fulltext_add_submit" :
	$setting = new PluginSetting( );
	$IN['Fields'] = array_unique( $IN['Fields'] );
	if ( $setting->addFullText( $IN['TableID'], $IN['SearchName'], $IN['Fields'] ) )
	{
		showmessage( "fulltext_add_ok", $base_url."o=index::setting_edit&TableID={$IN['TableID']}" );
	}
	else
	{
		showmessage( "fulltext_add_fail", $base_url."o=index::setting_edit&TableID={$IN['TableID']}" );
	}
	break;
case "fulltext_del" :
	$setting = new PluginSetting( );
	if ( $setting->delFullText( $IN['TableID'], $IN['SearchID'] ) )
	{
		showmessage( "fulltext_del_ok", $referer );
	}
	else
	{
		showmessage( "fulltext_del_fail", $referer );
		break;
	}
}
?>
