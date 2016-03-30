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
			$TableID = $this->db_insert_id;
			$table_content_name = $db_config['table_pre'].$db_config['table_content_pre']."_".$this->db_insert_id;
			$table_contribution_name = $db_config['table_pre'].$db_config['table_contribution_pre']."_".$this->db_insert_id;
			$table_collection_name = $db_config['table_pre'].$db_config['table_collection_pre']."_".$this->db_insert_id;
			$table_publish_name = $db_config['table_pre'].$db_config['table_publish_pre']."_".$this->db_insert_id;
			$mysql_version = explode( ".", $db->getServerInfo( ) );
			de($mysql_version);
			if ( $mysql_version[0] == 4 && 0 < $mysql_version[1] || 4 < $mysql_version[0] )
			{
				$set_chaset .= " DEFAULT CHARSET=".$db_config['db_charset']." ";
			}
			else
			{
				$set_chaset = "";
			}
			$sql_content = "CREATE TABLE `{$table_content_name}` (\r\n\t\t\t\t\t`ContentID` INT( 10 ) NOT NULL AUTO_INCREMENT,\r\n\t\t\t\t\t`CreationDate` INT( 10 )  default '0',\r\n\t\t\t\t\t`ModifiedDate` INT( 10 )  default '0',\r\n\t\t\t\t\t`CreationUserID` INT( 8 )  default '0',\r\n\t\t\t\t\t`LastModifiedUserID` INT( 8 )  default '0',\r\n\t\t\t\t\t`ContributionUserID` INT( 8 )  default '0',\r\n\t\t\t\t\t`ContributionID` INT( 10 ) default '0',\r\n\t\t\t\t\tPRIMARY KEY (`ContentID` )\r\n\t\t\t\t) ENGINE=MyISAM".$set_chaset;
			$sql_publish = "CREATE TABLE `{$table_publish_name}` (\r\n\t\t\t\t\tIndexID Integer(10) NOT NULL ,\r\n\t\t\t\t\tContentID Integer(10) default '0' ,\r\n\t\t\t\t\tNodeID Integer(10) default '0' ,\r\n\t\t\t\t\tPublishDate Integer(10) ,\r\n\t\t\t\t\tURL Char(250) ,\r\n\t\t\t\t\tPrimary Key (IndexID) ,\r\n\t\t\t\t\tKEY NodeID (NodeID),\r\n\t\t\t\t\tKEY ContentID (ContentID) ,\r\n\t\t\t\t\tKEY PublishDate (PublishDate)\r\n\t\t\t\t) ENGINE=MyISAM".$set_chaset;
			$sql_contribution = "CREATE TABLE `{$table_contribution_name}` (\r\n\t\t\t\t  `ContributionID` int(10) NOT NULL auto_increment,\r\n\t\t\t\t  `CateID` int(8) NOT NULL default '0',\r\n\t\t\t\t  `CreationDate` int(10) default '0',\r\n\t\t\t\t  `ModifiedDate` int(10) default '0',\r\n\t\t\t\t  `ApprovedDate` int(10) default '0',\r\n\t\t\t\t  `OwnerID` int(8) default '0',\r\n\t\t\t\t  `State` int(5) default '0',\r\n\t\t\t\t  `NodeID` int(8)  default '0',\r\n\t\t\t\t  `SubNodeID` varchar(250)  default '',\r\n\t\t\t\t  `IndexNodeID` varchar(250)  default '',\r\n\t\t\t\t  `ContributionDate` int(10) default '0',\r\n\t\t\t\t  PRIMARY KEY  (`ContributionID`,`CateID`),\r\n\t\t\t\t  UNIQUE KEY `ContributionID` (`ContributionID`),\r\n\t\t\t\t  KEY `NodeID` (`NodeID`)\r\n\t\t\t\t) ENGINE=MyISAM".$set_chaset;
			$sql_collection = "CREATE TABLE `{$table_collection_name}` (\r\n\t\t\t\t  `CollectionID` int(10) NOT NULL auto_increment,\r\n\t\t\t\t  `CateID` int(8)  default '0',\r\n\t\t\t\t  `CreationDate` int(10) default '0',\r\n\t\t\t\t  `ModifiedDate` int(10) default '0',\r\n\t\t\t\t  `ApprovedDate` int(10) default '0',\r\n\t\t\t\t  `PublishDate` int(10) default '0',\r\n\t\t\t\t  `State` int(2) default '0',\r\n\t\t\t\t  `NodeID` int(8)  default '0',\r\n\t\t\t\t  `SubNodeID` varchar(250)  default '',\r\n\t\t\t\t  `Src` varchar(250)  default '',\r\n\t\t\t\t  `IsImported` tinyint(1)  default '0',\r\n\t\t\t\t  PRIMARY KEY  (`CollectionID`,`CateID`),\r\n\t\t\t\t  UNIQUE KEY `CollectionID` (`CollectionID`),\r\n\t\t\t\t  KEY `C_I` (`CateID`,`IsImported`),\r\n\t\t\t\t  KEY `Src` (`Src`)\r\n\t\t\t\t) ENGINE=MyISAM".$set_chaset;
			
			
			$res= $db->query( $sql_content );
			
			if ( $res && $db->query( $sql_publish ) && $db->query( $sql_contribution ) && $db->query( $sql_collection ) )
			{
				$pluginFactory =& get_singleton( "CMS.Plugin" );
				$plugin =& $pluginFactory->getInstance( );
				$plugin->addTable( $TableID );
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

	public static function getAllTable( )
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

	function isValid( $add = 0 )
	{
		global $table;
		global $db;
		global $CONTENT_MODEL_INFO;
		require( SYS_PATH."/license.php" );
		$license_array = $License;
		unset( $License );
		$result['nr'] = count( $CONTENT_MODEL_INFO );
		if ( $license_array['ContentModel-num'] < $result['nr'] + $add && $license_array['ContentModel-num'] != 0 )
		{
			goback( "license_ContentModel_num_overflow" );
		}
	}

	public static function getTableFieldsInfo( $TableID )
	{
		global $table;
		global $db;
		global $CONTENT_MODEL_INFO;
		if ( !empty( $CONTENT_MODEL_INFO ) )
		{
			return $CONTENT_MODEL_INFO[$TableID]['Model'];
		}
		$sql = "SELECT * FROM {$table->content_fields} where TableID={$TableID} Order By FieldOrder";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			if ( strpos( $result->fields['FieldDefaultValue'], ";" ) )
			{
				$result->fields['selectValue'] = explode( ";", $result->fields['FieldDefaultValue'] );
			}
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getTableInfo( $TableID, $field = "*" )
	{
		global $table;
		global $db;
		$sql = "SELECT {$field} FROM {$table->content_table} where TableID={$TableID}";
		$data = $db->getRow( $sql );
		if ( $field == "*" )
		{
			return $data;
		}
		else
		{
			return $data[$field];
		}
	}

	function updateTable( $TableID )
	{
		global $table;
		global $db;
		$where = "where TableID=".$TableID;
		if ( $this->dataUpdate( $table->content_table, $where ) )
		{
			$sql = "UPDATE {$table->content_fields} SET EnableContribution=0,EnableCollection=0,EnablePublish=0,FieldSearchable=0,FieldListDisplay=0 WHERE TableID={$TableID}";
			$db->query( $sql );
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
			$table_name_1 = $db_config['table_pre'].$db_config['table_content_pre']."_".$TableID;
			$table_name_2 = $db_config['table_pre'].$db_config['table_contribution_pre']."_".$TableID;
			$table_name_3 = $db_config['table_pre'].$db_config['table_collection_pre']."_".$TableID;
			$table_name_4 = $db_config['table_pre'].$db_config['table_publish_pre']."_".$TableID;
			$sql_1 = "DROP TABLE {$table_name_1} ";
			$sql_2 = "DROP TABLE {$table_name_2} ";
			$sql_3 = "DROP TABLE {$table_name_3} ";
			$sql_4 = "DROP TABLE {$table_name_4} ";
			if ( $db->query( $sql_1 ) && $db->query( $sql_2 ) && $db->query( $sql_3 ) && $db->query( $sql_4 ) )
			{
				$pluginFactory =& get_singleton( "CMS.Plugin" );
				$plugin =& $pluginFactory->getInstance( );
				$plugin->delTable( $TableID );
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

	function addField( $TableID, $data, $_multiField = false )
	{
		global $table;
		global $db_config;
		global $db;
		$table_name_1 = $db_config['table_pre'].$db_config['table_content_pre']."_".$TableID;
		$table_name_2 = $db_config['table_pre'].$db_config['table_contribution_pre']."_".$TableID;
		$table_name_3 = $db_config['table_pre'].$db_config['table_collection_pre']."_".$TableID;
		$table_name_4 = $db_config['table_pre'].$db_config['table_publish_pre']."_".$TableID;
		if ( $_multiField == true )
		{
			$first_stamp = true;
			$sql_1 = "";
			$sql_2 = "";
			$sql_3 = "";
			$sql_4 = "";
			foreach ( $data as $key => $var )
			{
				$length = "";
				if ( $var['FieldSize'] != "" && $var['FieldType'] != "text" && $var['FieldType'] != "mediumtext" && $var['FieldType'] != "longtext" && $var['FieldType'] != "contentlink" )
				{
					$length = "({$var['FieldSize']})";
				}
				if ( $first_stamp == true )
				{
					if ( $var['FieldType'] == "contentlink" )
					{
						$sql_1 .= "ALTER TABLE `{$table_name_1}` ADD COLUMN `{$var['FieldName']}` text default NULL";
						$sql_2 .= "ALTER TABLE `{$table_name_2}` ADD COLUMN `{$var['FieldName']}` text default NULL";
						$sql_3 .= "ALTER TABLE `{$table_name_3}` ADD COLUMN `{$var['FieldName']}` text default NULL";
						$sql_4 .= "ALTER TABLE `{$table_name_4}` ADD COLUMN `{$var['FieldName']}` text default NULL";
					}
					else
					{
						$sql_1 .= "ALTER TABLE `{$table_name_1}` ADD `{$var['FieldName']}` {$var['FieldType']} {$length}  default NULL";
						$sql_2 .= "ALTER TABLE `{$table_name_2}` ADD `{$var['FieldName']}` {$var['FieldType']} {$length}  default NULL";
						$sql_3 .= "ALTER TABLE `{$table_name_3}` ADD `{$var['FieldName']}` {$var['FieldType']} {$length}  default NULL";
						$sql_4 .= "ALTER TABLE `{$table_name_4}` ADD `{$var['FieldName']}` {$var['FieldType']} {$length}  default NULL";
					}
					$first_stamp = false;
				}
				else if ( $var['FieldType'] == "contentlink" )
				{
					$sql_1 .= ", ADD COLUMN `{$var['FieldName']}` text default NULL";
					$sql_2 .= ", ADD COLUMN `{$var['FieldName']}` text default NULL";
					$sql_3 .= ", ADD COLUMN `{$var['FieldName']}` text default NULL";
					$sql_4 .= ", ADD COLUMN `{$var['FieldName']}` text default NULL";
				}
				else
				{
					$sql_1 .= ", ADD `{$var['FieldName']}` {$var['FieldType']} {$length}  default NULL";
					$sql_2 .= ", ADD `{$var['FieldName']}` {$var['FieldType']} {$length}  default NULL";
					$sql_3 .= ", ADD `{$var['FieldName']}` {$var['FieldType']} {$length}  default NULL";
					$sql_4 .= ", ADD `{$var['FieldName']}` {$var['FieldType']} {$length}  default NULL";
				}
			}
			$result = $db->query( $sql_1 );
			$result = $db->query( $sql_2 );
			$result = $db->query( $sql_3 );
			$result = $db->query( $sql_4 );
			if ( $result )
			{
				$pluginFactory =& get_singleton( "CMS.Plugin" );
				$Plugin =& $pluginFactory->getInstance( );
				foreach ( $data as $key => $var )
				{
					$Plugin->addField( $TableID, $var );
					$this->flushData( );
					$this->addData( "TableID", $TableID );
					$this->addData( "FieldTitle", $var['FieldTitle'] );
					$this->addData( "FieldName", $var['FieldName'] );
					$this->addData( "FieldType", $var['FieldType'] );
					$this->addData( "FieldSize", $var['FieldSize'] );
					$this->addData( "FieldDefaultValue", $var['FieldDefaultValue'] );
					$this->addData( "FieldInput", $var['FieldInput'] );
					$this->addData( "FieldDescription", $var['FieldDescription'] );
					$this->addData( "FieldInputFilter", $var['FieldInputFilter'] );
					$this->addData( "FieldInputPicker", $var['FieldInputPicker'] );
					$this->addData( "FieldInputTpl", $var['FieldInputTpl'] );
					$this->addData( "FieldOrder", $var['FieldOrder'] );
					$this->addData( "FieldListDisplay", $var['FieldListDisplay'] );
					$this->addData( "IsMainField", $var['IsMainField'] );
					$this->addData( "IsTitleField", $var['IsTitleField'] );
					$this->addData( "FieldSearchable", $var['FieldSearchable'] );
					$this->addData( "EnableContribution", $var['EnableContribution'] );
					$this->addData( "EnableCollection", $var['EnableCollection'] );
					$this->addData( "EnablePublish", $var['EnablePublish'] );
					$return = $this->_add_field( );
				}
				return $return;
			}
			else
			{
				return false;
			}
		}
		else
		{
		   
		    if ( $data['FieldSize'] != "" && $data['FieldType'] != "text" && $data['FieldType'] != "mediumtext" && $data['FieldType'] != "longtext" && $data['FieldType'] != "contentlink" )
			{
				$length = "({$data['FieldSize']})";
			}else{
			    $length= "(250)";   //add default length;
			}
			
			
			if ( $data['FieldType'] == "contentlink" )
			{
				$sql_1 = "ALTER TABLE `{$table_name_1}` ADD COLUMN `{$data['FieldName']}` text default NULL";
				$sql_2 = "ALTER TABLE `{$table_name_2}` ADD COLUMN `{$data['FieldName']}` text default NULL";
				$sql_3 = "ALTER TABLE `{$table_name_3}` ADD COLUMN `{$data['FieldName']}` text default NULL";
				$sql_4 = "ALTER TABLE `{$table_name_4}` ADD COLUMN `{$data['FieldName']}` text default NULL";
			}
			else
			{
				$sql_1 = "ALTER TABLE `{$table_name_1}` ADD `{$data['FieldName']}` {$data['FieldType']} {$length}  default NULL";
				$sql_2 = "ALTER TABLE `{$table_name_2}` ADD `{$data['FieldName']}` {$data['FieldType']} {$length}  default NULL";
				$sql_3 = "ALTER TABLE `{$table_name_3}` ADD `{$data['FieldName']}` {$data['FieldType']} {$length}  default NULL";
				$sql_4 = "ALTER TABLE `{$table_name_4}` ADD `{$data['FieldName']}` {$data['FieldType']} {$length}  default NULL";
			}
			$result = $db->query( $sql_1 );
			de($sql_1);
			de($db->errormsg());
			$result = $db->query( $sql_2 );
			$result = $db->query( $sql_3 );
			$result = $db->query( $sql_4 );
			if ( $result )
			{
				$pluginFactory =& get_singleton( "CMS.Plugin" );
				$Plugin =& $pluginFactory->getInstance( );
				$Plugin->addField( $TableID, $data );
				$this->flushData( );
				$this->addData( "TableID", $TableID );
				$this->addData( "FieldTitle", $data['FieldTitle'] );
				$this->addData( "FieldName", $data['FieldName'] );
				$this->addData( "FieldType", $data['FieldType'] );
				$this->addData( "FieldSize", $data['FieldSize'] );
				$this->addData( "FieldDefaultValue", $data['FieldDefaultValue'] );
				$this->addData( "FieldInput", $data['FieldInput'] );
				$this->addData( "FieldDescription", $data['FieldDescription'] );
				$this->addData( "FieldInputFilter", $data['FieldInputFilter'] );
				$this->addData( "FieldInputPicker", $data['FieldInputPicker'] );
				$this->addData( "FieldInputTpl", $data['FieldInputTpl'] );
				$this->addData( "FieldOrder", $data['FieldOrder'] );
				$this->addData( "FieldListDisplay", $data['FieldListDisplay'] );
				$this->addData( "IsMainField", $data['IsMainField'] );
				$this->addData( "IsTitleField", $data['IsTitleField'] );
				$this->addData( "FieldSearchable", $data['FieldSearchable'] );
				$this->addData( "EnableContribution", $data['EnableContribution'] );
				$this->addData( "EnableCollection", $data['EnableCollection'] );
				$this->addData( "EnablePublish", $data['EnablePublish'] );
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
		$table_name_1 = $db_config['table_pre'].$db_config['table_content_pre']."_".$fieldInfo['TableID'];
		$table_name_2 = $db_config['table_pre'].$db_config['table_contribution_pre']."_".$fieldInfo['TableID'];
		$table_name_3 = $db_config['table_pre'].$db_config['table_collection_pre']."_".$fieldInfo['TableID'];
		$table_name_4 = $db_config['table_pre'].$db_config['table_publish_pre']."_".$fieldInfo['TableID'];
		if ( $data['FieldSize'] != "" && $data['FieldType'] != "text" && $data['FieldType'] != "mediumtext" && $data['FieldType'] != "longtext" && $data['FieldType'] != "contentlink" )
		{
			$length = "({$data['FieldSize']})";
		}
		if ( $data['FieldType'] == "contentlink" )
		{
			$sql_1 = "ALTER TABLE `{$table_name_1}` CHANGE `{$fieldInfo['FieldName']}` `{$data['FieldName']}` text default NULL";
			$sql_2 = "ALTER TABLE `{$table_name_2}` CHANGE `{$fieldInfo['FieldName']}` `{$data['FieldName']}` text default NULL";
			$sql_3 = "ALTER TABLE `{$table_name_3}` CHANGE `{$fieldInfo['FieldName']}` `{$data['FieldName']}` text default NULL";
			$sql_4 = "ALTER TABLE `{$table_name_4}` CHANGE `{$fieldInfo['FieldName']}` `{$data['FieldName']}` text default NULL";
		}
		else
		{
			$sql_1 = "ALTER TABLE `{$table_name_1}` CHANGE `{$fieldInfo['FieldName']}` `{$data['FieldName']}` {$data['FieldType']} {$length}  default NULL";
			$sql_2 = "ALTER TABLE `{$table_name_2}` CHANGE `{$fieldInfo['FieldName']}` `{$data['FieldName']}` {$data['FieldType']} {$length}  default NULL";
			$sql_3 = "ALTER TABLE `{$table_name_3}` CHANGE `{$fieldInfo['FieldName']}` `{$data['FieldName']}` {$data['FieldType']} {$length}  default NULL";
			$sql_4 = "ALTER TABLE `{$table_name_4}` CHANGE `{$fieldInfo['FieldName']}` `{$data['FieldName']}` {$data['FieldType']} {$length}  default NULL";
		}
		$result = $db->query( $sql_1 );
		$result = $db->query( $sql_2 );
		$result = $db->query( $sql_3 );
		$result = $db->query( $sql_4 );
		if ( $result )
		{
			$pluginFactory =& get_singleton( "CMS.Plugin" );
			$Plugin =& $pluginFactory->getInstance( );
			$Plugin->editField( $fieldInfo['TableID'], $fieldInfo, $data );
			$this->flushData( );
			$this->addData( "FieldTitle", $data['FieldTitle'] );
			$this->addData( "FieldName", $data['FieldName'] );
			$this->addData( "FieldType", $data['FieldType'] );
			$this->addData( "FieldSize", $data['FieldSize'] );
			$this->addData( "FieldDefaultValue", $data['FieldDefaultValue'] );
			$this->addData( "FieldInput", $data['FieldInput'] );
			$this->addData( "FieldDescription", $data['FieldDescription'] );
			$this->addData( "FieldInputFilter", $data['FieldInputFilter'] );
			$this->addData( "FieldInputPicker", $data['FieldInputPicker'] );
			$this->addData( "FieldInputTpl", $data['FieldInputTpl'] );
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

	function indexField( $ContentFieldID )
	{
		global $table;
		global $db_config;
		global $db;
		$fieldInfo = $this->getFieldInfo( $ContentFieldID );
		$table_name_1 = $db_config['table_pre'].$db_config['table_content_pre']."_".$fieldInfo['TableID'];
		$table_name_2 = $db_config['table_pre'].$db_config['table_contribution_pre']."_".$fieldInfo['TableID'];
		$table_name_3 = $db_config['table_pre'].$db_config['table_collection_pre']."_".$fieldInfo['TableID'];
		$table_name_4 = $db_config['table_pre'].$db_config['table_publish_pre']."_".$fieldInfo['TableID'];
		if ( $fieldInfo['FieldType'] == "text" || $fieldInfo['FieldType'] == "mediumtext" || $fieldInfo['FieldType'] == "longtext" )
		{
			$sql_1 = "ALTER TABLE `{$table_name_1}` ADD FULLTEXT (`{$fieldInfo['FieldName']}`) ";
			$sql_2 = "ALTER TABLE `{$table_name_2}` ADD FULLTEXT (`{$fieldInfo['FieldName']}`) ";
			$sql_3 = "ALTER TABLE `{$table_name_3}` ADD FULLTEXT (`{$fieldInfo['FieldName']}`) ";
			$sql_4 = "ALTER TABLE `{$table_name_4}` ADD FULLTEXT (`{$fieldInfo['FieldName']}`) ";
			$result = $db->query( $sql_1 );
			$result = $db->query( $sql_2 );
			$result = $db->query( $sql_3 );
			$result = $db->query( $sql_4 );
		}
		else
		{
			$sql_1 = "ALTER TABLE `{$table_name_1}` ADD INDEX (`{$fieldInfo['FieldName']}`) ";
			$sql_2 = "ALTER TABLE `{$table_name_2}` ADD INDEX (`{$fieldInfo['FieldName']}`) ";
			$sql_3 = "ALTER TABLE `{$table_name_3}` ADD INDEX (`{$fieldInfo['FieldName']}`) ";
			$sql_4 = "ALTER TABLE `{$table_name_4}` ADD INDEX (`{$fieldInfo['FieldName']}`) ";
			$result = $db->query( $sql_1 );
			$result = $db->query( $sql_2 );
			$result = $db->query( $sql_3 );
			$result = $db->query( $sql_4 );
		}
		if ( $result )
		{
			$this->flushData( );
			$this->addData( "IsIndex", 1 );
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

	function delFieldIndex( $ContentFieldID )
	{
		global $table;
		global $db_config;
		global $db;
		$fieldInfo = $this->getFieldInfo( $ContentFieldID );
		$table_name_1 = $db_config['table_pre'].$db_config['table_content_pre']."_".$fieldInfo['TableID'];
		$table_name_2 = $db_config['table_pre'].$db_config['table_contribution_pre']."_".$fieldInfo['TableID'];
		$table_name_3 = $db_config['table_pre'].$db_config['table_collection_pre']."_".$fieldInfo['TableID'];
		$table_name_4 = $db_config['table_pre'].$db_config['table_publish_pre']."_".$fieldInfo['TableID'];
		$sql_1 = "ALTER TABLE `{$table_name_1}` DROP INDEX `{$fieldInfo['FieldName']}` ";
		$sql_2 = "ALTER TABLE `{$table_name_2}` DROP INDEX `{$fieldInfo['FieldName']}` ";
		$sql_3 = "ALTER TABLE `{$table_name_3}` DROP INDEX `{$fieldInfo['FieldName']}` ";
		$sql_4 = "ALTER TABLE `{$table_name_4}` DROP INDEX `{$fieldInfo['FieldName']}` ";
		$result = $db->query( $sql_1 );
		$result = $db->query( $sql_2 );
		$result = $db->query( $sql_3 );
		$result = $db->query( $sql_4 );
		if ( $result )
		{
			$deploy->close( );
			$this->flushData( );
			$this->addData( "IsIndex", 0 );
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
		$table_name_1 = $db_config['table_pre'].$db_config['table_content_pre']."_".$info['TableID'];
		$table_name_2 = $db_config['table_pre'].$db_config['table_contribution_pre']."_".$info['TableID'];
		$table_name_3 = $db_config['table_pre'].$db_config['table_collection_pre']."_".$info['TableID'];
		$table_name_4 = $db_config['table_pre'].$db_config['table_publish_pre']."_".$info['TableID'];
		$sql_1 = "ALTER TABLE `{$table_name_1}` DROP `{$info['FieldName']}`";
		$sql_2 = "ALTER TABLE `{$table_name_2}` DROP `{$info['FieldName']}`";
		$sql_3 = "ALTER TABLE `{$table_name_3}` DROP `{$info['FieldName']}`";
		$sql_4 = "ALTER TABLE `{$table_name_4}` DROP `{$info['FieldName']}`";
		if ( $db->query( $sql_1 ) && $db->query( $sql_2 ) && $db->query( $sql_3 ) && $db->query( $sql_4 ) )
		{
			$pluginFactory =& get_singleton( "CMS.Plugin" );
			$Plugin =& $pluginFactory->getInstance( );
			$Plugin->delField( $info['TableID'], $info, $data );
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

	function EnableFieldSearch( $ContentFieldID, $searchable )
	{
		$this->flushData( );
		$this->addData( "FieldSearchable", $searchable );
		if ( $this->_edit_field( $ContentFieldID ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function EnableContribution( $ContentFieldID, $searchable )
	{
		$this->flushData( );
		$this->addData( "EnableContribution", $searchable );
		if ( $this->_edit_field( $ContentFieldID ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function EnableCollection( $ContentFieldID, $searchable )
	{
		$this->flushData( );
		$this->addData( "EnableCollection", $searchable );
		if ( $this->_edit_field( $ContentFieldID ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function EnablePublish( $ContentFieldID, $searchable )
	{
		$this->flushData( );
		$this->addData( "EnablePublish", $searchable );
		if ( $this->_edit_field( $ContentFieldID ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function setAsMainField( $ContentFieldID, $TableID )
	{
		global $table;
		global $db;
		$this->flushData( );
		$this->addData( "IsMainField", 0 );
		$where = "where TableID=".$TableID;
		$this->dataUpdate( $table->content_fields, $where );
		$this->flushData( );
		$this->addData( "IsMainField", 1 );
		if ( $this->_edit_field( $ContentFieldID ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function setAsTitleField( $ContentFieldID, $TableID )
	{
		global $table;
		global $db;
		$this->flushData( );
		$this->addData( "IsTitleField", 0 );
		$where = "where TableID=".$TableID;
		$this->dataUpdate( $table->content_fields, $where );
		$this->flushData( );
		$this->addData( "IsTitleField", 1 );
		if ( $this->_edit_field( $ContentFieldID ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function getDisplayFieldsInfo( $TableID )
	{
		global $table;
		global $db;
		global $CONTENT_MODEL_INFO;
		if ( empty( $TableID ) )
		{
			return false;
		}
		if ( !empty( $CONTENT_MODEL_INFO ) )
		{
			foreach ( $CONTENT_MODEL_INFO[$TableID]['Model'] as $var )
			{
				if ( $var['FieldListDisplay'] == 1 )
				{
					$data[] = $var;
				}
			}
		}
		else
		{
			$sql = "SELECT * FROM {$table->content_fields} where TableID={$TableID} AND FieldListDisplay=1 Order By FieldOrder";
			$result = $db->Execute( $sql );
			while ( !$result->EOF )
			{
				$data[] = $result->fields;
				$result->MoveNext( );
			}
		}
		return $data;
	}

	public static function getSearchFieldsInfo( $TableID )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->content_fields} where TableID={$TableID} AND FieldSearchable=1 Order By FieldOrder";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	public static function getTitleFieldInfo( $TableID )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->content_fields} where TableID={$TableID} AND IsTitleField=1 ";
		$result = $db->getRow( $sql );
		return $result;
	}

}

?>
