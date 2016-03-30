<?php


class LogAdmin extends iData
{

	function addLoginLog( $uName, $IP, $state = false )
	{
		global $table;
		if ( $state == true )
		{
			$logdata = "{$IP} - - [".date( "Y-m-d H:i:s" )."] - - {$uName} - - OK";
		}
		else
		{
			$logdata = "{$IP} - - [".date( "Y-m-d H:i:s" )."] - - {$uName} - - Fail";
		}
		$this->addFileLog( "login", $logdata );
		if ( $state == true )
		{
			$this->addData( "uName", $uName );
			$this->addData( "IP", $IP );
			$this->addData( "Time", time( ) );
			if ( $this->dataInsert( $table->log_login ) )
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	function addAdminLog( $uName, $IP, &$IN )
	{
		global $table;
		include( INCLUDE_PATH."admin/log.ini.php" );
		$requestPathInfo = pathinfo( $_SERVER['PHP_SELF'] );
		if ( array_key_exists( $requestPathInfo['basename'], $LOG_MAP ) )
		{
			if ( in_array( $IN['o'], $LOG_MAP[$requestPathInfo['basename']] ) )
			{
				$logdata = "{$IP} - - [".date( "Y-m-d H:i:s" )."] - - {$uName} - -  ".$requestPathInfo['basename']."::".$IN['o']." - - ".preg_replace( "/(sId=[0-9a-z]*&)/is", "", $_SERVER['REQUEST_URI'] );
				$this->addFileLog( "admin", $logdata );
				$this->addData( "uName", $uName );
				$this->addData( "IP", $IP );
				$this->addData( "Action", $requestPathInfo['basename']."::".$IN['o'] );
				$this->addData( "ActionURL", preg_replace( "/(sId=[0-9a-z]*&)/is", "", $_SERVER['REQUEST_URI'] ) );
				$this->addData( "Time", time( ) );
				if ( $this->dataInsert( $table->log_admin ) )
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
		else
		{
			return true;
		}
	}

	function addFileLog( $type, $data )
	{
		switch ( $type )
		{
		case "login" :
			$filename = "login.".date( "Ymd" ).".log.php";
			break;
		case "admin" :
			$filename = "admin.".date( "Ymd" ).".log.php";
			break;
		}
		if ( is_writable( SYS_PATH."sysdata/logs" ) )
		{
			$filename = SYS_PATH."sysdata/logs/".$filename;
		}
		else
		{
			$filename = SYS_PATH."sysdata/".$filename;
		}
		if ( !file_exists( $filename ) )
		{
			if ( $handle = fopen( $filename, "a" ) )
			{
				fwrite( $handle, "<?php exit('Access Denied!'); ?>\n" );
				fclose( $handle );
			}
		}
		if ( $handle = fopen( $filename, "a" ) )
		{
			fwrite( $handle, $data."\n" );
			fclose( $handle );
		}
	}

	function delLoginLog( $LogID )
	{
		global $table;
		$which = "LogID";
		if ( $this->dataDel( $table->log_login, $which, $LogID, $method = "=" ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function delAdminLog( $LogID )
	{
		global $table;
		$which = "LogID";
		if ( $this->dataDel( $table->log_admin, $which, $LogID, $method = "=" ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function delLoginLogByTime( $start_time, $end_time )
	{
		global $table;
		global $db;
		$result = $db->query( "DELETE FROM {$table->log_login} where Time > {$start_time} AND Time < {$end_time} " );
		if ( $result )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function delAdminLogByTime( $start_time, $end_time )
	{
		global $table;
		global $db;
		$result = $db->query( "DELETE FROM {$table->log_admin} where Time > {$start_time} AND Time < {$end_time} " );
		if ( $result )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function getLoginLogRecordNum( )
	{
		global $table;
		global $db;
		$result = $db->getRow( "SELECT COUNT(*) as TotalNum FROM {$table->log_login} " );
		return $result['TotalNum'];
	}

	function getAdminLogRecordNum( )
	{
		global $table;
		global $db;
		$result = $db->getRow( "SELECT COUNT(*) as TotalNum FROM {$table->log_admin} " );
		return $result['TotalNum'];
	}

	function getLoginLogLimit( $start, $offset )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->log_login} ORDER BY LogID DESC LIMIT {$start}, {$offset} ";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getAdminLogLimit( $start, $offset )
	{
		global $table;
		global $db;
		$sql = "SELECT * FROM {$table->log_admin} ORDER BY LogID DESC LIMIT {$start}, {$offset} ";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function searchLoginLogRecordNum( $params )
	{
		global $table;
		global $db;
		switch ( $params['field'] )
		{
		case "uName" :
			$sql_count = "SELECT COUNT(*) as TotalNum FROM {$table->log_login} where uName='".$params['value']."' ";
			break;
		case "IP" :
			$sql_count = "SELECT COUNT(*) as TotalNum FROM {$table->log_login} where IP='".$params['value']."' ";
			break;
		case "Time" :
			$sql_count = "SELECT COUNT(*) as TotalNum FROM {$table->log_login} where Time > ".$params['start_time']." AND Time < ".$params['end_time'];
			break;
		}
		$numResult = $db->getRow( $sql_count );
		return $numResult[TotalNum];
	}

	function searchLoginLogLimit( $params )
	{
		global $table;
		global $db;
		switch ( $params['field'] )
		{
		case "uName" :
			$sql = "SELECT * FROM {$table->log_login} where uName='".$params['value']."' ORDER BY LogID DESC LIMIT ".$params['start']." , ".$params['offset'];
			break;
		case "IP" :
			$sql = "SELECT * FROM {$table->log_login} where IP='".$params['value']."' ORDER BY LogID DESC LIMIT     ".$params['start']." , ".$params['offset'];
			break;
		case "Time" :
			$sql = "SELECT * FROM {$table->log_login} where Time > ".$params['start_time']." AND Time < ".$params['end_time']." ORDER BY LogID DESC LIMIT  ".$params['start']." , ".$params['offset'];
			break;
		}
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function searchAdminLogRecordNum( $params )
	{
		global $table;
		global $db;
		switch ( $params['field'] )
		{
		case "uName" :
			$sql_count = "SELECT COUNT(*) as TotalNum FROM {$table->log_admin} where uName='".$params['value']."' ";
			break;
		case "IP" :
			$sql_count = "SELECT COUNT(*) as TotalNum FROM {$table->log_admin} where IP='".$params['value']."' ";
			break;
		case "Action" :
			$sql_count = "SELECT COUNT(*) as TotalNum FROM {$table->log_admin} where Action='".$params['value']."' ";
			break;
		case "Time" :
			$sql_count = "SELECT COUNT(*) as TotalNum FROM {$table->log_admin} where Time > ".$params['start_time']." AND Time < ".$params['end_time'];
			break;
		}
		$numResult = $db->getRow( $sql_count );
		return $numResult[TotalNum];
	}

	function searchAdminLogLimit( $params )
	{
		global $table;
		global $db;
		switch ( $params['field'] )
		{
		case "uName" :
			$sql = "SELECT * FROM {$table->log_admin} where uName='".$params['value']."' ORDER BY LogID DESC LIMIT    ".$params['start']." , ".$params['offset'];
			break;
		case "IP" :
			$sql = "SELECT * FROM {$table->log_admin} where IP='".$params['value']."' ORDER BY LogID DESC LIMIT  ".$params['start']." , ".$params['offset'];
			break;
		case "Action" :
			$sql = "SELECT * FROM {$table->log_admin} where Action='".$params['value']."' ORDER BY LogID DESC LIMIT  ".$params['start']." , ".$params['offset'];
			break;
		case "Time" :
			$sql = "SELECT * FROM {$table->log_admin} where Time > ".$params['start_time']." AND Time < ".$params['end_time']." ORDER BY LogID DESC LIMIT  ".$params['start']." , ".$params['offset'];
			break;
		}
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
