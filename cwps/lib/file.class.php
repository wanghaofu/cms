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
			return File::write( $_filename, $_content );
		}
	}

	function autocopy( $_src, $_des )
	{
		global $SYS_CONFIG;
		if ( $SYS_CONFIG['ftp_mode'] === 1 )
		{
			return File::_ftp_upload( $_src, $_des, $SYS_CONFIG );
		}
		else
		{
			return copy( $_src, $_des );
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
			ftp_chdir( $conn_id, $SYS_CONFIG['ftp_cms_admin_path'] );
			$dirinfo = pathinfo( $directory );
			if ( !ftp_chdir( $conn_id, $dirinfo['dirname'] ) )
			{
				$pathInfo = explode( "/", $dirinfo['dirname'] );
				$basedir = "";
				foreach ( $pathInfo as $var )
				{
					if ( $var == "." )
					{
						$basedir .= "./";
						$begin = false;
					}
					else
					{
						if ( $var == ".." )
						{
							$basedir .= "../";
							$begin = false;
						}
						else
						{
							if ( !$begin )
							{
								$var = $var;
								$begin = true;
							}
							else
							{
								$var = "/".$var;
							}
							if ( File::_ftp_mkdir( $basedir.$var, $SYS_CONFIG, octdec( $mode ) ) )
							{
								$repair = true;
								$basedir .= $var;
							}
							else
							{
								$repair = false;
							}
						}
					}
				}
			}
			else
			{
				return true;
			}
			if ( ftp_mkdir( $conn_id, $dirinfo['basename'] ) )
			{
				ftp_site( $conn_id, "CHMOD ".$mode." ".$dirinfo['basename'] );
				@ftp_close( $conn_id );
				return true;
			}
			else
			{
				@ftp_close( $conn_id );
				return false;
			}
		}
	}

	function write( $_filename, $_content )
	{
		$dirname = dirname( $_filename );
		if ( @!is_dir( $dirname ) )
		{
			File::xmkdir( $dirname, 511 );
		}
		$fp = fopen( $_filename, "w" );
		if ( $fp && fwrite( $fp, $_content ) )
		{
			fclose( $fp );
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
