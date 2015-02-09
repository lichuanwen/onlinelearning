<?php
	if ( defined( 'API_Footprints_get_ext' ) ) { return ; }
	define( 'API_Footprints_get_ext', true ) ;

	/****************************************************************/
	FUNCTION Footprints_get_ext_FootprintsRangeHash( &$dbh,
						$stat_start,
						$stat_end )
	{
		if ( ( $stat_start == "" ) || ( $stat_end == "" ) )
			return false ;

		LIST( $stat_start, $stat_end ) = database_mysql_quote( $stat_start, $stat_end ) ;

		$query = "SELECT SUM( total ) AS total, sdate FROM p_footstats WHERE sdate >= $stat_start AND sdate <= $stat_end GROUP BY sdate" ;
		database_mysql_query( $dbh, $query ) ;

		$output = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			while( $data = database_mysql_fetchrow( $dbh ) )
			{
				$sdate = $data["sdate"] ;

				if ( !isset( $output[$sdate] ) )
				{
					$output[$sdate] = Array() ;
					$output[$sdate]["total"] = 0 ;
				}

				$output[$sdate]["total"] = $data["total"] ;
			}
		}
		return $output ;
	}

	/****************************************************************/
	FUNCTION Footprints_get_ReferRangeHash( &$dbh,
						$stat_start,
						$stat_end )
	{
		if ( ( $stat_start == "" ) || ( $stat_end == "" ) )
			return false ;

		LIST( $stat_start, $stat_end ) = database_mysql_quote( $stat_start, $stat_end ) ;

		$query = "SELECT SUM( total ) AS total, sdate FROM p_referstats WHERE sdate >= $stat_start AND sdate <= $stat_end GROUP BY sdate" ;
		database_mysql_query( $dbh, $query ) ;

		$output = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			while( $data = database_mysql_fetchrow( $dbh ) )
			{
				$sdate = $data["sdate"] ;

				if ( !isset( $output[$sdate] ) )
				{
					$output[$sdate] = Array() ;
					$output[$sdate]["total"] = 0 ;
				}

				$output[$sdate]["total"] = $data["total"] ;
			}
		}
		return $output ;
	}

	/****************************************************************/
	FUNCTION Footprints_get_FootData( &$dbh,
					$stat_start,
					$stat_end )
	{
		if ( ( $stat_start == "" ) || ( $stat_end == "" ) )
			return false ;

		LIST( $stat_start, $stat_end ) = database_mysql_quote( $stat_start, $stat_end ) ;

		$query = "SELECT count(*) AS total, onpage FROM p_footprints WHERE created >= $stat_start AND created <= $stat_end GROUP BY mdfive ORDER BY total DESC LIMIT 25" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$output = Array() ;
			while( $data = database_mysql_fetchrow( $dbh ) )
				$output[] = $data ;
			return $output ;
		}
		return false ;
	}

	/****************************************************************/
	FUNCTION Footprints_get_FootStatsData( &$dbh,
					$stat_start,
					$stat_end )
	{
		if ( ( $stat_start == "" ) || ( $stat_end == "" ) )
			return false ;

		LIST( $stat_start, $stat_end ) = database_mysql_quote( $stat_start, $stat_end ) ;

		$query = "SELECT * FROM p_footstats WHERE sdate >= $stat_start AND sdate <= $stat_end ORDER BY total DESC LIMIT 100" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$output = Array() ;
			while( $data = database_mysql_fetchrow( $dbh ) )
				$output[] = $data ;
			return $output ;
		}
		return false ;
	}

	/****************************************************************/
	FUNCTION Footprints_get_ReferStatsData( &$dbh,
					$stat_start,
					$stat_end )
	{
		if ( ( $stat_start == "" ) || ( $stat_end == "" ) )
			return false ;

		LIST( $stat_start, $stat_end ) = database_mysql_quote( $stat_start, $stat_end ) ;

		$query = "SELECT * FROM p_referstats WHERE sdate >= $stat_start AND sdate <= $stat_end ORDER BY total DESC LIMIT 100" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$output = Array() ;
			while( $data = database_mysql_fetchrow( $dbh ) )
				$output[] = $data ;
			return $output ;
		}
		return false ;
	}

	/****************************************************************/
	FUNCTION Footprints_get_IPRefer( &$dbh,
					$ip,
					$limit )
	{
		if ( ( $ip == "" ) || ( $limit == "" ) )
			return false ;

		LIST( $ip, $limit ) = database_mysql_quote( $ip, $limit ) ;

		$query = "SELECT refer, p_marketing.marketID, p_marketing.name, p_marketing.color FROM p_refer LEFT JOIN p_marketing ON p_refer.marketID = p_marketing.marketID WHERE ip = '$ip' ORDER BY created DESC LIMIT $limit" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$output = Array() ;
			while( $data = database_mysql_fetchrow( $dbh ) )
				$output[] = $data ;
			return $output ;
		}
		return false ;
	}

?>