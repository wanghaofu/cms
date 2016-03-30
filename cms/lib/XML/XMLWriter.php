<?php


require_once( dirname( __FILE__ )."/xmlwriter/xmlwriterclass.php" );
class XML_XMLWriter extends xml_writer_class
{

	function XML_XMLWriter( )
	{
		$this->inputencoding = "utf-8";
		$this->outputencoding = "utf-8";
	}

	function writeToFile( $file )
	{
		$this->write( $output );
		$this->File =& get_singleton( "FileSystem.File" );
		return $this->File->write( $file, $output );
	}

	function clear( )
	{
		$this->structure = array( );
		$this->nodes = array( );
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
