<?php


class AccessGroup extends iData
{

	function add( )
	{
		global $plugin_table;
	
		if ( $this->dataInsert( $plugin_table['bbsi']['access'] ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function update( $AccessID )
	{
		global $plugin_table;
		$where = "where AccessID=".$AccessID;
		if ( $this->dataUpdate( $plugin_table['bbsi']['access'], $where ) )
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
		global $plugin_table;
		global $bbs_table;
		global $db;
		global $_FieldMapping;
		$sql = "SELECT g.{$_FieldMapping['GroupName']} as GroupName, g.{$_FieldMapping['GroupTable_GroupID']} as GroupID,a.* FROM {$bbs_table->group} g LEFT JOIN {$plugin_table['bbsi']['access']} a ON  a.OwnerID=g.{$_FieldMapping['GroupTable_GroupID']}   AND  a.AccessType=1 ";
		
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getInfo( $aId )
	{
		global $plugin_table;
		global $bbs_table;
		global $db;
		global $_FieldMapping;
		$sql = "SELECT a.*,g.{$_FieldMapping['GroupName']} as GroupName FROM {$plugin_table['bbsi']['access']} a, {$bbs_table->group} g WHERE a.OwnerID=g.{$_FieldMapping['GroupTable_GroupID']} AND  AccessID='{$aId}'   AND  a.AccessType=1 ";
		$result = $db->getRow( $sql );
		return $result;
	}

	function accessDefined( $GroupName )
	{
		global $plugin_table;
		global $bbs_table;
		global $db;
		global $_FieldMapping;
		$sql = "SELECT a.AccessID FROM {$plugin_table['bbsi']['access']} a, {$bbs_table->group} g WHERE a.OwnerID=g.{$_FieldMapping['GroupTable_GroupID']}  AND  a.AccessType=0  AND  g.{$_FieldMapping['GroupName']}='{$GroupName}'  ";
		$result = $db->getRow( $sql );
		if ( empty( $result['AccessID'] ) )
		{
			return false;
		}
		else
		{
			return $result['AccessID'];
		}
	}

}

?>
