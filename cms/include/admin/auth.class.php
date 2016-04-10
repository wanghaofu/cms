<?php
class Auth
{
	var $sId='';
	var $sIp='';
	var $session = array();
	var $module='';
	var $action='';

	function Auth( $params = NULL )
	{
		if ( isset( $params['sId'] ) )
		{
			$this->sId = $params['sId'];
		}
		else
		{
			$this->sId = isset( $_GET['sId'] ) ? $_GET['sId'] : $_POST['sId'];
		}
		if ( isset( $params['sIp'] ) )
		{
			$this->sIp = $params['sIp'];
		}
		else
		{
			$this->sIp = $IN['IP_ADDRESS'];
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
		global $_SESSION;
		$this->clearRubbishSession( );
		$sql = "SELECT * FROM {$table->admin_sessions} WHERE  sId='".$this->sId."'";
		$result = $db->getRow( $sql );
		if ( $result['sId'] != "" )
		{
			if ( !empty( $result['IpSecurity'] ) && ( $result['sIpAddress'] != $this->sIp && $this->sIp != $_SERVER['SERVER_ADDR'] ) )
			{
				return false;
			}
			else
			{
				$this->updateSession( );
				$result['accessCate'] = explode( ",", $result[sGAccessCate] );
				$this->session = $result;
				$this->session['Auth'] = unserialize( $result['sGAuthData'] );
				$this->Auth =& $this->session[Auth];
				$this->uId = $result[sUId];
				$this->uName = $result[sUserName];
				$_SESSION = unserialize( $result['sData'] );
				return true;
			}
		}
		else
		{
			return false;
		}
	}

	function addBlockIP( $IP )
	{
		global $db;
		global $table;
		global $_SESSION;
		$db->query( "INSERT INTO {$table->block_ip} (`IP`, `ExpireTime`, `Reason`) VALUES ('".$IP."', ".( time( ) + $SYS_ENV['LoginTryTime'] * 60 ).", '".$_LANG_ADMIN['LoginTryTimeOut']."')" );
	}

	function isAdmin( )
	{
		if ( $this->session['sGIsAdmin'] == 1 )
		{
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
		$sql = "UPDATE {$table->admin_sessions} SET sRunningTime='".time( )."' WHERE  sId='".$this->sId."'";
		if ( $db->query( $sql ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function isBanned( )
	{
		global $db;
		global $table;
		$db->query( "DELETE FROM {$table->block_ip} WHERE ExpireTime < ".time( ) );
		$result = $db->getRow( "SELECT Id FROM {$table->block_ip} where IP='".$this->sIp."'" );
		if ( !empty( $result[Id] ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	//登陆并写入登陆信息
	function login( $username, $password, $IpSecurity = 0 )
	{
		global $db;
		global $table;
		$password = md5( $password );
		$sql = "SELECT u.*, g.* FROM {$table->user} u LEFT JOIN {$table->group} g ON g.gId=u.uGId WHERE  u.uName='".$db->escape_string( $username )."' and u.uPass='{$password}'";
		$result = $db->getRow( $sql );
		if ( $result['uId'] != "" )
		{
			$IpSecurity = empty( $IpSecurity ) ? 0 : 1;
			$this->registerSession( $result, $IpSecurity );
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
		global $SYS_ENV;
		$SYS_ENV['sessionTimeout'] = intval( $SYS_ENV['sessionTimeout'] );
		$SYS_ENV['sessionTimeout'] = $SYS_ENV['sessionTimeout'] < 1 ? 5 : $SYS_ENV['sessionTimeout'];
		$time_out = empty( $SYS_ENV['sessionTimeout'] ) ? 7200 : $SYS_ENV['sessionTimeout'] * 60;
		$cut_off_stamp = time( ) - $time_out;
		$db->query( "DELETE FROM {$table->admin_sessions} WHERE sRunningTime < {$cut_off_stamp}" );
	}

	function getPublishAuth( $gPublishAuth )
	{
		$gPublishAuth = substr( $gPublishAuth, 2, -2 );
		$publishAuth = array(
			"NodeList" => array( ),
			"NodeExtraPublish" => array( ),
			"NodeSetting" => array( ),
			"ContentRead" => array( ),
			"ContentWrite" => array( ),
			"ContentApprove" => array( ),
			"ContentPublish" => array( ),
			"AuthInherit" => array( )
		);
		$gAuth = explode( ",", $gPublishAuth );
		if ( !empty( $gAuth ) )
		{
			foreach ( $gAuth as $key => $var )
			{
				$info = publishAuthAdmin::getinfo( $var );
				$publishAuth[NodeList] = array_merge( $publishAuth[NodeList], explode( ",", substr( $info[NodeList], 2, -2 ) ) );
				$publishAuth[NodeExtraPublish] = array_merge( $publishAuth[NodeExtraPublish], explode( ",", substr( $info[NodeExtraPublish], 2, -2 ) ) );
				$publishAuth[NodeSetting] = array_merge( $publishAuth[NodeSetting], explode( ",", substr( $info[NodeSetting], 2, -2 ) ) );
				$publishAuth[ContentRead] = array_merge( $publishAuth[ContentRead], explode( ",", substr( $info[ContentRead], 2, -2 ) ) );
				$publishAuth[ContentWrite] = array_merge( $publishAuth[ContentWrite], explode( ",", substr( $info[ContentWrite], 2, -2 ) ) );
				$publishAuth[ContentApprove] = array_merge( $publishAuth[ContentApprove], explode( ",", substr( $info[ContentApprove], 2, -2 ) ) );
				$publishAuth[ContentPublish] = array_merge( $publishAuth[ContentPublish], explode( ",", substr( $info[ContentPublish], 2, -2 ) ) );
				$publishAuth[AuthInherit] = array_merge( $publishAuth[AuthInherit], explode( ",", substr( $info[AuthInherit], 2, -2 ) ) );
			}
			$publishAuth[NodeList] = array_unique( $publishAuth[NodeList] );
			$publishAuth[NodeExtraPublish] = array_unique( $publishAuth[NodeExtraPublish] );
			$publishAuth[NodeSetting] = array_unique( $publishAuth[NodeSetting] );
			$publishAuth[ContentRead] = array_unique( $publishAuth[ContentRead] );
			$publishAuth[ContentWrite] = array_unique( $publishAuth[ContentWrite] );
			$publishAuth[ContentApprove] = array_unique( $publishAuth[ContentApprove] );
			$publishAuth[ContentPublish] = array_unique( $publishAuth[ContentPublish] );
			$publishAuth[AuthInherit] = array_unique( $publishAuth[AuthInherit] );
		}
		return $publishAuth;
	}
//重要登陆信息 以及权限验证信息注册 否则后边将失败
	function registerSession( $result, $IpSecurity = 0 )
	{
		global $db;
		global $table;
		global $SYS_AUTH;
		global $invalid_info;
		licenseverify( );
		$LicenseVerifyResult = $db->getRow( "SELECT varValue from {$table->sys} WHERE varName='openTask' " ); 
		if ( !$LicenseVerifyResult )
		{
			$db->query( "Insert into {$table->sys} VALUES('','openTask','start') " );
			$LicenseVerifyResult['varValue'] = "start";
			licenseverify( 1 );
//			exit( $invalid_info );  //推出操作
		}
		else if ( $LicenseVerifyResult['varValue'] == "start" )
		{
			licenseverify( 1 );
//			exit( $invalid_info );
		}
		$this->clearRubbishSession( );
		$this->sId = $this->makeSessionKey( );
		$time = time( );
		$Auth = $result;
		$sGAuthData = serialize( $Auth );
		$sql = "INSERT INTO {$table->admin_sessions} (`sId`, `sIpAddress`, `sUserName`, `sUId`, `sGId`,  `sGAuthData`,  `sGIsAdmin`, `sLogInTime`, `sRunningTime`,`IpSecurity`) VALUES ('".$this->sId."', '".$this->sIp."', '".$result[uName]."', '".$result[uId]."', '".$result[uGId]."', '".$sGAuthData."', '".$result[gIsAdmin]."', '{$time}', '{$time}', '{$IpSecurity}')";
		//此处必须执行
		if ( $db->query( $sql ) )
		{
			return true;
		}
		else
		{
			return false;
		}
		return true;
	}

	function logout( )
	{
		global $db;
		global $table;
		$sql = "DELETE FROM {$table->admin_sessions} WHERE sId = '".$this->sId."' AND sIpAddress ='".$this->sIp."'";
		if ( $db->query( $sql ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function canAccess( $action )
	{
		if ( $this->isAdmin( ) )
		{
			return true;
		}
		else if ( $this->Auth[$action] == 1 )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function canManagePublish( $o, $NodeID )
	{
		global $iWPC;
		if ( $this->isAdmin( ) )
		{
			return true;
		}
		switch ( $o )
		{
		case "content_header" :
		case "content_list" :
		case "content_header" :
		case "list" :
		case "IsRecordExists" :
			if ( in_array( $NodeID, $this->Auth['publishAuth']['NodeList'] ) )
			{
				return true;
			}
			else
			{
				$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
				$ParentNodeID = explode( "%", $NodeInfo['ParentNodeID'] );
				foreach ( $ParentNodeID as $var )
				{
					if ( in_array( $var, $this->Auth['publishAuth']['NodeList'] ) && in_array( $var, $this->Auth['publishAuth']['AuthInherit'] ) )
					{
						return true;
					}
				}
				$this->returnMsg = "permission_deny_NodeList";
				return false;
			}
		case "extrapublish" :
			if ( in_array( $NodeID, $this->Auth['publishAuth']['NodeExtraPublish'] ) )
			{
				return true;
			}
			else
			{
				$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
				$ParentNodeID = explode( "%", $NodeInfo[ParentNodeID] );
				foreach ( $ParentNodeID as $var )
				{
					if ( in_array( $var, $this->Auth[publishAuth][NodeExtraPublish] ) && in_array( $var, $this->Auth[publishAuth][AuthInherit] ) )
					{
						return true;
					}
				}
				$this->returnMsg = "permission_deny_NodeExtraPublish";
				return false;
			}
		case "setting" :
			if ( in_array( $NodeID, $this->Auth[publishAuth][NodeSetting] ) )
			{
				return true;
			}
			else
			{
				$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
				$ParentNodeID = explode( "%", $NodeInfo[ParentNodeID] );
				foreach ( $ParentNodeID as $var )
				{
					if ( in_array( $var, $this->Auth[publishAuth][NodeSetting] ) && in_array( $var, $this->Auth[publishAuth][AuthInherit] ) )
					{
						return true;
					}
				}
				$this->returnMsg = "permission_deny_NodeSetting";
				return false;
			}
		case "view" :
		case "viewLinkState" :
		case "refresh" :
			if ( in_array( $NodeID, $this->Auth[publishAuth][ContentRead] ) )
			{
				return true;
			}
			else
			{
				$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
				$ParentNodeID = explode( "%", $NodeInfo[ParentNodeID] );
				foreach ( $ParentNodeID as $var )
				{
					if ( in_array( $var, $this->Auth[publishAuth][ContentRead] ) && in_array( $var, $this->Auth[publishAuth][AuthInherit] ) )
					{
						return true;
					}
				}
				$this->returnMsg = "permission_deny_ContentRead";
				return false;
			}
		case "content_editor_frameset" :
		case "content_editor_header" :
		case "add" :
		case "add_submit" :
		case "edit" :
		case "edit_submit" :
		case "del" :
		case "cut" :
		case "createLink" :
		case "createIndexLink" :
		case "copy" :
		case "topIt" :
		case "topIt_submit" :
		case "pinkIt" :
		case "pinkIt_submit" :
		case "sortIt" :
		case "sortIt_submit" :
		case "destroy" :
		case "restore" :
		case "empty_recycle_bin" :
		case "editContentLink" :
		case "node_resync" :
		case "planPublish" :
		case "picker_content" :
		case "siteRefresh" :
		case "contribution" :
			if ( in_array( $NodeID, $this->Auth[publishAuth][ContentWrite] ) )
			{
				return true;
			}
			else
			{
				$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
				$ParentNodeID = explode( "%", $NodeInfo[ParentNodeID] );
				foreach ( $ParentNodeID as $var )
				{
					if ( in_array( $var, $this->Auth[publishAuth][ContentWrite] ) && in_array( $var, $this->Auth[publishAuth][AuthInherit] ) )
					{
						return true;
					}
				}
				$this->returnMsg = "permission_deny_ContentWrite";
				return false;
			}
		case "approve" :
			if ( in_array( $NodeID, $this->Auth[publishAuth][ContentApprove] ) )
			{
				return true;
			}
			else
			{
				$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
				$ParentNodeID = explode( "%", $NodeInfo[ParentNodeID] );
				foreach ( $ParentNodeID as $var )
				{
					if ( in_array( $var, $this->Auth[publishAuth][ContentApprove] ) && in_array( $var, $this->Auth[publishAuth][AuthInherit] ) )
					{
						return true;
					}
				}
				$this->returnMsg = "permission_deny_ContentApprove";
				return false;
			}
		case "publish" :
		case "unpublish" :
			if ( in_array( $NodeID, $this->Auth[publishAuth][ContentPublish] ) )
			{
				return true;
			}
			else
			{
				$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
				$ParentNodeID = explode( "%", $NodeInfo[ParentNodeID] );
				foreach ( $ParentNodeID as $var )
				{
					if ( in_array( $var, $this->Auth[publishAuth][ContentPublish] ) && in_array( $var, $this->Auth[publishAuth][AuthInherit] ) )
					{
						return true;
					}
				}
				$this->returnMsg = "permission_deny_ContentPublish";
				return false;
			}
		default :
			$this->returnMsg = "permission_deny_NodeUnknownAction";
			return false;
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
