<?php
	if ( defined( 'API_Chat_get' ) ) { return ; }
	define( 'API_Chat_get', true ) ;

	/****************************************************************/
	FUNCTION Chat_get_RequestHistCesInfo( &$dbh,
					$ces )
	{
		if ( $ces == "" )
			return false ;

		LIST( $ces ) = database_mysql_quote( $ces ) ;

		$query = "SELECT * FROM p_req_log WHERE ces = '$ces' LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	/****************************************************************/
	FUNCTION Chat_get_OpTotalRequests( &$dbh,
						$opid )
	{
		if ( $opid == "" )
			return false ;

		LIST( $opid ) = database_mysql_quote( $opid ) ;

		$query = "SELECT count(*) AS total FROM p_requests WHERE ( opID = $opid OR op2op = $opid OR opID = 1111111111 ) AND ( status = 1 OR status = 2 )" ;
		database_mysql_query( $dbh, $query ) ;

		$requests = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data["total"] ;
		}
		return 0 ;
	}

	/****************************************************************/
	FUNCTION Chat_get_IPTotalRequests( &$dbh,
						$ip )
	{
		if ( $ip == "" )
			return false ;

		LIST( $ip ) = database_mysql_quote( $ip ) ;

		$query = "SELECT count(*) AS total FROM p_req_log WHERE ip = '$ip'" ;
		database_mysql_query( $dbh, $query ) ;

		$requests = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data["total"] ;
		}
		return 0 ;
	}

	/****************************************************************/
	FUNCTION Chat_get_OpOverallRatings( &$dbh,
						$opid )
	{
		if ( $opid == "" )
			return false ;

		LIST( $opid ) = database_mysql_quote( $opid ) ;

		$query = "SELECT SUM(rateit) AS rateit, SUM(ratings) AS ratings FROM p_rstats_ops WHERE opID = $opid" ;
		database_mysql_query( $dbh, $query ) ;

		$rating = 0 ;
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			if ( $data["rateit"] )
				$rating = round( $data["ratings"]/$data["rateit"] ) ;
		}

		return $rating ;
	}

	/****************************************************************/
	FUNCTION Chat_get_OpOverallChats( &$dbh,
						$opid )
	{
		if ( $opid == "" )
			return false ;

		LIST( $opid ) = database_mysql_quote( $opid ) ;

		$query = "SELECT SUM(taken) AS total FROM p_rstats_ops WHERE opID = $opid" ;
		database_mysql_query( $dbh, $query ) ;

		$total = 0 ;
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			$total = ( $data["total"] ) ? $data["total"] : 0 ;
		}

		return $total ;
	}

	/****************************************************************/
	FUNCTION Chat_get_OpDayChats( &$dbh,
						$opid,
						$stat_start,
						$stat_end )
	{
		if ( ( $opid == "" ) || ( $stat_start == "" ) || ( $stat_end == "" ) )
			return false ;

		LIST( $opid ) = database_mysql_quote( $opid ) ;

		$query = "SELECT SUM(taken) AS total FROM p_rstats_ops WHERE sdate >= $stat_start AND sdate <= $stat_end AND opID = $opid" ;
		database_mysql_query( $dbh, $query ) ;

		$total = 0 ;
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			$total = ( $data["total"] ) ? $data["total"] : 0 ;
		}

		return $total ;
	}

	/****************************************************************/
	FUNCTION Chat_get_TotalIPTranscripts( &$dbh,
								$ip )
	{
		if ( $ip == "" )
			return false ;

		LIST( $ip ) = database_mysql_quote( $ip ) ;

		$query = "SELECT count(*) AS total FROM p_transcripts WHERE ip = '$ip'" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
				return $data["total"] ;
		}
		return 0 ;
	}

?>