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
require_once( LIB_PATH."SqlMap.class.php" );
require_once( LIB_PATH."SoapOAS.class.php" );
require_once( PLUGIN_PATH."include/AccessGroup.php" );
require_once( INCLUDE_PATH."admin/site_admin.class.php" );
$site = new site_admin( );
$AccessGroup = new AccessGroup( );
$bFactory =& Spring::getinstance( "spring.appcontext.php" );
$settingCache =& $bFactory->getBean( "SettingCache" );
$settingCache->load( "plugin_oas_setting" );
$oas = new SoapOAS( $settingCache->getData( "CWPS_Address" ) );
$oas->setTransactionAccessKey( $settingCache->getData( "CWPS_TransactionAccessKey" ) );
switch ( $action )
{
case "access_user_xml" :
	require_once( PLUGIN_PATH."include/AccessUser.php" );
	$AccessUser = new AccessUser( );
	if ( empty( $IN[NodeID] ) )
	{
		break;
	}
	$pInfo = $AccessUser->getInfo( $IN[AccessID], $oas );
	$TPL->assign( "pInfo", $pInfo );
	$TPL->assign( "NodeInfo", $site->getAll4Tree( $IN[NodeID] ) );
	header( "Content-Type: text/xml; charset=".CHARSET."\n" );
	$now = gmdate( "D, d M Y H:i:s" )." GMT";
	header( "Expires: ".$now );
	$TPL->display( "access_user_xml.xml" );
	break;
case "access_group_xml" :
	require_once( PLUGIN_PATH."include/AccessGroup.php" );
	$AccessGroup = new AccessGroup( );
	if ( empty( $IN[NodeID] ) )
	{
		break;
	}
	$pInfo = $AccessGroup->getInfo( $IN[AccessID], $oas );
	$TPL->assign( "pInfo", $pInfo );
	$TPL->assign( "NodeInfo", $site->getAll4Tree( $IN[NodeID] ) );
	header( "Content-Type: text/xml; charset=".CHARSET."\n" );
	$now = gmdate( "D, d M Y H:i:s" )." GMT";
	header( "Expires: ".$now );
	$TPL->display( "access_group_xml.xml" );
	break;
}
?>
