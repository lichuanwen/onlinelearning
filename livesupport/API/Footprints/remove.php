<?php
	if ( defined( 'API_Footprints_remove' ) ) { return ; }
	define( 'API_Footprints_remove', true ) ;

	/****************************************************************/
	FUNCTION Footprints_remove_Expired_U( &$dbh )
	{
		global $VARS_FOOTPRINT_U_EXPIRE ;

		$expired = time() - $VARS_FOOTPRINT_U_EXPIRE ;
		$query = "DELETE FROM p_footprints_u WHERE updated < $expired" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}

	/****************************************************************/
	FUNCTION Footprints_remove_Expired_Footprints( &$dbh )
	{
		global $VARS_FOOTPRINT_LOG_EXPIRE ;
		global $VARS_REFER_LOG_EXPIRE ;

		$expired = time() - ( 60*60*24*$VARS_FOOTPRINT_LOG_EXPIRE ) ;
		$expired_refer = time() - ( 60*60*24*$VARS_REFER_LOG_EXPIRE ) ;

		$query = "DELETE FROM p_footprints WHERE created < $expired" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "DELETE FROM p_refer WHERE created < $expired_refer" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}
?>
