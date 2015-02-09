<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/* Dev team: 615 */
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;

	if ( !isset( $_COOKIE["phplive_opID"] ) )
		$json_data = "json_data = { \"status\": -1 };" ;
	else if ( $action == "accept" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_itr.php" ) ;

		$requestid = Util_Format_Sanatize( Util_Format_GetVar( "requestid" ), "ln" ) ;
		$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "ln" ) ;
		$wname = rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "wname" ), "v" ) ) ;
		$tooslow = 0 ; // to catch collisions on slow chat accept when other op already accept

		$requestinfo = Chat_get_itr_RequestCesInfo( $dbh, $ces ) ;
		if ( !isset( $requestinfo["status"] ) || ( $requestinfo["vupdated"] == 1 ) || ( ( $requestinfo["vupdated"] < ( time() - $VARS_EXPIRED_REQS ) ) && !$requestinfo["op2op"] ) || ( $requestinfo["status"] && ( $requestinfo["opID"] != $_COOKIE["phplive_opID"] ) ) )
			$tooslow = 1 ;
		else
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/update.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/Util.php" ) ;

			$opinfo = ( $requestinfo["opID"] != 1111111111 ) ? Ops_get_OpInfoByID( $dbh, $requestinfo["opID"] ) : Ops_get_OpInfoByID( $dbh, $_COOKIE["phplive_opID"] ) ;
			Ops_update_OpValue( $dbh, $_COOKIE["phplive_opID"], "lastrequest", time() ) ;
			Chat_update_AcceptChat( $dbh, $requestinfo["requestID"], $requestinfo["status"], $requestinfo["op2op"] ) ;

			$lang = $CONF["lang"] ;
			$deptinfo = Depts_get_DeptInfo( $dbh, $requestinfo["deptID"] ) ;
			if ( $deptinfo["lang"] )
				$lang = $deptinfo["lang"] ;
			include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/$lang.php" ) ;

			// if transferred, keep the same created time (status 2 is transferred)
			if ( $requestinfo["status"] != 2 )
			{
				if ( ( $_COOKIE["phplive_opID"] != $requestinfo["opID"] ) && ( $requestinfo["opID"] != 1111111111 ) )
					$tooslow = 1 ;
				else
				{
					if ( $requestinfo["opID"] == 1111111111 )
					{
						Chat_update_RequestValue( $dbh, $requestid, "opID", $_COOKIE["phplive_opID"] ) ;
						Chat_update_RequestLogValue( $dbh, $ces, "opID", $_COOKIE["phplive_opID"] ) ;
					}

					Chat_update_RequestValue( $dbh, $requestid, "created", time() ) ;
					Chat_update_RequestLogValue( $dbh, $ces, "created", time() ) ;
	
					$text = "<div class='ca'><i>".$requestinfo["question"]."</i></div><><div class='ca'><b>$opinfo[name]</b> ".$LANG["CHAT_NOTIFY_JOINED"]."</div>" ;
					UtilChat_AppendToChatfile( "$ces.txt", $text ) ;

					if ( $requestinfo["op2op"] && ( $requestinfo["status"] != 2 ) )
					{
						$filename = $ces."-".$requestinfo["op2op"];
						UtilChat_AppendToChatfile( "$filename.text", $text ) ;

						$filename = $ces."-".$requestinfo["opID"] ;
						UtilChat_AppendToChatfile( "$filename.text", $text ) ;
					}
				}
			}
			else
			{
				// reset the op2op as it was used for the original opID for transfer back
				Chat_update_RequestValue( $dbh, $requestid, "op2op", 0 ) ;

				$text = "<div class='ca'><b>$opinfo[name]</b> ".$LANG["CHAT_NOTIFY_JOINED"]."</div>" ;
				$filename = $ces."-0" ;
				UtilChat_AppendToChatfile( "$ces.txt", $text ) ;
				UtilChat_AppendToChatfile( "$filename.text", $text ) ;
			}

			Chat_update_RequestLogValue( $dbh, $ces, "status", 1 ) ;
		}

		if ( $tooslow )
			$json_data = "json_data = { \"status\": 1, \"tooslow\": 1 };" ;
		else
			$json_data = "json_data = { \"status\": 1, \"tooslow\": 0 };" ;
	}
	else if ( $action == "decline" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_itr.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/update.php" ) ;

		$requestid = Util_Format_Sanatize( Util_Format_GetVar( "requestid" ), "ln" ) ;
		$isop = Util_Format_Sanatize( Util_Format_GetVar( "isop" ), "ln" ) ;
		$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "ln" ) ;
		$op2op = Util_Format_Sanatize( Util_Format_GetVar( "op2op" ), "ln" ) ;
		$status = Util_Format_Sanatize( Util_Format_GetVar( "status" ), "ln" ) ;

		$requestinfo = Chat_get_itr_RequestCesInfo( $dbh, $ces ) ;
		if ( ( $op2op || ( $status == 2 ) ) && ( ( $requestinfo["opID"] == $isop ) || ( $requestinfo["op2op"] == $isop ) ) && ( $status == $requestinfo["status"] ) )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/Util.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove.php" ) ;

			if ( !$status )
			{
				$text = "<c615><disconnected><d4><div class='cl'>Operator was not available for op2op chat.  Chat session has ended.</div></c615>" ;
				$filename = $ces."-".$requestinfo["opID"] ;
				UtilChat_AppendToChatfile( "$ces.txt", $text ) ;
				UtilChat_AppendToChatfile( "$filename.text", $text ) ;
				Chat_remove_Request( $dbh, $requestinfo["requestID"] ) ;
			}
			else
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

				$department = Depts_get_DeptInfo( $dbh, $requestinfo["deptID"] ) ;
				if ( isset( $department["lang"] ) && $department["lang"] )
					$CONF["lang"] = $department["lang"] ;
				include_once( "../lang_packs/$CONF[lang].php" ) ;

				$text = "<c615><restart_router><d4><div class='cl'>".$LANG["CHAT_TRANSFER_TIMEOUT"]."</div></c615>" ;
				$filename = $ces."-0" ;
				UtilChat_AppendToChatfile( "$ces.txt", $text ) ;
				UtilChat_AppendToChatfile( "$filename.text", $text ) ;
				Chat_update_TransferChatOrig( $dbh, $requestinfo["op2op"], $ces ) ;
			}
		}
		else if ( $requestinfo["opID"] == $isop )
		{
			// not a transfer, a standard request
			Chat_update_RequestValue( $dbh, $requestid, "vupdated", 615 ) ;
		}
		else if ( $requestinfo["opID"] == 1111111111 )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/put_itr.php" ) ;

			$sim_ops = Util_Format_ExplodeString( "-", $requestinfo["sim_ops"] ) ;
			Ops_put_itr_OpReqStat( $dbh, $requestinfo["deptID"], $isop, "declined", 1 ) ;

			$sim_string = "$isop-" . $requestinfo["sim_ops_"] ;
			Chat_update_RequestValue( $dbh, $requestid, "sim_ops_", $sim_string ) ;
			$sim_ops_ = Util_Format_ExplodeString( "-", $sim_string ) ;

			if ( count( $sim_ops_ ) == count( $sim_ops ) )
				Chat_update_RequestValue( $dbh, $requestid, "vupdated", 615 ) ;
		}

		$json_data = "json_data = { \"status\": 1, \"ces\": \"$ces\" };" ;
	}
	else if ( $action == "deptops" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get.php" ) ;

		$departments = Depts_get_AllDepts( $dbh ) ;
		$json_data = "json_data = { \"status\": 1, \"departments\": [  " ;
		for ( $c = 0; $c < count( $departments ); ++$c )
		{
			$department = $departments[$c] ;
			$dept_ops = Depts_get_DeptOps( $dbh, $department["deptID"] ) ;

			$json_data .= "{ \"deptid\": $department[deptID], \"name\": \"$department[name]\", \"operators\": [  " ;
			for ( $c2 = 0; $c2 < count( $dept_ops ); ++$c2 )
			{
				$operator = $dept_ops[$c2] ;
				$requests = Chat_get_OpTotalRequests( $dbh, $operator["opID"] ) ;

				$json_data .= "{ \"opid\": $operator[opID], \"status\": $operator[status], \"name\": \"$operator[name]\", \"email\": \"$operator[email]\", \"requests\": $requests }," ;
			}
			$json_data = substr_replace( $json_data, "", -1 ) ;
			$json_data .= "	] }," ;
		}
		$json_data = substr_replace( $json_data, "", -1 ) ;
		$json_data .= "	] };" ;
	}
	else if ( $action == "footprints" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/get_itr.php" ) ;
	
		$ip = Util_Format_Sanatize( Util_Format_GetVar( "ip" ), "ln" ) ;

		$footprint_u_info = Footprints_get_itr_IPFootprints_U( $dbh, $ip ) ;
		if ( isset( $footprint_u_info["ip"] ) )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_itr.php" ) ;
			$requestinfo = Chat_get_itr_RequestIPInfo( $dbh, $ip, "" ) ;
			if ( !isset( $requestinfo["ip"] ) )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/update.php" ) ;
				Footprints_update_FootprintUniqueValue( $dbh, $ip, "chatting", 0 ) ;
			}
		}
		$footprints = Footprints_get_itr_IPFootprints( $dbh, $ip, 25 ) ;
		$json_data = "json_data = { \"status\": 1, \"footprints\": [  " ;
		for ( $c = 0; $c < count( $footprints ); ++$c )
		{
			$footprint = $footprints[$c] ;
			$title = preg_replace( "/\"/", "&quot;", $footprint["title"] ) ;
			$onpage = preg_replace( "/hphp/i", "http", preg_replace( "/\"/", "&quot;", $footprint["onpage"] ) ) ;

			$json_data .= "{ \"total\": $footprint[total], \"mdfive\": \"$footprint[mdfive]\", \"onpage\": \"$onpage\", \"title\": \"$title\" }," ;
		}
		$json_data = substr_replace( $json_data, "", -1 ) ;
		$json_data .= "	] };" ;
	}
	else if ( $action == "transcripts" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_ext.php" ) ;
	
		$ip = Util_Format_Sanatize( Util_Format_GetVar( "ip" ), "ln" ) ;

		$operators = Ops_get_AllOps( $dbh ) ;
		$operators_hash = Array() ;
		for ( $c = 0; $c < count( $operators ); ++$c )
		{
			$operator = $operators[$c] ;
			$operators_hash[$operator["opID"]] = $operator["name"] ;
		}

		$transcripts = Chat_ext_get_IPTranscripts( $dbh, $ip ) ;
		$json_data = "json_data = { \"status\": 1, \"transcripts\": [  " ;
		for ( $c = 0; $c < count( $transcripts ); ++$c )
		{
			$transcript = $transcripts[$c] ;

			if ( $transcript["opID"] )
			{
				$operator = ( $transcript["op2op"] ) ? $operators_hash[$transcript["op2op"]] : $operators_hash[$transcript["opID"]] ;
				$created = date( "M j (g:i:s a)", $transcript["created"] ) ;
				$duration = $transcript["ended"] - $transcript["created"] ;
				$duration = ( ( $duration - 60 ) < 1 ) ? " 1 min" : Util_Format_Duration( $duration ) ;
				$question = Util_Format_ConvertQuotes( nl2br( $transcript["question"] ) ) ;
				$vname = ( $transcript["op2op"] ) ? $operators_hash[$transcript["opID"]] : $transcript["vname"] ;

				$json_data .= "{ \"ces\": \"$transcript[ces]\", \"created\": \"$created\", \"operator\": \"$operator\", \"duration\": \"$duration\", \"initiated\": $transcript[initiated], \"op2op\": $transcript[op2op], \"vname\": \"$vname\", \"question\": \"$question\" }," ;
			}
		}
		$json_data = substr_replace( $json_data, "", -1 ) ;
		$json_data .= "	] };" ;
	}
	else if ( $action == "spam_check" )
	{
		$ip = Util_Format_Sanatize( Util_Format_GetVar( "ip" ), "ln" ) ;

		if ( preg_match( "/$ip/", $VALS["CHAT_SPAM_IPS"], $matches ) && isset( $matches[0] ) )
			$exist = 1 ;
		else
			$exist = 0 ;

		$json_data = "json_data = { \"status\": 1, \"exist\": $exist }; " ;
	}
	else if ( $action == "spam_block" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;

		$flag = Util_Format_Sanatize( Util_Format_GetVar( "flag" ), "ln" ) ;
		$ip = Util_Format_Sanatize( Util_Format_GetVar( "ip" ), "ln" ) ;

		if ( !preg_match( "/$ip/", $VALS["CHAT_SPAM_IPS"] ) && $flag )
		{
			$val = preg_replace( "/ +/", "", $VALS["CHAT_SPAM_IPS"] ) . "-$ip" ;
			$val = preg_replace( "/--/", "-", $val ) ;
			Util_Vals_WriteToFile( "CHAT_SPAM_IPS", $val ) ;
			$json_data = "json_data = { \"status\": 1 }; " ;
		}
		else if ( preg_match( "/$ip/", $VALS["CHAT_SPAM_IPS"] ) && !$flag )
		{
			$val = preg_replace( "/$ip/", "", preg_replace( "/ +/", "", $VALS["CHAT_SPAM_IPS"] ) ) ;
			Util_Vals_WriteToFile( "CHAT_SPAM_IPS", $val ) ;
			$json_data = "json_data = { \"status\": 1 }; " ;
		}
		else
			$json_data = "json_data = { \"status\": 1 }; " ;
	}
	else if ( $action == "transfer" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/update.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/Util.php" ) ;

		$requestid = Util_Format_Sanatize( Util_Format_GetVar( "requestid" ), "ln" ) ;
		$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "ln" ) ;
		$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "ln" ) ;
		$deptname = Util_Format_Sanatize( Util_Format_GetVar( "deptname" ), "ln" ) ;
		$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "ln" ) ;
		$opname = Util_Format_Sanatize( Util_Format_GetVar( "opname" ), "ln" ) ;
		$rname = rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "rname" ), "v" ) ) ;

		$lang = $CONF["lang"] ;
		$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
		if ( $deptinfo["lang"] )
			$lang = $deptinfo["lang"] ;
		include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/$lang.php" ) ;

		$text = "<div class='ca'>".$LANG["CHAT_TRANSFER"]." <b><top>$opname</top><!--opid:$opid--></b>.<div style='margin-top: 10px;'><div class='ctitle'>".$LANG["TXT_CONNECTING"]."</div></div></div>" ;

		Chat_update_TransferChat( $dbh, $ces, $_COOKIE["phplive_opID"], $deptid, $opid ) ;

		$filename = $ces."-0" ;
		UtilChat_AppendToChatfile( "$ces.txt", $text ) ;
		UtilChat_AppendToChatfile( "$filename.text", $text ) ;
		$json_data = "json_data = { \"status\": 1 };" ;
	}
	else if ( $action == "opop" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_IP.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/Util.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/put.php" ) ;

		$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "ln" ) ;
		$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "ln" ) ;
		$resolution = Util_Format_Sanatize( Util_Format_GetVar( "win_dim" ), "ln" ) ;

		$ces = Util_Security_GenSetupSes() ;
		$agent = isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : "&nbsp;" ;
		LIST( $os, $browser ) = Util_Format_GetOS( $agent ) ;
		$mobile = ( $os == 5 ) ? 1 : 0 ;

		$opinfo = Ops_get_OpInfoByID( $dbh, $_COOKIE["phplive_opID"] ) ;
		$opinfo_ = Ops_get_OpInfoByID( $dbh, $opid ) ;

		if ( isset( $opinfo["opID"] ) )
		{
			$opinfo_next = $opinfo_ ; // set it to a variable that is recognized for SMS buffer
			if ( $requestid = Chat_put_Request( $dbh, $deptid, $opid, 0, 0, $_COOKIE["phplive_opID"], $os, $browser, $ces, $resolution, $opinfo["name"], $opinfo["email"], Util_IP_GetIP(), $agent, "", "", "Operator 2 Operator Chat", 0, "", "" ) )
			{
				// create empty file to signal a chat session
				touch( "$CONF[CHAT_IO_DIR]/$ces.txt" ) ;

				if ( $opinfo_["sms"] == 1 )
				{
					include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions_itr.php" ) ;
					include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Email.php" ) ;
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

					$question = "Operator-to-operator chat request from $opinfo[name]" ;
					$error = Util_Email_SendEmail( $opinfo["name"], $opinfo["email"], $opinfo_["name"], base64_decode( $opinfo_["smsnum"] ), "Chat Request", $question, "sms" ) ;
				}

				Chat_put_ReqLog( $dbh, $requestid ) ;
				$json_data = "json_data = { \"status\": 1, \"ces\": \"$ces\" };" ;
			}
			else
				$json_data = "json_data = { \"status\": -1 };" ;
		}
		else
			$json_data = "json_data = { \"status\": 0 };" ;
	}
	else if ( $action == "cans" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Canned/get.php" ) ;

		$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "ln" ) ;

		$cans = Canned_get_OpCanned( $dbh, $_COOKIE["phplive_opID"], $deptid ) ;
		$json_data = "json_data = { \"status\": 1, \"cans\": [  " ;
		for ( $c = 0; $c < count( $cans ); ++$c )
		{
			$can = $cans[$c] ;
			$title = Util_Format_ConvertQuotes( $can["title"] ) ;
			$message = Util_Format_ConvertQuotes( preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", $can["message"] ) ) ;

			$json_data .= "{ \"deptid\": $can[deptID], \"title\": \"$title\", \"message\": \"$message\" }," ;
		}
		$json_data = substr_replace( $json_data, "", -1 ) ;
		$json_data .= "	] };" ;
	}
	else if ( $action == "update_status" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get_itr.php" ) ;
	
		$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "ln" ) ;
		$status = Util_Format_Sanatize( Util_Format_GetVar( "status" ), "ln" ) ;

		Ops_update_putOpStatus( $dbh, $opid, $status ) ;
		Ops_update_OpValue( $dbh, $opid, "status", $status ) ;
		Ops_update_OpValue( $dbh, $opid, "lastactive", time() ) ;

		if ( !$status && !Ops_get_itr_AnyOpsOnline( $dbh, 0 ) )
		{
			$initiate_dir = $CONF["TYPE_IO_DIR"] ; 
			$dh = dir( $initiate_dir ) ; 
			while( $file = $dh->read() ) {
				if ( $file != "." && $file != ".." )
					unlink( "$initiate_dir/$file" ) ; 
			} 
			$dh->close() ;
		}

		$json_data = "json_data = { \"status\": 1 }; " ;
	}
	else if ( $action == "markets" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Marketing/get.php" ) ;

		$markets = Marketing_get_AllMarketing( $dbh ) ;
		$json_data = "json_data = { \"status\": 1, \"markets\": [  " ;
		for ( $c = 0; $c < count( $markets ); ++$c )
		{
			$market = $markets[$c] ;

			$json_data .= "{ \"marketid\": $market[marketID], \"name\": \"$market[name]\", \"color\": \"$market[color]\" }," ;
		}
		$json_data = substr_replace( $json_data, "", -1 ) ;
		$json_data .= "	] };" ;
	}
	else if ( $action == "logout" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;

		if ( isset( $_COOKIE["phplive_opID"] ) )
		{
			Ops_update_putOpStatus( $dbh,$_COOKIE["phplive_opID"], 0 ) ;
			Ops_update_OpValue( $dbh, $_COOKIE["phplive_opID"], "status", 0 ) ;
			setcookie( "phplive_opID", FALSE ) ;
			$json_data = "json_data = { \"status\": 1 };" ;
		}
		else
			$json_data = "json_data = { \"status\": 0 };" ;
	}
	else if ( $action == "initiate" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_itr.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/put.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/Util.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/get_itr.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/IPs/put.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

		$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "ln" ) ;
		$ip = Util_Format_Sanatize( Util_Format_GetVar( "ip" ), "ln" ) ;
		$question = Util_Format_ConvertQuotes( rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "question" ), "htmltags" ) ) ) ;

		if ( $question && $deptid && $ip )
		{
			$ces = Util_Security_GenSetupSes() ;
			$footprintinfo = Footprints_get_itr_IPFootprints_U( $dbh, $ip ) ;

			$opinfo = Ops_get_OpInfoByID( $dbh, $_COOKIE["phplive_opID"] ) ;

			if ( isset( $opinfo["opID"] ) && isset( $footprintinfo["ip"] ) )
			{
				$requestinfo = Chat_get_itr_RequestIPInfo( $dbh, $footprintinfo["ip"], 1 ) ;

				if ( !isset( $requestinfo["requestID"] ) )
				{
					if ( $requestid = Chat_put_Request( $dbh, $deptid, $opinfo["opID"], 0, 1, 0, $footprintinfo["os"], $footprintinfo["browser"], $ces, $footprintinfo["resolution"], "Visitor", "null", $ip, "&nbsp;", $footprintinfo["onpage"], $footprintinfo["title"], $question, 0, $footprintinfo["refer"], "" ) )
					{
						include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/put_itr.php" ) ;
	
						$lang = $CONF["lang"] ;
						$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
						if ( $deptinfo["lang"] )
							$lang = $deptinfo["lang"] ;
						include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/$lang.php" ) ;

						IPs_put_IP( $dbh, $ip, $deptid, 0, 0, 1, 1, 0, 0, time() ) ;
						Ops_put_itr_OpReqStat( $dbh, $deptid, $opinfo["opID"], "initiated", 1 ) ;
						Chat_put_ReqLog( $dbh, $requestid ) ;
						UtilChat_AppendToChatfile( "$ces.txt", "<div class='ca'><b>".$LANG["CHAT_WELCOME"]."</b></div><div class='co'><b>$opinfo[name]<timestamp_".time()."_co>:</b> $question</div>" ) ;

						$json_data = "json_data = { \"status\": 1, \"ces\": \"$ces\" };" ;
					}
					else
						$json_data = "json_data = { \"status\": 0, \"error\": \"Could not initiate: $dbh[error]\" };" ;
				}
				else
					$json_data = "json_data = { \"status\": 0, \"error\": \"Initiate chat session already in progress with the visitor.\" };" ;
			}
			else
				$json_data = "json_data = { \"status\": 0, \"error\": \"Visitor has left the website.  Request was not processed.\" };" ;
		}
		else
			$json_data = "json_data = { \"status\": 0, \"error\": \"Blank initiate message or department is invalid.\" };" ;
	}
	else if ( $action == "requestinfo" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_itr.php" ) ;
	
		$ip = Util_Format_Sanatize( Util_Format_GetVar( "ip" ), "ln" ) ;

		$requestinfo = Chat_get_itr_RequestIPInfo( $dbh, $ip, 0 ) ;
		$total_trans = Chat_get_TotalIPTranscripts( $dbh, $ip ) ;

		if ( isset( $requestinfo["requestID"] ) )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;

			$opinfo = Ops_get_OpInfoByID( $dbh, $requestinfo["opID"] ) ;
			$json_data = "json_data = { \"status\": 1, \"name\": \"$opinfo[name]\", \"total_trans\": \"$total_trans\" }; " ;
		}
		else
			$json_data = "json_data = { \"status\": 0, \"total_trans\": \"$total_trans\" }; " ;
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