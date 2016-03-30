<?php


class userAdmin extends iData
{

	function add( )
	{
		global $db;
		global $table;
		global $sys;
		$sql = "SELECT max( `uId` ) as uId FROM {$table->user} WHERE `uId` < 1000 ";
		$result = $db->getRow( $sql );
		if ( !empty( $result['uId'] ) )
		{
			$this->addData( "uId", intval( $result['uId'] ) + 1 );
		}
		$this->addData( "CreationUserID", $sys->session['sUId'] );
		if ( $this->dataInsert( $table->user ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function del( $uId )
	{
		global $table;
		$which = "uId";
		if ( $this->dataDel( $table->user, $which, $uId, $method = "=" ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function userExist( $username )
	{
		global $db;
		global $table;
		$sql = "SELECT uId FROM {$table->user}  WHERE uName='{$username}' ";
		$result = $db->getRow( $sql );
		if ( !empty( $result[uId] ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function update( $uId )
	{
		global $table;
		$where = "where uId=".$uId;
		if ( $this->dataUpdate( $table->user, $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function getAll( )
	{
		global $table;
		global $db;
		$sql = "SELECT u.*,g.gName FROM {$table->user} u,{$table->group} g  WHERE  g.gId=u.uGId ";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getAllByPermission( )
	{
		global $table;
		global $db;
		global $sys;
		if ( $sys->session['sGIsAdmin'] == 1 )
		{
			$sql = "SELECT u.*,g.gName,u2.uName as CreationUserName FROM {$table->user} u left join {$table->group} g ON g.gId=u.uGId left join {$table->user} u2 ON u.CreationUserID=u2.uId";
		}
		else
		{
			$sql = "SELECT u.*,g.gName,u2.uName as CreationUserName FROM {$table->user} u left join {$table->group} g ON g.gId=u.uGId left join {$table->user} u2 ON u.CreationUserID=u2.uId  WHERE  u.CreationUserID='".$sys->session['sUId']."'";
		}
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	static function getInfo( $uId, $field = "*" )
	{
		global $table;
		global $db;
		$sql = "SELECT {$field} FROM {$table->user}  WHERE uId='{$uId}'  ";
		$result = $db->getRow( $sql );
		if ( $field == "*" )
		{
			return $result;
		}
		else
		{
			return $result[$field];
		}
	}

	function Counter( $uId, $field, $op = "+", $type = 0, $NodeInfo = "" )
	{
		global $table;
		global $db;
		global $db_config;
		if ( $type == 0 )
		{
			$sql = "UPDATE {$table->user} SET `{$field}`={$field}{$op}1 WHERE uId='{$uId}'";
			$db->query( $sql );
		}
		else if ( $type == 1 )
		{
			$ContributionID = $uId;
			$table_name = $db_config['table_pre'].$db_config['table_contribution_pre']."_".$NodeInfo[TableID];
			$sql = "SELECT OwnerID FROM {$table_name}  WHERE ContributionID='{$ContributionID}'";
			$result = $db->getRow( $sql );
			$sql = "UPDATE {$table->user} SET `{$field}`={$field}{$op}1 WHERE uId='{$result['OwnerID']}'";
			$db->query( $sql );
		}
	}

	function canAccess( $uId, $mode )
	{
		global $group;
		global $sys;
		if ( !isset( $group ) )
		{
			require_once( INCLUDE_PATH."admin/groupAdmin.class.php" );
			$group = new groupAdmin( );
		}
		switch ( $mode )
		{
		case "Read" :
			$gInfo = $group->getInfo( $sys->session['sGId'] );
			if ( !( $gInfo['canMakeU'] == 1 ) )
			{
				break;
			}
			return true;
			break;
		}
		return false;
	}

	function PermissionDetector( &$IN )
	{
		global $_LANG_ADMIN;
		global $sys;
		global $group;
		if ( $sys->isAdmin( ) )
		{
			return true;
		}
		if ( !isset( $group ) )
		{
			require_once( INCLUDE_PATH."admin/groupAdmin.class.php" );
			$group = new groupAdmin( );
		}
		switch ( $IN[o] )
		{
		case "view" :
		case "add" :
		case "edit" :
			if ( $this->canAccess( "", "Read" ) )
			{
				break;
			}
			goback( "user_permission_deny_read" );
			break;
		case "add_submit" :
			if ( !$group->canAccess( "", "MakeU" ) )
			{
				goback( "user_permission_deny_make" );
			}
			if ( $group->canAccess( $IN[uGId], "Admin" ) )
			{
				break;
			}
			$GInfo = $group->getInfo( $IN[uGId] );
			goback( sprintf( $_LANG_ADMIN['user_permission_deny_mksonuser'], $GInfo['gName'] ), 1 );
			break;
		case "edit_submit" :
			$uInfo = $this->getInfo( $IN['uId'] );
			if ( $uInfo['CreationUserID'] != $sys->session['sUId'] )
			{
				goback( sprintf( $_LANG_ADMIN['user_permission_deny_write'], $uInfo['uName'] ), 1 );
			}
			if ( $group->canAccess( $IN[uGId], "Admin" ) )
			{
				break;
			}
			$GInfo = $this->getInfo( $IN[uGId] );
			goback( sprintf( $_LANG_ADMIN['user_permission_deny_mksonuser'], $GInfo['gName'] ), 1 );
			break;
		case "del" :
			$uInfo = $this->getInfo( $IN['uId'] );
			if ( !( $uInfo['CreationUserID'] != $sys->session['sUId'] ) )
			{
				break;
			}
			goback( sprintf( $_LANG_ADMIN['user_permission_deny_write'], $uInfo['uName'] ), 1 );
			break;
		}
	}

}

?>
