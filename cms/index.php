<?php
require_once( dirname( __FILE__ )."/config.php" );
if ( !file_exists( "./sysdata/install.lock" ) && file_exists( "./install2.php" ) )
{
	header( "Location: ./install2.php" );
}
else
{
	header( "Location: ./".ADMIN_NAME."/index.php" );
}
?>
