<?php


class FTP
{

	var $relateDir = NULL;
	var $file_list = array( );
	var $file_view_ext = array( );
	var $viewMode = NULL;
	var $viewMode_cate_type = NULL;
	var $NodeID = NULL;
	var $limit = array( );
	var $savePath = NULL;

	function FTP( $params )
	{
		if ( isset( $params['relateDir'] ) )
		{
			$this->relateDir = $params['relateDir'];
		}
		if ( isset( $params['NodeID'] ) )
		{
			$this->NodeID = $params['NodeID'];
		}
		if ( isset( $params['mode'] ) )
		{
			$this->viewMode = $params['mode'];
		}
		if ( isset( $params['savePath'] ) )
		{
			$this->savePath = $params['savePath'];
		}
		if ( isset( $params['mode_cate_type'] ) )
		{
			$this->viewMode_cate_type = $params['mode_cate_type'];
		}
		if ( isset( $params['file_view_ext'] ) )
		{
			$this->file_view_ext = explode( "|", $params['file_view_ext'] );
		}
	}

	function setLimit( $start, $limit )
	{
		$this->limit = array(
			$start,
			$limit
		);
	}

	function fileNameFilter( )
	{
		}

	function recordNum( )
	{
		global $db;
		global $table;
		$sql = "SELECT COUNT(*) as nr FROM {$table->resource} WHERE  Category='".$this->viewMode_cate_type."' and NodeID=".$this->NodeID;
		$row = $db->query( $sql );
		$this->recordNum = $row[nr];
		return $this->recordNum;
	}

	function listFile( )
	{
		global $db;
		global $table;
		if ( $this->viewMode == "cate" )
		{
			if ( $this->viewMode_cate_type == "img" )
			{
				$sql = "SELECT * FROM {$table->resource}   WHERE Category='img' and NodeID=".$this->NodeID."  ORDER BY `ResourceID` DESC  LIMIT ".$this->limit[0].",".$this->limit[1];
				$result = $db->Execute( $sql );
				while ( !$result->EOF )
				{
					$url = $this->savePath."/".$result->fields['Path'];
					$type = explode( ".", $result->fields['Path'] );
					$this->file_list[] = array(
						"id" => $result->fields['ResourceID'],
						"filename" => $result->fields['Name'],
						"uptime" => $result->fields['CreationDate'],
						"url" => $url,
						"type" => array_pop( $type ),
						"dimension" => explode( "*", $result->fields['Info'] ),
						"size" => ceil( $result->fields['Size'] / 1024 )
					);
					$result->MoveNext( );
				}
				return $this->file_list;
			}
		}
		else
		{
		}
	}

	function delFile( )
	{
		}

	function makeDir( )
	{
		}

	function delDir( )
	{
		}

	function renameDir( )
	{
		}

}

class Upload extends iData
{

	var $sysInfo = NULL;
	var $runError = false;
	var $uploadFileArray = NULL;
	var $POST_File = NULL;
	var $upload_num = NULL;
	var $saveFile = NULL;
	var $saveMiniFile = NULL;
	var $NodeID = NULL;
	var $uploadType = NULL;
	var $makeMini = NULL;
	var $miniHeight = NULL;
	var $miniWidth = NULL;
	var $changeName = NULL;
	var $rootPath = NULL;
	var $imgAllowSuffix = "gif|jpg|jpeg|png";
	var $imgAllowSize = 640000;

	function POST_Data_handler( )
	{
		if ( $this->uploadType == "img" )
		{
			$allowSuffix = $this->imgAllowSuffix;
			$allowSize = $this->imgAllowSize;
		}
		else if ( $this->uploadType == "attach" )
		{
			$allowSuffix = "";
			$allowSize = "";
		}
		else if ( $this->uploadType == "flash" )
		{
			$allowSuffix = "swf";
			$allowSize = $SYS_ENV[upFlashSize];
		}
		foreach ( $this->POST_File['name'] as $key => $var )
		{
			if ( $this->test_filetype( $var, $allowSuffix ) && $this->test_filesize( $this->POST_File['size'][$key], $allowSize ) )
			{
				$this->uploadFileArray[] = array(
					"name" => $var,
					"tmp_name" => $this->POST_File['tmp_name'][$key],
					"size" => $this->POST_File['size'][$key]
				);
			}
			else
			{
				@unlink( $this->POST_File['tmp_name'][$key] );
			}
		}
	}

	function Upload( $params )
	{
		global $db;
		global $table;
		global $SYS_ENV;
		if ( isset( $params['POST_File'] ) )
		{
			$this->POST_File = $params['POST_File'];
		}
		if ( isset( $params['uploadType'] ) )
		{
			$this->uploadType = $params['uploadType'];
		}
		if ( isset( $params['NodeID'] ) )
		{
			$this->NodeID = $params['NodeID'];
		}
		if ( isset( $params['rootPath'] ) )
		{
			$this->rootPath = $params['rootPath']."/";
		}
		if ( isset( $params['makeMini'] ) )
		{
			$this->makeMini = $params['makeMini'];
		}
		if ( isset( $params['miniWidth'] ) )
		{
			$this->miniWidth = $params['miniWidth'];
		}
		if ( isset( $params['miniHeight'] ) )
		{
			$this->miniHeight = $params['miniHeight'];
		}
		if ( isset( $params['changeName'] ) )
		{
			$this->changeName = $params['changeName'];
		}
		$sql = "SELECT varValue as num FROM {$table->sys} WHERE  varName ='ResourceNum'";
		$row = $db->getRow( $sql );
		$this->upload_num = $row[num];
		$this->imgAllowSuffix = $SYS_ENV[upImgType];
		$this->imgAllowSize = $SYS_ENV[upImgSize];
		$this->POST_Data_handler( );
		$this->_Upload( );
	}

	function _Upload( )
	{
		global $db;
		if ( !is_array( $this->uploadFileArray ) )
		{
			return false;
		}
		foreach ( $this->uploadFileArray as $key => $var )
		{
			$dataPath = $this->makeAutoPath( );
			$targetPath = $this->uploadType."/".$dataPath."/";
			if ( cmsware_mkdir( $this->rootPath.$targetPath, 511 ) )
			{
				if ( $this->changeName == "1" )
				{
					$arr = explode( ".", $var['name'] );
					$rename = $this->uploadType.date( "YmdHis", time( ) ).$key.".".array_pop( $arr );
				}
				else
				{
					$rename = $var['name'];
				}
				$destination = $this->rootPath.$targetPath.$rename;
				if ( move_uploaded_file( $var['tmp_name'], $destination ) )
				{
					$this->sysInfo[] = $var['name']." Upload OK,Rename As ".$rename;
					if ( $this->uploadType == "img" )
					{
						$img_size = Image::getimgsize( $destination );
						$info = $img_size['width']."*".$img_size['height'];
					}
					$this->flushData( );
					$this->addData( "Category", $this->uploadType );
					$this->addData( "Type", 1 );
					$this->addData( "NodeID", $this->NodeID );
					$this->addData( "Name", $rename );
					$this->addData( "Path", $targetPath.$rename );
					$this->addData( "Size", $var['size'] );
					$this->addData( "Info", $info );
					$this->insertDBLog( );
					$this->saveFile[] = $destination;
					if ( $this->makeMini == 1 )
					{
						$miniName = str_replace( ".", "_s.", $rename );
						if ( Image::makeminiature( $this->rootPath.$targetPath.$rename, $this->rootPath.$targetPath.$miniName, $this->miniWidth, $this->miniHeight ) )
						{
							$this->flushData( );
							$this->addData( "Category", $this->uploadType );
							$this->addData( "ParentID", $this->db_insert_id );
							$this->addData( "Type", 1 );
							$this->addData( "NodeID", $this->NodeID );
							$this->addData( "Name", $miniName );
							$this->addData( "Path", $targetPath.$miniName );
							$this->addData( "Size", $var['size'] );
							$this->addData( "Info", $this->miniWidth."*".$this->miniHeight );
							$this->insertDBLog( );
							$this->sysInfo[] = " MAKE SMALL IMAGE ".$miniName." OK \\n";
						}
						$this->saveMiniFile[] = $this->rootPath.$targetPath.$miniName;
					}
				}
				else
				{
					$this->sysInfo[] = $var['name']." upload fail";
				}
			}
			else
			{
				$this->sysInfo[] = "upload fail, dir can not be created";
			}
		}
		$this->Counter( );
	}

	function Counter( $num = 1 )
	{
		global $db;
		global $table;
		$sql = "UPDATE {$table->sys} SET `varValue`=varValue +1  where varName='ResourceNum'";
		$row = $db->query( $sql );
	}

	function insertDBLog( )
	{
		global $db;
		global $table;
		$time = time( );
		$this->addData( "CreationDate", $time );
		$this->addData( "ModifiedDate", $time );
		if ( $this->dataInsert( $table->resource ) )
		{
			return true;
		}
		else
		{
			new Error( "Failure: insertDBLog" );
			return false;
		}
	}

	function makeAutoPath( )
	{
		$num = $this->upload_num;
		$num = ceil( $num / 500 );
		if ( $num < 10 )
		{
			$strCId = "000".strval( $num );
		}
		else if ( $num < 100 )
		{
			$strCId = "00".strval( $num );
		}
		else if ( $num < 1000 )
		{
			$strCId = "0".strval( $num );
		}
		else
		{
			$strCId = strval( $num );
		}
		$thousandDirName = "h".substr( $strCId, 0, strlen( $strCId ) - 2 );
		$hundredDirName = "h".substr( $strCId, -2, 2 );
		$Path = $thousandDirName."/".$hundredDirName;
		return $Path;
	}

	function test_filetype( $upfilename_name, $allow_upimgtype )
	{
		global $SYS_ENV;
		$file_ext = $this->get_file_ext( $upfilename_name );
		if ( $upfilename_name != "none" && $upfilename_name != "" )
		{
			$typelist = explode( "|", $allow_upimgtype );
			$typecount = count( $typelist );
			$typeok = false;
			$i = 0;
			for ( ;	$i <= $typecount - 1;	++$i	)
			{
				if ( strlen( strpos( $file_ext, $typelist[$i] ) ) != 0 )
				{
					return true;
				}
			}
			if ( $typeok == false )
			{
				$this->sysInfo[] = "出错！".$upfilename_name."文件类型".$file_ext."不匹配，系统只支持以下格式:".$allow_upimgtype;
				$this->runError = true;
				return false;
			}
		}
		else
		{
			$this->runError = true;
			return false;
		}
	}

	function test_filesize( $upfilename_size, $allow_upimgsize )
	{
		global $SYS_ENV;
		if ( $allow_upimgsize * 1024 < $upfilename_size )
		{
			$this->sysInfo[] = "出错！文件大小超过允许的最大范围:".round( $allow_upimgsize )."KB";
			$this->runError = true;
			return false;
		}
		else
		{
			return true;
		}
	}

	function get_file_ext( $filename, $mode = 0 )
	{
		$tmpimgname = explode( ".", $filename );
		if ( $mode == 1 )
		{
			$tmpimgnamecount = count( $tmpimgname ) - 2;
			$file_ext = $tmpimgname[$tmpimgnamecount];
		}
		else
		{
			$tmpimgnamecount = count( $tmpimgname ) - 1;
			$file_ext = $tmpimgname[$tmpimgnamecount];
		}
		return strtolower( $file_ext );
	}

	function report( )
	{
		if ( is_array( $this->sysInfo ) )
		{
			foreach ( $this->sysInfo as $key => $val )
			{
				$output .= $val."<br>";
			}
		}
		echo $output;
	}

}

?>
