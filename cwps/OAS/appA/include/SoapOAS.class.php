<?php
//	\$h[A-Za-z0-9]{13}\s*=\s*time\(\s*\);(\s|\t)*\n*
//	\$h[A-Za-z0-9]{13}\s*=\s*[0-9]{10};(\s|\t)*\n*
// 	if\s*\(\s*\$h[a-zA-Z0-9]+\s*<\s*\$h[a-zA-Z0-9]+\s*\)(\s|\t)*\n*
//  
// \{\n*(\t|\s)*header\(\s*\"Location:\s*http:\/\/www\.cmsware\.org\/invalid\/\?expire=[0-9]{10}\"\s*\);\n*(\t|\s)*\}(\s|\t)*\n*

class SoapOAS
{

	var $server_address = NULL;
	var $Version = "1.1";
	var $SoapDataforSend = "";
	var $DataEncode = true;
	var $TransactionID = "";
	var $TransactionAccessKey = "";
	var $doLog = false;
	var $logFile = "oas.log.txt";
	var $Response = array( );
	var $ReqCharset = "utf8";
	var $RespCharset = "utf8";
	var $errorCode = 0;

	function SoapOAS( $server_address )
	{
		$this->server_address = $server_address;
		$this->ActionReqTpl = "<?xml version=\"1.0\" encoding=\"utf-8\" ?><SOAP-ENV:Envelope  xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:SOAP-ENC=\"http://schemas.xmlsoap.org/soap/encoding/\"><SOAP-ENV:Header>    <TransactionID xmlns=\"http://www.localhost.com/passport/schemas/\">{TransactionID}</TransactionID>    <TransactionAccessKey xmlns=\"http://www.localhost.com/passport/schemas/\">{TransactionAccessKey}</TransactionAccessKey></SOAP-ENV:Header><SOAP-ENV:Body><ActionReq xmlns=\"http://www.localhost.com/passport/schemas/\"><Version>".$this->Version."</Version>"."<Encoding>{Encoding}</Encoding>"."<Action>{Action}</Action>"."<Params>{Params}</Params>"."<ReqCharset>{ReqCharset}</ReqCharset>"."<RespCharset>{RespCharset}</RespCharset>"."</ActionReq>"."</SOAP-ENV:Body>"."</SOAP-ENV:Envelope>";
	}

	function call( $action, $params = NULL )
	{
		$this->SoapDataforSend = $this->ActionReqTpl;
		$this->SoapDataforSend = str_replace( "{TransactionID}", $this->TransactionID, $this->SoapDataforSend );
		$this->SoapDataforSend = str_replace( "{TransactionAccessKey}", $this->TransactionAccessKey, $this->SoapDataforSend );
		$this->SoapDataforSend = str_replace( "{Action}", $action, $this->SoapDataforSend );
		$this->SoapDataforSend = str_replace( "{ReqCharset}", $this->ReqCharset, $this->SoapDataforSend );
		$this->SoapDataforSend = str_replace( "{RespCharset}", $this->RespCharset, $this->SoapDataforSend );
		if ( !empty( $params ) && is_array( $params ) )
		{
			foreach ( $params as $key => $var )
			{
				if ( $this->DataEncode )
				{
					$tmp .= "<{$key}>".base64_encode( $var )."</{$key}>\n";
				}
				else
				{
					$tmp .= "<{$key}>".$var."</{$key}>\n";
				}
			}
		}
		if ( $this->DataEncode )
		{
			$this->SoapDataforSend = str_replace( "{Encoding}", 1, $this->SoapDataforSend );
		}
		else
		{
			$this->SoapDataforSend = str_replace( "{Encoding}", 0, $this->SoapDataforSend );
		}
		$this->SoapDataforSend = str_replace( "{Params}", $tmp, $this->SoapDataforSend );
		return $this->sendSoapData( $this->SoapDataforSend );
	}

	function setTransactionID( $TransactionID )
	{
		$this->TransactionID = $TransactionID;
	}

	function setTransactionAccessKey( $TransactionAccessKey )
	{
		$this->TransactionAccessKey = $TransactionAccessKey;
	}

	function setDataEncode( $_encode = true )
	{
		$this->DataEncode = $_encode;
	}

	function setLog( $_dolog = true )
	{
		$this->doLog = $_dolog;
	}

	function setLogFile( $_logfile )
	{
		$this->logFile = $_logfile;
	}

	function setReqCharset( $_ReqCharset )
	{
		$this->ReqCharset = $_ReqCharset;
	}

	function setRespCharset( $_RespCharset )
	{
		$this->RespCharset = $_RespCharset;
	}

	function getErrorCode( )
	{
		return $this->errorCode;
	}

	function getResponse( )
	{
		return $this->Response;
	}

	function sendSoapData( &$SoapData )
	{
		$info = parse_url( $this->server_address );
		$port = empty( $info['port'] ) ? 80 : $info['port'];
		$host = $info[host];
		$path = $info[path];
		$msg = "POST {$path} HTTP/1.0\r\n"."Host: {$host} \r\n"."Content-Type: application/x-www-form-urlencoded\r\n"."Content-Length: ".strlen( $SoapData )."\r\n\r\n";
		$ActionRespData = "";
		$f = fsockopen( $host, $port, $errno, $errstr, 1 );
		if ( $f )
		{
			fputs( $f, $msg.$SoapData );
			while ( !feof( $f ) )
			{
				$ActionRespData .= fread( $f, 32000 );
			}
			fclose( $f );
		}
		if ( $this->doLog )
		{
			$this->LogData( $SoapData."\n\n\n\n".$ActionRespData );
		}
		$this->parseActionResp( $ActionRespData );
		if ( $this->Response['TransactionID'] != $this->TransactionID )
		{
			$this->errorCode = SOAP_Response_TransactionID_Error;
			return false;
		}
		else if ( $this->Response['hRet'] != SOAP_OK )
		{
			$this->errorCode = $this->Response['hRet'];
			return false;
		}
		else
		{
			return $this->Response['Return'];
		}
	}

	function parseActionResp( &$str )
	{
		preg_match( "/<TransactionID[^>]+>(.*)<\\/TransactionID>/isU", $str, $TransactionIDMatch );
		$this->Response['TransactionID'] = $TransactionIDMatch[1];
		if ( preg_match( "/<ActionResp[^>]+>(.*)<\\/ActionResp>/isU", $str, $match ) )
		{
			preg_match( "/<Version>(.*)<\\/Version>/isU", $match[1], $VersionMatch );
			$this->Response['Version'] = $VersionMatch[1];
			preg_match( "/<hRet>(.*)<\\/hRet>/isU", $match[1], $hRetMatch );
			$this->Response['hRet'] = $hRetMatch[1];
			preg_match( "/<FeatureStr>(.*)<\\/FeatureStr>/isU", $match[1], $FeatureStrMatch );
			$this->Response['FeatureStr'] = $FeatureStrMatch[1];
			preg_match( "/<Encoding>(.*)<\\/Encoding>/isU", $match[1], $EncodingMatch );
			$this->Response['Encoding'] = $EncodingMatch[1];
			if ( preg_match( "/<Return>(.*)<\\/Return>/isU", $match[1], $ReturnMatch ) )
			{
				if ( preg_match_all( "/<([^>]+)>([^><]*)<\\/([^>]+)>/isU", $ReturnMatch[1], $matches ) )
				{
					foreach ( $matches[0] as $key => $var )
					{
						if ( $this->Response['Encoding'] == "1" )
						{
							$this->Response['Return'][$matches[1][$key]] = base64_decode( $matches[2][$key] );
						}
						else
						{
							$this->Response['Return'][$matches[1][$key]] = $matches[2][$key];
						}
					}
				}
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	function LogData( $data )
	{
		if ( $handle = fopen( $this->logFile, "a" ) )
		{
			fwrite( $handle, $data."  ".date( "Y-m-d H:i:s" )."\n" );
			fclose( $handle );
		}
	}

	function unserialize( $_data )
	{
		return unserialize( $_data );
	}

	function error( )
	{
		trigger_error( "ActionReq Error : [".$this->errorCode."] ".$this->Response['FeatureStr'], E_USER_WARNING );
	}

}

define( "SOAP_OK", 0 );
define( "SOAP_UNKNOWN_ERROR", 1 );
define( "SOAP_LOGIC_ERROR", 2 );
define( "SOAP_OAS_IP_Invalid", 4000 );
define( "SOAP_TransactionAccessKey_Error", 4001 );
define( "SOAP_Action_Null", 4002 );
define( "SOAP_Action_NotRegistered", 4003 );
define( "SOAP_Action_NotExists", 4004 );
define( "SOAP_Response_TransactionID_Error", 4005 );
define( "SOAP_DB_ERROR", 9000 );
?>
