<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/* Dev team: 615 */
	/****************************************/
	// STANDARD header for Setup
	if ( !is_file( "../web/config.php" ) ){ HEADER("location: ../setup/install.php") ; exit ; }
	include_once( "../web/config.php" ) ;
	include_once( "../API/Util_Format.php" ) ;
	include_once( "../API/Util_Error.php" ) ;
	include_once( "../API/SQL.php" ) ;
	include_once( "../API/Util_Security.php" ) ;
	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	if ( !$opinfo = Util_Security_AuthOp( $dbh, $ses ) ){ ErrorHandler( 602, "Invalid operator session or session has expired.", $PHPLIVE_FULLURL, 0, Array() ) ; }
	// STANDARD header end
	/****************************************/

	include_once( "../API/Depts/get.php" ) ;
	include_once( "../API/Canned/get.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$console = Util_Format_Sanatize( Util_Format_GetVar( "console" ), "ln" ) ; $body_width = ( $console ) ? 800 : 900 ;
	$flag = Util_Format_Sanatize( Util_Format_GetVar( "flag" ), "ln" ) ;

	$menu = "cans" ;
	$error = "" ;

	if ( $action == "submit" )
	{
		include_once( "../API/Canned/put.php" ) ;

		$canid = Util_Format_Sanatize( Util_Format_GetVar( "canid" ), "ln" ) ;
		$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "ln" ) ;
		$title = Util_Format_Sanatize( Util_Format_GetVar( "title" ), "ln" ) ;
		$message = Util_Format_Sanatize( Util_Format_GetVar( "message" ), "" ) ;

		$caninfo = Canned_get_CanInfo( $dbh, $canid ) ;
		if ( isset( $caninfo["opID"] ) )
			$opid = $caninfo["opID"] ;
		else
			$opid = $opinfo["opID"] ;

		if ( !Canned_put_Canned( $dbh, $canid, $opinfo["opID"], $deptid, $title, $message ) )
			$error = "Error processing canned message." ;
	}
	else if ( $action == "delete" )
	{
		include_once( "../API/Canned/remove.php" ) ;

		$canid = Util_Format_Sanatize( Util_Format_GetVar( "canid" ), "ln" ) ;

		$caninfo = Canned_get_CanInfo( $dbh, $canid ) ;
		if ( $caninfo["opID"] == $opinfo["opID"] )
			Canned_remove_Canned( $dbh, $opinfo["opID"], $canid ) ;
	}

	$departments = Depts_get_OpDepts( $dbh, $opinfo["opID"] ) ;
	$cans = Canned_get_OpCanned( $dbh, $opinfo["opID"], 0 ) ;

	// make hash for quick refrence
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
<title> Canned Responses </title>

<meta name="description" content="PHP Live! Support <?php echo $VERSION ?>">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8"> 
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<link rel="Stylesheet" href="../css/setup.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/tooltip.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var menu ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu_op() ;
		init_tooltips() ;
		toggle_menu_op( "<?php echo $menu ?>", '<?php echo $ses ?>' ) ;

		if ( ( typeof( window.external ) != "undefined" ) && ( typeof( window.external.wp_save_history ) != "undefined" ) ) { $('#menu_dn').hide() ; }
		else { $('#menu_dn').show() ; }

		<?php if ( ( $action == "submit" ) && !$error ): ?>parent.do_alert( 1, "Success" ) ;<?php endif ; ?>
	});

	function do_edit( thecanid, thetitle, thedeptid, themessage )
	{
		var pos = $('#div_title_edit').position() ;
		var scrollto = pos.top - 100 ;

		$( "input#canid" ).val( thecanid ) ;
		$( "input#title" ).val( thetitle.replace( /&-#39;/g, "'" ) ) ;
		$( "#deptid" ).val( thedeptid ) ;
		$( "#message" ).val( themessage.replace(/<br>/g, "\r\n").replace( /&-#39;/g, "'" ) ) ;

		$('html, body').animate({
			scrollTop: scrollto
		}, 500);
	}

	function do_delete( thiscanid )
	{
		if ( confirm( "Really delete this canned response?" ) )
			location.href = "cans.php?ses=<?php echo $ses ?>&action=delete&canid="+thiscanid ;
	}

	function do_submit()
	{
		var canid = $('#canid').val() ;
		var title = $('#title').val() ;
		var deptid = $('#deptid').val() ;
		var message = $('#message').val() ;

		if ( title == "" )
			do_alert( 0, "Please provide a title." ) ;
		else if ( message == "" )
			do_alert( 0, "Please provide a message." ) ;
		else
			$('#theform').submit() ;
	}

	function cancel_edit()
	{
		$( "input#canid" ).val( 0 ) ;
		$( "input#title" ).val( "" ) ;
		$( "#deptid" ).val( "" ) ;
		$( "#message" ).val( "" ) ;
		$('html, body').animate({
			scrollTop: 0
		}, 500);
	}

	function init_tooltips()
	{
		var help_tooltips = $( '#cans' ).find( '.help_tooltip' ) ;
		help_tooltips.tooltip({
			event: "mouseover",
			track: true,
			delay: 0,
			showURL: false,
			showBody: "- ",
			fade: 0,
			positionLeft: false,
			extraClass: "stat"
		});
	}

//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ; ?>

		<?php if ( !count( $departments ) ): ?>
		<div id="no_dept" class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> Contact the setup admin to assign this account to a department.  Once assigned, refresh this page and try again.</div>
		<?php else: ?>
		<div id="div_cans">
			<a name="a_top"></a>
			<div id="cans" style="">
				<table cellspacing=0 cellpadding=0 border=0 width="100%" id="table_trs">
				<tr>
					<td width="18" nowrap><div class="td_dept_header">&nbsp;</div></td>
					<td width="180" nowrap><div class="td_dept_header">Title</div></td>
					<td width="180"><div class="td_dept_header">Department</div></td>
					<td><div class="td_dept_header">Message</div></td>
				</tr>
				<?php
					for ( $c = 0; $c < count( $cans ); ++$c )
					{
						$can = $cans[$c] ;
						$title = preg_replace( "/\"/", "&quot;", preg_replace( "/'/", "&-#39;", $can["title"] ) ) ;
						$title_display = preg_replace( "/\"/", "&quot;", $can["title"] ) ;
						
						 $locked = ( $can["opID"] == 1111111111 ) ? "<img src=\"../pics/icons/lock.png\" width=\"16\" height=\"16\" border=\"0\" title=\"locked - created by admin\" alt=\"locked - created by admin\"> " : "" ;

						$dept_name = $dept_hash[$can["deptID"]] ;
						$message = preg_replace( "/\"/", "&quot;", preg_replace( "/'/", "&-#39;", preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", $can["message"] ) ) ) ;
						$message_display = preg_replace( "/\"/", "&quot;", preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", Util_Format_ConvertTags( $can["message"] ) ) ) ;

						$td1 = "td_dept_td" ;

						$edit_delete = ( $can["opID"] == $opinfo["opID"] ) ? "<div onClick=\"do_edit($can[canID], '$title', '$can[deptID]', '$message')\" style=\"cursor: pointer;\"><img src=\"../pics/btn_edit.png\" width=\"45\" height=\"16\" border=\"0\" alt=\"\"></div><div onClick=\"do_delete($can[canID])\" style=\"margin-top: 5px; cursor: pointer;\"><img src=\"../pics/btn_delete.png\" width=\"45\" height=\"16\" border=\"0\" alt=\"\"></div>" : "&nbsp;" ;

						print "<tr><td class=\"$td1\" nowrap>$edit_delete</td><td class=\"$td1\">$locked<b>$title_display</b></td><td class=\"$td1\">$dept_name</td><td class=\"$td1\">$message_display</td></tr>" ;
					}
					if ( $c == 0 )
						print "<tr><td colspan=7 class=\"td_dept_td\">blank results</td></tr>" ;
				?>
				</table>
			</div>

			<div id="div_title_edit" class="edit_title" style="margin-top: 55px;">Create/Edit Canned Responses <span class="txt_red"><?php echo $error ?></span></div>
			<div style="margin-top: 10px;">
				<form method="POST" action="cans.php?<?php echo time() ?>" id="theform">
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td valign="top" style="width: 420px; padding: 5px;">
						<input type="hidden" name="ses" value="<?php echo $ses ?>">
						<input type="hidden" name="action" value="submit">
						<input type="hidden" name="canid" id="canid" value="0">
						<div>
							Reference (example: "Welcome greeting", "Just a moment")<br>
							<input type="text" name="title" id="title" class="input_text" style="width: 98%; margin-bottom: 10px;" maxlength="25">
							Department<br>
							<select name="deptid" id="deptid" style="width: 99%; margin-bottom: 10px;">
							<option value="1111111111">All Departments</option>
							<?php
								for ( $c = 0; $c < count( $departments ); ++$c )
								{
									$department = $departments[$c] ;

									print "<option value=\"$department[deptID]\">$department[name]</option>" ;
								}
							?>
							</select>
							Canned Message<br>
							<textarea name="message" id="message" class="input_text" rows="7" style="min-width: 98%; margin-bottom: 10px;" wrap="virtual"></textarea>

							<button type="button" onClick="do_submit()" class="btn">Submit</button> &nbsp; <span style="text-decoration: underline; cursor: pointer;" onClick="cancel_edit()">cancel</span>
						</div>
					</td>
					<td valign="center">
						<ul>
							<li> Canned messages should not contain HTML.
							<li style="margin-top: 5px;"> Dynamically populated variables:
								<ul style="margin-top: 10px;">
									<li> <span class="text_blue"><b>%%visitor%%</b></span> = visitor's name
									<li> <span class="text_blue"><b>%%operator%%</b></span> = your name
									<li> <span class="text_blue"><b>%%op_email%%</b></span> = your email

									<li style="padding-top: 5px;"> <span class="text_blue"><b>email:</b><i>someone@somewhere.com</i> - link an email</span>
									<li> <span class="text_blue"><b>image:</b><i>http://www.someurl.com/image.gif</i> - display an image</span>

									<div style="margin-top: 15px;"><img src="../pics/icons/info.png" width="12" height="12" border="0" alt=""> Example:<br>Hi %%visitor%%!  Our contact email address is email:contact@yourdomain.com</div>
								</ul>
						</ul>
					</td>
				</tr>
				</table>
				</form>
			</div>
		</div>
		<?php endif ; ?>

<?php include_once( "./inc_footer.php" ) ; ?>
