<?php


class Auth
{

	var $sId = NULL;
	var $sIp = NULL;
	var $session = array( );
	var $module = NULL;
	var $action = NULL;

	function Auth( $params )
	{
		if ( isset( $params['sId'] ) )
		{
			$this->sId = $params['sId'];
		}
		if ( isset( $params['sIp'] ) )
		{
			$this->sIp = $params['sIp'];
		}
		if ( isset( $params['module'] ) )
		{
			$this->module = $params['module'];
		}
		if ( isset( $params['action'] ) )
		{
			$this->action = $params['action'];
		}
	}

	function isLogin( )
	{
		global $db;
		global $table;
		$this->clearRubbishSession( );
		$sql = "SELECT * FROM {$table->sessions} WHERE  sId='".$this->sId."' and sIpAddress='".$this->sIp."'";
		$result = $db->getRow( $sql );
		if ( $result['sId'] != "" )
		{
			$this->updateSession( );
			$this->session = $result;
			$this->uId = $result[sUId];
		
			$this->uName = $result[sUserName];
			return true;
		}
		else
		{
			return false;
		}
	}

	function getSession( )
	{
		if ( $this->isLogin( ) )
		{
			return $this->session;
		}
		else
		{
			return false;
		}
	}

	function updateSession( )
	{
		global $db;
		global $table;
		$sql = "UPDATE {$table->sessions} SET sRunningTime='".time( )."' WHERE  sId='".$this->sId."' and sIpAddress='".$this->sIp."'";
		if ( $db->query( $sql ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function login( $username, $password )
	{
		global $db;
		global $table;
		$password = md5( $password );
		$sql = "SELECT * FROM {$table->user} WHERE  uName='".$db->escape_string( $username )."' and uPass='{$password}'";
		$result = $db->getRow( $sql );
		if ( $result['uId'] != "" )
		{
			$this->registerSession( $result );
			return true;
		}
		else
		{
			return false;
		}
	}

	function chpassword( $password, $newpassword )
	{
		global $db;
		global $table;
		$password = md5( $password );
		$sql = "SELECT * FROM {$table->user} WHERE  uId='".$this->session[sUId]."' and  uPass='{$password}'";
		$result = $db->getRow( $sql );
		$newpassword = md5( $newpassword );
		if ( $result['uId'] != "" )
		{
			$sql = "UPDATE {$table->user} SET uPass='{$newpassword}' WHERE  uId='".$this->session[sUId]."' and  uPass='{$password}'";
			if ( $db->query( $sql ) )
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	function makeSessionKey( )
	{
		list( $usec, $sec ) = explode( " ", microtime( ) );
		return md5( ( double )$usec + ( double )$sec.mt_rand( 0, 100 ) );
	}

	function clearRubbishSession( )
	{
		global $db;
		global $table;
		$cut_off_stamp = time( ) - 7200;
		$db->query( "DELETE FROM {$table->sessions} WHERE sRunningTime < {$cut_off_stamp}" );
	}

	function registerSession( $result )
	{
		global $db;
		global $table;
		global $SYS_AUTH;
		$this->clearRubbishSession( );
		$this->sId = $this->makeSessionKey( );
		$time = time( );
		$sql = "INSERT INTO {$table->sessions} (`sId`, `sIpAddress`, `sUserName`, `sUId`, `sLogInTime`, `sRunningTime`) VALUES ('".$this->sId."', '".$this->sIp."', '".$result[uName]."', '".$result[uId]."',  '{$time}', '{$time}')";
		if ( $db->query( $sql ) )
		{
			$sql = "UPDATE {$table->user} SET LastLoginDate='".time( )."' WHERE uId={$result[uId]}";
			$db->query( $sql );
			return true;
		}
		else
		{
			return false;
		}
	}

	function logout( )
	{
		global $db;
		global $table;
		$sql = "DELETE FROM {$table->sessions} WHERE sId = '".$this->sId."' AND sIpAddress ='".$this->sIp."'";
		if ( $db->query( $sql ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function canAccess( &$module, &$action )
	{
		global $SYS_AUTH;
		$this->opFilter( $module );
		$this->opFilter( $action );
		$op = $module."_".$action;
		$this->op = $op;
		if ( !isset( $SYS_AUTH[$op] ) )
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	function opFilter( &$string )
	{
		$string = str_replace( "\"", "", $string );
		$string = str_replace( "'", "", $string );
		$string = str_replace( "`", "", $string );
		$string = str_replace( "\\", "", $string );
		$string = str_replace( "\\/", "", $string );
		$string = str_replace( "\$", "", $string );
		$string = str_replace( "^", "", $string );
	}

}

?>
