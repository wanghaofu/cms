<?php
class OAS
{

	var $sId = NULL;
	var $fromCWPS = false;
	var $cookiepath = "";
	var $cookiedomain = "";
	var $session = array( );
	var $guest_session = array
	(
		"UserID" => "0",
		"UserName" => "Guest",
		"Password" => "",
		"GroupName" => "Guest",
		"GroupID" => 1,
		"Credit" => 0
	);
	var $timestamp = "";
	var $table_session = "";

	function OAS( &$OAS_SETTING )
	{
		global $plugin_table;
		$this->table_session = $plugin_table['oas']['sessions'];
		$this->setting =& $OAS_SETTING;
		$this->cookie_name_sid = $this->setting['CookiePre']."sid";
		$this->sId = isset( $_GET['sId'] ) ? $_GET['sId'] : isset( $_POST['sId'] ) ? $_POST['sId'] : isset( $_SESSION['sId'] ) ? $_SESSION['sId'] : $_COOKIE[$this->cookie_name_sid];
		$this->Ip = $IN['IP_ADDRESS'];
		$this->OnlineHold = $OAS_SETTING['OnlineHold'];
		$this->cookiepath = $OAS_SETTING['CookiePath'];
		$this->cookiedomain = $OAS_SETTING['CookieDomain'];
		$this->CheckIP = $OAS_SETTING['CheckIP'];
		$this->timestamp = time( );
		if ( substr( $this->sId, 0, 6 ) == "CWPS::" )
		{
			$this->sId = substr( $this->sId, 6 );
			$this->fromCWPS = true;
			if ( !isset( $_COOKIE[$this->cookie_name_sid] ) )
			{
				setcookie( $this->cookie_name_sid, $this->sId, $this->timestamp + $this->OnlineHold, $this->cookiepath, $this->cookiedomain );
			}
		}
		$this->session = $this->guest_session;
		$this->init( );
		if ( $this->isLogin( ) )
		{
			$this->ActiveCWPSSession( $this->setting['CWPS_SessionActiveTime'] );
		}
	}

	function init( )
	{
		global $plugin_table;
		global $db;
		if ( !empty( $this->sId ) )
		{
			$check_ip_sql = $this->CheckIP ? " AND Ip='{$this->Ip}'" : "";
			$result = $db->getRow( "SELECT * FROM {$this->table_session} WHERE sId='{$this->sId}'".$check_ip_sql );
			if ( empty( $result['sId'] ) )
			{
				$this->session = $this->guest_session;
				$this->clearCookies( );
			}
			else
			{
				$this->session = $result;
				$this->session['SessionData'] = unserialize( $result['SessionData'] );
				$this->activeSession( );
				return true;
			}
		}
		if ( !$this->isLogin( ) )
		{
			$this->tryLogin( $this->sId, $this->Ip );
		}
	}

	function activeSession( )
	{
		global $db;
		global $table;
		$sql = "UPDATE {$this->table_session} SET RunningTime='".time( )."' WHERE  sId='".$this->sId."'";
		if ( $db->query( $sql ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function registerSession( $result )
	{
		global $table;
		global $db;
		$time = time( );
		$resultSession = $db->getRow( "SELECT * FROM {$this->table_session}  where sId='".$this->sId."' AND Ip='{$this->Ip}'" );
		if ( !empty( $resultSession['sId'] ) )
		{
			$sql = "update {$this->table_session} set `UserName`='{$result['UserName']}', `UserID`='{$result['UserID']}', `GroupID`='{$result['GroupID']}', `LogInTime`='{$time}', `RunningTime`='{$time}', SessionData='".$result[SessionData]."' where sId='".$this->sId."' AND Ip='{$this->Ip}'";
			if ( $db->query( $sql ) )
			{
				$this->session = array_merge( $resultSession, $result );
				setcookie( $this->cookie_name_sid, $this->sId, $this->timestamp + $this->OnlineHold, $this->cookiepath, $this->cookiedomain );
				return true;
			}
		}
		$this->sId = $result[sId];
		$this->session = $result;
		$this->session['sId'] = $this->sId;
		$sql = "INSERT INTO {$this->table_session} (`sId`, `UserName`, `UserID`, `GroupID`, `LogInTime`, `RunningTime`, `Ip`, `SessionData`) VALUES ('{$this->sId}', '{$result['UserName']}', '{$result['UserID']}', '{$result['GroupID']}', '{$time}', '{$time}', '{$this->Ip}', '".$result[SessionData]."' )";
		if ( $db->query( $sql ) )
		{
			setcookie( $this->cookie_name_sid, $this->sId, $this->timestamp + $this->OnlineHold, $this->cookiepath, $this->cookiedomain );
			return true;
		}
		else
		{
			return false;
		}
	}

	function clearRubbishSession( )
	{
		global $db;
		global $table;
		$cut_off_stamp = $this->timestamp - $this->OnlineHold;
		$db->query( "DELETE FROM {$this->table_session} WHERE RunningTime < {$cut_off_stamp}" );
	}

	function clearCookies( )
	{
		setcookie( $this->cookie_name_sid, "", $this->timestamp - 31536000, $this->cookiepath, $this->cookiedomain );
	}

	function isLogin( )
	{
		if ( empty( $this->session['UserID'] ) )
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	function ActiveCWPSSession( $activeTime )
	{
		if ( time( ) - $this->session['CWPS_SessionActiveTime'] < $activeTime )
		{
			return true;
		}
		require_once( LIB_PATH."SoapOAS.class.php" );
		$oas = new SoapOAS( $this->setting['CWPS_Address'] );
		$oas->setTransactionAccessKey( $this->setting['CWPS_TransactionAccessKey'] );
		$oas->setReqCharset( CHARSET );
		$oas->setRespCharset( CHARSET );
		$oas->doLog = false;
		$oas->logFile = "oas.log.".date( "Y-m-d" ).".txt";
		$oas->setTransactionID( time( ) );
		$Action = "ActiveSession";
		$params = array(
			"sId" => $this->sId
		);
		$return = $oas->call( $Action, $params );
		if ( $return === false )
		{
			$this->session = $this->guest_session;
			return false;
		}
		else
		{
			$this->session['CWPS_SessionActiveTime'] = time( );
			return true;
		}
	}

	function tryLogin( $sId, $ip )
	{
		if ( empty( $sId ) )
		{
			return false;
		}
		require_once( LIB_PATH."SoapOAS.class.php" );
		$oas = new SoapOAS( $this->setting['CWPS_Address'] );
		$oas->setTransactionAccessKey( $this->setting['CWPS_TransactionAccessKey'] );
		$oas->doLog = false;
		$oas->logFile = "oas.log.".date( "Y-m-d" ).".txt";
		$oas->setReqCharset( CHARSET );
		$oas->setRespCharset( CHARSET );
		$oas->setTransactionID( time( ) );
		$Action = "QueryUserSession";
		$params = array(
			"sId" => $sId,
			"Ip" => $ip
		);
		$return = $oas->call( $Action, $params );
		if ( $return === false )
		{
			$this->session = $this->guest_session;
			return false;
		}
		else
		{
			$this->session = $return;
			$this->syncUser( $return );
			$this->registerSession( $this->session );
			$this->session['CWPS_SessionActiveTime'] = time( );
			return true;
		}
	}

	function syncUser( &$_info )
	{
		global $table;
		global $db;
		global $plugin_table;
		if ( empty( $_info['UserID'] ) )
		{
			return false;
		}
		$result = $db->getRow( "select UserID from {$plugin_table['oas']['user']} where UserID='{$_info['UserID']}'" );
		if ( !empty( $result['UserID'] ) )
		{
			return true;
		}
		else
		{
			$db->query( "Replace into {$plugin_table['oas']['user']} (`UserID`,`UserName`) VALUES ('{$_info['UserID']}', '{$_info['UserName']}')" );
		}
	}

	function login( $referer = "" )
	{
		$this->goCWPS( $this->setting['PageInterface']['Login'], $referer );
	}

	function logout( $referer = "" )
	{
		global $db;
		$this->clearCookies( );
		$sql = "DELETE FROM {$this->table_session} WHERE sId = '".$this->sId."' AND Ip ='".$this->Ip."'";
		$db->query( $sql );
		$this->goCWPS( $this->setting['PageInterface']['Logout'], $referer );
	}

	function goPageInterface( $_interface, $_referer )
	{
		$this->goCWPS( $this->setting['PageInterface']['Portal']."?".$_SERVER['QUERY_STRING'], $_referer );
	}

	function isLoginCWPS( )
	{
		$this->goCWPS( $this->setting['PageInterface']['IsLogin'] );
	}

	function goCWPS( $url, $referer = "" )
	{
		if ( empty( $referer ) )
		{
			$port = $_SERVER['SERVER_PORT'] == 80 ? "" : $_SERVER['SERVER_PORT'];
			$referer = "http://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
		}
		$pos = strpos( $url, "?" );
		if ( $pos === false )
		{
			$url = $url."?&referer=OAS::".urlencode( $referer );
		}
		else
		{
			$url = $url."&referer=OAS::".urlencode( $referer );
		}
		header( "Location: {$url}" );
		exit( );
	}

}

?>
