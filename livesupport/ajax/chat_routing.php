<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/* Dev team: 615 */
	/*
	// status json route: -1 no request, 0 same op route, 1 request accepted, 2 new op route, 10 leave a message
	*/
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;

	if ( $action == "routing" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_itr.php" ) ;

		$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "ln" ) ;
		$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "ln" ) ;
		$c_routing = Util_Format_Sanatize( Util_Format_GetVar( "c_routing" ), "ln" ) ;
		$rtype = Util_Format_Sanatize( Util_Format_GetVar( "rtype" ), "ln" ) ;
		$rtime = Util_Format_Sanatize( Util_Format_GetVar( "rtime" ), "ln" ) ;
		$rloop = Util_Format_Sanatize( Util_Format_GetVar( "rloop" ), "ln" ) ;
		$loop = Util_Format_Sanatize( Util_Format_GetVar( "loop" ), "ln" ) ;

		$requestinfo = Chat_get_itr_RequestCesInfo( $dbh, $ces ) ;
		if ( !isset( $requestinfo["requestID"] ) )
			$json_data = "json_data = { \"status\": 10 };" ;
		else
		{
			if ( $requestinfo["status"] )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
				include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/put_itr.php" ) ;

				$opinfo = Ops_get_OpInfoByID( $dbh, $requestinfo["opID"] ) ;
				if ( !$requestinfo["initiated"] )
					Ops_put_itr_OpReqStat( $dbh, $requestinfo["deptID"], $opinfo["opID"], "taken", 1 ) ;

				$json_data = "json_data = { \"status\": 1, \"status_request\": $requestinfo[status], \"requestid\": $requestinfo[requestID], \"initiated\": $requestinfo[initiated], \"name\": \"$opinfo[name]\", \"rate\": $opinfo[rate], \"deptid\": $deptid, \"opid\": $opinfo[opID], \"email\": \"$opinfo[email]\", \"pic\": \"$opinfo[pic]\" };" ;
			}
			else
			{
				// vupdated is used for routing UNTIL chat is accepted then it is used
				// for visitor's callback updated time
				$rupdated = $requestinfo["vupdated"] + $rtime ;
				if ( time() <= $rupdated )
					$json_data = "json_data = { \"status\": 0 };" ;
				else
				{
					// no looping for simultaneous routing
					if ( $requestinfo["opID"] == 1111111111 )
					{
						include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/put_itr.php" ) ;
						include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove_itr.php" ) ;

						$sim_ops = Util_Format_ExplodeString( "-", $requestinfo["sim_ops"] ) ;
						$sim_ops_ = Util_Format_ExplodeString( "-", $requestinfo["sim_ops_"] ) ;
						for ( $c = 0; $c < count( $sim_ops ); ++$c )
						{
							$found = 0 ;
							for ( $c2 = 0; $c2 < count( $sim_ops_ ); ++$c2 )
							{
								if ( $sim_ops[$c] == $sim_ops_[$c2] )
									$found = 1 ;
							}
							if ( !$found )
								Ops_put_itr_OpReqStat( $dbh, $requestinfo["deptID"], $sim_ops[$c], "declined", 1 ) ;
						}

						// leave a message
						Ops_put_itr_OpReqStat( $dbh, $deptid, 0, "message", 1 ) ;
						Chat_remove_itr_RequestByCes( $dbh, $requestinfo["ces"] ) ;
						$json_data = "json_data = { \"status\": 10 };" ;
					}
					else
					{
						include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/update_itr.php" ) ;
						include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
						include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/put_itr.php" ) ;

						if ( $loop == 1 )
							Ops_put_itr_OpReqStat( $dbh, $deptid, $requestinfo["opID"], "declined", 1 ) ;
						$opinfo_next = Ops_get_NextRequestOp( $dbh, $deptid, $rtype, $requestinfo["rstring"] ) ;
						if ( isset( $opinfo_next["opID"] ) )
						{
							Chat_update_itr_RouteChat( $dbh, $requestinfo["requestID"], $requestinfo["ces"], $opinfo_next["opID"], $opinfo_next["sms"],  " $requestinfo[rstring] AND p_operators.opID <> $opinfo_next[opID] " ) ;

							if ( $opinfo_next["sms"] == 1 )
							{
								include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Email.php" ) ;
								include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions_itr.php" ) ;
								include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

								$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
								if ( $deptinfo["smtp"] )
								{
									$smtp_array = unserialize( Util_Functions_itr_Decrypt( $CONF["SALT"], $deptinfo["smtp"] ) ) ;

									$CONF["SMTP_HOST"] = $smtp_array["host"] ;
									$CONF["SMTP_LOGIN"] = $smtp_array["login"] ;
									$CONF["SMTP_PASS"] = $smtp_array["pass"] ;
									$CONF["SMTP_PORT"] = $smtp_array["port"] ;
								}

								$question = ( strlen( $requestinfo["question"] ) > 100 ) ? substr( $requestinfo["question"], 0, 100 ) . "..." : $requestinfo["question"] ;
								$question = preg_replace( "/<br>/", " ", $question ) ;
								Util_Email_SendEmail( $opinfo_next["name"], $opinfo_next["email"], $requestinfo["vname"], base64_decode( $opinfo_next["smsnum"] ), "Chat Request", $question, "sms" ) ;
							}

							// don't log trasfer chats on total stats of requests
							if ( ( $requestinfo["status"] != 2 ) && ( $loop == 1 ) )
							{
								if ( !$c_routing )
									Ops_put_itr_OpReqStat( $dbh, $deptid, $opinfo_next["opID"], "requests", 1 ) ;
								else
									Ops_put_itr_OpReqStat( $dbh, 0, $opinfo_next["opID"], "requests", 1 ) ;
							}
							$json_data = "json_data = { \"status\": 2 };" ;
						}
						else
						{
							if ( $loop < $rloop )
							{
								Chat_update_itr_ResetChat( $dbh, $requestinfo["requestID"], $ces ) ;
								$json_data = "json_data = { \"status\": 2, \"reset\": 1 };" ;
							}
							else
							{
								include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/put.php" ) ;
								include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove_itr.php" ) ;

								// on stats db the leave a message is not op specific, just use the current opID to track
								// requests that went to leave a messge
								Ops_put_itr_OpReqStat( $dbh, $deptid, 0, "message", 1 ) ;
								Chat_remove_itr_RequestByCes( $dbh, $requestinfo["ces"] ) ;
								$json_data = "json_data = { \"status\": 10 };" ;
							}
						}
					}
				}
			}
		}
	}

	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;

	$json_data = preg_replace( "/\r\n/", "", $json_data ) ;
	$json_data = preg_replace( "/\t/", "", $json_data ) ;
	print "$json_data" ;
	exit ;
?>