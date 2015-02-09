<?php
	if ( defined( 'API_IPs_remove' ) ) { return ; }
	define( 'API_IPs_remove', true ) ;

	/****************************************************************/
	FUNCTION IPs_remove_Expired_IPs( &$dbh )
	{
		global $VARS_IP_LOG_EXPIRE ;

		$expired = time() - (60*60*24*$VARS_IP_LOG_EXPIRE) ;
		$query = "DELETE FROM p_ips WHERE created < $expired" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}
?>
