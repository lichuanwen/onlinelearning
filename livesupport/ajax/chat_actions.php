<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/* Dev team: 615 */
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$ip = Util_Format_Sanatize( Util_Format_GetVar( "ip" ), "ln" ) ;

	if ( $action == "disconnect" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_itr.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/update.php" ) ;

		$isop = Util_Format_Sanatize( Util_Format_GetVar( "isop" ), "ln" ) ;
		$isop_ = Util_Format_Sanatize( Util_Format_GetVar( "isop_" ), "ln" ) ;
		$isop__ = Util_Format_Sanatize( Util_Format_GetVar( "isop__" ), "ln" ) ;
		$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "ln" ) ;
		$wname = rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "wname" ), "v" ) ) ;
		$rname = rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "rname" ), "v" ) ) ;
		$widget = Util_Format_Sanatize( Util_Format_GetVar( "widget" ), "ln" ) ;

		$now = time() ;
		if ( $widget )
		{
			$requestinfo = Chat_get_itr_RequestIPInfo( $dbh, $ip, 1 ) ;
			$ces = $requestinfo["ces"] ;
		}
		else
			$requestinfo = Chat_get_itr_RequestCesInfo( $dbh, $ces ) ;

		if ( isset( $requestinfo["requestID"] ) && ( $requestinfo["status"] || $requestinfo["initiated"] ) )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/put_itr.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/update.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/Util.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

			$lang = $CONF["lang"] ;
			$deptinfo = Depts_get_DeptInfo( $dbh, $requestinfo["deptID"] ) ;
			if ( $deptinfo["lang"] )
				$lang = $deptinfo["lang"] ;
			include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/$lang.php" ) ;

			if ( $isop )
			{
				$text = "<div class='cl'><disconnected><d1>".$LANG["CHAT_NOTIFY_ODISCONNECT"]."</div>" ;
				if ( $requestinfo["op2op"] )
				{
					if ( ( $isop && $isop_ ) && ( $isop == $isop_ ) ) { $wid = $isop_ ; }
					else if ( $isop && $isop_ ) { $wid = $isop__ ; }
					else { $wid = $isop_ ; }
					$filename = $ces."-$wid" ;
				}
				else
					$filename = $ces."-0" ;
				UtilChat_AppendToChatfile( "$filename.text", $text ) ;
			}
			else
			{
				if ( $requestinfo["initiated"] && !$requestinfo["status"] )
				{
					include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;

					$text = "<div class='cl'><disconnected><d2>Visitor has declined the chat invitation.</div>" ;
					$opinfo = Ops_get_OpInfoByID( $dbh, $requestinfo["opID"] ) ;
					$filename = $ces."-".$opinfo["opID"] ;
					UtilChat_AppendToChatfile( "$filename.text", $text ) ;
				}
				else
				{
					$text = "<div class='cl'><disconnected><d2>".$LANG["CHAT_NOTIFY_VDISCONNECT"]."</div>" ;
					$filename = $ces."-$isop_" ;
					UtilChat_AppendToChatfile( "$filename.text", $text ) ;
				}
			}

			UtilChat_AppendToChatfile( "$ces.txt", $text ) ;
			Chat_update_RequestLogValue( $dbh, $ces, "ended", $now ) ;

			if ( !$requestinfo["initiated"] || ( $requestinfo["initiated"] && $requestinfo["status"] ) )
			{
				$output = UtilChat_ExportChat( "$ces.txt" ) ;
				if ( isset( $output[0] ) )
				{
					$formatted = $output[0] ; $plain = $output[1] ;
					$fsize = strlen( $formatted ) ;
					if ( Chat_put_itr_Transcript( $dbh, $ces, $requestinfo["status"], $requestinfo["created"], $now, $requestinfo["deptID"], $requestinfo["opID"], $requestinfo["initiated"], $requestinfo["op2op"], 0, $fsize, $requestinfo["vname"],	$requestinfo["vemail"], $requestinfo["ip"], $requestinfo["question"], $formatted, $plain ) )
					{
						Chat_remove_Request( $dbh, $requestinfo["requestID"] ) ;
						Chat_update_RecentChat( $dbh, $requestinfo["opID"], $ces, 0 ) ;
						if ( is_file( "$CONF[TYPE_IO_DIR]/$ces.txt" ) ) { unlink( "$CONF[TYPE_IO_DIR]/$ces.txt" ) ; }
					}
				}
			}
			else if ( $requestinfo["initiated"] || $requestinfo["status"] )
				Chat_remove_Request( $dbh, $requestinfo["requestID"] ) ;
		}
		else if ( isset( $requestinfo["requestID"] ) && !$requestinfo["status"] )
		{
			if ( $isop && ( $requestinfo["opID"] != $isop ) )
			{
				if ( $requestinfo["op2op"] )
				{
					include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove_itr.php" ) ;

					Chat_remove_itr_RequestByCes( $dbh, $requestinfo["ces"] ) ;
					if ( is_file( "$CONF[TYPE_IO_DIR]/$ces.txt" ) ) { unlink( "$CONF[TYPE_IO_DIR]/$ces.txt" ) ; }
				}
			}
			else
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/put_itr.php" ) ;
				include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/update.php" ) ;
				include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove_itr.php" ) ;

				Ops_put_itr_OpReqStat( $dbh, $requestinfo["deptID"], 0, "message", 1 ) ;
				Chat_update_RequestLogValue( $dbh, $ces, "ended", time() ) ;
				Chat_remove_itr_RequestByCes( $dbh, $requestinfo["ces"] ) ;
				if ( is_file( "$CONF[TYPE_IO_DIR]/$ces.txt" ) ) { unlink( "$CONF[TYPE_IO_DIR]/$ces.txt" ) ; }
			}
		}

		// safe measure cleaning in case the chat duration is a very long time
		if ( is_file( "$CONF[TYPE_IO_DIR]/$ip.txt" ) )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/IPs/update.php" ) ;

			$initiate_array = ( isset( $CONF["auto_initiate"] ) && $CONF["auto_initiate"] ) ? unserialize( html_entity_decode( $CONF["auto_initiate"] ) ) : Array() ;

			$auto_initiate_reset = 0 ;
			if ( isset( $initiate_array["reset"] ) )
				$auto_initiate_reset = $initiate_array["reset"] ;

			$reset = 60*60*24*$auto_initiate_reset ;
			IPs_update_IpValue( $dbh, $ip, "i_initiate", time() + $reset ) ;

			if ( is_file( "$CONF[TYPE_IO_DIR]/$ip.txt" ) ) { unlink( "$CONF[TYPE_IO_DIR]/$ip.txt" ) ; }
		}
		Footprints_update_FootprintUniqueValue( $dbh, $ip, "chatting", 0 ) ;
		// end safe measure cleaning

		if ( $widget )
		{
			database_mysql_close( $dbh ) ;
			$image_dir = "$CONF[DOCUMENT_ROOT]/pics/icons/pixels" ;
			$image_path = "$image_dir/1x1.gif" ;
			Header( "Content-type: image/GIF" ) ;
			readfile( $image_path ) ;
			exit ;
		}
		else
			$json_data = "json_data = { \"status\": 1, \"ces\": \"$ces\" };" ;
	}
	else if ( $action == "rating" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/put_itr.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/update.php" ) ;

		$requestid = Util_Format_Sanatize( Util_Format_GetVar( "requestid" ), "ln" ) ;
		$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "ln" ) ;
		$rating = Util_Format_Sanatize( Util_Format_GetVar( "rating" ), "ln" ) ;
		$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "ln" ) ;
		$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "ln" ) ;

		if ( Chat_update_TranscriptValue( $dbh, $ces, "rating", $rating ) )
		{
			Chat_update_RecentChat( $dbh, $opid, $ces, $rating ) ;
			Ops_put_itr_OpReqStat( $dbh, $deptid, $opid, "rateit", 1 ) ;
			Ops_put_itr_OpReqStat( $dbh, $deptid, $opid, "ratings", $rating ) ;
		}
		
		$json_data = "json_data = { \"status\": 1 };" ;
	}
	else if ( $action == "istyping" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/Util.php" ) ;

		$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "ln" ) ;
		$isop = Util_Format_Sanatize( Util_Format_GetVar( "isop" ), "ln" ) ;
		$isop_ = Util_Format_Sanatize( Util_Format_GetVar( "isop_" ), "ln" ) ;
		$isop__ = Util_Format_Sanatize( Util_Format_GetVar( "isop__" ), "ln" ) ;
		$wname = Util_Format_Sanatize( Util_Format_GetVar( "wname" ), "v" ) ;
		$rname = Util_Format_Sanatize( Util_Format_GetVar( "rname" ), "v" ) ;
		$flag = Util_Format_Sanatize( Util_Format_GetVar( "flag" ), "ln" ) ;

		if ( $flag )
			UtilChat_WriteIsWriting( $ces, $flag, $isop, $isop_, $isop__ ) ;
		else
			UtilChat_WriteIsWriting( $ces, $flag, $isop, $isop_, $isop__ ) ;

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