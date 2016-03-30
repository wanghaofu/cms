<?php


require( "xmlwriterclass.php" );
$xml_writer_object =& new xml_writer_class( );
$noattributes = array( );
$xml_writer_object->addtag( "myxmldocument", $noattributes, "", $root, 1 );
$xml_writer_object->addtag( "name", $noattributes, $root, $toptag, 0 );
$xml_writer_object->adddata( "John Doe", $toptag, $path );
$attributes = array( );
$attributes['country'] = "us";
$xml_writer_object->addtag( "address", $attributes, $root, $toptag, 1 );
$xml_writer_object->adddatatag( "street", $noattributes, "Wall Street, 1641", $toptag, $datatag );
$xml_writer_object->adddatatag( "zip", $noattributes, "NY 72834", $toptag, $datatag );
$xml_writer_object->dtdtype = "SYSTEM";
$xml_writer_object->dtdurl = "myxmldocument.dtd";
$xml_writer_object->stylesheettype = "text/xsl";
$xml_writer_object->stylesheet = "myxmldocument.xsl";
if ( $xml_writer_object->write( $output ) )
{
	echo $output;
}
else
{
	echo "Error: ".$xml_writer_object->error;
}
?>
