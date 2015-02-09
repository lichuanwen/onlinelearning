<?php
	if ( defined( 'API_Util_Functions' ) ) { return ; }	
	define( 'API_Util_Functions', true ) ;

	function Util_Functions_Sort_Compare($a, $b){ return strnatcmp($b['total'], $a['total']) ; }

	function Util_Functions_IsProtoHttps( $theflag )
	{
		$proto = 0 ;
		if ( isset( $_SERVER["HTTPS"] ) && preg_match( "/on/i", $_SERVER["HTTPS"] ) )
			$proto = 1 ;

		if ( $theflag )
		{
			global $CONF ;

			if ( $proto )
				return preg_replace( "/http:\/\//i", "https://", $CONF["BASE_URL"] ) ;
			else
				return $CONF["BASE_URL"] ;
		}
		else
			return $proto ;
	}

	function Util_Functions_Bytes( $bytes )
	{
		$string = "" ;

		$kils = round ( $bytes/1000 ) ;
		$kil_re = ( $bytes % 1000 ) ;

		if ( $kils >= 1000 )
		{
			$megs = floor ( $kils/1000 ) ;
			$meg_re = ( $kils % 1000 ) ;
			$meg_per = round( $meg_re/1000 ) ;
			$megs_final = $megs + $meg_per ;
			$string = "$megs_final M" ;
		}
		elseif ( ( $bytes < 1000 ) && ( $bytes ) )
			$string = "$bytes byte" ;
		else if ( $bytes )
			$string = "$kils k" ;
		else
			$string = "0 byte" ;

		return $string ;
	}

	function Util_Functions_Page( $page, $index, $page_per, $total, $url, $query )
	{
		global $text ;
		if ( !isset( $text ) )
			$text = "" ;
		$string = "" ;
		
		$string .= "<div class=\"page_focus\">Page: </div>" ;
		$pages = $remainder = 0 ;

		$remainder = ( $total % $page_per ) ;
		$pages = floor( $total/$page_per ) ;
		$pages = ( $remainder ) ? $pages + 1 : $pages ;

		$span = 10 ;
		$remainder = ( $pages % $span ) ;
		$groups = floor( $pages/$span ) ;
		$groups = ( $remainder ) ? $groups + 1 : $groups ;
		$start = ( $index * $span ) ;
		$end = $start + $span ;

		$group_prev = "" ;
		if ( $index > 0 )
		{
			$c = $start - $span ;
			$new_index = $index - 1 ;
			$group_prev = "<div class=\"page\" onClick=\"location.href='$url?page=$c&index=$new_index&$query'\">...prev</div>" ;
		}

		$group_next = "" ;
		if ( $index < ( $groups - 1 ) )
		{
			$c = $end ;
			$new_index = $index + 1 ;
			$group_next = "<div class=\"page\" onClick=\"location.href='$url?page=$c&index=$new_index&$query'\">next...</div>" ;
		}

		$string .= $group_prev ;
		for ( $c = $start; $c < $end; ++$c )
		{
			if ( $c < $pages )
			{
				$this_page = $c + 1 ;

				if ( $c == $page )
					$string .= "<div class=\"page_focus\">$this_page</div>" ;
				else
					$string .= "<div class=\"page\" onClick=\"location.href='$url?page=$c&index=$index&$query'\">$this_page</div>" ;
			}
		}
		$string .= $group_next ;

		if ( preg_match( "/(op_trans.php)|(transcripts.php)/", $url ) )
			$string .= "<div style=\"float: left; padding-left: 10px;\"><form method=\"POST\" onSubmit=\"return false;\" id=\"form_search\">Search: <input type=\"text\" class=\"input_text_search\" size=\"25\" maxlength=\"25\" style=\"font-size: 10px;\" id=\"input_search\" value=\"$text\" onKeydown=\"input_text_listen_search(event);\"> <input type=\"button\" id=\"btn_page_search\" style=\"font-size: 10px;\" class=\"input_button\" value=\"go\" onClick=\"do_search('$url?$query')\"> <input type=\"button\" style=\"font-size: 10px;\" class=\"input_button\" value=\"reset\" onClick=\"location.href=reset_url\"></form></div>" ;

		$string .= "<div style=\"clear: both;\"></div>" ;

		return $string ;
	}

	function Util_Functions_Stars( $rating )
	{
		global $theme ;

		$base_url = Util_Functions_IsProtoHttps( 1 ) ;
		$star_img = "$base_url/themes/$theme/stars.png" ;

		$output = "<div style='width: 60px;'>" ;
		for ( $c = 1; $c <= $rating; ++$c )
			$output .= "<div style='float: left; width: 12px; height: 12px; background: url( $star_img ) no-repeat; background-position: 0px -12px;'></div>" ;
		for ( $c2 = $c; $c2 <= 5; ++$c2 )
			$output .= "<div style='float: left; width: 12px; height: 12px; background: url( $star_img ) no-repeat;'></div>" ;
		$output .= "<div style='clear: both;'></div></div>" ;
		
		return $output ;
	}

?>