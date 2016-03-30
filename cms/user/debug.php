<?php

	/**
     * Very stupid function that will hopefully replace all my
     * impossible-to-find-prints-in-22000-lines of code :P
     *
     * @param $params Whatever you'd like to print
     */

     /**
      * set this to 'false' to disable all debugging output
      */
	define( "DEBUG_ENABLED", false );

	function _debug( $params )
    {
    	if( DEBUG_ENABLED ) {
			if( function_exists("debug_backtrace")) {
        		$info = debug_backtrace();
            	$last = $info[0];
				$line = $last["file"].":".$last["line"];
				if( is_array($params)) {
					print($line.":");
					print_r($params);
					print("<br/>");
				}
				else 
					print($line.":".$params."<br/>");
        	}
            else {
            	print($params."<br/>");
            }
        }

        return true;
    }
?>
