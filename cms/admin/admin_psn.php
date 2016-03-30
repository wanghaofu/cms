<?php
require_once( "common.php" );
require_once( INCLUDE_PATH."admin/psn_admin.class.php" );
require_once( INCLUDE_PATH."admin/groupAdmin.class.php" );
if ( !$sys->isAdmin( ) )
{
	goback( "access_deny_module_psn" );
}

$psn = new psn_admin( );
switch ( $IN['o'] )
{
case "view" :
	$TPL->assign( "psnList", $psn->getAllPSN( ) );
	$TPL->display( "psn_view.html" );
	break;
case "add" :
	$TPL->assign_by_ref( "SrcPermissionReadG", groupAdmin::getall( "0" ) );
	$TPL->display( "psn_add.html" );
	break;
case "add_submit" :
	if ( !empty( $IN[Name] ) && !empty( $IN[psnType] ) )
	{
		
		$psn->flushData( );
		$psn->addData( "Name", $IN[Name] );
		$psn->addData( "Description", $IN[Description] );
		$psn->addData( "URL", $IN[URL] );
		$psn->addData( "PermissionReadG", $IN[data_PermissionReadG] );
		switch ( $IN[psnType] )
		{
		case "local" :
			$PSN = "relate::".$IN[localPath];
			break;
		case "ftp" :
			$PSN = "ftp::".$IN[ftp_user].":".$IN[ftp_pass]."@".$IN[ftp_host].":".$IN[ftp_port].$IN[ftp_path];
			break;
		case "rpc" :
		case "soap" :
		default :
			exit( "unsupport psn definition" );
		}
		$psn->addData( "PSN", $PSN );
	}
	if ( $psn->add( ) )
	{
		$cache = new CacheData( );
		$cache->makeCache( "psn" );
		$iWPC->clearALLNodeInfo( );
		_goto( "view", "add_psn_ok" );
	}
	else
	{
		_goto( "view", "add_psn_fail" );
	}
	break;
case "edit" :
	if ( !empty( $IN[PSNID] ) )
	{
		
		$psnInfo = $psn->getPSNInfo( $IN[PSNID] );
		$psnInfo = array_merge( $psnInfo, $psn->parsePSN( $psnInfo[PSN] ) );
		$gInfo = groupAdmin::getall( "0" );
		$PermissionReadG = explode( ",", $psnInfo['PermissionReadG'] );
		foreach ( $gInfo as $var )
		{
			if ( in_array( $var['gId'], $PermissionReadG ) )
			{
				$TargetPermissionReadG[] = $var;
			}
			else
			{
				$SrcPermissionReadG[] = $var;
			}
		}
		$TPL->assign_by_ref( "SrcPermissionReadG", $SrcPermissionReadG );
		$TPL->assign_by_ref( "TargetPermissionReadG", $TargetPermissionReadG );
		$TPL->assign( "psnInfo", $psnInfo );
		$TPL->display( "psn_edit.html" );
	}
	else
	{
		_goto( "view" );
	}
	break;
case "edit_submit" :
	if ( empty( $IN[PSNID] ) )
	{
		_goto( "view" );
	}
	if ( !empty( $IN[Name] ) && !empty( $IN[psnType] ) )
	{
		
		$psn->flushData( );
		$psn->addData( "Name", $IN[Name] );
		$psn->addData( "Description", $IN[Description] );
		$psn->addData( "URL", $IN[URL] );
		$psn->addData( "PermissionReadG", $IN[data_PermissionReadG] );
		switch ( $IN[psnType] )
		{
		case "local" :
			$PSN = "relate::".$IN[localPath];
			break;
		case "ftp" :
			$PSN = "ftp::".$IN[ftp_user].":".$IN[ftp_pass]."@".$IN[ftp_host].":".$IN[ftp_port].$IN[ftp_path];
			break;
		case "rpc" :
		case "soap" :
		default :
			exit( "unsupport psn definition" );
		}
		$psn->addData( "PSN", $PSN );
		if ( $psn->update( $IN[PSNID] ) )
		{
			$cache = new CacheData( );
			$cache->makeCache( "psn" );
			$iWPC->clearALLNodeInfo( );
			_goto( "view", "edit_psn_ok" );
		}
		else
		{
			_goto( "view", "edit_psn_fail" );
		}
	}
	else
	{
		_goto( "view", "edit_psn_fail" );
	}
	break;
case "del" :
	if ( empty( $IN[PSNID] ) )
	{
		_goto( "view" );
	}
	if ( $psn->del( $IN[PSNID] ) )
	{
		
		$cache = new CacheData( );
		$cache->makeCache( "psn" );
		$iWPC->clearALLNodeInfo( );
		_goto( "view", "del_psn_ok" );
	}
	else
	{
		_goto( "view", "del_psn_fail" );
	}
	break;
case "detect" :
	$psnInfo = $psn->getPSNInfo( $IN[PSNID] );
	if ( $psn->connect( $psnInfo[PSN] ) )
	{
		$psn->close( );
		exit( "1" );
	}
	else
	{
		exit( "0" );
	}
}
include( MODULES_DIR."footer.php" );
?>
