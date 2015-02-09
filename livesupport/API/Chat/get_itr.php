<?php
	if ( defined( 'API_Chat_get_itr' ) ) { return ; }
	define( 'API_Chat_get_itr', true ) ;

	/****************************************************************/
	FUNCTION Chat_get_itr_RequestCesInfo( &$dbh,
					$ces )
	{
		if ( $ces == "" )
			return false ;

		LIST( $ces ) = database_mysql_quote( $ces ) ;

		$query = "SELECT * FROM p_requests WHERE ces = '$ces' LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	/****************************************************************/
	FUNCTION Chat_get_itr_RequestIPInfo( &$dbh,
					$ip,
					$initiate_flag )
	{
		if ( $ip == "" )
			return false ;

		LIST( $ip ) = database_mysql_quote( $ip ) ;

		$initiate_string = "" ;
		if ( $initiate_flag )
			$initiate_string = "AND initiated = 1" ;

		$query = "SELECT * FROM p_requests WHERE ip = '$ip' $initiate_string LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}
?>