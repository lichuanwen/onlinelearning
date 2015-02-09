<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/* Dev team: 615 */
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;

	if ( !isset( $_COOKIE["phplive_opID"] ) )
		$json_data = "json_data = { \"status\": -1 };" ;
	else if ( $action == "traffic" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/get.php" ) ;

		$dept_string = "" ;
		// todo: disabled for further testing [mod Sam: 60]
		/*
		$departments = Ops_get_OpDepts( $dbh, $_COOKIE["phplive_opID"] ) ;
		for ( $c = 0; $c < count( $departments ); ++$c )
		{
			$department = $departments[$c] ;
			$dept_string .= " OR deptID = $department[deptID]" ;
		}
		*/

		$traffics = Footprints_get_Footprints_U( $dbh, $dept_string ) ;
		$json_data = "json_data = { \"status\": 1, \"traffics\": [  " ;
		for ( $c = 0; $c < count( $traffics ); ++$c )
		{
			$traffic = $traffics[$c] ;
			$duration = $traffic["updated"] - $traffic["created"] ;
			if ( $duration < 60 )
				$duration = 60 ;
			$duration = Util_Format_Duration( $duration ) ;
			$os = $VARS_OS[$traffic["os"]] ;
			$browser = $VARS_BROWSER[$traffic["browser"]] ;
			$title = preg_replace( "/\"/", "&quot;", $traffic["title"] ) ;
			$onpage = preg_replace( "/hphp/i", "http", preg_replace( "/\"/", "&quot;", $traffic["onpage"] ) ) ;
			$refer_raw = preg_replace( "/hphp/i", "http", preg_replace( "/\"/", "&quot;", $traffic["refer"] ) ) ;
			$refer_snap = ( strlen( $refer_raw ) > 30 ) ? substr( $refer_raw, 0, 30 ) . "..." : $refer_raw ;
			$refer_snap = preg_replace( "/((http)|(https)):\/\/(www.)/", "", $refer_snap ) ;

			$t_footprints = $traffic["footprints"] ;
			$t_requests = $traffic["requests"] ;
			$t_initiates = $traffic["initiates"] ;

			$ip = $traffic["ip"] ;
			$hostname = $traffic["hostname"] ;

			$geo_country = ( $traffic["country"] ) ? $traffic["country"] : "unknown" ;
			$geo_region = ( $traffic["region"] ) ? utf8_encode( $traffic["region"] ) : "-" ;
			$geo_city = ( $traffic["city"] ) ? utf8_encode( $traffic["city"] ) : "-" ;
			$geo_latitude = ( $traffic["latitude"] ) ? $traffic["latitude"] : 28.613459424004414 ;
			$geo_longitude = ( $traffic["longitude"] ) ? $traffic["longitude"] : -40.4296875 ;

			$md5 = md5( $ip ) ;

			// override settings if spam IP
			if ( $ip && preg_match( "/$ip/", $VALS["CHAT_SPAM_IPS"] ) )
			{
				$ip = "<span class=info_error>$ip</span>" ;
				$hostname = "$hostname (spam)" ;
			}

			$json_data .= "{ \"md5\": \"$md5\", \"chatting\": $traffic[chatting], \"ip\": \"$ip\", \"hostname\": \"$hostname\", \"onpage\": \"$onpage\", \"title\": \"$title\", \"duration\": \"$duration\", \"os\": \"$os\", \"browser\": \"$browser\", \"resolution\": \"$traffic[resolution]\", \"marketid\": \"$traffic[marketID]\", \"refer_snap\": \"$refer_snap\", \"refer_raw\": \"$refer_raw\", \"t_footprints\": $t_footprints, \"t_requests\": $t_requests, \"t_initiates\": $t_initiates, \"country\": \"$geo_country\", \"region\": \"$geo_region\", \"city\": \"$geo_city\" }," ;
		}
		$json_data = substr_replace( $json_data, "", -1 ) ;
		$json_data .= "	] };" ;
	}
	else if ( $action == "fetch_ratings" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get.php" ) ;

		$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
		$flag = Util_Format_Sanatize( Util_Format_GetVar( "flag" ), "ln" ) ;

		$opinfo = Ops_get_OpInfoByID( $dbh, $_COOKIE["phplive_opID"] ) ;

		// auto logout if operator session does not exist
		if ( isset( $opinfo["ses"] ) && ( $opinfo["ses"] == $ses ) )
		{
			$m = date( "m", time() ) ;
			$d = date( "j", time() ) ;
			$y = date( "Y", time() ) ;
			$stat_start = mktime( 0, 0, 0, $m, $d, $y ) ;
			$stat_end = mktime( 23, 59, 59, $m, $d, $y ) ;

			$overall = Chat_get_OpOverallRatings( $dbh, $_COOKIE["phplive_opID"] ) ;

			// only get new stats if active chats so not to use up resources if chat activity is idle
			if ( $flag )
			{
				$chats_overall = Chat_get_OpOverallChats( $dbh, $_COOKIE["phplive_opID"] ) ;
				$chats_today = Chat_get_OpDayChats( $dbh, $_COOKIE["phplive_opID"], $stat_start, $stat_end ) ;
			}
			else
				$chats_overall = $chats_today = 0 ;

			$status = ( $opinfo["ses"] != $ses ) ? 0 : 1 ;
			$signal = $opinfo["signall"] ;
			if ( $signal )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
				Ops_update_OpValue( $dbh, $opinfo["opID"], "signall", 0 ) ;
			}

			$json_data = "json_data = { \"status\": 1, \"rating_overall\": \"$overall\", \"rating_recent\": \"$opinfo[rating]\", \"ces\": \"$opinfo[ces]\", \"chats_today\": $chats_today, \"chats_overall\": $chats_overall, \"status_op\": $status, \"signal\": $signal }; " ;
		}
		else
			$json_data = "json_data = { \"status\": 0, \"rating_overall\": \"\", \"rating_recent\": \"\", \"ces\": \"\", \"status_op\": 0, \"signal\": -1 }; " ;
	}
	else
		$json_data = "json_data = { \"status\": 0 };" ;

	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;

	$json_data = preg_replace( "/\r\n/", "", $json_data ) ;
	$json_data = preg_replace( "/\t/", "", $json_data ) ;
	print "$json_data" ;
	exit ;
?>
