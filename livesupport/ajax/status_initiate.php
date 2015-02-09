<?php
	Header( 'Access-Control-Allow-Origin: *' ) ;
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/* Dev team: 615 */
	include_once( "../web/config.php" ) ;
	include_once( "../API/Util_IP.php" ) ;

	$image_dir = realpath( "$CONF[DOCUMENT_ROOT]/pics/icons/pixels" ) ;
	$ip = Util_IP_GetIP() ;
	$image_path = "$image_dir/1x1.gif" ;

	// override previous images if initiate flag
	if ( is_file( "$CONF[TYPE_IO_DIR]/$ip.txt" ) )
	{
		$fsize = filesize( "$CONF[TYPE_IO_DIR]/$ip.txt" ) ;
		if ( $fsize > 5 ) { $image_path = "$image_dir/3x3.gif" ; }
		else { $image_path = "$image_dir/2x2.gif" ; }
	}

	Header( "Content-type: image/GIF" ) ;
	readfile( $image_path ) ;
?>