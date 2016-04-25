<?php
class publishAdmin extends iData
{

	function publishAdmin( )
	{
		global $_BeanFactory;
		if ( !isset( $_BeanFactory ) )
		{
			require_once( LIB_PATH."Spring.php" );
			$_BeanFactory = new Spring( "spring.appcontext.php" );
			$this->beanFactory =& $_BeanFactory;
		}
		else
		{
			$this->beanFactory =& $_BeanFactory;
		}
	}

	function indexAdd( )
	{
		global $table;
		
		if ( $this->dataInsert( $table->content_index ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function indexEdit( $IndexID )
	{
		global $table;
		$where = "where IndexID=".$IndexID;
		if ( $this->dataUpdate( $table->content_index, $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function linkExists( $ContentID, $NodeID )
	{
		global $table;
		global $db;
		$result = $db->getRow( "select IndexID from {$table->content_index} where NodeID='{$NodeID}' AND ContentID='{$ContentID}' and Type=0 and State!='-1'" );
		if ( empty( $result['IndexID'] ) )
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	function getResyncIndexLimit( $NodeID, $start = 0, $offset = 15, $State = "!= -1", $Type = "" )
	{
		global $table;
		global $db;
		global $iWPC;
		global $db_config;
		if ( $start == "" )
		{
			$start = 0;
		}
		if ( $offset == "" )
		{
			$offset = 15;
		}
		if ( !empty( $Type ) )
		{
			$Type = "AND i.Type={$Type}";
		}
		$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
		$table_name = $db_config['table_pre'].$db_config['table_content_pre']."_".$NodeInfo['TableID'];
		$sql = "SELECT i2.NodeID,i2.ContentID,i.State,i.Top,i.Pink,i.Sort,i2.URL,i.IndexID,i.PublishDate,i.Type,c.*,u.uName as CreationUser FROM {$table->content_index} i,{$table->content_index} i2 ,{$table_name} c,{$table->site} s , {$table->user} u  where u.uId=c.CreationUserID AND i.NodeID='{$NodeID}' AND i.ParentIndexID=i2.IndexID AND i.ContentID =c.ContentID AND s.NodeID='{$NodeID}' AND i.State{$State} {$Type}   ORDER BY i.Top , i.Sort  , i.PublishDate   LIMIT {$start}, {$offset}";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function restore( $IndexID )
	{
		$this->flushData( );
		$this->addData( "State", 0 );
		if ( $this->indexEdit( $IndexID ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function destroy( $IndexID )
	{
		global $table;
		global $iWPC;
		global $db_config;
		global $db;
		if ( empty( $IndexID ) )
		{
			return false;
		}
		$this->unpublish( $IndexID );
		$IndexInfo = $this->getIndexInfo( $IndexID );
		if ( $IndexInfo['Type'] == 1 || $IndexInfo['Type'] == 3 || $IndexInfo['Type'] == 4 )
		{
			if ( $IndexInfo['Type'] == 1 )
			{
				$result = $db->Execute( "select IndexID from {$table->content_index} where IndexID !='{$IndexID}' AND ContentID='{$IndexInfo['ContentID']}' AND TableID='{$IndexInfo['TableID']}'" );
				while ( !$result->EOF )
				{
					$this->destroy( $result->fields['IndexID'] );
					$result->MoveNext( );
				}
			}
			$this->dataDel( $table->content_index, "ParentIndexID", $IndexInfo['IndexID'], "=" );
			$this->destoryResource( $IndexID );
			$NodeInfo = $iWPC->loadNodeInfo( $IndexInfo['NodeID'] );
			$table_name = $db_config['table_pre'].$db_config['table_content_pre']."_".$NodeInfo['TableID'];
			return $this->dataDel( $table_name, "ContentID", $IndexInfo['ContentID'], "=" );
		}
		else
		{
			$this->destoryResource( $IndexID );
			return $this->dataDel( $table->content_index, "IndexID", $IndexID, "=" );
		}
	}

	function destoryResource( $IndexID )
	{
		global $table;
		global $db;
		global $iWPC;
		global $db_config;
		global $SYS_ENV;
		$result = $db->Execute( "SELECT * FROM {$table->resource_ref}\tWHERE IndexID='{$IndexID}'" );
		while ( !$result->EOF )
		{
			$num_result = $db->getRow( "SELECT COUNT(*) as nr FROM {$table->resource_ref}  WHERE ResourceID='{$result->fields['ResourceID']}'" );
			if ( $num_result['nr'] == 1 )
			{
				$resource_result = $db->getRow( "SELECT Path FROM {$table->resource}  WHERE ResourceID='{$result->fields['ResourceID']}'" );
				if ( file_exists( $SYS_ENV['ResourcePath']."/".$resource_result['Path'] ) )
				{
					echo "Delete ".$SYS_ENV['ResourcePath']."/".$resource_result['Path']." ...<br/>";
					unlink( $SYS_ENV['ResourcePath']."/".$resource_result['Path'] );
				}
				$db->query( "DELETE FROM {$table->resource} WHERE ResourceID='{$result->fields['ResourceID']}'" );
			}
			$result->MoveNext( );
		}
		$db->query( "DELETE FROM {$table->resource_ref}\tWHERE IndexID='{$IndexID}'" );
	}

	function empty_recycle_bin( $NodeID )
	{
		global $table;
		global $iWPC;
		global $db_config;
		$list = $this->getAllIndex( $NodeID, "=-1", "IndexID,NodeID,ContentID,Type" );
		if ( !empty( $list ) )
		{
			foreach ( $list as $key => $var )
			{
				if ( $var['Type'] == 1 || $var['Type'] == 3 || $var['Type'] == 4 )
				{
					$this->dataDel( $table->content_index, "ParentIndexID", $var['IndexID'], "=" );
					$this->destoryResource( $var['IndexID'] );
					$NodeInfo = $iWPC->loadNodeInfo( $var['NodeID'] );
					$table_name = $db_config['table_pre'].$db_config['table_content_pre']."_".$NodeInfo['TableID'];
					$result = $this->dataDel( $table_name, "ContentID", $var['ContentID'], "=" );
				}
				else
				{
					$result = $this->dataDel( $table->content_index, "IndexID", $var['IndexID'], "=" );
				}
			}
			if ( $result )
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return true;
		}
	}

	function getResourceInfo( $IndexID, $Category )
	{
		global $table;
		global $db;
		global $iWPC;
		global $db_config;
		$sql = "select   * from {$table->resource_ref} ref ,{$table->resource} r where ref.ResourceID=r.ResourceID and ref.IndexID='{$IndexID}' and r.Category='{$Category}'";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getAllIndex( $NodeID, $State, $field = "*" )
	{
		global $table;
		global $db;
		global $iWPC;
		global $db_config;
		$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
		$table_name = $db_config['table_pre'].$db_config['table_content_pre']."_".$NodeInfo['TableID'];
		$sql = "SELECT {$field} FROM {$table->content_index}  where State{$State} ";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getLinkState( $IndexID )
	{
		global $table;
		global $iWPC;
		global $db;
		$result = $this->getContentInfo( $IndexID );
		$LinkState['self'] = $result;
		$sql = "SELECT IndexID,NodeID  FROM {$table->content_index} WHERE ContentID='{$result['ContentID']}' AND Type=1  AND State!=-1";
		$result_solid = $db->getRow( $sql );
		$NodeInfo = $iWPC->loadNodeInfo( $result_solid['NodeID'] );
		$LinkState['solid'] = array(
			"Name" => $NodeInfo['Name'],
			"NodeID" => $NodeInfo['NodeID'],
			"IndexID" => $result_solid['IndexID']
		);
		$sql = "SELECT IndexID,NodeID  FROM {$table->content_index} WHERE ContentID='{$result['ContentID']}' AND Type=0 AND State!=-1";
		$result_void = $db->Execute( $sql );
		while ( !$result_void->EOF )
		{
			$NodeInfo = $iWPC->loadNodeInfo( $result_void->fields['NodeID'] );
			$LinkState['void'][] = array(
				"Name" => $NodeInfo['Name'],
				"NodeID" => $NodeInfo['NodeID'],
				"IndexID" => $result_void->fields['IndexID']
			);
			$result_void->MoveNext( );
		}
		$sql = "SELECT IndexID,ParentIndexID,NodeID  FROM {$table->content_index} WHERE ContentID='{$result['ContentID']}' AND Type=2  AND State!=-1";
		$result_index = $db->Execute( $sql );
		while ( !$result_index->EOF )
		{
			$NodeInfo = $iWPC->loadNodeInfo( $result_index->fields['NodeID'] );
			$LinkState['index'][] = array(
				"Name" => $NodeInfo['Name'],
				"NodeID" => $NodeInfo['NodeID'],
				"IndexID" => $result_index->fields['IndexID']
			);
			$result_index->MoveNext( );
		}
		return $LinkState;
	}

	function top( $IndexID, $Num )
	{
		$this->flushData( );
		$this->addData( "Top", $Num );
		if ( $this->indexEdit( $IndexID ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function pink( $IndexID, $Num )
	{
		$this->flushData( );
		$this->addData( "Pink", $Num );
		if ( $this->indexEdit( $IndexID ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function sortIt( $IndexID, $Num )
	{
		$this->flushData( );
		$this->addData( "Sort", $Num );
		if ( $this->indexEdit( $IndexID ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function move( $IndexID, $targetNodeID )
	{
		$this->unpublish( $IndexID, 0 );
		$this->flushData( );
		$this->addData( "NodeID", $targetNodeID );
		if ( $this->indexEdit( $IndexID ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function copyTo( $IndexID, $targetNodeID )
	{
		global $iWPC;
		global $sys;
		$contentInfo = $this->getContentInfo( $IndexID );
		$NodeInfo = $iWPC->loadNodeInfo( $contentInfo['NodeID'] );
		$fieldInfo = content_table_admin::gettablefieldsinfo( $NodeInfo['TableID'] );
		$this->flushData( );
		foreach ( $fieldInfo as $key => $var )
		{
			$this->addData( $var['FieldName'], $contentInfo[$var['FieldName']] );
		}
		$time = time( );
		$this->addData( "CreationDate", $time );
		$this->addData( "ModifiedDate", $time );
		$this->addData( "CreationUserID", $sys->session['sUId'] );
		$this->addData( "LastModifiedUserID", $sys->session['sUId'] );
		$Info['PublishDate'] = $contentInfo['PublishDate'];
		if ( $this->contentAdd( $targetNodeID, $Info ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function createLink( $IndexID, $targetNodeID )
	{
		global $iWPC;
		$IndexInfo = $this->getIndexInfo( $IndexID );
		$this->flushData( );
		$this->addData( "ContentID", $IndexInfo['ContentID'] );
		$this->addData( "NodeID", $targetNodeID );
		$this->addData( "TableID", $IndexInfo['TableID'] );
		$this->addData( "Type", 0 );
		$this->addData( "PublishDate", time( ) );
		if ( $this->indexAdd( ) )
		{
			$IndexID = $this->db_insert_id;
			$this->flushData( );
			$this->addData( "ParentIndexID", $IndexID );
			if ( $this->indexEdit( $IndexID ) )
			{
				$NodeInfo = $iWPC->loadNodeInfo( $targetNodeID );
				if ( $NodeInfo['AutoPublish'] == 1 )
				{
					$this->publish( $IndexID );
				}
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	function createIndexLink( $IndexID, $targetNodeID )
	{
		global $iWPC;
		$IndexInfo = $this->getIndexInfo( $IndexID );
		$this->flushData( );
		$this->addData( "ParentIndexID", $IndexID );
		$this->addData( "ContentID", $IndexInfo['ContentID'] );
		$this->addData( "NodeID", $targetNodeID );
		$this->addData( "TableID", $IndexInfo['TableID'] );
		$this->addData( "Type", 2 );
		$this->addData( "PublishDate", time( ) );
		if ( $this->indexAdd( ) )
		{
			$NodeInfo = $iWPC->loadNodeInfo( $targetNodeID );
			if ( $NodeInfo['AutoPublish'] == 1 )
			{
				$this->publish( $this->db_insert_id );
			}
			return true;
		}
		else
		{
			return false;
		}
	}

	function indexDel( $IndexID )
	{
		global $table;
		global $db;
		$this->unpublish( $IndexID );
		$IndexInfo = $this->getIndexInfo( $IndexID );
		$where = "where IndexID=".$IndexID;
		$this->flushData( );
		$this->addData( "State", -1 );
		if ( $this->dataUpdate( $table->content_index, $where ) )
		{
			if ( $IndexInfo['Type'] == 1 )
			{
				$result = $db->Execute( "select IndexID from {$table->content_index} where IndexID !='{$IndexID}' AND ContentID='{$IndexInfo['ContentID']}' AND TableID='{$IndexInfo['TableID']}'" );
				while ( !$result->EOF )
				{
					$this->indexDel( $result->fields['IndexID'] );
					$result->MoveNext( );
				}
			}
			unset( $IndexInfo );
			unset( $result );
			return true;
		}
		else
		{
			return false;
		}
	}

	function contentHaveIndex( $ContentID )
	{
		global $table;
		global $db;
		$sql = "SELECT count(*) as nr FROM {$table->content_index}  WHERE ContentID='{$ContentID}' ";
		$result = $db->getRow( $sql );
		if ( 0 < $result['nr'] )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function getIndexLimit( $NodeID, $start = 0, $offset = 15, $State = "!= -1", $Type = "" )
	{
		global $table;
		global $db;
		global $iWPC;
		global $db_config;
		site_admin::isvalid( );
		content_table_admin::isvalid( );
		psn_admin::isvalid( );
		if ( $start == "" )
		{
			$start = 0;
		}
		if ( $offset == "" )
		{
			$offset = 15;
		}
		if ( !empty( $Type ) )
		{
			$pos = strpos( $Type, "=" );
			if ( $pos === false )
			{
				$Type = "AND i.Type={$Type}";
			}
			else
			{
				$Type = "AND i.Type {$Type}";
			}
		}
		$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
		if ( empty( $NodeInfo['TableID'] ) )
		{
			return false;
		}
		$table_name = $db_config['table_pre'].$db_config['table_content_pre']."_".$NodeInfo['TableID'];
		if ( $NodeInfo['NodeType'] == 3 )
		{
			$table_user = $db_config['table_pre']."plugin_oas_user";
			$sql = "SELECT i2.NodeID,i2.ContentID,i.State,i.Top,i.Pink,i.Sort,i2.URL,i.IndexID,i.PublishDate,i.Type,c.*,u.UserName as CreationUser FROM {$table->content_index} i LEFT JOIN {$table->content_index} i2 ON  i2.IndexID=i.ParentIndexID   LEFT JOIN {$table_name} c ON c.ContentID =i.ContentID  LEFT JOIN {$table->site} s ON s.NodeID='{$NodeID}' Left Join {$table_user} u ON u.UserID=c.CreationUserID where i.NodeID='{$NodeID}' AND i.State{$State} {$Type}   ORDER BY i.NodeID DESC,i.State DESC,i.Top DESC, i.PublishDate DESC, i.Sort DESC LIMIT {$start}, {$offset}";
		}
		else
		{
			$sql = "SELECT i2.NodeID,i2.ContentID,i.State,i.Top,i.Pink,i.Sort,i2.URL,i.IndexID,i.PublishDate,i.Type,c.*,u.uName as CreationUser FROM {$table->content_index} i LEFT JOIN {$table->content_index} i2 ON  i2.IndexID=i.ParentIndexID   LEFT JOIN {$table_name} c ON c.ContentID =i.ContentID  LEFT JOIN {$table->site} s ON s.NodeID='{$NodeID}' Left Join {$table->user} u ON u.uId=c.CreationUserID where i.NodeID='{$NodeID}' AND i.State{$State} {$Type}   ORDER BY i.NodeID DESC,i.State DESC,i.Top DESC, i.PublishDate DESC, i.Sort DESC LIMIT {$start}, {$offset}";
		}
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getIndexRecordNum( $NodeID, $State = "!= -1", $Type = "" )
	{
		global $table;
		global $db;
		if ( !empty( $Type ) )
		{
			$Type = "AND Type={$Type}";
		}
		$sql = "SELECT COUNT(*) as nr  FROM {$table->content_index} WHERE NodeID='{$NodeID}'  AND State{$State} {$Type} ";
		$result = $db->getRow( $sql );
		return $result['nr'];
	}

	function getIndexInfo( $IndexID, $field = "*" )
	{
		global $table;
		global $db;
		if ( isset( $Cache_IndexInfo[$IndexID] ) )
		{
			if ( $field != "*" )
			{
				return $Cache_IndexInfo[$IndexID][$field];
			}
			else
			{
				return $Cache_IndexInfo[$IndexID];
			}
		}
		else
		{
			$sql = "SELECT {$field}  FROM {$table->content_index} WHERE IndexID='{$IndexID}'";
			$result = $db->getRow( $sql );
			if ( $field != "*" )
			{
				return $result[$field];
			}
			else
			{
				$Cache_IndexInfo[$IndexID] = $result;
				return $result;
			}
		}
	}

	function getContentInfo( $IndexID )
	{
		global $table;
		global $db;
		global $iWPC;
		global $db_config;
		if ( isset( $Cache_ContentInfo[$IndexID] ) )
		{
			return $Cache_ContentInfo[$IndexID];
		}
		else
		{
			$result = $this->getIndexInfo( $IndexID );
			$NodeInfo = $iWPC->loadNodeInfo( $result['NodeID'] );
			if ( empty( $NodeInfo['TableID'] ) )
			{
				return false;
			}
			$table_name = $db_config['table_pre'].$db_config['table_content_pre']."_".$NodeInfo['TableID'];
			$sql = "SELECT i2.NodeID,i2.ContentID,i2.State,i2.Top,i2.Pink,i2.Sort,i2.URL,i2.SelfTemplate ,i2.SelfPSN,i2.SelfURL,i2.SelfPSNURL,i2.SelfPublishFileName,i.IndexID,i.PublishDate,i.Type,c.*,u.uName as CreationUser FROM {$table->content_index} i LEFT JOIN {$table->content_index} i2 ON  i2.IndexID=i.ParentIndexID   LEFT JOIN {$table_name} c ON   c.ContentID = i.ContentID LEFT JOIN {$table->site} s ON s.NodeID='{$result['NodeID']}' LEFT JOIN {$table->user} u ON u.uId=c.CreationUserID where i.NodeID='{$result['NodeID']}'   AND i.IndexID='{$IndexID}'";
			$result = $db->getRow( $sql );
			$result['TableID'] = $NodeInfo['TableID'];
			$Cache_ContentInfo[$IndexID] = $result;
			return $result;
		}
	}

	function getPublishInfo( $IndexID )
	{
		global $table;
		global $db;
		global $iWPC;
		global $db_config;
		if ( isset( $Cache_ContentInfo[$IndexID] ) )
		{
			return $Cache_ContentInfo[$IndexID];
		}
		else
		{
			$result = $this->getIndexInfo( $IndexID );
			$NodeInfo = $iWPC->loadNodeInfo( $result['NodeID'] );
			$table_publish = $db_config['table_pre'].$db_config['table_publish_pre']."_".$NodeInfo['TableID'];
			$table_content = $db_config['table_pre'].$db_config['table_content_pre']."_".$NodeInfo['TableID'];
			$plugin_table['oas']['user'] = $db_config['table_pre']."plugin_oas_user";
			if ( $NodeInfo['NodeType'] == 3 )
			{
				$sql = "SELECT p.*,u.UserName as CreationUserName,u.UserID as CreationUserID from {$table_publish} p, {$table_content} c, {$plugin_table['oas']['user']} u  where p.IndexID='{$IndexID}' AND c.ContentID=p.ContentID AND c.CreationUserID=u.UserID";
			}
			else
			{
				$sql = "SELECT * from {$table_publish} where IndexID='{$IndexID}'";
			}
			$result = $db->getRow( $sql );
			return $result;
		}
	}

	function editor_getContentInfo( $IndexID )
	{
		global $table;
		global $db;
		global $iWPC;
		global $db_config;
		global $CONTENT_MODEL_INFO;
		$sql = "SELECT ContentID,NodeID FROM {$table->content_index}  WHERE IndexID='{$IndexID}'";
		$result = $db->getRow( $sql );
		$NodeInfo = $iWPC->loadNodeInfo( $result['NodeID'] );
		$TableID =& $NodeInfo['TableID'];
		$table_name = $db_config['table_pre'].$db_config['table_content_pre']."_".$TableID;
		$TitleField = $CONTENT_MODEL_INFO[$TableID]['TitleField'];
		$sql = "SELECT i.IndexID, i.URL,c.{$TitleField} FROM {$table->content_index} i, {$table_name} c where i.ContentID =c.ContentID  AND i.IndexID='{$IndexID}'";
		$result = $db->getRow( $sql );
		$result['TableID'] = $TableID;
		return $result;
	}

	function getUnPublishLimit( $NodeID, $start, $offset )
	{
		global $db;
		global $table;
		$sql = "SELECT IndexID From {$table->content_index}  WHERE State=0 AND NodeID={$NodeID} LIMIT {$start}, {$offset}";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getPublishLimit( $NodeID, $start, $offset )
	{
		global $db;
		global $table;
		$sql = "SELECT IndexID From {$table->content_index}  WHERE State=1 AND NodeID={$NodeID} LIMIT {$start}, {$offset}";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function IsRecordExists( $params )
	{
		global $db_config;
		global $iWPC;
		global $db;
		global $table;
		$NodeInfo = $iWPC->loadNodeInfo( $params['NodeID'] );
		$table_name = $db_config['table_pre'].$db_config['table_content_pre']."_".$NodeInfo['TableID'];
		if ( $params['o'] == "add" )
		{
			$sql = "SELECT t.{$params['FieldName']} FROM {$table_name} t, {$table->content_index} i WHERE t.{$params['FieldName']}='{$params['FieldValue']}' AND i.NodeID={$params['NodeID']} AND i.ContentID=t.ContentID AND i.State!=-1";
		}
		else if ( $params['o'] == "edit" )
		{
			$sql = "SELECT t.{$params['FieldName']},t.ContentID as tContentID,i.* FROM {$table_name} t, {$table->content_index} i WHERE t.{$params['FieldName']}='{$params['FieldValue']}'  AND i.ContentID=t.ContentID AND i.IndexID!={$params['IndexID']} AND i.NodeID={$params['NodeID']} AND i.State!=-1";
		}
		$result = $db->getRow( $sql );
		if ( empty( $result[$params['FieldName']] ) )
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	function contentAdd( $NodeID, $IndexInfo )
	{
		global $db_config;
		global $iWPC;
		global $IN;
		
		$PublishDate = empty( $IndexInfo['PublishDate'] ) ? time( ) : $IndexInfo['PublishDate'];
		$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
		$table_name = $db_config['table_pre'].$db_config['table_content_pre']."_".$NodeInfo['TableID'];
//		echo $table_name.'<br/>';
//		exit();
		if ( $this->dataInsert( $table_name ) )
		{
			$this->flushData();
			$this->addData( "ContentID", $this->db_insert_id );
			$this->addData( "NodeID", $NodeID );
			$this->addData( "PublishDate", $PublishDate );
			$this->addData( "Type", 1 );
			$this->addData( "TableID", $NodeInfo['TableID'] );
			if ( $this->indexAdd( ) )  
			{
				$IndexID = $this->db_insert_id;
				$IN['IndexID'] = $IndexID;
				$this->flushData( );
				$this->addData( "ParentIndexID", $IndexID );
				$this->addData( $IndexInfo );
				if ( !empty( $IndexInfo['SelfURL'] ) )
				{
					$this->addData( "URL", $IndexInfo['SelfURL'] );
					$this->addData( "Type", 4 );
				}
				else
				{
					$this->addData( "Type", 1 );
				}
				$this->delData( "SubTargetNodeID" );
				$this->delData( "IndexTargetNodeID" );
				$this->indexEdit( $IndexID ); //出错点
				$db_insert_id = $this->db_insert_id;
				if ( $NodeInfo['AutoPublish'] == 1 )
				{
					$this->publish( $this->db_insert_id );
				}
				$this->db_insert_id = $db_insert_id;
				if ( !class_exists( "site_admin" ) )
				{
					require_once( INCLUDE_PATH."/admin/site_admin.class.php" );
				}
				$SonNodes = site_admin::getsonnode( $NodeID );
				if ( !empty( $SonNodes ) )
				{
					foreach ( $SonNodes as $key => $var )
					{
						$this->createLink( $IndexID, $var );
					}
				}
				if ( !empty( $IndexInfo['SubTargetNodeID'] ) )
				{
					foreach ( $IndexInfo['SubTargetNodeID'] as $key => $var )
					{
						$this->createLink( $IndexID, $var );
					}
				}
				if ( !empty( $IndexInfo['IndexTargetNodeID'] ) )
				{
					foreach ( $IndexInfo['IndexTargetNodeID'] as $key => $var )
					{
						$this->createIndexLink( $IndexID, $var );
					}
				}
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	function imageContentAdd( $NodeID, $PublishDate )
	{
		global $db_config;
		global $iWPC;
		$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
		$table_name = $db_config['table_pre'].$db_config['table_content_pre']."_".$NodeInfo['TableID'];
		if ( $this->dataInsert( $table_name ) )
		{
			$this->flushData( );
			$this->addData( "ContentID", $this->db_insert_id );
			$this->addData( "NodeID", $NodeID );
			$this->addData( "Type", 3 );
			$this->addData( "State", 1 );
			$this->addData( "PublishDate", $PublishDate );
			if ( $this->indexAdd( ) )
			{
				$IndexID = $this->db_insert_id;
				$this->flushData( );
				$this->addData( "ParentIndexID", $IndexID );
				$this->indexEdit( $IndexID );
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	function contentEdit( $IndexID, $IndexInfo = "" )
	{
		global $db_config;
		global $iWPC;
		$indexInfo = $this->getIndexInfo( $IndexID );
		$ContentID = $indexInfo['ContentID'];
		$NodeID = $indexInfo['NodeID'];
		$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
		$table_name = $db_config['table_pre'].$db_config['table_content_pre']."_".$NodeInfo['TableID'];
		$where = "where ContentID=".$ContentID;
		if ( $this->dataUpdate( $table_name, $where ) )
		{
			$this->flushData( );
			if ( !empty( $IndexInfo ) )
			{
				$this->addData( $IndexInfo );
			}
			if ( !empty( $IndexInfo['SelfURL'] ) )
			{
				$this->addData( "URL", $IndexInfo['SelfURL'] );
				$this->addData( "Type", 4 );
			}
			else
			{
				$this->addData( "Type", 1 );
			}
			if ( $this->indexEdit( $IndexID ) )
			{
				if ( $NodeInfo['AutoPublish'] == "1" )
				{
					$this->refresh( $IndexID );
				}
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	function extraUpdate( $ContentID, $eId )
	{
		global $db_config;
		$table_name = $db_config['table_pre'].$db_config['table_content_pre']."_".$eId;
		$where = "where ContentID=".$ContentID;
		if ( $this->dataUpdate( $table_name, $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function publish( $IndexID )
	{
		global $iWPC;
		global $table;
		global $db;
		global $db_config;
		$info = $this->getIndexInfo( $IndexID );
		psn_admin::isvalid();
		$NodeInfo = $iWPC->loadNodeInfo( $info['NodeID'] );
		if ( $info['State'] == 1 )
		{
			return true;
		}
		else if ( $info['Type'] == 2 )
		{
			$this->flushData( );
			$this->addData( "State", 1 );
			$where = "where IndexID=".$IndexID;
			if ( $this->dataUpdate( $table->content_index, $where ) )
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else if ( $info['Type'] == 4 )
		{
			if ( $this->publishURL( $IndexID ) )
			{
				$this->flushData( );
				$this->addData( "State", 1 );
				$where = "where IndexID=".$IndexID;
				if ( $this->dataUpdate( $table->content_index, $where ) )
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		switch ( $NodeInfo['PublishMode'] )
		{
		case "0" :
			return true;
		case "1" :
		case "3" :
			if ( $this->publishMakeHtml( $IndexID ) )
			{
				$this->flushData( );
				$this->addData( "State", 1 );
				$time=time();
				$this->addData( "PublishDate", $time );
				$where = "where IndexID=".$IndexID;
				if ( $this->dataUpdate( $table->content_index, $where ) )
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		case "2" :
			if ( $this->publishDynamic( $IndexID ) )
			{
				$this->flushData( );
				$this->addData( "State", 1 );
				$where = "where IndexID=".$IndexID;
				if ( $this->dataUpdate( $table->content_index, $where ) )
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
	}

	function unpublish( $IndexID, $mode = 1 )
	{
		global $table;
		global $db;
		global $iWPC;
		global $db_config;
		global $Plugin;
		$info = $this->getIndexInfo( $IndexID );
		$NodeInfo = $iWPC->loadNodeInfo( $info['NodeID'] );
		if ( $info['Type'] == 2 || $info['Type'] == 4 )
		{
			$this->flushData( );
			$this->addData( "State", 0 );
			$where = "where IndexID=".$IndexID;
			if ( $this->dataUpdate( $table->content_index, $where ) )
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		$this->flushData( );
		$this->addData( "State", 0 );
		$where = "where IndexID=".$IndexID;
		if ( $this->dataUpdate( $table->content_index, $where ) )
		{
			if ( $info['Type'] == 1 || $info['Type'] == 0 || $info['Type'] == 3 )
			{
				$this->clearPublishedItem( $info['ContentID'], $info['NodeID'], $mode );
			}
			$this->publishDel( $IndexID, $mode );
			$publishInfo = $this->getContentInfo( $IndexID );
			include( SETTING_DIR."content.ini.php" );
			$publishInfo[$mainContentLabel];
			$parseInfo = $this->_htmlPhoto_parseContent( $publishInfo[$mainContentLabel] );
			if ( !empty( $parseInfo['IndexIDs'] ) )
			{
				foreach ( $parseInfo['IndexIDs'] as $key => $var )
				{
					$this->unpublish( $var, $mode );
				}
			}
			return true;
		}
		else
		{
			return false;
		}
	}

	function clearPublishedItem( $ContentID, $NodeID, $mode = 1 )
	{
		global $db;
		global $table;
		$sql = "SELECT *  FROM {$table->publish_log}  WHERE ContentID={$ContentID} AND NodeID={$NodeID} ";
		$psn =& $this->beanFactory->getBean( "psn" );
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$psn->connect( $result->fields['PSN'] );
			$psn->delFile( "", $result->fields['FileName'] );
			$result->MoveNext( );
		}
		$sql = "DELETE  FROM {$table->publish_log}  WHERE ContentID='{$ContentID}' AND NodeID='{$NodeID}' ";
		if ( $result = $db->query( $sql ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
   /** 发布**/
	function refresh( $IndexID )
	{
		global $iWPC;
		global $table;
		global $db;
		global $db_config;
		$info = $this->getIndexInfo( $IndexID );
		$NodeInfo = $iWPC->loadNodeInfo( $info['NodeID'] );
		
		if ( $info['State'] == 0 )
		{
			return true;
		}
		else if ( $info['Type'] == 2 )
		{
			return true;
		}
		else if ( $info['Type'] == 4 )
		{
			return $this->publishURL( $IndexID );
		}
		switch ( $NodeInfo['PublishMode'] )
		{
		case "0" :
			return true;
		case "1" :
		case "3" :
			if ( $this->publishMakeHtml( $IndexID ) )
			{
				return true;
			}
			else
			{
				return false;
			}
		case "2" :
			if ( $this->publishDynamic( $IndexID ) )
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	function refreshIndex( $NodeID, $tplname, $filename )
	{
		global $iWPC;
		global $SYS_ENV;
		$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
		$this->NodeInfo =& $NodeInfo;
		switch ( $NodeInfo['PublishMode'] )
		{
		case "0" :
			return true;
		case "2" :
		case "3" :
			$template = new kTemplate( );
			$template->template_dir = SYS_PATH."templates";
			$cacheId = $NodeID."0".$NodeInfo['IndexTpl'];
			$i = 0;
			for ( ;	$i <= 10;	++$i	)
			{
				$template->clear_cache( $NodeInfo['IndexTpl'], $NodeID.$i.$NodeInfo['IndexTpl'] );
			}
			return true;
		}
		$filename = formatpublishfile( $filename );
		$template = new kTemplate( );
		$template->template_dir = $SYS_ENV['templatePath'];
		$template->compile_dir = SYS_PATH."sysdata/templates_c/";
		$template->assign( "NodeInfo", $NodeInfo );
		$template->assign( "cms_version", CMSWARE_VERSION );
		if ( !class_exists( "TplVarsAdmin" ) )
		{
			require_once( INCLUDE_PATH."admin/TplVarsAdmin.class.php" );
		}
		$tpl_vars = TplVarsAdmin::getall( );
		foreach ( $tpl_vars as $key => $var )
		{
			if ( $var['IsGlobal'] )
			{
				$template->assign( $var['VarName'], $var['VarValue'] );
			}
			else if ( preg_match( "/,".$this->NodeInfo['NodeID']."/isU", $var['NodeScope'] ) )
			{
				$template->assign( $var['VarName'], $var['VarValue'] );
			}
			else
			{
				foreach ( explode( "%", $this->NodeInfo['ParentNodeID'] ) as $varIn )
				{
					if ( preg_match( "/all-".$varIn."/isU", $var['NodeScope'] ) )
					{
						$template->assign( $var['VarName'], $var['VarValue'] );
					}
				}
			}
		}
		if ( !empty( $tplname ) )
		{
			if ( !file_exists( $template->template_dir.$tplname ) )
			{
				new Error( "Error: The index template  \\'{$template->template_dir}{ {$tplname}}\\' does not exits, Please Set it First to run." );
				return false;
			}
		}
		else
		{
			new Error( "Error: You have not set the index template, Please Set it First." );
			return false;
		}
		$template->registerPreFilter( "CMS_Parser" );
		$output = $template->fetch( $tplname, 1 );
		$output = restorexmlheader( $output );
		$this->IndexID = 0;
		$this->publishInfo['NodeID'] = $NodeID;
		$this->publishInfo['ContentID'] = 0;
		if ( $this->_publishing( $filename, $output ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function fetchIndex( $NodeID, $template_dir = "", $templatefilename = "" )
	{
		global $iWPC;
		global $SYS_ENV;
		global $IN;
		if ( !empty( $NodeID ) )
		{
			$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
		}
		else
		{
			$NodeInfo['NodeID'] = 0;
		}
		$this->NodeInfo =& $NodeInfo;
		$tplname = empty( $templatefilename ) ? $NodeInfo['IndexTpl'] : $templatefilename;
		$template = new kTemplate( );
		$template->template_dir = empty( $template_dir ) ? $SYS_ENV['templatePath'] : $template_dir;
		$template->compile_dir = SYS_PATH."sysdata/templates_c/";
		$template->assign( "NodeInfo", $NodeInfo );
		$template->assign( "cms_version", CMSWARE_VERSION );
		if ( !empty( $IN['IndexID'] ) )
		{
			$publishInfo = $this->getPublishInfo( $IN['IndexID'] );
			foreach ( $publishInfo as $key => $var )
			{
				$template->assign( $key, $var );
			}
		}
		if ( !class_exists( "TplVarsAdmin" ) )
		{
			require_once( INCLUDE_PATH."admin/TplVarsAdmin.class.php" );
		}
		$tpl_vars = TplVarsAdmin::getall( );
		foreach ( $tpl_vars as $key => $var )
		{
			if ( $var['IsGlobal'] )
			{
				$template->assign( $var['VarName'], $var['VarValue'] );
			}
			else if ( preg_match( "/,".$this->NodeInfo['NodeID']."/isU", $var['NodeScope'] ) )
			{
				$template->assign( $var['VarName'], $var['VarValue'] );
			}
			else
			{
				foreach ( explode( "%", $this->NodeInfo['ParentNodeID'] ) as $varIn )
				{
					if ( preg_match( "/all-".$varIn."/isU", $var['NodeScope'] ) )
					{
						$template->assign( $var['VarName'], $var['VarValue'] );
					}
				}
			}
		}
		if ( !empty( $tplname ) )
		{
			if ( !file_exists( $template->template_dir.$tplname ) )
			{
				new Error( "Error: The index template  \\'{$template->template_dir}{ {$tplname}}\\' does not exits, Please Set it First to run." );
				return false;
			}
		}
		else
		{
			new Error( "Error: You have not set the index template, Please Set it First." );
			return false;
		}
		$template->registerPreFilter( "CMS_Parser" );
		$output = $template->fetch( $tplname, 1 );
		$output = restorexmlheader( $output );
		return $output;
	}

	function publishURL( $IndexID )
	{
		global $db;
		global $db_config;
		global $Plugin;
		global $table;
		global $SYS_ENV;
		global $TPL;
		global $SMARTY_VAR;
		global $iWPC;
		$NodeID = $this->getIndexInfo( $IndexID, $field = "NodeID" );
		$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
		$publishInfo = $this->getContentInfo( $IndexID );
		$this->IndexID = $IndexID;
		$this->NodeInfo =& $NodeInfo;
		$this->publishInfo = $publishInfo;
		$this->flushData( );
		$this->addData( "URL", $publishInfo['SelfURL'] );
		$this->URL = $publishInfo['SelfURL'];
		$right = $this->indexEdit( $IndexID );
		$publishInfo[$mainContentLabel] = $this->ReplaceKeywords( $publishInfo[$mainContentLabel] );
		foreach ( $publishInfo as $key => $var )
		{
			$this->resourcePublish( $publishInfo[$key] );
			$this->htmlPhotoPublish( $publishInfo[$key] );
			$this->psnPublish( $publishInfo[$key] );
		}
		$FieldsInfo = content_table_admin::gettablefieldsinfo( $NodeInfo['TableID'] );
		$this->flushData( );
		foreach ( $FieldsInfo as $key => $var )
		{
			if ( empty( $var['EnablePublish'] ) )
			{
				continue;
			}
			$this->addData( $var['FieldName'], $publishInfo[$var['FieldName']] );
		}
		$this->addData( "IndexID", $publishInfo['IndexID'] );
		$this->addData( "ContentID", $publishInfo['ContentID'] );
		$this->addData( "NodeID", $publishInfo['NodeID'] );
		$this->addData( "PublishDate", $publishInfo['PublishDate'] );
		$this->addData( "URL", $this->URL );
		$publishInfo['URL'] = $this->URL;
		$this->publishUpdate( $NodeInfo['TableID'] );
		if ( isset( $Plugin ) )
		{
			$Plugin->update( $publishInfo );
		}
		return $right;
	}

	function publishDynamic( $IndexID )
	{
		global $db;
		global $db_config;
		global $Plugin;
		global $table;
		global $SYS_ENV;
		global $TPL;
		global $SMARTY_VAR;
		global $iWPC;
		$NodeID = $this->getIndexInfo( $IndexID, $field = "NodeID" );
		$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
		$publishInfo = $this->getContentInfo( $IndexID );
		$fieldInfo = content_table_admin::gettablefieldsinfo( $NodeInfo['TableID'] );
		foreach ( $fieldInfo as $key => $var )
		{
			if ( !empty( $var['EnablePublish'] ) )
			{
			}
			else
			{
				unset( $publishInfo[$var['FieldName']] );
			}
		}
		$this->IndexID = $IndexID;
		$this->NodeInfo =& $NodeInfo;
		$this->publishInfo = $publishInfo;
		include( SETTING_DIR."content.ini.php" );
		$PublishFileFormat = $NodeInfo['ContentPortalURL'];
		foreach ( $filenameFormatMap as $key => $var )
		{
			$PublishFileFormat = str_replace( $key, $var, $PublishFileFormat );
		}
		$publishInfo[$mainContentLabel] = $this->ReplaceKeywords( $publishInfo[$mainContentLabel] );
		foreach ( $publishInfo as $key => $var )
		{
			$publishInfo[$key] = $this->convertPSN2URL( $var );
			$this->resourcePublish( $publishInfo[$key] );
			$this->htmlPhotoPublish( $publishInfo[$key] );
			$this->psnPublish( $publishInfo[$key] );
		}
		eval( "\$publishFileName = \"{$PublishFileFormat}\";" );
		$publishFileName = str_replace( "{Page}", 0, $publishFileName );
		$publishFileName = str_replace( "{IndexID}", $IndexID, $publishFileName );
		$this->flushData( );
		$this->addData( "URL", $publishFileName );
		$this->URL = $publishFileName;
		$right = $this->indexEdit( $IndexID );
		if ( $this->publishInfo['Type'] == 1 || $this->publishInfo['Type'] == 0 || $this->publishInfo['Type'] == 3 )
		{
			$FieldsInfo = content_table_admin::gettablefieldsinfo( $NodeInfo['TableID'] );
			$TPL->clear_cache( $NodeInfo['ContentTpl'], $IndexID."0".$NodeInfo['ContentTpl'] );
			$this->flushData( );
			foreach ( $FieldsInfo as $key => $var )
			{
				if ( empty( $var['EnablePublish'] ) )
				{
					continue;
				}
				$this->addData( $var['FieldName'], $publishInfo[$var['FieldName']] );
			}
			$this->addData( "IndexID", $publishInfo['IndexID'] );
			$this->addData( "ContentID", $publishInfo['ContentID'] );
			$this->addData( "NodeID", $publishInfo['NodeID'] );
			$this->addData( "PublishDate", $publishInfo['PublishDate'] );
			$this->addData( "URL", $this->URL );
			$publishInfo['URL'] = $this->URL;
			$this->publishUpdate( $NodeInfo['TableID'] );
			if ( isset( $Plugin ) )
			{
				$Plugin->update( $publishInfo );
			}
		}
		return $right;
	}

	function publishUpdate( $TableID )
	{
		global $table;
		global $db;
		global $iWPC;
		global $db_config;
		$table_publish = $db_config['table_pre'].$db_config['table_publish_pre']."_".$TableID;
		if ( $this->dataReplace( $table_publish ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function publishDel( $IndexID, $mode = 1 )
	{
		global $table;
		global $db;
		global $iWPC;
		global $db_config;
		global $Plugin;
		$NodeID = $this->getIndexInfo( $IndexID, $field = "NodeID" );
		if ( !is_object( $Plugin ) )
		{
			require_once( INCLUDE_PATH."admin/plugin.class.php" );
			$Plugin = new Plugin( );
		}
		$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
		$table_publish = $db_config['table_pre'].$db_config['table_publish_pre']."_".$NodeInfo['TableID'];
		if ( $this->dataDel( $table_publish, "IndexID", $IndexID, "=" ) )
		{
			if ( $mode == 1 )
			{
				$Plugin->del( $IndexID );
			}
			return true;
		}
		else
		{
			return false;
		}
	}

	function publishMakeHtml( $IndexID )
	{
		global $db;
		global $db_config;
		global $Plugin;
		global $table;
		global $SYS_ENV;
		global $TPL;
		global $SMARTY_VAR;
		global $iWPC;
		$NodeID = $this->getIndexInfo( $IndexID, $field = "NodeID" );
		$IN['NodeID'] = empty( $IN['NodeID'] ) ? $NodeID : $IN['NodeID'];
		$NodeInfo = $iWPC->loadNodeInfo( $NodeID );
		$publishInfo = $this->getContentInfo( $IndexID );
		
		$fieldInfo = content_table_admin::gettablefieldsinfo( $NodeInfo['TableID'] );
		foreach ( $fieldInfo as $key => $var )
		{
			if ( !empty( $var['EnablePublish'] ) )
			{
				
			}
			else
			{
				unset( $publishInfo[$var['FieldName']] );
			}
		}
		$this->IndexID = $IndexID;
		$this->NodeInfo =& $NodeInfo;
		$this->publishInfo = $publishInfo;
		$template = new kTemplate( );
		$template->assign( "cms_version", CMSWARE_VERSION );
		if ( !class_exists( "TplVarsAdmin" ) )
		{
			require_once( INCLUDE_PATH."admin/TplVarsAdmin.class.php" );
		}
		$tpl_vars = TplVarsAdmin::getall( );
		
		
		foreach ( $tpl_vars as $key => $var )
		{
			if ( $var['IsGlobal'] )
			{
				$template->assign( $var['VarName'], $var['VarValue'] );
			}
			else if ( preg_match( "/,".$this->NodeInfo['NodeID']."/isU", $var['NodeScope'] ) )
			{
				$template->assign( $var['VarName'], $var['VarValue'] );
			}
			else
			{
				foreach ( explode( "%", $this->NodeInfo['ParentNodeID'] ) as $varIn )
				{
					if ( preg_match( "/all-".$varIn."/isU", $var['NodeScope'] ) )
					{
						$template->assign( $var['VarName'], $var['VarValue'] );
					}
				}
			}
		}
		if ( $this->publishInfo['Type'] == 3 )
		{
			$template->template_dir = $SYS_ENV['templatePath'];
			if ( !file_exists( $template->template_dir.$this->NodeInfo['ImageTpl'] ) )
			{
				new Error( "Error: The Image template {$template->template_dir}{$this->NodeInfo['ImageTpl']} you have set  for your Image does not exists, system now use the default template \"{$SYS_ENV['templatePath']}/default/image.html\" to run." );
				$tplname = "image.html";
				$template->template_dir = $SYS_ENV['templatePath']."/default/";
			}
			else
			{
				$tplname = $this->NodeInfo['ImageTpl'];
				$template->template_dir = $SYS_ENV['templatePath'];
			}
		}
		else if ( !empty( $this->publishInfo['SelfTemplate'] ) )
		{
			if ( preg_match( "/\\{TID:([0-9]+)\\}/isU", $this->publishInfo['SelfTemplate'], $matches ) )
			{
				require_once( INCLUDE_PATH."admin/cate_tpl_admin.class.php" );
				if ( !isset( $cate_tpl ) )
				{
					$cate_tpl = new cate_tpl_admin( );
					$TID = $matches[1];
					$TInfo = $cate_tpl->getInfo( $TID );
					$this->publishInfo['SelfTemplate'] = "/ROOT/".$TInfo['TCID']."/".$TInfo['TID'].".tpl";
				}
			}
			$tplname = $this->publishInfo['SelfTemplate'];
			$template->template_dir = $SYS_ENV['templatePath'];
			if ( !file_exists( $template->template_dir.$this->publishInfo['SelfTemplate'] ) )
			{
				new Error( "Error: The content template {$template->template_dir}{$this->publishInfo['Template']} you have set alone for your content does not exists, system now use the default template \"{$SYS_ENV['templatePath']}/default/content.html\" to run." );
				$tplname = "content.html";
				$template->template_dir = $SYS_ENV['templatePath']."/default/";
			}
		}
		else if ( !empty( $this->NodeInfo['ContentTpl'] ) )
		{
			$tplname = $this->NodeInfo['ContentTpl'];
			$template->template_dir = $SYS_ENV['templatePath'];
			if ( !file_exists( $template->template_dir.$this->NodeInfo['ContentTpl'] ) )
			{
				new Error( "Error: The content template {$template->template_dir}{$this->NodeInfo['ContentTpl']} does not exists, system now use the default template \"{$SYS_ENV['templatePath']}/default/content.html\" to run." );
				$tplname = "content.html";
				$template->template_dir = $SYS_ENV['templatePath']."/default/";
			}
		}
		else
		{
			new Error( "Warning: You haven\\'t set the content template, system now use the default template \"{$SYS_ENV['templatePath']}/default/content.html\" to run." );
			$tplname = "content.html";
			$template->template_dir = $SYS_ENV['templatePath']."/default/";
		}
		$template->compile_dir = SYS_PATH."sysdata/templates_c/";
		$template->registerPreFilter( "CMS_Parser" );
		include( SETTING_DIR."content.ini.php" );   //手动分页开始  初始参数
		$PublishFileFormat = $NodeInfo['PublishFileFormat'];
		foreach ( $filenameFormatMap as $key => $var )
		{
			$PublishFileFormat = str_replace( $key, $var, $PublishFileFormat );
		}
		$publishInfo[$mainContentLabel] = $this->ReplaceKeywords( $publishInfo[$mainContentLabel] );
		foreach ( $publishInfo as $key => $var )
		{
			$PublishFileFormat = str_replace( "{".$key."}", addslashes( $var ), $PublishFileFormat );
			$publishInfo[$key] = $this->convertPSN2URL( $var );
			$this->resourcePublish( $publishInfo[$key] );
			$this->htmlPhotoPublish( $publishInfo[$key] );
			$this->psnPublish( $publishInfo[$key] );
			$template->assign( $key, $publishInfo[$key] );
		}
		$PublishFileFormat = formatpublishfile( $PublishFileFormat );
		$publishInfo['NodeInfo'] = $NodeInfo;
		$template->assign_by_ref( "Publish", $publishInfo );
		$template->assign( "Navigation", $Navigation );
		$template->assign( "sysRelateDoc", $RelateDoc );
		$template->assign_by_ref( "NodeInfo", $NodeInfo );
		$template->registerPreFilter( "CMS_Parser" ); //给模板变量赋值
		
		//前边没有生成页面
		
		
		if ( empty( $this->NodeInfo['Pager'] ) )
		{
			$this->NodeInfo['Pager'] = "default.php";
		}
		include( SETTING_DIR."pager/".$this->NodeInfo['Pager'] );
		

  //已经生成完成
		$this->flushData( );
		$URL = $this->getHtmlURL( $publishFileName );
		print $URL."<br>";
		$this->addData( "URL", $URL );
		$this->URL = $URL;
		$publishInfo['URL'] = $this->URL;
		$this->indexEdit( $IndexID );
		
		return $right;
	}

	function htmlPhotoPublish( &$content )
	{
		global $table;
		$parseInfo = $this->_htmlPhoto_parseContent( $content );
		$publishInfo1 = $this->publishInfo;
		if ( !empty( $parseInfo['IndexIDs'] ) )
		{
			foreach ( $parseInfo['IndexIDs'] as $key => $var )
			{
				if ( $this->publishMakehtml( $var ) )
				{
					$this->flushData( );
					$this->addData( "State", 1 );
					$where = "where IndexID=".$var;
					$this->dataUpdate( $table->content_index, $where );
				}
				$content = str_replace( $parseInfo['matches'][$key], $this->URL, $content );
			}
		}
		$this->publishInfo = $publishInfo1;
	}

	function _htmlPhoto_parseContent( $content )
	{
		$_Image_Pattern = "/cmsware:\\/\\/publish\\/url.cw\\?IndexID=([0-9]+)/ise";
		if ( preg_match_all( $_Image_Pattern, $content, $match, PREG_PATTERN_ORDER ) )
		{
			$returnValue = array(
				"IndexIDs" => $match[1],
				"matches" => $match[0]
			);
		}
		if ( is_array( $returnValue['IndexIDs'] ) )
		{
			$returnValue['IndexIDs'] = array_unique( $returnValue['IndexIDs'] );
		}
		if ( is_array( $returnValue['matches'] ) )
		{
			$returnValue['matches'] = array_unique( $returnValue['matches'] );
		}
		return $returnValue;
	}

	function psnPublish( &$content )
	{
		$psn = new psn_admin( );
		$patt = "/\\{PSN-URL:([0-9]+)\\}/isU";
		if ( preg_match_all( $patt, $content, $matches ) )
		{
			foreach ( $matches[0] as $key => $var )
			{
				$psnInfo = $psn->getPSNInfo( $matches[1][$key] );
				$content = str_replace( $matches[0][$key], $psnInfo['URL'], $content );
			}
		}
		unset( $psn );
	}

	function resourcePublish( &$content )
	{
		global $db;
		global $table;
		global $SYS_ENV;
		$ImgArray = $this->_parseContent( $content );
		if ( !empty( $ImgArray ) )
		{
			$sql = "SELECT varValue as num FROM {$table->sys} WHERE  varName ='publishResourceNum'";
			$row = $db->getRow( $sql );
			$this->publish_num = $row['num'];
			$localImgArray = $this->_resourcePublishing( $ImgArray );
			if ( $localImgArray )
			{
				$this->_output( $content, $ImgArray, $localImgArray );
			}
			else
			{
				return false;
			}
		}
		else
		{
			return true;
		}
	}

	function publishAll( &$string )
	{
		$this->psnPublish( $string );
		$this->cmsware_stream_parse( $string );
		$this->resourcePublish( $string );
	}

	function cmsware_stream_parse( &$string )
	{
		$parseInfo = $this->_htmlPhoto_parseContent( $string );
		if ( !empty( $parseInfo['IndexIDs'] ) )
		{
			foreach ( $parseInfo['IndexIDs'] as $key => $var )
			{
				$url = $this->getIndexInfo( $var, "URL" );
				$string = str_replace( $parseInfo['matches'][$key], $url, $string );
			}
		}
	}

	function getPublishLog( $ContentID, $NodeID )
	{
		global $db;
		global $table;
		global $SYS_ENV;
		$publishLog = array( );
		$result = $db->Execute( "select FileName from {$table->publish_log} where ContentID='{$ContentID}' AND NodeID='{$NodeID}'" );
		while ( !$result->EOF )
		{
			$pathinfo = pathinfo( $result->fields['FileName'] );
			$publishLog[$pathinfo['basename']] = $result->fields['FileName'];
			$result->MoveNext( );
		}
		return $publishLog;
	}

	function _resourcePublishing( $ImgArray )
	{
		global $db;
		global $table;
		global $SYS_ENV;
		if ( !is_array( $ImgArray ) )
		{
			return false;
		}
		$isUploadResources = array( );
		$psn =& $this->beanFactory->getBean( "psn" );
		$publishLog = $this->getPublishLog( $this->publishInfo['ContentID'], $this->publishInfo['NodeID'] );
		$patt = "/{PSN:([0-9]+)}([\\S]*)/is";
		preg_match( $patt, $this->NodeInfo['ResourcePSN'], $matches );
		$PSNID = $matches[1];
		$publish_path = $matches[2];
		$psnInfo = $psn->getPSNInfo( $PSNID );
		$psn->connect( $psnInfo['PSN'] );
		$psn->sendVar['IndexID'] = $this->IndexID;
		$psn->sendVar['NodeID'] = $this->publishInfo['NodeID'];
		$psn->sendVar['ContentID'] = $this->publishInfo['ContentID'];
		foreach ( $ImgArray as $key => $var )
		{
			$pathinfo = pathinfo( $var );
			$sql = "select Name,Path  from {$table->resource} where Name = '{$pathinfo['basename']}'";
			$result = $db->getRow( $sql );
			$filename = $SYS_ENV['ResourcePath']."/".$result['Path'];
			if ( empty( $result['Name'] ) )
			{
				$saveFile[$key] = $var;
			}
			else if ( !empty( $publishLog[$pathinfo['basename']] ) )
			{
				if ( !in_array( $filename, $isUploadResources ) )
				{
					$isUploadResources[] = $filename;
					$psn->upload( $filename, $publishLog[$pathinfo['basename']] );
					$saveFile[$key] = $this->getPSNURL( ).$publishLog[$pathinfo['basename']];
					echo "PublishLog: ".$saveFile[$key]."<br>";
				}
			}
			else if ( !in_array( $filename, $isUploadResources ) )
			{
				$isUploadResources[] = $filename;
				$dataPath = $this->makeAutoPath( );
				$destination = $dataPath."/".$pathinfo['basename'];
				$saveFile[$key] = $this->getResourceURL( )."/".$destination;
				$psn->upload( $filename, $publish_path."/".$destination, $saveFile[$key] );
				if ( !psn_admin::logexits( $psn->sendVar['ContentID'], $psnInfo['PSN'], $publish_path."/".$destination ) )
				{
					$this->Counter( );
				}
				echo "Publishing: ".$saveFile[$key]."<br>";
			}
		}
		$psn->close( );
		return $saveFile;
	}

	function makeAutoPath( )
	{
		$num = $this->publish_num;
		$num = strval( $num );
		$add_zero = 8 - strlen( $num );
		$num = str_repeat( "0", $add_zero ).$num;
		$DirSecond = "h".substr( $num, 0, 3 );
		$DirFirst = "h".substr( $num, -5, 2 );
		return $DirSecond."/".$DirFirst;
	}

	function Counter( $num = 1 )
	{
		global $db;
		global $table;
		$sql = "UPDATE {$table->sys} SET `varValue`=varValue +1  where varName='publishResourceNum'";
		$row = $db->query( $sql );
	}

	function _parseContent( &$content )
	{
		global $SYS_ENV;
		$_Image_Pattern = array(
			"1" => array( "pattern" => "/<img[\\s]*[^><]*[\\s]*src=[\"]?([^\"><\\s]*.[jpg|gif|png|jpeg])[\"]?[\\s]*[^><]*>/ise", "dataKey" => "1" ),
			"2" => array( "pattern" => "/href=\"([^\"><\\s]*.[jpg|gif|png|jpeg])\"/ise", "dataKey" => "1" ),
			"3" => array(
				"pattern" => "/(\\.\\.\\/resource\\/[^\"><\\s]*.[jpg|gif|png|jpeg|".$SYS_ENV['upAttachType']."])/ise",
				"dataKey" => "1"
			)
		);
		$matches = array( );
		foreach ( $_Image_Pattern as $key => $var )
		{
			$datakey = $var['dataKey'];
			if ( preg_match_all( $var['pattern'], $content, $match, PREG_PATTERN_ORDER ) )
			{
				$matches = array_merge( $match[$datakey], $matches );
			}
		}
		$img_data = $matches;
		if ( is_array( $img_data ) )
		{
			array_unique( $img_data );
			$img_data = $this->_imgLocalFilter( $img_data );
		}
		return $img_data;
	}

	function _imgLocalFilter( $img_data )
	{
		global $SYS_ENV;
		preg_match_all( "/{([^}]+)}/siU", $SYS_ENV['localImgIgnoreURL'], $matches );
		$ignoreURLs = $matches[1];
		foreach ( $img_data as $var )
		{
			$urlinfo = parse_url( $var );
			$urlinfo['host'] = strtolower( $urlinfo['host'] );
			if ( in_array( $urlinfo['host'], $ignoreURLs ) )
			{
				$return[] = $var;
			}
			else if ( empty( $urlinfo['host'] ) )
			{
				$return[] = $var;
			}
		}
		return $return;
	}

	function _output( &$value, $ImgArray, $localImgArray )
	{
		foreach ( $ImgArray as $key => $var )
		{
			$value = str_replace( $ImgArray[$key], $localImgArray[$key], $value );
		}
	}

	function _publishing( $filename, $content )
	{
		$psn =& $this->beanFactory->getBean( "psn" );
		$patt = "/{PSN:([0-9]+)}([\\S]*)/is";
		if ( !empty( $this->publishInfo['SelfPSN'] ) )
		{
			preg_match( $patt, $this->publishInfo['SelfPSN'], $matches );
		}
		else
		{
			preg_match( $patt, $this->NodeInfo['ContentPSN'], $matches );
		}
		$PSNID = $matches[1];
		$publish_path = $matches[2];
		$psnInfo = $psn->getPSNInfo( $PSNID );
		$psn->connect( $psnInfo['PSN'] );
		$psn->sendVar['IndexID'] = $this->IndexID;
		$psn->sendVar['NodeID'] = $this->publishInfo['NodeID'];
		$psn->sendVar['ContentID'] = $this->publishInfo['ContentID'];
		$filename = $publish_path."/".$filename;
		if ( $psn->put( $filename, $content ) )
		{
			$psn->close( );
			return true;
		}
		else
		{
			$psn->close( );
			return false;
		}
	}

	function getResourceURL( )
	{
		$patt = "/{PSN-URL:([0-9]+)}([\\S]*)/is";
		$ResourceURL = str_replace( "{NodeID}", $this->NodeInfo['NodeID'], $this->NodeInfo['ResourceURL'] );
		if ( preg_match( $patt, $ResourceURL, $matches ) )
		{
			$PSNID = $matches[1];
			$publish_path = $matches[2];
			$psnInfo = psn_admin::getpsninfo( $PSNID );
			$url = $psnInfo['URL'].$publish_path;
		}
		else
		{
			$url = $ResourceURL;
		}
		return $url;
	}

	function convertPSN2URL( $_str )
	{
		$patt = "/{PSN-URL:([0-9]+)}/is";
		if ( preg_match_all( $patt, $_str, $matches ) )
		{
			foreach ( $matches[0] as $key => $var )
			{
				$psnInfo = psn_admin::getpsninfo( $matches[1][$key] );
				$_str = str_replace( $var, $psnInfo['URL'], $_str );
			}
		}
		return $_str;
	}

	function getPSNURL( )
	{
		$patt = "/{PSN-URL:([0-9]+)}([\\S]*)/is";
		$ResourceURL = str_replace( "{NodeID}", $this->NodeInfo['NodeID'], $this->NodeInfo['ResourceURL'] );
		if ( preg_match( $patt, $ResourceURL, $matches ) )
		{
			$PSNID = $matches[1];
			$psnInfo = psn_admin::getpsninfo( $PSNID );
			$url = $psnInfo['URL'];
		}
		else
		{
			$url = $ResourceURL;
		}
		return $url;
	}

	function getHtmlURL( $publishFileName )
	{
		$patt = "/{PSN-URL:([0-9]+)}([\\S]*)/is";
		$publishFileName = formatpublishfile( $publishFileName );
		if ( !empty( $this->publishInfo['SelfPSNURL'] ) )
		{
			if ( preg_match( $patt, $this->publishInfo['SelfPSNURL'], $matches ) )
			{
				$PSNID = $matches[1];
				$publish_path = $matches[2];
				$psnInfo = psn_admin::getpsninfo( $PSNID );
				$url = $psnInfo['URL'].$publish_path."/".$publishFileName;
			}
			else
			{
				$url = $this->publishInfo['SelfURL']."/".$publishFileName;
			}
		}
		else
		{
			$ContentURL = str_replace( "{NodeID}", $this->NodeInfo['NodeID'], $this->NodeInfo['ContentURL'] );
			if ( preg_match( $patt, $ContentURL, $matches ) )
			{
				$PSNID = $matches[1];
				$publish_path = $matches[2];
				$psnInfo = psn_admin::getpsninfo( $PSNID );
				$url = $psnInfo['URL'].$publish_path."/".$publishFileName;
			}
			else
			{
				$url = $ContentURL."/".$publishFileName;
			}
		}
		return $url;
	}

	function ReplaceKeywords( $str )
	{
		global $table;
		global $db;
		global $SYS_ENV;
		if ( !isset( $this->Cache_ReplaceKeywords ) )
		{
			$this->Cache_ReplaceKeywords = array( );
			$sql = "SELECT * FROM {$table->keywords} ";
			$recordSet = $db->Execute( $sql, 2, 10000 );
			while ( !$recordSet->EOF )
			{
				$this->Cache_ReplaceKeywords[] = $recordSet->fields;
				$recordSet->MoveNext( );
			}
			$recordSet->Close( );
		}
		if ( !empty( $this->Cache_ReplaceKeywords ) )
		{
			foreach ( $this->Cache_ReplaceKeywords as $key => $var )
			{
				if ( $var['IsGlobal'] )
				{
					$str = $this->doPregReplace( $var['keyword'], $var['kReplace'], $str );
				}
				else if ( preg_match( "/,".$this->NodeInfo['NodeID']."/isU", $var['NodeScope'] ) )
				{
					$str = $this->doPregReplace( $var['keyword'], $var['kReplace'], $str );
				}
				else
				{
					foreach ( explode( "%", $this->NodeInfo['ParentNodeID'] ) as $varIn )
					{
						if ( preg_match( "/all-".$varIn."/isU", $var['NodeScope'] ) )
						{
							$str = $this->doPregReplace( $var['keyword'], $var['kReplace'], $str );
						}
					}
				}
			}
		}
		return $str;
	}

	function doPregReplace( $_keyword, $_replace, $_str )
	{
		if ( preg_match_all( "/".preg_quote( $_keyword )."/si", $_str, $matches ) )
		{
			if ( strpos( $_str, "<" ) === false && strpos( $_str, ">" ) === false )
			{
				$_str = str_replace( $_keyword, $_replace, $_str );
			}
			else
			{
				if ( preg_match_all( "/(<[^<>]+>[^<>]*)".preg_quote( $_keyword )."([^<>]*<[^<>]+>)/si", $_str, $matches ) )
				{
					foreach ( $matches[0] as $key => $var )
					{
						if ( strpos( strtolower( $matches[1][$key] ), "href" ) !== false )
						{
							$_str = str_replace( $matches[0][$key], "[base64]".base64_encode( $matches[0][$key] )."[/base64]", $_str );
						}
					}
				}
				if ( preg_match_all( "/(<[^<>]+)".preg_quote( $_keyword )."([^<>]+>)/si", $_str, $matches ) )
				{
					foreach ( $matches[0] as $key => $var )
					{
						$_str = str_replace( $matches[0][$key], "[base64]".base64_encode( $matches[0][$key] )."[/base64]", $_str );
					}
				}
				$_str = str_replace( $_keyword, $_replace, $_str );
				if ( preg_match_all( "/\\[base64\\](.*)\\[\\/base64\\]/siU", $_str, $matches ) )
				{
					foreach ( $matches[0] as $key => $var )
					{
						$_str = str_replace( $matches[0][$key], base64_decode( $matches[1][$key] ), $_str );
					}
				}
			}
		}
		return $_str;
	}

	function getRelateDoc( $IndexID, $from, $to )
	{
		global $table;
		global $db;
		global $SYS_ENV;
		global $db_config;
		$table_name = $db_config['table_pre'].$db_config['table_content_pre']."_1";
		$ContentInfo = $this->getContentInfo( $IndexID );
		$from = intval( $from );
		$to = intval( $to );
		$to -= $from;
		$keywords = array_unique( explode( ",", $ContentInfo['KeyWords'] ) );
		if ( !is_array( $keywords ) )
		{
			return false;
		}
		foreach ( $keywords as $var )
		{
			if ( $var == "" )
			{
				continue;
			}
			$sql = "SELECT i.*,c.* FROM {$table->content_index} i ,{$table_name} c WHERE c.Keywords LIKE '%{$var}%' AND i.ContentID = c.ContentID AND i.Type='1' AND i.IndexID!='{$IndexID}' ORDER BY i.PublishDate DESC LIMIT {$from} ,{$to}";
			$relativeLink = $db->Execute( $sql );
			while ( !$relativeLink->EOF )
			{
				if ( !empty( $doclist ) )
				{
					if ( in_array( $relativeLink->fields, $doclist ) )
					{
						$relativeLink->MoveNext( );
						continue;
					}
				}
				$doclist[] = $relativeLink->fields;
			}
		}
		if ( !is_array( $doclist ) )
		{
			return false;
		}
		$resultlist = array_slice( $doclist, $from, $to );
		return $resultlist;
	}

	function makeIndexSavePath( $IndexID )
	{
		$IndexID = strval( $IndexID );
		$add_zero = 8 - strlen( $IndexID );
		$IndexID = str_repeat( "0", $add_zero ).$IndexID;
		$DirSecond = "h".substr( $IndexID, 0, 3 );
		$DirFirst = "h".substr( $IndexID, -5, 2 );
		return $DirSecond."/".$DirFirst;
	}

}

?>
