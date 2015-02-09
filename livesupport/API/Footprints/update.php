<?php
	if ( defined( 'API_Footprints_update' ) ) { return ; }
	define( 'API_Footprints_update', true ) ;

	/****************************************************************/
	FUNCTION Footprints_update_FootprintUniqueValue( &$dbh,
					  $ip,
					  $tbl_name,
					  $value )
	{
		if ( ( $ip == "" ) || ( $tbl_name == "" ) )
			return false ;

		LIST( $ip, $tbl_name, $value ) = database_mysql_quote( $ip, $tbl_name, $value ) ;

		$query = "UPDATE p_footprints_u SET $tbl_name = '$value' WHERE ip = '$ip'" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$flag = database_mysql_nresults( $dbh ) ;
			return $flag ;
		}
		return false ;
	}

?>