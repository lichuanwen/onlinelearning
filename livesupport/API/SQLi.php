<?php
	/////////////////////////////////////////
	//
	// Copyright OSI Codes Inc.
	//
	/////////////////////////////////////////
	if ( defined( 'API_Util_SQL' ) ) { return ; }	
	define( 'API_Util_SQL', true ) ;

	$connection = new mysqli( $CONF["SQLHOST"], $CONF["SQLLOGIN"], stripslashes( $CONF["SQLPASS"] ) ) ;
	if ( $connection->select_db( $CONF["DATABASE"] ) )
		$dbh['con'] = $connection ;
	else
	{
		if ( !defined( 'API_Util_Error' ) )
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
		ErrorHandler( 600, "DB Connection Failed", $PHPLIVE_FULLURL, 0, Array() ) ;
	}

	// The &$dbh is passed by reference so you can call it
	// anywhere in the code.  you can always access
	// the $dbh[error] to see the most recent db error.
	function database_mysql_query( &$dbh, $query )
	{
		$dbh['ok'] = 0 ;
		$dbh['result'] = 0 ;
		$dbh['error'] = "None" ; 
		$dbh['query'] = $query ;

		$result = $dbh['con']->query( $query ) ;
		if ( $result )
		{
			$dbh['result'] = $result ;
			$dbh['ok'] = 1 ;
			$dbh['error'] = "None" ;
		}
		else
		{
			$dbh['result'] = 0 ;
			$dbh['ok'] = 0 ;
			$dbh['error'] = $dbh['con']->error ;
		}
	}

	function database_mysql_fetchrow( &$dbh )
	{
		$result = $dbh['result']->fetch_array( MYSQLI_ASSOC ) ;
		return $result ;
	}

	function database_mysql_fetchrowa( &$dbh )
	{
		// new function $result->free();
		$result = $dbh['result']->fetch_assoc() ;
		return $result ;
	}

	function database_mysql_insertid( &$dbh )
	{
		$id = $dbh['con']->insert_id ;
		return $id ;
	}

	function database_mysql_nresults( &$dbh )
	{
		$total = $dbh['con']->affected_rows ;
		return $total ;
	}

	function database_mysql_aresults( &$dbh )
	{
		$total = $dbh['con']->affected_rows ;
		return $total ;
	}

	function database_mysql_quote()
	{
		global $dbh ;
		$output = Array() ;
		for ( $i = 0; $i < func_num_args(); $i++ )
			$output[] = $dbh['con']->real_escape_string( stripslashes( func_get_arg( $i ) ) ) ;
		return $output ;
	}

	function database_mysql_close( &$dbh )
	{
		if ( isset( $dbh['con'] ) && $dbh['con']->close() )
			return true ;
		return false ;
	}

	function database_mysql_version( &$dbh )
	{
		database_mysql_query( $dbh, "SELECT version() AS version" ) ;
		$output = database_mysql_fetchrow( $dbh ) ;
		return $output["version"] ;
	}

	function database_mysql_old( &$dbh )
	{
		return preg_match( "/^(4.0|3)/", database_mysql_version( $dbh ) ) ? 1 : 0 ;
	}

	if ( isset( $CONF['UTF_DB'] ) )
	{
		//$query = "SET NAMES 'utf8'" ;
		//database_mysql_query( $dbh, $query ) ;
	}

?>