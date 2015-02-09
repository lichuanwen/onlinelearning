<?php
	if ( defined( 'API_Chat_remove_itr' ) ) { return ; }
	define( 'API_Chat_remove_itr', true ) ;

	/****************************************************************/
	FUNCTION Chat_remove_itr_RequestByCes( &$dbh,
						$ces )
	{
		if ( $ces == "" )
			return false ;

		LIST( $ces ) = database_mysql_quote( $ces ) ;

		$query = "DELETE FROM p_requests WHERE ces = '$ces'" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}

	/****************************************************************/
	FUNCTION Chat_remove_itr_OldRequests( &$dbh )
	{
		global $CONF ;

		global $VARS_EXPIRED_REQS ;
		$now = time() ;
		$expired = $now - $VARS_EXPIRED_REQS ;

		// cycle it so data is put in transcript .txt file for warning BEFORE delete
		// set it AFTER delete so it sets it on next pass to delete
		$query = "SELECT * FROM p_requests WHERE ( created < $expired AND ( updated < $expired OR vupdated < $expired ) ) AND op2op = 0" ;
		database_mysql_query( $dbh, $query ) ;
		if ( $dbh[ 'ok' ] )
		{
			if ( database_mysql_nresults( $dbh ) )
			{
				if ( !defined( 'API_Chat_Util' ) )
					include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/Util.php" ) ;
				if ( !defined( 'API_Chat_put' ) )
					include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/put_itr.php" ) ;
				if ( !defined( 'API_Chat_get' ) )
					include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get.php" ) ;
				if ( !defined( 'API_Chat_remove_itr' ) )
					include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove_itr.php" ) ;
				if ( !defined( 'API_Depts_get' ) )
					include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

				$lang = $CONF["lang"] ; $prev_deptid = 1111111111 ; // start things off
				$expired_requests = Array() ;
				while( $data = database_mysql_fetchrow( $dbh ) )
					$expired_requests[] = $data ;

				for ( $c = 0; $c < count( $expired_requests ); ++$c )
				{
					$request = $expired_requests[$c] ;
					$ces = $request["ces"] ;
					$ip = $request["ip"] ;
					$deptid = $request["deptID"] ;
					$trans_file = "$ces.txt" ;

					if ( $prev_deptid != $deptid )
					{
						$prev_deptid = $deptid ;
						$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
						if ( $deptinfo["lang"] )
							$lang = $deptinfo["lang"] ;
						include( "$CONF[DOCUMENT_ROOT]/lang_packs/$lang.php" ) ;
					}

					$query = "UPDATE p_footprints_u SET chatting = 0 WHERE ip = '$ip'" ;
					database_mysql_query( $dbh, $query ) ;

					if ( is_file( "$CONF[CHAT_IO_DIR]/$trans_file" ) )
					{
						$string_disconnect = "<div class='cl'><disconnected><d6>".$LANG["CHAT_NOTIFY_DISCONNECT"]."</div>" ;
						UtilChat_AppendToChatfile( $trans_file, $string_disconnect ) ;
						$filename_op = $ces."-$request[opID]" ; UtilChat_AppendToChatfile( "$filename_op.text", $string_disconnect ) ;
						$filename_vis = $ces."-0" ; UtilChat_AppendToChatfile( "$filename_vis.text", $string_disconnect ) ;

						$output = UtilChat_ExportChat( $trans_file ) ;
						if ( isset( $output[0] ) )
						{
							LIST( $formatted, $plain ) = $output ;

							$fsize = strlen( $formatted ) ;
							$requestinfo = Chat_get_RequestHistCesInfo( $dbh, $ces ) ;
							
							// should be deleted but check to make sure
							if ( is_file( "$CONF[TYPE_IO_DIR]/$ip.txt" ) ) { unlink( "$CONF[TYPE_IO_DIR]/$ip.txt" ) ; }

							if ( !$requestinfo["ended"] )
							{
								LIST( $ces ) = database_mysql_quote( $ces ) ;
								$query = "UPDATE p_req_log SET ended = $now WHERE ces = '$ces'" ;
								database_mysql_query( $dbh, $query ) ;
							}

							if ( $requestinfo["status"] )
							{
								if ( Chat_put_itr_Transcript( $dbh, $ces, $requestinfo["status"], $requestinfo["created"], $now, $requestinfo["deptID"], $requestinfo["opID"], $requestinfo["initiated"], $requestinfo["op2op"], 0, $fsize, $requestinfo["vname"], $requestinfo["vemail"], $requestinfo["ip"], $requestinfo["question"], $formatted, $plain ) )
									Chat_remove_itr_RequestByCes( $dbh, $ces ) ;
							}
						}
						if ( is_file( "$CONF[TYPE_IO_DIR]/$trans_file" ) ) { unlink( "$CONF[TYPE_IO_DIR]/$trans_file" ) ; }
					}
				}

				$query = "DELETE FROM p_requests WHERE ( created < $expired AND ( updated < $expired OR vupdated < $expired ) ) AND op2op = 0" ;
				database_mysql_query( $dbh, $query ) ;
			}
		}

		return true ;
	}

	/****************************************************************/
	FUNCTION Chat_remove_itr_ExpiredOp2OpRequests( &$dbh )
	{
		global $VARS_EXPIRED_OP2OP ;
		$expired_op2op = time() - $VARS_EXPIRED_OP2OP ;

		$query = "DELETE FROM p_requests WHERE updated < $expired_op2op AND vupdated < $expired_op2op AND op2op <> 0" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}

?>
