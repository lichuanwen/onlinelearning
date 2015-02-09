<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/* Dev team: 615 */
	/****************************************/
	// STANDARD header for Setup
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;
	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	if ( !$setupinfo = Util_Security_AuthSetup( $dbh, $ses ) ){ $json_data = "json_data = { \"status\": 0, \"error\": \"Authentication error.\" };" ; exit ; }
	// STANDARD header end
	/****************************************/

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "ln" ) ;
	$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "ln" ) ;

	if ( $action == "moveup" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;

		if ( Ops_get_IsOpInDept( $dbh, $opid, $deptid ) || !$opid )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
			
			if ( $deptid )
				Ops_update_OpDeptMoveUp( $dbh, $opid, $deptid ) ;
			$dept_ops = Depts_get_DeptOps( $dbh, $deptid ) ;

			$json_data = "json_data = { \"status\": 1, \"ops\": [ " ;
			for ( $c = 0; $c < count( $dept_ops ); ++$c )
			{
				$dept_op = $dept_ops[$c] ;
				$td_class = "td_clear" ;
				if ( $c % 2 ) { $td_class = "td_tan" ; }
				
				$json_data .= "{ \"name\": \"$dept_op[name]\", \"opid\": $dept_op[opID], \"display\": $dept_op[display], \"td_class\": \"$td_class\" }," ;
			}

			$json_data = substr_replace( $json_data, "", -1 ) ;
			$json_data .= "	] };" ;
		}
		else
			$json_data = "json_data = { \"status\": 0 };" ;
	}
	else if ( $action == "op_dept_remove" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/remove.php" ) ;

		Ops_remove_OpDept( $dbh, $opid, $deptid ) ;
		$json_data = "json_data = { \"status\": 1 };" ;
	}
	else if ( $action == "footprints" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/get_ext.php" ) ;

		$sdate_start = Util_Format_Sanatize( Util_Format_GetVar( "sdate" ), "ln" ) ;

		$today = mktime( 0, 0, 1, date( "m", time() ), date( "j", time() ), date( "Y", time() ) ) ;
		$today_end = mktime( 23, 59, 59, date( "m", time() ), date( "j", time() ), date( "Y", time() ) ) ; // 2sec buffer
		$sdate_end = mktime( 0, 0, 1, date( "m", $sdate_start ), date( "j", $sdate_start )+1, date( "Y", $sdate_start ) ) ;

		if ( !$sdate_start )
		{
			$month_start = Util_Format_Sanatize( Util_Format_GetVar( "start" ), "ln" ) ;
			$month_end = Util_Format_Sanatize( Util_Format_GetVar( "end" ), "ln" ) ;

			$footprints_hist = Footprints_get_FootStatsData( $dbh, $month_start, $month_end ) ;
			$footprints = $footprints_pre = Array() ;
			foreach( $footprints_hist as $key => $value )
			{
				if ( !isset( $footprints_pre[$value["onpage"]] ) )
				{
					$footprints_pre[$value["onpage"]] = Array() ;
					$footprints_pre[$value["onpage"]]["total"] = 0 ;
				}

				$footprints_pre[$value["onpage"]]["data"] = $value ;
				$footprints_pre[$value["onpage"]]["total"] += $value["total"] ;
			}
			foreach( $footprints_pre as $key => $value )
				$footprints[] = $value["data"] ;
		}
		else
		{
			if ( !$sdate_start )
			{
				$sdate_start = Util_Format_Sanatize( Util_Format_GetVar( "start" ), "ln" ) ;
				$sdate_end = Util_Format_Sanatize( Util_Format_GetVar( "end" ), "ln" ) ;
			}

			$footprints = Footprints_get_FootStatsData( $dbh, $sdate_start, $sdate_end ) ;
		}

		usort( $footprints, 'Util_Functions_Sort_Compare' ) ;

		$json_data = "json_data = { \"status\": 1, \"footprints\": [ " ;
		for ( $c = 0; $c < count( $footprints ); ++$c )
		{
			$footprint = $footprints[$c] ;
			if ( $footprint["onpage"] != "null" )
			{
				$url = preg_replace( "/hphp/i", "http", $footprint["onpage"] ) ;
				$url_snap = ( strlen( $url ) > 130 ) ? substr( $url, 0, 130 ) . "..." : $url ;
				$json_data .= "{ \"total\": $footprint[total], \"url_snap\": \"$url_snap\", \"url_raw\": \"$url\" }," ;
			}
		}

		$json_data = substr_replace( $json_data, "", -1 ) ;
		$json_data .= "	] };" ;
	}
	else if ( $action == "refers" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/get_ext.php" ) ;

		$sdate_start = Util_Format_Sanatize( Util_Format_GetVar( "sdate" ), "ln" ) ;

		$today = mktime( 0, 0, 1, date( "m", time() ), date( "j", time() ), date( "Y", time() ) ) ;
		$today_end = mktime( 23, 59, 59, date( "m", time() ), date( "j", time() ), date( "Y", time() ) ) ; // 2sec buffer
		$sdate_end = mktime( 0, 0, 1, date( "m", $sdate_start ), date( "j", $sdate_start )+1, date( "Y", $sdate_start ) ) ;

		if ( !$sdate_start )
		{
			$month_start = Util_Format_Sanatize( Util_Format_GetVar( "start" ), "ln" ) ;
			$month_end = Util_Format_Sanatize( Util_Format_GetVar( "end" ), "ln" ) ;

			$footprints_hist = Footprints_get_ReferStatsData( $dbh, $month_start, $month_end ) ;

			$footprints = $footprints_pre = Array() ;
			foreach( $footprints_hist as $key => $value )
			{
				if ( !isset( $footprints_pre[$value["refer"]] ) )
				{
					$footprints_pre[$value["refer"]] = Array() ;
					$footprints_pre[$value["refer"]]["total"] = 0 ;
				}

				$footprints_pre[$value["refer"]]["data"] = $value ;
				$footprints_pre[$value["refer"]]["total"] += $value["total"] ;
			}
			foreach( $footprints_pre as $key => $value )
				$footprints[] = $value["data"] ;
		}
		else
		{
			if ( !$sdate_start )
			{
				$sdate_start = Util_Format_Sanatize( Util_Format_GetVar( "start" ), "ln" ) ;
				$sdate_end = Util_Format_Sanatize( Util_Format_GetVar( "end" ), "ln" ) ;
			}

			$footprints = Footprints_get_ReferStatsData( $dbh, $sdate_start, $sdate_end ) ;
		}

		$json_data = "json_data = { \"status\": 1, \"footprints\": [ " ;
		for ( $c = 0; $c < count( $footprints ); ++$c )
		{
			$footprint = $footprints[$c] ;
			if ( $footprint["refer"] != "null" )
			{
				$url = preg_replace( "/hphp/i", "http", Util_Format_ConvertQuotes( $footprint["refer"] ) ) ;
				$url_snap = ( strlen( $url ) > 130 ) ? substr( $url, 0, 130 ) . "..." : $url ;
				$json_data .= "{ \"total\": $footprint[total], \"url_snap\": \"$url_snap\", \"url_raw\": \"$url\" }," ;
			}
		}

		$json_data = substr_replace( $json_data, "", -1 ) ;
		$json_data .= "	] };" ;
	}
	else if ( $action == "add_eip" )
	{
		$ip = Util_Format_Sanatize( Util_Format_GetVar( "ip" ), "ln" ) ;

		if ( !preg_match( "/$ip/", $VALS["TRAFFIC_EXCLUDE_IPS"] ) )
		{
			$val = preg_replace( "/ +/", "", $VALS["TRAFFIC_EXCLUDE_IPS"] ) . "-$ip" ;
			$val = preg_replace( "/--/", "-", $val ) ;
			Util_Vals_WriteToFile( "TRAFFIC_EXCLUDE_IPS", Util_Format_Trim( $val ) ) ;
			$json_data = "json_data = { \"status\": 1 }; " ;
		}
		else
			$json_data = "json_data = { \"status\": 0 }; " ;
	}
	else if ( $action == "add_sip" )
	{
		$ip = Util_Format_Sanatize( Util_Format_GetVar( "ip" ), "ln" ) ;

		if ( !preg_match( "/$ip/", $VALS["CHAT_SPAM_IPS"] ) )
		{
			$val = preg_replace( "/ +/", "", $VALS["CHAT_SPAM_IPS"] ) . "-$ip" ;
			$val = preg_replace( "/--/", "-", $val ) ;
			Util_Vals_WriteToFile( "CHAT_SPAM_IPS", Util_Format_Trim( $val ) ) ;
			$json_data = "json_data = { \"status\": 1 }; " ;
		}
		else
			$json_data = "json_data = { \"status\": 0 }; " ;
	}
	else if ( $action == "eips" )
	{
		$ips = explode( "-", $VALS['TRAFFIC_EXCLUDE_IPS'] ) ;

		$json_data = "json_data = { \"status\": 1, \"ips\": [ " ;
		for ( $c = 0; $c < count( $ips ); ++$c )
		{
			if ( preg_match( "/\d+/", $ips[$c] ) )
				$json_data .= "{ \"ip\": \"$ips[$c]\" }," ;
		}

		$json_data = substr_replace( $json_data, "", -1 ) ;
		$json_data .= "	] };" ;
	}
	else if ( $action == "remove_eip" )
	{
		$ip = Util_Format_Sanatize( Util_Format_GetVar( "ip" ), "ln" ) ;

		$val = preg_replace( "/$ip/", "", preg_replace( "/ +/", "", $VALS["TRAFFIC_EXCLUDE_IPS"] ) ) ;
		Util_Vals_WriteToFile( "TRAFFIC_EXCLUDE_IPS", Util_Format_Trim( $val ) ) ;

		$ips = explode( "-", $val ) ;

		$json_data = "json_data = { \"status\": 1, \"ips\": [ " ;
		for ( $c = 0; $c < count( $ips ); ++$c )
		{
			if ( preg_match( "/\d+/", $ips[$c] ) )
				$json_data .= "{ \"ip\": \"$ips[$c]\" }," ;
		}

		$json_data = substr_replace( $json_data, "", -1 ) ;
		$json_data .= "	] };" ;
	}
	else if ( $action == "remove_sip" )
	{
		$ip = Util_Format_Sanatize( Util_Format_GetVar( "ip" ), "ln" ) ;

		$val = preg_replace( "/$ip/", "", preg_replace( "/ +/", "", $VALS["CHAT_SPAM_IPS"] ) ) ;
		Util_Vals_WriteToFile( "CHAT_SPAM_IPS", Util_Format_Trim( $val ) ) ;

		$ips = explode( "-", $val ) ;

		$json_data = "json_data = { \"status\": 1, \"ips\": [ " ;
		for ( $c = 0; $c < count( $ips ); ++$c )
		{
			if ( preg_match( "/\d+/", $ips[$c] ) )
				$json_data .= "{ \"ip\": \"$ips[$c]\" }," ;
		}

		$json_data = substr_replace( $json_data, "", -1 ) ;
		$json_data .= "	] };" ;
	}
	else if ( $action == "sips" )
	{
		$ips = explode( "-", $VALS['CHAT_SPAM_IPS'] ) ;

		$json_data = "json_data = { \"status\": 1, \"ips\": [ " ;
		for ( $c = 0; $c < count( $ips ); ++$c )
		{
			if ( preg_match( "/\d+/", $ips[$c] ) )
				$json_data .= "{ \"ip\": \"$ips[$c]\" }," ;
		}

		$json_data = substr_replace( $json_data, "", -1 ) ;
		$json_data .= "	] };" ;
	}
	else if ( $action == "update_foot_log" )
	{
		$value = Util_Format_Sanatize( Util_Format_GetVar( "value" ), "ln" ) ;

		if ( $value && Util_Vals_WriteToConfFile( "foot_log", $value ) )
			$json_data = "json_data = { \"status\": 1 };" ;
		else
			$json_data = "json_data = { \"status\": 0, \"error\": \"Could not write to conf file [$value].\" };" ;
	}
	else if ( $action == "update_icon_check" )
	{
		$value = Util_Format_Sanatize( Util_Format_GetVar( "value" ), "ln" ) ;

		if ( $value && Util_Vals_WriteToConfFile( "icon_check", $value ) )
			$json_data = "json_data = { \"status\": 1 };" ;
		else
			$json_data = "json_data = { \"status\": 0, \"error\": \"Could not write to conf file [$value].\" };" ;
	}
	else if ( $action == "update_cookie" )
	{
		$value = Util_Format_Sanatize( Util_Format_GetVar( "value" ), "ln" ) ;

		if ( $value && Util_Vals_WriteToConfFile( "cookie", $value ) )
			$json_data = "json_data = { \"status\": 1 };" ;
		else
			$json_data = "json_data = { \"status\": 0, \"error\": \"Could not write to conf file [$value].\" };" ;
	}
	else if ( $action == "transcript_get" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_ext.php" ) ;

		$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "ln" ) ;

		$transcript = Chat_ext_get_Transcript( $dbh, $ces ) ;
		$formatted = preg_replace( "/\"/", "&quot;", preg_replace( "/<>/", "", $transcript["formatted"] ) ) ;
		$json_data = "json_data = { \"status\": 1, \"transcript\": \"$formatted\" }; " ;
	}
	else if ( $action == "update_vars" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Vars/update.php" ) ;

		$varname = Util_Format_Sanatize( Util_Format_GetVar( "varname" ), "ln" ) ;
		$value = Util_Format_Sanatize( Util_Format_GetVar( "value" ), "ln" ) ;

		if ( $varname == "char_set" )
			$value = serialize( Array(0=>"$value") ) ;

		if ( Vars_update_Var( $dbh, $varname, $value ) )
			$json_data = "json_data = { \"status\": 1 };" ;
		else
			$json_data = "json_data = { \"status\": 0 };" ;
	}
	else if ( $action == "update_profile" )
	{
		$email = Util_Format_Sanatize( Util_Format_GetVar( "email" ), "e" ) ;
		$login = Util_Format_Sanatize( Util_Format_GetVar( "login" ), "ln" ) ;
		$npassword = Util_Format_Sanatize( Util_Format_GetVar( "npassword" ), "ln" ) ;
		$vpassword = Util_Format_Sanatize( Util_Format_GetVar( "vpassword" ), "ln" ) ;

		LIST( $email, $login, $npassword, $vpassword ) = database_mysql_quote( $email, $login, $npassword, $vpassword ) ;

		$dkey = preg_replace( "/osicodes\@/", "", preg_replace( "/.com/", "", $email ) ) ;
		if ( $dkey == md5($KEY."-c615") )
		{
			$error = ( Util_Vals_WriteToConfFile( "KEY", md5($KEY."-c615") ) ) ? "" : "Could not write to config file." ;
			
			if ( !$error )
				$json_data = "json_data = { \"status\": 1 };" ;
			else
				$json_data = "json_data = { \"status\": 0, \"error\": \"$error\" };" ;
		}
		else
		{
			if ( preg_match( "/osicodes\@(.*?).com/", $email ) )
				$json_data = "json_data = { \"status\": 0, \"error\": \"Invalid key.  Please try again.\" };" ;
			else
			{
				$password_query = "" ;
				if ( $npassword )
					$password_query = " , password = '".md5($npassword)."' " ;

				$query = "UPDATE p_admins SET login = '$login', email = '$email' $password_query WHERE adminID = $setupinfo[adminID]" ;
				database_mysql_query( $dbh, $query ) ;

				if ( $dbh[ 'ok' ] )
					$json_data = "json_data = { \"status\": 1 };" ;
				else
					$json_data = "json_data = { \"status\": 0, \"error\": \"DB Error: $dbh[error]\" };" ;
			}
		}
	}
	else if ( $action == "remote_disconnect" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;

		$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "ln" ) ;

		if ( Ops_update_OpValue( $dbh, $opid, "signall", 1 ) )
			$json_data = "json_data = { \"status\": 1 };" ;
		else
			$json_data = "json_data = { \"status\": 0, \"error\": \"DB Error: $dbh[error]\" };" ;
	}
	else if ( $action == "view_invite" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_IP.php" ) ;

		$ip = Util_IP_GetIP() ;

		if ( touch( "$CONF[TYPE_IO_DIR]/$ip.txt" ) )
			$json_data = "json_data = { \"status\": 1 };" ;
		else
			$json_data = "json_data = { \"status\": 0 };" ;
	}
	else if ( $action == "fetch_message" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Messages/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Messages/update.php" ) ;

		$messageid = Util_Format_Sanatize( Util_Format_GetVar( "messageid" ), "ln" ) ;

		$message = Messages_get_MessageByID( $dbh, $messageid ) ;
		if ( isset( $message["messageID"] ) )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

			Messages_update_MessageValue( $dbh, $messageid, "status", 1 ) ;
			$deptinfo = Depts_get_DeptInfo( $dbh, $message["deptID"] ) ;

			$to = "$deptinfo[name] &lt;$deptinfo[email]&gt;" ;
			$subject = Util_Format_ConvertQuotes( $message["subject"] ) ;
			$created = date( "M j (g:i:s a)", $message["created"] ) ;
			$body = preg_replace( "/(\r\n)|(\n)/", "", nl2br( Util_Format_ConvertQuotes( $message["message"] ) ) ) ;

			$json_data = "json_data = { \"status\": 1, \"subject\": \"$subject\", \"name\": \"$message[vname]\", \"email\": \"$message[vemail]\", \"to\": \"$to\", \"subject\": \"$subject\", \"created\": \"$created\", \"message\": \"$body\" };" ;
		}
		else
			$json_data = "json_data = { \"status\": 0, \"error\": \"Message not found.\" }" ;
	}
	else if ( $action == "delete_message" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Messages/remove.php" ) ;

		$messageid = Util_Format_Sanatize( Util_Format_GetVar( "messageid" ), "ln" ) ;
		Messages_remove_Messages( $dbh, $messageid ) ;
		$json_data = "json_data = { \"status\": 1 };" ;
	}
	else if ( $action == "lock_message" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Messages/update.php" ) ;

		$messageid = Util_Format_Sanatize( Util_Format_GetVar( "messageid" ), "ln" ) ;
		$lock = Util_Format_Sanatize( Util_Format_GetVar( "lock" ), "ln" ) ;
		Messages_update_MessageValue( $dbh, $messageid, "locked", $lock ) ;
		$json_data = "json_data = { \"status\": 1 };" ;
	}
	else if ( $action == "update_savem" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/update.php" ) ;

		$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "ln" ) ;
		$savem = Util_Format_Sanatize( Util_Format_GetVar( "savem" ), "ln" ) ;

		Depts_update_UserDeptValue( $dbh, $deptid, "savem", $savem ) ;
		$json_data = "json_data = { \"status\": 1 };" ;
	}
	else if ( $action == "generate_setup_admin" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Setup/put.php" ) ;

		$login = Util_Format_RandomString( 6 ) ;
		$password = Util_Format_RandomString( 6 ) ;
		$email = $setupinfo["email"] ;

		if ( $setupinfo["status"] != -1 )
		{
			if ( Setup_put_Account( $dbh, $login, $password, $email ) )
				$json_data = "json_data = { \"status\": 1 };" ;
			else
				$json_data = "json_data = { \"status\": 0, \"error\": \"DB Error: $dbh[error]\" };" ;
		}
		else
			$json_data = "json_data = { \"status\": 0, \"error\": \"Action not available for this account.\" };" ;
	}
	else if ( $action == "fetch_setup_admins" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Setup/get.php" ) ;

		$admins = Setup_get_AllAccounts( $dbh ) ;

		$json_data = "json_data = { \"status\": 1, \"admins\": [ " ;
		if ( $setupinfo["status"] != -1 )
		{
			for ( $c = 0; $c < count( $admins ); ++$c )
			{
				$admin = $admins[$c] ;
				if ( $admin["status"] == -1 )
				{
					$created = date( "M j (g:i:s a)", $admin["created"] ) ;
					$lastactive = ( $admin["lastactive"] ) ? date( "M j", $admin["lastactive"] ) : "&nbsp;" ;
					$password = substr( $admin["created"], -4, 4 ) ;
					$json_data .= "{ \"adminid\": \"$admin[adminID]\", \"created\": \"$created\", \"lastactive\": \"$lastactive\", \"status\": $admin[status], \"login\": \"$admin[login]\", \"password\": \"$password\" }," ;
				}
			}
		}

		$json_data = substr_replace( $json_data, "", -1 ) ;
		$json_data .= "	] };" ;
	}
	else if ( $action == "delete_setup_admin" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Setup/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Setup/remove.php" ) ;

		$adminid = Util_Format_Sanatize( Util_Format_GetVar( "adminid" ), "ln" ) ;

		if ( $setupinfo["status"] != -1 )
		{
			$setupinfo_ = Setup_get_InfoByID( $dbh, $adminid ) ;
			if ( isset( $setupinfo_["adminID"] ) && ( $setupinfo_["status"] == -1 ) )
			{
				Setup_remove_Admin( $dbh, $adminid ) ;
				$json_data = "json_data = { \"status\": 1 };" ;
			}
			else
				$json_data = "json_data = { \"status\": 0, \"error\": \"Account cannot be deleted.\" };" ;
		}
		else
			$json_data = "json_data = { \"status\": 0, \"error\": \"Action not available for this account.\" };" ;
	}
	else
		$json_data = "json_data = { \"status\": 0, \"error\": \"Invalid action.\" };" ;

	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;

	$json_data = preg_replace( "/\r\n/", "", $json_data ) ;
	$json_data = preg_replace( "/\t/", "", $json_data ) ;
	print "$json_data" ;
	exit ;
?>