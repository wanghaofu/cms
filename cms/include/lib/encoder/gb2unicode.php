<?php


function gb2unicode( $gb )
{
	if ( !trim( $gb ) )
	{
		return $gb;
	}
	$filename = INCLUDE_PATH."lib/encoder/gb2312.txt";
	$tmp = file( $filename );
	$codetable = array( );
	while ( list( $key, $value ) = each( $tmp ) )
	{
		$codetable[hexdec( substr( $value, 0, 6 ) )] = substr( $value, 9, 4 );
	}
	$utf = "";
	while ( $gb )
	{
		if ( 127 < ord( substr( $gb, 0, 1 ) ) )
		{
			$hawking = substr( $gb, 0, 2 );
			$gb = substr( $gb, 2, strlen( $gb ) );
			$utf .= "&#x".$codetable[hexdec( bin2hex( $hawking ) ) - 32896].";";
		}
		else
		{
			$utf .= substr( $gb, 0, 1 );
			$gb = substr( $gb, 1, strlen( $gb ) );
		}
	}
	return $utf;
}

?>
