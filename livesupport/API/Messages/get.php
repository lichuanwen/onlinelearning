<?php
	if ( defined( 'API_Messages_get' ) ) { return ; }
	define( 'API_Messages_get', true ) ;

	/****************************************************************/
	FUNCTION Messages_get_Messages( &$dbh,
						$deptid,
						$page,
						$limit )
	{
		if ( $limit == "" )
			return false ;

		LIST( $deptid, $page, $limit ) = database_mysql_quote( $deptid, $page, $limit ) ;
		$start = ( $page * $limit ) ;

		$dept_string = ( $deptid ) ? "WHERE deptID = $deptid" : "" ;
		$query = "SELECT * FROM p_messages $dept_string ORDER BY created DESC LIMIT $start, $limit" ;
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

	/****************************************************************/
	FUNCTION Messages_get_MessageByID( &$dbh,
						$messageid )
	{
		if ( $messageid == "" )
			return Array() ;

		LIST( $messageid ) = database_mysql_quote( $messageid ) ;

		$query = "SELECT * FROM p_messages WHERE messageID = $messageid LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		$ops = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return Array() ;
	}

	/****************************************************************/
	FUNCTION Messages_get_TotalMessages( &$dbh,
						$deptid )
	{
		LIST( $deptid ) = database_mysql_quote( $deptid ) ;

		$dept_string = ( $deptid ) ? "WHERE deptID = $deptid" : "" ;
		$query = "SELECT count(*) AS total FROM p_messages $dept_string" ;
		database_mysql_query( $dbh, $query ) ;

		$ops = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data["total"] ;
		}
		return false ;
	}

	/****************************************************************/
	FUNCTION Messages_get_TotalUnreadMessages( &$dbh,
						$deptid )
	{
		LIST( $deptid ) = database_mysql_quote( $deptid ) ;

		$dept_string = ( $deptid ) ? "AND deptID = $deptid" : "" ;
		$query = "SELECT count(*) AS total FROM p_messages WHERE status = 0 $dept_string" ;
		database_mysql_query( $dbh, $query ) ;

		$ops = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data["total"] ;
		}
		return false ;
	}

?>
