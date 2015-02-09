<?php
	if ( defined( 'API_Chat_get_ext' ) ) { return ; }
	define( 'API_Chat_get_ext', true ) ;

	/****************************************************************/
	FUNCTION Chat_get_ext_RequestsRangeHash( &$dbh,
								$stat_start,
								$stat_end )
	{
		global $operators ;

		if ( ( $stat_start == "" ) || ( $stat_end == "" ) )
			return false ;

		/***********************************************************/
		// do auto clean on non existing operators
		$ops_query = "" ;
		for ( $c = 0; $c < count( $operators ); ++$c )
		{
			$operator = $operators[$c] ;
			$ops_query .= " AND opID <> $operator[opID] " ;
		}
		if ( count( $operators ) )
		{
			$query = "DELETE FROM p_rstats_ops WHERE (opID <> 0 $ops_query)" ;
			database_mysql_query( $dbh, $query ) ;
		}
		// end auto clean
		/***********************************************************/

		LIST( $stat_start, $stat_end ) = database_mysql_quote( $stat_start, $stat_end ) ;

		$query = "SELECT * FROM p_rstats_depts WHERE sdate >= $stat_start AND sdate <= $stat_end" ;
		database_mysql_query( $dbh, $query ) ;

		$output = Array() ; $stats = Array() ; $depts_sdate = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			while( $data = database_mysql_fetchrow( $dbh ) )
				$stats[] = $data ;

			for ( $c = 0; $c < count( $stats ); ++$c )
			{
				$data = $stats[$c] ;
				$sdate = $data["sdate"] ;
				$deptid = $data["deptID"] ;

				if ( !isset( $output[$sdate] ) )
				{
					$output[$sdate] = Array() ;
					$output[$sdate]["depts"] = Array() ;
				}

				if ( !isset( $output[$sdate]["depts"][$deptid] ) )
				{
					$output[$sdate]["depts"][$deptid] = Array() ;
					$output[$sdate]["depts"][$deptid]["requests"] = 0 ;
					$output[$sdate]["depts"][$deptid]["taken"] = 0 ;
					$output[$sdate]["depts"][$deptid]["declined"] = 0 ;
					$output[$sdate]["depts"][$deptid]["message"] = 0 ;
					$output[$sdate]["depts"][$deptid]["initiated"] = 0 ;
					$output[$sdate]["depts"][$deptid]["initiated_"] = 0 ;
					$output[$sdate]["depts"][$deptid]["rateit"] = 0 ;
					$output[$sdate]["depts"][$deptid]["ratings"] = 0 ;
				}

				$output[$sdate]["depts"][$deptid]["requests"] += $data["requests"] ;
				$output[$sdate]["depts"][$deptid]["taken"] += $data["taken"] ;
				$output[$sdate]["depts"][$deptid]["declined"] += $data["declined"] ;
				$output[$sdate]["depts"][$deptid]["message"] += $data["message"] ;
				$output[$sdate]["depts"][$deptid]["initiated"] += $data["initiated"] ;
				$output[$sdate]["depts"][$deptid]["initiated_"] += $data["initiated_"] ;
				$output[$sdate]["depts"][$deptid]["rateit"] += $data["rateit"] ;
				$output[$sdate]["depts"][$deptid]["ratings"] += $data["ratings"] ;
			}

			$stats = Array() ;
			$query = "SELECT * FROM p_rstats_ops WHERE sdate >= $stat_start AND sdate <= $stat_end" ;
			database_mysql_query( $dbh, $query ) ;
			while( $data = database_mysql_fetchrow( $dbh ) )
				$stats[] = $data ;

			for ( $c = 0; $c < count( $stats ); ++$c )
			{
				$data = $stats[$c] ;
				$sdate = $data["sdate"] ;
				$opid = $data["opID"] ;

				if ( !isset( $output[$sdate]["ops"] ) )
					$output[$sdate]["ops"] = Array() ;

				if ( !isset( $output[$sdate]["ops"][$opid] ) )
				{
					$output[$sdate]["ops"][$opid] = Array() ;
					$output[$sdate]["ops"][$opid]["requests"] = 0 ;
					$output[$sdate]["ops"][$opid]["taken"] = 0 ;
					$output[$sdate]["ops"][$opid]["declined"] = 0 ;
					$output[$sdate]["ops"][$opid]["message"] = 0 ;
					$output[$sdate]["ops"][$opid]["initiated"] = 0 ;
					$output[$sdate]["ops"][$opid]["initiated_"] = 0 ;
					$output[$sdate]["ops"][$opid]["rateit"] = 0 ;
					$output[$sdate]["ops"][$opid]["ratings"] = 0 ;
				}

				$output[$sdate]["ops"][$opid]["requests"] += $data["requests"] ;
				$output[$sdate]["ops"][$opid]["taken"] += $data["taken"] ;
				$output[$sdate]["ops"][$opid]["declined"] += $data["declined"] ;
				$output[$sdate]["ops"][$opid]["message"] += $data["message"] ;
				$output[$sdate]["ops"][$opid]["initiated"] += $data["initiated"] ;
				$output[$sdate]["ops"][$opid]["initiated_"] += $data["initiated_"] ;
				$output[$sdate]["ops"][$opid]["rateit"] += $data["rateit"] ;
				$output[$sdate]["ops"][$opid]["ratings"] += $data["ratings"] ;
			}
		}
		return $output ;
	}

	/****************************************************************/
	FUNCTION Chat_ext_get_IPTranscripts( &$dbh,
						$ip )
	{
		if ( $ip == "" )
			return false ;

		LIST( $ip ) = database_mysql_quote( $ip ) ;

		$query = "SELECT * FROM p_transcripts WHERE ip = '$ip' ORDER BY created DESC" ;
		database_mysql_query( $dbh, $query ) ;

		$output = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			while( $data = database_mysql_fetchrow( $dbh ) )
				$output[] = $data ;
		}
		return $output ;
	}

	/****************************************************************/
	FUNCTION Chat_ext_get_RefinedTranscripts( &$dbh,
								$deptid,
								$opid,
								$text,
								$page,
								$limit )
	{
		if ( ( $deptid == "" ) || ( $opid == "" ) || ( $limit == "" ) )
			return false ;

		LIST( $deptid, $opid, $text, $page, $limit ) = database_mysql_quote( $deptid, $opid, $text, $page, $limit ) ;
		$start = ( $page * $limit ) ;

		$search_string = "" ;
		if ( $text )
			$search_string = " AND plain LIKE '%$text%' " ;

		if ( $deptid )
		{
			$query = "SELECT * FROM p_transcripts WHERE deptID = $deptid $search_string ORDER BY created DESC LIMIT $start, $limit" ;
			$query2 = "SELECT count(*) AS total FROM p_transcripts WHERE deptID = $deptid $search_string" ;
		}
		else if ( $opid )
		{
			$query = "SELECT * FROM p_transcripts WHERE opID = $opid $search_string ORDER BY created DESC LIMIT $start, $limit" ;
			$query2 = "SELECT count(*) AS total FROM p_transcripts WHERE opID = $opid $search_string" ;
		}
		else
		{
			$query = "SELECT * FROM p_transcripts WHERE created > 0 $search_string ORDER BY created DESC LIMIT $start, $limit" ;
			$query2 = "SELECT count(*) AS total FROM p_transcripts WHERE created > 0 $search_string" ;
		}
		database_mysql_query( $dbh, $query ) ;

		$output = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			while( $data = database_mysql_fetchrow( $dbh ) )
				$output[] = $data ;
		}

		database_mysql_query( $dbh, $query2 ) ;
		$data = database_mysql_fetchrow( $dbh ) ;
		$output[] = $data["total"] ;

		return $output ;
	}

	/****************************************************************/
	FUNCTION Chat_ext_get_Transcript( &$dbh,
								$ces )
	{
		if ( $ces == "" )
			return false ;

		LIST( $ces ) = database_mysql_quote( $ces ) ;

		$query = "SELECT * FROM p_transcripts WHERE ces = '$ces'" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	/****************************************************************/
	FUNCTION Chat_ext_get_OpDeptTrans( &$dbh,
								$opid,
								$text,
								$page,
								$limit )
	{
		if ( ( $opid == "" ) || ( $limit == "" ) )
			return false ;

		global $CONF ;
		if ( !defined( 'API_Depts_get' ) )
			include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

		LIST( $opid, $text, $page, $limit ) = database_mysql_quote( $opid, $text, $page, $limit ) ;
		$start = ( $page * $limit ) ;

		$departments = Depts_get_OpDepts( $dbh, $opid ) ;
		$dept_string = " ( opID = $opid OR op2op = $opid " ;
		for ( $c = 0; $c < count( $departments ); ++$c )
		{
			if ( $departments[$c]["tshare"] )
				$dept_string .= " OR deptID = " . $departments[$c]["deptID"] ;
		}
		$dept_string .= " ) " ;

		$search_string = "" ;
		if ( $text )
			$search_string = " plain LIKE '%$text%' AND " ;

		$query = "SELECT * FROM p_transcripts WHERE $search_string $dept_string ORDER BY created DESC LIMIT $start, $limit" ;
		database_mysql_query( $dbh, $query ) ;

		$output = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			while( $data = database_mysql_fetchrow( $dbh ) )
				$output[] = $data ;
		}

		$query = "SELECT count(*) AS total FROM p_transcripts WHERE $search_string $dept_string" ;
		database_mysql_query( $dbh, $query ) ;
		$data = database_mysql_fetchrow( $dbh ) ;
		$output[] = $data["total"] ;

		return $output ;
	}

	/****************************************************************/
	FUNCTION Chat_ext_get_AllRequests( &$dbh,
					$deptid )
	{
		LIST( $deptid ) = database_mysql_quote( $deptid ) ;

		$dept_string = "" ;
		if ( $deptid )
			$dept_string = " WHERE deptID = $deptid " ;

		$query = "SELECT * FROM p_requests $dept_string ORDER BY created ASC" ;
		database_mysql_query( $dbh, $query ) ;

		$output = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			while( $data = database_mysql_fetchrow( $dbh ) )
				$output[] = $data ;
			return $output ;
		}
		return false ;
	}

	/****************************************************************/
	FUNCTION Chat_ext_get_OpStatusLog( &$dbh,
								$opid,
								$stat_start,
								$stat_end )
	{
		if ( ( $opid == "" ) || ( $stat_start == "" ) || ( $stat_end == "" ) )
			return Array() ;

		LIST( $opid, $stat_start, $stat_end ) = database_mysql_quote( $opid, $stat_start, $stat_end ) ;

		$query = "SELECT * FROM p_opstatus_log WHERE opID = $opid AND created >= $stat_start AND created <= $stat_end ORDER BY created ASC" ;
		database_mysql_query( $dbh, $query ) ;

		$output = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			while( $data = database_mysql_fetchrow( $dbh ) )
				$output[] = $data ;
		}
		return $output ;
	}

?>