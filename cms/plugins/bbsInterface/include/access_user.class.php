<?php


class AccessUser extends iData
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

	function del( $AccessID )
	{
		global $plugin_table;
		$which = "AccessID";
		if ( $this->dataDel( $plugin_table['bbsi']['access'], $which, $AccessID, $method = "=" ) )
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

	function getRecordNum( )
	{
		global $plugin_table;
		global $db;
		$sql = "SELECT count(*) nr FROM {$plugin_table['bbsi']['access']} ";
		$result = $db->getRow( $sql );
		return $result['nr'];
	}

	function getRecordLimit( $start = 0, $offset = 10 )
	{
		global $plugin_table;
		global $bbs_table;
		global $db;
		global $_FieldMapping;
		$sql = "SELECT a.*,u.{$_FieldMapping['UserTable_UserName']} as UserName FROM {$plugin_table['bbsi']['access']} a, {$bbs_table->user} u WHERE a.OwnerID=u.{$_FieldMapping['UserTable_UserID']}  AND  a.AccessType=0 Limit {$start}, {$offset} ";
		de($sql,__file__,__line__);
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
		$sql = "SELECT a.*,u.{$_FieldMapping['UserTable_UserName']} as UserName FROM {$plugin_table['bbsi']['access']} a, {$bbs_table->user} u WHERE a.OwnerID=u.{$_FieldMapping['UserTable_UserID']} AND  AccessID='{$aId}'   AND  a.AccessType=0 ";
		$result = $db->getRow( $sql );
		return $result;
	}

	function userExists( $UserName )
	{
		global $plugin_table;
		global $bbs_table;
		global $db;
		global $_FieldMapping;
		$sql = "SELECT {$_FieldMapping['UserTable_UserID']} as UserID,{$_FieldMapping['UserTable_UserName']} FROM  {$bbs_table->user}  WHERE {$_FieldMapping['UserTable_UserName']}='{$UserName}' ";
		$result = $db->getRow( $sql );
		if ( empty( $result[$_FieldMapping['UserTable_UserName']] ) )
		{
			return false;
		}
		else
		{
			return $result['UserID'];
		}
	}

	function accessDefined( $UserName )
	{
		global $plugin_table;
		global $bbs_table;
		global $db;
		global $_FieldMapping;
		$sql = "SELECT a.AccessID FROM {$plugin_table['bbsi']['access']} a, {$bbs_table->user} u WHERE a.OwnerID=u.{$_FieldMapping['UserTable_UserID']}  AND  a.AccessType=0  AND  u.{$_FieldMapping['UserTable_UserName']}='{$UserName}'  ";
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
