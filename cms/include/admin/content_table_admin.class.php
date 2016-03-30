<?php


if ( PHP_VERSION_5 )
{
	include_once( dirname( __FILE__ )."/content_table_admin.class.php5.php" );
}
else
{
	include_once( dirname( __FILE__ )."/content_table_admin.class.php4.php" );
}
?>
