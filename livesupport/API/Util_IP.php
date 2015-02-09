<?php
	if ( defined( 'API_Util_IP' ) ) { return ; }	
	define( 'API_Util_IP', true ) ;

	function Util_IP_GetIP()
	{
		if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && $_SERVER['HTTP_X_FORWARDED_FOR'] )
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ;
			if ( preg_match( "/,/", $ip ) )
				LIST( $ip, $ip_ ) = explode( ",", preg_replace( "/ +/", "", $ip ) ) ;
			return $ip ;
		}
		else if ( isset( $_SERVER['REMOTE_ADDR'] ) && $_SERVER['REMOTE_ADDR'] )
			return $_SERVER['REMOTE_ADDR'] ;
		else
			return "0.0.0.0" ;
	}
?>