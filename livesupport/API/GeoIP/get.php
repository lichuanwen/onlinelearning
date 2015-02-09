<?php
	if ( defined( 'API_GeoIP_get' ) ) { return ; }
	define( 'API_GeoIP_get', true ) ;

	/****************************************************************/
	FUNCTION GeoIP_get_GeoInfo( &$dbh,
			$ip_num,
			$network )
	{
		if ( ( $network == "" ) || ( $ip_num == "" ) )
			return false ;

		$query = "SELECT l.country, l.region, l.city, l.latitude, l.longitude FROM p_geo_loc l JOIN p_geo_bloc b ON ( l.locId = b.locId ) WHERE b.network = $network AND $ip_num BETWEEN startIpNum AND endIpNum limit 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

?>