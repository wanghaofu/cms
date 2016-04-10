<?php
require_once( "common.php" );
if ( !$sys->isAdmin( ) )
{
	goback( "access_deny_module_log" );
}
require_once( INCLUDE_PATH."admin/logAdmin.class.php" );
$log_admin = new logAdmin( );
$IN[o] = empty( $IN[o] ) ? "list_LoginLog" : $IN[o];
switch ( $IN[o] )
{
case "list_LoginLog" :
	$offset = empty( $IN['offset'] ) ? 20 : $IN['offset'];
	$num = $log_admin->getLoginLogRecordNum( );
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
	$TPL->assign( "pList", $log_admin->getLoginLogLimit( $start, $offset ) );
	$TPL->assign( "recordInfo", $recordInfo );
	$TPL->assign( "NodeInfo", $NodeInfo );
	$TPL->assign( "offset", $offset );
	$TPL->assign( "pagelist", pagelist( $pagenum, $Page, $base_url."o=list_LoginLog&offset={$offset}", "#000000" ) );
	$TPL->display( "LoginLog_list.html" );
	break;
case "list_AdminLog" :
	$offset = empty( $IN['offset'] ) ? 20 : $IN['offset'];
	$num = $log_admin->getAdminLogRecordNum( );
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
	$TPL->assign( "pList", $log_admin->getAdminLogLimit( $start, $offset ) );
	$TPL->assign( "recordInfo", $recordInfo );
	$TPL->assign( "NodeInfo", $NodeInfo );
	$TPL->assign( "offset", $offset );
	$TPL->assign( "pagelist", pagelist( $pagenum, $Page, $base_url."o=list_AdminLog&offset={$offset}", "#000000" ) );
	$TPL->display( "AdminLog_list.html" );
	break;
case "search_LoginLog" :
	$offset = empty( $IN['offset'] ) ? 20 : $IN['offset'];
	$params['field'] = $IN['field'];
	$params['value'] = $IN['keywords'];
	$params['offset'] = $offset;
	$params['start_time'] = strtotime( $IN['start_time'] );
	$params['end_time'] = strtotime( $IN['end_time'] );
	$num = $log_admin->searchLoginLogRecordNum( $params );
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
	$params['start'] = $start;
	$recordInfo[currentPage] = $Page;
	$recordInfo[pageNum] = $pagenum;
	$recordInfo[recordNum] = $num;
	$recordInfo[offset] = $offset;
	$recordInfo[from] = $start;
	$recordInfo[to] = $start + $offset;
	$TPL->assign( "pList", $log_admin->searchLoginLogLimit( $params ) );
	$TPL->assign( "recordInfo", $recordInfo );
	$TPL->assign( "NodeInfo", $NodeInfo );
	$TPL->assign( "offset", $offset );
	$TPL->assign( "pagelist", pagelist( $pagenum, $Page, "{$base_url}o=search_LoginLog&offset={$offset}&field={$IN[field]}&keywords={$IN['keywords']}&start_time={$IN['start_time']}&end_time={$IN['end_time']}", "#000000" ) );
	$TPL->display( "LoginLog_list.html" );
	break;
case "search_AdminLog" :
	$offset = empty( $IN['offset'] ) ? 20 : $IN['offset'];
	$params['field'] = $IN['field'];
	$params['value'] = $IN['keywords'];
	$params['offset'] = $offset;
	$params['start_time'] = strtotime( $IN['start_time'] );
	$params['end_time'] = strtotime( $IN['end_time'] );
	$num = $log_admin->searchAdminLogRecordNum( $params );
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
	$params['start'] = $start;
	$recordInfo[currentPage] = $Page;
	$recordInfo[pageNum] = $pagenum;
	$recordInfo[recordNum] = $num;
	$recordInfo[offset] = $offset;
	$recordInfo[from] = $start;
	$recordInfo[to] = $start + $offset;
	$TPL->assign( "pList", $log_admin->searchAdminLogLimit( $params ) );
	$TPL->assign( "recordInfo", $recordInfo );
	$TPL->assign( "NodeInfo", $NodeInfo );
	$TPL->assign( "offset", $offset );
	$TPL->assign( "pagelist", pagelist( $pagenum, $Page, "{$base_url}o=search_AdminLog&offset={$offset}&field={$IN[field]}&keywords={$IN['keywords']}&start_time={$IN['start_time']}&end_time={$IN['end_time']}", "#000000" ) );
	$TPL->display( "AdminLog_list.html" );
	break;
case "delAdminLog" :
	if ( !empty( $IN['LogIDs'] ) )
	{
		foreach ( $IN['LogIDs'] as $key => $var )
		{
			$result = $log_admin->delAdminLog( $var );
		}
		if ( $result )
		{
			showmessage( "delAdminLog_ok", $referer );
		}
		showmessage( "delAdminLog_fail", $referer );
	}
	if ( !empty( $IN['LogID'] ) )
	{
		if ( $log_admin->delAdminLog( $IN['LogID'] ) )
		{
			showmessage( "delAdminLog_ok", $referer );
		}
		showmessage( "delAdminLog_fail", $referer );
	}
	if ( !empty( $IN['start_time'] ) && !empty( $IN['end_time'] ) )
	{
		if ( $log_admin->delAdminLogByTime( strtotime( $IN['start_time'] ), strtotime( $IN['end_time'] ) ) )
		{
			showmsg( sprintf( $_LANG_ADMIN['delAdminLogByTime_ok'], $IN['start_time'], $IN['end_time'] ), $referer, 3 );
		}
		showmsg( sprintf( $_LANG_ADMIN['delAdminLogByTime_fail'], $IN['start_time'], $IN['end_time'] ), $referer, 3 );
	}
	showmessage( "delAdminLog_null", $referer );
case "delLoginLog" :
	if ( !empty( $IN['LogIDs'] ) )
	{
		foreach ( $IN['LogIDs'] as $key => $var )
		{
			$result = $log_admin->delLoginLog( $var );
		}
		if ( $result )
		{
			showmessage( "delLoginLog_ok", $referer );
		}
		else
		{
			showmessage( "delLoginLog_fail", $referer );
		}
	}
	else if ( !empty( $IN['LogID'] ) )
	{
		if ( $log_admin->delLoginLog( $IN['LogID'] ) )
		{
			showmessage( "delLoginLog_ok", $referer );
		}
		else
		{
			showmessage( "delLoginLog_fail", $referer );
		}
	}
	else if ( !empty( $IN['start_time'] ) && !empty( $IN['end_time'] ) )
	{
		if ( $log_admin->delLoginLogByTime( strtotime( $IN['start_time'] ), strtotime( $IN['end_time'] ) ) )
		{
			showmsg( sprintf( $_LANG_ADMIN['delLoginLogByTime_ok'], $IN['start_time'], $IN['end_time'] ), $referer, 3 );
		}
		else
		{
			showmsg( sprintf( $_LANG_ADMIN['delLoginLogByTime_fail'], $IN['start_time'], $IN['end_time'] ), $referer, 3 );
		}
	}
	else
	{
		showmessage( "delLoginLog_null", $referer );
	}
	break;
}
include( MODULES_DIR."footer.php" );
?>
