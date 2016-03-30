<?php


class AutoMini
{

	var $src = "";
	var $pixel = "120*100";
	var $cache = FALSE;
	var $cacheTime = 1000;
	var $cacheFile = "";
	var $cachePath = "";
	var $miniMode = 2;
	var $miniType = "jpg";
	var $quality = 75;
	var $ContentType = array
	(
		"gif" => "Content-type: image/gif",
		"png" => "Content-type: image/png",
		"jpg" => "Content-type: image/jpeg"
	);

	function AutoMini( &$IN )
	{
		if ( isset( $IN['copyright'] ) )
		{
			header( "Content-Type: text/html; charset=utf-8" );
			exit( "<H1>AutoMini ".CLASS_VERSION."</H1> <HR>Copyright &copy; 1999-".date( "Y" )." <A HREF='http://www.localhost.com'>CMSware</A>&trade;. All rights reserved.<BR>CMSware组件，商业软件，未经允许，不得擅自使用和撒播，违者必究！ " );
		}
		if ( isset( $IN['src'] ) )
		{
			$this->src = $IN['src'];
		}
		if ( isset( $IN['pixel'] ) )
		{
			$this->pixel = $IN['pixel'];
		}
		if ( isset( $IN['cache'] ) )
		{
			$this->cache = $IN['cache'] == 1 ? TRUE : FALSE;
		}
		if ( isset( $IN['cacheTime'] ) )
		{
			$this->cacheTime = intval( $IN['cacheTime'] );
		}
		if ( isset( $IN['miniMode'] ) )
		{
			$this->miniMode = $IN['miniMode'] == 1 ? 1 : 2;
		}
		if ( isset( $IN['quality'] ) )
		{
			$this->quality = intval( $IN['quality'] );
		}
		if ( isset( $IN['cacheKey'] ) )
		{
			$this->cachePath = $this->makeAutoPath( intval( $IN['cacheKey'] ) );
		}
		if ( isset( $IN['miniType'] ) )
		{
			switch ( $IN['miniType'] )
			{
			case "gif" :
				$this->miniType = "gif";
				break;
			case "png" :
				$this->miniType = "png";
				break;
			case "jpg" :
			default :
				$this->miniType = "jpg";
				break;
			}
		}
		if ( $this->cache )
		{
			if ( !empty( $this->cachePath ) && function_exists( "CMSware_mkDir" ) )
			{
				$Path = PHOTO_CACHE_PATH.$this->cachePath;
				cmsware_mkdir( $Path );
				$Path .= "/";
			}
			else
			{
				$Path = PHOTO_CACHE_PATH;
			}
			$this->cacheFile = $Path."cache.automini.".md5( $this->src.$this->miniMode ).".".str_replace( "*", "x", $this->pixel ).".".$this->miniType;
		}
	}

	function output( )
	{
		if ( !function_exists( "GetImageSize" ) )
		{
			$this->goHeader( $this->src );
		}
		$pixelInfo = explode( "*", $this->pixel );
		$sizeInfo = $this->getImgSize( $this->src );
		if ( !$sizeInfo )
		{
			$this->goHeader( $this->src );
		}
		else if ( $sizeInfo['width'] == $pixelInfo[0] && $sizeInfo['height'] == $pixelInfo[1] )
		{
			$this->goHeader( $this->src );
		}
		else if ( $sizeInfo['width'] < $pixelInfo[0] && $sizeInfo['height'] < $pixelInfo[1] && $this->miniMode == "2" )
		{
			$this->goHeader( $this->src );
		}
		else if ( $this->cache )
		{
			if ( file_exists( $this->cacheFile ) && time( ) - filemtime( $this->cacheFile ) < $this->cacheTime )
			{
				$this->goCacheOutput( );
			}
			else
			{
				$this->makeMiniature( $pixelInfo[0], $pixelInfo[1], TRUE );
				$this->goCacheOutput( );
			}
		}
		else
		{
			$this->makeMiniature( $pixelInfo[0], $pixelInfo[1] );
		}
	}

	function goCacheOutput( )
	{
		header( $this->ContentType[$this->miniType] );
		header( "Last-Modified: ".gmdate( "D, d M Y H:i:s", time( ) + $this->cacheTime )." GMT" );
		header( "Expires: ".gmdate( "D, d M Y H:i:s", time( ) + $this->cacheTime )." GMT" );
		$handle = fopen( $this->cacheFile, "rb" );
		$contents = "";
		do
		{
			$data = fread( $handle, 8192 );
			if ( strlen( $data ) == 0 )
			{
				break;
			}
			$contents .= $data;
		} while ( 1 );
		fclose( $handle );
		print $contents;
		exit( );
	}

	function goHeader( $url )
	{
		header( "Location: ".$url );
		exit( );
	}

	function makeAutoPath( $num )
	{
		$num = strval( $num );
		$add_zero = 8 - strlen( $num );
		$num = str_repeat( "0", $add_zero ).$num;
		$DirSecond = "h".substr( $num, 0, 3 );
		$DirFirst = "h".substr( $num, -5, 2 );
		return $DirSecond."/".$DirFirst;
	}

	function getImgSize( $srcFile )
	{
		if ( !function_exists( "GetImageSize" ) )
		{
			exit( "Fatal Error : function GetImageSize() does not exists ." );
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
				exit( "Fatal Error : function ImageCreateFromGIF() does not exists ." );
				return FALSE;
			}
			$im = imagecreatefromgif( $srcFile );
			break;
		case 2 :
			if ( !function_exists( "imagecreatefromjpeg" ) )
			{
				exit( "Fatal Error : function imagecreatefromjpeg() does not exists ." );
				return FALSE;
			}
			$im = imagecreatefromjpeg( $srcFile );
			break;
		case 3 :
			if ( !function_exists( "ImageCreateFromPNG" ) )
			{
				exit( "Fatal Error : function ImageCreateFromPNG() does not exists ." );
				return FALSE;
			}
			$im = imagecreatefrompng( $srcFile );
			break;
		}
		$info['width'] = imagesx( $im );
		$info['height'] = imagesy( $im );
		return $info;
	}

	function makeMiniature( $dstW, $dstH, $cache = FALSE )
	{
		$srcFile =& $this->src;
		if ( !function_exists( "GetImageSize" ) )
		{
			exit( "func_getimagesize_does_not_exists" );
			return FALSE;
		}
		$data = getimagesize( $srcFile );
		switch ( $data[2] )
		{
		case 1 :
			if ( !function_exists( "ImageCreateFromGIF" ) )
			{
				exit( "func_imagecreatefromgif_does_not_exists" );
				return FALSE;
			}
			$im = @imagecreatefromgif( $srcFile );
			break;
		case 2 :
			if ( !function_exists( "imagecreatefromjpeg" ) )
			{
				exit( "func_imagecreatefromjpeg_does_not_exists" );
				return FALSE;
			}
			$im = @imagecreatefromjpeg( $srcFile );
			break;
		case 3 :
			if ( !function_exists( "ImageCreateFromPNG" ) )
			{
				exit( "func_imagecreatefrompng_does_not_exists" );
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
			exit( "func_imagecreatetruecolor_does_not_exists" );
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
		if ( $cache )
		{
			switch ( $this->miniType )
			{
			case "gif" :
				if ( imagegif( $ni, $this->cacheFile ) )
				{
					return TRUE;
				}
				else
				{
					exit( "imagegif_failure" );
					return FALSE;
				}
				break;
			case "jpg" :
				if ( imagejpeg( $ni, $this->cacheFile, $this->quality ) )
				{
					return TRUE;
				}
				else
				{
					exit( "imagejpeg_failure" );
					return FALSE;
				}
				break;
			case "png" :
				if ( imagepng( $ni, $this->cacheFile ) )
				{
					return TRUE;
				}
				else
				{
					exit( "imagepng_failure" );
					return FALSE;
				}
				break;
			}
		}
		else
		{
			header( $this->ContentType[$this->miniType] );
			switch ( $this->miniType )
			{
			case "gif" :
				if ( imagegif( $ni ) )
				{
					return TRUE;
				}
				else
				{
					exit( "imagegif_failure" );
					return FALSE;
				}
				break;
			case "jpg" :
				if ( imagejpeg( $ni ) )
				{
					return TRUE;
				}
				else
				{
					exit( "imagejpeg_failure" );
					return FALSE;
				}
				break;
			case "png" :
				if ( imagepng( $ni ) )
				{
					return TRUE;
				}
				else
				{
					exit( "imagepng_failure" );
					return FALSE;
				}
				break;
			}
		}
		imagedestroy( $tmpImg );
		imagedestroy( $im );
		imagedestroy( $ni );
	}

}

require_once( "common.php" );
require_once( "automini.config.php" );
define( "CLASS_VERSION", "1.0" );
$automini = new AutoMini( $_GET );
$automini->output( );
?>
