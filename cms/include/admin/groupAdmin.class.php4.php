<?php
/*********************/
/*                   */
/*  Version : 5.1.0  */
/*  Author  : RM     */
/*  Comment : 071223 */
/*                   */
/*********************/

class groupadmin extends idata
{

	function add( )
	{
		global $table;
		global $sys;
		$this->adddata( "CreationUserID", $sys->session['sUId'] );
		if ( $this->datainsert( $table->group ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function del( $gId )
	{
		global $table;
		$which = "gId";
		if ( $this->datadel( $table->group, $which, $gId, $method = "=" ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function update( $gId )
	{
		global $table;
		$where = "where gId=".$gId;
		if ( $this->dataupdate( $table->group, $where ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function getall( $ParentGID = "" )
	{
		global $table;
		global $db;
		if ( $ParentGID == "" )
		{
			$sql = "SELECT * FROM {$table->group}";
		}
		else
		{
			$sql = "SELECT * FROM {$table->group} where ParentGID='{$ParentGID}'";
		}
		$result = $db->execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->movenext( );
		}
		return $data;
	}

	function getallbypermissionread( )
	{
		global $table;
		global $db;
		global $sys;
		if ( $sys->session['sGIsAdmin'] == 1 )
		{
			$sql = "SELECT g.*, lg.gName as ParentGName,u.uName as CreationUserName FROM {$table->group} g left join {$table->group} lg ON g.ParentGID=lg.gId left join {$table->user} u ON g.CreationUserID=u.uId order by g.ParentGID, g.gId";
			$data[] = array( "gId" => 0 );
		}
		else
		{
			$sql = "SELECT g.*, lg.gName as ParentGName,u.uName as CreationUserName FROM {$table->group} g left join {$table->group} lg ON g.ParentGID=lg.gId left join {$table->user} u ON g.CreationUserID=u.uId where g.CreationUserID='".$sys->session['sUId']."' or g.gId='".$sys->session['sGId']."' order by g.ParentGID, g.gId ";
		}
		$result = $db->execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->movenext( );
		}
		return $data;
	}

	function getallbypermissionadmin( )
	{
		global $table;
		global $db;
		global $sys;
		if ( $sys->session['sGIsAdmin'] == 1 )
		{
			$sql = "SELECT g.*, lg.gName as ParentGName,u.uName as CreationUserName FROM {$table->group} g left join {$table->group} lg ON g.ParentGID=lg.gId left join {$table->user} u ON g.CreationUserID=u.uId order by g.ParentGID, g.gId";
			$data[] = array( "gId" => 0 );
		}
		else
		{
			$sql = "SELECT g.*, lg.gName as ParentGName,u.uName as CreationUserName FROM {$table->group} g left join {$table->group} lg ON g.ParentGID=lg.gId left join {$table->user} u ON g.CreationUserID=u.uId where g.CreationUserID='".$sys->session['sUId']."'  order by g.ParentGID, g.gId ";
		}
		$result = $db->execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->movenext( );
		}
		return $data;
	}

	function getinfo( $gId )
	{
		global $table;
		global $db;
		if ( $gId == 0 )
		{
			$result[gId] = 0;
			$result[gName] = "Root";
		}
		else
		{
			$sql = "SELECT * FROM {$table->group}  WHERE gId='{$gId}' ";
			$result = $db->getrow( $sql );
		}
		return $result;
	}

	function canaccess( $gId = "", $mode )
	{
		global $sys;
		if ( $sys->isadmin( ) )
		{
			return TRUE;
		}
		switch ( $mode )
		{
		case "Read" :
			if ( $gId == "" )
			{
				$gInfo = $this->getinfo( $sys->session['sGId'] );
				if ( $gInfo['canMakeG'] == 1 )
				{
					return TRUE;
				}
				return FALSE;
			}
			if ( $gId == 0 )
			{
				return FALSE;
			}
			$gInfo = $this->getinfo( $gId );
			if ( $gInfo['CreationUserID'] == $sys->session['sUId'] )
			{
				return TRUE;
			}
			if ( $gInfo['gId'] == $sys->session['sGId'] )
			{
				return TRUE;
			}
			return $this->canaccess( $gInfo['ParentGID'], $mode );
		case "Write" :
		case "Admin" :
			if ( $gId == "" )
			{
				return FALSE;
			}
			if ( $gId == 0 )
			{
				return FALSE;
			}
			$gInfo = $this->getinfo( $gId );
			if ( $gInfo['CreationUserID'] == $sys->session['sUId'] )
			{
				return TRUE;
			}
			return $this->canaccess( $gInfo['ParentGID'], $mode );
		case "Make" :
			$gInfo = $this->getinfo( $sys->session['sGId'] );
			if ( !( $gInfo['canMakeG'] == 1 ) )
			{
				break;
			}
			return TRUE;
			break;
		case "MakeU" :
			$gInfo = $this->getinfo( $sys->session['sGId'] );
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
		if ( $sys->isadmin( ) )
		{
			return TRUE;
		}
		switch ( $IN[o] )
		{
		case "view" :
		case "add" :
		case "edit" :
			if ( $this->canaccess( $IN['gId'], "Read" ) )
			{
				break;
			}
			$gInfo = $this->getinfo( $IN['gId'] );
			goback( sprintf( $_LANG_ADMIN['group_permission_deny_read'], $gInfo['gName'] ), 1 );
			break;
		case "add_submit" :
			if ( !$sys->isadmin( ) )
			{
				$IN[isAdmin] = 0;
				$IN[canChangePW] = 0;
				$IN[canNode] = 0;
				$IN[canTpl] = 0;
				$IN[canCollection] = 0;
			}
			if ( !$this->canaccess( $IN[ParentGID], "Read" ) )
			{
				$ParentGInfo = $this->getinfo( $IN[ParentGID] );
				goback( sprintf( $_LANG_ADMIN['group_permission_deny_mksongroup'], $ParentGInfo['gName'] ), 1 );
			}
			if ( $this->canaccess( "", "Make" ) )
			{
				break;
			}
			$ParentGInfo = $this->getinfo( $sys->session['sGId'] );
			goback( sprintf( $_LANG_ADMIN['group_permission_deny_make'], $ParentGInfo['gName'] ), 1 );
			break;
		case "edit_submit" :
			if ( !$sys->isadmin( ) )
			{
				$IN[isAdmin] = 0;
				$IN[canChangePW] = 0;
				$IN[canNode] = 0;
				$IN[canTpl] = 0;
				$IN[canCollection] = 0;
			}
			if ( !$this->canaccess( $IN[ParentGID], "Read" ) )
			{
				$ParentGInfo = $this->getinfo( $IN[ParentGID] );
				goback( sprintf( $_LANG_ADMIN['group_permission_deny_mksongroup'], $ParentGInfo['gName'] ), 1 );
			}
			if ( $this->canaccess( $IN[gId], "Write" ) )
			{
				break;
			}
			$GInfo = $this->getinfo( $IN[gId] );
			goback( sprintf( $_LANG_ADMIN['group_permission_deny_write'], $GInfo['gName'] ), 1 );
			break;
		case "del" :
			if ( !$this->canaccess( $IN[ParentGID], "Read" ) )
			{
				$ParentGInfo = $this->getinfo( $IN[ParentGID] );
				goback( sprintf( $_LANG_ADMIN['group_permission_deny_mksongroup'], $ParentGInfo['gName'] ), 1 );
			}
			if ( $this->canaccess( $IN[gId], "Write" ) )
			{
				break;
			}
			$GInfo = $this->getinfo( $IN[gId] );
			goback( sprintf( $_LANG_ADMIN['group_permission_deny_write'], $GInfo['gName'] ), 1 );
		}
	}

}

?>
