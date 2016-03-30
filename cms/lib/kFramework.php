<?php


function get_file_path( $class_path )
{
	if ( strpos( $class_path, "." ) !== false )
	{
		$filename = str_replace( ".", DIRECTORY_SEPARATOR, $class_path );
		$filename = CLASS_PATH.DIRECTORY_SEPARATOR.$filename;
	}
	else
	{
		$filename = $class_path;
	}
	$ext = pathinfo( $filename, PATHINFO_EXTENSION );
	if ( $ext == "" )
	{
		$filename .= ".php";
	}
	if ( is_readable( $filename ) )
	{
		return realpath( $filename );
	}
	if ( is_array( $CLASS_PATH ) )
	{
		$filename = str_replace( ".", DIRECTORY_SEPARATOR, $class_path );
		$ext = pathinfo( $filename, PATHINFO_EXTENSION );
		if ( $ext == "" )
		{
			$filename .= ".php";
		}
		foreach ( $CLASS_PATH as $classdir )
		{
			$path = $classdir.DIRECTORY_SEPARATOR.$filename;
			if ( is_readable( $path ) )
			{
				return realpath( $path );
			}
		}
	}
	return false;
}

function load_class( $class_path )
{
	$className = str_replace( ".", "_", $class_path );
	if ( class_exists( $className ) )
	{
		return true;
	}
	$filename = get_file_path( $class_path );
	if ( $filename )
	{
		require_once( $filename );
		if ( class_exists( $className ) )
		{
			return true;
		}
	}
	trigger_error( "{$filename} not exists, {$className} not found", E_USER_ERROR );
	return false;
}

function &get_singleton( $class_path )
{
	static $objs = array( );
	$className = str_replace( ".", "_", $class_path );
	if ( isset( $objs[$className] ) )
	{
		return $objs[$className];
	}
	if ( !class_exists( $className ) )
	{
		load_class( $class_path );
	}
	$objs[$className] = new $className( );
	return $objs[$className];
}

function import( $class_path )
{
	return load_class( $class_path );
}

function logger( $_msg, $_level = "INFO" )
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
	switch ( $_level )
	{
	case "error" :
		echo "ERROR [{$file['basename']}:{$debuginfo[0]['line']}] ".$_msg."\n";
		break;
	case "INFO" :
	case "info" :
	default :
		echo "INFO [{$file['basename']}:{$debuginfo[0]['line']}] ".$_msg."\n";
		break;
	}
}

$GLOBALS['GLOBALS']['CLASS_PATH'] = array( );
if ( defined( "LIB_PATH" ) )
{
	$CLASS_PATH[] = LIB_PATH;
}
?>
