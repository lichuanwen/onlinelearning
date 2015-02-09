<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/* Dev team: 615 */
	/*
	// status DB request: -1 ended by action taken, 0 waiting pick-up, 1 picked up, 2 transfer
	*/
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;

	if ( $action == "requests" )
	{
		if ( !isset( $_COOKIE["phplive_opID"] ) || !$_COOKIE["phplive_opID"] )
			$json_data = "json_data = { \"status\": -1 };" ;
		else
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/Util.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/get_itr.php" ) ;

			$now = time() ;
			$opid = $_COOKIE["phplive_opID"] ;

			$prev_status = Util_Format_Sanatize( Util_Format_GetVar( "prev_status" ), "ln" ) ;
			$c_requesting = Util_Format_Sanatize( Util_Format_GetVar( "c_requesting" ), "ln" ) ;
			$traffic = Util_Format_Sanatize( Util_Format_GetVar( "traffic" ), "ln" ) ;

			// do the clean on old requests, transcript files, operator status, etc every xth call
			if ( $c_requesting % $VARS_CYCLE_CLEAN == 0 )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove_itr.php" ) ;
				include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/remove.php" ) ;
				include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update_itr.php" ) ;

				$vars = Util_Format_Get_Vars( $dbh ) ;
				if ( $vars["ts_clean"] <= ( $now - $VARS_CYCLE_CLEAN ) )
				{
					Footprints_remove_Expired_U( $dbh ) ;
					Chat_remove_itr_ExpiredOp2OpRequests( $dbh ) ;
					Chat_remove_itr_OldRequests( $dbh ) ;
					Ops_update_itr_IdleOps( $dbh ) ;
					Util_Format_Update_TimeStamp( $dbh, "clean", $now ) ;
				}
			}
			else if ( $c_requesting % $VARS_CYCLE_RESET == 0 )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;

				Ops_update_OpValue( $dbh, $opid, "lastactive", time() ) ;
				$query = "UPDATE p_requests SET updated = $now WHERE ( opID = $opid OR op2op = $opid OR opID = 1111111111 ) AND ( status = 0 OR status = 1 OR status = 2 )" ;
				database_mysql_query( $dbh, $query ) ;
			}
			/********** END CLEAN UP AND OTHER ACTIONS ***********/

			$total_traffics = ( $traffic ) ? Footprints_get_itr_TotalFootprints_U( $dbh ) : 0 ;

			$query = "SELECT * FROM p_requests WHERE ( opID = $opid OR op2op = $opid OR opID = 1111111111 ) AND ( status >= 0 AND status <= 2 ) ORDER BY created ASC" ;
			database_mysql_query( $dbh, $query ) ;

			$requests_temp = Array() ;
			if ( $dbh[ 'ok' ] )
			{
				while ( $data = database_mysql_fetchrow( $dbh ) )
					$requests_temp[] = $data ;
			}

			// filter out various conditions
			$requests = Array() ;
			for ( $c = 0; $c < count( $requests_temp ); ++$c )
			{
				$data = $requests_temp[$c] ;
				if ( ( $data["status"] == 2 ) && ( $data["op2op"] == $opid ) )
				{
					// 30 seconds then transfer back to original operator
					if ( $data["tupdated"] < ( time() - $VARS_TRANSFER_BACK ) )
					{
						include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

						$lang = $CONF["lang"] ;
						$deptinfo = Depts_get_DeptInfo( $dbh, $data["deptID"] ) ;
						if ( $deptinfo["lang"] )
							$lang = $deptinfo["lang"] ;
						include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/$lang.php" ) ;

						$text = "<c615><restart_router><d4><div class='ca'>".$LANG["CHAT_TRANSFER_TIMEOUT"]."</div></c615>" ;
						$filename = $data["ces"]."-0" ;
						UtilChat_AppendToChatfile( "$data[ces].txt", $text ) ;
						UtilChat_AppendToChatfile( "$filename.text", $text ) ;
						$query = "UPDATE p_requests SET tupdated = 0, status = 0, opID = $data[op2op], op2op = 0 WHERE ces = '$data[ces]'" ;
						database_mysql_query( $dbh, $query ) ;
					}
				}
				else
				{
					// sim ops filter for declined
					if ( !preg_match( "/(^|-)($opid-)/", $data["sim_ops_"] ) )
						$requests[] = $data ;
				}
			}

			$json_data = "json_data = { \"status\": 1, \"traffics\": $total_traffics, \"requests\": [  " ;
			for ( $c = 0; $c < count( $requests ); ++$c )
			{
				$request = $requests[$c] ;

				$os = $VARS_OS[$request["os"]] ;
				$browser = $VARS_BROWSER[$request["browser"]] ;
				$title = preg_replace( "/\"/", "&quot;", $request["title"] ) ;
				$question = preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", preg_replace( "/\"/", "&quot;", $request["question"] ) ) ;
				$onpage = preg_replace( "/hphp/i", "http", $request["onpage"] ) ;
				$refer_raw = preg_replace( "/hphp/i", "http", $request["refer"] ) ;
				$refer_snap = ( strlen( $refer_raw ) > 50 ) ? substr( $refer_raw, 0, 45 ) . "..." : $refer_raw ;
				$custom = $request["custom"] ;

				// if status is 2 then it's a transfer call... keep original visitor name
				if ( ( $request["status"] != 2 ) && $request["op2op"] )
				{
					// dynamically fill name and email according to the requesting operator
					include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;

					if ( $opid == $request["op2op"] )
						$opinfo = Ops_get_OpInfoByID( $dbh, $request["opID"] ) ;
					else
						$opinfo = Ops_get_OpInfoByID( $dbh, $request["op2op"] ) ;
					$vname = $opinfo["name"] ; $vemail = $opinfo["email"] ;
				}
				else
					$vname = $request["vname"] ; $vemail = $request["vemail"] ;

				$json_data .= "{ \"requestid\": $request[requestID], \"ces\": \"$request[ces]\", \"created\": \"$request[created]\", \"deptid\": $request[deptID], \"opid\": $request[opID], \"op2op\": $request[op2op], \"vname\": \"$vname\", \"status\": $request[status], \"auto_pop\": $request[auto_pop], \"initiated\": $request[initiated], \"os\": \"$os\", \"browser\": \"$browser\", \"requests\": \"$request[requests]\", \"resolution\": \"$request[resolution]\", \"vemail\": \"$vemail\", \"ip\": \"$request[ip]\", \"hostname\": \"$request[hostname]\", \"agent\": \"$request[agent]\", \"onpage\": \"$onpage\", \"title\": \"$title\", \"question\": \"$question\", \"marketid\": \"$request[marketID]\", \"refer_raw\": \"$refer_raw\", \"refer_snap\": \"$refer_snap\", \"custom\": \"$custom\" }," ;
			}
			$json_data = substr_replace( $json_data, "", -1 ) ;
			$json_data .= "	] };" ;
		}
	}

	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;

	$json_data = preg_replace( "/\r\n/", "", $json_data ) ;
	$json_data = preg_replace( "/\t/", "", $json_data ) ;
	print "$json_data" ;
	exit ;
?>