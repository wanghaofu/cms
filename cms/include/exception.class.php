<?php


class cmsware_Exception
{

	var $_exceptionString = NULL;
	var $_exceptionCode = NULL;

	function cmsware_Exception( $exceptionString, $exceptionCode = 0 )
	{
		$this->_exceptionString = $exceptionString;
		$this->_exceptionCode = $exceptionCode;
	}

	function cmsware_throw( )
	{
		switch ( Error_Display )
		{
		case "js" :
			print "<script>alert(\"";
			print "\\nException message: ".$this->_exceptionString."\\nError code: ".$this->_exceptionCode."\\n";
			break;
		case "html" :
			print "<br/><b>Exception message</b>: ".$this->_exceptionString."<br/><b>Error code</b>: ".$this->_exceptionCode."<br/>";
			break;
		case "text" :
			break;
		case "" :
			break;
		}
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
	}

}

function _internalErrorHandler( $errorCode, $errorString )
{
	$exc = new cmsware_Exception( $errorString, $errorCode );
	if ( $errorCode != E_NOTICE && $errorCode != 2048 )
	{
		$exc->cmsware_throw( );
	}
}

function cmsware_throw( $exception )
{
	$exception->cmsware_throw( );
}

function cmsware_catch( $exception )
{
	print "Exception catched!";
}

$old_error_handler = set_error_handler( "_internalErrorHandler" );
?>
