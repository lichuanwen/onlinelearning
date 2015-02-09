<?php
	if ( defined( 'API_Util_Upload' ) ) { return ; }	
	define( 'API_Util_Upload', true ) ;

	function Util_Upload_File( $icon, $deptid )
	{
		global $upload_dir ;
		global $CONF ;
		$now = time() ;
		$extension = $error = $filename = "" ;

		if ( !defined( 'API_Util_Vals' ) )
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;
		//ini_set( 'upload_tmp_dir', '$CONF[DOCUMENT_ROOT]/web/temp' ) ;

		if ( isset( $_FILES[$icon]['size'] ) )
		{
			$filesize = $_FILES[$icon]['size'] ;
			$filetype = $_FILES[$icon]['type'] ;
			$errorno = $_FILES[$icon]['error'] ;

			if ( $errorno == UPLOAD_ERR_OK )
			{
				if ( preg_match( "/gif/i", $filetype ) )
					$extension = "GIF" ;
				else if ( preg_match( "/jpeg/i", $filetype ) )
					$extension = "JPEG" ;
				else if ( preg_match( "/png/i", $filetype ) )
					$extension = "PNG" ;

				if ( $extension )
				{
					if ( preg_match( "/(online)|(offline)|(initiate)|(logo)/", $icon ) )
					{
						$filename = $icon."_$deptid" ;

						if ( is_file( "$upload_dir/$filename.PNG" ) )
							unlink( "$upload_dir/$filename.PNG" ) ;
						else if ( is_file( "$upload_dir/$filename.JPEG" ) )
							unlink( "$upload_dir/$filename.JPEG" ) ;
						else if ( is_file( "$upload_dir/$filename.GIF" ) )
							unlink( "$upload_dir/$filename.GIF" ) ;

						$filename = $icon."_$deptid.$extension" ;
					}
					else
						$filename = "$icon.$extension" ;

					if( move_uploaded_file( $_FILES[$icon]['tmp_name'], "$upload_dir/$filename" ) )
					{
						chmod( "$upload_dir/$filename", 0777 ) ;
						if ( preg_match( "/(online)|(offline)|(logo)|(initiate)/", $icon ) && !$deptid )
							$error = ( Util_Vals_WriteToConfFile( $icon, $filename ) ) ? "" : "Could not write to config file." ;
					}
					else
						$error = "Could not process uploading of files." ;
				}
				else
					$error = "Please provide a valid image file.  GIF, PNG or JPEG formats only." ;
			}
			else if ( $errorno == UPLOAD_ERR_NO_TMP_DIR )
				$error = "Upload temp dir not set or not writeable.  Check the value of \"upload_tmp_dir\" in the php.ini file." ;
			else if ( $errorno == UPLOAD_ERR_NO_FILE )
				$error = "Nothing to upload." ;
			else if ( $errorno == UPLOAD_ERR_INI_SIZE )
				$error = "The uploaded file exceeds the upload_max_filesize directive in php.ini" ;
			else if ( $errorno )
				$error = "Error in uploading. [errorno: $errorno]" ;
			else
				$error = "Error in uploading." ;
		}
		else
			$error = "Please provide a valid image file.  GIF, PNG or JPEG formats only." ;

		return $error ;
	}

	function Util_Upload_GetChatIcon( $base_url, $prefix, $deptid )
	{
		global $CONF ;
		global $upload_dir ;

		$now = time() ;
		if ( is_file( "$upload_dir/$prefix"."_$deptid.GIF" ) )
			return "$base_url/web/$prefix"."_$deptid.GIF?".$now ;
		else if ( is_file( "$upload_dir/$prefix"."_$deptid.JPEG" ) )
			return "$base_url/web/$prefix"."_$deptid.JPEG?".$now ;
		else if ( is_file( "$upload_dir/$prefix"."_$deptid.PNG" ) )
			return "$base_url/web/$prefix"."_$deptid.PNG?".$now ;
		else if ( is_file( "$upload_dir/$CONF[$prefix]" ) && $CONF["$prefix"] )
			return "$base_url/web/$CONF[$prefix]?".$now ;
		else
			return "$base_url/pics/icons/$prefix".".gif" ;
	}

	function Util_Upload_GetLogo( $base_url, $deptid )
	{
		global $CONF ;
		global $upload_dir ;
		global $theme ;

		if ( isset( $theme ) && $theme )
			$local_theme = $theme ;
		else
			$local_theme = $CONF["THEME"] ;

		$now = time() ;
		if ( is_file( "$upload_dir/logo"."_$deptid.GIF" ) )
			return "$base_url/web/logo"."_$deptid.GIF?".$now ;
		else if ( is_file( "$upload_dir/logo"."_$deptid.JPEG" ) )
			return "$base_url/web/logo"."_$deptid.JPEG?".$now ;
		else if ( is_file( "$upload_dir/logo"."_$deptid.PNG" ) )
			return "$base_url/web/logo"."_$deptid.PNG?".$now ;
		else if ( is_file( "$upload_dir/logo_0.GIF" ) )
			return "$base_url/web/logo_0.GIF?".$now ;
		else if ( is_file( "$upload_dir/logo_0.JPEG" ) )
			return "$base_url/web/logo_0.JPEG?".$now ;
		else if ( is_file( "$upload_dir/logo_0.PNG" ) )
			return "$base_url/web/logo_0.PNG?".$now ;
		else if ( is_file( "$CONF[DOCUMENT_ROOT]/themes/$local_theme/logo.png" ) )
			return "$base_url/themes/$local_theme/logo.png?".$now ;
		else
			return "$base_url/pics/logo.png" ;
	}

	function Util_Upload_GetInitiate( $base_url, $deptid )
	{
		global $CONF ;

		$now = time() ;
		if ( isset( $CONF["icon_initiate"] ) && $CONF["icon_initiate"] && is_file( "$CONF[DOCUMENT_ROOT]/web/$CONF[icon_initiate]" ) )
			return "$base_url/web/$CONF[icon_initiate]?".$now ;
		else if ( is_file( "$CONF[DOCUMENT_ROOT]/themes/initiate/initiate.gif" ) )
			return "$base_url/themes/initiate/initiate.gif?".$now ;
		else
			return "$base_url/pics/icons/initiate.gif" ;
	}

?>