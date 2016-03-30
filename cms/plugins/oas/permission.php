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
$sqlMap = new SqlMap( dirname( __FILE__ )."/sqlmap.php" );
switch ( $action )
{
case "view" :
	$sqlMap->startTransaction( );
	$pList = $sqlMap->queryForList( "getAllPermission" );
	$TPL->assign_by_ref( "PermissionList", $pList );
	$TPL->display( "permission_list.html" );
	break;
case "add" :
	$TPL->display( "permission_add.html" );
	break;
case "add_submit" :
	$sqlMap->startTransaction( );
	$sqlMap->addData( "PermissionKey", $IN['data_PermissionKey'] );
	$pInfo = $sqlMap->queryForObject( "getPermissionInfo" );
	if ( !empty( $pInfo['PermissionKey'] ) )
	{
		goback( "permission.key_exits" );
	}
	$sqlMap->startTransaction( );
	$sqlMap->filterData( $IN );
	$sqlMap->addData( "Reserved", 0 );
	if ( $sqlMap->dataInsert( "addPermission" ) )
	{
		showmessage( "permission.add_ok", "{$base_url}o=permission::view" );
	}
	else
	{
		showmessage( "permission.add_fail", $referer );
	}
	break;
case "del" :
	if ( empty( $IN[PermissionKey] ) )
	{
		showmessage( "", "{$base_url}o=permission::view" );
	}
	$sqlMap->startTransaction( );
	$sqlMap->addData( "PermissionKey", $IN[PermissionKey] );
	if ( $sqlMap->dataDel( "delPermission" ) && $sqlMap->dataDel( "delAccessMap" ) )
	{
		showmessage( "permission.del_ok", $referer );
	}
	else
	{
		showmessage( "permission.del_fail", $referer );
		break;
	}
}
include( MODULES_DIR."footer.php" );
?>
