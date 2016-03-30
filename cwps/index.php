<?php
class Debug
{

	var $starttime = 0;
	var $pretime = 0;
	var $endtime = 0;
	var $node = array( );

	function startTimer( )
	{
		$mtime = microtime( );
		$mtime = explode( " ", $mtime );
		$mtime = $mtime[1] + $mtime[0];
		$this->starttime = $mtime;
		$this->pretime = $mtime;
	}

	function debugNode( $node_name )
	{
		$mtime = microtime( );
		$mtime = explode( " ", $mtime );
		$mtime = $mtime[1] + $mtime[0];
		$endtime = $mtime;
		$totaltime = round( $endtime - $this->pretime, 5 );
		$this->node[] = array(
			"name" => $node_name,
			"time" => $totaltime
		);
		$this->pretime = $endtime;
	}

	function endTimer( )
	{
		$mtime = microtime( );
		$mtime = explode( " ", $mtime );
		$mtime = $mtime[1] + $mtime[0];
		$endtime = $mtime;
		$totaltime = round( $endtime - $this->starttime, 5 );
		$this->endtime = $endtime;
		return $totaltime;
	}

}

error_reporting( E_ALL ^ E_NOTICE );
if ( substr( phpversion( ), 0, 1 ) == 5 )
{
	define( "PHP_VERSION_5", true );
	@ini_set( "zend.ze1_compatibility_mode", "1" );
}
else
{
	define( "PHP_VERSION_5", false );
}
define( "DS", "/" );
define( "ROOT_PATH", ".".DS );
define( "INCLUDE_PATH", ROOT_PATH."include".DS );
define( "LIB_PATH", ROOT_PATH."lib".DS );
define( "CLS_PATH", ROOT_PATH."classes".DS );
define( "CLASS_PATH", ROOT_PATH."classes".DS );
define( "STRUTS_DIR", LIB_PATH."kStruts".DS );
define( "TMP_DIR", ROOT_PATH."tmp".DS );
define( "KTPL_DIR", LIB_PATH."kTemplate".DS );
define( "KDB_DIR", LIB_PATH."kDB".DS );
define( "IN_SYS", true );
define( "SYS_VERSION", "CWPS 1.6" );
define( "BUILD_VERSION", "1.6.20070816" );
set_magic_quotes_runtime( 0 );
$Debug = new Debug( );
$Debug->startTimer( );
require_once( INCLUDE_PATH."functions.php" );
require_once( ROOT_PATH."config.php" );
require_once( ROOT_PATH."struts-config.xml.php" );
require_once( LIB_PATH."file.class.php" );
require_once( LIB_PATH."data.class.php" );
require_once( LIB_PATH."Error.class.php" );
require_once( STRUTS_DIR."kStruts.class.php" );
$Error = new Error( $SYS_ENV['errorReports'] );
$IN = parse_incoming( );
if ( !$IN[referer] )
{
	$referer = _addslashes( $_SERVER['HTTP_REFERER'] );
}
else
{
	$referer = $IN[referer];
}
$SYS_ENV['language'] = empty( $SYS_ENV['language'] ) ? $STRUTS_CONFIG['message-resources']['language-package'] : $SYS_ENV['language'];

define( "LANG_PATH", ROOT_PATH.$STRUTS_CONFIG['message-resources']['language-dir'].DS );
foreach ( $STRUTS_CONFIG['message-resources']['sys-messages'] as $var )
{
	include_once( LANG_PATH.$SYS_ENV['language'].DS.$var );
}
$Struts = new kStruts( $STRUTS_CONFIG, $IN );
$Struts->tpl_assign( "version", SYS_VERSION );
$Struts->doing( );
?>
