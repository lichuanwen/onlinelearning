<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/* Dev team: 615 */
	/****************************************/
	// STANDARD header for Setup
	if ( !is_file( "../web/config.php" ) ){ HEADER("location: install.php") ; exit ; }
	include_once( "../web/config.php" ) ;
	include_once( "../API/Util_Format.php" ) ;
	include_once( "../API/Util_Error.php" ) ;
	include_once( "../API/SQL.php" ) ;
	include_once( "../API/Util_Security.php" ) ;
	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	if ( !$admininfo = Util_Security_AuthSetup( $dbh, $ses ) ){ ErrorHandler( 608, "Invalid setup session or session has expired.", $PHPLIVE_FULLURL, 0, Array() ) ; }
	// STANDARD header end
	/****************************************/

	include_once( "../API/Util_Vals.php" ) ;
	include_once( "../API/External/get.php" ) ;
	include_once( "../API/Depts/get.php" ) ;
	include_once( "../API/Ops/get.php" ) ;

	$https = $error = "" ;
	if ( isset( $_SERVER["HTTP_CF_VISITOR"] ) && preg_match( "/(https)/i", $_SERVER["HTTP_CF_VISITOR"] ) ) { $https = "s" ; }
	else if ( isset( $_SERVER["HTTP_X_FORWARDED_PROTO"] ) && preg_match( "/(https)/i", $_SERVER["HTTP_X_FORWARDED_PROTO"] ) ) { $https = "s" ; }
	else if ( isset( $_SERVER["HTTPS"] ) && preg_match( "/(on)/i", $_SERVER["HTTPS"] ) ) { $https = "s" ; }

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$jump = ( Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) ) ? Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) : "external" ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "ln" ) ;

	if ( $action == "submit" )
	{
		if ( $jump == "apis" )
		{
			include_once( "../API/Util_Vals.php" ) ;

			$error = ( Util_Vals_WriteToConfFile( "API_KEY",Util_Format_RandomString( 10 ) ) ) ? "" : "Could not write to config file." ;
		}
		else
		{
			include_once( "../API/External/put.php" ) ;
			include_once( "../API/External/remove.php" ) ;

			$extid = Util_Format_Sanatize( Util_Format_GetVar( "extid" ), "ln" ) ;
			$name = Util_Format_Sanatize( Util_Format_GetVar( "name" ), "ln" ) ;
			$url = Util_Format_Sanatize( Util_Format_GetVar( "url" ), "url" ) ;
			$opids = Util_Format_Sanatize( Util_Format_GetVar( "opids" ), "a" ) ;

			if ( $id = External_put_External( $dbh, $extid, $name, $url ) )
			{
				External_remove_AllExtOps( $dbh, $id ) ;
				for ( $c = 0; $c < count( $opids ); ++$c )
				{
					$opid = Util_Format_Sanatize( $opids[$c], "ln" ) ;
					External_put_ExtOp( $dbh, $id, $opid ) ;
				}
			}
			else	
				$error = "Name ($name) is already in use." ;
		}
	}
	else if ( $action == "delete" )
	{
		include_once( "../API/External/remove.php" ) ;

		$extid = Util_Format_Sanatize( Util_Format_GetVar( "extid" ), "ln" ) ;

		External_remove_External( $dbh, $extid ) ;
	}

	// auto write conf file if variables do not exist
	if ( !isset( $CONF["API_KEY"] ) )
	{
		$CONF["API_KEY"] = Util_Format_RandomString( 10 ) ;
		$error = ( Util_Vals_WriteToConfFile( "API_KEY", $CONF["API_KEY"] ) ) ? "" : "Could not write to config file." ;
	}

	$departments = Depts_get_AllDepts( $dbh ) ;
	if ( $deptid )
		$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;

	$operators = Ops_get_AllOps( $dbh ) ;
	$externals = External_get_AllExternal( $dbh ) ;
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
	var ops = new Array() ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		toggle_menu_setup( "extras" ) ;
		init_tooltips() ;

		show_div( "<?php echo $jump ?>" ) ;
		switch_dept_api( 0 ) ;
		switch_geoip_api( "csv" ) ;

		<?php if ( ( $action == "submit" ) && !$error ): ?>do_alert( 1, "Success" ) ;<?php endif ; ?>
	});

	function show_div( thediv )
	{
		var divs = Array( "external", "geoip", "apis" ) ;
		for ( c = 0; c < divs.length; ++c )
		{
			$('#extras_'+divs[c]).hide() ;
			$('#menu_'+divs[c]).removeClass('op_submenu_focus').addClass('op_submenu') ;
		}

		$('input#jump').val( thediv ) ;
		$('#extras_'+thediv).show() ;
		$('#menu_'+thediv).removeClass('op_submenu').addClass('op_submenu_focus') ;
	}

	function do_options( theoption, theextid, thename, theurl, theops )
	{
		if ( theoption == "edit" )
		{
			ops = theops.split( "," ) ;
			$( "input#extid" ).val( theextid ) ;
			$( "input#name" ).val( thename ) ;
			$( "input#url" ).val( theurl ) ;

			check_all( "undefined" ) ;
			for ( c = 0; c < ops.length; ++c )
				$('#ck_op_'+ops[c]).attr( 'checked', true ) ;

			location.href = "#a_edit" ;
		}
		else if ( theoption == "delete" )
		{
			if ( confirm( "Delete this External URL?" ) )
				location.href = "extras.php?ses=<?php echo $ses ?>&action=delete&extid="+theextid ;
		}
	}

	function do_submit()
	{
		var name = $( "input#name" ).val() ;
		var url = $( "input#url" ).val() ;
		var flag = 0 ;

		$( '*', 'body' ).each( function () {
			var div_name = $(this).attr('id') ;
			if ( div_name.indexOf( "ck_op_" ) == 0 )
			{
				if ( $(this).attr( 'checked' ) )
					flag = 1 ;
			}
		}) ;

		if ( name == "" )
			do_alert( 0, "Please provide the external url name." ) ;
		else if ( url == "" )
			do_alert( 0, "Please provide the external url." ) ;
		else if ( !flag )
		{
			// let it pass for now to reset if only 1 operator assigned previously
			//do_alert( 0, "At least one operator should be checked." ) ;
			$('#theform').submit() ;
		}
		else
			$('#theform').submit() ;
	}

	function launch_external( theextid, theurl )
	{
		var unique = unixtime() ;
		window.open(theurl, unique, 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1') ;
	}

	function check_all( theobject )
	{
		if ( ( typeof( theobject ) != "undefined" ) && ( theobject.checked ) )
		{
			$( '*', 'body' ).each( function () {
				var div_name = $(this).attr('id') ;
				if ( div_name.indexOf( "ck_op_" ) == 0 )
					$(this).attr( 'checked', true ) ;
			}) ;
		}
		else
		{
			$( '*', 'body' ).each( function () {
				var div_name = $(this).attr('id') ;
				if ( div_name.indexOf( "ck_op_" ) == 0 )
					$(this).attr( 'checked', false ) ;
			}) ;
		}
	}

	function reset_check_all()
	{
		$('#ck_op_all').attr( 'checked', false ) ;
	}

	function switch_dept_api( thedeptid )
	{
		$('#api_status').val( "<?php echo ( !preg_match( "/^(http)/", $CONF["BASE_URL"] ) ) ? "http$https:$CONF[BASE_URL]" : $CONF["BASE_URL"] ; ?>/ajax/status.php?akey=<?php echo $CONF["API_KEY"] ?>&deptid="+thedeptid ) ;
	}

	function switch_geoip_api( theformat )
	{
		$('#api_geoip').val( "<?php echo ( !preg_match( "/^(http)/", $CONF["BASE_URL"] ) ) ? "http$https:$CONF[BASE_URL]" : $CONF["BASE_URL"] ; ?>/http/geoip.php?akey=<?php echo $CONF["API_KEY"] ?>&f="+theformat+"&ip=124.108.31.255" ) ;
	}

	function gen_new_key()
	{
		var unique = unixtime() ;

		if ( confirm( "You'll want to update your API URLs once a new key is generated.  Continue?" ) )
		{
			location.href = "extras.php?ses=<?php echo $ses ?>&action=submit&jump=apis&"+unique ;
		}
	}

	function init_tooltips()
	{
		var help_tooltips = $('body').find( '.help_tooltip' ) ;
		help_tooltips.tooltip({
			event: "mouseover",
			track: true,
			delay: 0,
			showURL: false,
			showBody: "- ",
			fade: 0,
			extraClass: "stat"
		});
	}

//-->
</script>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu_focus" onClick="show_div('external')" id="menu_external">External URLs</div>
			<div class="op_submenu" onClick="location.href='extras_geo.php?ses=<?php echo $ses ?>'" id="menu_geoip">GeoIP</div>
			<div class="op_submenu" onClick="location.href='extras_geo.php?ses=<?php echo $ses ?>&jump=geomap'" id="menu_geomap">Google Maps</div>
			<?php if ( is_file( "../addons/smtp/smtp.php" ) ): ?><div class="op_submenu" onClick="location.href='../addons/smtp/smtp.php?ses=<?php echo $ses ?>'" id="menu_smtp"><img src="../pics/icons/email.png" width="12" height="12" border="0" alt=""> SMTP</div><?php endif ; ?>
			<div class="op_submenu" onClick="show_div('apis')" id="menu_apis">Dev APIs</div>
			<div style="clear: both"></div>
		</div>

		<div style="display: none; margin-top: 25px;" id="extras_external">
			<div>
				External URLs allow operators to easily access a webpage within the operator console.  URLs could be to "Client Search", "Trial Accounts", "Company Directory", "Order Search" or other helpful URLs.  The links will appear on the operator console footer menu.  <a href="JavaScript:void(0)" class="help_tooltip" title="- <img src='../pics/screens/external_url.gif' width='443' height='128' border='0'>" style="cursor: default;">Example Screenshot</a>
			</div>

			<?php if ( count( $operators ) > 0 ): ?>
				<form>
				<table cellspacing=0 cellpadding=0 border=0 width="100%" style="margin-top: 25px;">
				<tr>
					<td width="40"><div class="td_dept_header">&nbsp;</div></td>
					<td><div class="td_dept_header">Name</div></td>
					<td width="100%"><div class="td_dept_header">URL</div></td>
				</tr>
				<?php
					for ( $c = 0; $c < count( $externals ); ++$c )
					{
						$external = $externals[$c] ;
						$ops = External_get_ExtOps( $dbh, $external["extID"] ) ;

						$ops_string = $ops_js_string = "" ;
						for ( $c2 = 0; $c2 < count( $ops ); ++$c2 )
						{
							$op = $ops[$c2] ;
							$ops_string .= " <div class=\"li_op\">$op[name]</div>" ;
							$ops_js_string .= "$op[opID]," ;
						}
						$ops_js_string = substr_replace( $ops_js_string, "", -1 ) ;

						$edit_delete = "<div onClick=\"do_options( 'edit', $external[extID], '$external[name]', '$external[url]', '$ops_js_string' )\" style=\"cursor: pointer;\"><img src=\"../pics/btn_edit.png\" width=\"64\" height=\"23\" border=\"0\" alt=\"\"></div><div onClick=\"do_options( 'delete', $external[extID], '$external[name]', '$external[url]', '$ops_js_string' )\" style=\"margin-top: 10px; cursor: pointer;\"><img src=\"../pics/btn_delete.png\" width=\"64\" height=\"23\" border=\"0\" alt=\"\"></div>" ;

						$td1 = "td_dept_td" ;

						print "
							<tr>
								<td class=\"$td1\" nowrap>$edit_delete</td>
								<td class=\"$td1\" nowrap>$external[name]</td>
								<td class=\"$td1\">
									<div style=\"margin-bottom: 5px;\"><a href=\"JavaScript:void(0)\" onClick=\"launch_external( '$external[extID]', '$external[url]' )\">$external[url]</a></div>
									$ops_string
								</td>
							</tr>
						" ;
					}
					if ( $c == 0 )
						print "<tr><td colspan=7 class=\"td_dept_td\">blank results</td></tr>" ;
				?>
				</table>
				</form>

				<div style="padding: 5px; margin-top: 55px;">
					<a name="a_edit"></a><div class="edit_title">Create/Edit External URL <span class="txt_red"><?php echo $error ?></span></div>
					<div style="margin-top: 10px;">
						<form method="POST" action="extras.php?submit" id="theform">
						<input type="hidden" name="action" value="submit">
						<input type="hidden" name="ses" value="<?php echo $ses ?>">
						<input type="hidden" name="extid" id="extid" value="0">
						<table cellspacing=0 cellpadding=5 border=0>
						<tr>
							<td>Name<br><input type="text" name="name" id="name" size="50" maxlength="15" value=""></td>
						</tr>
						<tr>
							<td>Target URL<br><input type="text" name="url" id="url" size="120" maxlength="255" value=""></td>
						</tr>
						<tr>
							<td>
								<div><img src="../pics/icons/info.png" width="12" height="12" border="0" alt=""> It is recommended that each operator has max THREE external URLs to maintain proper operator console formatting.</div>
								<div style="margin-top: 15px;">Operator(s) who can access this external URL:</div>
								<div id="li_ops" style="margin-top: 5px;">
								<div class="li_op_focus round"><input type="checkbox" id="ck_op_all" name="opids[]" value="all" onClick="check_all(this)"> Check All</div>
								<?php
									for ( $c = 0; $c < count( $operators ); ++$c )
									{
										$operator = $operators[$c] ;

										if ( $operator["name"] != "Archive" )
											print "<div class=\"li_op round\"><input type=\"checkbox\" id=\"ck_op_$operator[opID]\" name=\"opids[]\" value=\"$operator[opID]\" onClick=\"reset_check_all()\"> $operator[name]</div>" ;
									}
								?>
								<div style="clear: both;"></div>
								</div>
							</td>
						</tr>
						<tr>
							<td> <div style="padding-top: 25px;"><input type="button" value="Submit" onClick="do_submit()" class="btn"> <input type="reset" value="Reset" onClick="$( 'input#extid' ).val(0)" class="btn"></div></td>
						</tr>
						</table>
						</form>
					</div>
				</div>
			<?php else: ?>
			<div style="margin-top: 15px;"><span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> Create an <a href="JavaScript:void(0)" onClick="show_div('main')" style="color: #FFFFFF;">Operator</a> to continue.</span></div>
			<?php endif ;?>
		</div>

		<div style="display: none; margin-top: 25px;" id="extras_apis">
			<div>
				The following HTTP APIs are for developers to aid with website or application integration.  The APIs will retrieve a specific response from the chat system.  Some coding knowledge is needed, such as <a href="http://php.net/manual/en/function.fopen.php" target="new">PHP fopen()</a> or <a href="http://php.net/manual/en/book.curl.php" target="new">PHP curl</a>.  Other languages, such as ASP or Java can utilize the HTTP APIs by simply calling the query URL and obtaining the output value.
			</div>

			<div style="margin-top: 25px;">
				<div class="info_info"><input type="button" value="Generate New Key" onClick="gen_new_key()"> HTTP APIs require an API Key (akey) for authentication.  At times you'll want to generate a new API Key for security.</div>

				<div style="margin-top: 25px;">
					<div>The following HTTP API URL returns a 1 (online) or 0 (offline) depending on the department chat availability status.</div>
					<div style="margin-top: 5px;">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td class="td_dept_td">
								<select name="deptid" id="deptid" style="width: 200px; font-size: 16px; background: #D4FFD4; color: #009000;" OnChange="switch_dept_api( this.value )">
								<option value="0">All Departments</option>
								<?php
									for ( $c = 0; $c < count( $departments ); ++$c )
									{
										$department = $departments[$c] ;
										if ( $department["name"] != "Archive" )
										{
											$selected = ( $deptid == $department["deptID"] ) ? "selected" : "" ;
											print "<option value=\"$department[deptID]\" $selected>$department[name]</option>" ;
										}
									}
								?>
								</select>
							</td>
							<td class="td_dept_td"><input type="text" class="input" style="background: #CEE0F4;" id="api_status" readonly size="110" value="" onMouseDown="setTimeout(function(){ $('#api_status').select(); }, 200);"></td>
						</tr>
						</table>
					</div>

					<div style="margin-top: 25px;">The following HTTP API URL will return the number of visitors currently on your website. (tracks pages that has the <a href="code.php?ses=<?php echo $ses ?>">HTML Code</a>)</div>
					<div style="margin-top: 5px;">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td class="td_dept_td">
								<select style="width: 200px; font-size: 16px; background: #D4FFD4; color: #009000;">
								<option value="0">All Departments</option>
								</select>
							</td>
							<td class="td_dept_td"><input type="text" class="input" style="background: #CEE0F4;" id="api_traffic" readonly size="110" value="<?php echo ( !preg_match( "/^(http)/", $CONF["BASE_URL"] ) ) ? "http$https:$CONF[BASE_URL]" : $CONF["BASE_URL"] ; ?>/http/traffic.php?akey=<?php echo $CONF["API_KEY"] ?>" onMouseDown="setTimeout(function(){ $('#api_traffic').select(); }, 200);"></td>
						</tr>
						</table>
					</div>

					<div style="margin-top: 25px;">The following HTTP API URL will return the provided IP's GeoIP location.  Change the <i>ip=</i> to an IP of your choice.  The output will be in the format of:<br> <code>country abbreviation,country name,region,city,latitude,longitude</code></div>
					<div style="margin-top: 5px;">
						<table cellspacing=0 cellpadding=0 border=0>
						<?php if ( $geoip ): ?>
						<tr>
							<td class="td_dept_td">
								<select name="f" id="f" style="width: 200px; font-size: 16px; background: #A3B0D0; color: #FFFFFF; border: 1px solid #6D7892;" OnChange="switch_geoip_api( this.value )">
								<option value="csv">Comma Seperated</option>
								<option value="json">Json Format</option>
								</select>
							</td>
							<td class="td_dept_td"><input type="text" class="input" style="background: #CEE0F4;" id="api_geoip" readonly size="110" value="" onMouseDown="setTimeout(function(){ $('#api_geoip').select(); }, 200);"></td>
						</tr>
						<?php else: ?>
						<tr><td class="td_dept_td" style="text-shadow: none;"><span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> <a href="extras_geo.php?ses=<?php echo $ses ?>" style="color: #FFFFFF;">Enable GeoIP Addon</a> for the GeoIP API.</span></td></tr>
						<?php endif ; ?>
						</table>
					</div>

				</div>
			</div>
		</div>

<?php include_once( "./inc_footer.php" ) ?>

