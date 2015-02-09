<?php
	if ( defined( 'API_Chat_remove' ) ) { return ; }
	define( 'API_Chat_remove', true ) ;

	/****************************************************************/
	FUNCTION Chat_remove_Request( &$dbh,
						$requestid )
	{
		if ( $requestid == "" )
			return false ;

		LIST( $requestid ) = database_mysql_quote( $requestid ) ;

		$query = "DELETE FROM p_requests WHERE requestID = $requestid" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}

	/****************************************************************/
	FUNCTION Chat_remove_OpDept( &$dbh,
						$opid,
						$deptid )
	{
		if ( ( $opid == "" ) || ( $deptid == "" ) )
			return false ;

		LIST( $opid, $deptid ) = database_mysql_quote( $opid, $deptid ) ;

		$query = "SELECT * FROM p_dept_ops WHERE deptID = $deptid AND opID = $opid" ;
		database_mysql_query( $dbh, $query ) ;
		$data = database_mysql_fetchrow( $dbh ) ;

		$query = "DELETE FROM p_dept_ops WHERE deptID = $deptid AND opID = $opid" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "UPDATE p_dept_ops SET display = display-1 WHERE deptID = $deptid AND display >= $data[display]" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}

	/****************************************************************/
	FUNCTION Chat_remove_ExpiredOp2OpRequests( &$dbh )
	{
		global $VARS_EXPIRED_OP2OP ;
		$expired_op2op = time() - $VARS_EXPIRED_OP2OP ;

		$query = "DELETE FROM p_requests WHERE updated < $expired_op2op AND vupdated < $expired_op2op AND op2op <> 0" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}

	/****************************************************************/
	FUNCTION Chat_remove_Transcript( &$dbh,
						$ces,
						$created )
	{
		if ( ( $ces == "" ) || ( $created == "" ) )
			return false ;

		LIST( $ces, $created ) = database_mysql_quote( $ces, $created ) ;

		$query = "DELETE FROM p_transcripts WHERE ces = '$ces' AND created = $created" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$query = "DELETE FROM p_req_log WHERE ces = '$ces'" ;
			database_mysql_query( $dbh, $query ) ;

			return true ;
		}
		else
			return false ;
	}

	/****************************************************************/
	FUNCTION Chat_remove_ResetReports( &$dbh )
	{
		$query = "TRUNCATE TABLE p_rstats_depts" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "TRUNCATE TABLE p_rstats_ops" ;
		database_mysql_query( $dbh, $query ) ;
		
		return true ;
	}

?>
