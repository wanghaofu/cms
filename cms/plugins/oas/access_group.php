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
$AccessGroup = new AccessGroup();
$bFactory =&Spring::getinstance( "spring.appcontext.php" );
$settingCache =&$bFactory->getBean( "SettingCache" );
$settingCache->load( "plugin_oas_setting" );
$oas = new SoapOAS( $settingCache->getData( "CWPS_Address" ) );
$oas->setTransactionAccessKey( $settingCache->getData( "CWPS_TransactionAccessKey" ) );
$oas->doLog = false;
$oas->logFile = "./oas.log.".date( "Y-m-d" ).".txt";
$oas->setReqCharset( CHARSET );
$oas->setRespCharset( CHARSET );
switch ( $action )
{
case "view" :
	$AccessGroup->startTransaction( );
	$gList = $AccessGroup->getAll( $oas );
	$TPL->assign_by_ref( "gList", $gList );
	$TPL->display( "access_group_view.html" );
	break;
case "add" :
	$AccessGroup->startTransaction( );
	$permList = $AccessGroup->queryForList( "getAllPermission" );
	$TPL->assign_by_ref( "PermissionList", $permList );
	$TPL->assign( "NODE_LIST", $NODE_LIST );
	$TPL->assign( "GroupName", $IN['GroupName'] );
	$TPL->assign( "GroupID", $IN['GroupID'] );
	$TPL->display( "access_group_add.html" );
	break;
case "add_submit" :
	if ( empty( $IN[GroupID] ) )
	{
		showmessage( "", "{$base_url}o=access_group::view" );
	}
	$AccessGroup->startTransaction( );
	$AccessGroup->addData( "AccessType", 1 );
	$AccessGroup->addData( "OwnerID", $IN[GroupID] );
	$AccessGroup->addData( "AccessInherit", "[".$IN['AccessInherit'].",]" );
	$AccessGroup->addData( "Info", $IN[Info] );
	$result = $AccessGroup->dataInsert( "addAccess" );
	$AccessID = $AccessGroup->db_insert_id;
	$AccessGroup->commitTransaction( );
	$permList = $AccessGroup->queryForList( "getAllPermission" );
	foreach ( $permList as $var )
	{
		$AccessGroup->startTransaction( );
		$AccessGroup->addData( "AccessID", $AccessID );
		$AccessGroup->addData( "PermissionKey", $var['PermissionKey'] );
		$AccessGroup->addData( "AccessNodeIDs", "[".$IN[$var['PermissionKey']].",]" );
		$result = $AccessGroup->dataReplace( "addAccessMap" );
		$AccessGroup->commitTransaction( );
	}
	if ( $result )
	{
		showmessage( "group_access_add_ok", "{$base_url}o=access_group::view" );
	}
	else
	{
		showmessage( "group_access_add_fail", $referer );
	}
	break;
case "edit" :
	if ( empty( $IN[AccessID] ) )
	{
		showmessage( "", "{$base_url}o=access_group::view" );
	}
	$pInfo = $AccessGroup->getInfo( $IN[AccessID], $oas );
	$TPL->assign( "pInfo", $pInfo );
	$permList = $AccessGroup->queryForList( "getAllPermission" );
	$TPL->assign_by_ref( "PermissionList", $permList );
	$TPL->assign( "NODE_LIST", $NODE_LIST );
	$TPL->assign( "GroupName", $pInfo['GroupName'] );
	$TPL->display( "access_group_edit.html" );
	break;
case "edit_submit" :
	if ( empty( $IN[AccessID] ) )
	{
		showmessage( "", "{$base_url}o=access_group::view" );
	}
	$AccessGroup->startTransaction( );
	$AccessGroup->addData( "AccessID", $IN[AccessID] );
	$AccessGroup->addData( "AccessInherit", "[".$IN['AccessInherit'].",]" );
	$AccessGroup->addData( "Info", $IN[Info] );
	$result = $AccessGroup->dataUpdate( "updateAccess" );
	$AccessGroup->commitTransaction( );
	$permList = $AccessGroup->queryForList( "getAllPermission" );
	foreach ( $permList as $var )
	{
		$AccessGroup->startTransaction( );
		$AccessGroup->addData( "AccessID", $IN[AccessID] );
		$AccessGroup->addData( "PermissionKey", $var['PermissionKey'] );
		$AccessGroup->addData( "AccessNodeIDs", "[".$IN[$var['PermissionKey']].",]" );
		$result = $AccessGroup->dataReplace( "addAccessMap" );
		$AccessGroup->commitTransaction( );
	}
	if ( $result )
	{
		showmessage( "group_access_edit_ok", "{$base_url}o=access_group::view" );
	}
	else
	{
		showmessage( "group_access_edit_fail", $referer );
	}
	break;
case "del" :
	if ( empty( $IN[AccessID] ) )
	{
		showmessage( "", "{$base_url}o=access_group::view" );
	}
	$AccessGroup->startTransaction( );
	$AccessGroup->addData( "AccessID", $IN[AccessID] );
	if ( $AccessGroup->dataDel( "delAccess" ) && $AccessGroup->dataDel( "delAccessMap" ) )
	{
		showmessage( "group_access_del_ok", $referer );
	}
	else
	{
		showmessage( "group_access_del_fail", $referer );
		break;
	}
}
include( MODULES_DIR."footer.php" );
?>
