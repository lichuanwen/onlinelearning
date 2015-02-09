<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/* Dev team: 615 */
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$isop = Util_Format_Sanatize( Util_Format_GetVar( "isop" ), "n" ) ;
	$isop_ = Util_Format_Sanatize( Util_Format_GetVar( "isop_" ), "n" ) ;
	$isop__ = Util_Format_Sanatize( Util_Format_GetVar( "isop__" ), "n" ) ;
	$requestid = Util_Format_Sanatize( Util_Format_GetVar( "requestid" ), "ln" ) ;
	$wname = rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "wname" ), "v" ) ) ;
	$rname = rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "rname" ), "v" ) ) ;
	$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "ln" ) ;
	$salt = Util_Format_Sanatize( Util_Format_GetVar( "salt" ), "ln" ) ;
	$text = preg_replace( "/(p_br)/", "<br>", Util_Format_Sanatize( Util_Format_GetVar( "text" ), "" ) ) ;

	if ( ( md5( $CONF["SALT"] ) == $salt ) || isset( $_COOKIE["phplive_opID"] ) )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/Util.php" ) ;

		// override javascript timestamp
		$now = time() ;
		$text = preg_replace( "/<timestamp_(\d+)_((co)|(cv))>/", "<timestamp_".$now."_$2>", $text ) ;

		if ( ( $isop && $isop_ ) && ( $isop == $isop_ ) ) { $wid = $isop_ ; }
		else if ( $isop && $isop_ ) { $wid = $isop__ ; }
		else { $wid = $isop_ ; }
		$filename = $ces."-$wid" ;
		UtilChat_AppendToChatfile( "$ces.txt", $text ) ;
		UtilChat_AppendToChatfile( "$filename.text", $text ) ;
		UtilChat_WriteIsWriting( $ces, 0, $isop, $isop_, $isop__ ) ;
		$json_data = "json_data = { \"status\": 1 };" ;
	}
	else
		$json_data = "json_data = { \"status\": -1 };" ;
	
	$json_data = preg_replace( "/\r\n/", "", $json_data ) ;
	$json_data = preg_replace( "/\t/", "", $json_data ) ;
	print "$json_data" ;
	exit ;
?>