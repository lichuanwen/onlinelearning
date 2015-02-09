<?php
	Header( 'Access-Control-Allow-Origin: *' ) ;
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/* Dev team: 615 */
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_IP.php" ) ;

	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "ln" ) ;
	$onpage = rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "onpage" ), "url" ) ) ;
	$title = rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "title" ), "title" ) ) ;
	$refer = rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "r" ), "url" ) ) ;
	$resolution = Util_Format_Sanatize( Util_Format_GetVar( "resolution" ), "ln" ) ;
	$c = Util_Format_Sanatize( Util_Format_GetVar( "c" ), "ln" ) ;
	$image_dir = realpath( "$CONF[DOCUMENT_ROOT]/pics/icons/pixels" ) ; $image_path = "$image_dir/4x4.gif" ;

	$ip = Util_IP_GetIP() ;
	$pi = $marketid = $skey = $excluded = 0 ;
	if ( preg_match( "/$ip/", $VALS["TRAFFIC_EXCLUDE_IPS"] ) ) { $excluded = 1 ; }
	preg_match( "/plk(=|%3D)(.*)-m/", $onpage, $matches ) ;
	if ( isset( $matches[2] ) ) { LIST( $pi, $marketid, $skey ) = explode( "-", $matches[2] ) ; }
	if ( !isset( $CONF["foot_log"] ) ) { $CONF["foot_log"] = "on" ; }

	$agent = isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : "&nbsp;" ;
	LIST( $os, $browser ) = Util_Format_GetOS( $agent ) ;
	$mobile = ( $os == 5 ) ? 1 : 0 ;

	if ( preg_match( "/(statichtmlapp.com)/", $onpage ) && !$title ) { $title = "Facebook Page" ; }
	else if ( !$title ) { $title = "- no title -" ; }

	if ( $excluded ) { $image_path = "$image_dir/4x4.gif" ; }
	else
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/put.php" ) ;

		$now = time() ;
		$country = $region = $city = "" ; $latitude = $longitude = 0 ;
		if ( $geoip && !$c )
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
		if ( $onpage && !$c )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/IPs/get.php" ) ;
			$ipinfo = IPs_get_IPInfo( $dbh, $ip ) ;
			$footprints = isset( $ipinfo["t_footprints"] ) ? $ipinfo["t_footprints"] : 1 ;
			$requests = isset( $ipinfo["t_requests"] ) ? $ipinfo["t_requests"] : 0 ;
			$initiates = isset( $ipinfo["t_initiate"] ) ? $ipinfo["t_initiate"] : 0 ;
			if ( $pi && $marketid && $skey )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Marketing/get.php" ) ;
				include_once( "$CONF[DOCUMENT_ROOT]/API/Marketing/update.php" ) ;
				include_once( "$CONF[DOCUMENT_ROOT]/API/Marketing/put.php" ) ;

				$marketinfo = Marketing_get_MarketingByID( $dbh, $marketid ) ;

				if ( $marketinfo["skey"] == $skey )
				{
					$clickinfo = Marketing_get_ClickInfo( $dbh, $marketid ) ;
					if ( isset( $clickinfo["marketID"] ) )
						Marketing_update_MarketClickValue( $dbh, $marketid, "clicks", $clickinfo["clicks"]+1 ) ;
					else
						Marketing_put_Click( $dbh, $marketid, 1 ) ;
				}
			}
		}
		else { $footprints = 1 ; $requests = $initiates = 0 ; }

		$nresults = Footprints_put_Print_U( $dbh, $deptid, $os, $browser, $footprints, $requests, $initiates, $resolution, $ip, $onpage, $title, $marketid, $refer, $country, $region, $city, $latitude, $longitude ) ;
		if ( $c > $VARS_JS_FOOTPRINT_MAX_CYCLE )
			$image_path = "$image_dir/4x4.gif" ;
		else if ( !$c )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/IPs/put.php" ) ;
	
			if ( $CONF["foot_log"] == "on" )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/put_itr.php" ) ;
				Footprints_put_itr_Print( $dbh, $deptid, $os, $browser, $ip, $onpage, $title ) ;
			}

			// if $refer exists always put it into DB on first visit for logging
			if ( $refer )
			{
				if ( !isset( $_COOKIE["phplive_refer"] ) ) { setcookie( "phplive_refer", $refer, $now+60*60*24*180 ) ; }
				Footprints_put_Refer( $dbh, $ip, $marketid, $refer ) ;
			}
			IPs_put_IP( $dbh, $ip, $deptid, 1, 0, 0, 0, 1, 1, $now, $onpage ) ;

			///////////////////////////
			// auto cleaning of DB
			if ( $now % 2 )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/remove.php" ) ;
				Footprints_remove_Expired_Footprints( $dbh ) ;
			}
			else
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/IPs/remove.php" ) ;
				IPs_remove_Expired_IPs( $dbh ) ;
			}
			///////////////////////////

			$image_path = "$image_dir/1x1.gif" ;
		}
		else
		{
			// repeat calling, just update footprint unique
			include_once( "$CONF[DOCUMENT_ROOT]/API/IPs/put.php" ) ;

			IPs_put_IP( $dbh, $ip, $deptid, 0, 0, 0, 0, 1, 0, 0, $onpage ) ;
			$image_path = "$image_dir/1x1.gif" ;
		}
	}

	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;

	Header( "Content-type: image/GIF" ) ;
	readfile( $image_path ) ;
?>