<?php
	if ( defined( 'API_Util_Vals' ) ) { return ; }	
	define( 'API_Util_Vals', true ) ;

	FUNCTION Util_Vals_WriteToConfFile( $valname, $val )
	{
		global $CONF ;

		$conf_vars = "\$CONF = Array() ;\n" ;
		foreach( $CONF as $key => $value )
		{
			// skip few values to conf file as they are set elsewhere
			if ( ( $key != "CHAT_IO_DIR" ) && ( $key != "TYPE_IO_DIR" ) )
			{
				if ( $key == "SQLPASS" )
					$CONF[$key] = stripslashes( $value ) ;

				if ( $key == $valname )
					$CONF[$key] = $val ;

				if ( $key == "DOCUMENT_ROOT" )
					$conf_vars .= "\$CONF['$key'] = addslashes( '".$CONF[$key]."' ) ;\n" ;
				else
					$conf_vars .= "\$CONF['$key'] = '".$CONF[$key]."' ;\n" ;
			}
		}

		// auto add new conf value if not exist
		if ( !isset( $CONF[$valname] ) )
			$conf_vars .= "\$CONF['$valname'] = '$val' ;\n" ;

		$conf_string = "< php\n	$conf_vars" ;
		$conf_string .= "	if ( phpversion() >= '5.1.0' ){ date_default_timezone_set( \$CONF['TIMEZONE'] ) ; }\n" ;
		$conf_string .= "	include_once( \"\$CONF[DOCUMENT_ROOT]/API/Util_Vars.php\" ) ;\n?>" ;
		$conf_string = preg_replace( "/< php/", "<?php", $conf_string ) ;

		if ( $fp = fopen( realpath( "$CONF[DOCUMENT_ROOT]/web/config.php" ), "w" ) )
		{
			fwrite( $fp, $conf_string, strlen( $conf_string ) ) ;
			fclose( $fp ) ;
			return true ;
		}
		else
			return false ;
	}

	FUNCTION Util_Vals_WriteToFile( $valname, $val )
	{
		global $CONF ;
		global $VALS ;

		if ( !isset( $VALS['sun'] ) )
			$VALS['sun'] = "center bottom" ;
		else if ( !isset( $VALS['OFFLINE'] ) )
			$VALS['OFFLINE'] = "" ;

		$conf_string = "" ;
		if ( $valname == "CHAT_SPAM_IPS" )
			$conf_string = "< php \$VALS = Array() ; \$VALS['CHAT_SPAM_IPS'] = \"$val\" ; \$VALS['TRAFFIC_EXCLUDE_IPS'] = \"$VALS[TRAFFIC_EXCLUDE_IPS]\" ; \$VALS['OFFLINE'] = '$VALS[OFFLINE]' ; \$VALS['sun'] = '$VALS[sun]' ; ?>" ;
		else if ( $valname == "OFFLINE" )
			$conf_string = "< php \$VALS = Array() ; \$VALS['CHAT_SPAM_IPS'] = \"$VALS[CHAT_SPAM_IPS]\" ; \$VALS['TRAFFIC_EXCLUDE_IPS'] = \"$VALS[TRAFFIC_EXCLUDE_IPS]\" ; \$VALS['OFFLINE'] = '$val' ; \$VALS['sun'] = '$VALS[sun]' ; ?>" ;
		else if ( $valname == "sun" )
			$conf_string = "< php \$VALS = Array() ; \$VALS['CHAT_SPAM_IPS'] = \"$VALS[CHAT_SPAM_IPS]\" ; \$VALS['TRAFFIC_EXCLUDE_IPS'] = \"$VALS[TRAFFIC_EXCLUDE_IPS]\" ; \$VALS['OFFLINE'] = '$val' ; \$VALS['sun'] = '$val' ; ?>" ;
		else
			$conf_string = "< php \$VALS = Array() ; \$VALS['CHAT_SPAM_IPS'] = \"$VALS[CHAT_SPAM_IPS]\" ; \$VALS['TRAFFIC_EXCLUDE_IPS'] = \"$val\" ; \$VALS['OFFLINE'] = '$VALS[OFFLINE]' ; \$VALS['sun'] = '$VALS[sun]' ; ?>" ;

		$conf_string = preg_replace( "/< php/", "<?php", preg_replace( "/  +/", "", $conf_string ) ) ;

		if ( $fp = fopen( "$CONF[DOCUMENT_ROOT]/web/vals.php", "w" ) )
		{
			fwrite( $fp, $conf_string, strlen( $conf_string ) ) ;
			fclose( $fp ) ;
			return true ;
		}
		else
			return false ;
	}

	FUNCTION Util_Vals_WriteVersion( $version )
	{
		global $CONF ;

		$version_string = "< php \$VERSION = \"$version\" ; ?>" ;
		$version_string = preg_replace( "/< php/", "<?php", $version_string ) ;
		$fp = fopen( "$CONF[DOCUMENT_ROOT]/web/VERSION.php", "w" ) ;
		fwrite( $fp, $version_string, strlen( $version_string ) ) ;
		fclose( $fp ) ;
	}
?>