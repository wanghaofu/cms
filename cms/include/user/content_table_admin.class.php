<?php


class content_table_admin extends iData
{

	function addTable( $TableID = 0 )
	{
		global $table;
		global $db_config;
		global $db;
		if ( $this->dataInsert( $table->content_table ) )
		{
			$table_content_name = $db_config['table_pre'].$db_config['table_extra_pre']."_".$this->db_insert_id;
			$table_contribution_name = $db_config['table_pre'].$db_config['table_contribution_pre']."_".$this->db_insert_id;
			$sql_content = "CREATE TABLE `{$table_content_name}` (\r\n\t\t\t\t\t`ContentID` INT( 10 ) NOT NULL AUTO_INCREMENT,\r\n\t\t\t\t\t`CreationDate` INT( 10 ) NOT NULL,\r\n\t\t\t\t\t`ModifiedDate` INT( 10 ) NOT NULL,\r\n\t\t\t\t\t`CreationUserID` INT( 8 ) NOT NULL,\r\n\t\t\t\t\t`LastModifiedUserID` INT( 8 ) NOT NULL,\r\n\t\t\t\t\t`ContributionUserID` INT( 8 ) NOT NULL,\r\n\t\t\t\t\tPRIMARY KEY (\r\n\t\t\t\t\t`ContentID` \r\n\t\t\t\t\t),\r\n\t\t\t\t\t) TYPE=MyISAM";
			$sql_contribution = "CREATE TABLE `{$table_contribution_name}` (\r\n\t\t\t\t\t`ContentID` INT( 10 ) NOT NULL AUTO_INCREMENT,\r\n\t\t\t\t\t`CreationDate` INT( 10 ) NOT NULL,\r\n\t\t\t\t\t`ModifiedDate` INT( 10 ) NOT NULL,\r\n\t\t\t\t\t`CreationUserID` INT( 8 ) NOT NULL,\r\n\t\t\t\t\t`LastModifiedUserID` INT( 8 ) NOT NULL,\r\n\r\n\t\t\t\t\tPRIMARY KEY (\r\n\t\t\t\t\t`ContentID` \r\n\t\t\t\t\t),\r\n\t\t\t\t\t) TYPE=MyISAM";
			if ( $db->query( $sql_content ) && $db->query( $sql_contribution ) )
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

	function getAllTable( )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->content_table} ";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getTableFieldsInfo( $TableID )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->content_fields} where TableID={$TableID} Order By FieldOrder";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			if ( strpos( $result->fields[FieldDefaultValue], ";" ) )
			{
				$result->fields[selectValue] = explode( ";", $result->fields[FieldDefaultValue] );
			}
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getTableInfo( $TableID )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->content_table} where TableID={$TableID}";
		$data = $db->getRow( $sql );
		return $data;
	}

	function updateTable( $TableID )
	{
		global $table;
		$where = "where TableID=".$TableID;
		if ( $this->dataUpdate( $table->content_table, $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function delTable( $TableID )
	{
		global $table;
		global $db_config;
		global $db;
		$which = "TableID";
		if ( $this->dataDel( $table->content_table, $which, $TableID, $method = "=" ) )
		{
			$db->query( "DELETE FROM {$table->content_fields} WHERE TableID={$TableID}" );
			$table_name = $db_config['table_pre'].$db_config['table_extra_pre']."_".$TableID;
			$sql = "DROP TABLE {$table_name} ";
			if ( $db->query( $sql ) )
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

	function addField( $TableID, $data )
	{
	    
		global $table;
		global $db_config;
		global $db;
		$table_name = $db_config['table_pre'].$db_config['table_extra_pre']."_".$TableID;
		if ( $data[FieldSize] != "" && $data[FieldType] != "text" && $data[FieldType] != "mediumtext" && $data[FieldType] != "longtext" )
		{
			$length = "({$data[FieldSize]})";
		}
		$sql = "ALTER TABLE `{$table_name}` ADD `{$data[FieldName]}` {$data[FieldType]} {$length}  NOT NULL";
		$result = $db->query( $sql );
		if ( $data['index'] != "" && $data[FieldType] != "text" && $data[FieldType] != "mediumtext" && $data[FieldType] != "longtext" )
		{
			$sql = "ALTER TABLE `{$table_name}` ADD INDEX (`{$data[FieldName]}`) ";
			$result = $db->query( $sql );
		}
		if ( $data['fulltext'] != "" )
		{
			$sql = "ALTER TABLE `{$table_name}` ADD FULLTEXT (`{$data[FieldName]}`) ";
			$result = $db->query( $sql );
		}
		if ( $result )
		{
			$this->flushData( );
			$this->addData( "TableID", $TableID );
			$this->addData( "FieldTitle", $data[FieldTitle] );
			$this->addData( "FieldName", $data[FieldName] );
			$this->addData( "FieldType", $data[FieldType] );
			$this->addData( "FieldSize", $data[FieldSize] );
			$this->addData( "FieldDefaultValue", $data[FieldDefaultValue] );
			$this->addData( "FieldInput", $data[FieldInput] );
			$this->addData( "FieldDescription", $data[FieldDescription] );
			if ( $this->_add_field( ) )
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

	function _add_field( )
	{
		global $table;
		if ( $this->dataInsert( $table->content_fields ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function editField( $ContentFieldID, $data )
	{
		global $table;
		global $db_config;
		global $db;
		$fieldInfo = $this->getFieldInfo( $ContentFieldID );
		$table_name = $db_config['table_pre'].$db_config['table_extra_pre']."_".$fieldInfo[TableID];
		if ( $data[FieldSize] != "" && $data[FieldType] != "text" && $data[FieldType] != "mediumtext" && $data[FieldType] != "longtext" )
		{
			$length = "({$data[FieldSize]})";
		}
		$sql = "ALTER TABLE `{$table_name}` CHANGE `{$fieldInfo[FieldName]}` `{$data[FieldName]}` {$data[FieldType]} {$length}  NOT NULL";
		$result = $db->query( $sql );
		if ( $data['index'] != "" && $data[FieldType] != "text" && $data[FieldType] != "mediumtext" && $data[FieldType] != "longtext" )
		{
			$sql = "ALTER TABLE `{$table_name}` ADD INDEX (`{$data[FieldName]}`) ";
			$result = $db->query( $sql );
		}
		if ( $data['fulltext'] != "" )
		{
			$sql = "ALTER TABLE `{$table_name}` ADD FULLTEXT (`{$data[FieldName]}`) ";
			$result = $db->query( $sql );
		}
		if ( $result )
		{
			$this->flushData( );
			$this->addData( "FieldTitle", $data[FieldTitle] );
			$this->addData( "FieldName", $data[FieldName] );
			$this->addData( "FieldType", $data[FieldType] );
			$this->addData( "FieldSize", $data[FieldSize] );
			$this->addData( "FieldDefaultValue", $data[FieldDefaultValue] );
			$this->addData( "FieldInput", $data[FieldInput] );
			$this->addData( "FieldDescription", $data[FieldDescription] );
			if ( $this->_edit_field( $ContentFieldID ) )
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

	function _edit_field( $ContentFieldID )
	{
		global $table;
		$where = "where ContentFieldID=".$ContentFieldID;
		if ( $this->dataUpdate( $table->content_fields, $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function getFieldInfo( $ContentFieldID )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->content_fields} where ContentFieldID={$ContentFieldID}";
		$data = $db->getRow( $sql );
		return $data;
	}

	function delField( $ContentFieldID )
	{
		global $table;
		global $db;
		global $db_config;
		$info = $this->getFieldInfo( $ContentFieldID );
		$table_name = $db_config['table_pre'].$db_config['table_extra_pre']."_".$info[TableID];
		$sql = "ALTER TABLE `{$table_name}` DROP `{$info[FieldName]}`";
		if ( $db->query( $sql ) )
		{
			if ( $this->_del_data( $ContentFieldID ) )
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

	function _del_data( $ContentFieldID )
	{
		global $table;
		$which = "ContentFieldID";
		if ( $this->dataDel( $table->content_fields, $which, $ContentFieldID, $method = "=" ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function OrderField( $Fields )
	{
		foreach ( $Fields as $key => $var )
		{
			$this->flushData( );
			$this->addData( "FieldOrder", $key );
			if ( $this->_edit_field( $var ) )
			{
				$return = true;
			}
			else
			{
				$return = false;
			}
		}
		return $return;
	}

	function EnableFieldListDisplay( $ContentFieldID, $display )
	{
		$this->flushData( );
		$this->addData( "FieldListDisplay", $display );
		if ( $this->_edit_field( $ContentFieldID ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function getDisplayFieldsInfo( $TableID )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->content_fields} where TableID={$TableID} AND FieldListDisplay=1 Order By FieldOrder";
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
