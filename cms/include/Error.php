<?php
class Error
{

	var $conf = NULL;
	var $lang = NULL;

	function Error( $conf = 1, $lang = 1 )
	{
		$this->conf = $conf;
		$this->lang = $lang;
		set_error_handler( array(
			$this,
			"handler"
		) );
	}

	function setErrorHander( )
	{
		set_error_handler( array(
			$this,
			"handler"
		) );
	}

	function handler( $no, $str, $file, $line, $ctx )
	{
		if ( $no == E_NOTICE || $no == 2048 )
		{
			return FALSE;
		}
		switch ( Error_Display )
		{
		case "js" :
			print "<script>alert(\"";
			print "\\nException message: ".$str."\\nError code: ".$no."\\n";
			break;
		case "html" :
			print "<br/><b>Exception message</b>: ".$str."<br/><b>Error code</b>: ".$no."<br/>";
			break;
		case "text" :
			break;
			break;
		}
		$this->logdata = "Exception message: ".$str."\nError code: ".$no."\n";
		$this->_printStackTrace( );
	}

	function _printStackTrace( )
	{
		switch ( Error_Display )
		{
		case "js" :
			if ( function_exists( "debug_backtrace" ) )
			{
				$info = debug_backtrace( );
				print "-- Backtrace --\\n";
				foreach ( $info as $trace )
				{
					if ( $trace['function'] != "_internalerrorhandler" && $trace['file'] != __FILE__ )
					{
						print addslashes( $trace['file'] );
						print "(".$trace['line']."): ";
						if ( $trace['class'] != "" )
						{
							print $trace['class'].".";
						}
						print $trace['function'];
						print "\\n";
					}
				}
			}
			else
			{
				print "Stack trace is not available\\n";
			}
			print "\");</script>";
			break;
		case "html" :
			if ( function_exists( "debug_backtrace" ) )
			{
				$info = debug_backtrace( );
				print "-- Backtrace --<br/><i>";
				foreach ( $info as $trace )
				{
					if ( $trace['function'] != "_internalerrorhandler" && $trace['file'] != __FILE__ )
					{
						print $trace['file'];
						print "(".$trace['line']."): ";
						if ( $trace['class'] != "" )
						{
							print $trace['class'].".";
						}
						print $trace['function'];
						print "<br/>";
					}
				}
				print "</i>";
			}
			else
			{
				print "<i>Stack trace is not available</i><br/>";
			}
			break;
		case "text" :
			break;
		}
		if ( function_exists( "debug_backtrace" ) )
		{
			$info = debug_backtrace( );
			$this->logdata .= "-- Backtrace --\n";
			foreach ( $info as $trace )
			{
				if ( $trace['function'] != "_internalerrorhandler" && $trace['file'] != __FILE__ )
				{
					$this->logdata .= addslashes( $trace['file'] );
					$this->logdata .= "(".$trace['line']."): ";
					if ( $trace['class'] != "" )
					{
						$this->logdata .= $trace['class'].".";
					}
					$this->logdata .= $trace['function']."\n";
				}
			}
		}
		else
		{
			$this->logdata .= "Stack trace is not available";
		}
		$this->logFile( $this->logdata );
	}

	function raiseError( $msg, $no = E_USER_WARNING, $type = "label" )
	{
		global $_LANG_SYS;
		global $_Error_vars;
		if ( $type == "str" )
		{
			trigger_error( $msg, E_USER_WARNING );
		}
		else if ( $type == "label" )
		{
			if ( !empty( $_Error_vars ) )
			{
				extract( $_Error_vars, EXTR_PREFIX_SAME, "cms_" );
			}
			$msg = $_LANG_SYS[$msg];
			eval( "\$msg = \"{$msg}\";" );
			trigger_error( $msg, E_USER_WARNING );
		}
	}

	function logFile( $data )
	{
		global $SYS_CONFIG;
		if ( $SYS_CONFIG['enable_error_log'] == FALSE )
		{
			return TRUE;
		}
		$filename = "error.".date( "Ymd" ).".log.php";
		$data = "- - - - - - - - - - - - - - - - ".date( "Y-m-d H:i:s" )."- - - - - - - - - - - - - - - - \n".$data."\n";
		if ( is_writable( SYS_PATH."sysdata/logs" ) )
		{
			$filename = SYS_PATH."sysdata/logs/".$filename;
		}
		else
		{
			$filename = SYS_PATH."sysdata/".$filename;
		}
		if ( !file_exists( $filename ) && ( $handle = fopen( $filename, "a" ) ) )
		{
			fwrite( $handle, "<?php exit('Access Denied!'); ?>\n" );
			fclose( $handle );
		}
		if ( $handle = fopen( $filename, "a" ) )
		{
			fwrite( $handle, $data."\n" );
			fclose( $handle );
		}
	}

	function addVar( $key, $var )
	{
		global $_Error_vars;
		$_Error_vars[$key] = $var;
	}

	function flushVar( )
	{
		$_Error_vars = "";
	}

}

?>
