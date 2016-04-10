<?php
class File
{

	function xdel( $_path, $_filename )
	{
		if ( substr( $_path, -1 ) == "/" )
		{
			$_path = substr( $_path, 0, strlen( $_path ) - 1 );
		}
		if ( file_exists( $_path ) )
		{
			chmod( $_path, 511 );
			if ( is_dir( $_path ) )
			{
				$handle = opendir( $_path );
				while ( false !== ( $filename = readdir( $handle ) ) )
				{
					if ( $filename != "." && $filename != ".." )
					{
						xdel( $_path."/".$filename, $_filename );
					}
				}
				closedir( $handle );
				if ( $_filename == "*" || $_filename == "*.*" )
				{
					rmdir( $_path );
				}
			}
			else
			{
				$temp = explode( "/", $_path );
				if ( $_filename == "*" || $_filename == "*.*" )
				{
					unlink( $_path );
				}
				if ( $temp[count( $temp ) - 1] == $_filename )
				{
					unlink( $_path );
				}
			}
		}
	}

	function xmkdir( $_path, $_mode )
	{
		$fullpath = "";
		$_path = str_replace( DIRECTORY_SEPARATOR, "/", $_path );
		$_path = split( "/", $_path );
		while ( list( , $v ) = each( $_path ) )
		{
			$fullpath .= "{$v}/";
			if ( is_dir( $fullpath ) == false )
			{
				$oldmask = umask( 0 );
				if ( mkdir( $fullpath, $_mode ) == false )
				{
					return false;
				}
				umask( $oldmask );
			}
		}
		return true;
	}

	function automkdir( $_path, $_mode = 511 )
	{
		global $SYS_CONFIG;
		if ( $SYS_CONFIG['ftp_mode'] === 1 )
		{
			return File::_ftp_mkdir( $_path, $SYS_CONFIG, $_mode );
		}
		else
		{
			return File::xmkdir( $_path, $_mode );
		}
	}

	function autowrite( $_filename, &$_content )
	{
		global $SYS_CONFIG;
		if ( $SYS_CONFIG['ftp_mode'] === 1 )
		{
			return File::_ftp_write( $_filename, $_content, $SYS_CONFIG );
		}
		else
		{
			return File::write( $_filename, $_content, $SYS_CONFIG['file_mode'] );
		}
	}

	static function autocopy( $_src, $_des )
	{
		global $SYS_CONFIG;
		if ( $SYS_CONFIG['ftp_mode'] === 1 )
		{
			return File::_ftp_upload( $_src, $_des, $SYS_CONFIG );
		}
		else
		{
			copy( $_src, $_des );
			chmod( $_des, $SYS_CONFIG['file_mode'] );
			return true;
		}
	}

	function _ftp_write( $_filename, &$_content, $SYS_CONFIG )
	{
		$conn_id = ftp_connect( $SYS_CONFIG['ftp_host'], $SYS_CONFIG['ftp_port'] );
		ftp_login( $conn_id, $SYS_CONFIG['ftp_username'], $SYS_CONFIG['ftp_password'] );
		$pathInfo = pathinfo( $_filename );
		File::_ftp_mkdir( $pathInfo['dirname'], $SYS_CONFIG );
		$des = File::_ftp_realpath( $SYS_CONFIG['ftp_cms_admin_path'], $_filename );
		$tmpsrc = File::xtmpfile( $_content );
		if ( ftp_put( $conn_id, $des, $tmpsrc, FTP_BINARY ) )
		{
			if ( file_exists( $tmpsrc ) )
			{
				unlink( $tmpsrc );
			}
			ftp_site( $conn_id, "CHMOD ".decoct( $SYS_CONFIG['file_mode'] )." ".$des );
			return true;
		}
		else
		{
			return false;
		}
	}

	function _ftp_upload( $_src, $_des, $SYS_CONFIG )
	{
		$conn_id = ftp_connect( $SYS_CONFIG['ftp_host'], $SYS_CONFIG['ftp_port'] );
		ftp_login( $conn_id, $SYS_CONFIG['ftp_username'], $SYS_CONFIG['ftp_password'] );
		$pathInfo = pathinfo( $_des );
		File::_ftp_mkdir( $pathInfo['dirname'], $SYS_CONFIG );
		$des = File::_ftp_realpath( $SYS_CONFIG['ftp_cms_admin_path'], $_des );
		if ( ftp_put( $conn_id, $des, $_src, FTP_BINARY ) )
		{
			ftp_site( $conn_id, "CHMOD ".decoct( $SYS_CONFIG['file_mode'] )." ".$des );
			return true;
		}
		else
		{
			return false;
		}
	}

	function xtmpfile( &$content )
	{
		list( $usec, $sec ) = explode( " ", microtime( ) );
		$tmpfilename = CACHE_DIR.md5( ( double )$usec + ( double )$sec.mt_rand( 0, 10000 ) );
		if ( File::write( $tmpfilename, $content ) )
		{
			return $tmpfilename;
		}
		else
		{
			return false;
		}
	}

	function _ftp_realpath( $_root_path, $_path )
	{
		if ( substr( $_root_path, -1 ) == "/" )
		{
			$_root_path = substr( $_root_path, 0, -1 );
		}
		if ( substr( $_path, -1 ) == "/" )
		{
			$_path = substr( $_path, 0, -1 );
		}
		$return_path = $_root_path;
		foreach ( explode( "/", $_path ) as $key => $var )
		{
			if ( $key == 0 && $var == "" )
			{
				$return_path .= $_path;
				break;
			}
			if ( $var == ".." )
			{
				preg_match( "/(.*)\\//", $return_path, $match );
				$return_path = $match[1];
			}
			else if ( $var != "." )
			{
				$return_path .= "/".$var;
			}
		}
		return $return_path;
	}

	function _ftp_mkdir( $directory, $SYS_CONFIG, $mode = 511 )
	{
		$mode = $mode == "" ? 511 : $mode;
		$mode = decoct( $mode );
		if ( strlen( $mode ) == 4 )
		{
			$mode = substr( $mode, 1 );
		}
		$conn_id = ftp_connect( $SYS_CONFIG['ftp_host'], $SYS_CONFIG['ftp_port'] );
		$login_result = ftp_login( $conn_id, $SYS_CONFIG['ftp_username'], $SYS_CONFIG['ftp_password'] );
		if ( !$conn_id || !$login_result )
		{
			echo "<font color=red>FTP connection has failed!</font><br>Attempted to connect to {$ftp_server} for user {$ftp_user_name}.<br>";
			echo "Please reset you FTP accounts correctly in your  system setting.";
			exit( );
		}
		else
		{
			if ( is_dir( $directory ) )
			{
				return true;
			}
			$fullpath = "";
			$_path = str_replace( DIRECTORY_SEPARATOR, "/", $directory );
			$_path = split( "/", $directory );
			while ( list( , $v ) = each( $_path ) )
			{
				$fullpath .= "{$v}/";
				$dopath = File::_ftp_realpath( $SYS_CONFIG['ftp_cms_admin_path'], $fullpath );
				if ( !( is_dir( $fullpath ) == false ) && !ftp_mkdir( $conn_id, $dopath ) )
				{
					ftp_site( $conn_id, "CHMOD ".$mode." ".$dopath );
				}
			}
			return true;
			@ftp_close( $conn_id );
		}
	}

	function write( $_filename, $_content, $_mode = 511 )
	{
		$_mode = $_mode == "" ? 511 : $_mode;
		$dirname = dirname( $_filename );
		if ( @!is_dir( $dirname ) )
		{
			File::xmkdir( $dirname, $_mode );
		}
		$fp = fopen( $_filename, "w" );
		if ( $fp && fwrite( $fp, $_content ) )
		{
			fclose( $fp );
			chmod( $_filename, $_mode );
			return true;
		}
		return false;
	}

	function read( $_filename )
	{
		$fp = @fopen( $_filename, "r" );
		if ( !$fp )
		{
			return false;
		}
		$return = fread( $fp, filesize( $_filename ) );
		fclose( $fp );
		return $return;
	}

}

$SYS_CONFIG['file_mode'] = empty( $SYS_CONFIG['file_mode'] ) ? 511 : $SYS_CONFIG['file_mode'];
$SYS_CONFIG['dir_mode'] = empty( $SYS_CONFIG['dir_mode'] ) ? 511 : $SYS_CONFIG['dir_mode'];
if ( !class_exists( "Logger" ) )
{
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

}
?>
