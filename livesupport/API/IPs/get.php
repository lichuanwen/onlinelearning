<?php
	if ( defined( 'API_IPs_get' ) ) { return ; }
	define( 'API_IPs_get', true ) ;

	/****************************************************************/
	FUNCTION IPs_get_IPInfo( &$dbh,
					$ip )
	{
		if ( $ip == "" )
			return false ;

		$ip = md5( $ip ) ;
		$query = "SELECT * FROM p_ips WHERE ip = '$ip' LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

?>