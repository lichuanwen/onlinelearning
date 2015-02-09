<?php
	Header( 'Access-Control-Allow-Origin: *' ) ;
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$akey = Util_Format_Sanatize( Util_Format_GetVar( "akey" ), "ln" ) ; $jkey = Util_Format_Sanatize( Util_Format_GetVar( "jkey" ), "ln" ) ;
	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "ln" ) ;
	$onpage = Util_Format_Sanatize( Util_Format_GetVar( "p" ), "url" ) ;
	$image_dir = realpath( "$CONF[DOCUMENT_ROOT]/pics/icons/pixels" ) ;

	if ( $akey ) { $jkey = md5( $akey ) ; }

	if ( $jkey && isset( $CONF["API_KEY"] ) && ( $jkey == md5( $CONF["API_KEY"] ) ) )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get_itr.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update_itr.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/remove.php" ) ;

		///////////////////////////
		// auto cleaning of DB
		Footprints_remove_Expired_U( $dbh ) ;
		Ops_update_itr_IdleOps( $dbh ) ;
		///////////////////////////

		$total_ops = Ops_get_itr_AnyOpsOnline( $dbh, $deptid ) ;
		database_mysql_close( $dbh ) ;

		if ( $action == "js" )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_IP.php" ) ;

			$ip = Util_IP_GetIP() ;
			$file_name = "online_$deptid.info" ;
			if ( $total_ops )
			{
				if ( !is_file( "$CONF[CHAT_IO_DIR]/$file_name" ) )
					touch( "$CONF[CHAT_IO_DIR]/$file_name" ) ;
			}
			else
			{
				if ( is_file( "$CONF[CHAT_IO_DIR]/$file_name" ) )
					unlink( "$CONF[CHAT_IO_DIR]/$file_name" ) ;
			}

			if ( $ip && preg_match( "/$ip/", $VALS["CHAT_SPAM_IPS"] ) )
				$image_path = "$image_dir/3x3.gif" ;
			else if ( $total_ops ) { $image_path = "$image_dir/1x1.gif" ; }
			else { $image_path = "$image_dir/2x2.gif" ; }

			Header( "Content-type: image/GIF" ) ;
			readfile( $image_path ) ; exit ;
		}
		else
		{
			if ( $total_ops ) { print "1" ; }
			else { print "0" ; }
		}
	}
	else { print "Invalid API Key." ; }
?>