<?php
	if ( defined( 'API_Canned_put' ) ) { return ; }
	define( 'API_Canned_put', true ) ;

	/****************************************************************/
	FUNCTION Canned_put_Canned( &$dbh,
					$canid,
					$opid,
					$deptid,
					$title,
					$message )
	{
		if ( ( $opid == "" ) || ( $deptid == "" )  || ( $title == "" )
			|| ( $message == "" ) )
			return false ;

		if ( !$canid ) { $canid = "NULL" ; }
		LIST( $canid, $opid, $deptid, $title, $message ) = database_mysql_quote( $canid, $opid, $deptid, $title, $message ) ;

		$query = "REPLACE INTO p_canned VALUES( $canid, $opid, $deptid, '$title', '$message' )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$id = database_mysql_insertid ( $dbh ) ;
			return $id ;
		}

		return false ;
	}

?>