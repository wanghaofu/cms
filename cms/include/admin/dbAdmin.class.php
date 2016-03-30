<?php


class adminDatabase
{

	var $bakFile = NULL;

	function listTablesStatus( )
	{
		global $db;
		global $table;
		global $db_config;
		global $TableStats;
		$sql = "SHOW TABLE STATUS";
		$recordSet = $db->Execute( $sql );
		$pattern = "/^{$db_config['table_pre']}.+/is";
		while ( !$recordSet->EOF )
		{
			if ( preg_match( $pattern, $recordSet->fields[Name] ) )
			{
				$result = $recordSet->fields;
				$length = $result['Data_length'] + $result['Index_length'];
				$TableStats[Rows] = $TableStats[Rows] + $result['Rows'];
				++$TableStats[TableNum];
				$TableStats[Length] = $TableStats[Length] + $length;
				if ( 1048576 < $result['Data_length'] )
				{
					$result['Data_length'] = number_format( $result['Data_length'] / 1048576, 1 )."  MB";
				}
				else
				{
					$result['Data_length'] = number_format( $result['Data_length'] / 1024, 1 )."  KB";
				}
				if ( 1048576 < $result['Index_length'] )
				{
					$result['Index_length'] = number_format( $result['Index_length'] / 1048576, 1 )."  MB";
				}
				else
				{
					$result['Index_length'] = number_format( $result['Index_length'] / 1024, 1 )."  KB";
				}
				if ( 1048576 < $length )
				{
					$length = number_format( $length / 1048576, 1 )."  MB";
				}
				else
				{
					$length = number_format( $length / 1024, 1 )."  KB";
				}
				$tables[] = array(
					"name" => $recordSet->fields[Name],
					"Rows" => $result['Rows'],
					"length" => $length,
					"Data_length" => $result['Data_length'],
					"Index_length" => $result['Index_length']
				);
			}
			$recordSet->MoveNext( );
		}
		if ( 1048576 < $TableStats[Length] )
		{
			$TableStats[Length] = number_format( $TableStats[Length] / 1048576, 1 )."  MB";
		}
		else
		{
			$TableStats[Length] = number_format( $TableStats[Length] / 1024, 1 )."  KB";
		}
		$TableStats[Rows] = number_format( $TableStats[Rows] );
		return $tables;
	}

	function listTables( )
	{
		global $db;
		global $table;
		global $db_config;
		$sql = "SHOW TABLES  FROM {$db_config['db_name']}";
		$db->SetFetchMode( "num" );
		$recordSet = $db->Execute( $sql );
		$pattern = "/^{$db_config['table_pre']}.+/is";
		while ( !$recordSet->EOF )
		{
			if ( preg_match( $pattern, $recordSet->fields[0] ) )
			{
				$tables[] = $recordSet->fields[0];
			}
			$recordSet->MoveNext( );
		}
		return $tables;
	}

	function OptimizeTables( $tablename )
	{
		global $db;
		global $table;
		if ( count( $tablename ) == 0 )
		{
			return false;
		}
		else
		{
			$sql = "OPTIMIZE TABLE ";
			$t = count( $tablename ) - 1;
			foreach ( $tablename as $k => $v )
			{
				if ( $k == $t )
				{
					$sql .= "`{$v}` ";
				}
				else
				{
					$sql .= "`{$v}`, ";
				}
			}
			$db->Execute( $sql );
			return true;
		}
	}

	function backupInit( $tablename, $drop )
	{
		global $table;
		global $db;
		global $_SESSION;
		global $db_config;
		global $SYS_ENV;
		$_SESSION[BackUp_Index] = array( );
		if ( count( $tablename ) == 0 )
		{
			return false;
		}
		else
		{
			$backupFilePrefix = "CMS_backup_".date( "Y_m_d_His" ).rand( 1, 1000 )."_";
			foreach ( $tablename as $k => $v )
			{
				if ( strpos( $v, "admin_sessions" ) )
				{
					continue;
				}
				$result = $db->getRow( "SHOW TABLE STATUS LIKE '{$v}'" );
				$TableSession[] = array(
					"name" => $v,
					"Rows" => $result['Rows'],
					"start" => 0,
					"Avg_row_length" => $result['Avg_row_length']
				);
				$tableDef .= pma_gettabledef( $db_config['db_name'], $v, "\n", $drop )."  \n\n\n";
			}
			$_SESSION[BackUp_FilePrefix] = $backupFilePrefix;
			$_SESSION[BackUp_TableSession] = $TableSession;
			$_SESSION[BackUp_Count] = 1;
			$table_structure_filename = $backupFilePrefix."structure.sql";
			$_SESSION[BackUp_Index][] = $table_structure_filename;
			$fp = @fopen( $SYS_ENV[backupPath]."/".$table_structure_filename, "w" );
			if ( !$fp )
			{
				return false;
			}
			else
			{
				flock( $fp, LOCK_EX );
				fwrite( $fp, $tableDef );
				fclose( $fp );
				return true;
			}
		}
	}

	function bakTables( $tablename, $backPath )
	{
		if ( count( $tablename ) == 0 )
		{
			return false;
		}
		else
		{
			foreach ( $tablename as $k => $v )
			{
				$buffer .= $this->dumptable( $v );
			}
			$newFile = $backPath."/CMS_backup_".date( "Y-m-d-His", time( ) ).".sql";
			$this->bakFile = $newFile;
			$fp = @fopen( $newFile, "w" );
			if ( !$fp )
			{
				return false;
			}
			else
			{
				flock( $fp, LOCK_EX );
				fwrite( $fp, $buffer );
				fclose( $fp );
				return true;
			}
		}
	}

	function dumptable( $table_name, $start = 0, $offset = 0 )
	{
		global $db;
		$tabledump .= "";
		$db->SetFetchMode( "num" );
		if ( $start == 0 && $offset == 0 )
		{
			$rows = $db->Execute( "SELECT * FROM {$table_name}" );
		}
		else
		{
			$rows = $db->Execute( "SELECT * FROM {$table_name} Limit {$start}, {$offset} " );
		}
		if ( !$rows->EOF )
		{
			$numfields = $rows->FieldCount( );
		}
		while ( !$rows->EOF )
		{
			$row = $rows->fields;
			$tabledump .= "INSERT INTO {$table_name} VALUES(";
			$fieldcounter = -1;
			$firstfield = 1;
			while ( ++$fieldcounter < $numfields )
			{
				if ( !isset( $row[$fieldcounter] ) )
				{
					if ( !$firstfield )
					{
						$tabledump .= ",NULL";
					}
					else
					{
						$tabledump .= "NULL";
						$firstfield = 0;
					}
				}
				else if ( !$firstfield )
				{
					$tabledump .= ",'".$db->escape_string( $row[$fieldcounter] )."'";
				}
				else
				{
					$tabledump .= "'".$db->escape_string( $row[$fieldcounter] )."'";
					$firstfield = 0;
				}
			}
			$tabledump .= ");\n";
			$rows->MoveNext( );
		}
		$rows->Close( );
		return $tabledump;
	}

	function getBackupIndex( )
	{
		global $SYS_ENV;
		$dir = dir( $SYS_ENV[backupPath] );
		$dir->rewind( );
		while ( $file = $dir->read( ) )
		{
			if ( $file == "." || $file == ".." )
			{
				}
			else if ( is_dir( $SYS_ENV[backupPath].$file ) )
			{
			}
			else if ( preg_match( "/CMS_backup_(.*).xml/isU", $file ) )
			{
				$temp = explode( ".", $file );
				$list[] = array(
					"filename" => $file,
					"name" => $temp[0]
				);
			}
		}
		$dir->close( );
		return $list;
	}

}

?>
