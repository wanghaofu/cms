<?php


if ( !defined( "METAL_LIBRARY_XML_XML_WRITER_CLASS" ) )
{
	define( "METAL_LIBRARY_XML_XML_WRITER_CLASS", 1 );
	class xml_writer_class
	{

		var $structure = array( );
		var $nodes = array( );
		var $stylesheet = "";
		var $stylesheettype = "text/xsl";
		var $dtdtype = "";
		var $dtddefinition = "";
		var $dtdurl = "";
		var $outputencoding = "utf-8";
		var $inputencoding = "iso-8859-1";
		var $linebreak = "\n";
		var $indenttext = " ";
		var $generatedcomment = "";
		var $error = "";

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

		function encodedata( $data, &$encodeddata )
		{
			if ( !strcmp( $this->inputencoding, $this->outputencoding ) )
			{
				$encodeddata = $this->escapedata( $data );
			}
			else
			{
				switch ( strtolower( $this->outputencoding ) )
				{
				case "utf-8" :
					if ( !strcmp( strtolower( $this->inputencoding ), "iso-8859-1" ) )
					{
						$encoded_data = utf8_encode( $this->escapedata( $data ) );
						$encodeddata = $encoded_data;
					}
					else
					{
						$this->error = "can not encode iso-8859-1 data in ".$this->outputencoding;
						return 0;
					}
				case "iso-8859-1" :
					if ( !strcmp( strtolower( $this->inputencoding ), "utf-8" ) )
					{
						$decoded = utf8_decode( $data );
						$encodeddata = $this->escapedata( $decoded );
					}
					else
					{
						$this->error = "can not encode utf-8 data in ".$this->outputencoding;
						return 0;
					}
				default :
					$this->error = "can not encode data in ".$this->inputencoding;
					return 0;
				}
			}
			return 1;
		}

		function writetag( &$output, $path, $indent )
		{
			$tag = $this->structure[$path]['Tag'];
			$output .= "<".$tag;
			$attributecount = count( $this->structure[$path]['Attributes'] );
			if ( 0 < $attributecount )
			{
				$attributes = $this->structure[$path]['Attributes'];
				reset( $attributes );
				$end = gettype( $key = key( $attributes ) ) != "string";
				for ( ;	!$end;	)
				{
					$output .= " ".$key."=\"".$attributes[$key]."\"";
					next( $attributes );
					$end = gettype( $key = key( $attributes ) ) != "string";
				}
			}
			$elements = $this->structure[$path]['Elements'];
			if ( 0 < $elements )
			{
				$output .= ">";
				$doindent = $this->structure[$path]['Indent'];
				$elementindent = $doindent ? $this->linebreak.$indent.$this->indenttext : "";
				$element = 0;
				for ( ;	$element < $elements;	)
				{
					$elementpath = $path.",".strval( $element );
					$output .= $elementindent;
					if ( isset( $this->nodes[$elementpath] ) )
					{
						if ( !$this->writetag( $output, $elementpath, $indent.$this->indenttext ) )
						{
							return 0;
						}
					}
					else
					{
						$output .= $this->structure[$elementpath];
					}
					++$element;
				}
				$output .= ( $doindent ? $this->linebreak.$indent : "" )."</".$tag.">";
			}
			else
			{
				$output .= "/>";
			}
			return 1;
		}

		function write( &$output )
		{
			if ( strcmp( $this->error, "" ) )
			{
				return 0;
			}
			if ( !isset( $this->structure['0'] ) )
			{
				$this->error = "XML document structure is empty";
				return 0;
			}
			$output = "<?xml version=\"1.0\" encoding=\"".$this->outputencoding."\"?>".$this->linebreak;
			if ( strcmp( $this->dtdtype, "" ) )
			{
				$output .= "<!DOCTYPE ".$this->structure['0']['Tag']." ";
				switch ( $this->dtdtype )
				{
				case "INTERNAL" :
					if ( !strcmp( $this->dtddefinition, "" ) )
					{
						$this->error = "it was not specified a valid internal DTD definition";
						return 0;
					}
					$output .= "[".$this->linebreak.$this->dtddefinition.$this->linebreak."]";
					break;
				case "SYSTEM" :
					if ( !strcmp( $this->dtdurl, "" ) )
					{
						$this->error = "it was not specified a valid system DTD url";
						return 0;
					}
					$output .= "SYSTEM";
					if ( strcmp( $this->dtddefinition, "" ) )
					{
						$output .= " \"".$this->dtddefinition."\"";
					}
					$output .= " \"".$this->dtdurl."\"";
					break;
				case "PUBLIC" :
					if ( !strcmp( $this->dtddefinition, "" ) )
					{
						$this->error = "it was not specified a valid public DTD definition";
						return 0;
					}
					$output .= "PUBLIC \"".$this->dtddefinition."\"";
					if ( !strcmp( $this->dtdurl, "" ) )
					{
						break;
					}
					$output .= " \"".$this->dtdurl."\"";
					break;
				default :
					$this->error = "it was not specified a valid DTD type";
					return 0;
				}
				$output .= ">".$this->linebreak;
			}
			if ( strcmp( $this->stylesheet, "" ) )
			{
				if ( !strcmp( $this->stylesheettype, "" ) )
				{
					$this->error = "it was not specified a valid stylesheet type";
					return 0;
				}
				$output .= "<?xml-stylesheet type=\"".$this->stylesheettype."\" href=\"".$this->stylesheet."\"?>".$this->linebreak;
			}
			if ( strcmp( $this->generatedcomment, "" ) )
			{
				$output .= "<!-- ".$this->generatedcomment." -->".$this->linebreak;
			}
			return $this->writetag( $output, "0", "" );
		}

		function addtag( $tag, &$attributes, $parent, &$path, $indent )
		{
			if ( strcmp( $this->error, "" ) )
			{
				return 0;
			}
			$path = !strcmp( $parent, "" ) ? "0" : $parent.",".strval( $this->structure[$parent]['Elements'] );
			if ( isset( $this->structure[$path] ) )
			{
				$this->error = "tag with path ".$path." is already defined";
				return 0;
			}
			$encodedattributes = array( );
			reset( $attributes );
			$end = gettype( $attribute_name = key( $attributes ) ) != "string";
			for ( ;	!$end;	)
			{
				$encodedattributes[$attribute_name] = "";
				if ( !$this->encodedata( $attributes[$attribute_name], $encoded_data ) )
				{
					return 0;
				}
				$encodedattributes[$attribute_name] = $encoded_data;
				next( $attributes );
				$end = gettype( $attribute_name = key( $attributes ) ) != "string";
			}
			$this->structure[$path] = array(
				"Tag" => $tag,
				"Attributes" => $encodedattributes,
				"Elements" => 0,
				"Indent" => $indent
			);
			$this->nodes[$path] = 1;
			if ( strcmp( $parent, "" ) )
			{
				$this->structure[$parent]['Elements'] = $this->structure[$parent]['Elements'] + 1;
			}
			return 1;
		}

		function adddata( $data, $parent, &$path )
		{
			if ( strcmp( $this->error, "" ) )
			{
				return 0;
			}
			if ( !isset( $this->structure[$parent] ) )
			{
				$this->error = "the parent tag path".$path."is not defined";
				return 0;
			}
			if ( !strcmp( $data, "" ) )
			{
				return 1;
			}
			$path = $parent.",".strval( $this->structure[$parent]['Elements'] );
			if ( !$this->encodedata( $data, $encoded_data ) )
			{
				return 0;
			}
			$this->structure[$path] = $encoded_data;
			$this->structure[$parent]['Elements'] = $this->structure[$parent]['Elements'] + 1;
			return 1;
		}

		function adddatatag( $tag, &$attributes, $data, $parent, &$path )
		{
			return $this->addtag( $tag, $attributes, $parent, $path, 0 ) && $this->adddata( $data, $path, $datapath );
		}

	}

}
?>
