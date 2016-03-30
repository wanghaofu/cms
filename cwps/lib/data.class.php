<?php


class iData
{

	var $insData = NULL;
	var $checkpass = true;
	var $errinfo = NULL;
	var $db_insert_id = NULL;
	var $db_debug = false;

	function filterData( $IN )
	{
		if ( !is_array( $IN ) )
		{
			return false;
		}
		foreach ( $IN as $key => $var )
		{
			$header = substr( $key, 0, 5 );
			if ( $header == "data_" )
			{
				$field = substr( $key, 5 );
				$this->addData( $field, $var );
			}
		}
	}

	function iData( )
	{
		$this->checkpass = true;
	}

	function getForm( $tmpArray )
	{
		foreach ( $tmpArray as $key => $val )
		{
			$this->insData[$key] = $val;
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
					$this->insData[$key] = $db->escape_string( $this->array2str( $var ) );
				}
				else
				{
					$this->insData[$key] = $db->escape_string( $var );
				}
			}
		}
		else if ( is_array( $val ) )
		{
			$this->insData[$data] = $db->escape_string( $this->array2str( $val ) );
		}
		else
		{
			$this->insData[$data] = $db->escape_string( $val );
		}
	}

	function array2str( $array )
	{
		if ( is_array( $array ) )
		{
			$i = 0;
			foreach ( $array as $key => $var )
			{
				if ( $i === 0 )
				{
					$return = $var;
				}
				else
				{
					$return .= ",".$var;
				}
				++$i;
			}
			return $return;
		}
		else
		{
			return $array;
		}
	}

	function delData( $key )
	{
		unset( $this->insData[$key] );
	}

	function flushData( )
	{
		$this->insData = '';
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
				return true;
			}
			else
			{
				$result = $db->errormsg( );
				$db->errorinfo[] = "<P>数据库错误:数据更新失败。MySQL_ERRNO:".$result[code].".&nbsp;&nbsp;MySQL_ERROR:".$result[message]."</P>";
				$db->report = phphighlite( "{$query}" );
				return false;
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
				return true;
			}
			else
			{
				$result = $db->errormsg( );
				$db->errorinfo[] = "<P>数据库错误:数据更新失败。MySQL_ERRNO:".$result[code].".&nbsp;&nbsp;MySQL_ERROR:".$result[message]."</P>";
				$db->report = phphighlite( "{$query}" );
				return false;
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
			$query = "Delete From ".$table." where ".$which.$method."'".$id."'";
			if ( $db->query( $query ) )
			{
				return true;
			}
			else
			{
				$result = $db->errormsg( );
				$db->errorinfo[] = "<P>数据库错误:数据更新失败。MySQL_ERRNO:".$result[code].".&nbsp;&nbsp;MySQL_ERROR:".$result[message]."</P>";
				$db->report = phphighlite( "{$query}" );
				return false;
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
			return true;
		}
		else
		{
			return false;
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
			$checkpass = False;
		}
	}

	function chkStrIsNull( $chkStr, $strName )
	{
		if ( 0 < !strlen( $chkStr ) )
		{
			$this->errinfo[] = $strName."不能为空.";
			$this->checkpass = False;
		}
	}

}

?>
