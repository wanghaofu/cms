<?php
if ( !defined( "IN_CMSWARE" ) )
{
	exit( "Access Denied" );
}
require_once( PLUGIN_PATH."include/setting.class.php" );
$_SETTING = PluginSetting::getinfo( );
require_once( PLUGIN_PATH."bbs/".$_SETTING['BBS']."/bbs.config.php" );
require_once( INCLUDE_PATH."admin/site_admin.class.php" );
$site = new site_admin( );
switch ( $action )
{
case "tree" :
	$NodeInfo = $site->getAll4Tree( );
	$TPL->assign( "NodeInfo", $NodeInfo );
	$TPL->display( $IN['tpl'] );
	break;
case "tree_xml" :
	if ( !empty( $IN[NodeID] ) )
	{
		break;
	}
	$NodeInfo = $site->getAll4Tree( $IN[NodeID] );
	$TPL->assign( "NodeInfo", $NodeInfo );
	header( "Content-Type: text/xml; charset=".CHARSET."\n" );
	$now = gmdate( "D, d M Y H:i:s" )." GMT";
	header( "Expires: ".$now );
	$TPL->display( $IN['tpl'] );
	break;
}
?>
