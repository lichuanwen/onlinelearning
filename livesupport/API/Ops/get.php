<?php
	if ( defined( 'API_Ops_get' ) ) { return ; }
	define( 'API_Ops_get', true ) ;

	/****************************************************************/
	FUNCTION Ops_get_AllOps( &$dbh )
	{
		$query = "SELECT * FROM p_operators ORDER BY name ASC" ;
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
	FUNCTION Ops_get_TotalOps( &$dbh )
	{
		$query = "SELECT count(*) AS total FROM p_operators" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data["total"] ;
		}
		return false ;
	}

	/****************************************************************/
	FUNCTION Ops_get_IsOpInDept( &$dbh,
					$opid,
					$deptid )
	{
		if ( ( $opid == "" ) || ( $deptid == "" ) )
			return false ;

		LIST( $opid, $deptid ) = database_mysql_quote( $opid, $deptid ) ;

		$query = "SELECT * FROM p_dept_ops WHERE deptID = $deptid AND opID = $opid" ;
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
	FUNCTION Ops_get_OpInfoByID( &$dbh,
					$opid )
	{
		if ( $opid == "" )
			return false ;

		LIST( $opid ) = database_mysql_quote( $opid ) ;

		$query = "SELECT * FROM p_operators WHERE opID = $opid LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	/****************************************************************/
	FUNCTION Ops_get_NextRequestOp( &$dbh,
					$deptid,
					$rtype,
					$rstring )
	{
		if ( ( $deptid == "" ) || ( $rtype == "" ) )
			return false ;

		global $VARS_EXPIRED_OPS ;
		$lastactive = time() - $VARS_EXPIRED_OPS ;

		if ( $rtype == 1 )
			$order_by = "ORDER BY p_dept_ops.display ASC" ;
		else if ( $rtype == 2 )
			$order_by = "ORDER BY p_operators.lastrequest ASC" ;
		else
			return false ;

		LIST( $deptid ) = database_mysql_quote( $deptid ) ;

		$query = "SELECT p_operators.opID AS opID, p_operators.rate AS rate, p_operators.sms AS sms, p_operators.smsnum AS smsnum, p_operators.name AS name, p_operators.email AS email FROM p_operators INNER JOIN p_dept_ops ON p_operators.opID = p_dept_ops.opID WHERE p_operators.status = 1 AND p_operators.lastactive > $lastactive AND p_dept_ops.deptID = $deptid $rstring $order_by LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	/****************************************************************/
	FUNCTION Ops_get_OpDepts( &$dbh,
						$opid )
	{
		if ( $opid == "" )
			return false ;

		LIST( $opid ) = database_mysql_quote( $opid ) ;

		$query = "SELECT p_dept_ops.deptID AS deptID, p_departments.name AS name FROM p_dept_ops, p_departments WHERE p_dept_ops.opID = $opid AND p_dept_ops.deptID = p_departments.deptID" ;
		database_mysql_query( $dbh, $query ) ;

		$depts = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			while ( $data = database_mysql_fetchrow( $dbh ) )
				$depts[] = $data ;
			return $depts ;
		}
		return false ;
	}

?>