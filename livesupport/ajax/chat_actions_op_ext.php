<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/* Dev team: 615 */
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;

	if ( !isset( $_COOKIE["phplive_opID"] ) )
		$json_data = "json_data = { \"status\": -1 };" ;
	else if ( $action == "sms_send" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	
		$phonenum = Util_Format_Sanatize( Util_Format_GetVar( "phonenum" ), "n" ) ;
		$carrier = Util_Format_Sanatize( Util_Format_GetVar( "carrier" ), "ln" ) ;

		$opinfo = Ops_get_OpInfoByID( $dbh, $_COOKIE["phplive_opID"] ) ;
		if ( !$opinfo["sms"] )
			$json_data = "json_data = { \"status\": 0, \"error\": \"SMS is not enabled for this account.\" }; " ;
		else if ( $opinfo["sms"] < time()-60 )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions_itr.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Email.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

			$departments = Depts_get_OpDepts( $dbh, $opinfo["opID"] ) ;

			for ( $c = 0; $c < count( $departments ); ++$c )
			{
				$deptinfo = $departments[$c] ;
				if ( $deptinfo["smtp"] )
				{
					$smtp_array = unserialize( Util_Functions_itr_Decrypt( $CONF["SALT"], $deptinfo["smtp"] ) ) ;

					$CONF["SMTP_HOST"] = $smtp_array["host"] ;
					$CONF["SMTP_LOGIN"] = $smtp_array["login"] ;
					$CONF["SMTP_PASS"] = $smtp_array["pass"] ;
					$CONF["SMTP_PORT"] = $smtp_array["port"] ;
					break 1 ;
				}
			}

			$vcode = time() ;
			$smsnum = "$phonenum@$carrier" ;
			$error = Util_Email_SendEmail( $opinfo["name"], $opinfo["email"], "Mobile", $smsnum, "Verification", "Verification Code: $vcode", "sms" ) ;

			if ( $error )
				$json_data = "json_data = { \"status\": 0, \"error\": \"$error\" }; " ;
			else
			{
				Ops_update_OpValue( $dbh, $_COOKIE["phplive_opID"], "sms", $vcode ) ;
				Ops_update_OpValue( $dbh, $_COOKIE["phplive_opID"], "smsnum", base64_encode( $smsnum ) ) ;

				$json_data = "json_data = { \"status\": 1 }; " ;
			}
		}
		else
		{
			$time_left = $opinfo["sms"] - ( time()-60 ) ;
			$json_data = "json_data = { \"status\": 0, \"error\": \"Please try again in $time_left seconds.\" }; " ;
		}
	}
	else if ( $action == "sms_verify" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
	
		$code = Util_Format_Sanatize( Util_Format_GetVar( "code" ), "ln" ) ;

		$opinfo = Ops_get_OpInfoByID( $dbh, $_COOKIE["phplive_opID"] ) ;
		if ( !$opinfo["sms"] )
			$json_data = "json_data = { \"status\": 0, \"error\": \"SMS is not enabled for this account.\" }; " ;
		else if ( ( $code == 1 ) || ( $code == 2 ) )
		{
			Ops_update_OpValue( $dbh, $_COOKIE["phplive_opID"], "sms", $code ) ;
			$json_data = "json_data = { \"status\": 1 };" ;
		}
		else if ( ( $opinfo["sms"] == 1 ) || ( $opinfo["sms"] == 2 ) )
			$json_data = "json_data = { \"status\": 1, \"error\": \"Your mobile has already been verified.\" }; " ;
		else if ( $opinfo["sms"] == $code )
		{
			Ops_update_OpValue( $dbh, $_COOKIE["phplive_opID"], "sms", 1 ) ;
			$json_data = "json_data = { \"status\": 1 }; " ;
		}
		else
			$json_data = "json_data = { \"status\": 0, \"error\": \"Verification code is invalid.\" }; " ;
	}
	else if ( $action == "dn_toggle" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
	
		$dn = Util_Format_Sanatize( Util_Format_GetVar( "dn" ), "n" ) ;

		Ops_update_OpValue( $dbh, $_COOKIE["phplive_opID"], "dn", $dn ) ;
		$json_data = "json_data = { \"status\": 1 };" ;
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
