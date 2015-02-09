<?php
	if ( defined( 'API_Footprints_get' ) ) { return ; }
	define( 'API_Footprints_get', true ) ;

	/****************************************************************/
	FUNCTION Footprints_get_RequestsRangeHash( &$dbh )
	{
		return Array() ;
	}

	/****************************************************************/
	FUNCTION Footprints_get_Footprints_U( &$dbh,
					$dept_string )
	{
		LIST( $dept_string ) = database_mysql_quote( $dept_string ) ;

		// for now 100 traffic output max to save load.  in future will optimize this area
		$query = "SELECT * FROM p_footprints_u ORDER BY created ASC LIMIT 100" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$output = Array() ;
			while ( $data = database_mysql_fetchrow( $dbh ) )
				$output[] = $data ;
			return $output ;
		}
		return false ;
	}
?>