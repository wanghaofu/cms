<?php


class Error
{

	var $displayErrorMode = "text";
	var $mode = "";

	function Error( $params )
	{
		if ( $params['switch'] )
		{
			if ( isset( $params['mode'] ) )
			{
				$this->mode = $params['mode'];
			}
			if ( isset( $params['displayMode'] ) )
			{
				$this->displayErrorMode = $params['displayMode'];
			}
			switch ( $this->mode )
			{
			case 0 :
				error_reporting( 0 );
				break;
			case 1 :
				error_reporting( E_ERROR | E_WARNING | E_PARSE );
				break;
			case 2 :
				error_reporting( E_ALL ^ E_NOTICE );
				break;
			case 3 :
				error_reporting( E_ALL );
				break;
			case 4 :
				error_reporting( E_ALL ^ E_NOTICE );
				break;
			default :
				set_error_handler( array(
					$this,
					"handler"
				) );
				break;
			}
		}
	}

	function handler( $no, $str, $file, $line, $ctx )
	{
		if ( $no == E_NOTICE || $no == 2048 )
		{
			return false;
		}
		switch ( $this->displayErrorMode )
		{
		case "js" :
			print "<script>alert(\"";
			print "\\nException message: ".$str."\\nError code: ".$no."\\n";
			break;
		case "html" :
			print "<br/><b>Exception message</b>: ".$str."<br/><b>Error code</b>: ".$no."<br/>";
			break;
		case "text" :
		default :
			print "[Exception message]: ".$str."\n[Error code] : ".$no."\n";
			break;
		}
		$this->_printStackTrace( );
	}

	function _printStackTrace( )
	{
		switch ( $this->displayErrorMode )
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
		default :
			if ( function_exists( "debug_backtrace" ) )
			{
				$info = debug_backtrace( );
				print "-- Backtrace --\n";
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
						print "\n";
					}
				}
			}
			else
			{
				print "Stack trace is not available\n";
				break;
			}
		}
	}

	function raiseError( $msg, $no = E_USER_WARNING )
	{
		trigger_error( $msg, $no );
	}

}

?>
