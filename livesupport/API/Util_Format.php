<?php
	if ( defined( 'API_Util_Format' ) ) { return ; }	
	define( 'API_Util_Format', true ) ;

	function Util_Format_Sanatize( $string, $flag )
	{
		switch ( $flag )
		{
			case ( "a" ):
				return ( is_array( $string ) ) ? $string : Array() ;
				break ;
			case ( "n" ):
				return preg_replace( "/[^0-9]/i", "", trim( $string, "\x00" ) ) ;
				break ;
			case ( "ln" ):
				return preg_replace( "/[\$%=<>\(\)\[\]\|\{\}]/i", "", trim( $string, "\x00" ) ) ;
				break ;
			case ( "e" ):
				return preg_replace( "/[^a-z0-9_.\-@]/i", "", Util_Format_Trim( trim( $string, "\x00" ) ) ) ;
				break ;
			case ( "v" ):
				return preg_replace( "/(%20)|(%00)|(%3Cv%3E)|(<v>)/", "", Util_Format_Trim( trim( $string, "\x00" ) ) ) ;
				break ;
			case ( "vs" ):
				return preg_replace( "/(%20)|(%00)|(%3Cv%3E)|(<v>)|( )/", "", Util_Format_Trim( trim( $string, "\x00" ) ) ) ;
				break ;
			case ( "base_url" ):
				return preg_replace( "/[\$\!`\"<>'\?;]/i", "", Util_Format_Trim( preg_replace( "/hphp/i", "http", trim( $string, "\x00" ) ) ) ) ;
				break ;
			case ( "url" ):
				return preg_replace( "/[\$\!`\"<>'\(\); ]/i", "", Util_Format_Trim( preg_replace( "/hphp/i", "http", trim( $string, "\x00" ) ) ) ) ;
				break ;
			case ( "title" ):
				return preg_replace( "/[\$=\!<>;]/i", "", Util_Format_Trim( preg_replace( "/hphp/i", "http", trim( $string, "\x00" ) ) ) ) ;
				break ;
			case ( "htmltags" ):
				return Util_Format_ConvertTags( trim( $string, "\x00" ) ) ;
				break ;
			case ( "notags" ):
				return strip_tags( trim( $string, "\x00" ) ) ;
				break ;
			case ( "htmle" ):
				return htmlentities( trim( $string, "\x00" ) ) ;
				break ;
			default:
				return trim( $string, "\x00" ) ;
		}
	}

	function Util_Format_URL( $string )
	{
		$string = preg_replace( "/http/is", "hphp", $string ) ; 
		return $string ;
	}

	function Util_Format_Trim( $string )
	{
		$string = preg_replace( "/(\r\n)|(\r)|(\n)/", "", $string ) ; 
		return $string ;
	}

	function Util_Format_ConvertTags( $string )
	{
		$string = preg_replace( "/</", "&lt;", $string ) ; $string = preg_replace( "/>/", "&gt;", $string ) ;
		return $string ;
	}

	function Util_Format_ConvertQuotes( $string )
	{
		$string = preg_replace( "/\"/", "&quot;", $string ) ; $string = preg_replace( "/'/", "&lsquo;", $string ) ;
		return $string ;
	}

	function Util_Format_StripQuotes( $string )
	{
		$string = preg_replace( "/[\"']/", "", $string ) ;
		return $string ;
	}

	function Util_Format_Duration( $duration )
	{
		$string = $minutes = $hours = "" ;

		$minutes = round( $duration/60 ) ;
		if ( $minutes >= 60 )
		{
			$hours = floor( $minutes/60 ) ;
			$minutes = $minutes % 60 ;
			$string = "$hours h $minutes m" ;
		}
		else
			$string = "$minutes min" ;

		return $string ;
	}

	function Util_Format_GetVar( $varname )
	{
		$varout = 0 ;

		if ( isset( $_POST[$varname] ) )
			$varout = $_POST[$varname] ;
		else if ( isset( $_GET[$varname] ) )
			$varout = $_GET[$varname] ;

		if ( get_magic_quotes_gpc() && !is_array( $varout ) )
			$varout = stripslashes( $varout ) ;
		return $varout ;
	}

	function Util_Format_GetOS( $agent )
	{
		global $CONF ;
		if ( !defined( 'API_Util_Mobile' ) )
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Mobile.php" ) ;
		
		$mobile = Util_Mobile_Detect() ;
		if ( $mobile ) { $os = 5 ; }
		else if ( preg_match( "/Windows/i", $agent ) ) { $os = 1 ; }
		else if ( preg_match( "/Mac/i", $agent ) ) { $os = 2 ; }
		else { $os = 4 ; }

		if ( preg_match( "/MSIE/i", $agent ) ) { $browser = 1 ; }
		else if ( preg_match( "/Firefox/i", $agent ) ) { $browser = 2 ; }
		else if ( preg_match( "/Chrome/i", $agent ) ) { $browser = 3 ; }
		else if ( preg_match( "/Safari/i", $agent ) ) { $browser = 4 ; }
		else { $browser = 6 ; }

		return Array( $os, $browser ) ;
	}

	function Util_Format_RandomString( $length = 5, $chars = '23456789abcdefghjkmnpqrstuvwxyz')
	{
		$charLength = strlen($chars)-1;

		$randomString = "";
		for($i = 0 ; $i < $length ; $i++)
			$randomString .= $chars[mt_rand(0,$charLength)];

		return $randomString;
	}

	function Util_Format_DEBUG( $string )
	{
		global $CONF ;
		file_put_contents( "$CONF[DOCUMENT_ROOT]/web/debug.txt", $string, FILE_APPEND ) ;
	}

	/****************************************************************/
	FUNCTION Util_Format_Get_Vars( &$dbh )
	{
		$query = "SELECT * FROM p_vars LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	/****************************************************************/
	FUNCTION Util_Format_Update_TimeStamp( &$dbh, $timestamp, $now )
	{
		if ( !preg_match( "/(clean)/", $timestamp ) )
			return false ;

		LIST( $timestamp, $now ) = database_mysql_quote( $timestamp, $now ) ;

		$query = "UPDATE p_vars SET ts_$timestamp = $now" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}

	/****************************************************************/
	FUNCTION Util_Format_ExplodeString( $delim, $string )
	{
		$output = explode( $delim, $string ) ;
		for ( $c = 0; $c < count( $output ); ++$c )
		{
			if ( !$output[$c] ) { unset( $output[$c] ) ; }
		}
		return $output ;
	}
?>