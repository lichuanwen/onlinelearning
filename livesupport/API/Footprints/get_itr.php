<?php
	if ( defined( 'API_Footprints_get_itr' ) ) { return ; }
	define( 'API_Footprints_get_itr', true ) ;

	/****************************************************************/
	FUNCTION Footprints_get_itr_IPFootprints_U( &$dbh,
					$ip )
	{
		if ( $ip == "" )
			return false ;

		LIST( $ip ) = database_mysql_quote( $ip ) ;

		$query = "SELECT * FROM p_footprints_u WHERE ip = '$ip' LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	/****************************************************************/
	FUNCTION Footprints_get_itr_TotalFootprints_U( &$dbh )
	{
		$query = "SELECT count(*) AS total FROM p_footprints_u" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data["total"] ;
		}
		return 0 ;
	}

	/****************************************************************/
	FUNCTION Footprints_get_itr_IPFootprints( &$dbh,
					$ip,
					$limit )
	{
		if ( ( $ip == "" ) || ( $limit == "" ) )
			return false ;

		LIST( $ip, $limit ) = database_mysql_quote( $ip, $limit ) ;

		$query = "SELECT count(*) AS total, mdfive, onpage, title FROM p_footprints WHERE ip = '$ip' GROUP BY mdfive ORDER BY total DESC LIMIT $limit" ;
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