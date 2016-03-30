<?php


class Encoding
{

	var $GetEncoding = "1";
	var $ToEncoding = "3";
	var $UnicodeString = "";
	var $Encodings = array
	(
		1 => "GBK",
		2 => "UTF-8",
		3 => "UTF-16BE",
		4 => "UTF-16LE",
		5 => "BIG5"
	);
	var $FilePath = "lib/encoder/";

	function Encoding( )
	{
		$this->FilePath = INCLUDE_PATH."lib/encoder/";
	}

	function SetGetEncoding( $GetEncoding )
	{
		$order = 0;
		if ( $this->CheckEncoding( $GetEncoding, $order ) )
		{
			$this->GetEncoding = $order;
			return true;
		}
		echo "警告:您设置了程序不支持的编码类型,编码格式将不做变动";
		return false;
	}

	function SetToEncoding( $ToEncoding )
	{
		$order = 0;
		if ( $this->CheckEncoding( $ToEncoding, $order ) )
		{
			$this->ToEncoding = $order;
			return true;
		}
		echo "警告:您设置了程序不支持的编码类型,编码格式将不做变动";
		return false;
	}

	function EncodeString( $String )
	{
		if ( $this->GetEncoding == "1" )
		{
			$this->UnicodeString = $this->GBKToUnicode( $String );
		}
		else if ( $this->GetEncoding == "2" )
		{
			$this->UnicodeString = $this->UTFToUnicode( $String );
		}
		else if ( $this->GetEncoding == "3" )
		{
			$this->UnicodeString = $String;
		}
		else if ( $this->GetEncoding == "4" )
		{
			$this->UnicodeString = $this->ChangeByte( $String );
		}
		else if ( $this->GetEncoding == "5" )
		{
			$this->UnicodeString = $this->BIGToUnicode( $String );
		}
		if ( $this->ToEncoding == "1" )
		{
			return $this->UnicodeToGBK( $this->UnicodeString );
		}
		else if ( $this->ToEncoding == "2" )
		{
			return $this->UnicodeToUTF( $this->UnicodeString );
		}
		else if ( $this->ToEncoding == "3" )
		{
			return $this->UnicodeString;
		}
		else if ( $this->ToEncoding == "4" )
		{
			return $this->ChangeByte( $this->UnicodeString );
		}
		else if ( $this->ToEncoding == "5" )
		{
			return $this->UnicodeToBIG( $this->UnicodeString );
		}
	}

	function GBKToUnicode( &$String )
	{
		$UnicodeData = file( $this->FilePath."gbkunicode.data" );
		$ReturnString = "";
		$StringLength = strlen( $String );
		$p = "";
		$q = "";
		$i = 0;
		for ( ;	$i < $StringLength;	++$i	)
		{
			if ( 128 < ( $p = ord( substr( $String, $i, 1 ) ) ) )
			{
				$q = ord( substr( $String, ++$i, 1 ) );
				if ( 254 < $p )
				{
					$ReturnString .= "003f";
				}
				else if ( $q < 64 || 254 < $q )
				{
					$ReturnString .= "003f";
				}
				else
				{
					$q = ( $q - 64 ) * 4;
					$ReturnString .= substr( $UnicodeData[$p - 128], $q, 4 );
				}
			}
			else if ( $p == 128 )
			{
				$ReturnString .= "20ac";
			}
			else
			{
				$ReturnString .= "00";
				$ReturnString .= dechex( $p );
			}
		}
		return $this->hex2bin( $ReturnString );
	}

	function BIGToUnicode( &$String )
	{
		$UnicodeData = file( $this->FilePath."bigunicode.data" );
		$ReturnString = "";
		$StringLength = strlen( $String );
		$p = "";
		$q = "";
		$i = 0;
		for ( ;	$i < $StringLength;	++$i	)
		{
			if ( 128 < ( $p = ord( substr( $String, $i, 1 ) ) ) )
			{
				$q = ord( substr( $String, ++$i, 1 ) );
				if ( 249 < $p )
				{
					$ReturnString .= "003f";
				}
				else if ( $q < 64 || 254 < $q )
				{
					$ReturnString .= "003f";
				}
				else
				{
					$q = ( $q - 64 ) * 4;
					$ReturnString .= substr( $UnicodeData[$p - 160], $q, 4 );
				}
			}
			else
			{
				$ReturnString .= "00";
				$ReturnString .= dechex( $p );
			}
		}
		return $this->hex2bin( $ReturnString );
	}

	function UnicodeToGBK( &$String )
	{
		$GBKData = file( $this->FilePath."unicodegbk.data" );
		$ReturnString = "";
		$StringLength = strlen( $String );
		$p = "";
		$q = "";
		$temp = "";
		$i = 0;
		for ( ;	$i < $StringLength;	++$i	)
		{
			$p = ord( substr( $String, $i++, 1 ) );
			if ( $i == $StringLength )
			{
				$temp = dechex( $p );
				if ( strlen( $temp ) < 2 )
				{
					$temp = "0".$temp;
				}
				$ReturnString .= $temp;
				continue;
			}
			$q = ord( substr( $String, $i, 1 ) );
			if ( $p == 0 && $q < 127 )
			{
				$temp = dechex( $q );
				if ( strlen( $temp ) < 2 )
				{
					$temp = "0".$temp;
				}
				$ReturnString .= $temp;
				continue;
			}
			++$p;
			$begin = hexdec( substr( $GBKData[$p], 0, 2 ) );
			if ( strlen( $GBKData[$p] ) < 3 || $q < $begin || hexdec( substr( $GBKData[$p], 2, 2 ) ) < $q )
			{
				$ReturnString .= "3f";
				continue;
			}
			$q *= 4;
			$q -= $begin * 4;
			$temp = substr( $GBKData[$p], $q + 4, 2 );
			if ( $temp == "00" )
			{
				$ReturnString .= substr( $GBKData[$p], $q + 6, 2 );
			}
			else
			{
				$ReturnString .= $temp.substr( $GBKData[$p], $q + 6, 2 );
			}
		}
		return $this->hex2bin( $ReturnString );
	}

	function UnicodeToBIG( &$String )
	{
		$BIGData = file( $this->FilePath."unicodebig.data" );
		$ReturnString = "";
		$StringLength = strlen( $String );
		$p = "";
		$q = "";
		$temp = "";
		$i = 0;
		for ( ;	$i < $StringLength;	++$i	)
		{
			$p = ord( substr( $String, $i++, 1 ) );
			if ( $i == $StringLength )
			{
				$temp = dechex( $p );
				if ( strlen( $temp ) < 2 )
				{
					$temp = "0".$temp;
				}
				$ReturnString .= $temp;
				continue;
			}
			$q = ord( substr( $String, $i, 1 ) );
			if ( $p == 0 && $q < 127 )
			{
				$temp = dechex( $q );
				if ( strlen( $temp ) < 2 )
				{
					$temp = "0".$temp;
				}
				$ReturnString .= $temp;
				continue;
			}
			++$p;
			$begin = hexdec( substr( $BIGData[$p], 0, 2 ) );
			if ( strlen( $BIGData[$p] ) < 3 || $q < $begin || hexdec( substr( $BIGData[$p], 2, 2 ) ) < $q )
			{
				$ReturnString .= "3f";
				continue;
			}
			$q *= 4;
			$q -= $begin * 4;
			$temp = substr( $BIGData[$p], $q + 4, 2 );
			if ( $temp == "00" )
			{
				$ReturnString .= substr( $BIGData[$p], $q + 6, 2 );
			}
			else
			{
				$ReturnString .= $temp.substr( $BIGData[$p], $q + 6, 2 );
			}
		}
		return $this->hex2bin( $ReturnString );
	}

	function UnicodeToUTF( &$String )
	{
		$len = strlen( $String );
		$ReturnString = "";
		$x = 0;
		for ( ;	$x < $len;	++$x	)
		{
			$Char = substr( $String, $x++, 1 );
			if ( $x == $len )
			{
				$ReturnString .= bin2hex( $Char );
				continue;
			}
			$Char .= substr( $String, $x, 1 );
			$hex = bin2hex( $Char );
			$dec = hexdec( $hex );
			$bin = decbin( $dec );
			$temp = "";
			if ( 127 < $dec )
			{
				$binlen = strlen( $bin );
				$i = 0;
				for ( ;	$i < 16 - $binlen;	++$i	)
				{
					$bin = "0".$bin;
				}
				$temp .= "1110".substr( $bin, 0, 4 );
				$temp .= "10".substr( $bin, 4, 6 );
				$temp .= "10".substr( $bin, 10, 6 );
				$temp = dechex( bindec( $temp ) );
			}
			else
			{
				$temp = substr( $hex, 2, 2 );
			}
			$ReturnString .= $temp;
		}
		return $this->hex2bin( $ReturnString );
	}

	function ChangeByte( &$String )
	{
		$len = strlen( $String );
		$ReturnString = "";
		$i = 0;
		for ( ;	$i < $len;	++$i	)
		{
			if ( $i + 1 != $len )
			{
				$ReturnString .= substr( $String, $i + 1, 1 ).substr( $String, $i++, 1 );
			}
			else
			{
				$ReturnString .= substr( $String, $i, 1 );
			}
		}
		return $ReturnString;
	}

	function UTFToUnicode( &$String )
	{
		$UTFlen = strlen( $String );
		$x = "";
		$y = "";
		$z = "";
		$ReturnString = "";
		$i = 0;
		for ( ;	$i < $UTFlen;	++$i	)
		{
			if ( 128 < ( $x = ord( substr( $String, $i, 1 ) ) ) )
			{
				if ( $i + 1 == $UTFlen )
				{
					$ReturnString .= dechex( $x );
					continue;
				}
				$y = ord( substr( $String, ++$i, 1 ) );
				if ( $i + 1 == $UTFlen )
				{
					$ReturnString .= dechex( $x ).dechex( $y );
					continue;
				}
				$x = decbin( $x );
				$y = decbin( $y );
				$z = decbin( ord( substr( $String, ++$i, 1 ) ) );
				$temp = dechex( bindec( substr( $x, 4, 4 ).substr( $y, 2, 4 ).substr( $y, 6, 2 ).substr( $z, 2, 6 ) ) );
				$len = strlen( $temp );
				$j = 0;
				for ( ;	$j < 4 - $len;	++$j	)
				{
					$temp = "0".$temp;
				}
				$ReturnString .= $temp;
			}
			else
			{
				$ReturnString .= "00";
				$ReturnString .= dechex( $x );
			}
		}
		return $this->hex2bin( $ReturnString );
	}

	function hex2bin( &$String )
	{
		$ReturnString = "";
		$len = strlen( $String );
		$i = 0;
		for ( ;	$i < $len;	$i += 2	)
		{
			$ReturnString .= pack( "C", hexdec( substr( $String, $i, 2 ) ) );
		}
		return $ReturnString;
	}

	function CheckEncoding( $Encoding, &$order )
	{
		$order = 0;
		reset( $this->Encodings );
		while ( list( $key, $value ) = each( $this->Encodings ) )
		{
			if ( $Encoding == $value )
			{
				$order = $key;
				return true;
			}
		}
		return false;
	}

}

?>
