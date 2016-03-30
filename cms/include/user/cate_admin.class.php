<?php


class cate_admin extends iData
{

	function add( )
	{
		global $table;
		if ( $this->dataInsert( $table->cate ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function del( $CateID )
	{
		global $table;
		global $iWPC;
		global $db;
		global $sys;
		$sql = "UPDATE {$table->cate} SET `Disabled`=1 WHERE CateID={$CateID} AND OwnerID={$sys->uId}";
		if ( $db->query( $sql ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function update( $CateID )
	{
		global $table;
		global $iWPC;
		global $sys;
		$where = "where  OwnerID={$sys->uId} AND CateID=".$CateID;
		if ( $this->dataUpdate( $table->cate, $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function getCateInfo( $CateID )
	{
		global $table;
		global $db;
		global $sys;
		$sql = "SELECT * FROM {$table->cate}  WHERE CateID='{$CateID}' AND OwnerID={$sys->uId}  AND Disabled=0";
		$result = $db->getRow( $sql );
		return $result;
	}

	function getAll( $ParentID = 0 )
	{
		global $table;
		global $db;
		global $sys;
		$sql = "SELECT * FROM {$table->cate} where ParentID={$ParentID} AND OwnerID={$sys->uId} AND Disabled=0";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function haveSon( $CateID )
	{
		global $table;
		global $db;
		global $sys;
		$sql = "SELECT count(*) as nr FROM {$table->cate}  WHERE ParentID='{$CateID}' AND OwnerID={$sys->uId} AND Disabled=0";
		$result = $db->getRow( $sql );
		if ( 0 < $result[nr] )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function getAll4Tree( $ParentID = 0 )
	{
		global $table;
		global $db;
		global $sys;
		$sql = "SELECT CateID,Name,ParentID FROM {$table->cate} where ParentID={$ParentID} AND OwnerID={$sys->uId} AND Disabled=0";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			if ( $this->haveSon( $result->fields[CateID] ) )
			{
				$haveSon = 1;
			}
			else
			{
				$haveSon = 0;
			}
			$data[] = array(
				"CateID" => $result->fields[CateID],
				"Name" => $result->fields[Name],
				"ParentID" => $result->fields[ParentID],
				"haveSon" => $haveSon
			);
			$result->MoveNext( );
		}
		return $data;
	}

}

?>
