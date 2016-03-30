<?php
class workflowAdmin extends iData
{

	function add( )
	{
		global $table;
		if ( $this->dataInsert( $table->workflow ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function add_state( )
	{
		global $table;
		if ( $this->isStateExists( $this->insData[State] ) )
		{
			return false;
		}
		else if ( $this->insData[State] < 100 )
		{
			return false;
		}
		else if ( $this->dataInsert( $table->workflow_state ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function add_record( )
	{
		global $table;
		if ( $this->dataInsert( $table->workflow_record ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function isStateExists( $State )
	{
		global $table;
		global $db;
		$sql = "SELECT ID FROM {$table->workflow_state}  WHERE State='{$State}'  ";
		$result = $db->getRow( $sql );
		if ( empty( $result[ID] ) )
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	function del_state( $ID )
	{
		global $table;
		$which = "ID";
		if ( $this->dataDel( $table->workflow_state, $which, $ID, $method = "=" ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function update_state( $ID )
	{
		global $table;
		$StateInfo = $this->getStateInfo( $ID, $field = "*" );
		if ( $this->isStateExists( $this->insData[State] ) && $StateInfo[State] != $this->insData[State] )
		{
			return false;
		}
		else if ( $this->insData[State] < 100 )
		{
			return false;
		}
		else
		{
			$where = "where ID=".$ID;
			if ( $this->dataUpdate( $table->workflow_state, $where ) )
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	function edit_record( $OpID )
	{
		global $table;
		$where = "where OpID=".$OpID;
		if ( $this->dataUpdate( $table->workflow_record, $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function del( $wID )
	{
		global $table;
		$which = "wID";
		if ( $this->dataDel( $table->workflow, $which, $wID, $method = "=" ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function del_record( $OpID )
	{
		global $table;
		$which = "OpID";
		if ( $this->dataDel( $table->workflow_record, $which, $OpID, $method = "=" ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function update( $wID )
	{
		global $table;
		$where = "where wID=".$wID;
		if ( $this->dataUpdate( $table->workflow, $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function getAll( )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->workflow}";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getAllState( )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->workflow_state}";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getState( $State )
	{
		global $table;
		global $db;
		if ( empty( $State ) )
		{
			return false;
		}
		$sql = "SELECT * FROM {$table->workflow_state} where State IN ({$State}) ";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getAllRecord( $wID )
	{
		global $table;
		global $db;
		if ( empty( $wID ) )
		{
			return false;
		}
		$sql = "SELECT r.*,g.gName FROM {$table->workflow_record} r left join {$table->group} g ON g.gId=r.Executor where r.wID='{$wID}' order by r.Executor";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getAllWorkFlowRecordByGroup( $wID, $gId )
	{
		global $table;
		global $db;
		if ( $gId == "admin" )
		{
			$sql = "SELECT r.*,g.gName FROM {$table->workflow_record} r left join {$table->group} g ON g.gId=r.Executor where r.wID='{$wID}' order by r.Executor ";
		}
		else
		{
			$sql = "SELECT r.*,g.gName FROM {$table->workflow_record} r left join {$table->group} g ON g.gId=r.Executor where r.wID='{$wID}' and r.Executor='{$gId}' ";
		}
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getAllStateByGroup( $wID, $gId )
	{
		global $table;
		global $db;
		if ( $gId == "admin" )
		{
			$sql = "SELECT StateBeforeOp FROM {$table->workflow_record}  where wID='{$wID}' order by Executor ";
		}
		else
		{
			$sql = "SELECT StateBeforeOp FROM {$table->workflow_record}  where wID='{$wID}' and Executor='{$gId}' ";
		}
		$result = $db->Execute( $sql );
		$i = 0;
		while ( !$result->EOF )
		{
			if ( $i == 0 )
			{
				$data = $result->fields[StateBeforeOp];
			}
			else
			{
				$data = $data."/".$result->fields[StateBeforeOp];
			}
			++$i;
			$result->MoveNext( );
		}
		$StateArray = array_unique( explode( "/", $data ) );
		$i = 0;
		foreach ( $StateArray as $key => $var )
		{
			if ( $i == 0 )
			{
				$return = $var;
			}
			else
			{
				$return = $return.",".$var;
			}
			++$i;
		}
		return $return;
	}

	function getStateInfo( $ID, $field = "*" )
	{
		global $table;
		global $db;
		$sql = "SELECT {$field} FROM {$table->workflow_state}  WHERE ID='{$ID}'  ";
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

	function getRecordInfo( $OpID, $field = "*" )
	{
		global $table;
		global $db;
		$sql = "SELECT {$field} FROM {$table->workflow_record}  WHERE OpID='{$OpID}'  ";
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

	function getRecordInfoByStateBeforeOp( $wID, $State, $field = "*" )
	{
		global $table;
		global $db;
		$sql = "SELECT {$field} FROM {$table->workflow_record}  WHERE wID='{$wID}' AND StateBeforeOp='{$State}' ";
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

	function getInfo( $wID, $field = "*" )
	{
		global $table;
		global $db;
		$sql = "SELECT {$field} FROM {$table->workflow}  WHERE wID='{$wID}'  ";
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

}

?>
