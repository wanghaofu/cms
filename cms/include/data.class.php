<?php
class iData
{

	var $insData = NULL;
	var $checkpass = TRUE;
	var $errinfo = NULL;
	var $db_insert_id = NULL;
	var $db_debug = FALSE;

	function iData( )
	{
		$this->checkpass = TRUE;
	}

	function getForm( $tmpArray )
	{
		foreach ( $tmpArray as $key => $val )
		{
			$this->insData[$key] = $val;
		}
	}

	function filterData( $IN )
	{
		if ( !is_array( $IN ) )
		{
			return FALSE;
		}
		foreach ( $IN as $key => $var )
		{
			$header = substr( $key, 0, 5 );
			if ( $header == "data_" )
			{
				$field = substr( $key, 5 );
				if ( is_array( $var ) )
				{
					$tmp_var = "";
					foreach ( $var as $var_key => $value )
					{
						if ( empty( $var_key ) )
						{
							$tmp_var = $value;
						}
						else
						{
							$tmp_var .= ",".$value;
						}
					}
					$var = $tmp_var;
				}
				$this->addData( $field, $var );
			}
			else
			{
				continue;
			}
		}
	}

	function debugData( )
	{
		foreach ( $this->insData as $key => $val )
		{
			echo "{$key} -- {$val} \n<br>";
		}
		exit( );
	}

	function getData( $key = NULL )
	{
		if ( !empty( $key ) )
		{
			return $this->insData[$key];
		}
		else
		{
			return $this->insData;
		}
	}

	function addData( $data, $val = NULL )
	{
		global $db;
		if ( is_array( $data ) )
		{
			foreach ( $data as $key => $var )
			{
				if ( is_array( $var ) )
				{
					continue;
				}
				$this->insData[$key] = $db->escape_string( $var );
			}
		}
		else
		{
			if ( is_array( $val ) )
			{
				continue;
			}
			$this->insData[$data] = $db->escape_string( $val );
		}
	}

	function delData( $key )
	{
//		unset( $Var_0[$key] );
		unset( $this->insData[$key] );
	}

	function flushData( )
	{
			$this->insData = NULL;
	}

	function chgData( $key, $val )
	{
		$this->insData[$key] = $val;
	}

	function dataInsert( $Table )
	{
		
		if ( !$this->checkpass )
		{
			return 0;
		}
		else
		{
			
			global $db;
			$insData_Num = count( $this->insData );
			$Foreach_I = 0;
			$query = "Insert into ".$Table." \n(\n";
			$query_key = "";
			$query_val = "";
	
			foreach ( $this->insData as $key => $val )
			{
				if ( 0 < strlen( $val ) )
				{
					if ( $Foreach_I == 0 )
					{
						$query_key .= "`".$key."`";
						$query_val .= "'".$this->ensql( $val )."'";
					}
					else
					{
						$query_key .= ",\n`".$key."`";
						$query_val .= ",\n'".$this->ensql( $val )."'";
					}
					$Foreach_I += 1;
				}
			}
			$query .= $query_key."\n) \nValues \n(\n".$query_val."\n)";
			if ( $result = $db->query( $query ) )
			{
				$this->db_insert_id = $db->Insert_ID( );
				return TRUE;
			}
			else
			{
				$result = $db->errormsg( );
				$db->errorinfo[] = "<P>数据库错误:数据更新失败。MySQL_ERRNO:".$result[code].".&nbsp;&nbsp;MySQL_ERROR:".$result[message]."</P>";
				$db->report = $query;
				return FALSE;
			}
		}
	}

	function dataUpdate( $table, $where )
	{
		if ( !$this->checkpass )
		{
			return 0;
		}
		else
		{
			global $db;
			$Foreach_I = 0;
			$query = "update ".$table." set ";
			$query_key = "";
			$query_val = "";
			foreach ( $this->insData as $key => $val )
			{
				if ( 0 <= strlen( $val ) )
				{
					if ( $Foreach_I == 0 )
					{
						$query_key = "`".$key."`";
						$query_val = "='".$this->ensql( $val )."'";
						$query .= $query_key.$query_val;
					}
					else
					{
						$query_key = ",`".$key."`";
						$query_val = "='".$this->ensql( $val )."'";
						$query .= $query_key.$query_val;
					}
					$Foreach_I += 1;
				}
			}
			$query .= " {$where}";
			if ( $db->query( $query ) )
			{
				return TRUE;
			}
			else
			{
				$result = $db->errormsg( );
				$db->errorinfo[] = "<P>数据库错误:数据更新失败。MySQL_ERRNO:".$result[code].".&nbsp;&nbsp;MySQL_ERROR:".$result[message]."</P>";
				$db->report = $query;
				return FALSE;
			}
		}
	}

	function dataReplace( $Table )
	{
		global $db;
		if ( !$this->checkpass )
		{
			return 0;
		}
		else
		{
			global $db;
			$insData_Num = count( $this->insData );
			$Foreach_I = 0;
			$query = "Replace into ".$Table." \n(\n";
			$query_key = "";
			$query_val = "";
			foreach ( $this->insData as $key => $val )
			{
				if ( 0 < strlen( $val ) )
				{
					if ( $Foreach_I == 0 )
					{
						$query_key .= "`".$key."`";
						$query_val .= "'".$this->ensql( $val )."'";
					}
					else
					{
						$query_key .= ",\n`".$key."`";
						$query_val .= ",\n'".$this->ensql( $val )."'";
					}
					$Foreach_I += 1;
				}
			}
			$query .= $query_key."\n) \nValues \n(\n".$query_val."\n)";
			if ( $result = $db->query( $query ) )
			{
				$db_insert_id = $db->Insert_ID( );
				return TRUE;
			}
			else
			{
				$result = $db->errormsg( );
				$db->errorinfo[] = "<P>数据库错误:数据更新失败。MySQL_ERRNO:".$result[code].".&nbsp;&nbsp;MySQL_ERROR:".$result[message]."</P>";
				$db->report = $query;
				return FALSE;
			}
		}
	}

	function dataDel( $table, $which, $id, $method = "=" )
	{
		if ( !$this->checkpass )
		{
			return 0;
		}
		else
		{
			global $db;
			$query = "Delete From ".$table." where ".$which.$method.$id;
			if ( $db->query( $query ) )
			{
				return TRUE;
			}
			else
			{
				$result = $db->errormsg( );
				$db->errorinfo[] = "<P>数据库错误:数据更新失败。MySQL_ERRNO:".$result[code].".&nbsp;&nbsp;MySQL_ERROR:".$result[message]."</P>";
				$db->report = $query;
				return FALSE;
			}
		}
	}

	function dataExists( $table, $method, $field, $var )
	{
		global $db;
		$query = "select COUNT(*) as nr From ".$table." where ".$field.$method.$var;
		$result = $db->Execute( $query );
		if ( $result )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function ensql( $string )
	{
		return $string;
	}

	function chkTel( $strPhoneNumber )
	{
		if ( strspn( $strPhoneNumber, "0123456789-" ) )
		{
			$errinfo[] = "Telphone number input error.";
			$checkpass = FALSE;
		}
	}

	function chkStrIsNull( $chkStr, $strName )
	{
		if ( 0 < !strlen( $chkStr ) )
		{
			$this->errinfo[] = $strName."不能为空.";
			$this->checkpass = FALSE;
		}
	}

}

?>
