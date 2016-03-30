<?php
function PMA_backquote( $a_name, $do_it = TRUE )
{
	if ( $do_it && !empty( $a_name ) && $a_name != "*" )
	{
		if ( is_array( $a_name ) )
		{
			$result = array( );
			foreach ( $a_name as $key => $val )
			{
				$result[$key] = "`".$val."`";
			}
			return $result;
		}
		else
		{
			return "`".$a_name."`";
		}
	}
	else
	{
		return $a_name;
	}
}

function PMA_getTableDef( $db_name, $table, $crlf, $drop )
{
	global $db;
	global $use_backquotes;
	$schema_create = "";
	if ( !empty( $drop ) )
	{
		$schema_create .= "DROP TABLE IF EXISTS ".pma_backquote( $table ).";".$crlf;
	}
	if ( 32321 <= PMA_MYSQL_INT_VERSION )
	{
		if ( $use_backquotes )
		{
			mysql_query( "SET SQL_QUOTE_SHOW_CREATE = 1" );
		}
		else
		{
			mysql_query( "SET SQL_QUOTE_SHOW_CREATE = 0" );
		}
		$result = mysql_query( "SHOW CREATE TABLE ".pma_backquote( $db_name ).".".pma_backquote( $table ) );
		if ( $result != FALSE && 0 < mysql_num_rows( $result ) )
		{
			$tmpres = mysql_fetch_array( $result );
			$pos = strpos( $tmpres[1], " (" );
			$pos2 = strpos( $tmpres[1], "(" );
			if ( $pos2 != $pos + 1 )
			{
				$pos = $pos2;
				$tmpres[1] = str_replace( ",", ",\n     ", $tmpres[1] );
			}
			$tmpres[1] = substr( $tmpres[1], 0, 13 ).( $use_backquotes ? pma_backquote( $tmpres[0] ) : $tmpres[0] ).substr( $tmpres[1], $pos );
			$tmpres[1] = $tmpres[1].";\n";
			$schema_create .= str_replace( "\n", $crlf, $tmpres[1] );
		}
		mysql_free_result( $result );
		return $schema_create;
	}
	$schema_create .= "CREATE TABLE ".pma_backquote( $table )." (".$crlf;
	$local_query = "SHOW FIELDS FROM ".pma_backquote( $table )." FROM ".pma_backquote( $db_name );
	$result = mysql_query( $local_query );
	while ( $row = mysql_fetch_array( $result ) )
	{
		$schema_create .= "   ".pma_backquote( $row['Field'], $use_backquotes )." ".$row['Type'];
		if ( isset( $row['Default'] ) && $row['Default'] != "" )
		{
			$schema_create .= " DEFAULT '".mysql_real_escape_string( $row['Default'] )."'";
		}
		if ( $row['Null'] != "YES" )
		{
			$schema_create .= " NOT NULL";
		}
		if ( $row['Extra'] != "" )
		{
			$schema_create .= " ".$row['Extra'];
		}
		$schema_create .= ",".$crlf;
	}
	mysql_free_result( $result );
	$schema_create = ereg_replace( ",".$crlf."\$", "", $schema_create );
	$local_query = "SHOW KEYS FROM ".pma_backquote( $table )." FROM ".pma_backquote( $db_name );
	$result = mysql_query( $local_query );
	while ( $row = mysql_fetch_array( $result ) )
	{
		$kname = $row['Key_name'];
		$comment = isset( $row['Comment'] ) ? $row['Comment'] : "";
		$sub_part = isset( $row['Sub_part'] ) ? $row['Sub_part'] : "";
		if ( $kname != "PRIMARY" && $row['Non_unique'] == 0 )
		{
			$kname = "UNIQUE|{$kname}";
		}
		if ( $comment == "FULLTEXT" )
		{
			$kname = "FULLTEXT|\$kname";
		}
		if ( !isset( $index[$kname] ) )
		{
			$index[$kname] = array( );
		}
		if ( 1 < $sub_part )
		{
			$index[$kname][] = pma_backquote( $row['Column_name'], $use_backquotes )."(".$sub_part.")";
		}
		else
		{
			$index[$kname][] = ppma_backquote( $row['Column_name'], $use_backquotes );
		}
	}
	mysql_free_result( $result );
	while ( list( $x, $columns ) = each( $index ) )
	{
		$schema_create .= ",".$crlf;
		if ( $x == "PRIMARY" )
		{
			$schema_create .= "   PRIMARY KEY (";
		}
		else if ( substr( $x, 0, 6 ) == "UNIQUE" )
		{
			$schema_create .= "   UNIQUE ".substr( $x, 7 )." (";
		}
		else if ( substr( $x, 0, 8 ) == "FULLTEXT" )
		{
			$schema_create .= "   FULLTEXT ".substr( $x, 9 )." (";
		}
		else
		{
			$schema_create .= "   KEY ".$x." (";
		}
		$schema_create .= implode( $columns, ", " ).")";
	}
	$schema_create .= $crlf.")";
	return $schema_create;
}

require_once( "common.php" );
if ( !defined( "PMA_MYSQL_INT_VERSION" ) )
{
	$result = mysql_query( "SELECT VERSION() AS version" );
	if ( $result != FALSE && 0 < @mysql_num_rows( $result ) )
	{
		$row = mysql_fetch_row( $result );
		$match = explode( ".", $row[0] );
		mysql_free_result( $result );
	}
	if ( !isset( $row ) )
	{
		define( "PMA_MYSQL_INT_VERSION", 32332 );
		define( "PMA_MYSQL_STR_VERSION", "3.23.32" );
	}
	else
	{
		define( "PMA_MYSQL_INT_VERSION", ( integer )sprintf( "%d%02d%02d", $match[0], $match[1], intval( $match[2] ) ) );
		define( "PMA_MYSQL_STR_VERSION", $row[0] );
		unset( $result );
		unset( $row );
		unset( $match );
	}
}
if ( !$sys->isAdmin( ) )
{
	goback( "access_deny_module_db" );
}
require_once( INCLUDE_PATH."admin/dbAdmin.class.php" );
$adminDB = new adminDatabase( );
switch ( $IN[o] )
{
case "backup" :
	if ( !empty( $IN['running'] ) )
	{
		$FileData = "";
		$FileSize = 0;
		if ( !empty( $_SESSION['BackUp_TableSession'] ) )
		{
			$flag = TRUE;
			while ( $flag )
			{
				$MaxFileSize = empty( $_SESSION['BackUp_MaxFileSize'] ) ? 1048576 : $_SESSION['BackUp_MaxFileSize'];
				$CurrentTable = array_shift( $_SESSION['BackUp_TableSession'] );
				if ( $FileSize + $CurrentTable['Rows'] * $CurrentTable['Avg_row_length'] <= $MaxFileSize )
				{
					$FileData .= $adminDB->dumptable( $CurrentTable['name'], $CurrentTable['start'], $CurrentTable['Rows'] );
					$FileSize += $CurrentTable['Rows'] * $CurrentTable['Avg_row_length'];
					if ( empty( $_SESSION['BackUp_TableSession'] ) )
					{
						$flag = FALSE;
					}
				}
				else
				{
					$ToGetRows = ceil( ( $MaxFileSize - $FileSize ) / $CurrentTable['Avg_row_length'] );
					$FileSize += $ToGetRows * $CurrentTable['Avg_row_length'];
					$FileData .= $adminDB->dumptable( $CurrentTable['name'], $CurrentTable['start'], $ToGetRows );
					$CurrentTable['Rows'] = $CurrentTable['Rows'] - $ToGetRows;
					$CurrentTable['start'] = $CurrentTable['start'] + $ToGetRows;
					array_unshift( $_SESSION['BackUp_TableSession'], $CurrentTable );
					$flag = FALSE;
				}
			}
			$backupFileName = $_SESSION[BackUp_FilePrefix].$_SESSION[BackUp_Count].".sql";
			++$_SESSION[BackUp_Count];
			$_SESSION[BackUp_Index][] = $backupFileName;
			$fp = @fopen( $SYS_ENV[backupPath]."/".$backupFileName, "w" );
			if ( $fp )
			{
				flock( $fp, LOCK_EX );
				fwrite( $fp, $FileData );
				fclose( $fp );
			}
			showmsg( sprintf( $_LANG_ADMIN['db_backup_running'], $backupFileName ), $base_url."o=backup&running=1" );
		}
		else
		{
			$IndexData = "<Backup>\n";
			foreach ( $_SESSION[BackUp_Index] as $var )
			{
				$IndexData .= "<File>{$var}</File>\n";
			}
			$IndexData .= "</Backup>";
			$IndexFileName = $_SESSION[BackUp_FilePrefix]."index.xml";
			$fp = @fopen( $SYS_ENV[backupPath]."/".$IndexFileName, "w" );
			if ( $fp )
			{
				flock( $fp, LOCK_EX );
				fwrite( $fp, $IndexData );
				fclose( $fp );
			}
			showmsg( $_LANG_ADMIN['db_backup_finished'], $base_url );
		}
	}
	else
	{
		$_SESSION['BackUp_MaxFileSize'] = $IN['MaxFileSize'] * 1024 * 1024 * 0.6;
		if ( $adminDB->backupInit( $IN[tablename], $IN['addDrop'] ) )
		{
			showmsg( $_LANG_ADMIN['db_backup_init_ok'], $base_url."o=backup&running=1" );
		}
		else
		{
			showmsg( $_LANG_ADMIN['db_backup_init_fail'] );
		}
	}
	break;
case "optimize" :
	if ( $adminDB->OptimizeTables( $IN[tablename] ) )
	{
		showmsg( $_LANG_ADMIN['db_optimize_ok'] );
	}
	else
	{
		showmsg( $_LANG_ADMIN['db_optimize_fail'] );
	}
	break;
case "restore" :
	$TPL->assign( "backIndexs", $adminDB->getBackupIndex( ) );
	$TPL->display( "admin_db_restore.html" );
	break;
case "restore_submit" :
	if ( $IN['running'] == 1 )
	{
		if ( !empty( $_SESSION['RestoreBackupFiles'] ) )
		{
			$backFile = array_shift( $_SESSION['RestoreBackupFiles'] );
			$sql = getfile( $SYS_ENV[backupPath]."/".$backFile );
			plugin_runquery( $sql );
			showmsg( sprintf( $_LANG_ADMIN['db_restore_running'], $backFile ), $base_url."o=restore_submit&running=1" );
		}
		else
		{
			showmsg( $_LANG_ADMIN['db_restore_finished'], $base_url );
		}
	}
	else
	{
		$IndexContent = getfile( $SYS_ENV[backupPath]."/".$IN['RestoreIndex'] );
		preg_match_all( "/<File>(.*)<\\/File>/isU", $IndexContent, $matches );
		$_SESSION['RestoreBackupFiles'] = $matches[1];
		showmsg( $_LANG_ADMIN['db_restore_init'], $base_url."o=restore_submit&running=1" );
	}
	break;
case "query" :
	$TPL->display( "admin_db_query.html" );
	break;
	break;
case "query_submit" :
	if ( !empty( $IN[sql] ) )
	{
		if ( $result = plugin_runquery( $IN[sql] ) )
		{
			if ( preg_match( "/select/isU", $IN[sql] ) )
			{
				while ( $row = mysql_fetch_array( $result, MYSQL_ASSOC ) )
				{
					$theresult[] = $row;
				}
				$TPL->assign( "result", $theresult );
			}
			$TPL->assign( "query_result", $_LANG_ADMIN['db_query_ok'] );
		}
		else
		{
			$TPL->assign( "query_result", $_LANG_ADMIN['db_query_fail'] );
		}
	}
	else
	{
		$TPL->assign( "query_result", $_LANG_ADMIN['db_query_null'] );
	}
	$TPL->display( "admin_db_query.html" );
	break;
case "fieldReplace" :
	require_once( INCLUDE_PATH."admin/content_table_admin.class.php" );
	$TableID = empty( $IN['TableID'] ) ? 1 : $IN['TableID'];
	$TPL->assign( "tableInfo", content_table_admin::getalltable( ) );
	$TPL->assign( "fieldsInfo", content_table_admin::gettablefieldsinfo( $TableID ) );
	$TPL->assign( "TableID", $TableID );
	$TPL->assign( "NODE_LIST", $NODE_LIST );
	$TPL->display( "admin_fieldReplace.html" );
	break;
case "fieldReplace_submit" :
	$table_name = $db_config['table_pre'].$db_config['table_content_pre']."_".$IN['TableID'];
	foreach ( $IN['Fields'] as $var )
	{
		if ( !empty( $IN['NodeIDs'] ) )
		{
			foreach ( $IN['NodeIDs'] as $key => $varIn )
			{
				if ( empty( $key ) )
				{
					$ids = $varIn;
				}
				else
				{
					$ids .= ",".$varIn;
				}
			}
			if ( empty( $ids ) )
			{
				$sql = "UPDATE {$table_name} SET `{$var}`= replace( {$var} , '".$IN['Keyword']."', '".$IN['Replace']."')";
			}
			else
			{
				$sql = "UPDATE {$table_name} c , {$table->content_index} i SET c.{$var}= replace( c.{$var} , '".$IN['Keyword']."', '".$IN['Replace']."') WHERE c.ContentID=i.ContentID and i.NodeID IN({$ids})";
			}
		}
		else
		{
			$sql = "UPDATE {$table_name} SET `{$var}`= replace( {$var} , '".$IN['Keyword']."', '".$IN['Replace']."')";
		}
		$result = $db->query( $sql );
	}
	if ( $result )
	{
		showmessage( "field_replace_ok", $referer );
	}
	else
	{
		showmessage( "field_replace_fail", $referer );
	}
	break;
default :
	$TPL->assign( "tablelists", $adminDB->listTablesStatus( ) );
	$TPL->assign( "tablestats", $TableStats );
	$TPL->display( "admin_db.html" );
	break;
}
include( MODULES_DIR."footer.php" );
?>
