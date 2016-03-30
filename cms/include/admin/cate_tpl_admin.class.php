<?php


class cate_tpl_admin extends iData
{

	function add( )
	{
		global $table;
		global $sys;
		$time = time( );
		$this->addData( "CreationUserID", $sys->session['sUId'] );
		$this->addData( "LastModifiedUserID", $sys->session['sUId'] );
		$this->addData( "CreationDate", $time );
		$this->addData( "ModifiedDate", $time );
		if ( $this->dataInsert( $table->tpl_data ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function update( $TID )
	{
		global $table;
		global $sys;
		$this->addData( "LastModifiedUserID", $sys->session['sUId'] );
		$this->addData( "ModifiedDate", time( ) );
		$where = "where TID=".$TID;
		if ( $this->dataUpdate( $table->tpl_data, $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function del( $TID )
	{
		global $table;
		$which = "TID";
		if ( $this->dataDel( $table->tpl_data, $which, $TID, $method = "=" ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function move( $TID, $TCID )
	{
		global $table;
		global $sys;
		$this->addData( "LastModifiedUserID", $sys->session['sUId'] );
		$this->addData( "ModifiedDate", time( ) );
		$this->addData( "TCID", $TCID );
		$where = "where TID=".$TID;
		if ( $this->dataUpdate( $table->tpl_data, $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function copyTo( $TID, $TCID )
	{
		global $table;
		global $sys;
		global $SYS_ENV;
		global $psn;
		$TInfo = $this->getInfo( $TID );
		$this->flushData( );
		$this->addData( "TplName", $TInfo['TplName'] );
		$this->addData( "TplType", $TInfo['TplType'] );
		$this->addData( "TCID", $TCID );
		$result = $this->add( );
		$newTID = $this->db_insert_id;
		$srcFile = $SYS_ENV['templatePath']."/ROOT/".$TInfo[TCID]."/".$TInfo[TID].".tpl";
		$destFile = "/ROOT/".$TCID."/".$newTID.".tpl";
		if ( isset( $psn ) )
		{
			require_once( INCLUDE_PATH."admin/psn_admin.class.php" );
			$psn = new psn_admin( );
		}
		$psn->isLog = false;
		$psnInfo[PSN] = "file::".$SYS_ENV['templatePath'];
		$psn->connect( $psnInfo[PSN] );
		$psn->upload( $srcFile, $destFile );
		return $result;
	}

	function getAll( $TCID = 0 )
	{
		global $table;
		global $db;
		$sql = "SELECT t.*, u.uName as LastModifiedUser FROM {$table->tpl_data} t, {$table->user} u where t.TCID='{$TCID}' AND u.uId=t.LastModifiedUserID ";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function getInfo( $TID )
	{
		global $db;
		global $table;
		$sql = "SELECT * FROM {$table->tpl_data} where TID={$TID}";
		return $db->getRow( $sql );
	}

	function getTplContent( $TID )
	{
		global $db;
		global $table;
		$sql = "SELECT tContent FROM {$table->tpl_data} where TID={$TID}";
		$result = $db->getRow( $sql );
		return $result[tContent];
	}

	function getAllbyType( $TCID, $type )
	{
		global $table;
		global $db;
		$sql = "SELECT TID,TplName,TCID FROM {$table->tpl_data} where TCID={$TCID}  AND TplType='{$type}'";
		$result = $db->Execute( $sql );
		while ( !$result->EOF )
		{
			$data[] = $result->fields;
			$result->MoveNext( );
		}
		return $data;
	}

	function addTpl( $TCID, $tpltype, $tplname, $tplcontent )
	{
		global $db;
		global $tplname;
		$this->addData( "TCID", $TCID );
		$this->addData( "TplType", $tpltype );
		$this->addData( "TplName", $tplname );
		$this->addData( "tContent", $tplcontent );
		if ( $TID = $this->tplExist( $TCID, $tplname ) )
		{
			$filename = $this->writeTPL( $TCID, $tplname, $tplcontent );
			$this->addData( "tTime", filemtime( $filename ) );
			return $this->_db_update( $TID );
		}
		else
		{
			$filename = $this->writeTPL( $TCID, $tplname, $tplcontent );
			$this->addData( "tTime", filemtime( $filename ) );
			return $this->_db_add( );
		}
	}

	function writeTPL( &$TCID, &$tplname, &$content )
	{
		$filename = SYS_PATH."templates/".$TCID."@".$tplname;
		if ( writefile( $filename, $content ) )
		{
			return $filename;
		}
		else
		{
			return false;
		}
	}

	function refresh( $TID )
	{
		global $iWPC;
		$tpl = new kTemplate( );
		$tpl->template_dir = SYS_PATH."templates/";
		$tpl->compile_dir = SYS_PATH."sysdata/templates_c/";
		$tInfo = $this->getTpl( $TID );
		$cInfo = $iWPC->loadCateInfo( $tInfo[TCID] );
		$this->cInfo =& $cInfo;
		$tplname = $tInfo[TCID]."@".$tInfo[TplName];
		$output = $tpl->fetch( $tplname );
		if ( $this->_publishing( $tInfo[TplName], $output ) )
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
		if ( $this->dataInsert( $table->tpl_data ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function _db_update( $TID )
	{
		global $table;
		$where = "where TID=".$TID;
		if ( $this->dataUpdate( $table->tpl_data, $where ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function tplExist( $TCID, $tplname )
	{
		global $table;
		global $db;
		$sql = "SELECT TID FROM {$table->tpl_data} WHERE TCID='{$TCID}' AND TplName='{$tplname}'";
		$result = $db->getRow( $sql );
		if ( !empty( $result[TID] ) )
		{
			return $result[TID];
		}
		else
		{
			return false;
		}
	}

}

?>
