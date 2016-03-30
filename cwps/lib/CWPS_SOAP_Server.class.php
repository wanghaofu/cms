<?php
define( "SOAP_OK", 0 );
define( "SOAP_UNKNOWN_ERROR", 1 );
define( "SOAP_LOGIC_ERROR", 2 );
define( "SOAP_OAS_IP_Invalid", 4000 );
define( "SOAP_TransactionAccessKey_Error", 4001 );
define( "SOAP_Action_Null", 4002 );
define( "SOAP_Action_NotRegistered", 4003 );
define( "SOAP_Action_NotExists", 4004 );
define( "SOAP_Response_TransactionID_Error", 4005 );
define( "SOAP_TransactionOASID_Error", 4006 );
define( "SOAP_TransactionOASUID_Error", 4007 );
define( "SOAP_DB_ERROR", 9000 );
class CWPS_SOAP_Server extends iData
{

	var $SyncOrderRelationReqTpl = "";
	var $SyncOrderRelationRespTpl = "";
	var $Request = array( );
	var $Response_hRet = 1;
	var $Response_FeatureStr = "";
	var $Response_Return = "";
	var $TransactionAccessKey = "";
	var $TransactionAccessKeyFileName = "";
	var $ActionReqData = "";
	var $Version = "1.5";
	var $Valid_OAS_IP = array( );
	var $Valid_Action = array( );
	var $DataEncode = true;
	var $Response_Charset = "utf8";
	var $transferEncrypt = false;

	function CWPS_SOAP_Server( )
	{
		$this->soapInterfacePath = SOAP_INTERFACE_PATH;
		$this->Response_hRet = SOAP_UNKNOWN_ERROR;
		$this->ActionRespTpl = "<?xml version=\"1.0\" encoding=\"utf-8\" ?><SOAP-ENV:Envelope  xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:SOAP-ENC=\"http://schemas.xmlsoap.org/soap/encoding/\"><SOAP-ENV:Header>    <TransactionID xmlns=\"http://www.sesoe.com/passport/schemas/\">{TransactionID}</TransactionID>    <TransactionEncrypt xmlns=\"http://www.sesoe.com/passport/schemas/\">{TransactionEncrypt}</TransactionEncrypt></SOAP-ENV:Header><SOAP-ENV:Body><ActionResp xmlns=\"http://www.sesoe.com/passport/schemas/\"><Version>".$this->Version."</Version>"."<Encoding>{Encoding}</Encoding>"."<hRet>{hRet}</hRet>"."<RespCharset>{RespCharset}</RespCharset>"."<FeatureStr>{FeatureStr}</FeatureStr>"."<Return>{Return}</Return>"."</ActionResp>"."{SOAP-ENV:Fault}"."</SOAP-ENV:Body>"."</SOAP-ENV:Envelope>";
		$this->SOAPFaultTpl = "<SOAP-ENV:Fault><faultcode>{faultcode}</faultcode><faultstring>{faultstring}</faultstring><detail>{detail}</detail></SOAP-ENV:Fault>";
	}

	function service( )
	{
		$handle = fopen( "php://input", "rb" );
		$contents = "";
		do
		{
			$data = fread( $handle, 8192 );
			if ( strlen( $data ) == 0 )
			{
				break;
			}
			$contents .= $data;
		} while ( true );
		fclose( $handle );
		if ( get_magic_quotes_gpc( ) )
		{
			$this->ActionReqData = stripslashes( urldecode( $contents ) );
		}
		else
		{
			$this->ActionReqData = urldecode( $contents );
		}
		$this->parseActionReq( $this->ActionReqData );
		$this->ActionReqOASIp = $_SERVER['REMOTE_ADDR'];
		$this->run( );
		$response = $this->ActionRespTpl;
		$response = str_replace( "{TransactionID}", $this->Request['TransactionID'], $response );
		$response = str_replace( "{hRet}", $this->Response_hRet, $response );
		$response = str_replace( "{FeatureStr}", $this->Response_FeatureStr, $response );
		$response = str_replace( "{Return}", $this->Response_Return, $response );
		$response = str_replace( "{SOAP-ENV:Fault}", $this->Response_SOAP_Fault, $response );
		if ( $this->transferEncrypt )
		{
			$response = str_replace( "{TransactionEncrypt}", "1", $response );
		}
		else
		{
			$response = str_replace( "{TransactionEncrypt}", "0", $response );
		}
		$response = str_replace( "{RespCharset}", $this->Response_Charset, $response );
		if ( $this->DataEncode )
		{
			$response = str_replace( "{Encoding}", 1, $response );
		}
		else
		{
			$response = str_replace( "{Encoding}", 0, $response );
		}
		header( "Content-Type: text/xml; charset=utf-8" );
		print $response;
		exit( );
	}

	function run( )
	{
		if ( empty( $this->OASUID ) && !empty( $this->OASID ) )
		{
			if ( empty( $this->Valid_OAS_INFO[$this->OASID] ) )
			{
				$this->Response_hRet = SOAP_TransactionOASID_Error;
				$this->Response_FeatureStr = "ActionReq TransactionOASID Error";
				return false;
			}
			else if ( $this->Request['TransactionAccessKey'] != $this->Valid_OAS_INFO[$this->OASID]['password'] )
			{
				$this->Response_hRet = SOAP_TransactionAccessKey_Error;
				$this->Response_FeatureStr = "ActionReq TransactionAccessKey Error";
				return false;
			}
			else if ( !empty( $this->Valid_OAS_INFO[$this->OASID]['ip'] ) && strpos( $this->Valid_OAS_INFO[$this->OASID]['ip'], $this->ActionReqOASIp ) === false )
			{
				$this->Response_hRet = SOAP_OAS_IP_Invalid;
				$this->Response_FeatureStr = "ActionReq OAS IP is not valid";
				return false;
			}
		}
		else if ( !empty( $this->OASUID ) )
		{
			if ( empty( $this->Valid_OAS_INFO[$this->OASUID] ) )
			{
				$this->Response_hRet = SOAP_TransactionOASUID_Error;
				$this->Response_FeatureStr = "ActionReq TransactionOASUID Error";
				return false;
			}
			else if ( $this->Request['TransactionAccessKey'] != $this->Valid_OAS_INFO[$this->OASUID]['password'] )
			{
				$this->Response_hRet = SOAP_TransactionAccessKey_Error;
				$this->Response_FeatureStr = "ActionReq TransactionAccessKey Error";
				return false;
			}
			else if ( !empty( $this->Valid_OAS_INFO[$this->OASID]['ip'] ) && strpos( $this->Valid_OAS_INFO[$this->OASUID]['ip'], $this->ActionReqOASIp ) === false )
			{
				$this->Response_hRet = SOAP_OAS_IP_Invalid;
				$this->Response_FeatureStr = "ActionReq OAS IP is not valid";
				return false;
			}
		}
		else if ( !empty( $this->Valid_OAS_IP ) && !isset( $this->Valid_OAS_IP[$this->ActionReqOASIp] ) )
		{
			$this->Response_hRet = SOAP_OAS_IP_Invalid;
			$this->Response_FeatureStr = "ActionReq OAS IP is not valid";
			return false;
		}
		$action = "SOAP_".$this->Request['Action'];
		if ( empty( $this->Request['Action'] ) )
		{
			$this->Response_hRet = SOAP_Action_Null;
			$this->Response_FeatureStr = "ActionReq Action is null";
			return false;
		}
		else if ( !in_array( $this->Request['Action'], $this->Valid_Action ) )
		{
			$this->Response_hRet = SOAP_Action_NotRegistered;
			$this->Response_FeatureStr = "ActionReq Action ".$this->Request['Action']." is not registered";
			return false;
		}
		else if ( !file_exists( $this->soapInterfacePath.$action.".php" ) )
		{
			$this->Response_hRet = SOAP_Action_NotExists;
			$this->Response_FeatureStr = "ActionReq Action is not exists";
			return false;
		}
		$this->initDbResult( );
		include_once( $this->soapInterfacePath.$action.".php" );
		$action( $this, $this->Request['Params'] );
	}

	function addResponseElement( $key, $value )
	{
		if ( is_array( $value ) )
		{
			$this->Response_Return .= "<{$key}>".$this->array2element( $value )."</{$key}>";
		}
		else if ( $this->DataEncode )
		{
			$this->Response_Return .= "<{$key}>".base64_encode( $this->soap_encode( $value ) )."</{$key}>";
		}
		else
		{
			$this->Response_Return .= "<{$key}>".$this->soap_encode( $value )."</{$key}>";
		}
	}

	function array2element( $data )
	{
		foreach ( $data as $key => $var )
		{
			if ( is_array( $var ) )
			{
				$return .= "<{$key}>".$this->array2element( $var )."</{$key}>";
			}
			else if ( $this->DataEncode )
			{
				$return .= "<{$key}>".base64_encode( $this->soap_encode( $var ) )."</{$key}>";
			}
			else
			{
				$return .= "<{$key}>".$this->soap_encode( $var )."</{$key}>";
			}
		}
		return $return;
	}

	function setEncode( $_encode = false )
	{
		$this->DataEncode = $_encode;
	}

	function register( $action )
	{
		$this->Valid_Action[] = $action;
	}

	function addOAS( $OASID, $ip, $pass )
	{
		if ( strpos( $ip, "," ) !== false )
		{
			foreach ( explode( ",", $ip ) as $var )
			{
				$this->Valid_OAS_IP[$var] = $pass;
			}
		}
		else
		{
			$this->Valid_OAS_IP[$ip] = $pass;
		}
		$this->Valid_OAS_INFO[$OASID] = array(
			"ip" => $ip,
			"password" => $pass
		);
	}

	function setTransferEncrypt( $_how = true )
	{
		$this->transferEncrypt = $_how;
	}

	function soap_encode( $str )
	{
		if ( $this->transferEncrypt )
		{
			return cwps_encrypt( $str, $this->public_key );
		}
		return $str;
	}

	function soap_decode( $str )
	{
		if ( $this->transferEncrypt )
		{
			return cwps_decrypt( $str, $this->public_key );
		}
		return $str;
	}

	function parseActionReq( &$str )
	{
		preg_match( "/<TransactionEncrypt[^>]+>(.*)<\\/TransactionEncrypt>/isU", $str, $TransactionIDMatch );
		$this->transferEncrypt = empty( $TransactionIDMatch[1] ) ? false : true;
		preg_match( "/<TransactionOASID[^>]+>(.*)<\\/TransactionOASID>/isU", $str, $TransactionOASIDMatch );
		$this->OASID = $TransactionOASIDMatch[1];
		$this->public_key = $this->Valid_OAS_INFO[$this->OASID]['password'];
		if ( preg_match( "/<TransactionOASUID[^>]+>(.*)<\\/TransactionOASUID>/isU", $str, $TransactionOASUIDMatch ) )
		{
			$this->OASUID = $TransactionOASUIDMatch[1];
			$this->public_key = $this->Valid_OAS_INFO[$this->OASUID]['password'];
		}
		preg_match( "/<TransactionID[^>]+>(.*)<\\/TransactionID>/isU", $str, $TransactionIDMatch );
		$this->Request['TransactionID'] = $TransactionIDMatch[1];
		preg_match( "/<TransactionAccessKey[^>]+>(.*)<\\/TransactionAccessKey>/isU", $str, $TransactionAccessKeyMatch );
		$this->Request['TransactionAccessKey'] = $this->soap_decode( $TransactionAccessKeyMatch[1] );
		if ( preg_match( "/<ActionReq[^>]+>(.*)<\\/ActionReq>/isU", $str, $match ) )
		{
			preg_match( "/<Action>(.*)<\\/Action>/isU", $match[1], $ActionMatch );
			$this->Request['Action'] = $ActionMatch[1];
			preg_match( "/<Version>(.*)<\\/Version>/isU", $match[1], $VersionMatch );
			$this->Request['Version'] = $VersionMatch[1];
			preg_match( "/<Encoding>(.*)<\\/Encoding>/isU", $match[1], $EncodingMatch );
			$this->Request['Encoding'] = $EncodingMatch[1];
			preg_match( "/<ReqCharset>(.*)<\\/ReqCharset>/isU", $match[1], $EncodingMatch );
			$this->Request['ReqCharset'] = $EncodingMatch[1];
			preg_match( "/<RespCharset>(.*)<\\/RespCharset>/isU", $match[1], $EncodingMatch );
			$this->Request['RespCharset'] = $EncodingMatch[1];
			if ( preg_match( "/<Params>(.*)<\\/Params>/isU", $match[1], $ParamsMatch ) )
			{
				if ( preg_match_all( "/<([^>]+)>([^><]*)<\\/([^>]+)>/isU", $ParamsMatch[1], $matches ) )
				{
					foreach ( $matches[0] as $key => $var )
					{
						if ( $this->Request['Encoding'] == "1" )
						{
							$this->Request['Params'][$matches[1][$key]] = $this->soap_decode( base64_decode( $matches[2][$key] ) );
						}
						else
						{
							$this->Request['Params'][$matches[1][$key]] = $this->soap_decode( $matches[2][$key] );
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

	function getTransactionAccessKey( )
	{
		return $this->TransactionAccessKey;
	}

	function initDbResult( )
	{
		global $db;
		global $db_config;
		$dbversion = $db->getDbVersion( );
		if ( !empty( $dbversion ) )
		{
			if ( strtolower( $this->Request['ReqCharset'] ) == "utf-8" )
			{
				$this->Request['ReqCharset'] = "utf8";
			}
			if ( strtolower( $this->Request['RespCharset'] ) == "utf-8" )
			{
				$this->Request['RespCharset'] = "utf8";
			}
			$db->query( "SET character_set_client='".$this->Request['ReqCharset']."'" );
			$db->query( "SET character_set_connection='".$db_config['db_charset']."'" );
			$db->query( "SET character_set_results='".$this->Request['RespCharset']."'" );
			$this->Response_Charset = $this->Request['RespCharset'];
		}
		else
		{
			$this->Response_Charset = $db_config['db_charset'];
		}
	}

}

?>
