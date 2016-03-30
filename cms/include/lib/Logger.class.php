<?php


class Logger
{

	function info( $_msg )
	{
		$debuginfo = debug_backtrace( );
		$file = pathinfo( $debuginfo[0]['file'] );
		if ( is_array( $_msg ) )
		{
			$_msg = "Array ".var_export( $_msg, TRUE );
		}
		else if ( is_bool( $_msg ) )
		{
			$_msg = $_msg ? "Boolean TRUE" : "Boolean FALSE";
		}
		else if ( is_int( $_msg ) )
		{
			$_msg = "INT ".$_msg;
		}
		echo "INFO [{$file['basename']}:{$debuginfo[0]['line']}] ".$_msg."\n";
	}

	function error( $_msg )
	{
		$debuginfo = debug_backtrace( );
		$file = pathinfo( $debuginfo[0]['file'] );
		if ( is_array( $_msg ) )
		{
			$_msg = var_export( $_msg, TRUE );
		}
		echo "ERROR [{$file['basename']}:{$debuginfo[0]['line']}] ".$_msg."\n";
	}

}

?>
