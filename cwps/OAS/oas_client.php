<?php
require_once( "SoapOAS.class.php" );
$CWPS_Address = "http://localhost/cmsphp5/cwps/soap.php";
$TransactionAccessKey = "abc";
$oas = new SoapOAS( $CWPS_Address );
$oas->setTransferEncrypt( TRUE );
$oas->setOASID( "22" );
$oas->setTransactionAccessKey( $TransactionAccessKey );
$oas->setLog( TRUE );
$oas->setLogFile( "oas.log.".date( "Y-m-d" ).".txt" );
$oas->setTransactionID( time( ) );
$Action = "Login";
$params = array( "UserName" => "hawking", "Password" => "a", "Ip" => "127.0.0.1" );
$oas->setDataEncode( FALSE );
$return = $oas->call( $Action, $params );
if ( $return === FALSE )
{
	echo "Error Code:".$oas->getErrorCode( )."<HR>";
	print_r( $oas->getResponse( ) );
}
else
{
	echo "OK!";
	print_r( $return );
}
$oas->setTransactionID( time( ) );
$Action = "updateMoney";
$params = array( "UserID" => "12", "Operator" => "-", "Money" => "10" );
$return = $oas->call( $Action, $params );
if ( $return === FALSE )
{
	echo "Error Code:".$oas->getErrorCode( )."<HR>";
	print_r( $oas->getResponse( ) );
}
else
{
	echo "OK!";
	print_r( $return );
}
$oas->setTransactionID( time( ) );
$Action = "ActiveSession";
$params = array( "sId" => "8a1814b918e49d7ab8b50be06755bc0c" );
$return = $oas->call( $Action, $params );
print_r( $return );
$Action = "QueryUserSession";
$params = array( "sId" => "8a1814b918e49d7ab8b50be06755bc0c", "Ip" => "127.0.0.1" );
$return = $oas->call( $Action, $params );
print_r( $return );
?>
