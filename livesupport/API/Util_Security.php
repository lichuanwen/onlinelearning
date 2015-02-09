<?php
	if ( defined( 'API_Util_Security' ) ) { return ; }	
	define( 'API_Util_Security', true ) ;

	/*****************************************************************/
	function Util_Security_GenSetupSes()
	{
		global $CONF ;
		$ses = md5( time().$CONF["DOCUMENT_ROOT"] ) ;
		return $ses ;
	}

	function Util_Security_AuthSetup( &$dbh,
					$ses,
					$adminid = 0 )
	{
		if ( $ses == "" )
			return false ;

		$adminid = isset( $_COOKIE["phplive_adminID"] ) ? $_COOKIE["phplive_adminID"] : 0 ;

		LIST( $adminid, $ses ) = database_mysql_quote( $adminid, $ses ) ;

		$query = "SELECT * FROM p_admins WHERE adminID = $adminid AND ses = '$ses'" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			if ( isset( $data["adminID"] ) )
				return $data ;
		}
		return false ;
	}

	function Util_Security_AuthOp( &$dbh,
					$ses,
					$opid = 0,
					$wp = 0 )
	{
		if ( $ses == "" )
			return false ;

		if ( !$opid && !$wp )
			$opid = isset( $_COOKIE["phplive_opID"] ) ? $_COOKIE["phplive_opID"] : 0 ;

		LIST( $opid, $ses ) = database_mysql_quote( $opid, $ses ) ;

		$query = "SELECT * FROM p_operators WHERE opID = $opid AND ses = '$ses'" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			if ( isset( $data["opID"] ) )
				return $data ;
		}
		return false ;
	}
?>