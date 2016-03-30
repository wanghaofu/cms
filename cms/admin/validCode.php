<?php


function creatValidateImg( $width, $height, $space, $size, $disturb_num, $sname = "" )
{
	$left = 1;
	$top = 0;
	$authstr = mt_rand( 1000, 9999 );
	if ( $sname != "" )
	{
		$_SESSION[$sname] = $authstr;
	}
	$image = imagecreate( $width, $height );
	$colorList[] = imagecolorallocate( $image, 15, 73, 210 );
	$colorList[] = imagecolorallocate( $image, 46, 175, 7 );
	$colorList[] = imagecolorallocate( $image, 231, 185, 3 );
	$colorList[] = imagecolorallocate( $image, 230, 16, 4 );
	$colorList[] = imagecolorallocate( $image, 199, 88, 35 );
	$colorList[] = imagecolorallocate( $image, 173, 114, 61 );
	$colorList[] = imagecolorallocate( $image, 55, 179, 179 );
	$colorList[] = imagecolorallocate( $image, 171, 50, 153 );
	$colorList[] = imagecolorallocate( $image, 254, 52, 138 );
	$colorList[] = imagecolorallocate( $image, 0, 0, 145 );
	$colorList[] = imagecolorallocate( $image, 0, 0, 113 );
	$colorList[] = imagecolorallocate( $image, 228, 118, 237 );
	$colorList[] = imagecolorallocate( $image, 158, 180, 35 );
	$colorList[] = imagecolorallocate( $image, 255, 36, 36 );
	$colorList[] = imagecolorallocate( $image, 255, 72, 72 );
	$colorList[] = imagecolorallocate( $image, 247, 179, 51 );
	$gray = imagecolorallocate( $image, 230, 230, 230 );
	imagefill( $image, 0, 0, $gray );
	$i = 0;
	for ( ;	$i < strlen( $authstr );	++$i	)
	{
		$colorRandom = mt_rand( 0, sizeof( $colorList ) - 1 );
		imagestring( $image, $size, $space * $i + $left, $top, substr( $authstr, $i, 1 ), $colorList[$colorRandom] );
	}
	$i = 0;
	for ( ;	$i < $disturb_num;	++$i	)
	{
		$colorRandom = mt_rand( 0, sizeof( $colorList ) - 1 );
		imagesetpixel( $image, rand( ) % 70, rand( ) % 10, $colorList[$colorRandom] );
	}
	header( "Content-type: image/PNG" );
	imagepng( $image );
	imagedestroy( $image );
}

session_save_path( "../sysdata" );
session_start( );
$_SESSION['sessionValid'] = mt_rand( 1000, 9999 );
creatvalidateimg( 41, 15, 10, 5, 30, "ValidateCode" );
echo " \r\n ";
?>
