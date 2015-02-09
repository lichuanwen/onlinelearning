<?php
	if ( defined( 'API_Footprints_put_itr' ) ) { return ; }
	define( 'API_Footprints_put_itr', true ) ;

	/****************************************************************/
	FUNCTION Footprints_put_itr_Print( &$dbh,
					$deptid,
					$os,
					$browser,
					$ip,
					$onpage,
					$title )
	{
		if ( ( $deptid == "" ) || ( $os == "" ) || ( $browser == "" )
			|| ( $ip == "" ) || ( $onpage == "" ) )
			return false ;

		$now = time() ;
		$today = mktime( 0, 0, 1, date( "m", time() ), date( "j", time() ), date( "Y", time() ) ) ;
		$url_mdfive = md5( $onpage ) ;

		LIST( $deptid, $os, $browser, $ip, $onpage, $title, $url_mdfive ) = database_mysql_quote( $deptid, $os, $browser, $ip, $onpage, $title, $url_mdfive ) ;

		$query = "INSERT INTO p_footprints VALUES ( $now, '$ip', $os, $browser, '$url_mdfive', '$onpage', '$title' )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$query = "SELECT * FROM p_footstats WHERE sdate = $today AND mdfive = '$url_mdfive'" ;
			database_mysql_query( $dbh, $query ) ;

			if ( $dbh[ 'ok' ] )
			{
				$data = database_mysql_fetchrow( $dbh ) ;

				if ( isset( $data["sdate"] ) )
				{
					$total = $data["total"] + 1 ;
					$query = "UPDATE p_footstats SET total = $total WHERE sdate = $today AND mdfive = '$url_mdfive'" ;
				}
				else
					$query = "INSERT INTO p_footstats VALUES ( $today, '$url_mdfive', 1, '$onpage' )" ;

				database_mysql_query( $dbh, $query ) ;

				return true ;
			}
		}

		return false ;
	}

?>