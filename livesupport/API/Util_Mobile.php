<?php
if ( defined( 'API_Util_Mobile' ) ) { return ; }	
define( 'API_Util_Mobile', true ) ;

function Util_Mobile_Detect() {

	$agent = isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : "&nbsp;" ;
	$accept = isset( $_SERVER["HTTP_ACCEPT"] ) ? $_SERVER["HTTP_ACCEPT"] : "" ;

	if ( isset( $_SERVER["HTTP_X_WAP_PROFILE"] ) ) {
		return 1 ;
	}

	if ( preg_match( "/wap\.|\.wap/i", $accept ) ) {
		return 1 ;
	}

	if ( $agent ){
		$user_agents = array("midp", "j2me", "avantg", "docomo", "novarra", "palmos", "palmsource", "240x320", "opwv", "chtml", "pda", "windows\ ce", "mmp\/", "blackberry", "mib\/", "symbian", "wireless", "nokia", "hand", "mobi", "phone", "cdm", "up\.b", "audio", "SIE\-", "SEC\-", "samsung", "HTC", "mot\-", "mitsu", "sagem", "sony", "alcatel", "lg", "erics", "vx", "NEC", "philips", "mmm", "xx", "panasonic", "sharp", "wap", "sch", "rover", "pocket", "benq", "java", "pt", "pg", "vox", "amoi", "bird", "compal", "kg", "voda", "sany", "kdd", "dbt", "sendo", "sgh", "gradi", "jb", "\d\d\di", "moto");
		foreach( $user_agents as $user_string ){
			if( preg_match( "/".$user_string."/i", $agent ) ) {
				return 1 ;
			}
		}
	}

	if ( preg_match( "/iphone/i",$agent ) ) { return 1 ; }

	return 0 ;
}
