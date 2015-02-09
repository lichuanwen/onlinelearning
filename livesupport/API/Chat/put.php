<?php
	if ( defined( 'API_Chat_put' ) ) { return ; }
	define( 'API_Chat_put', true ) ;

	/****************************************************************/
	FUNCTION Chat_put_Request( &$dbh,
					$deptid,
					$opid,
					$status,
					$initiate,
					$op2op,
					$os,
					$browser,
					$ces,
					$resolution,
					$vname,
					$vemail,
					$ip,
					$agent,
					$onpage,
					$title,
					$question,
					$marketid,
					$refer,
					$custom,
					$auto_pop = 0,
					$sim_ops = "" )
	{
		if ( ( $deptid == "" ) || ( $opid == "" ) || ( $os == "" ) || ( $browser == "" )
			|| ( $ces == "" ) || ( $vname == "" ) || ( $ip == "" )
			|| ( $agent == "" ) || ( $question == "" ) )
			return false ;

		global $CONF ;
		global $VARS_SMS_BUFFER ;
		global $opinfo_next ;
		if ( !defined( 'API_Chat_get_itr' ) )
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_itr.php" ) ;
		if ( !defined( 'API_Util_Functions_itr' ) )
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions_itr.php" ) ;

		$now = ( isset( $opinfo_next["sms"] ) && ( $opinfo_next["sms"] == 1 ) ) ? time() + $VARS_SMS_BUFFER : time() ;
		$hostname = Util_Functions_itr_GetHostname( $ip ) ;
		$onpage = strip_tags( $onpage ) ;

		LIST( $deptid, $opid, $status, $initiate, $op2op, $os, $browser, $ces, $resolution, $vname, $vemail, $ip, $hostname, $agent, $onpage, $title, $question, $marketid, $refer, $custom, $auto_pop, $sim_ops ) = database_mysql_quote( $deptid, $opid, $status, $initiate, $op2op, $os, $browser, $ces, $resolution, $vname, $vemail, $ip, $hostname, $agent, $onpage, $title, $question, $marketid, $refer, $custom, $auto_pop, $sim_ops ) ;

		$requestinfo = Chat_get_itr_RequestCesInfo( $dbh, $ces ) ;
		if ( isset( $requestinfo["requestID"] ) )
		{
			if ( $requestinfo["initiated"] )
			{
				if ( !defined( 'API_Chat_update' ) )
					include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/update.php" ) ;

				Chat_update_RequestValue( $dbh, $requestinfo["requestID"], "status", 1 ) ;
				Chat_update_RequestValue( $dbh, $requestinfo["requestID"], "auto_pop", $auto_pop ) ;
			}

			return $requestinfo["requestID"] ;
		}
		else
		{
			if ( !defined( 'API_Chat_get' ) )
				include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get.php" ) ;

			$vupdated = time() ;
			$rstring = "AND p_operators.opID <> $opid" ;

			// todo: perhaps put total requests during activation of chat and not here
			$requests = Chat_get_IPTotalRequests( $dbh, $ip ) ;
			if ( !$requests )
				$requests = 1 ;

			$query = "INSERT INTO p_requests VALUES ( NULL, $now, 0, $now, $vupdated, $status, $auto_pop, $initiate, $deptid, $opid, $op2op, $marketid, $os, $browser, $requests, '$ces', '$resolution', '$vname', '$vemail', '$ip', '$hostname', '$sim_ops', '', '$agent', '$onpage', '$title', '$rstring', '$refer', '$custom', '$question' )" ;
			database_mysql_query( $dbh, $query ) ;

			if ( $dbh[ 'ok' ] )
			{
				if ( $initiate && !is_file( "$CONF[TYPE_IO_DIR]/$ip.txt" ) )
				{
					$fp = fopen( "$CONF[TYPE_IO_DIR]/$ip.txt", "a" ) ;
					fwrite( $fp, "initiate chat", strlen( "initiate chat" ) ) ;
					fclose( $fp ) ;
				}

				$id = database_mysql_insertid ( $dbh ) ;
				return $id ;
			}

			return false ;
		}
	}

	/****************************************************************/
	FUNCTION Chat_put_ReqLog( &$dbh,
					$requestid )
	{
		if ( $requestid == "" )
			return false ;

		LIST( $requestid ) = database_mysql_quote( $requestid ) ;

		$query = "INSERT INTO p_req_log ( ces, created, ended, status, initiated, deptID, opID, op2op, marketID, os, browser, resolution, vname, vemail, ip, hostname, sim_ops, agent, onpage, title, custom, question ) SELECT ces, created, 0, status, initiated, deptID, opID, op2op, marketID, os, browser, resolution, vname, vemail, ip, hostname, sim_ops, agent, onpage, title, custom, question FROM p_requests WHERE p_requests.requestID = $requestid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
			return true ;

		return false ;
	}

?>