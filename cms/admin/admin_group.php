<?php

//
//require_once( "common.php" );
//require_once( INCLUDE_PATH."admin/groupAdmin.class.php" );
//require_once( INCLUDE_PATH."admin/userAdmin.class.php" );
//$group = new groupAdmin( );
//$group->PermissionDetector( $IN );
//switch ( $IN[o] )
//{
//case "view" :
//	$TPL->assign( "gInfo", $group->getAllByPermissionAdmin( ) );
//	$TPL->display( "group_view.html" );
//	break;
//case "add" :
//	$TPL->assign( "groupsInfo", $group->getAllByPermissionRead( ) );
//	$TPL->display( "group_add.html" );
//	break;
//case "add_submit" :
//	$group->flushData( );
//	$group->addData( "gName", $IN[gName] );
//	$group->addData( "gInfo", $IN[gInfo] );
//	$group->addData( "gIsAdmin", $IN[isAdmin] );
//	$group->addData( "canChangePW", $IN[canChangePW] );
//	$group->addData( "canLogin", $IN[canLogin] );
//	$group->addData( "canLoginAdmin", $IN[canLoginAdmin] );
//	$group->addData( "canNode", $IN[canNode] );
//	$group->addData( "canTpl", $IN[canTpl] );
//	$group->addData( "canCollection", $IN[canCollection] );
//	$group->addData( "ParentGID", $IN[ParentGID] );
//	if ( $group->add( ) )
//	{
//		_goto( "view", "add_group_ok" );
//	}
//	else
//	{
//		_goto( "view", "add_group_fail" );
//	}
//	break;
//case "edit" :
//	if ( empty( $IN[gId] ) )
//	{
//		_goto( "view" );
//	}
//	$TPL->assign( "groupsInfo", $group->getAllByPermissionRead( ) );
//	$TPL->assign( "gInfo", $group->getInfo( $IN[gId] ) );
//	$TPL->display( "group_edit.html" );
//	break;
//case "edit_submit" :
//	if ( empty( $IN[gId] ) )
//	{
//		_goto( "view" );
//	}
//	$group->flushData( );
//	$group->addData( "gName", $IN[gName] );
//	$group->addData( "gInfo", $IN[gInfo] );
//	$group->addData( "gIsAdmin", $IN[isAdmin] );
//	$group->addData( "canChangePW", $IN[canChangePW] );
//	$group->addData( "canLogin", $IN[canLogin] );
//	$group->addData( "canLoginAdmin", $IN[canLoginAdmin] );
//	$group->addData( "canNode", $IN[canNode] );
//	$group->addData( "canTpl", $IN[canTpl] );
//	$group->addData( "canCollection", $IN[canCollection] );
//	$group->addData( "ParentGID", $IN[ParentGID] );
//	if ( $group->update( $IN[gId] ) )
//	{
//		_goto( "view", "edit_group_ok" );
//	}
//	else
//	{
//		_goto( "view", "edit_group_fail" );
//	}
//	break;
//case "del" :
//	if ( empty( $IN[gId] ) )
//	{
//		_goto( "view" );
//	}
//	if ( $group->del( $IN[gId] ) )
//	{
//		_goto( "view", "del_group_ok" );
//	}
//	else
//	{
//		_goto( "view", "del_group_fail" );
//	}
//	break;
//}
//include( "./modules/footer.php" );
?>
<?php


require_once( "common.php" );
require_once( INCLUDE_PATH."admin/groupAdmin.class.php" );
require_once( INCLUDE_PATH."admin/userAdmin.class.php" );
$group = new groupAdmin( );
$group->PermissionDetector( $IN );
switch ( $IN[o] )
{
case "view" :
	$TPL->assign( "gInfo", $group->getAllByPermissionAdmin( ) );
	$TPL->display( "group_view.html" );
	break;
case "add" :
	$TPL->assign( "groupsInfo", $group->getAllByPermissionRead( ) );
	$TPL->display( "group_add.html" );
	break;
case "add_submit" :
	$group->flushData( );
	$group->addData( "gName", $IN[gName] );
	$group->addData( "gInfo", $IN[gInfo] );
	$group->addData( "gIsAdmin", $IN[isAdmin] );
	$group->addData( "canChangePW", $IN[canChangePW] );
	$group->addData( "canLogin", $IN[canLogin] );
	$group->addData( "canLoginAdmin", $IN[canLoginAdmin] );
	$group->addData( "canNode", $IN[canNode] );
	$group->addData( "canTpl", $IN[canTpl] );
	$group->addData( "canCollection", $IN[canCollection] );
	$group->addData( "ParentGID", $IN[ParentGID] );
	$group->addData( "canMakeG", $IN[canMakeG] );
	$group->addData( "canMakeU", $IN[canMakeU] );
	if ( $group->add( ) )
	{
		_goto( "view", "add_group_ok" );
	}
	else
	{
		_goto( "view", "add_group_fail" );
	}
	break;
case "edit" :
	if ( empty( $IN[gId] ) )
	{
		_goto( "view" );
	}
	$TPL->assign( "groupsInfo", $group->getAllByPermissionRead( ) );
	$TPL->assign( "gInfo", $group->getInfo( $IN[gId] ) );
	$TPL->display( "group_edit.html" );
	break;
case "edit_submit" :
	if ( empty( $IN[gId] ) )
	{
		_goto( "view" );
	}
	$group->flushData( );
	$group->addData( "gName", $IN[gName] );
	$group->addData( "gInfo", $IN[gInfo] );
	$group->addData( "gIsAdmin", $IN[isAdmin] );
	$group->addData( "canChangePW", $IN[canChangePW] );
	$group->addData( "canLogin", $IN[canLogin] );
	$group->addData( "canLoginAdmin", $IN[canLoginAdmin] );
	$group->addData( "canNode", $IN[canNode] );
	$group->addData( "canTpl", $IN[canTpl] );
	$group->addData( "canCollection", $IN[canCollection] );
	$group->addData( "ParentGID", $IN[ParentGID] );
	$group->addData( "canMakeG", $IN[canMakeG] );
	$group->addData( "canMakeU", $IN[canMakeU] );
	if ( $group->update( $IN[gId] ) )
	{
		_goto( "view", "edit_group_ok" );
	}
	else
	{
		_goto( "view", "edit_group_fail" );
	}
	break;
case "del" :
	if ( empty( $IN[gId] ) )
	{
		_goto( "view" );
	}
	if ( $group->del( $IN[gId] ) )
	{
		
		_goto( "view", "del_group_ok" );
	}
	else
	{
		_goto( "view", "del_group_fail" );
		break;
	}
}
include( "./modules/footer.php" );
?>
