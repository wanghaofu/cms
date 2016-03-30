<?php


class groupAdmin extends iData
{

	function add( )
	{
		global $table;
		global $sys;
		$this->addData( "CreationUserID", $sys->session['sUId'] );
		if ( $this->dataInsert( $table->group ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function del( $gId )
	{
		global $table;
		$which = "gId";
		if ( $this->dataDel( $table->group, $which, $gId, $method = "=" ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function update( $gId )
	{
		global $table;
		$where = "where gId=".$gId;
		if ( $this->dataUpdate( $table->group, $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function getAll( $ParentGID = "" )
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
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getAllByPermissionRead( )
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
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getAllByPermissionAdmin( )
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
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getInfo( $gId )
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
			$result = $db->getRow( $sql );
		}
		return $result;
	}

	function canAccess( $gId = "", $mode )
	{
		global $sys;
		if ( $sys->isAdmin( ) )
		{
			return true;
		}
		switch ( $mode )
		{
		case "Read" :
			if ( $gId == "" )
			{
				$gInfo = $this->getInfo( $sys->session['sGId'] );
				if ( $gInfo['canMakeG'] == 1 )
				{
					return true;
				}
				return false;
			}
			else if ( $gId == 0 )
			{
				return false;
			}
			else
			{
				$gInfo = $this->getInfo( $gId );
				if ( $gInfo['CreationUserID'] == $sys->session['sUId'] )
				{
					return true;
				}
				if ( $gInfo['gId'] == $sys->session['sGId'] )
				{
					return true;
				}
				return $this->canAccess( $gInfo['ParentGID'], $mode );
			}
		case "Write" :
		case "Admin" :
			if ( $gId == "" )
			{
				return false;
			}
			else if ( $gId == 0 )
			{
				return false;
			}
			else
			{
				$gInfo = $this->getInfo( $gId );
				if ( $gInfo['CreationUserID'] == $sys->session['sUId'] )
				{
					return true;
				}
				return $this->canAccess( $gInfo['ParentGID'], $mode );
			}
		case "Make" :
			$gInfo = $this->getInfo( $sys->session['sGId'] );
			if ( !( $gInfo['canMakeG'] == 1 ) )
			{
				break;
			}
			return true;
			break;
		case "MakeU" :
			$gInfo = $this->getInfo( $sys->session['sGId'] );
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
		if ( $sys->isAdmin( ) )
		{
			return true;
		}
		switch ( $IN[o] )
		{
		case "view" :
		case "add" :
		case "edit" :
			if ( $this->canAccess( $IN['gId'], "Read" ) )
			{
				break;
			}
			$gInfo = $this->getInfo( $IN['gId'] );
			goback( sprintf( $_LANG_ADMIN['group_permission_deny_read'], $gInfo['gName'] ), 1 );
			break;
		case "add_submit" :
			if ( !$sys->isAdmin( ) )
			{
				$IN[isAdmin] = 0;
				$IN[canChangePW] = 0;
				$IN[canNode] = 0;
				$IN[canTpl] = 0;
				$IN[canCollection] = 0;
			}
			if ( !$this->canAccess( $IN[ParentGID], "Read" ) )
			{
				$ParentGInfo = $this->getInfo( $IN[ParentGID] );
				goback( sprintf( $_LANG_ADMIN['group_permission_deny_mksongroup'], $ParentGInfo['gName'] ), 1 );
			}
			if ( $this->canAccess( "", "Make" ) )
			{
				break;
			}
			$ParentGInfo = $this->getInfo( $sys->session['sGId'] );
			goback( sprintf( $_LANG_ADMIN['group_permission_deny_make'], $ParentGInfo['gName'] ), 1 );
			break;
		case "edit_submit" :
			if ( !$sys->isAdmin( ) )
			{
				$IN[isAdmin] = 0;
				$IN[canChangePW] = 0;
				$IN[canNode] = 0;
				$IN[canTpl] = 0;
				$IN[canCollection] = 0;
			}
			if ( !$this->canAccess( $IN[ParentGID], "Read" ) )
			{
				$ParentGInfo = $this->getInfo( $IN[ParentGID] );
				goback( sprintf( $_LANG_ADMIN['group_permission_deny_mksongroup'], $ParentGInfo['gName'] ), 1 );
			}
			if ( $this->canAccess( $IN[gId], "Write" ) )
			{
				break;
			}
			$GInfo = $this->getInfo( $IN[gId] );
			goback( sprintf( $_LANG_ADMIN['group_permission_deny_write'], $GInfo['gName'] ), 1 );
			break;
		case "del" :
			if ( !$this->canAccess( $IN[ParentGID], "Read" ) )
			{
				$ParentGInfo = $this->getInfo( $IN[ParentGID] );
				goback( sprintf( $_LANG_ADMIN['group_permission_deny_mksongroup'], $ParentGInfo['gName'] ), 1 );
			}
			if ( $this->canAccess( $IN[gId], "Write" ) )
			{
				break;
			}
			$GInfo = $this->getInfo( $IN[gId] );
			goback( sprintf( $_LANG_ADMIN['group_permission_deny_write'], $GInfo['gName'] ), 1 );
			break;
		}
	}

}

?>
