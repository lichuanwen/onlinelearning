<?php
	if ( defined( 'API_Messages_remove' ) ) { return ; }
	define( 'API_Messages_remove', true ) ;

	/****************************************************************/
	FUNCTION Messages_remove_Messages( &$dbh,
						$messageid )
	{
		if ( $messageid == "" )
			return false ;

		LIST( $messageid ) = database_mysql_quote( $messageid ) ;

		$query = "DELETE FROM p_messages WHERE messageID = $messageid" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}

	/****************************************************************/
	FUNCTION Messages_remove_LastMessages( &$dbh,
						$months )
	{
		if ( !$months || !is_numeric( $months ) )
			return false ;

		$expired = mktime( 0, 0, 1, date( "n", time() )-$months, date( "j", time() ), date( "Y", time() ) ) ;
		$created = date( "M j Y (g:i:s a)", $expired ) ;

		$query = "DELETE FROM p_messages WHERE created < $expired" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}

?>
