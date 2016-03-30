<?php
/*********************/
/*                   */
/*  Version : 5.1.0  */
/*  Author  : RM     */
/*  Comment : 071223 */
/*                   */
/*********************/

class useradmin extends idata
{

	function add( )
	{
		global $table;
		global $sys;
		global $db;
		$sql = "SELECT max( `uId` ) as uId FROM {$table->user} WHERE `uId` < 1000 ";
		$result = $db->getrow( $sql );
		if ( !empty( $result['uId'] ) )
		{
			$this->adddata( "uId", intval( $result['uId'] ) + 1 );
		}
		$this->adddata( "CreationUserID", $sys->session['sUId'] );
		if ( $this->datainsert( $table->user ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function del( $uId )
	{
		global $table;
		$which = "uId";
		if ( $this->datadel( $table->user, $which, $uId, $method = "=" ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function userexist( $username )
	{
		global $db;
		global $table;
		$sql = "SELECT uId FROM {$table->user}  WHERE uName='{$username}' ";
		$result = $db->getrow( $sql );
		if ( !empty( $result[uId] ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function update( $uId )
	{
		global $table;
		$where = "where uId=".$uId;
		if ( $this->dataupdate( $table->user, $where ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function getall( )
	{
		global $table;
		global $db;
		$sql = "SELECT u.*,g.gName FROM {$table->user} u,{$table->group} g  WHERE  g.gId=u.uGId ";
		$result = $db->execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->movenext( );
		}
		return $data;
	}

	function getallbypermission( )
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
		$result = $db->execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->movenext( );
		}
		return $data;
	}

	function getinfo( $uId, $field = "*" )
	{
		global $table;
		global $db;
		$sql = "SELECT {$field} FROM {$table->user}  WHERE uId='{$uId}'  ";
		$result = $db->getrow( $sql );
		if ( $field == "*" )
		{
			return $result;
		}
		else
		{
			return $result[$field];
		}
	}

	function counter( $uId, $field, $op = "+", $type = 0, $NodeInfo = "" )
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
			$result = $db->getrow( $sql );
			$sql = "UPDATE {$table->user} SET `{$field}`={$field}{$op}1 WHERE uId='{$result['OwnerID']}'";
			$db->query( $sql );
		}
	}

	function canaccess( $uId, $mode )
	{
		global $group;
		global $sys;
		if ( !isset( $group ) )
		{
			require_once( INCLUDE_PATH."admin/groupAdmin.class.php" );
			$group = new groupadmin( );
		}
		switch ( $mode )
		{
		case "Read" :
			$gInfo = $group->getinfo( $sys->session['sGId'] );
			if ( !( $gInfo['canMakeU'] == 1 ) )
			{
				break;
			}
			return TRUE;
		}
		return FALSE;
	}

	function permissiondetector( &$IN )
	{
		global $_LANG_ADMIN;
		global $sys;
		global $group;
		if ( $sys->isadmin( ) )
		{
			return TRUE;
		}
		if ( !isset( $group ) )
		{
			require_once( INCLUDE_PATH."admin/groupAdmin.class.php" );
			$group = new groupadmin( );
		}
		switch ( $IN[o] )
		{
		case "view" :
		case "add" :
		case "edit" :
			if ( $this->canaccess( "", "Read" ) )
			{
				break;
			}
			goback( "user_permission_deny_read" );
			break;
		case "add_submit" :
			if ( !$group->canaccess( "", "MakeU" ) )
			{
				goback( "user_permission_deny_make" );
			}
			if ( $group->canaccess( $IN[uGId], "Admin" ) )
			{
				break;
			}
			$GInfo = $group->getinfo( $IN[uGId] );
			goback( sprintf( $_LANG_ADMIN['user_permission_deny_mksonuser'], $GInfo['gName'] ), 1 );
			break;
		case "edit_submit" :
			$uInfo = $this->getinfo( $IN['uId'] );
			if ( $uInfo['CreationUserID'] != $sys->session['sUId'] )
			{
				goback( sprintf( $_LANG_ADMIN['user_permission_deny_write'], $uInfo['uName'] ), 1 );
			}
			if ( $group->canaccess( $IN[uGId], "Admin" ) )
			{
				break;
			}
			$GInfo = $this->getinfo( $IN[uGId] );
			goback( sprintf( $_LANG_ADMIN['user_permission_deny_mksonuser'], $GInfo['gName'] ), 1 );
			break;
		case "del" :
			$uInfo = $this->getinfo( $IN['uId'] );
			if ( !( $uInfo['CreationUserID'] != $sys->session['sUId'] ) )
			{
				break;
			}
			goback( sprintf( $_LANG_ADMIN['user_permission_deny_write'], $uInfo['uName'] ), 1 );
		}
	}

}

?>
