<?php
class workflowadmin extends idata
{
	function add( )
	{
		global $table;
		if ( $this->datainsert( $table->workflow ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function add_state( )
	{
		global $table;
		if ( $this->isstateexists( $this->insData[State] ) )
		{
			return FALSE;
		}
		else if ( $this->insData[State] < 100 )
		{
			return FALSE;
		}
		else if ( $this->datainsert( $table->workflow_state ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function add_record( )
	{
		global $table;
		if ( $this->datainsert( $table->workflow_record ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function isstateexists( $State )
	{
		global $table;
		global $db;
		$sql = "SELECT ID FROM {$table->workflow_state}  WHERE State='{$State}'  ";
		$result = $db->getrow( $sql );
		if ( empty( $result[ID] ) )
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	function del_state( $ID )
	{
		global $table;
		$which = "ID";
		if ( $this->datadel( $table->workflow_state, $which, $ID, $method = "=" ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function update_state( $ID )
	{
		global $table;
		$StateInfo = $this->getstateinfo( $ID, $field = "*" );
		if ( $this->isstateexists( $this->insData[State] ) && $StateInfo[State] != $this->insData[State] )
		{
			return FALSE;
		}
		else if ( $this->insData[State] < 100 )
		{
			return FALSE;
		}
		else
		{
			$where = "where ID=".$ID;
			if ( $this->dataupdate( $table->workflow_state, $where ) )
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
	}

	function edit_record( $OpID )
	{
		global $table;
		$where = "where OpID=".$OpID;
		if ( $this->dataupdate( $table->workflow_record, $where ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function del( $wID )
	{
		global $table;
		$which = "wID";
		if ( $this->datadel( $table->workflow, $which, $wID, $method = "=" ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function del_record( $OpID )
	{
		global $table;
		$which = "OpID";
		if ( $this->datadel( $table->workflow_record, $which, $OpID, $method = "=" ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function update( $wID )
	{
		global $table;
		$where = "where wID=".$wID;
		if ( $this->dataupdate( $table->workflow, $where ) )
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
		$sql = "SELECT * FROM {$table->workflow}";
		$result = $db->execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->movenext( );
		}
		return $data;
	}

	function getallstate( )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->workflow_state}";
		$result = $db->execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->movenext( );
		}
		return $data;
	}

	function getstate( $State )
	{
		global $table;
		global $db;
		if ( empty( $State ) )
		{
			return FALSE;
		}
		$sql = "SELECT * FROM {$table->workflow_state} where State IN ({$State}) ";
		$result = $db->execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->movenext( );
		}
		return $data;
	}

	function getallrecord( $wID )
	{
		global $table;
		global $db;
		if ( empty( $wID ) )
		{
			return FALSE;
		}
		$sql = "SELECT r.*,g.gName FROM {$table->workflow_record} r left join {$table->group} g ON g.gId=r.Executor where r.wID='{$wID}' order by r.Executor";
		$result = $db->execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->movenext( );
		}
		return $data;
	}

	function getallworkflowrecordbygroup( $wID, $gId )
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
		$result = $db->execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->movenext( );
		}
		return $data;
	}

	function getallstatebygroup( $wID, $gId )
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
		$result = $db->execute( $sql );
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
			$result->movenext( );
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

	function getstateinfo( $ID, $field = "*" )
	{
		global $table;
		global $db;
		$sql = "SELECT {$field} FROM {$table->workflow_state}  WHERE ID='{$ID}'  ";
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

	function getrecordinfo( $OpID, $field = "*" )
	{
		global $table;
		global $db;
		$sql = "SELECT {$field} FROM {$table->workflow_record}  WHERE OpID='{$OpID}'  ";
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

	function getrecordinfobystatebeforeop( $wID, $State, $field = "*" )
	{
		global $table;
		global $db;
		$sql = "SELECT {$field} FROM {$table->workflow_record}  WHERE wID='{$wID}' AND StateBeforeOp='{$State}' ";
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

	function getinfo( $wID, $field = "*" )
	{
		global $table;
		global $db;
		$sql = "SELECT {$field} FROM {$table->workflow}  WHERE wID='{$wID}'  ";
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

}

?>
