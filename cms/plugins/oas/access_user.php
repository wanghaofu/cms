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
require_once( PLUGIN_PATH."include/AccessUser.php" );
$bFactory =& Spring::getinstance( "spring.appcontext.php" );
$settingCache =& $bFactory->getBean( "SettingCache" );
$settingCache->load( "plugin_oas_setting" );
$oas = new SoapOAS( $settingCache->getData( "CWPS_Address" ) );
$oas->setTransactionAccessKey( $settingCache->getData( "CWPS_TransactionAccessKey" ) );
$oas->doLog = false;
$oas->setReqCharset( CHARSET );
$oas->setRespCharset( CHARSET );
$AccessUser = new AccessUser( );
switch ( $action )
{
case "view" :
	$AccessUser->startTransaction( );
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
	
	$TPL->assign( "uList", $AccessUser->getRecordLimit( $start, $offset, $oas ) );
	$TPL->assign( "offset", $offset );
	$TPL->assign( "pagelist", pagelist( $pagenum, $Page, "{$base_url}o=access_user::view&offset={$offset}", "#000000" ) );
	
//	de($AccessUser,__file__,__line__);
//	de($TPL,__file__,__line__);
	
	$TPL->display( "access_user_view.html" );
	break;
case "add" :
	if ( !$AccessUser->userExists( $IN['UserName'], $oas ) )
	{
		goback( "user_not_exists" );
	}
	if ( $AccessID = $AccessUser->accessDefined( $IN['UserName'], $oas ) )
	{
		showmessage( "access_defined", "{$base_url}o=access_user::edit&AccessID={$AccessID}" );
	}
	$AccessUser->startTransaction( );
	$permList = $AccessUser->queryForList( "getAllPermission" );
	$TPL->assign_by_ref( "PermissionList", $permList );
	$TPL->assign( "NODE_LIST", $NODE_LIST );
	$TPL->assign( "UserName", $IN['UserName'] );
	$TPL->display( "access_user_add.html" );
	break;
case "add_submit" :
	if ( !( $UserID = $AccessUser->userExists( $IN['UserName'], $oas ) ) )
	{
		goback( "user_not_exists" );
	}
	$AccessUser->startTransaction( );
	$AccessUser->addData( "AccessType", 0 );
	$AccessUser->addData( "OwnerID", $UserID );
	$AccessUser->addData( "AccessInherit", "[".$IN['AccessInherit'].",]" );
	$AccessUser->addData( "Info", $IN[Info] );
	$result = $AccessUser->dataInsert( "addAccess" );
	$AccessID = $AccessUser->db_insert_id;
	$AccessUser->commitTransaction( );
	$permList = $AccessUser->queryForList( "getAllPermission" );
	foreach ( $permList as $var )
	{
		$AccessUser->startTransaction( );
		$AccessUser->addData( "AccessID", $AccessID );
		$AccessUser->addData( "PermissionKey", $var['PermissionKey'] );
		$AccessUser->addData( "AccessNodeIDs", "[".$IN[$var['PermissionKey']].",]" );
		$result = $AccessUser->dataReplace( "addAccessMap" );
		$AccessUser->commitTransaction( );
	}
	if ( $result )
	{
		showmessage( "user_access_add_ok", "{$base_url}o=access_user::view" );
	}
	else
	{
		showmessage( "user_access_add_fail", $referer );
	}
	break;
case "edit" :
	if ( empty( $IN[AccessID] ) )
	{
		showmessage( "", "{$base_url}o=access_user::view" );
	}
	$pInfo = $AccessUser->getInfo( $IN[AccessID], $oas );
	$TPL->assign( "pInfo", $pInfo );
	$permList = $AccessUser->queryForList( "getAllPermission" );
	$TPL->assign_by_ref( "PermissionList", $permList );
	$TPL->assign( "NODE_LIST", $NODE_LIST );
	$TPL->assign( "UserName", $pInfo['UserName'] );
	$TPL->display( "access_user_edit.html" );
	break;
case "edit_submit" :
	if ( empty( $IN[AccessID] ) )
	{
		showmessage( "", "{$base_url}o=access_user::view" );
	}
	$AccessUser->startTransaction( );
	$AccessUser->addData( "AccessID", $IN[AccessID] );
	$AccessUser->addData( "AccessInherit", "[".$IN['AccessInherit'].",]" );
	$AccessUser->addData( "Info", $IN[Info] );
	$result = $AccessUser->dataUpdate( "updateAccess" );
	$AccessUser->commitTransaction( );
	$permList = $AccessUser->queryForList( "getAllPermission" );
	foreach ( $permList as $var )
	{
		$AccessUser->startTransaction( );
		$AccessUser->addData( "AccessID", $IN[AccessID] );
		$AccessUser->addData( "PermissionKey", $var['PermissionKey'] );
		$AccessUser->addData( "AccessNodeIDs", "[".$IN[$var['PermissionKey']].",]" );
		$result = $AccessUser->dataReplace( "addAccessMap" );
		$AccessUser->commitTransaction( );
	}
	if ( $result )
	{
		showmessage( "user_access_edit_ok", "{$base_url}o=access_user::view" );
	}
	else
	{
		showmessage( "user_access_edit_fail", $referer );
	}
	break;
case "del" :
	
	if ( empty( $IN[AccessID] ) )
	{
		showmessage( "", "{$base_url}o=access_user::view" );
	}
	$AccessUser->startTransaction( );
	$AccessUser->addData( "AccessID", $IN[AccessID] );
	if ( $AccessUser->dataDel( "delAccess" ) && $AccessUser->dataDel( "delAccessMap" ) )
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
