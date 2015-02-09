<?php
	Header( 'Access-Control-Allow-Origin: *' ) ;
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/* Dev team: 615 */
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_IP.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get_itr.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update_itr.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/remove.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/IPs/remove.php" ) ;

	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "d" ), "ln" ) ;
	$image_dir = "$CONF[DOCUMENT_ROOT]/web" ; $image_path = $image_type = "" ;

	$ip = Util_IP_GetIP() ;
	$agent = isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : "&nbsp;" ;
	LIST( $os, $browser ) = Util_Format_GetOS( $agent ) ;
	$mobile = ( $os == 5 ) ? 1 : 0 ;

	$now = time() ;
	$country = $region = $city = "" ; $latitude = $longitude = 0 ;
	if ( $geoip )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/IPs/Util.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/GeoIP/get.php" ) ;

		LIST( $ip_num, $network ) = UtilIPs_IP2Long( $ip ) ;
		$geoinfo = GeoIP_get_GeoInfo( $dbh, $ip_num, $network ) ;
		if ( isset( $geoinfo["latitude"] ) )
		{
			$country = $geoinfo["country"] ;
			$region = $geoinfo["region"] ;
			$city = $geoinfo["city"] ;
			$latitude = $geoinfo["latitude"] ;
			$longitude = $geoinfo["longitude"] ;
		}
	}

	Ops_update_itr_IdleOps( $dbh ) ;
	if ( $ip && preg_match( "/$ip/", $VALS["CHAT_SPAM_IPS"] ) )
		$total_ops = 0 ;
	else
		$total_ops = Ops_get_itr_AnyOpsOnline( $dbh, $deptid ) ;

	///////////////////////////
	// auto cleaning of DB 
	Footprints_remove_Expired_U( $dbh ) ;
	Footprints_remove_Expired_Footprints( $dbh ) ;
	IPs_remove_Expired_IPs( $dbh ) ;
	///////////////////////////

	database_mysql_close( $dbh ) ;

	$file_name = "online_$deptid.info" ;
	if ( $total_ops )
	{
		$prefix = "icon_online" ;
		if ( !is_file( "$CONF[CHAT_IO_DIR]/$file_name" ) )
			touch( "$CONF[CHAT_IO_DIR]/$file_name" ) ;
	}
	else
	{
		$prefix = "icon_offline" ;
		if ( is_file( "$CONF[CHAT_IO_DIR]/$file_name" ) )
			unlink( "$CONF[CHAT_IO_DIR]/$file_name" ) ;
	}

	$offline = ( isset( $VALS['OFFLINE'] ) ) ? unserialize( $VALS['OFFLINE'] ) : Array() ;
	if ( !$total_ops && isset( $offline[$deptid] ) && ( $offline[$deptid] == "hide" ) )
	{
		$image_type = "GIF" ;
		$image_path = "$CONF[DOCUMENT_ROOT]/pics/space.gif" ;
	}
	else if ( is_file( realpath( "$image_dir/$prefix"."_$deptid.GIF" ) ) )
	{
		$image_type = "GIF" ;
		$image_path = "$image_dir/$prefix"."_$deptid.GIF" ;
	}
	else if ( is_file( realpath( "$image_dir/$prefix"."_$deptid.JPEG" ) ) )
	{
		$image_type = "JPEG" ;
		$image_path = "$image_dir/$prefix"."_$deptid.JPEG";
	}
	else if ( is_file( realpath( "$image_dir/$prefix"."_$deptid.PNG" ) ) )
	{
		$image_type = "PNG" ;
		$image_path = "$image_dir/$prefix"."_$deptid.PNG" ;
	}
	else if ( is_file( realpath( "$image_dir/$CONF[$prefix]" ) ) && $CONF[$prefix] )
	{
		$image_type = preg_replace( "/(.*?)\./", "", $CONF[$prefix] ) ;
		$image_path = "$image_dir/$CONF[$prefix]" ;
	}
	else
	{
		$image_type = "GIF" ;
		$image_path = "$CONF[DOCUMENT_ROOT]/pics/icons/$prefix.gif" ;
	}

	Header( "Content-type: image/GIF" ) ;
	readfile( $image_path ) ;
?>