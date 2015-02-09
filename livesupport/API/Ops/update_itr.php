<?php
	if ( defined( 'API_Ops_update_itr' ) ) { return ; }
	define( 'API_Ops_update_itr', true ) ;

	/****************************************************************/
	FUNCTION Ops_update_itr_IdleOps( &$dbh )
	{
		global $CONF ;
		global $VARS_EXPIRED_OPS ;

		// chat_routing.php has the # of times cycled (2 sec x 4 = 8)
		$idle = time() - $VARS_EXPIRED_OPS ;

		$query = "SELECT * FROM p_operators WHERE lastactive < $idle AND status = 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			if ( database_mysql_nresults( $dbh ) )
			{
				if ( !defined( 'API_Ops_update' ) )
					include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;

				$operators = Array() ;
				while( $data = database_mysql_fetchrow( $dbh ) )
					$operators[] = $data ;
				$query = "UPDATE p_operators SET status = 0 WHERE lastactive < $idle AND status = 1" ;
				database_mysql_query( $dbh, $query ) ;

				$now = time() ;
				for( $c = 0; $c < count( $operators ); ++$c )
				{
					$operator = $operators[$c] ;

					$query = "INSERT INTO p_opstatus_log VALUES( $now, $operator[opID], 0 )" ;
					database_mysql_query( $dbh, $query ) ;

					$query = "UPDATE p_dept_ops SET status = 0 WHERE opID = $operator[opID]" ;
					database_mysql_query( $dbh, $query ) ;
				}
			}
		}
		return true ;
	}

?>