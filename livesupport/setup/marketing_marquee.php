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

	include_once( "../API/Depts/get.php" ) ;
	include_once( "../API/Marketing/get.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;

	if ( $action == "submit" )
	{
		include_once( "../API/Marketing/put.php" ) ;

		$marqid = Util_Format_Sanatize( Util_Format_GetVar( "marqid" ), "ln" ) ;
		$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "ln" ) ;
		$snapshot = Util_Format_Sanatize( Util_Format_GetVar( "snapshot" ), "notags" ) ;
		$message = Util_Format_ConvertQuotes( Util_Format_Sanatize( Util_Format_GetVar( "message" ), "notags" ) ) ;

		if ( !Marketing_put_Marquee( $dbh, $marqid, $deptid, $snapshot, $message ) )
		{
			$error = "Create new Chat Footer Marquee failed." ;
		}
	}
	else if ( $action == "delete" )
	{
		include_once( "../API/Marketing/remove.php" ) ;

		$marqid = Util_Format_Sanatize( Util_Format_GetVar( "marqid" ), "ln" ) ;

		Marketing_remove_Marquee( $dbh, $marqid ) ;
	}

	$departments = Depts_get_AllDepts( $dbh ) ;
	$marquees = Marketing_get_AllMarquees( $dbh ) ;

	$dept_hash = Array() ;
	$dept_hash[1111111111] = "All Departments" ;
	for ( $c = 0; $c < count( $departments ); ++$c )
	{
		$department = $departments[$c] ;
		$dept_hash[$department["deptID"]] = $department["name"] ;
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

<script type="text/javascript">
<!--
	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		init_marquees() ;
		toggle_menu_setup( "marketing" ) ;

		<?php if ( ( $action == "submit" ) && !$error ): ?>do_alert( 1, "Success" ) ;<?php endif ; ?>
	});

	function init_marquees()
	{
		$( '*', '#table_marquees' ).each( function(){
			var div_name = $( this ).attr('id') ;

			if ( div_name.indexOf("marquee_msg_") != -1 )
			{
				var message = $(this).html() ;
				$(this).html( parse_marquee( message ) ) ;
			}
		} );
	}

	function do_edit( themarqid, thedeptid, thesnapshot, themessage )
	{
		$( "input#marqid" ).val( themarqid ) ;
		$( "select#deptid" ).val( thedeptid ) ;
		$( "input#snapshot" ).val( thesnapshot ) ;
		$( "input#message" ).val( themessage ) ;
		location.href = "#a_edit" ;
	}

	function do_delete( themarqid )
	{
		if ( confirm( "Delete this Marquee?" ) )
			location.href = "marketing_marquee.php?ses=<?php echo $ses ?>&action=delete&marqid="+themarqid ;
	}

	function do_submit()
	{
		var deptid = $( "input#deptid" ).val() ;
		var snapshot = $( "#snapshot" ).val() ;
		var message = $( "#message" ).val() ;

		if ( snapshot == "" )
			do_alert( 0, "Blank reference name is invalid." ) ;
		else if ( message == "" )
			do_alert( 0, "Blank marquee message is invalid." ) ;
		else
			$('#theform').submit() ;
	}

	function view_marquee()
	{
		var message = $( "#message" ).val()
		message = encodeURIComponent( message ) ;

		if ( message == "" )
			do_alert( 0, "Blank marquee message is invalid." ) ;
		else
		{
			url = "<?php echo $CONF["BASE_URL"] ?>/phplive.php?marquee_test="+message+"&"+unixtime() ;
			var newwin = window.open( url, "Marquee", 'scrollbars=no,resizable=yes,menubar=no,location=no,screenX=50,screenY=100,width=<?php echo $VARS_CHAT_WIDTH ?>,height=<?php echo $VARS_CHAT_HEIGHT ?>' ) ;
		}
	}

//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu" onClick="location.href='marketing.php?ses=<?php echo $ses ?>'">Social Media</div>
			<div class="op_submenu_focus">Chat Footer Marquee</div>
			<div class="op_submenu" onClick="location.href='marketing_click.php?ses=<?php echo $ses ?>'">Campaign Tracking</div>
			<div class="op_submenu" onClick="location.href='reports_marketing.php?ses=<?php echo $ses ?>'">Report: Campaign Clicks</div>
			<div style="clear: both"></div>
		</div>

		<div style="margin-top: 25px;">
			Announcement messages can be displayed on the visitor chat window footer.  The messages will cycle every 10 seconds.  Short and sweet messages are recommended to avoid formatting issues. (example: "Free shipping today!")
		</div>

		<div style="margin-top: 25px;">
			<form>
			<table cellspacing=0 cellpadding=0 border=0 width="100%" id="table_marquees">
			<tr>
				<td width="40"><div class="td_dept_header">&nbsp;</div></td>
				<td width="200"><div class="td_dept_header">Department</div></td>
				<td><div class="td_dept_header">Title</div></td>
				<td><div class="td_dept_header">Message</div></td>
			</tr>
			<?php
				for ( $c = 0; $c < count( $marquees ); ++$c )
				{
					$marquee = $marquees[$c] ;
					$dept_name = $dept_hash[$marquee["deptID"]] ;
					$message = $marquee["message"] ;
					$message_js = preg_replace( "/'/", "\'", preg_replace( "/\"/", "&quot;", $marquee["message"] ) ) ;
					$snapshot = $marquee["snapshot"] ;
					$snapshot_js = preg_replace( "/'/", "\'", preg_replace( "/\"/", "&quot;", $marquee["snapshot"] ) ) ;

					$edit_delete = "<div style=\"cursor: pointer;\" onClick=\"do_edit( $marquee[marqID], '$marquee[deptID]', '$snapshot_js', '$message_js' )\"><img src=\"../pics/btn_edit.png\" width=\"64\" height=\"23\" border=\"0\" alt=\"\"></div><div onClick=\"do_delete($marquee[marqID])\" style=\"margin-top: 10px; cursor: pointer;\"><img src=\"../pics/btn_delete.png\" width=\"64\" height=\"23\" border=\"0\" alt=\"\"></div>" ;

					$td1 = "td_dept_td" ;

					print "
						<tr>
							<td class=\"$td1\" nowrap>$edit_delete</td>
							<td class=\"$td1\" nowrap>$dept_name</td>
							<td class=\"$td1\">$snapshot</td>
							<td class=\"$td1\"><div id=\"marquee_msg_$marquee[marqID]\">$message</div></td>
						</tr>
					" ;
				}
				if ( $c == 0 )
					print "<tr><td colspan=7 class=\"td_dept_td\">blank results</td></tr>" ;
			?>
			</table>
			</form>
		</div>

		<div class="edit_wrapper" style="padding: 5px; margin-top: 55px;">
			<a name="a_edit"></a><div class="edit_title">Create/Edit Chat Footer Marquee <span class="txt_red"><?php echo $error ?></span></div>
			<div style="margin-top: 10px;">
				<form method="POST" action="marketing_marquee.php?submit" id="theform">
				<input type="hidden" name="action" value="submit">
				<input type="hidden" name="ses" value="<?php echo $ses ?>">
				<input type="hidden" name="marqid" id="marqid" value="0">
				<table cellspacing=0 cellpadding=5 border=0>
				<tr>
					<td>Display this Marquee to visitors requesting support to Department(s)<br>
						<select name="deptid" id="deptid" style="font-size: 16px; background: #D4FFD4; color: #009000;">
						<option value="1111111111">All Departments</option>
						<?php
							for ( $c = 0; $c < count( $departments ); ++$c )
							{
								$department = $departments[$c] ;
								if ( $department["name"] != "Archive" )
									print "<option value=\"$department[deptID]\">$department[name]</option>" ;
							}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Reference Name - not displayed (example: "FREE shipping")<br><input type="text" name="snapshot" id="snapshot" size="50" maxlength="35" value=""></td>
				</tr>
				<tr>
					<td>Marquee Message - displayed (example: "Receive FREE shipping today!")
						<div style="padding: 5px;"><img src="../pics/icons/info.png" width="12" height="12" border="0" alt=""> Plain text only.  HTML tags will be ommitted.</div>
						<div style="padding: 5px;"><img src="../pics/icons/info.png" width="12" height="12" border="0" alt="">
							To link a <b>URL</b> and <i><b>email</b></i> addresses, use the [url] and [email] BBcode codes.
							<div class="info_neutral" style="margin-top: 5px;">
								<table cellspacing=0 cellpadding=0 border=0>
								<tr>
									<td>
										To link a URL:<br>
										<span class="txt_blue">[url=http://www.yoursite.com]link text[/url]</span>
									</td>
									<td style="padding-left: 15px;">
										To link an email address:<br>
										<span class="txt_blue">[email]info@yoursite.com[/email]</span>
									</td>
								</tr>
								</table>
							</div>
						</div>
						<input type="text" name="message" id="message" size="120" maxlength="255" value="" onKeyPress="return notags(event)">
						<div style="margin-top: 15px; font-size: 10px"><img src="../pics/icons/bullet.png" width="16" height="16" border="0" alt=""> <a href="JavaScript:void(0)" onClick="view_marquee()">view how it looks</a></div>
					</td>
				</tr>
				<tr>
					<td> <div style="padding-top: 25px;"><input type="button" value="Submit" onClick="do_submit()" class="btn"> <input type="reset" value="Reset" onClick="$( 'input#marqid' ).val(0);" class="btn"></div></td>
				</tr>
				</table>
				</form>
			</div>
		</div>
<?php include_once( "./inc_footer.php" ) ?>

