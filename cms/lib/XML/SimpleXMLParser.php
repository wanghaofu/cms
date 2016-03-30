<?php


class XML_SimpleXMLParser
{

	var $File = NULL;
	var $xpath = array
	(
		"models" => array
		(
			"model" => array
			(
				0 => "name",
				1 => "TableID",
				2 => "file"
			)
		),
		"test" => "__auto__"
	);
	var $returnValue = NULL;

	function XML_SimpleXMLParser( )
	{
		}

	function parseFile( $_file, $_xpath )
	{
		$this->File =& get_singleton( "FileSystem.File" );
		$xml_content = $this->File->read( $_file );
		if ( !empty( $this->getEncoding ) && !empty( $this->toEncoding ) )
		{
			if ( empty( $this->CharEncoding ) )
			{
				require_once( INCLUDE_PATH."/encoding/encoding.inc.php" );
				$this->CharEncoding = new Encoding( );
			}
			$this->CharEncoding->SetGetEncoding( $this->getEncoding );
			$this->CharEncoding->SetToEncoding( $this->toEncoding );
			$xml_content = $this->CharEncoding->EncodeString( $xml_content );
		}
		$this->returnValue = array( );
		$this->_parsing( $xml_content, $_xpath );
		return $this->returnValue;
	}

	function parse( $_content, $_xpath )
	{
		$this->returnValue = array( );
		$this->_parsing( $_content, $_xpath );
		return $this->returnValue;
	}

	function getXMLFileEncoding( $_file )
	{
		$this->File =& get_singleton( "FileSystem.File" );
		$xml_content = $this->File->read( $_file );
		$this->returnValue = array( );
		preg_match( "/<\\?xml[^\r\n]*[\\s]+encoding=\"([a-zA-z0-9-]+)\"\\?>/isU", $xml_content, $match );
		return strtolower( $match['1'] );
	}

	function _parsing( $_content, $_xpath )
	{
		foreach ( $_xpath as $key => $var )
		{
			if ( is_array( $var ) )
			{
				$this->returnValue[$key] = $this->_parsingSub( $this->_getElementInnerData( $key, $_content ), $var );
			}
			else if ( $var == "__auto__" )
			{
				$this->returnValue[$key] = $this->_parsingAuto( $this->_getElementInnerData( $key, $_content ) );
			}
			else
			{
				$this->returnValue[$var] = $this->unescapedata( $this->_getElementInnerData( $var, $_content ) );
			}
		}
	}

	function _parsingSub( $_content, $_xpath )
	{
		$subReturnValue = array( );
		foreach ( $_xpath as $key => $var )
		{
			if ( is_array( $var ) )
			{
				if ( preg_match_all( "/<{$key}[^<>]*>(.*)<\\/{$key}>/isU", $_content, $matches ) )
				{
					foreach ( $matches[0] as $keyIn => $varIn )
					{
						$inner_content = $matches[1][$keyIn];
						$subReturnValue[$key][] = $this->_parsingSub( $inner_content, $var );
					}
				}
			}
			else if ( $var == "__auto__" )
			{
				if ( preg_match_all( "/<{$key}[^<>]*>(.*)<\\/{$key}>/isU", $_content, $matches ) )
				{
					foreach ( $matches[0] as $keyIn => $varIn )
					{
						$inner_content = $matches[1][$keyIn];
						$subReturnValue[$key][] = $this->_parsingAuto( $inner_content );
					}
				}
			}
			else
			{
				$subReturnValue[$var] = $this->unescapedata( $this->_getElementInnerData( $var, $_content ) );
			}
		}
		if ( empty( $subReturnValue ) )
		{
			$subReturnValue = $this->unescapedata( $_content );
		}
		return $subReturnValue;
	}

	function _getElementInnerData( $_element, $_content )
	{
		if ( preg_match( "/<{$_element}[^<>]*>(.*)<\\/{$_element}>/is", $_content, $match ) )
		{
			return $match[1];
		}
		else
		{
			return false;
		}
	}

	function _parsingAuto( $_content )
	{
		$return = array( );
		if ( preg_match_all( "/<([^<>]*)>([^<>]*)<\\/([^<>]*)>/is", $_content, $autoMatches ) )
		{
			foreach ( $autoMatches[1] as $key => $var )
			{
				$return_key = $autoMatches[1][$key];
				$return[$return_key] = $this->unescapedata( $autoMatches[2][$key] );
			}
			if ( preg_match_all( "/<([^<>]*)\\/>/is", $_content, $autoMatches ) )
			{
				foreach ( $autoMatches[1] as $key => $var )
				{
					$return_key = $autoMatches[1][$key];
					$return[$return_key] = "";
				}
				return $return;
			}
			return $return;
		}
		else
		{
			return false;
		}
	}

	function escapedata( $data )
	{
		$position = 0;
		$length = strlen( $data );
		$escapeddata = "";
		for ( ;	$position < $length;	)
		{
			$character = substr( $data, $position, 1 );
			$code = ord( $character );
			switch ( $code )
			{
			case 34 :
				$character = "&quot;";
				break;
			case 38 :
				$character = "&amp;";
				break;
			case 39 :
				$character = "&apos;";
				break;
			case 60 :
				$character = "&lt;";
				break;
			case 62 :
				$character = "&gt;";
				break;
			default :
				if ( !( $code < 32 ) )
				{
					break;
				}
				$character = "&#".strval( $code ).";";
				break;
			}
			$escapeddata .= $character;
			++$position;
		}
		return $escapeddata;
	}

	function unescapedata( $data )
	{
		$data = str_replace( "&quot;", chr( 34 ), $data );
		$data = str_replace( "&amp;", chr( 38 ), $data );
		$data = str_replace( "&apos;", chr( 39 ), $data );
		$data = str_replace( "&lt;", chr( 60 ), $data );
		$data = str_replace( "&gt;", chr( 62 ), $data );
		$i = 0;
		for ( ;	$i < 32;	++$i	)
		{
			$data = str_replace( "&#".strval( $i ).";", chr( $i ), $data );
		}
		return $data;
	}

}

?>
