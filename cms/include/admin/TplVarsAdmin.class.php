<?php


class TplVarsAdmin extends iData
{

	function add( )
	{
		global $table;
		if ( $this->dataInsert( $table->tpl_vars ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function del( $Id )
	{
		global $table;
		$which = "Id";
		if ( $this->dataDel( $table->tpl_vars, $which, $Id, $method = "=" ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function update( $Id )
	{
		global $table;
		$where = "where Id=".$Id;
		if ( $this->dataUpdate( $table->tpl_vars, $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function updateByVarName( $varName )
	{
		global $table;
		global $db;
		$where = "where VarName='".$db->escape_string( $varName )."'";
		if ( $this->dataUpdate( $table->tpl_vars, $where ) )
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
		$sql = "SELECT * FROM {$table->tpl_vars} ";
		$result = $db->Execute( $sql, 2, 10000 );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getLimit( $start = 0, $offset = 15 )
	{
		global $table;
		global $db;
		if ( $start == "" )
		{
			$start = 0;
		}
		if ( $offset == "" )
		{
			$offset = 15;
		}
		$sql = "SELECT * FROM {$table->tpl_vars}  ORDER BY Id DESC LIMIT {$start}, {$offset}";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getRecordNum( )
	{
		global $table;
		global $db;
		$sql = "SELECT COUNT(*) as nr  FROM {$table->tpl_vars} ";
		$result = $db->getRow( $sql );
		return $result[nr];
	}

	function getInfo( $Id )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->tpl_vars}   WHERE Id='{$Id}'";
		$result = $db->getRow( $sql );
		return $result;
	}

	function getValue( $varName )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->tpl_vars}   WHERE VarName='{$varName}'";
		$result = $db->getRow( $sql );
		return $result[VarValue];
	}

}

?>
