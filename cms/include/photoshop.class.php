<?php


class Photoshop
{

	var $srcImg = NULL;
	var $dstImg = NULL;
	var $saveImg = NULL;
	var $dstImgId = NULL;
	var $quality = 80;
	var $im = NULL;
	var $srcW = NULL;
	var $srcH = NULL;
	var $workPath = NULL;
	var $savePath = NULL;

	function Photoshop( $params )
	{
		if ( isset( $params['srcImg'] ) )
		{
			$this->srcImg = $params['srcImg'];
		}
		if ( isset( $params['workPath'] ) )
		{
			$this->workPath = $params['workPath'];
		}
		if ( isset( $params['savePath'] ) )
		{
			$this->savePath = $params['savePath'];
		}
		if ( isset( $params['quality'] ) )
		{
			$this->quality = $params['quality'];
		}
	}

	function init( )
	{
		$data = getimagesize( $this->srcImg );
		switch ( $data[2] )
		{
		case 1 :
			$this->im = imagecreatefromgif( $this->srcImg );
			break;
		case 2 :
			$this->im = imagecreatefromjpeg( $this->srcImg );
			break;
		case 3 :
			$this->im = imagecreatefrompng( $this->srcImg );
			break;
		}
		$this->srcW = imagesx( $this->im );
		$this->srcH = imagesy( $this->im );
		$this->dstImgId = md5( $this->getmicrotime( ).mt_rand( 0, 100 ) );
		$this->dstImg = $this->workPath.$this->dstImgId.".jpg";
		$this->addLog( );
	}

	function save( )
	{
		$hello = pathinfo( $this->srcImg );
		$this->saveImg = $this->savePath."/".$hello['basename'];
		if ( copy( $this->srcImg, $this->saveImg ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function getmicrotime( )
	{
		list( $usec, $sec ) = explode( " ", microtime( ) );
		return ( double )$usec + ( double )$sec;
	}

	function addLog( )
	{
		$cut_off_stamp = time( ) - 3600;
		$result = dbquery( "SELECT * FROM ".$GLOBALS['tbl_photoeditor']." WHERE opTime < {$cut_off_stamp}" );
		while ( $row = dbfetchrow( $result ) )
		{
			$file = $this->workPath.$row['opId'].".jpg";
			unlink( $file );
		}
		dbquery( "DELETE FROM ".$GLOBALS['tbl_photoeditor']." WHERE opTime < {$cut_off_stamp}" );
		$sql = "INSERT INTO ".$GLOBALS['tbl_photoeditor']." (`opId`,  `opTime`) VALUES ('".$this->dstImgId."','".time( )."')";
		dbquery( $sql );
	}

	function newphoto( $width, $height, $color )
	{
		$this->init( );
		$tmpImg = imagecreatetruecolor( $width, $height );
		$RGB = $this->str2RGB( $color );
		$white = imagecolorallocate( $tmpImg, $RGB[0], $RGB[1], $RGB[2] );
		imagefilledrectangle( $tmpImg, 0, 0, $width, $height, $white );
		if ( imagejpeg( $tmpImg, $this->dstImg, $this->quality ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
		imagedestroy( $tmpImg );
	}

	function crop( $left, $top, $width, $height )
	{
		$this->init( );
		$tmpImg = imagecreatetruecolor( $width, $height );
		$black = imagecolorallocate( $tmpImg, 255, 255, 255 );
		imagefilledrectangle( $tmpImg, 0, 0, $width, $height, $black );
		imagecopy( $tmpImg, $this->im, 0, 0, $left, $top, $width, $height );
		if ( imagejpeg( $tmpImg, $this->dstImg, $this->quality ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
		imagedestroy( $tmpImg );
		imagedestroy( $this->im );
	}

	function str2RGB( $color )
	{
		$color = str_replace( "#", "", $color );
		$colorArray[] = substr( $color, 0, 2 );
		$colorArray[] = substr( $color, 2, 2 );
		$colorArray[] = substr( $color, 4, 2 );
		foreach ( $colorArray as $var )
		{
			$RGB[] = hexdec( "0x".$var );
		}
		return $RGB;
	}

	function overlay( $left, $top, $font, $size, $color, $wrap, $text )
	{
		$this->init( );
		$textInfo = imagettfbbox( $size, 0, $font, $text );
		$height = abs( $textInfo[1] - $textInfo[7] );
		$top = $top + $height - $height * 0.22;
		$RGB = $this->str2RGB( $color );
		$white = imagecolorallocate( $this->im, $RGB[0], $RGB[1], $RGB[2] );
		imagettftext( $this->im, $size, 0, $left, $top, $white, $font, $text );
		if ( imagejpeg( $this->im, $this->dstImg, $this->quality ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
		imagedestroy( $this->im );
	}

	function luminance( $level )
	{
		$this->init( );
		$tmpImg = imagecreatetruecolor( $this->srcW, $this->srcH );
		$black = imagecolorallocate( $tmpImg, 255, 255, 255 );
		imagefilledrectangle( $tmpImg, 0, 0, $this->srcW, $this->srcH, $black );
		$w = 0;
		for ( ;	$w <= $this->srcW;	++$w	)
		{
			$h = 0;
			for ( ;	$h <= $this->srcH;	++$h	)
			{
				$rgb = imagecolorat( $this->im, $w, $h );
				$r = $rgb >> 16 & 255;
				$g = $rgb >> 8 & 255;
				$b = $rgb & 255;
				$lev = floor( ( $level - 50 ) * 2.55 );
				$r += $lev;
				$g += $lev;
				$b += $lev;
				$r = 255 < $r ? 255 : $r;
				$g = 255 < $g ? 255 : $g;
				$b = 255 < $b ? 255 : $b;
				$r = $r < 0 ? 0 : $r;
				$g = $g < 0 ? 0 : $g;
				$b = $b < 0 ? 0 : $b;
				$rgb = imagecolorallocate( $tmpImg, $r, $g, $b );
				imagesetpixel( $tmpImg, $w, $h, $rgb );
			}
		}
		if ( imagejpeg( $tmpImg, $this->dstImg, $this->quality ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
		imagedestroy( $this->im );
		imagedestroy( $tmpImg );
	}

	function contrast( $level )
	{
	}

	function colorize( $mode )
	{
		$this->init( );
		switch ( $mode )
		{
		case "1" :
			$tmpImg = imagecreatetruecolor( $this->srcW, $this->srcH );
			$black = imagecolorallocate( $tmpImg, 255, 255, 255 );
			imagefilledrectangle( $tmpImg, 0, 0, $this->srcW, $this->srcH, $black );
			$w = 0;
			for ( ;	$w <= $this->srcW;	++$w	)
			{
				$h = 0;
				for ( ;	$h <= $this->srcH;	++$h	)
				{
					$rgb = imagecolorat( $this->im, $w, $h );
					$r = $rgb >> 16 & 255;
					$g = $rgb >> 8 & 255;
					$b = $rgb & 255;
					$r ^= 255;
					$g ^= 255;
					$b ^= 255;
					$rgb = imagecolorallocate( $tmpImg, $r, $g, $b );
					imagesetpixel( $tmpImg, $w, $h, $rgb );
				}
			}
			if ( imagejpeg( $tmpImg, $this->dstImg, $this->quality ) )
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
			imagedestroy( $this->im );
			imagedestroy( $tmpImg );
			break;
		case "2" :
			$tmpImg = imagecreatetruecolor( $this->srcW, $this->srcH );
			$black = imagecolorallocate( $tmpImg, 255, 255, 255 );
			imagefilledrectangle( $tmpImg, 0, 0, $this->srcW, $this->srcH, $black );
			$w = 0;
			for ( ;	$w <= $this->srcW;	++$w	)
			{
				$h = 0;
				for ( ;	$h <= $this->srcH;	++$h	)
				{
					$rgb = imagecolorat( $this->im, $w, $h );
					$r = $rgb >> 16 & 255;
					$g = $rgb >> 8 & 255;
					$b = $rgb & 255;
					$rgb = floor( $b * 0.11 + $g * 0.59 + $r * 0.3 );
					$rgb ^= 255;
					$rgb = imagecolorallocate( $tmpImg, $rgb, $rgb, $rgb );
					imagesetpixel( $tmpImg, $w, $h, $rgb );
				}
			}
			if ( imagejpeg( $tmpImg, $this->dstImg, $this->quality ) )
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
			imagedestroy( $this->im );
			imagedestroy( $tmpImg );
			break;
		case "3" :
			$tmpImg = imagecreatetruecolor( $this->srcW, $this->srcH );
			$black = imagecolorallocate( $tmpImg, 255, 255, 255 );
			imagefilledrectangle( $tmpImg, 0, 0, $this->srcW, $this->srcH, $black );
			$w = 0;
			for ( ;	$w <= $this->srcW;	++$w	)
			{
				$h = 0;
				for ( ;	$h <= $this->srcH;	++$h	)
				{
					$rgb = imagecolorat( $this->im, $w, $h );
					$r = $rgb >> 16 & 255;
					$g = $rgb >> 8 & 255;
					$b = $rgb & 255;
					$rgb = floor( $b * 0.11 + $g * 0.59 + $r * 0.3 );
					$rgb = imagecolorallocate( $tmpImg, $rgb, $rgb, $rgb );
					imagesetpixel( $tmpImg, $w, $h, $rgb );
				}
			}
			if ( imagejpeg( $tmpImg, $this->dstImg, $this->quality ) )
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
			imagedestroy( $this->im );
			imagedestroy( $tmpImg );
			break;
		case "4" :
			imagetruecolortopalette( $this->im, TRUE, 1 );
			if ( imagejpeg( $this->im, $this->dstImg, $this->quality ) )
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
			imagedestroy( $this->im );
			break;
		case "5" :
			imagetruecolortopalette( $this->im, TRUE, 2 );
			if ( imagejpeg( $this->im, $this->dstImg, $this->quality ) )
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
			imagedestroy( $this->im );
			break;
		}
	}

	function rotate( $mode )
	{
		$this->init( );
		switch ( $mode )
		{
		case "1" :
			$this->im = imagerotate( $this->im, 90, $black );
			if ( imagejpeg( $this->im, $this->dstImg, $this->quality ) )
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
			imagedestroy( $this->im );
			break;
		case "2" :
			$this->im = imagerotate( $this->im, -90, $black );
			if ( imagejpeg( $this->im, $this->dstImg, $this->quality ) )
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
			imagedestroy( $this->im );
			break;
		case "3" :
			$this->im = imagerotate( $this->im, 180, $black );
			if ( imagejpeg( $this->im, $this->dstImg, $this->quality ) )
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
			imagedestroy( $this->im );
			break;
		case "4" :
			$tmpImg = imagecreatetruecolor( $this->srcW, $this->srcH );
			$black = imagecolorallocate( $tmpImg, 255, 255, 255 );
			imagefilledrectangle( $tmpImg, 0, 0, $this->srcW, $this->srcH, $black );
			$width_header = $this->srcW / 2;
			$w = 0;
			for ( ;	$w <= $this->srcW;	++$w	)
			{
				$h = 0;
				for ( ;	$h <= $this->srcH;	++$h	)
				{
					$rgb = imagecolorat( $this->im, $w, $h );
					$width = $w + ( $width_header - $w ) * 2;
					imagesetpixel( $tmpImg, $width, $h, $rgb );
				}
			}
			if ( imagejpeg( $tmpImg, $this->dstImg, $this->quality ) )
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
			imagedestroy( $tmpImg );
			imagedestroy( $this->im );
			break;
		case "5" :
			$tmpImg = imagecreatetruecolor( $this->srcW, $this->srcH );
			$black = imagecolorallocate( $tmpImg, 255, 255, 255 );
			imagefilledrectangle( $tmpImg, 0, 0, $this->srcW, $this->srcH, $black );
			$height_header = $this->srcH / 2;
			$w = 0;
			for ( ;	$w <= $this->srcW;	++$w	)
			{
				$h = 0;
				for ( ;	$h <= $this->srcH;	++$h	)
				{
					$rgb = imagecolorat( $this->im, $w, $h );
					$height = $h + ( $height_header - $h ) * 2;
					imagesetpixel( $tmpImg, $w, $height, $rgb );
				}
			}
			if ( imagejpeg( $tmpImg, $this->dstImg, $this->quality ) )
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
			imagedestroy( $tmpImg );
			imagedestroy( $this->im );
			break;
		case "6" :
			$tmpImg = imagecreatetruecolor( $this->srcW, $this->srcH );
			$black = imagecolorallocate( $tmpImg, 0, 255, 255 );
			imagefilledrectangle( $tmpImg, 0, 0, $this->srcW, $this->srcH, $black );
			$width_header = $this->srcW / 2;
			$w = 0;
			for ( ;	$w <= $this->srcW;	++$w	)
			{
				$h = 0;
				for ( ;	$h <= $this->srcH;	++$h	)
				{
					$rgb = imagecolorat( $this->im, $w, $h );
					$width = $w + ( $width_header - $w ) * 2;
					imagesetpixel( $tmpImg, $width, $h, $rgb );
				}
			}
			$tmpImg = imagerotate( $tmpImg, 90, $black );
			if ( imagejpeg( $tmpImg, $this->dstImg, $this->quality ) )
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
			imagedestroy( $tmpImg );
			imagedestroy( $this->im );
			break;
		case "7" :
			$tmpImg = imagecreatetruecolor( $this->srcW, $this->srcH );
			$black = imagecolorallocate( $tmpImg, 0, 255, 255 );
			imagefilledrectangle( $tmpImg, 0, 0, $this->srcW, $this->srcH, $black );
			$width_header = $this->srcW / 2;
			$w = 0;
			for ( ;	$w <= $this->srcW;	++$w	)
			{
				$h = 0;
				for ( ;	$h <= $this->srcH;	++$h	)
				{
					$rgb = imagecolorat( $this->im, $w, $h );
					$width = $w + ( $width_header - $w ) * 2;
					imagesetpixel( $tmpImg, $width, $h, $rgb );
				}
			}
			$tmpImg = imagerotate( $tmpImg, -90, $black );
			if ( imagejpeg( $tmpImg, $this->dstImg, $this->quality ) )
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
			imagedestroy( $tmpImg );
			imagedestroy( $this->im );
			break;
		}
	}

	function scale( $zoom, $width, $height )
	{
		$this->init( );
		if ( $zoom != "" )
		{
			$zoom = intval( $zoom );
			$tmpImgW = $this->srcW * $zoom / 100;
			$tmpImgH = $this->srcH * $zoom / 100;
		}
		else if ( $width != "" && $height != "" )
		{
			$tmpImgW = intval( $width );
			$tmpImgH = intval( $height );
		}
		else if ( $width != "" && $height == "" )
		{
			$width = intval( $width );
			$tmpImgW = $width;
			$tmpImgH = intval( $this->srcH / ( $this->srcW / $width ) );
		}
		else
		{
			return FALSE;
		}
		$tmpImg = imagecreatetruecolor( $tmpImgW, $tmpImgH );
		$black = imagecolorallocate( $tmpImg, 0, 0, 0 );
		imagefilledrectangle( $tmpImg, 0, 0, $tmpImgW, $tmpImgH, $black );
		imagecopyresampled( $tmpImg, $this->im, 0, 0, 0, 0, $tmpImgW, $tmpImgH, $this->srcW, $this->srcH );
		if ( imagejpeg( $tmpImg, $this->dstImg, $this->quality ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
		imagedestroy( $tmpImg );
		imagedestroy( $this->im );
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

}

?>
