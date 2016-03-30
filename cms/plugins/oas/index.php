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
case "menu" :
	$TPL->display( "menu.html" );
	break;
case "setting" :
	$sqlMap->startTransaction( );
	$Info = $sqlMap->queryForSettingMap( "getOasInfo" );
	$TPL->assign_by_ref( "setting", $Info );
	$TPL->display( "setting.html" );
	break;
case "setting_submit" :
	$sqlMap->startTransaction( );
	$sqlMap->filterData( $IN );
	if ( $sqlMap->settingUpdate( "updateOasInfo" ) )
	{
		$bf =&Spring::getinstance( "spring.appcontext.php" );
		$sc =&$bf->getBean( "SettingCache" );
		if ( $sc->make( "plugin_oas_setting", $sqlMap->getData( ) ) )
		{
			showmessage( "update_setting_ok", $referer );
		}
		showmessage( "update_setting_fail_mkcache_error", $referer );
	}
	else
	{
		showmessage( "update_setting_fail", $referer );
	}
	break;
case "cwps_admin" :
	$bFactory =& Spring::getinstance( "spring.appcontext.php" );
	$settingCache =& $bFactory->getBean( "SettingCache" );
	$settingCache->load( "plugin_oas_setting" );
	if ( !empty( $IN[cwps_adminsid] ) )
	{
		$cwps_adminsid = substr( $IN[cwps_adminsid], 6 );
		_session_register( "cwps_adminsid", $cwps_adminsid );
		$data = $settingCache->getData( "CWPS_SelfAdminURL" );
		if ( !empty( $data ) )
		{
			$forward_url = $settingCache->getData( "CWPS_SelfAdminURL" )."?do=main&sId=".$cwps_adminsid;
		}
		else
		{
			$forward_url = $settingCache->getData( "CWPS_RootURL" )."/admin.php?do=main&sId=".$cwps_adminsid;
		}
		header( "Location: ".$forward_url );
	}
	else if ( !empty( $_SESSION[cwps_adminsid] ) )
	{
		$data = $settingCache->getData( "CWPS_SelfAdminURL" );
		if ( !empty( $data ) )
		{
			$forward_url = $settingCache->getData( "CWPS_SelfAdminURL" )."?do=main&sId=".$_SESSION[cwps_adminsid];
		}
		else
		{
			$forward_url = $settingCache->getData( "CWPS_RootURL" )."/admin.php?do=main&sId=".$_SESSION[cwps_adminsid];
		}
		header( "Location: ".$forward_url );
	}
	else
	{
		$data = $settingCache->getData( "CWPS_SelfAdminURL" );
		if ( !empty( $data ) )
		{
			$form_action = $settingCache->getData( "CWPS_SelfAdminURL" )."?do=login";
		}
		else
		{
			$form_action = $settingCache->getData( "CWPS_RootURL" )."/admin.php?do=login";
		}
		$TPL->assign( "form_action", $form_action );
		$TPL->assign( "Setting", $settingCache->getData( ) );
		$TPL->display( "cwps_login.html" );
		break;
	}
}
?>
