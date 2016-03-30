<?php


require_once( "common.php" );
require( "../license.php" );
$license_array = $License;
unset( $License );
if ( $license_array['Module-PublishAuth'] != 1 )
{
	goback( "license_Module_PublishAuth_disabled" );
}
if ( !$sys->isAdmin( ) )
{
	goback( "access_deny_module_publishAuth" );
}
require_once( INCLUDE_PATH."admin/publishAuthAdmin.class.php" );
require_once( INCLUDE_PATH."admin/site_admin.class.php" );
$pubAuth = new publishAuthAdmin( );
$site = new site_admin( );
switch ( $IN[o] )
{
case "view" :
	$TPL->assign( "pInfo", $pubAuth->getAll( ) );
	$TPL->display( "publishauth_view.html" );
	break;
case "add" :
	$TPL->assign( "NodeInfo", $site->getAll4Tree( ) );
	$TPL->display( "publishauth_add.html" );
	break;
case "add_submit" :
	$pubAuth->flushData( );
	$pubAuth->addData( "pName", $IN[pName] );
	$pubAuth->addData( "pInfo", $IN[pInfo] );
	$pubAuth->addData( "NodeList", "[".$IN[node_list_submit].",]" );
	$pubAuth->addData( "NodeExtraPublish", "[".$IN[node_extracontent_submit].",]" );
	$pubAuth->addData( "NodeSetting", "[".$IN[node_set_submit].",]" );
	$pubAuth->addData( "ContentRead", "[".$IN[content_read_submit].",]" );
	$pubAuth->addData( "ContentWrite", "[".$IN[content_write_submit].",]" );
	$pubAuth->addData( "ContentApprove", "[".$IN[content_approve_submit].",]" );
	$pubAuth->addData( "ContentPublish", "[".$IN[content_publish_submit].",]" );
	$pubAuth->addData( "AuthInherit", "[".$IN[auth_inherit_submit].",]" );
	if ( $pubAuth->add( ) )
	{
		_goto( "view", "add_publishadmin_ok" );
	}
	else
	{
		_goto( "view", "add_publishadmin_fail" );
	}
	break;
case "edit" :
	if ( empty( $IN[pId] ) )
	{
		_goto( "view" );
	}
	$TPL->assign( "pInfo", $pubAuth->getInfo( $IN[pId] ) );
	$TPL->assign( "NodeInfo", $site->getAll4Tree( ) );
	$TPL->display( "publishauth_edit.html" );
	break;
case "edit_submit" :
	if ( empty( $IN[pId] ) )
	{
		_goto( "view" );
	}
	$pubAuth->flushData( );
	$pubAuth->flushData( );
	$pubAuth->addData( "pName", $IN[pName] );
	$pubAuth->addData( "pInfo", $IN[pInfo] );
	$pubAuth->addData( "NodeList", "[".$IN[node_list_submit].",]" );
	$pubAuth->addData( "NodeExtraPublish", "[".$IN[node_extrapublish_submit].",]" );
	$pubAuth->addData( "NodeSetting", "[".$IN[node_set_submit].",]" );
	$pubAuth->addData( "ContentRead", "[".$IN[content_read_submit].",]" );
	$pubAuth->addData( "ContentWrite", "[".$IN[content_write_submit].",]" );
	$pubAuth->addData( "ContentApprove", "[".$IN[content_approve_submit].",]" );
	$pubAuth->addData( "ContentPublish", "[".$IN[content_publish_submit].",]" );
	$pubAuth->addData( "AuthInherit", "[".$IN[auth_inherit_submit].",]" );
	if ( $pubAuth->update( $IN[pId] ) )
	{
		_goto( "view", "edit_publishadmin_ok" );
	}
	else
	{
		_goto( "view", "edit_publishadmin_fail" );
	}
	break;
case "del" :
	if ( empty( $IN[pId] ) )
	{
		_goto( "view" );
	}
	if ( $pubAuth->del( $IN[pId] ) )
	{
		_goto( "view", "del_publishadmin_ok" );
	}
	else
	{
		_goto( "view", "del_publishadmin_fail" );
	}
	break;
}
include( "./modules/footer.php" );
?>
