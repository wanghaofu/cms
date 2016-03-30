<?php


class ipbannedAdmin extends iData
{

	function add( )
	{
		global $table;
		if ( $this->dataInsert( $table->block_ip ) )
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
		if ( $this->dataDel( $table->block_ip, $which, $Id, $method = "=" ) )
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
		if ( $this->dataUpdate( $table->block_ip, $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function getRecordNum( )
	{
		global $table;
		global $db;
		$result = $db->getRow( "SELECT COUNT(*) as TotalNum FROM {$table->block_ip} " );
		return $result['TotalNum'];
	}

	function getRecordLimit( $start, $offset )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->block_ip} ORDER BY Id DESC LIMIT {$start}, {$offset} ";
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
