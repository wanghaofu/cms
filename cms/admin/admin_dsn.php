<?php


require_once( "common.php" );
if ( !$sys->isAdmin( ) )
{
	goback( "access_deny_module_dsn" );
}
$dsn = new dsn_admin( );
switch ( $IN['o'] )
{
case "view" :
	$TPL->assign( "dsnList", $dsn->getAllDSN( ) );
	$TPL->display( "dsn_view.html" );
	break;
case "add" :
	$TPL->assign( "db_config", $db_config );
	$TPL->display( "dsn_add.html" );
	break;
case "add_submit" :
	if ( !empty( $IN[Name] ) )
	{
		$dsn->flushData( );
		$dsn->addData( "Name", $IN[Name] );
		$dsn->addData( "dbName", $IN[dbName] );
	}
	if ( $dsn->add( ) )
	{
		_goto( "view", "add_dsn_ok" );
	}
	else
	{
		_goto( "view", "add_dsn_fail" );
	}
	break;
case "edit" :
	if ( !empty( $IN[DSNID] ) )
	{
		$dsnInfo = $dsn->getDSNInfo( $IN[DSNID] );
		$TPL->assign( "db_config", $db_config );
		$TPL->assign( "dsnInfo", $dsnInfo );
		$TPL->display( "dsn_edit.html" );
	}
	else
	{
		_goto( "view" );
	}
	break;
case "edit_submit" :
	if ( empty( $IN[DSNID] ) )
	{
		_goto( "view" );
	}
	if ( !empty( $IN[Name] ) )
	{
		$dsn->flushData( );
		$dsn->addData( "Name", $IN[Name] );
		$dsn->addData( "dbName", $IN[dbName] );
		if ( $dsn->update( $IN[DSNID] ) )
		{
			_goto( "view", "edit_dsn_ok" );
		}
		else
		{
			_goto( "view", "edit_dsn_fail" );
		}
	}
	break;
case "del" :
	if ( empty( $IN[DSNID] ) )
	{
		_goto( "view" );
	}
	if ( $dsn->del( $IN[DSNID] ) )
	{
		_goto( "view", "del_dsn_ok" );
	}
	else
	{
		_goto( "view", "del_dsn_fail" );
	}
	break;
case "detect" :
	$params = array(
		"db_driver" => "db",
		"db_type" => "mysql",
		"db_host" => $IN[dbHost],
		"db_user" => $IN[dbUser],
		"db_password" => $IN[dbPass],
		"db_name" => $IN[dbName]
	);
	if ( $dsn->detect( $params ) )
	{
		exit( "1" );
	}
	else
	{
		exit( "0" );
	}
	break;
}
include( MODULES_DIR."footer.php" );
?>
