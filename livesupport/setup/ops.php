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

	$error = "" ;

	include_once( "../API/Ops/get.php" ) ;
	include_once( "../API/Ops/update_itr.php" ) ;
	include_once( "../API/Depts/get.php" ) ;

	$https = "" ;
	if ( isset( $_SERVER["HTTP_CF_VISITOR"] ) && preg_match( "/(https)/i", $_SERVER["HTTP_CF_VISITOR"] ) ) { $https = "s" ; }
	else if ( isset( $_SERVER["HTTP_X_FORWARDED_PROTO"] ) && preg_match( "/(https)/i", $_SERVER["HTTP_X_FORWARDED_PROTO"] ) ) { $https = "s" ; }
	else if ( isset( $_SERVER["HTTPS"] ) && preg_match( "/(on)/i", $_SERVER["HTTPS"] ) ) { $https = "s" ; }

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$jump = Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) ;
	if ( !$jump )
		$jump = "main" ;

	if ( $action == "submit" )
	{
		include_once( "../API/Ops/put.php" ) ;
		include_once( "../API/Ops/get_ext.php" ) ;

		$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "ln" ) ;
		$rate = Util_Format_Sanatize( Util_Format_GetVar( "rate" ), "ln" ) ;
		$sms = Util_Format_Sanatize( Util_Format_GetVar( "sms" ), "ln" ) ;
		$op2op = Util_Format_Sanatize( Util_Format_GetVar( "op2op" ), "ln" ) ;
		$traffic = Util_Format_Sanatize( Util_Format_GetVar( "traffic" ), "ln" ) ;
		$viewip = Util_Format_Sanatize( Util_Format_GetVar( "viewip" ), "ln" ) ;
		$login = Util_Format_Sanatize( Util_Format_GetVar( "login" ), "ln" ) ;
		$password = Util_Format_Sanatize( Util_Format_GetVar( "password" ), "" ) ;
		$name = Util_Format_Sanatize( Util_Format_GetVar( "name" ), "ln" ) ;
		$email = Util_Format_Sanatize( Util_Format_GetVar( "email" ), "e" ) ;

		$total_ops = Ops_get_TotalOps( $dbh ) ;
	
		if ( isset( $VARS_MAX_OPS ) && ( $total_ops >= $VARS_MAX_OPS ) && !$opid )
			$error = "Max allowed operators have been reached." ;
		else
		{
			$error = Ops_put_Op( $dbh, $opid, 1, $rate, $sms, $op2op, $traffic, $viewip, $login, $password, $name, $email ) ;
			if ( is_numeric( $error ) ) { $error = "" ; }
		}
	}
	else if ( $action == "delete" )
	{
		include_once( "../API/Ops/remove.php" ) ;

		$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "ln" ) ;
		Ops_remove_Op( $dbh, $opid ) ;
	}
	else if ( $action == "submit_assign" )
	{
		include_once( "../API/Ops/put.php" ) ;

		$opids = Util_Format_GetVar( "opids" ) ;
		$deptids = Util_Format_GetVar( "deptids" ) ;

		for ( $c = 0; $c < count( $opids ); ++$c )
		{
			$opid = Util_Format_Sanatize( $opids[$c], "ln" ) ;
			for ( $c2 = 0; $c2 < count( $deptids ); ++$c2 )
			{
				$deptid = Util_Format_Sanatize( $deptids[$c2], "ln" ) ;
				$opinfo = Ops_get_OpInfoByID( $dbh, $opid ) ;
				$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
				Ops_put_OpDept( $dbh, $opid, $deptid, $deptinfo["visible"], $opinfo["status"] ) ;
			}
		}
	}

	Ops_update_itr_IdleOps( $dbh ) ;
	$operators = Ops_get_AllOps( $dbh ) ;
	$departments = Depts_get_AllDepts( $dbh ) ;
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

<script type="text/javascript">
<!--
	var global_div_list_height ;
	var global_div_form_height ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;
		$("body").show() ;

		init_menu() ;
		toggle_menu_setup( "ops" ) ;
		init_divs() ;
		init_op_dept_list() ;

		show_div( "<?php echo $jump ?>" ) ;

		<?php if ( $action && !$error ): ?>
		do_alert( 1, "Success" ) ;
		<?php elseif ( $error ): ?>
		do_alert( 0, "<?php echo $error ?>" ) ;
		<?php endif ; ?>
	});

	function init_divs()
	{
		global_div_list_height = $('#div_list').outerHeight() ;
		global_div_form_height = $('#div_form').outerHeight() ;
	}

	function init_op_dept_list() { <?php for ( $c = 0; $c < count( $departments ); ++$c ) { $department = $departments[$c] ; if ( $department["name"] != "Archive" ) { print "op_dept_moveup( $department[deptID], 0 ) ;" ; } } ?> }

	function do_edit( theopid, thename, theemail, thelogin, therate, thesms, theop2op, thetraffic, theviewip )
	{
		$( "input#opid" ).val( theopid ) ;
		$( "input#name" ).val( thename ) ;
		$( "input#email" ).val( theemail ) ;
		$( "input#login" ).val( thelogin ) ;
		$( "input#password" ).val( "php-live-support" ) ;
		$( "input#rate_"+therate ).attr( "checked", true ) ;
		$( "input#sms_"+thesms ).attr( "checked", true ) ;
		$( "input#op2op_"+theop2op ).attr( "checked", true ) ;
		$( "input#traffic_"+thetraffic ).attr( "checked", true ) ;
		$( "input#viewip_"+theviewip ).attr( "checked", true ) ;
		
		show_form() ;
	}

	function do_delete( theopid )
	{
		if ( confirm( "Really delete this operator and all their data?" ) )
			location.href = "ops.php?ses=<?php echo $ses ?>&action=delete&opid="+theopid ;
	}

	function do_submit()
	{
		var name = encodeURIComponent( $( "input#name" ).val() ) ;
		var email = $( "input#email" ).val() ;
		var login = encodeURIComponent( $( "input#login" ).val() ) ;
		var password = encodeURIComponent( $( "input#password" ).val() ) ;

		if ( name == "" )
			do_alert( 0, "Please provide a name." ) ;
		else if ( !check_email( email ) )
			do_alert( 0, "Please provide a valid email address." ) ;
		else if ( login == "" )
			do_alert( 0, "Please provide a login." ) ;
		else if ( password == "" )
			do_alert( 0, "Please provide a password." ) ;
		else if ( login == "<?php echo $admininfo["login"] ?>" )
			do_alert( 0, "Operator login cannot be the same as the setup admin login." ) ;
		else
		{
			email = encodeURIComponent( email ) ;
			$('#theform').submit() ;
		}
	}

	function show_div( thediv )
	{
		var divs = Array( "main", "assign", "report", "monitor", "online" ) ;

		if ( $('#div_form').is(':visible') )
			do_reset() ;

		for ( c = 0; c < divs.length; ++c )
		{
			$('#edit').hide() ;

			$('#ops_'+divs[c]).hide() ;
			$('#menu_ops_'+divs[c]).removeClass('op_submenu_focus').addClass('op_submenu') ;
		}

		if ( thediv == "main" )
			$('#edit').show() ;
		if ( thediv == "online" )
			$('#img_bulb').attr('src', '../pics/icons/bulb.png') ;
		else
			$('#img_bulb').attr('src', '../pics/icons/bulb_off.png') ;

		$('input#jump').val( thediv ) ;
		$('#ops_'+thediv).show() ;
		$('#menu_ops_'+thediv).removeClass('op_submenu').addClass('op_submenu_focus') ;
	}

	function op_dept_moveup( thedeptid, theopid )
	{
		$('#dept_ops_'+thedeptid).css({'opacity': 1, 'z-Index': -10}) ;

		$.ajax({
			type: "GET",
			url: "../ajax/setup_actions.php",
			data: "ses=<?php echo $ses ?>&action=moveup&deptid="+thedeptid+"&opid="+theopid+"&"+unixtime(),
			success: function(data){
				eval( data ) ;

				if ( json_data.ops != undefined )
				{
					var ops_string = "<table cellspacing=0 cellpadding=0 border=0 width=\"92%\">" ;
					for ( c = 0; c < json_data.ops.length; ++c )
					{
						var name = json_data.ops[c]["name"] ;
						var opid = json_data.ops[c]["opid"] ;
						var move_up = ( c ) ? "&nbsp; <a href=\"JavaScript:void(0)\" onClick=\"op_dept_moveup( "+thedeptid+", "+opid+" )\">move up</a>" : "&nbsp;" ;

						ops_string += "<tr><td class=\"td_dept_td\" nowrap><a href=\"JavaScript:void(0)\" onClick=\"op_dept_remove( "+thedeptid+", "+opid+" )\"><img src=\"../pics/btn_delete.png\" width=\"45\" height=\"16\" border=\"0\" alt=\"\"></a> "+move_up+"</td><td class=\"td_dept_td\">"+name+"</td></tr>" ;
					}
					if ( c == 0 )
						ops_string += "<tr><td colspan=7 class=\"td_dept_td\">blank results</td></tr>" ;
				}
				ops_string += "</table>" ;
				$('#dept_ops_'+thedeptid).html( ops_string ) ;
				setTimeout(function(){ $('#dept_ops_'+thedeptid).css({'opacity': 1, 'z-Index': 10}) ; }, 500) ;
			}
		});
	}

	function op_dept_remove( thedeptid, theopid )
	{
		$('#dept_ops_'+thedeptid).css({'opacity': 1, 'z-Index': -10}) ;

		$.ajax({
			type: "GET",
			url: "../ajax/setup_actions.php",
			data: "ses=<?php echo $ses ?>&action=op_dept_remove&deptid="+thedeptid+"&opid="+theopid+"&"+unixtime(),
			success: function(data){
				eval( data ) ;

				if ( json_data.status )
					op_dept_moveup( thedeptid, 0 ) ;
			}
		});
	}

	function remote_disconnect( theopid )
	{
		if ( confirm( "Remote disconnect operator console?" ) )
		{
			$('#remote_disconnect_notice').center().show() ;

			$.ajax({
				type: "GET",
				url: "../ajax/setup_actions.php",
				data: "ses=<?php echo $ses ?>&action=remote_disconnect&opid="+theopid+"&"+unixtime(),
				success: function(data){
					eval( data ) ;

					if ( json_data.status )
					{
						// 15 seconds of wait since console checks every 10 seconds
						setTimeout( function(){ location.href = 'ops.php?ses=<?php echo $ses ?>' ; }, 15000 ) ;
					}
					else
					{
						$('#remote_disconnect_notice').hide() ;
						do_alert( 0, "Could not remote disconnect console.  Please try again." ) ;
					}
				}
			});
		}
	}

	function launch_tools_op_status()
	{
		var url = "tools_op_status.php?ses=<?php echo $ses ?>" ;

		if ( <?php echo count( $operators ) ?> > 0 )
			window.open( url, "Operators", "scrollbars=yes,menubar=no,resizable=1,location=no,width=300,height=400,status=0" ) ;
		else
		{
			if ( confirm( "Operator account does not exist.  Create an operator?" ) )
				location.href = "ops.php?ses=<?php echo $ses ?>" ;
		}
	}

	function check_all_ops( theobject )
	{
		if ( ( typeof( theobject ) != "undefined" ) && ( theobject.checked ) )
		{
			$( '*', '#div_list_ops' ).each( function () {
				var div_name = $(this).attr('id') ;
				if ( div_name.indexOf( "ck_op_" ) == 0 )
					$(this).attr( 'checked', true ) ;
			}) ;
		}
		else
		{
			$( '*', '#div_list_ops' ).each( function () {
				var div_name = $(this).attr('id') ;
				if ( div_name.indexOf( "ck_op_" ) == 0 )
					$(this).attr( 'checked', false ) ;
			}) ;
		}
	}

	function check_all_depts( theobject )
	{
		if ( ( typeof( theobject ) != "undefined" ) && ( theobject.checked ) )
		{
			$( '*', '#div_list_depts' ).each( function () {
				var div_name = $(this).attr('id') ;
				if ( div_name.indexOf( "ck_dept_" ) == 0 )
					$(this).attr( 'checked', true ) ;
			}) ;
		}
		else
		{
			$( '*', '#div_list_depts' ).each( function () {
				var div_name = $(this).attr('id') ;
				if ( div_name.indexOf( "ck_dept_" ) == 0 )
					$(this).attr( 'checked', false ) ;
			}) ;
		}
	}

	function do_assign()
	{
		var ok_ops = 0 ;
		var ok_depts = 0 ;

		$( '*', '#div_list_ops' ).each( function () {
			var div_name = $(this).attr('id') ;
			if ( ( div_name.indexOf( "ck_op_" ) == 0 ) && $(this).attr( 'checked' ) )
				ok_ops = 1 ;
		}) ;
		$( '*', '#div_list_depts' ).each( function () {
			var div_name = $(this).attr('id') ;
			if ( ( div_name.indexOf( "ck_dept_" ) == 0 ) && $(this).attr( 'checked' ) )
				ok_depts = 1 ;
		}) ;

		if ( !ok_ops )
			do_alert( 0, "An operator must be selected." ) ;
		else if ( !ok_depts )
			do_alert( 0, "A department must be selected." ) ;
		else
			$('#form_assign').submit() ;
	}

	function show_form()
	{
		$(window).scrollTop(0) ;
		$('#div_list').hide() ;
		$('#div_btn_add').hide() ;
		$('#div_form').show() ;
	}

	function do_reset()
	{
		$('#theform').each(function(){
			this.reset();
		});

		$(window).scrollTop(0) ;
		$('#div_form').hide() ;
		$('#div_btn_add').show() ;
		$('#div_list').show() ;
	}

//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu_focus" onClick="show_div('main')" id="menu_ops_main">Operators</div>
			<div class="op_submenu" onClick="show_div('assign')" id="menu_ops_assign">Assign Operator to Department</div>
			<div class="op_submenu" onClick="location.href='ops_reports.php?ses=<?php echo $ses ?>'" id="menu_ops_report">Online Activity</div>
			<div class="op_submenu" onClick="show_div('monitor')" id="menu_ops_monitor">Operator Status Widget</div>
			<div class="op_submenu" onClick="show_div('online')" id="menu_ops_online"><img src="../pics/icons/bulb.png" width="12" height="12" border="0" alt=""> Go ONLINE!</div>
			<div style="clear: both"></div>
		</div>

		<div id="ops_main">
			<div id="div_btn_add" class="edit_focus" style="margin-top: 25px;" onClick="show_form()">Add Operator</div>
			<div id="div_list" style="margin-top: 25px;">
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td width="40"><div class="td_dept_header">&nbsp;</div></td>
					<td><div class="td_dept_header">Name</div></td>
					<td><div class="td_dept_header">Login</div></td>
					<td><div class="td_dept_header">Email</div></td>
					<td width="60" nowrap align="center"><div class="td_dept_header">Rate</div></td>
					<td width="50" nowrap align="center"><div class="td_dept_header">SMS</div></td>
					<td width="80" nowrap align="center"><div class="td_dept_header">Op2Op</div></td>
					<td width="60" nowrap align="center"><div class="td_dept_header">Traffic</div></td>
					<td width="80" nowrap align="center"><div class="td_dept_header">View IP</div></td>
					<td width="60" nowrap align="center"><div class="td_dept_header">Status</div></td>
				</tr>
				<?php
					$image_empty = "<img src=\"../pics/space.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"\">" ;
					$image_checked = "<img src=\"../pics/icons/check.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\">";
					for ( $c = 0; $c < count( $operators ); ++$c )
					{
						$operator = $operators[$c] ;

						$login = $operator["login"] ;
						$email = $operator["email"] ;
						$rate = ( $operator["rate"] ) ? $image_checked : $image_empty ;
						$sms = ( $operator["sms"] ) ? $image_checked : $image_empty ;
						$op2op = ( $operator["op2op"] ) ? $image_checked : $image_empty ;
						$traffic = ( $operator["traffic"] ) ? $image_checked : $image_empty ;
						$viewip = ( $operator["viewip"] ) ? $image_checked : $image_empty ;
						$status = ( $operator["status"] ) ? "<b>Operator is Online</b><br>Click to disconnect console." : "Offline" ;
						$status_img = ( $operator["status"] ) ? "<img src=\"../pics/icons/bulb.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"online\" title=\"online\">" : "<img src=\"../pics/icons/bulb_off.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"offline\" title=\"offline\">" ;
						$style = ( $operator["status"] ) ? "cursor: pointer" : "" ;
						$td_style = ( $operator["status"] ) ? "background: #AFFF9F; $style" : "" ;
						$js = ( $operator["status"] ) ? "onClick='remote_disconnect($operator[opID])'" : "" ;
						$sms_edit = ( $operator["sms"] ) ? 1 : 0 ;

						$edit_delete = "<div style=\"cursor: pointer;\" onClick=\"do_edit( $operator[opID], '$operator[name]', '$operator[email]', '$operator[login]', $operator[rate], $sms_edit, $operator[op2op], $operator[traffic], $operator[viewip] )\"><img src=\"../pics/btn_edit.png\" width=\"64\" height=\"23\" border=\"0\" alt=\"\"></div><div onClick=\"do_delete($operator[opID])\" style=\"margin-top: 10px; cursor: pointer;\"><img src=\"../pics/btn_delete.png\" width=\"64\" height=\"23\" border=\"0\" alt=\"\"></div>" ;

						$td1 = "td_dept_td" ;

						print "
						<tr>
							<td class=\"$td1\" nowrap>$edit_delete</td>
							<td class=\"$td1\">
								<div style=\"\">$operator[name]</div>
							</td>
							<td class=\"$td1\">$login</td>
							<td class=\"$td1\">$email</td>
							<td class=\"$td1\" align=\"center\">$rate</td>
							<td class=\"$td1\" align=\"center\">$sms</td>
							<td class=\"$td1\" align=\"center\">$op2op</td>
							<td class=\"$td1\" align=\"center\">$traffic</td>
							<td class=\"$td1\" align=\"center\">$viewip</td>
							<td class=\"$td1\" align=\"center\" style=\"$td_style\" $js>$status_img</td>
						</tr>
						" ;
					}
					if ( $c == 0 )
						print "<tr><td colspan=10 class=\"td_dept_td\">blank results</td></tr>" ;
				?>
				</table>
			</div>
		</div>

		<div style="display: none;" id="ops_assign">

			<div style="margin-top: 25px;">
				<?php if ( !count( $departments ) ): ?>
				<span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> Create a <a href="depts.php?ses=<?php echo $ses ?>" style="color: #FFFFFF;">Department</a> to continue.</span>
				<?php elseif ( !count( $operators ) ):  ?>
				<span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> Create an <a href="JavaScript:void(0)" onClick="show_div('main')" style="color: #FFFFFF;">Operator</a> to continue.</span>

				<?php else: ?>
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td valign="top">
						<form method="POST" action="ops.php?submit" id="form_assign">
						<input type="hidden" name="ses" value="<?php echo $ses ?>">
						<input type="hidden" name="action" value="submit_assign">
						<input type="hidden" name="jump" value="assign">
						
						<div style="max-height: 300px; overflow: auto;" id="div_list_ops">
							<table cellspacing=0 cellpadding=0 border=0 width="100%">
							<tr>
								<td width="20"><div class="td_dept_header"><input type="checkbox" onClick="check_all_ops(this)" id="ck_op_all"></div></td>
								<td><div class="td_dept_header">Operator Name</div></td>
								<td><div class="td_dept_header">Email</div></td>
							</tr>
							<?php
								for ( $c = 0; $c < count( $operators ); ++$c )
								{
									$operator = $operators[$c] ;

									$td1 = "td_dept_td" ;

									print "
									<tr>
										<td class=\"$td1\"><input type=\"checkbox\" id=\"ck_op_$operator[opID]\" name=\"opids[]\" value=\"$operator[opID]\"></td>
										<td class=\"$td1\" nowrap>
											<div style=\"\">$operator[name]</div>
										</td>
										<td class=\"$td1\">$operator[email]</td>
									</tr>
									" ;
								}
							?>
							</table>
						</div>
						<div style="margin-top: 15px;" id="div_list_depts">
							<div class="">Assign the above checked operator(s) to the following departments:</div>

							<div style="margin-top: 15px; max-height: 300px; overflow: auto;">
								<table cellspacing=0 cellpadding=0 border=0 width="100%">
								<tr>
									<td width="20"><div class="td_dept_header"><input type="checkbox" onClick="check_all_depts(this)" id="ck_dept_all"></div></td>
									<td><div class="td_dept_header">Department Name</div></td>
								</tr>
								<?php
									$ops_assigned = 0 ;
									for ( $c = 0; $c < count( $departments ); ++$c )
									{
										$department = $departments[$c] ;
										$ops = Depts_get_DeptOps( $dbh, $department["deptID"] ) ;
										if ( count( $ops ) )
											$ops_assigned = 1 ;

										$td1 = "td_dept_td" ;

										if ( $department["name"] != "Archive" )
										{
											print "
											<tr>
												<td class=\"$td1\"><input type=\"checkbox\" id=\"ck_dept_$department[deptID]\" name=\"deptids[]\" value=\"$department[deptID]\"></td>
												<td class=\"$td1\" nowrap>
													<div style=\"\">$department[name]</div>
												</td>
											</tr>
											" ;
										}
									}
								?>
								</table>
							</div>

							<div style="margin-top: 10px;">
								<button type="button" style="padding: 10px;" onClick="do_assign()">Assign</button>
							</div>
						</div>
						</form>
					</td>
					<td valign="top" style="padding-left: 25px;" width="350">
						<div class="info_info">
							<div style="padding-bottom: 15px;"><img src="../pics/icons/vcard.png" width="16" height="16" border="0" alt=""> Department assignment and <span class="info_box">Defined Order</span></div>
							<div style="">
								<?php
									for ( $c = 0; $c < count( $departments ); ++$c )
									{
										$department = $departments[$c] ;

										if ( $department["name"] != "Archive" )
										{
											print "
												<div class=\"info_info\" style=\"margin-bottom: 5px;\">
													<div class=\"td_dept_header\">$department[name]</div>
													<div id=\"dept_ops_$department[deptID]\" style=\"min-height: 25px; max-height: 200px; overflow: auto;\"></div>
												</div>
											" ;
										}
									}
								?>
							</div>
						</div>
					</td>
				</tr>
				</table>
				<?php endif ; ?>
			</div>
		</div>


		<div style="display: none;" id="ops_monitor">
			<div style="margin-top: 25px;">
				View operator online/offline status and chat activity within a tiny widget window. The widget window can be left open on the desktop for quick access to online/offline status information and real-time chat sessions.
			</div>

			<div style="margin-top: 25px;">
				<button type="button" onClick="launch_tools_op_status()" class="btn">Launch Operator Status Widget Window</button>
			</div>
		</div>

		<div style="display: none;" id="ops_online">
			<div style="margin-top: 25px;">
				<?php if ( !count( $departments ) ): ?>
				<span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> Create a <a href="depts.php?ses=<?php echo $ses ?>" style="color: #FFFFFF;">Department</a> to continue.</span>
				<?php elseif ( !count( $operators ) ): ?>
				<span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> Create an <a href="ops.php?ses=<?php echo $ses ?>" style="color: #FFFFFF;">Operator</a> to continue.</span>
				<?php elseif ( !$ops_assigned ): ?>
				<span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> <a href="ops.php?ses=<?php echo $ses ?>&jump=assign" style="color: #FFFFFF;">Assign an operator to a department</a> to continue.</span>
				<?php else: ?>
				<div style="">Provide the following Operator Login URL to your <a href="JavaScript:void(0)" onClick="show_div('main')">operators</a>.  Once logged in, launch the operator console to go <span style="color: #3EB538; font-weight: bold;">ONLINE</span> and to accept visitor chat requests.</div>

				<div class="info_info" style="margin-top: 15px;">
					<div style=""><img src="../pics/icons/bulb.png" width="16" height="16" border="0" alt=""> Operator Login URL</div>
					<div style="margin-top: 5px; font-size: 32px; font-weight: bold; text-shadow: 1px 1px #FFFFFF;"><a href="<?php echo ( !preg_match( "/^(http)/", $CONF["BASE_URL"] ) ) ? "http$https:$CONF[BASE_URL]" : $CONF["BASE_URL"] ; ?>" target="new" style="color: #8DB173; text-shadow: 1px 1px #FFFFFF;" class="nounder"><?php echo ( !preg_match( "/^(http)/", $CONF["BASE_URL"] ) ) ? "http$https:$CONF[BASE_URL]" : $CONF["BASE_URL"] ; ?></a></div>
				</div>

				<div style="margin-top: 25px;"><img src="../pics/icons/info.png" width="12" height="12" border="0" alt=""> Don't forget to place the online/offline chat icon <a href="./code.php?ses=<?php echo $ses ?>">HTML Code</a> onto your webpages.</div>
				<?php endif ; ?>
			</div>
		</div>

		<div id="div_form" style="display: none; margin-top: 25px;" id="a_edit">
			<form method="POST" action="ops.php?submit" id="theform">
			<input type="hidden" name="ses" value="<?php echo $ses ?>">
			<input type="hidden" name="action" value="submit">
			<input type="hidden" name="jump" id="jump" value="">
			<input type="hidden" name="opid" id="opid" value="0">

			<div>
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td nowrap><div class="tab_form_title">Operator Name</div></td>
					<td width="100%" style="padding-left: 10px;"><input type="text" name="name" id="name" size="30" maxlength="40" value="" onKeyPress="return noquotes(event)"></td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td><div class="tab_form_title">Operator Email</div></td>
					<td style="padding-left: 10px;"><input type="text" name="email" id="email" size="30" maxlength="160" value="" onKeyPress="return justemails(event)"></td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td><div class="tab_form_title">Login</div></td>
					<td style="padding-left: 10px;"><input type="text" name="login" id="login" size="30" maxlength="160" value="" onKeyPress="return noquotestags(event)"></td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td><div class="tab_form_title">Password</div></td>
					<td style="padding-left: 10px;"><input type="password" name="password" id="password" class="input" size="30" maxlength="15" value="" onKeyPress="return noquotes(event)"></td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td><div class="tab_form_title">Rate Operator</div></td>
					<td style="padding-left: 10px;">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td><div class="li_op round"><input type="radio" name="rate" id="rate_1" value="1" checked> Yes</div><div class="li_op round"><input type="radio" name="rate" id="rate_0" value="0"> No</div><div style="clear:both;"></div></td>
							<td style="padding-left: 5px;">Enable visitors to leave chat performance rating after the chat session ends.</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td><div class="tab_form_title">Op-2-Op Chat</div></td>
					<td style="padding-left: 10px;">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td><div class="li_op round"><input type="radio" name="op2op" id="op2op_1" value="1" checked> Yes</div><div class="li_op round"><input type="radio" name="op2op" id="op2op_0" value="0"> No</div><div style="clear:both;"></div></td>
							<td style="padding-left: 5px;">Enable the operator to chat with other online operators within the operator console window.</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td><div class="tab_form_title">Traffic Monitor</div></td>
					<td style="padding-left: 10px;">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td><div class="li_op round"><input type="radio" name="traffic" id="traffic_1" value="1" checked> Yes</div><div class="li_op round"><input type="radio" name="traffic" id="traffic_0" value="0"> No</div><div style="clear:both;"></div></td>
							<td style="padding-left: 5px;">View website traffic (can perform initiate chat and other traffic related features).</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td><div class="tab_form_title">View IP</div></td>
					<td style="padding-left: 10px;">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td><div class="li_op round"><input type="radio" name="viewip" id="viewip_1" value="1"> Yes</div><div class="li_op round"><input type="radio" name="viewip" id="viewip_0" value="0" checked> No</div><div style="clear:both;"></div></td>
							<td style="padding-left: 5px;">View website visitor IP and the <a href="extras_geo.php?ses=<?php echo $ses ?>">GeoIP</a> information.</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td><div class="tab_form_title">SMS Alerts</div></td>
					<td style="padding-left: 10px;">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td><div class="li_op round"><input type="radio" name="sms" id="sms_1" value="1"> Yes</div><div class="li_op round"><input type="radio" name="sms" id="sms_0" value="0" checked> No</div><div style="clear:both;"></div></td>
							<td style="padding-left: 5px;">Receive new chat request SMS mobile alert. (configured at the <a href="JavaScript:void(0)" onClick="show_div('online')">operator area</a>)</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</div>
			<div style="margin-top: 25px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td><div class="tab_form_title" style="background: #FFFFFF; border: 1px solid #FFFFFF;"></div></td>
					<td style="padding-left: 10px;"><button type="button" onClick="do_submit()" class="btn">Submit</button> &nbsp; <a href="JavaScript:void(0)" onClick="do_reset()">cancel</a></td>
				</tr>
				</table>
			</div>

			</form>
		</div>

		<div id="remote_disconnect_notice" class="info_error" style="display: none; position: absolute;">Disconnecting console... <img src="../pics/loading_fb.gif" width="16" height="11" border="0" alt=""></div>

<?php include_once( "./inc_footer.php" ) ?>

