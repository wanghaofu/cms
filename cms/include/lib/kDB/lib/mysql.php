<?php


class mysql
{

	var $connection = NULL;
	var $fields = array( );
	var $EOF = 0;
	var $FetchMode = "assoc";
	var $charset = "";
	var $result = NULL;
	var $debug = false;
	var $TotalQueryTime = 0;
	var $TotalQueryNum = 0;
	var $TotalCacheum = 0;
	var $QueryLog = array( );
	var $Cache_ExecuteRecord = array( );
	var $isCached_ExecuteRecord = false;
	var $cache_dir = NULL;
	var $db_version = 0;

	function connect( $params )
	{
		if ( !( $this->connection = mysql_connect( $params['db_host'], $params['db_user'], $params['db_password'] ) ) )
		{
			exit( "<b>kDB Error:</b> Connecting to MySQL failed,please contact to your administrator" );
		}
		if ( !empty( $params['db_name'] ) )
		{
			if ( mysql_select_db( $params['db_name'] ) )
			{
				$this->cache_dir = SYS_PATH."sysdata/cache/";
				$this->setCharset( $params['db_charset'] );
			}
			else
			{
				printf( "<b>kDB Error:</b> Database {$params['db_name']} does not exists, please contact to your administrator" );
			}
		}
	}

	function getDbVersion( )
	{
		return $this->db_version;
	}

	function getServerInfo( )
	{
		return mysql_get_server_info( );
	}

	function setCharset( $charset )
	{
		$this->charset = $charset;
		$serverVersion = mysql_get_server_info( $this->connection );
		$version = explode( ".", $serverVersion );
		if ( $version[0] == 4 && 0 < $version[1] )
		{
			$this->db_version = 41;
		}
		else if ( 4 < $version[0] )
		{
			$this->db_version = 5;
		}
		else
		{
			$this->db_version = 0;
		}
		if ( $version[0] < 4 )
		{
			return;
		}
		if ( $this->charset == "utf-8" || $this->charset == "UTF-8" )
		{
			$this->charset = "utf8";
		}
		$result = mysql_query( "SHOW CHARACTER SET like '".$this->charset."'", $this->connection );
		if ( is_resource( $result ) )
		{
			if ( mysql_num_rows( $result ) <= 0 )
			{
				return;
			}
		}
		else
		{
			return;
		}
		mysql_query( "SET NAMES '".$this->charset."'", $this->connection );
	}

	function db_close( )
	{
		mysql_close( $this->connection );
	}

	function Close( )
	{
		}

	function &query( $query )
	{
		if ( $this->debug )
		{
			$this->startTimer( );
		}
		if ( $this->db_version == 5 )
		{
			$query = preg_replace( "/!=[\\s]*NULL/", " IS NOT NULL", $query );
		}
		$QueryResult = mysql_query( $query, $this->connection );
		if ( $this->debug )
		{
			$time = $this->endTimer( );
			$this->TotalQueryTime = $this->TotalQueryTime + $time;
			$this->QueryLog[] = array(
				"query" => $query,
				"time" => $time
			);
		}
		if ( !$QueryResult )
		{
			$this->halt( "MySQL Query Error", $query );
		}
		$this->TotalQueryNum++;
		return $QueryResult;
	}

	function halt( $message = "", $sql = "" )
	{
		require( KDB_DIR."lib/mysql_error.php" );
	}

	function setDebug( $debug )
	{
		$this->debug = $debug;
	}

	function &Execute( $query, $cache = 0, $cache_time = 600 )
	{
		$recordset = new DBRecordSet( $this );
		$recordset->isCached_ExecuteRecord = false;
		$queryKey = md5( $query );
		if ( $cache == 1 )
		{
			if ( isset( $this->Cache_ExecuteRecord[$queryKey] ) )
			{
				if ( $this->debug )
				{
					$this->startTimer( );
				}
				$recordset->result = $this->Cache_ExecuteRecord[$queryKey];
				$this->TotalCacheNum++;
				if ( $this->debug )
				{
					$time = $this->endTimer( );
					$this->TotalQueryTime = $this->TotalQueryTime + $time;
					$this->QueryLog[] = array(
						"query" => $query,
						"time" => $time,
						"cache" => "Cached-1 "
					);
				}
				$recordset->isCached_ExecuteRecord = true;
			}
			else
			{
				$recordset->result =& $this->query( $query );
			}
		}
		else if ( $cache == 2 )
		{
			$cache_filename = $this->cache_dir.$queryKey.".db.php";
			$cache_file_exists = false;
			if ( file_exists( $cache_filename ) )
			{
				if ( time( ) - filemtime( $cache_filename ) < $cache_time )
				{
					if ( $this->debug )
					{
						$this->startTimer( );
					}
					if ( isset( $this->Cache_ExecuteRecord[$queryKey] ) )
					{
						$recordset->result = $this->Cache_ExecuteRecord[$queryKey];
					}
					else
					{
						include( $cache_filename );
						$recordset->result = $CacheData;
						$this->Cache_ExecuteRecord[$queryKey] = $CacheData;
					}
					$this->TotalCacheNum++;
					if ( $this->debug )
					{
						$time = $this->endTimer( );
						$this->TotalQueryTime = $this->TotalQueryTime + $time;
						$this->QueryLog[] = array(
							"query" => $query,
							"time" => $time,
							"cache" => "Cached-2 "
						);
					}
					$recordset->isCached_ExecuteRecord = true;
					$cache_file_exists = true;
				}
				else
				{
					if ( file_exists( $cache_filename ) )
					{
						unlink( $cache_filename );
					}
					$recordset->result =& $this->query( $query );
				}
			}
			else
			{
				$recordset->result =& $this->query( $query );
			}
		}
		else if ( $cache == 3 )
		{
			if ( isset( $_SESSION['DB_QUERY_CACHE'][$queryKey] ) )
			{
				if ( time( ) < $_SESSION['DB_QUERY_CACHE'][$queryKey]['expire'] )
				{
					if ( $this->debug )
					{
						$this->startTimer( );
					}
					$this->TotalCacheNum++;
					$recordset->result = $_SESSION['DB_QUERY_CACHE'][$queryKey]['data'];
					if ( $this->debug )
					{
						$time = $this->endTimer( );
						$this->TotalQueryTime = $this->TotalQueryTime + $time;
						$this->QueryLog[] = array(
							"query" => $query,
							"time" => $time,
							"cache" => "Cached-3 "
						);
					}
					$recordset->isCached_ExecuteRecord = true;
				}
				else
				{
//					unset( $this->endTimer( )[$queryKey] );
					$recordset->result =& $this->query( $query );
				}
			}
			else
			{
				$recordset->result =& $this->query( $query );
			}
		}
		else
		{
			$recordset->result =& $this->query( $query );
		}
		if ( $recordset->result )
		{
			if ( $recordset->FetchMode == "num" )
			{
				if ( $recordset->fields = $recordset->fetch_array( $recordset->result, MYSQL_NUM ) )
				{
					$recordset->EOF = 0;
				}
				else
				{
					$recordset->EOF = 1;
					$recordset->free_result( $recordset->result );
				}
			}
			else if ( $recordset->FetchMode == "assoc" )
			{
				if ( $recordset->fields = $recordset->fetch_array( $recordset->result, MYSQL_ASSOC ) )
				{
					$recordset->EOF = 0;
				}
				else
				{
					$recordset->EOF = 1;
					$recordset->free_result( $recordset->result );
				}
			}
			else if ( $recordset->fields = $recordset->fetch_array( $recordset->result ) )
			{
				$recordset->EOF = 0;
			}
			else
			{
				$recordset->EOF = 1;
				$recordset->free_result( $recordset->result );
			}
		}
		else
		{
			$recordset->EOF = 1;
			$recordset->free_result( $recordset->result );
		}
		if ( $cache == 1 )
		{
			if ( !isset( $this->Cache_ExecuteRecord[$queryKey] ) )
			{
				while ( !$recordset->EOF )
				{
					$this->Cache_ExecuteRecord[$queryKey][] = $recordset->fields;
					$recordset->MoveNext( );
				}
				$recordset->result = $this->Cache_ExecuteRecord[$queryKey];
				$recordset->isCached_ExecuteRecord = true;
				if ( $recordset->fields = $recordset->fetch_array( $recordset->result ) )
				{
					$recordset->EOF = 0;
				}
				else
				{
					$recordset->EOF = 1;
					$recordset->free_result( $recordset->result );
				}
			}
		}
		else if ( $cache == 2 )
		{
			if ( !$cache_file_exists )
			{
				while ( !$recordset->EOF )
				{
					$CacheData[] = $recordset->fields;
					$recordset->MoveNext( );
				}
				$recordset->result = $CacheData;
				$this->writeCache( $cache_filename, $CacheData );
				$recordset->isCached_ExecuteRecord = true;
				if ( $recordset->fields = $recordset->fetch_array( $recordset->result ) )
				{
					$recordset->EOF = 0;
				}
				else
				{
					$recordset->EOF = 1;
					$recordset->free_result( $recordset->result );
				}
			}
		}
		else if ( $cache == 3 && !isset( $_SESSION['DB_QUERY_CACHE'][$queryKey] ) )
		{
			while ( !$recordset->EOF )
			{
				$_SESSION['DB_QUERY_CACHE'][$queryKey]['data'][] = $recordset->fields;
				$recordset->MoveNext( );
			}
			$recordset->result = $_SESSION['DB_QUERY_CACHE'][$queryKey]['data'];
			$_SESSION['DB_QUERY_CACHE'][$queryKey]['expire'] = time( ) + $cache_time;
			$recordset->isCached_ExecuteRecord = true;
			if ( $recordset->fields = $recordset->fetch_array( $recordset->result ) )
			{
				$recordset->EOF = 0;
			}
			else
			{
				$recordset->EOF = 1;
				$recordset->free_result( $recordset->result );
			}
		}
		return $recordset;
	}

	function &getRow( $query, $cache = 0, $cache_time = 1800 )
	{
		global $_SESSION;
		$queryKey = md5( $query );
		if ( $cache == 1 )
		{
			if ( isset( $this->Cache_ExecuteRecord[$queryKey] ) )
			{
				if ( $this->debug )
				{
					$this->startTimer( );
				}
				$returnRow = $this->Cache_ExecuteRecord[$queryKey];
				$this->TotalCacheNum++;
				if ( $this->debug )
				{
					$time = $this->endTimer( );
					$this->TotalQueryTime = $this->TotalQueryTime + $time;
					$this->QueryLog[] = array(
						"query" => $query,
						"time" => $time,
						"cache" => "Cached-1 "
					);
				}
			}
			else
			{
				$result =& $this->query( $query );
				$returnRow = mysql_fetch_array( $result, MYSQL_ASSOC );
				$this->Cache_ExecuteRecord[$queryKey] = $returnRow;
				$this->free_result( $result );
			}
		}
		else if ( $cache == 2 )
		{
			$cache_filename = $this->cache_dir.$queryKey.".db.php";
			$cache_file_exists = false;
			if ( file_exists( $cache_filename ) )
			{
				if ( time( ) - filemtime( $cache_filename ) < $cache_time )
				{
					if ( $this->debug )
					{
						$this->startTimer( );
					}
					if ( isset( $this->Cache_ExecuteRecord[$queryKey] ) )
					{
						$returnRow = $this->Cache_ExecuteRecord[$queryKey];
					}
					else
					{
						include( $cache_filename );
						$returnRow = $CacheData;
						$this->Cache_ExecuteRecord[$queryKey] = $returnRow;
					}
					$this->TotalCacheNum++;
					if ( $this->debug )
					{
						$time = $this->endTimer( );
						$this->TotalQueryTime = $this->TotalQueryTime + $time;
						$this->QueryLog[] = array(
							"query" => $query,
							"time" => $time,
							"cache" => "Cached-2 "
						);
					}
				}
				else
				{
					if ( file_exists( $cache_filename ) )
					{
						unlink( $cache_filename );
					}
					$result =& $this->query( $query );
					$returnRow = mysql_fetch_array( $result, MYSQL_ASSOC );
					$this->writeCache( $cache_filename, $returnRow );
					$this->free_result( $result );
				}
			}
			else
			{
				$result =& $this->query( $query );
				$returnRow = mysql_fetch_array( $result, MYSQL_ASSOC );
				$this->writeCache( $cache_filename, $returnRow );
				$this->free_result( $result );
			}
		}
		else if ( $cache == 3 )
		{
			if ( isset( $_SESSION['DB_QUERY_CACHE'][$queryKey] ) )
			{
				if ( time( ) < $_SESSION['DB_QUERY_CACHE'][$queryKey]['expire'] )
				{
					if ( $this->debug )
					{
						$this->startTimer( );
					}
					$this->TotalCacheNum++;
					$returnRow = $_SESSION['DB_QUERY_CACHE'][$queryKey]['data'];
					if ( $this->debug )
					{
						$time = $this->endTimer( );
						$this->TotalQueryTime = $this->TotalQueryTime + $time;
						$this->QueryLog[] = array(
							"query" => $query,
							"time" => $time,
							"cache" => "Cached-3 "
						);
					}
				}
				else
				{
//					unset( $this->endTimer( )[$queryKey] );
					$result =& $this->query( $query );
					$returnRow = mysql_fetch_array( $result, MYSQL_ASSOC );
					$_SESSION['DB_QUERY_CACHE'][$queryKey]['data'] = $returnRow;
					$_SESSION['DB_QUERY_CACHE'][$queryKey]['expire'] = time( ) + $cache_time;
					$this->free_result( $result );
				}
			}
			else
			{
				$result =& $this->query( $query );
				$returnRow = mysql_fetch_array( $result, MYSQL_ASSOC );
				$_SESSION['DB_QUERY_CACHE'][$queryKey]['data'] = $returnRow;
				$_SESSION['DB_QUERY_CACHE'][$queryKey]['expire'] = time( ) + $cache_time;
				$this->free_result( $result );
			}
		}
		else
		{
			$result =& $this->query( $query );
			$returnRow = mysql_fetch_array( $result, MYSQL_ASSOC );
			$this->free_result( $result );
		}
		return $returnRow;
	}

	function FieldCount( )
	{
		if ( $this->result )
		{
			return mysql_num_fields( $this->result );
		}
	}

	function FetchRow( )
	{
		return mysql_fetch_array( $this->result, MYSQL_ASSOC );
	}

	function RecordCount( )
	{
		return mysql_num_rows( $this->result );
	}

	function free_result( $result )
	{
		if ( $result )
		{
			mysql_free_result( $result );
		}
	}

	function Insert_ID( )
	{
		return mysql_insert_id( );
	}

	function errormsg( )
	{
		$result['message'] = mysql_error( $this->connection );
		$result['code'] = mysql_errno( $this->connection );
		return $result;
	}

	function error( )
	{
		return mysql_error( );
	}

	function errno( )
	{
		return mysql_errno( );
	}

	function escape_string( $string )
	{
		return mysql_real_escape_string( $string );
	}

	function SetFetchMode( $mode )
	{
		$this->FetchMode = $mode;
	}

	function info( )
	{
		return mysql_get_server_info( );
	}

	function startTimer( )
	{
		global $db_starttime;
		$mtime = microtime( );
		$mtime = explode( " ", $mtime );
		$mtime = $mtime[1] + $mtime[0];
		$db_starttime = $mtime;
	}

	function endTimer( )
	{
		global $db_starttime;
		$mtime = microtime( );
		$mtime = explode( " ", $mtime );
		$mtime = $mtime[1] + $mtime[0];
		$endtime = $mtime;
		$totaltime = round( $endtime - $db_starttime, 5 );
		return $totaltime;
	}

	function writeCache( $cache_filename, $cacheData )
	{
		$CacheFileHeader = "<?php\n//kDB cache file, DO NOT modify me!\n//Created on ";
		$CacheFileFooter = "\n?>";
		$writeData = var_export( $cacheData, true );
		$writeData = "\$CacheData = ".$writeData.";";
		$writeData = $CacheFileHeader.date( "F j, Y, H:i" )."\n\n".$writeData.$CacheFileFooter;
		if ( $fp = fopen( $cache_filename, "w" ) )
		{
			fwrite( $fp, $writeData );
			fclose( $fp );
		}
		else
		{
			exit( "<b>kDB error:</b> Unable to write cache file : <b>".$cache_filename."</b>" );
		}
	}

}

class DBRecordSet extends mysql
{

	function DBRecordSet( &$mysql )
	{
		$this->fields = $mysql->fields;
		$this->FetchMode = $mysql->FetchMode;
		$this->debug = $mysql->debug;
		$this->cache_dir = $mysql->cache_dir;
	}

	function &fetch_array( &$result, $MODE = MYSQL_ASSOC )
	{
		if ( $this->isCached_ExecuteRecord )
		{
			if ( !empty( $result ) )
			{
				$return = array_shift( $result );
				return $return;
			}
			else
			{
				return false;
			}
		}
		else
		{
			if ( $MODE == MYSQL_ASSOC )
			{
				$Query = mysql_fetch_array( $result, MYSQL_ASSOC );
			}
			else if ( $MODE == MYSQL_NUM )
			{
				$Query = mysql_fetch_array( $result, MYSQL_NUM );
			}
			else
			{
				$Query = mysql_fetch_array( $result );
			}
			return $Query;
		}
	}

	function MoveNext( )
	{
		if ( $this->FetchMode == "num" )
		{
			if ( $this->fields = $this->fetch_array( $this->result, MYSQL_NUM ) )
			{
				$this->EOF = 0;
			}
			else
			{
				$this->EOF = 1;
				$this->free_result( $this->result );
			}
		}
		else if ( $this->FetchMode == "assoc" )
		{
			if ( $this->fields = $this->fetch_array( $this->result, MYSQL_ASSOC ) )
			{
				$this->EOF = 0;
			}
			else
			{
				$this->EOF = 1;
				$this->free_result( $this->result );
			}
		}
		else if ( $this->fields = $this->fetch_array( $this->result ) )
		{
			$this->EOF = 0;
		}
		else
		{
			$this->EOF = 1;
			$this->free_result( $this->result );
		}
	}

	function FieldCount( )
	{
		if ( $this->result )
		{
			return mysql_num_fields( $this->result );
		}
		else
		{
			return false;
		}
	}

	function setCharset( $charset )
	{
		$this->charset = $charset;
	}

}

?>
