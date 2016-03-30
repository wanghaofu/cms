<?php


class tplAdmin extends iData
{

	function getTpl( $tId )
	{
		global $db;
		global $table;
		$sql = "SELECT * FROM {$table->tpl} where tId={$tId}";
		return $db->getRow( $sql );
	}

	function getTplContent( $tId )
	{
		global $db;
		global $table;
		$sql = "SELECT tContent FROM {$table->tpl} where tId={$tId}";
		$result = $db->getRow( $sql );
		return $result[tContent];
	}

	function getAll( $cId = 0 )
	{
		global $table;
		global $db;
		$sql = "SELECT tId,tName,cId FROM {$table->tpl} where cId={$cId}  ";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getAllbyType( $cId, $type )
	{
		global $table;
		global $db;
		$sql = "SELECT tId,tName,cId FROM {$table->tpl} where cId={$cId}  AND tType='{$type}'";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function addTpl( $cId, $tpltype, $tplname, $tplcontent )
	{
		global $db;
		global $tplname;
		$this->addData( "cId", $cId );
		$this->addData( "tType", $tpltype );
		$this->addData( "tName", $tplname );
		$this->addData( "tContent", $tplcontent );
		if ( $tId = $this->tplExist( $cId, $tplname ) )
		{
			$filename = $this->writeTPL( $cId, $tplname, $tplcontent );
			$this->addData( "tTime", filemtime( $filename ) );
			return $this->_db_update( $tId );
		}
		else
		{
			$filename = $this->writeTPL( $cId, $tplname, $tplcontent );
			$this->addData( "tTime", filemtime( $filename ) );
			return $this->_db_add( );
		}
	}

	function writeTPL( &$cId, &$tplname, &$content )
	{
		$filename = SYS_PATH."templates/".$cId."@".$tplname;
		if ( writefile( $filename, $content ) )
		{
			return $filename;
		}
		else
		{
			return false;
		}
	}

	function refresh( $tId )
	{
		global $iWPC;
		$tpl = new kTemplate( );
		$tpl->template_dir = SYS_PATH."templates/";
		$tpl->compile_dir = SYS_PATH."sysdata/templates_c/";
		$tInfo = $this->getTpl( $tId );
		$cInfo = $iWPC->loadCateInfo( $tInfo[cId] );
		$this->cInfo =& $cInfo;
		$tplname = $tInfo[cId]."@".$tInfo[tName];
		$output = $tpl->fetch( $tplname );
		if ( $this->_publishing( $tInfo[tName], $output ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function _publishing( $filename, $content )
	{
		if ( $this->cInfo[publish_type] == "local" )
		{
			$publishpath = $this->cInfo[publish_path];
			iwpc_mkdir( $publishpath );
			if ( writefile( $publishpath."/".$filename, $content ) )
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else if ( $this->cInfo[publish_type] == "remote" )
		{
			if ( function_exists( "ftp_connect" ) )
			{
				$conn_id = @ftp_connect( $this->cInfo['publish_ftp_host'], $this->cInfo['publish_ftp_port'] );
				$login_result = @ftp_login( $conn_id, $this->cInfo['publish_ftp_user'], $this->cInfo['publish_ftp_pass'] );
				if ( !$conn_id || !$login_result )
				{
					echo "<font color=red>FTP connection has failed!</font><br>Attempted to connect to {$this->cInfo['publish_ftp_host']}:{$this->cInfo['publish_ftp_port']} for user {$this->cInfo['publish_ftp_user']}.<br>";
					echo "Please reset you FTP accounts correctly in your iWPC system setting.";
					exit( );
				}
				else
				{
					$publishpath = $this->cInfo[publish_path];
					$this->ftpMakeDir( $publishpath, $conn_id );
					$tmpFile = $this->makeTmpFile( $content );
					$upload = ftp_put( $conn_id, $publishpath."/".$filename, $tmpFile, FTP_ASCII );
					@unlink( $tmpFile );
					@ftp_close( $conn_id );
					if ( $upload )
					{
						return true;
					}
					else
					{
						return false;
					}
				}
			}
			else
			{
				echo "The FTP module can not found,Please contact to you web administrator to install it";
				return false;
			}
		}
	}

	function makeTmpFile( &$content )
	{
		$tmpfilename = CACHE_DIR.Auth::makesessionkey( );
		if ( writefile( $tmpfilename, $content ) )
		{
			return $tmpfilename;
		}
		else
		{
			return false;
		}
	}

	function ftpMakeDir( $directory, &$conn_id )
	{
		$pwd = ftp_pwd( $conn_id );
		if ( @ftp_chdir( $conn_id, $directory ) )
		{
			@ftp_chdir( $conn_id, $pwd );
			return true;
		}
		$dirinfo = pathinfo( $directory );
		if ( !ftp_chdir( $conn_id, $dirinfo['dirname'] ) )
		{
			$pathInfo = explode( "/", $dirinfo['dirname'] );
			$basedir = "";
			foreach ( $pathInfo as $var )
			{
				if ( $var == "" )
				{
					}
				else if ( $this->ftpMakeDir( $dirinfo['dirname'], $conn_id ) )
				{
					echo "Repair {$basedir}{$var} OK<br>";
				}
				else
				{
					echo "Repair {$basedir}{$var} Fail<br>";
				}
			}
		}
		if ( ftp_mkdir( $conn_id, $dirinfo['basename'] ) )
		{
			ftp_site( $conn_id, "CHMOD 777 ".$dirinfo['basename'] );
			return true;
		}
		else
		{
			return false;
		}
	}

	function _db_add( )
	{
		global $table;
		if ( $this->dataInsert( $table->tpl ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function _db_update( $tId )
	{
		global $table;
		$where = "where tId=".$tId;
		if ( $this->dataUpdate( $table->tpl, $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function tplExist( $cId, $tplname )
	{
		global $table;
		global $db;
		$sql = "SELECT tId FROM {$table->tpl} WHERE cId='{$cId}' AND tName='{$tplname}'";
		$result = $db->getRow( $sql );
		if ( !empty( $result[tId] ) )
		{
			return $result[tId];
		}
		else
		{
			return false;
		}
	}

	function del( $tId )
	{
		global $table;
		$which = "tId";
		if ( $this->dataDel( $table->tpl, $which, $tId, $method = "=" ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

}

?>
