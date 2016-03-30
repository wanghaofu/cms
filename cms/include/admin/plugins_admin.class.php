<?php


class PluginsAdmin extends iData
{

	function add( )
	{
		global $table;
		if ( $this->dataInsert( $table->plugins ) && $this->installPlugin( $this->db_insert_id ) )
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
		if ( $this->uninstallPlugin( $pId ) && $this->dataDel( $table->plugins, $which, $pId, $method = "=" ) )
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
		if ( $this->dataUpdate( $table->plugins, $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
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
		$sql = "SELECT * FROM {$table->plugins}  ORDER BY pId DESC LIMIT {$start}, {$offset}";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getAll( )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->plugins}  ORDER BY pId DESC  ";
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
		$sql = "SELECT COUNT(*) as nr  FROM {$table->plugins} ";
		$result = $db->getRow( $sql );
		return $result[nr];
	}

	function getInfo( $pId )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->plugins}   WHERE pId='{$pId}'";
		$result = $db->getRow( $sql );
		return $result;
	}

	function installPlugin( $pId )
	{
		global $table;
		global $db;
		global $db_config;
		global $sys;
		global $plugin_table;
		global $iWPC;
		$pluginInfo = $this->getInfo( $pId );
		$processFile = PLUGIN_PATH.$pluginInfo['Path']."/include/data_sync/install.php";
		if ( file_exists( $processFile ) )
		{
			include( $processFile );
		}
		return $result;
	}

	function uninstallPlugin( $pId )
	{
		global $table;
		global $db;
		global $db_config;
		global $sys;
		global $plugin_table;
		global $iWPC;
		$pluginInfo = $this->getInfo( $pId );
		$processFile = PLUGIN_PATH.$pluginInfo['Path']."/include/data_sync/uninstall.php";
		if ( file_exists( $processFile ) )
		{
			include( $processFile );
		}
		return $result;
	}

}

?>
