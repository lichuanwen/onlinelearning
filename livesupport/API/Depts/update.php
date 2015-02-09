<?php
	if ( defined( 'API_Depts_update' ) ) { return ; }
	define( 'API_Depts_update', true ) ;

	/****************************************************************/
	FUNCTION Depts_update_UserDeptValue( &$dbh,
					  $deptid,
					  $tbl_name,
					  $value )
	{
		if ( ( $deptid == "" ) || ( $tbl_name == "" ) )
			return false ;
		
		LIST( $deptid, $tbl_name, $value ) = database_mysql_quote( $deptid, $tbl_name, $value ) ;

		$query = "UPDATE p_departments SET $tbl_name = '$value' WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}

	/****************************************************************/
	FUNCTION Depts_update_UserDeptValues( &$dbh,
					  $deptid,
					  $tbl_name,
					  $value,
					  $tbl_name2,
					  $value2 )
	{
		if ( ( $deptid == "" ) || ( $tbl_name == "" ) || ( $value == "" )
			|| ( $tbl_name2 == "" ) || ( $value2 == "" ) )
			return false ;
		
		LIST( $deptid, $tbl_name, $value, $tbl_name2, $value2 ) = database_mysql_quote( $deptid, $tbl_name, $value, $tbl_name2, $value2 ) ;

		$query = "UPDATE p_departments SET $tbl_name = '$value', $tbl_name2 = '$value2' WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}

	/****************************************************************/
	FUNCTION Depts_update_DeptValueEncrypt( &$dbh,
					  $deptid,
					  $tbl_name,
					  $value )
	{
		if ( ( $deptid == "" ) || ( $tbl_name == "" ) )
			return false ;
		
		LIST( $deptid, $tbl_name, $value ) = database_mysql_quote( $deptid, $tbl_name, $value ) ;

		$query = "UPDATE p_departments SET $tbl_name = '$value' WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}
?>