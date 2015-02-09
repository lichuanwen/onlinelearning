<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/* Dev team: 615 */
	/****************************************/
	// STANDARD header for Setup
	if ( !is_file( "../web/config.php" ) ){ HEADER("location: install.php") ; exit ; }
	include_once( "../web/config.php" ) ;
	include_once( "../API/Util_Format.php" ) ;
	include_once( "../API/Util_IP.php" ) ;
	include_once( "../API/Util_Error.php" ) ;
	include_once( "../API/SQL.php" ) ;
	include_once( "../API/Util_Security.php" ) ;
	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	if ( !$admininfo = Util_Security_AuthSetup( $dbh, $ses ) ){ ErrorHandler( 608, "Invalid setup session or session has expired.", $PHPLIVE_FULLURL, 0, Array() ) ; }
	// STANDARD header end
	/****************************************/

	include_once( "../API/Util_Vals.php" ) ;

	$error = "" ; $dev = 0 ;
	$geo_dir = "$CONF[DOCUMENT_ROOT]/addons/geo_data" ;
	$VERSION_GEO = ( isset( $CONF["geo_v"] ) ) ? $CONF["geo_v"] : 0 ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$apikey = Util_Format_Sanatize( Util_Format_GetVar( "apikey" ), "ln" ) ;
	$jump = ( Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) ) ? Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) : "geoip" ;
	
	if ( $action == "import_geo_files" )
	{
		$json_data = $next_file = $error = "" ;

		if ( !is_dir( $geo_dir ) )
			$json_data = "json_data = { \"status\": 0, \"error\": \"Directory addons/geo_data/ does not exist.\" };" ;
		else if ( !is_file( "$geo_dir/VERSION.php" ) )
			$json_data = "json_data = { \"status\": 0, \"error\": \"GeoIP Addon is invalid.  Download the latest GeoIP Addon.\" };" ;
		else
		{
			$index = Util_Format_Sanatize( Util_Format_GetVar( "index" ), "n" ) * 1 ;
			$index_next = $index + 1 ;

			// if first pass, empty out the database for the new inserts. truncate again for good measure
			if ( !$index )
			{
				if ( !$dev )
				{
					$query = "TRUNCATE TABLE p_geo_bloc" ;
					database_mysql_query( $dbh, $query ) ;

					$query = "TRUNCATE TABLE p_geo_loc" ;
					database_mysql_query( $dbh, $query ) ;
				}
			}

			if ( !$error )
			{
				$files = Array() ;
				$dh = opendir( $geo_dir ) ;
				while( false !== ( $file = readdir( $dh ) ) )
				{
					if ( $file != "." && $file != ".." && $file != "README.txt" && $file != "VERSION.php" && $file != "COPYRIGHT.txt" )
						$files[] = $file ;
				}
				closedir( $dh ) ;

				asort( $files ) ;
				$total_files = count( $files ) ;
				for( $c = 0; $c < $total_files; ++$c )
				{
					$file = $files[$c] ;

					if ( $c == $index )
					{
						if ( !$dev )
							import_file( $dbh, "$geo_dir/$file" ) ;
						$percent = floor( $c/count($files) * 100 ) ;

						$json_data = "json_data = { \"status\": 0, \"index\": $index_next, \"total\": $total_files, \"percent\": $percent, \"file\": \"$file\" };" ;
					}
				}

				if ( !$json_data )
				{
					include_once( "$geo_dir/VERSION.php" ) ;

					Util_Vals_WriteToConfFile( "geo", "1" ) ;  $CONF["geo"] = 1 ;
					Util_Vals_WriteToConfFile( "geo_v", $VERSION_GEO ) ;
					$json_data = "json_data = { \"status\": 1 };" ;
				}
			}
			else
				$json_data = "json_data = { \"status\": 0, \"error\": \"$error\" };" ;
		}

		if ( isset( $dbh ) && isset( $dbh['con'] ) )
			database_mysql_close( $dbh ) ;

		print "$json_data" ;
		exit ;
	}
	else if ( $action == "update_api" )
	{
		if ( strlen( $apikey ) == 39 )
		{
			$error = ( Util_Vals_WriteToConfFile( "geo", "$apikey" ) ) ? "" : "Could not write to config file." ;
			if ( !$error )
				$json_data = "json_data = { \"status\": 1 };" ;
			else
				$json_data = "json_data = { \"status\": 0, \"error\": \"Could not write to conf file.\" };" ;
		}
		else
			$json_data = "json_data = { \"status\": 0, \"error\": \"API Key format is invalid.\" };" ;

		print "$json_data" ;
		exit ;
	}
	else if ( $action == "check_dir" )
	{
		if ( !is_dir( $geo_dir ) )
			$json_data = "json_data = { \"status\": 1 };" ;
		else
			$json_data = "json_data = { \"status\": 0 };" ;

		print "$json_data" ;
		exit ;
	}
	else if ( $action == "clear" )
	{
		if ( !$dev )
		{
			$query = "TRUNCATE TABLE p_geo_bloc" ;
			database_mysql_query( $dbh, $query ) ;

			$query = "TRUNCATE TABLE p_geo_loc" ;
			database_mysql_query( $dbh, $query ) ;
		}

		Util_Vals_WriteToConfFile( "geo", "" ) ;
		Util_Vals_WriteToConfFile( "geo_v", "" ) ;
		database_mysql_close( $dbh ) ;
		HEADER( "location: ./extras_geo.php?ses=$ses&action=cleared" ) ;
		exit ;
	}
	else if ( $action == "lookup" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Hash.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/IPs/Util.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/GeoIP/get.php" ) ;

		$ip = Util_Format_Sanatize( Util_Format_GetVar( "ip" ), "ln" ) ;

		if ( preg_match( "/^\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b$/", $ip ) )
		{
			$countries = Util_Hash_Countries() ;
			LIST( $ip_num, $network ) = UtilIPs_IP2Long( $ip ) ;
			$geoinfo = GeoIP_get_GeoInfo( $dbh, $ip_num, $network ) ;
			database_mysql_close( $dbh ) ;
			
			$country = ( isset( $geoinfo["country"] ) && $geoinfo["country"] ) ? $geoinfo["country"] : "unknown" ;
			$country_name = ( isset( $geoinfo["country"] ) && $geoinfo["country"] ) ? $countries[$geoinfo["country"]] : "Location Unknown" ;
			$region = ( isset( $geoinfo["region"] ) && $geoinfo["region"] ) ? $geoinfo["region"] : "-" ;
			$city = ( isset( $geoinfo["city"] ) && $geoinfo["city"] ) ? $geoinfo["city"] : "-" ;
			$latitude = ( isset( $geoinfo["latitude"] ) && $geoinfo["latitude"] ) ? $geoinfo["latitude"] : 28.613459424004414 ;
			$longitude = ( isset( $geoinfo["longitude"] ) && $geoinfo["longitude"] ) ? $geoinfo["longitude"] : -40.4296875 ;

			$country = strtolower( $country ) ;

			$json_data = "json_data = { \"status\": 1, \"country\": \"$country\", \"country_name\": \"$country_name\", \"region\": \"$region\", \"city\": \"$city\", \"latitude\": \"$latitude\", \"longitude\": \"$longitude\" };" ;
		}
		else
			$json_data = "json_data = { \"status\": 0, \"error\": \"Invalid IP address format.\" };" ;

		print $json_data ;
		exit ;
	}

	function import_file( $dbh, $thefile )
	{
		$queries = Array() ;

		$fh = fopen( $thefile, "rb" ) ;
		while( !feof( $fh ) )
		{
			$query = preg_replace( "/[`;]/", "", rtrim( fgets( $fh ) ) ) ;
			if ( $query )
				$queries[] = $query ;
		}
		fclose( $fh ) ;

		for ( $c = 0; $c < count( $queries ); ++$c )
		{
			$query = $queries[$c] ;
			database_mysql_query( $dbh, $query ) ;
		}
	}
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> PHP Live! Support <?php echo $VERSION ?> </title>

<meta name="description" content="PHP Live! Support <?php echo $VERSION ?>">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8"> 

<link rel="Stylesheet" href="../css/setup.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/tooltip.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		toggle_menu_setup( "extras" ) ;
		show_div( "<?php echo $jump ?>" ) ;

		<?php if ( isset( $CONF["geo"] ) && $CONF["geo"] && ( strlen( $CONF["geo"] ) != 39 ) ): ?>
		$('#div_maps_steps').show() ;
		<?php else: ?>
		$('#div_maps_update').show() ;
		<?php endif; ?>

		if ( "<?php echo $error ?>" )
			do_alert( 0, "<?php echo $error ?>" ) ;
		else if ( "<?php echo $action ?>" == "update" )
			do_alert( 1, "Success" ) ;
		else if ( "<?php echo $action ?>" == "cleared" )
			do_alert( 1, "GeoIP addon has been reset." ) ;
	});

	function confirm_import()
	{
		if ( confirm( "This process may take up to 10 minutes. Continue?" ) )
		{
			$(window).scrollTop(0) ;
			check_geo_files(0, 0) ;
		}
	}

	function check_geo_files( theindex, thepercent )
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		$('#div_import').show() ;
		$('body').css({'overflow': 'hidden'}) ;
		$('#data_percent').html( thepercent+"% complete " ) ;

		if ( !theindex )
			$('#div_import_status').append("Beginning import...<br>") ;

		$.post("./extras_geo.php", { action: "import_geo_files", index: theindex, ses: "<?php echo $ses ?>", unique: unique },  function(data){
			eval( data ) ;

			if ( !json_data.status && ( typeof( json_data.error ) == "undefined" ) )
			{
				var index_display = theindex + 1 ;
				$('#div_import_status').append("Importing file: "+json_data.file+" ("+index_display+":"+json_data.total+")<br>") ;
				$('#div_import_status').attr( "scrollTop", $('#div_import_status').attr( "scrollHeight" ) ) ;
				check_geo_files( theindex+1, json_data.percent ) ;
			}
			else if ( json_data.status == 1 )
			{
				$('#data_percent').html( "100% complete " ) ;
				$('#img_loading').hide() ;
				$('#btn_close').show() ;
				do_alert( 1, "Import Success!" ) ;
			}
			else
			{
				$('#div_import').hide() ;
				$('body').css({'overflow': 'visible'}) ;
				do_alert(0, json_data.error) ;
			}
		});
	}

	function submit_key()
	{
		var unique = unixtime() ;
		var json_data = new Object ;
		var apikey = $('#apikey').val() ;

		if ( !apikey )
			do_alert( 0, "Blank API Key is invalid." ) ;
		else if ( apikey.length != 39 )
			do_alert( 0, "API Key format is invalid." ) ;
		else
		{
			$.post("./extras_geo.php", { action: "update_api", apikey: apikey, ses: "<?php echo $ses ?>", unique: unique },  function(data){
				eval( data ) ;

				if ( json_data.status )
					location.href = "./extras_geo.php?ses=<?php echo $ses ?>&jump=geomap" ;
				else
				{
					do_alert( 0, jaon_data.error ) ;
				}
			});
		}
	}

	function reset_addon( theflag )
	{
		if ( theflag )
		{
			$('#div_reset_btn').hide() ;
			$('#div_reset').show() ;
		}
		else
		{
			$('#div_reset').hide() ;
			$('#div_reset_btn').show() ;
		}
	}

	function reset_addon_doit()
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		if ( confirm( "Reset and Clear the GeoIP addon?" ) )
		{
			$.post("./extras_geo.php", { action: "check_dir", ses: "<?php echo $ses ?>", unique: unique },  function(data){
				eval( data ) ;

				if ( json_data.status )
					location.href = "./extras_geo.php?ses=<?php echo $ses ?>&action=clear" ;
				else
				{
					do_alert( 0, "Error: The geo_data/ directory has not been deleted." ) ;
					$('#div_error').show() ;
				}
			});
		}
	}

	function show_div( thediv )
	{
		var divs = Array( "geoip", "geomap" ) ;
		for ( c = 0; c < divs.length; ++c )
		{
			$('#div_'+divs[c]).hide() ;
			$('#menu_'+divs[c]).removeClass('op_submenu_focus').addClass('op_submenu') ;
		}

		$('#div_'+thediv).show() ;
		$('#menu_'+thediv).removeClass('op_submenu').addClass('op_submenu_focus') ;
	}

	function lookup_ip( theip )
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		$('#geoip_output').html('<img src="../pics/loading_fb.gif" width="16" height="11" border="0" alt="">') ;

		if ( !theip )
		{
			$('#geoip_output').html("") ;
			do_alert( 0, "Blank IP is invalid." ) ;
		}
		else
		{
			$('#btn_lookup').attr("disabled", true) ;

			$.post("./extras_geo.php", { action: "lookup", ses: "<?php echo $ses ?>", ip: theip, unique: unique },  function(data){
				eval( data ) ;

				if ( json_data.status )
				{
					var output = "<img src=\"../pics/maps/"+json_data.country+".gif\" width=\"18\" height=\"12\" border=\"0\" alt=\""+json_data.country_name+"\" title=\""+json_data.country_name+"\"> "+json_data.country_name+" | Region: "+json_data.region+" | City: "+json_data.city+" | Latitude: "+json_data.latitude+" | Longitude: "+json_data.longitude ;
					$('#geoip_output').html( output ) ;
				}
				else
				{
					$('#geoip_output').html("") ;
					do_alert( 0, json_data.error ) ;
				}

				$('#btn_lookup').attr("disabled", false) ;
			});
		}
	}

//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu" onClick="location.href='extras.php?ses=<?php echo $ses ?>'" id="menu_external">External URLs</div>
			<div class="op_submenu_focus" onClick="show_div('geoip')" id="menu_geoip">GeoIP</div>
			<div class="op_submenu" onClick="show_div('geomap')" id="menu_geomap">Google Maps</div>
			<?php if ( is_file( "../addons/smtp/smtp.php" ) ): ?><div class="op_submenu" onClick="location.href='../addons/smtp/smtp.php?ses=<?php echo $ses ?>'" id="menu_smtp"><img src="../pics/icons/email.png" width="12" height="12" border="0" alt=""> SMTP</div><?php endif ; ?>
			<div class="op_submenu" onClick="location.href='extras.php?ses=<?php echo $ses ?>&jump=apis'" id="menu_apis">Dev APIs</div>
			<div style="clear: both"></div>
		</div>

		<form method="POST" action="extras_geo.php?submit" enctype="multipart/form-data">
		<input type="hidden" name="action" value="update">
		<input type="hidden" name="ses" value="<?php echo $ses ?>">

		<div style="text-align: justify; margin-top: 25px;">
			<div id="div_geoip">
				<div>GeoIP is the identification of the real-world geographic location of an IP address. Enabling this feature will display a country flag, the region and city of an IP address throughout various areas of the system.  To enable this feature, complete the following step:</div>

				<?php if ( !isset( $CONF["geo"] ) || !$CONF["geo"] ): ?>
				<div style="margin-top: 15px;" class="edit_title">Activate the GeoIP addon:</div>
				<div style="margin-top: 15px;">
					<table cellspacing=0 cellpadding=0 border=0 width="100%">
					<tr>
						<td width="33%" valign="top" style="padding-right: 5px;">
							<div style="height: 210px;" class="info_info">
								<div style="font-size: 14px; font-weight: bold;"><span style="color: #F38725;">Step 1:</span> Download</div>
								<div style="margin-top: 5px;">
									Login to your OSI Codes Inc. client area and download the compressed GeoIP Addon file (~27 Megs GZ compressed).

									<div style="margin-top: 15px;"><a href="http://www.phplivesupport.com/r.php?r=login" target="new">Login and Download the GeoIP Addon</a></div>
								</div>
							</div>
						</td>
						<td width="33%" valign="top" style="padding-right: 5px;">
							<div style="height: 210px;" class="info_info">
								<div style="font-size: 14px; font-weight: bold;"><span style="color: #F38725;">Step 2:</span> Extract</div>
								<div style="margin-top: 5px;">
									The Addon file has been compress twice for minimal file size.  First decompression will produce the file <code>"geo_data.tar"</code>.  Second decompression of the decompressed file <code>"geo_data.tar"</code> will produce the actual GeoIP Addon data folder <code>"geo_data/"</code> containing all the data files.  Extract the entire <code>"geo_data/"</code> folder to your computer.

									<div style="margin-top: 15px;">File decompression software such as <a href="http://www.winzip.com" target="new">WinZip</a> is needed to decompress the GeoIP Addon file.</div>
								</div>
							</div>
						</td>
						<td width="33%" valign="top" style="">
							<div style="height: 210px;" class="info_info">
								<div style="font-size: 14px; font-weight: bold;"><span style="color: #F38725;">Step 3:</span> FTP</div>
								<div style="margin-top: 5px;">
									FTP the entire <code>"geo_data/"</code> folder to your server and place it inside the <code>addons/</code> directory of your PHP Live! system.
									
									<div style="margin-top: 15px;"><code>phplive/addons/geo_data/</code></div>

									<div style="margin-top: 15px;">FTP of the <code>"geo_data/"</code> folder may take up to 15 minutes as there are over 100 Megs of data.</div>
								</div>

								<div style="margin-top: 15px;"><button type="button" onClick="confirm_import()" class="btn">I'm ready.  Import the GeoIP data.</button></div>
							</div>
						</td>
					</tr>
					</table>
				</div>

				<?php else: ?>
				<div style="margin-top: 15px;">
					<div class="edit_title"><img src="../pics/icons/check.png" width="16" height="16" border="0" alt=""> GeoIP is enabled.</div>
					<div style="margin-top: 15px;">Throughout various areas of the PHP Live! system, you'll now notice a map icon with the apporoximate region and city for each visitor IP address.</div>

					<div id="div_geoip_lookup" style="margin-top: 15px;">
						<div class="op_submenu_focus" style="background: #F3F3F3;">Quick IP Lookup</div>
						<div style="clear: both;"></div>
						<div class="info_info">
							IP Address (your current IP: <span class="txt_orange"><?php echo Util_IP_GetIP() ?></span>)<br>
							<span style="font-size: 10px;">* support for IPv6 will be available in the near future</span>

							<div style="margin-top: 10px;">
								<table cellspacing=0 cellpadding=0 border=0>
								<tr>
									<td><input type="text" name="ip_addy" id="ip_addy" size="20" maxlength="45" onKeyPress="return numbersonly(event)"> &nbsp; <input type="button" onClick="lookup_ip($('#ip_addy').val())" value="Lookup" class="btn" id="btn_lookup"></td>
									<td style="padding-left: 25px;"><div id="geoip_output"></div></div>
								</tr>
								</table>
							</div>
						</div>
						<div style="margin-top: 15px;"><img src="../pics/icons/reset.png" width="16" height="16" border="0" alt=""> <a href="JavaScript:void(0)" onClick="$('#div_geoip_reset').show(); $('#div_geoip_lookup').hide();">Reset GeoIP Addon</a></div>
					</div>
					<div id="div_geoip_reset" style="display: none; margin-top: 15px;">
						<div class="info_info">
							This action will clear the GeoIP data from the database and switch off the GeoIP feature (and Google Maps, if enabled).
							<ul style="margin-top: 10px;">
								<div style="padding: 5px;">Possible situations to reset the GeoIP addon:</div>
								<li> to update the GeoIP data to a newer version.
								<li> to save on disk space by deleting the GeoIP data from the database.
								<li> to import again due to errors.
							</ul>

							<div style="margin-top: 15px;"><b>Before Proceeding:</b><br>Manually delete the <code>"geo_data/"</code> directory from the <code>"phplive/addons/"</code> folder. To reactivate the GeoIP Addon, you'll want to follow the steps to import the data again.</div>
							<div style="margin-top: 25px;"><input type="button" value="Reset GeoIP Addon" onClick="reset_addon_doit()" class="btn"> &nbsp; <a href="JavaScript:void(0)" onClick="$('#div_geoip_reset').hide();$('#div_geoip_lookup').show();">cancel</a></div>
						</div>
					</div>

					<div style="margin-top: 25px; text-align: right;">GeoIP Data v.<?php echo $VERSION_GEO ?>  <img src="../pics/icons/disc.png" width="16" height="16" border="0" alt=""> <a href="http://www.phplivesupport.com/r.php?r=vcheck_geo&v=<?php echo base64_encode( $VERSION_GEO ) ?>&v_=<?php echo base64_encode( $VERSION ) ?>" target="new">Check for updates.</a></div>
				</div>

				<?php endif ; ?>
			</div>

			<div id="div_geomap" style="display: none; margin-top: 25px;">
				<div>Expand the GeoIP features with integrated Google Maps.  Google Maps will display the approximate location of an IP address on Google Maps.  Due to the <a href="https://developers.google.com/maps/terms" target="new">Google Maps API Terms of Service</a>, you'll want to signup for a Google API Key so that the integration is linked to your account.  API Key also enables the reporting features on the <a href="https://code.google.com/apis/console/" target="new">Google Code Console</a>.</div>

				<?php if ( !isset( $CONF["geo"] ) || !$CONF["geo"] ): ?>
					<div style="margin-top: 25px;">
						<span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> <a href="extras_geo.php?ses=<?php echo $ses ?>" style="color: #FFFFFF;">Enable GeoIP Addon</a> before activating Google Maps.</span>
					</div>
				<?php else: ?>
					<div style="margin-top: 25px;">
						<?php if ( strlen( $CONF["geo"] ) == 39 ): ?>
						<div class="edit_title" style="padding-bottom: 15px;"><img src="../pics/icons/check.png" width="16" height="16" border="0" alt="">  Google Maps is enabled.</div>
						<?php endif ; ?>
						<div id="div_maps_steps" style="display: none;">
							<table cellspacing=0 cellpadding=0 border=0 width="100%">
							<tr>
								<td width="33%" valign="top" style="padding: 5px;">
									<div style="height: 260px;" class="info_info">
										<div style="font-size: 14px; font-weight: bold;"><span style="color: #F38725;">Step 1:</span> Google Code</div>
										<div style="margin-top: 5px;">
											Login to the <a href="https://code.google.com/apis/console/" target="new">Google Code Console</a> and access the "Services" tab.

											<div style="margin-top: 15px;"><div style="font-size: 10px; padding: 3px; background: #DFDFDF; width: 255px;" class="round_top">screenshot</div><img src="../pics/setup/api_menu_services.gif" width="255" height="125" style="border: 1px solid #DFDFDF" class="round" alt=""></div>
										</div>
									</div>
								</td>
								<td width="33%" valign="top" style="padding: 5px;">
									<div style="height: 260px;" class="info_info">
										<div style="font-size: 14px; font-weight: bold;"><span style="color: #F38725;">Step 2:</span> API Enable</div>
										<div style="margin-top: 5px;">
											On the "Services" page, scroll down to the "Google Maps API v3" and enable this service.

											<div style="margin-top: 15px;"><div style="font-size: 10px; padding: 3px; background: #DFDFDF; width: 255px;" class="round_top">screenshot</div><img src="../pics/setup/api_menu_api_on.gif" width="255" height="125" style="border: 1px solid #DFDFDF" class="round" border="0" alt=""></div>
										</div>
									</div>
								</td>
								<td width="33%" valign="top" style="padding: 5px;">
									<div style="height: 260px;" class="info_info">
										<div style="font-size: 14px; font-weight: bold;"><span style="color: #F38725;">Step 3:</span> API Key</div>
										<div style="margin-top: 5px;">
											Click the "API Access" tab to locate your Google API Key:

											<div style="margin-top: 15px;"><div style="font-size: 10px; padding: 3px; background: #DFDFDF; width: 255px;" class="round_top">screenshot</div><img src="../pics/setup/api_menu_access.gif" width="255" height="125" style="border: 1px solid #DFDFDF" class="round" border="0" alt=""></div>
										</div>
									</div>
								</td>
							</tr>
							</table>

							<div style="margin-top: 15px; padding: 5px;">
								<table cellspacing=0 cellpadding=0 border=0 width="100%">
								<tr>
									<td valign="top">
										<div style="padding-right: 25px;">
											<div style="font-size: 14px; font-weight: bold;"><span style="color: #F38725;">Final Step:</span> Input API Key</div>
											<div style="margin-top: 5px;">On the "API Access" page, there is a box detailing your API credentials. Copy the "Simple API Access Key" and provide the key below to activate Google Maps:</div>

											<div style="margin-top: 15px;"><input type="text" id="apikey" name="apikey" class="input" size="50" maxlength="55" value="<?php echo ( isset( $CONF["geo"] ) && ( strlen( $CONF["geo"] ) == 39 ) ) ? $CONF["geo"] : "" ; ?>"></div>
											<div style="margin-top: 15px;"><input type="button" value="Update Google API Key" onClick="submit_key()" class="btn"> &nbsp; <span style="display: none;" id="text_cancel"><a href="JavaScript:void(0)" onClick="$('#div_maps_steps').hide(); $('#div_maps_update').show(); $(window).scrollTop(0) ;">cancel</a></span></div>
										</div>
									</td>
									<td width="125" valign="top"><div style=""><div style="font-size: 10px; padding: 3px; background: #DFDFDF; width: 255px;" class="round_top">screenshot</div><img src="../pics/setup/key.gif" width="560" height="125" style="border: 1px solid #DFDFDF" class="round" border="0" alt=""></div></td>
								</tr>
								</table>
							</div>
						</div>
						<div id="div_maps_update" style="display: none;">
							<div class="info_info">Google API Key: <input type="text" id="apikey" name="apikey" class="input" size="50" maxlength="55" value="<?php echo ( isset( $CONF["geo"] ) && ( strlen( $CONF["geo"] ) == 39 ) ) ? $CONF["geo"] : "" ; ?>" disabled></div>
							<div style="margin-top: 15px;"><img src="../pics/icons/key.png" width="16" height="16" border="0" alt=""> <a href="JavaScript:void(0)" onClick="$('#div_maps_steps').show(); $('#div_maps_update').hide(); $('#text_cancel').show();">Update Google API Key</a></div>
						</div>
						
					</div>
				<?php endif ; ?>

			</div>

		</div>
		</form>

<div id="div_import" style="display: none; position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; background: url( ../pics/bg_trans_white.png ) repeat; overflow: hidden; z-index: 20;">
	<div class="info_info" style="position: relative; width: 400px; margin: 0 auto; top: 130px;">
		<div style="background: url( ../pics/bg_trans_white.png ) repeat; padding: 25px;">
			<div style="font-size: 14px; font-weight: bold;">Importing GeoIP data: <span id="data_percent"></span><img src="../pics/loading_fb.gif" width="16" height="11" border="0" alt="" id="img_loading"></div>

			<div id="div_import_status" class="round info_default" style="margin-top: 15px; height: 120px; font-size: 10px; overflow: auto;"></div>
			<div style="margin-top: 15px;"><button type="button" id="btn_close" onClick="location.href='extras_geo.php?ses=<?php echo $ses ?>'" style="display: none;">Continue</button></div>
		</div>
	</div>
</div>

<?php include_once( "./inc_footer.php" ) ?>
