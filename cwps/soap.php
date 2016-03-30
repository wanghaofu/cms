<?php
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
if ( DIRECTORY_SEPARATOR == "\\" )
{
	define( "DS", "\\\\" );
}
else
{
	define( "DS", "/" );
}
define( "ROOT_PATH", dirname( __FILE__ ).DS );
define( "INCLUDE_PATH", ROOT_PATH."include".DS );
define( "LIB_PATH", ROOT_PATH."lib".DS );
define( "CLS_PATH", ROOT_PATH."classes".DS );
define( "STRUTS_DIR", LIB_PATH."kStruts".DS );
define( "TMP_DIR", ROOT_PATH."tmp".DS );
define( "KTPL_DIR", LIB_PATH."kTemplate".DS );
define( "KDB_DIR", LIB_PATH."kDB".DS );
define( "IN_SYS", true );
define( "BUILD_VERSION", "CWPS 1.0beta" );
set_magic_quotes_runtime( 0 );
require_once( ROOT_PATH."config.php" );
require_once( LIB_PATH."file.class.php" );
require_once( LIB_PATH."data.class.php" );
require_once( LIB_PATH."CWPS_SOAP_Server.class.php" );
require_once( INCLUDE_PATH."functions.php" );
require_once( KDB_DIR."kDB.php" );
$db = new kDB( $db_config['db_driver'] );
$db->connect( $db_config );
define( "SOAP_INTERFACE_PATH", CLS_PATH."soapInterface".DS );
$soap = new CWPS_SOAP_Server( );
import( "com.member.admin.biz.SoapAdmin" );
import( "com.member.admin.biz.OAS" );
import( "com.member.admin.biz.Setting" );
$soapadmin = new SoapAdmin( );
$oas = new OAS( );
$setting = new Setting( );
$soap->TransactionAccessKey = $setting->Setting['TransactionAccessKey'];
foreach ( $soapadmin->loadSoapAction( ) as $var )
{
	$soap->register( $var );
}
foreach ( $oas->loadSoapOAS( ) as $oasid => $var )
{
	$soap->addOAS( $oasid, $var['IP'], $var['CWPSPassword'] );
}
$soap->service( );
?>
