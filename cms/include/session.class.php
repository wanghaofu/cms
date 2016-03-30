<?php


function _session_start( )
{
	global $db;
	global $_SESSION;
	global $IN;
	global $__SESSION_Started;
	$__SESSION_Started = TRUE;
}

function _session_register( $_key, $_var = NULL )
{
	global $db;
	global $_SESSION;
	global $_SESSION_KEY;
	global $IN;
	global $__SESSION_Started;
	$_SESSION[$_key] = $_var;
}

function _session_store( )
{
	global $db;
	global $_SESSION;
	global $table;
	global $sys;
	$SessionData = serialize( $_SESSION );
	if ( isset( $db ) )
	{
		$sql = "update {$table->admin_sessions} set sData='".$db->escape_string( $SessionData )."' where sId='{$sys->sId}' ";
		$db->query( $sql );
		$db->close( );
	}
}

if ( !isset( $_SESSION ) )
{
	$_SESSION = array( );
}
$__SESSION_Started = FALSE;
register_shutdown_function( "_session_store" );
?>
