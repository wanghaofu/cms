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
require_once( PLUGIN_PATH."bbs/".$_SETTING['BBS']."/bbs.config.php" );
require_once( INCLUDE_PATH."admin/site_admin.class.php" );
$site = new site_admin( );
switch ( $action )
{
case "access_user_xml" :
	require_once( PLUGIN_PATH."include/access_user.class.php" );
	$AccessUser = new AccessUser( );
	if ( empty( $IN[NodeID] ) )
	{
		break;
	}
	$pInfo = $AccessUser->getInfo( $IN[AccessID] );
	$TPL->assign( "pInfo", $pInfo );
	$TPL->assign( "NodeInfo", $site->getAll4Tree( $IN[NodeID] ) );
	header( "Content-Type: text/xml; charset=".CHARSET."\n" );
	$now = gmdate( "D, d M Y H:i:s" )." GMT";
	header( "Expires: ".$now );
	echo "<?xml version=\"1.0\" encoding=\"".CHARSET."\"?>\n";
	$TPL->display( "access_user_xml.xml" );
	break;
case "access_group_xml" :
	require_once( PLUGIN_PATH."include/access_group.class.php" );
	$AccessGroup = new AccessGroup( );
	if ( empty( $IN[NodeID] ) )
	{
		break;
	}
	$pInfo = $AccessGroup->getInfo( $IN[AccessID] );
	$TPL->assign( "pInfo", $pInfo );
	$TPL->assign( "NodeInfo", $site->getAll4Tree( $IN[NodeID] ) );
	header( "Content-Type: text/xml; charset=".CHARSET."\n" );
	$now = gmdate( "D, d M Y H:i:s" )." GMT";
	header( "Expires: ".$now );
	echo "<?xml version=\"1.0\" encoding=\"".CHARSET."\"?>\n";
	$TPL->display( "access_group_xml.xml" );
	break;
}
?>
