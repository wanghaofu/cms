<?php


class collection_cate_admin extends iData
{

	function add( )
	{
		global $table;
		if ( $this->dataInsert( $table->collection_cate ) )
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
		$sql = "UPDATE {$table->collection_cate} SET `Disabled`=1 WHERE CateID={$CateID}";
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
		$where = "where  CateID=".$CateID;
		if ( $this->dataUpdate( $table->collection_cate, $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function updateRule( $CateID, $ContentFieldID )
	{
		global $table;
		global $iWPC;
		global $sys;
		$where = "where  CateID=".$CateID." AND ContentFieldID=".$ContentFieldID;
		if ( $this->dataUpdate( $table->collection_rules, $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function addRule( )
	{
		global $table;
		if ( $this->dataInsert( $table->collection_rules ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function replaceRule( )
	{
		global $table;
		if ( $this->dataReplace( $table->collection_rules ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function isRuleExists( $CateID, $ContentFieldID )
	{
		global $table;
		global $db;
		global $sys;
		$sql = "SELECT RuleID FROM {$table->collection_rules}  WHERE CateID='{$CateID}' AND ContentFieldID='{$ContentFieldID}'";
		$result = $db->getRow( $sql );
		if ( !empty( $result[RuleID] ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function getRules( $CateID )
	{
		global $table;
		global $db;
		global $sys;
		$sql = "SELECT * FROM {$table->collection_rules} where CateID='{$CateID}'";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$id = $result->fields[ContentFieldID];
			$data[$id] = $result->fields[Rule];
			$result->MoveNext( );
		}
		return $data;
	}

	public static function getCateInfo( $CateID )
	{
		global $table;
		global $db;
		global $sys;
		$sql = "SELECT * FROM {$table->collection_cate}  WHERE CateID='{$CateID}' AND Disabled=0";
		$result = $db->getRow( $sql );
		return $result;
	}

	function getAll( $ParentID = 0 )
	{
		global $table;
		global $db;
		global $sys;
		$sql = "SELECT * FROM {$table->collection_cate} where ParentID={$ParentID} AND Disabled=0";
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
		$sql = "SELECT count(*) as nr FROM {$table->collection_cate}  WHERE ParentID='{$CateID}' AND Disabled=0";
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
		$sql = "SELECT CateID,Name,ParentID FROM {$table->collection_cate} where ParentID={$ParentID} AND Disabled=0";
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
