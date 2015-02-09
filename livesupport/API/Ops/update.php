<?php
	if ( defined( 'API_Ops_update' ) ) { return ; }
	define( 'API_Ops_update', true ) ;

	/****************************************************************/
	FUNCTION Ops_update_OpDeptMoveUp( &$dbh,
					  $opid,
					  $deptid )
	{
		if ( ( $opid == "" ) || ( $deptid == "" ) )
			return false ;

		LIST( $opid, $deptid ) = database_mysql_quote( $opid, $deptid ) ;

		$query = "SELECT display FROM p_dept_ops WHERE opID = $opid AND deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;
		$data = database_mysql_fetchrow( $dbh ) ;
		
		if ( isset( $data["display"] ) )
		{
			$query = "UPDATE p_dept_ops SET display = display + 1 WHERE deptID = $deptid AND display = $data[display] - 1" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "UPDATE p_dept_ops SET display = display - 1 WHERE deptID = $deptid AND opID = $opid" ;
			database_mysql_query( $dbh, $query ) ;
			return true ;
		}
		return false ;
	}

	/****************************************************************/
	FUNCTION Ops_update_OpValue( &$dbh,
					  $opid,
					  $tbl_name,
					  $value )
	{
		if ( ( $opid == "" ) || ( $tbl_name == "" ) )
			return false ;
		
		LIST( $opid, $tbl_name, $value ) = database_mysql_quote( $opid, $tbl_name, $value ) ;

		$query = "UPDATE p_operators SET $tbl_name = '$value' WHERE opID = $opid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}

	/****************************************************************/
	FUNCTION Ops_update_OpDeptVisible( &$dbh,
					  $deptid,
					  $visible )
	{
		if ( ( $deptid == "" ) || ( $visible == "" ) )
			return false ;
		
		LIST( $deptid, $visible ) = database_mysql_quote( $deptid, $visible ) ;

		$query = "UPDATE p_dept_ops SET visible = $visible WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}

	FUNCTION Ops_update_PutOpStatus( &$dbh,
						$opid,
						$status )
	{
		if ( $opid == "" )
			return false ;
		global $CONF ;

		LIST( $opid, $status ) = database_mysql_quote( $opid, $status ) ;

		$now = time() ;
		$query = "INSERT INTO p_opstatus_log VALUES( $now, $opid, $status )" ;
		database_mysql_query( $dbh, $query ) ;

		$query = "UPDATE p_dept_ops SET status = $status WHERE opID = $opid" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}

?>