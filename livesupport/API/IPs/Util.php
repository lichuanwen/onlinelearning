<?php
	if ( defined( 'API_IPs_Util' ) ) { return ; }	
	define( 'API_IPs_Util', true ) ;

	function UtilIPs_IP2Long( $ip )
	{
		if ( $ip == "" ) {
			return 0 ;
		} else {
			$ips = explode( ".", $ip ) ;
			$net = isset( $ips[0] ) ? $ips[0] : 0 ;
			$ip_num = ( $ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256 ) ;
			return Array( $ip_num, $net ) ;
		}
	}

	function UtilIPs_Long2IP( $digit )
	{
		if ( $digit == "" ) {
			return 0 ;
		} else {
			$ip = UtilIPs_Long2IP_( $digit ) ;
			$ips = explode(".", $ip) ;
			$net = isset( $ips[0] ) ? $ips[0] : 0 ;
			return Array( $ip, $net ) ;
		}
	}

	function UtilIPs_Long2IP_( $digit ){
		$b = Array( 0, 0 ,0, 0 ) ;
		$c = 16777216.0 ;
		$digit += 0.0 ;
		for( $i = 0; $i < 4; $i++ )
		{
			$k = (int) ($digit / $c) ;
			$digit -= $c * $k ;
			$b[$i] = $k ;
			$c /= 256.0 ;
		}

		$d = join( '.', $b ) ;
		return ($d) ;
	}
?>