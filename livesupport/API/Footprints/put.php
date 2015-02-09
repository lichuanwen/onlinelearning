<?php
	if ( defined( 'API_Footprints_put' ) ) { return ; }
	define( 'API_Footprints_put', true ) ;

	/****************************************************************/
	FUNCTION Footprints_put_Print_U( &$dbh,
					$deptid,
					$os,
					$browser,
					$footprints,
					$requests,
					$initiates,
					$resolution,
					$ip,
					$onpage,
					$title,
					$marketid,
					$refer,
					$country = "",
					$region = "",
					$city = "",
					$latitude = 0,
					$longitude = 0 )
	{
		if ( ( $deptid == "" ) || ( $os == "" ) || ( $browser == "" )
			|| ( $footprints == "" ) || ( $ip == "" ) || ( $onpage == "" ) )
			return false ;

		global $CONF ;
		if ( !defined( 'API_Util_Functions_itr' ) )
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions_itr.php" ) ;

		$now = time() ; // todo: set own md5 instead of using ip
		$hostname = Util_Functions_itr_GetHostname( $ip ) ;
		$agent = isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : "&nbsp;" ;

		LIST( $deptid, $os, $browser, $footprints, $requests, $initiates, $resolution, $ip, $hostname, $agent, $onpage, $title, $marketid, $refer, $country, $region, $city, $latitude, $longitude ) = database_mysql_quote( $deptid, $os, $browser, $footprints, $requests, $initiates, $resolution, $ip, $hostname, $agent, $onpage, $title, $marketid, $refer, $country, $region, $city, $latitude, $longitude ) ;

		$footprint_string = ( $footprints > 1 ) ? ", footprints = $footprints, requests = $requests, initiates = $initiates " : "" ;

		$query = "INSERT INTO p_footprints_u VALUES ( $now, $now, $deptid, $marketid, 0, $os, $browser, $footprints, $requests, $initiates, '$resolution', '$ip', '$hostname', '$agent', '$onpage', '$title', '$refer', '$country', '$region', '$city', $latitude, $longitude ) ON DUPLICATE KEY UPDATE updated = $now, onpage = '$onpage', title = '$title' $footprint_string" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
			return database_mysql_nresults( $dbh ) ;
		return 0 ;
	}

	/****************************************************************/
	FUNCTION Footprints_put_Refer( &$dbh,
					$ip,
					$marketid,
					$refer )
	{
		if ( $ip == "" )
			return false ;

		$now = time() ;
		$today = mktime( 0, 0, 1, date( "m", time() ), date( "j", time() ), date( "Y", time() ) ) ;
		$url_mdfive = md5( $refer ) ;

		LIST( $ip, $marketid, $url_mdfive, $refer ) = database_mysql_quote( $ip, $marketid, $url_mdfive, $refer ) ;

		$query = "INSERT INTO p_refer VALUES ( '$ip', $now, $marketid, '$url_mdfive', '$refer' )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$query = "SELECT * FROM p_referstats WHERE sdate = $today AND mdfive = '$url_mdfive'" ;
			database_mysql_query( $dbh, $query ) ;

			if ( $dbh[ 'ok' ] )
			{
				$data = database_mysql_fetchrow( $dbh ) ;

				if ( isset( $data["sdate"] ) )
				{
					$total = $data["total"] + 1 ;
					$query = "UPDATE p_referstats SET total = $total WHERE sdate = $today AND mdfive = '$url_mdfive'" ;
				}
				else
					$query = "INSERT INTO p_referstats VALUES ( $today, '$url_mdfive', 1, '$refer' )" ;

				database_mysql_query( $dbh, $query ) ;

				return true ;
			}
		}

		return false ;
	}

?>