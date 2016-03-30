<?php


class publishAccessAdmin extends iData
{

	function add( )
	{
		global $table;
		if ( $this->dataInsert( $table->pubaccessmasks ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function del( $pId )
	{
		global $table;
		$which = "pId";
		if ( $this->dataDel( $table->pubaccessmasks, $which, $pId, $method = "=" ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function update( $pId )
	{
		global $table;
		$where = "where pId=".$pId;
		if ( $this->dataUpdate( $table->pubaccessmasks, $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function getInfo( $pId )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->pubaccessmasks}  WHERE pId='{$pId}'";
		$result = $db->getRow( $sql );
		return $result;
	}

	function getAll( )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->pubaccessmasks}";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

}

?>
