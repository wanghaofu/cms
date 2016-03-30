<?php
/*********************/
/*                   */
/*  Version : 5.1.0  */
/*  Author  : RM     */
/*  Comment : 071223 */
/*                   */
/*********************/

class site_admin extends idata
{

	function add( )
	{
		global $table;
		global $sys;
		site_admin::isvalid( );
		$this->adddata( "CreationUserID", $sys->session['sUId'] );
		if ( $this->datainsert( $table->site ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function destroy( $NodeID )
	{
		global $table;
		global $iWPC;
		global $db;
		$publish = new publishadmin( );
		$publish->empty_recycle_bin( $NodeID );
		$sql = "SELECT IndexID FROM {$table->content_index}  where NodeID='".$NodeID."' ";
		$result = $db->execute( $sql );
		while ( !$result->EOF )
		{
			$publish->destroy( $result->fields['IndexID'] );
			$result->movenext( );
		}
		unset( $publish );
		$which = "NodeID";
		if ( $this->datadel( $table->site, $which, $NodeID, "=" ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function restore( $NodeID, $targetNodeID )
	{
		global $table;
		global $iWPC;
		global $db;
		$sql = "UPDATE {$table->site} SET `Disabled`=0 , `ParentID`='".$targetNodeID."' WHERE NodeID=".$NodeID;
		if ( $db->query( $sql ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function empty_recycle_bin( )
	{
		global $table;
		global $iWPC;
		$list = $this->getall4recyclebin( );
		foreach ( $list as $key => $var )
		{
			$result = $this->destroy( $var['NodeID'] );
		}
		if ( $result )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function getall4recyclebin( )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->site} where  Disabled=1 ";
		$result = $db->execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->movenext( );
		}
		return $data;
	}

	function del( $NodeID )
	{
		global $table;
		global $iWPC;
		global $db;
		$sql = "UPDATE {$table->site} SET `Disabled`=1 WHERE NodeID={$NodeID}";
		if ( $db->query( $sql ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function deltree( $NodeID )
	{
		global $table;
		global $db;
		$return = $this->del( $NodeID );
		$sql = "SELECT NodeID FROM {$table->site} where  ParentID={$NodeID} ";
		$result = $db->execute( $sql );
		while ( !$result->EOF )
		{
			$return = $this->deltree( $result->fields['NodeID'] );
			$result->movenext( );
		}
		return $return;
	}

	function isvalid( $add = 0 )
	{
		global $table;
		global $db;
		global $NODE_LIST;
		require( SYS_PATH."/license.php" );
		$license_array = $License;
		unset( $License );
		if ( !empty( $NODE_LIST ) )
		{
			$result[nr] = count( $NODE_LIST );
		}
		else
		{
			$sql = "SELECT count(*) as nr FROM {$table->site}  WHERE  Disabled=0";
			$result = $db->getrow( $sql );
		}
		if ( $license_array['Node-num'] < $result[nr] + $add && $license_array['Node-num'] != 0 )
		{
			goback( "license_node_num_overflow" );
		}
	}

	function update( $NodeID )
	{
		global $table;
		global $iWPC;
		$where = "where NodeID=".$NodeID;
		if ( $this->dataupdate( $table->site, $where ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function getnodeinfo( $NodeID )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->site}  WHERE NodeID='{$NodeID}' ";
		$result = $db->getrow( $sql );
		return $result;
	}

	function getall( $ParentID = 0 )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->site} where ParentID={$ParentID}  AND Disabled=0 Order by NodeSort DESC";
		$result = $db->execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->movenext( );
		}
		return $data;
	}

	function haveson( $NodeID )
	{
		global $table;
		global $db;
		$sql = "SELECT count(*) as nr FROM {$table->site}  WHERE ParentID='{$NodeID}'  AND Disabled=0";
		$result = $db->getrow( $sql );
		if ( 0 < $result[nr] )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function getall4tree( $ParentID = 0 )
	{
		global $table;
		global $db;
		if ( empty( $ParentID ) )
		{
			$ParentID = 0;
		}
		$sql = "SELECT * FROM {$table->site} where ParentID={$ParentID}  AND Disabled=0 Order by NodeSort DESC";
		$result = $db->execute( $sql );
		$i = 1;
		while ( !$result->EOF )
		{
			if ( $this->haveson( $result->fields[NodeID] ) )
			{
				$haveSon = 1;
			}
			else
			{
				$haveSon = 0;
			}
			$data[$i] = $result->fields;
			$data[$i]['haveSon'] = $haveSon;
			++$i;
			$result->movenext( );
		}
		return $data;
	}

	function getrecyclebin( )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->site} where  Disabled=1 Order by NodeSort DESC";
		$result = $db->execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->movenext( );
		}
		$result = $db->execute( "SELECT * FROM {$table->site} where  Disabled=0 AND ParentID!=0" );
		while ( !$result->EOF )
		{
			$ParentNodeInfo = $this->getnodeinfo( $result->fields['ParentID'] );
			if ( empty( $ParentNodeInfo['NodeID'] ) || $ParentNodeInfo['Disabled'] == 1 )
			{
				$data[] = $result->fields;
			}
			$result->movenext( );
		}
		return $data;
	}

	function getsonnode( $NodeID )
	{
		global $table;
		global $db;
		$sql = "SELECT NodeID FROM {$table->site} where InheritNodeID={$NodeID}  AND Disabled=0";
		$result = $db->execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields['NodeID'];
			$result->movenext( );
		}
		return $data;
	}

	function issiteadmin( )
	{
		global $sys;
		if ( $sys->session['sGIsAdmin'] == 1 )
		{
			return TRUE;
		}
		if ( $sys->Auth[canNode] )
		{
			return TRUE;
		}
		return FALSE;
	}

	function canaccess( &$NodeInfo, $mode )
	{
		global $sys;
		global $iWPC;
		if ( $sys->session['sGIsAdmin'] == 1 )
		{
			return TRUE;
		}
		if ( $sys->Auth[canNode] )
		{
			return TRUE;
		}
		if ( $this->iscreationuser( $NodeInfo ) )
		{
			return TRUE;
		}
		if ( $NodeInfo['PermissionInherit'] == 1 )
		{
			$ParentNodeInfo = $iWPC->loadnodeinfo( $NodeInfo['ParentID'] );
			return $this->canaccess( $ParentNodeInfo, $mode );
		}
		if ( !empty( $NodeInfo['PermissionManageG'] ) )
		{
			$posMG = strpos( ",".$NodeInfo['PermissionManageG'].",", ",".$sys->session['sGId']."," );
			if ( $posMG === FALSE )
			{
				}
			else
			{
				return TRUE;
			}
		}
		if ( !empty( $NodeInfo['PermissionManageU'] ) )
		{
			$posMU = strpos( ",".$NodeInfo['PermissionManageU'].",", ",".$sys->session['sUId']."," );
			if ( $posMU === FALSE )
			{
				}
			else
			{
				return TRUE;
			}
		}
		if ( !empty( $NodeInfo["Permission".$mode."G"] ) )
		{
			$posG = strpos( ",".$NodeInfo["Permission".$mode."G"].",", ",".$sys->session['sGId']."," );
		}
		else
		{
			$posG = FALSE;
		}
		if ( $posG === FALSE )
		{
			if ( !empty( $NodeInfo["Permission".$mode."U"] ) )
			{
				$posU = strpos( ",".$NodeInfo["Permission".$mode."U"].",", ",".$sys->session['sUId']."," );
			}
			else
			{
				return FALSE;
			}
			if ( $posU === FALSE )
			{
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
		else
		{
			return TRUE;
		}
	}

	function iscreationuser( &$NodeInfo )
	{
		global $sys;
		if ( $sys->session['sGIsAdmin'] == 1 )
		{
			return TRUE;
		}
		if ( $sys->Auth[canNode] )
		{
			return TRUE;
		}
		if ( $NodeInfo['CreationUserID'] == $sys->session['sUId'] )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function publishpermissiondetector( $o, $NodeID, &$IN )
	{
		global $sys;
		global $iWPC;
		global $_LANG_ADMIN;
		$NodeInfo = $iWPC->loadnodeinfo( $NodeID );
		switch ( $o )
		{
		case "list" :
		case "content_header" :
		case "content_list" :
		case "content_editor_frameset" :
		case "content_editor_header" :
		case "add" :
		case "edit" :
		case "view" :
		case "refresh" :
		case "IsRecordExists" :
		case "viewLinkState" :
		case "siteRefresh" :
		case "sitePublish" :
		case "recycle_bin" :
		case "viewpublish" :
		case "editContentLink" :
		case "picker_content" :
			if ( $this->canaccess( $NodeInfo, "Read" ) )
			{
				break;
			}
			goback( sprintf( $_LANG_ADMIN['site_permission_deny_read'], $NodeInfo['Name'] ), 1 );
			break;
		case "add_submit" :
		case "edit_submit" :
		case "del" :
		case "cut" :
		case "topIt" :
		case "topIt_submit" :
		case "pinkIt" :
		case "pinkIt_submit" :
		case "sortIt" :
		case "sortIt_submit" :
		case "restore" :
		case "node_resync" :
		case "planPublish" :
			if ( $this->canaccess( $NodeInfo, "Write" ) )
			{
				break;
			}
			goback( sprintf( $_LANG_ADMIN['site_permission_deny_write'], $NodeInfo['Name'] ), 1 );
			break;
		case "createLink" :
		case "createIndexLink" :
		case "copy" :
			if ( !$this->canaccess( $NodeInfo, "Read" ) )
			{
				goback( sprintf( $_LANG_ADMIN['site_permission_deny_read'], $NodeInfo['Name'] ), 1 );
			}
			$targetNodeInfo = $iWPC->loadnodeinfo( $IN[targetNodeID] );
			if ( $this->canaccess( $targetNodeInfo, "Write" ) )
			{
				break;
			}
			goback( sprintf( $_LANG_ADMIN['site_permission_deny_write'], $targetNodeInfo['Name'] ), 1 );
			break;
		case "publish" :
		case "unpublish" :
			if ( $this->canaccess( $NodeInfo, "Publish" ) )
			{
				break;
			}
			goback( sprintf( $_LANG_ADMIN['site_permission_deny_publish'], $NodeInfo['Name'] ), 1 );
			break;
		case "destroy" :
		case "empty_recycle_bin" :
			if ( !$this->canaccess( $NodeInfo, "Manage" ) )
			{
				break;
			}
			goback( sprintf( $_LANG_ADMIN['site_permission_deny_manage'], $NodeInfo['Name'] ), 1 );
		}
	}

	function contributionpermissiondetector( $o, $NodeID, &$IN )
	{
		global $sys;
		global $iWPC;
		global $_LANG_ADMIN;
		$NodeInfo = $iWPC->loadnodeinfo( $NodeID );
		switch ( $o )
		{
		case "list" :
		case "content_header" :
		case "content_list" :
		case "contribution_editor_frameset" :
		case "contribution_editor_header" :
		case "edit" :
		case "view" :
		case "viewNote" :
		case "workflow" :
			if ( $this->canaccess( $NodeInfo, "Read" ) )
			{
				break;
			}
			goback( sprintf( $_LANG_ADMIN['site_permission_deny_read'], $NodeInfo['Name'] ), 1 );
			break;
		case "edit_submit" :
		case "approve" :
		case "callback" :
			if ( !$this->canaccess( $NodeInfo, "Write" ) )
			{
				break;
			}
			goback( sprintf( $_LANG_ADMIN['site_permission_deny_write'], $NodeInfo['Name'] ), 1 );
		}
	}

	function getallfieldsinfo( )
	{
		global $table;
		global $db;
		global $NODE_FIELDS_INFO;
		if ( !empty( $FIELDS_INFO ) )
		{
			return $FIELDS_INFO;
		}
		$sql = "SELECT * FROM {$table->node_fields}  Order By FieldOrder";
		$result = $db->execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->movenext( );
		}
		$NODE_FIELDS_INFO = $data;
		return $data;
	}

	function addfield( $data )
	{
		global $table;
		global $db_config;
		global $db;
		if ( $data[FieldSize] != "" && $data[FieldType] != "text" && $data[FieldType] != "mediumtext" && $data[FieldType] != "longtext" && $data[FieldType] != "contentlink" )
		{
			$length = "({$data[FieldSize]})";
		}
		$sql = "ALTER TABLE {$table->site} ADD `{$data[FieldName]}` {$data[FieldType]} {$length}   default NULL";
		if ( $db->query( $sql ) )
		{
			$this->flushdata( );
			$this->adddata( $data );
			if ( $this->_add_field( ) )
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}

	function _add_field( )
	{
		global $table;
		if ( $this->datainsert( $table->node_fields ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function _edit_field( $FieldID )
	{
		global $table;
		$where = "where FieldID=".$FieldID;
		if ( $this->dataupdate( $table->node_fields, $where ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function editfield( $FieldID, $data )
	{
		global $table;
		global $db_config;
		global $db;
		$fieldInfo = $this->getfieldinfo( $FieldID );
		if ( $data[FieldSize] != "" && $data[FieldType] != "text" && $data[FieldType] != "mediumtext" && $data[FieldType] != "longtext" && $data[FieldType] != "contentlink" )
		{
			$length = "({$data[FieldSize]})";
		}
		$sql = "ALTER TABLE {$table->site} CHANGE `{$fieldInfo[FieldName]}` `{$data[FieldName]}` {$data[FieldType]} {$length}   default NULL";
		if ( $db->query( $sql ) )
		{
			$this->flushdata( );
			$this->adddata( $data );
			if ( $this->_edit_field( $FieldID ) )
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}

	function getfieldinfo( $FieldID )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->node_fields} where FieldID='{$FieldID}'";
		$data = $db->getrow( $sql );
		return $data;
	}

	function delfield( $FieldID )
	{
		global $table;
		global $db;
		global $db_config;
		$info = $this->getfieldinfo( $FieldID );
		$sql = "ALTER TABLE {$table->site} DROP `{$info[FieldName]}`";
		if ( $db->query( $sql ) )
		{
			if ( $this->_del_data( $FieldID ) )
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}

	function _del_data( $FieldID )
	{
		global $table;
		$which = "FieldID";
		if ( $this->datadel( $table->node_fields, $which, $FieldID, $method = "=" ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function isfieldnameexists( $FieldName, $FieldID = 0 )
	{
		global $table;
		global $db;
		$sql = "SELECT FieldID FROM {$table->node_fields}  where  FieldName='{$FieldName}' AND FieldID!='{$FieldID}' ";
		$result = $db->getrow( $sql );
		if ( empty( $result[FieldID] ) )
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

}

?>
