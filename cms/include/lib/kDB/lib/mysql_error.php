<?php


if ( !defined( "IN_SYS" ) )
{
	exit( "Access Denied" );
}
$timestamp = time( );
$errmsg = "";
$dberror = $this->error( );
$dberrno = $this->errno( );
if ( $dberrno == 1114 )
{
	echo "<html>\r\n<head><title>Max onlines reached</title></head>\r\n<body>\r\n<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"500\" height=\"90%\" align=\"center\" style=\"font-family: Verdana, Tahoma;font-size: 9px;color: #000000\">\r\n<tr><td height=\"50%\">&nbsp;</td></tr><tr><td valign=\"middle\" align=\"center\" bgcolor=\"#EAEAEA\">\r\n<br><b style=\"font-size: 11px;\">Forum onlines reached the upper limit</b><br><br><br>Sorry, the num";
	echo "ber of online visitors has reached the upper limit.<br>Please wait for someone else going offline or visit us in idle hours.<br><br></td>\r\n</tr><tr><td height=\"50%\">&nbsp;</td></tr></table>\r\n</body>\r\n</html>\r\n";
	exit( );
}
else
{
	if ( $message )
	{
		$errmsg = "<b>SYS info</b>: {$message}\n\n";
	}
	$errmsg .= "<b>Time</b>: ".gmdate( "Y-n-j g:ia", $timestamp + $timeoffset * 3600 )."\n";
	$errmsg .= "<b>Script</b>: ".$GLOBALS[PHP_SELF]."\n\n";
	if ( $sql )
	{
		$errmsg .= "<b>SQL</b>: ".htmlspecialchars( $sql )."\n";
	}
	$errmsg .= "<b>Error</b>:  {$dberror}\n";
	$errmsg .= "<b>Errno.</b>:  {$dberrno}";
	trigger_error( $errmsg, E_USER_ERROR );
}
?>
