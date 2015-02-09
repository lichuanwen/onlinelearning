<?php
	if ( defined( 'API_Chat_put_itr' ) ) { return ; }
	define( 'API_Chat_put_itr', true ) ;

	/****************************************************************/
	FUNCTION Chat_put_itr_Transcript( &$dbh,
					$ces,
					$status,
					$created,
					$ended,
					$deptid,
					$opid,
					$initiated,
					$op2op,
					$rating,
					$fsize,
					$vname,
					$vemail,
					$ip,
					$question,
					$formatted,
					$plain )
	{
		if ( ( $ces == "" ) || ( $deptid == "" ) || ( $opid == "" ) || ( $fsize == "" )
			|| ( $ended == "" ) || ( $vname == "" ) || ( $ip == "" )
			|| ( $question == "" ) || ( $formatted == "" ) || ( $plain == "" ) )
			return false ;

		global $CONF ;
		global $deptinfo ;
		if ( !defined( 'API_Ops_get' ) )
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
		if ( !defined( 'API_Util_Email' ) )
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Email.php" ) ;

		LIST( $ces, $status, $created, $ended, $deptid, $opid, $initiated, $op2op, $rating, $fsize, $vname, $vemail, $ip, $question, $formatted, $plain ) = database_mysql_quote( $ces, $status, $created, $ended, $deptid, $opid, $initiated, $op2op, $rating, $fsize, $vname, $vemail, $ip, $question, $formatted, $plain ) ;

		$query = "SELECT * FROM p_transcripts WHERE ces = '$ces' LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;
		$transcript = database_mysql_fetchrow( $dbh ) ;

		if ( !isset( $transcript["ces"] ) )
		{
			if ( !defined( 'API_Chat_get' ) )
				include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get.php" ) ;

			// get initiated value from log because during transfer it resets the initiate flag
			$requestinfo_log = Chat_get_RequestHistCesInfo( $dbh, $ces ) ;
			$initiated = ( isset( $requestinfo_log["initiated"] ) ) ? $requestinfo_log["initiated"] : $initiated ;

			$query = "INSERT INTO p_transcripts VALUES ( '$ces', $created, $ended, $deptid, $opid, $initiated, $op2op, $rating, $fsize, '$vname', '$vemail', '$ip', '$question', '$formatted', '$plain' )" ;
			database_mysql_query( $dbh, $query ) ;
		}
		else if ( $created == "null" ) { $formatted = $transcript["formatted"] ; }
		else { $formatted = false ; }

		if ( $dbh['ok'] && $formatted && isset( $deptinfo["temail"] ) )
		{
			if ( $status && ( $deptinfo["temail"] || $deptinfo["temaild"] ) && ( $vemail != "null" ) )
			{
				if ( $deptinfo["smtp"] )
				{
					if ( !defined( 'API_Util_Functions_itr' ) )
						include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions_itr.php" ) ;

					$smtp_array = unserialize( Util_Functions_itr_Decrypt( $CONF["SALT"], $deptinfo["smtp"] ) ) ;

					$CONF["SMTP_HOST"] = $smtp_array["host"] ;
					$CONF["SMTP_LOGIN"] = $smtp_array["login"] ;
					$CONF["SMTP_PASS"] = $smtp_array["pass"] ;
					$CONF["SMTP_PORT"] = $smtp_array["port"] ;
				}

				$opinfo = Ops_get_OpInfoByID( $dbh, $opid ) ;
				$vname = preg_replace( "/<v>/", "", $vname ) ;

				$lang = $CONF["lang"] ;
				if ( $deptinfo["lang"] ) { $lang = $deptinfo["lang"] ; }
				include( "$CONF[DOCUMENT_ROOT]/lang_packs/$lang.php" ) ;

				$subject_visitor = $LANG["TRANSCRIPT_SUBJECT"]." $opinfo[name]" ;
				$subject_department = $LANG["TRANSCRIPT_SUBJECT"]." $vname" ;

				$message_trans = preg_replace( "/%%visitor%%/", $vname, $deptinfo["msg_email"] ) ;
				$message_trans = preg_replace( "/%%operator%%/", $opinfo["name"], $message_trans ) ;
				$message_trans = preg_replace( "/%%op_email%%/", $opinfo["email"], $message_trans ) ;
				$message_trans = preg_replace( "/%%transcript%%/", preg_replace( "/\\$/", "-dollar-", stripslashes( $formatted ) ), $message_trans ) ;

				if ( ( $created == "null" ) && $vemail )
					Util_Email_SendEmail( $opinfo["name"], $opinfo["email"], $vname, $vemail, $subject_visitor, $message_trans, "trans" ) ;
				
				if ( $deptinfo["temaild"] )
					Util_Email_SendEmail( $opinfo["name"], $opinfo["email"], $deptinfo["name"], $deptinfo["email"], $subject_department, "---\r\n$subject_department <$vemail>\r\n--\r\n$message_trans", "trans" ) ;
			}
			return true ;
		}
		return false ;
	}

?>