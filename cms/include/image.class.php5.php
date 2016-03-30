<?php


class Image
{

	public static function makeMiniature( $srcFile, $dstFile, $dstW, $dstH )
	{
		if ( !function_exists( "GetImageSize" ) )
		{
			Error::raiseerror( "func_getimagesize_does_not_exists", E_USER_WARNING );
			return FALSE;
		}
		$data = getimagesize( $srcFile );
		switch ( $data[2] )
		{
		case 1 :
			if ( !function_exists( "ImageCreateFromGIF" ) )
			{
				Error::raiseerror( "func_imagecreatefromgif_does_not_exists", E_USER_WARNING );
				return FALSE;
			}
			$im = @imagecreatefromgif( $srcFile );
			break;
		case 2 :
			if ( !function_exists( "imagecreatefromjpeg" ) )
			{
				Error::raiseerror( "func_imagecreatefromjpeg_does_not_exists", E_USER_WARNING );
				return FALSE;
			}
			$im = @imagecreatefromjpeg( $srcFile );
			break;
		case 3 :
			if ( !function_exists( "ImageCreateFromPNG" ) )
			{
				Error::raiseerror( "func_imagecreatefrompng_does_not_exists", E_USER_WARNING );
				return FALSE;
			}
			$im = @imagecreatefrompng( $srcFile );
			break;
		}
		$srcW = imagesx( $im );
		$srcH = imagesy( $im );
		if ( $srcH < $srcW )
		{
			$tmpImgH = $dstH;
			$tmpImgW = $srcW / ( $srcH / $dstH );
		}
		else if ( $srcW <= $srcH )
		{
			$tmpImgW = $dstW;
			$tmpImgH = $srcH / ( $srcW / $dstW );
		}
		if ( !function_exists( "imagecreatetruecolor" ) )
		{
			Error::raiseerror( "func_imagecreatetruecolor_does_not_exists", E_USER_WARNING );
			return FALSE;
		}
		$tmpImg = imagecreatetruecolor( $tmpImgW, $tmpImgH );
		$white = imagecolorallocate( $tmpImg, 255, 255, 255 );
		imagecopyresampled( $tmpImg, $im, 0, 0, 0, 0, $tmpImgW, $tmpImgH, $srcW, $srcH );
		$ni = imagecreatetruecolor( $dstW, $dstH );
		$black = imagecolorallocate( $ni, 255, 255, 255 );
		imagefill( $ni, 0, 0, $black );
		imagecopyresampled( $ni, $tmpImg, 0, 0, 0, 0, $dstW, $dstH, $dstW, $dstH );
		if ( $tmpImgW < $dstW )
		{
			imagefilledrectangle( $ni, $tmpImgW, 0, $dstW, $dstH, $black );
		}
		else if ( $tmpImgH < $dstH )
		{
			imagefilledrectangle( $ni, 0, $tmpImgH, $dstW, $dstH, $black );
		}
		if ( imagejpeg( $ni, $dstFile ) )
		{
			return TRUE;
		}
		else
		{
			Error::addvar( "ni", $ni );
			Error::addvar( "distFile", $distFile );
			Error::raiseerror( "imagejpeg_failure", E_USER_WARNING );
			return FALSE;
		}
		imagedestroy( $tmpImg );
		imagedestroy( $im );
		imagedestroy( $ni );
	}

	function getImgSize( $srcFile )
	{
		if ( !function_exists( "GetImageSize" ) )
		{
			Error::raiseerror( "func_getimagesize_does_not_exists", E_USER_WARNING );
			return FALSE;
		}
		$data = getimagesize( $srcFile );
		if ( !$data )
		{
			return FALSE;
		}
		switch ( $data[2] )
		{
		case 1 :
			if ( !function_exists( "ImageCreateFromGIF" ) )
			{
				Error::raiseerror( "func_imagecreatefromgif_does_not_exists", E_USER_WARNING );
				return FALSE;
			}
			$im = imagecreatefromgif( $srcFile );
			break;
		case 2 :
			if ( !function_exists( "imagecreatefromjpeg" ) )
			{
				Error::raiseerror( "func_imagecreatefromjpeg_does_not_exists", E_USER_WARNING );
				return FALSE;
			}
			$im = imagecreatefromjpeg( $srcFile );
			break;
		case 3 :
			if ( !function_exists( "ImageCreateFromPNG" ) )
			{
				Error::raiseerror( "func_imagecreatefrompng_does_not_exists", E_USER_WARNING );
				return FALSE;
			}
			$im = imagecreatefrompng( $srcFile );
			break;
		}
		$info['width'] = imagesx( $im );
		$info['height'] = imagesy( $im );
		imagedestroy( $im );
		return $info;
	}

}

class ImgAutoLocalize extends iData
{

	function ImgAutoLocalize( $NodeID )
	{
		global $db;
		global $table;
		global $SYS_ENV;
		$sql = "SELECT varValue as num FROM {$table->sys} WHERE  varName ='ResourceNum'";
		$row = $db->getRow( $sql );
		$this->NodeID = $NodeID;
		$this->upload_num = $row[num];
		$this->uploadType = "img";
		$this->rootPath = $SYS_ENV['ResourcePath']."/";
		$this->changeName = 1;
	}

	function execute( $value )
	{
		$ImgArray = $this->_parseContent( $value );
		$localImgArray = $this->_localize( $ImgArray );
		if ( $localImgArray )
		{
			return $this->_output( $value, $ImgArray, $localImgArray );
		}
		else
		{
			return $value;
		}
	}

	function _parseContent( &$content )
	{
		$_Image_Pattern = array(
			"1" => array( "pattern" => "/<img[\\s]*[^><]*[\\s]*src=[\"]?([^\"><]*.[jpg|gif|png|jpeg])[\"]?[\\s]*[^><]*>/ise", "dataKey" => "1" )
		);
		foreach ( $_Image_Pattern as $key => $var )
		{
			$datakey = $var['dataKey'];
			if ( preg_match_all( $var[pattern], $content, $match, PREG_PATTERN_ORDER ) )
			{
				$matches[] = $match[$datakey];
			}
		}
		$img_data = $matches[0];
		if ( is_array( $img_data ) )
		{
			array_unique( $img_data );
			$img_data = $this->_imgLocalFilter( $img_data );
		}
		return $img_data;
	}

	function _imgLocalFilter( $img_data )
	{
		global $SYS_ENV;
		preg_match_all( "/{([^}]+)}/siU", $SYS_ENV[localImgIgnoreURL], $matches );
		$ignoreURLs = $matches[1];
		foreach ( $img_data as $var )
		{
			$urlinfo = parse_url( $var );
			$urlinfo[host] = strtolower( $urlinfo[host] );
			if ( in_array( $urlinfo[host], $ignoreURLs ) )
			{
				continue;
			}
			else if ( empty( $urlinfo[host] ) )
			{
				continue;
			}
			else if ( $urlinfo[host] == $_SERVER['SERVER_NAME'] || $urlinfo[host] == $_SERVER['SERVER_ADDR'] )
			{
				continue;
			}
			else
			{
				$return[] = $var;
			}
		}
		return $return;
	}

	function _output( &$value, $ImgArray, $localImgArray )
	{
		if ( !empty( $ImgArray ) )
		{
			foreach ( $ImgArray as $key => $var )
			{
				$value = str_replace( $ImgArray[$key], $localImgArray[$key], $value );
			}
		}
		return $value;
	}

	function _localize( $ImgArray )
	{
		global $db;
		global $SYS_ENV;
		if ( !is_array( $ImgArray ) )
		{
			return FALSE;
		}
		$num = 0;
		foreach ( $ImgArray as $key => $var )
		{
			$dataPath = $this->makeAutoPath( );
			$pathinfo = pathinfo( $var );
			if ( $result = $this->recordExists( $var ) )
			{
				$saveFile[$key] = $this->rootPath.$result[Path];
				continue;
			}
			$targetPath = $this->uploadType."/".$dataPath."/";
			if ( cmsware_mkdir( $this->rootPath.$targetPath, 511 ) )
			{
				if ( $this->changeName == "1" )
				{
					$rename = $this->uploadType.date( "YmdHis", time( ) ).$key.".".$pathinfo[extension];
				}
				else
				{
					$rename = $pathinfo['basename'];
				}
				$destination = $this->rootPath.$targetPath.$rename;
				if ( copy( url_valid( $var ), $destination ) )
				{
					if ( $this->uploadType == "img" )
					{
						$img_size = Image::getimgsize( $destination );
						$info = $img_size['width']."*".$img_size['height'];
					}
					$this->flushData( );
					$this->addData( "Category", $this->uploadType );
					$this->addData( "Type", 1 );
					$this->addData( "Name", $rename );
					$this->addData( "Path", $targetPath.$rename );
					$this->addData( "Size", filesize( $destination ) );
					$this->addData( "Info", $info );
					$this->insertDBLog( );
					++$num;
					$saveFile[$key] = $destination;
				}
				if ( $SYS_ENV['EnableEditorWaterMark'] == 1 )
				{
					imagewatermark( $destination, $SYS_ENV['WaterMarkPosition'], $SYS_ENV['WaterMarkImgPath'] );
				}
			}
			else
			{
				return FALSE;
			}
		}
		$this->Counter( $num );
		return $saveFile;
	}

	function Counter( $num = 1 )
	{
		global $db;
		global $table;
		$sql = "UPDATE {$table->sys} SET `varValue`=varValue+{$num}  where varName='ResourceNum'";
		$row = $db->query( $sql );
	}

	function recordExists( $src )
	{
		global $db;
		global $table;
		$result = $db->getRow( "SELECT ResourceID,Path FROM {$table->resource} WHERE Src='{$src}'" );
		if ( !empty( $result[ResourceID] ) )
		{
			return $result;
		}
		else
		{
			return FALSE;
		}
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
			return TRUE;
		}
		else
		{
			new Error( "Failure: insertDBLog" );
			return FALSE;
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

}

?>
