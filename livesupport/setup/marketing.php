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

	include_once( "../API/Vars/get.php" ) ;
	include_once( "../API/Depts/get.php" ) ;

	$error = "" ;
	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "ln" ) ;

	if ( $action == "submit" )
	{
		include_once( "../API/Vars/put.php" ) ;

		$sm_fb = Util_Format_Sanatize( Util_Format_GetVar( "sm_fb" ), "ln" ) ;
		$sm_tw = Util_Format_Sanatize( Util_Format_GetVar( "sm_tw" ), "ln" ) ;
		$sm_yt = Util_Format_Sanatize( Util_Format_GetVar( "sm_yt" ), "ln" ) ;
		$sm_li = Util_Format_Sanatize( Util_Format_GetVar( "sm_li" ), "ln" ) ;
		$sm_gl = Util_Format_Sanatize( Util_Format_GetVar( "sm_gl" ), "ln" ) ;
		$sm_ot = Util_Format_Sanatize( Util_Format_GetVar( "sm_ot" ), "ln" ) ;

		$sm_fb_tip = Util_Format_Sanatize( Util_Format_GetVar( "sm_fb_tip" ), "ln" ) ;
		$sm_tw_tip = Util_Format_Sanatize( Util_Format_GetVar( "sm_tw_tip" ), "ln" ) ;
		$sm_yt_tip = Util_Format_Sanatize( Util_Format_GetVar( "sm_yt_tip" ), "ln" ) ;
		$sm_li_tip = Util_Format_Sanatize( Util_Format_GetVar( "sm_li_tip" ), "ln" ) ;
		$sm_gl_tip = Util_Format_Sanatize( Util_Format_GetVar( "sm_gl_tip" ), "ln" ) ;
		$sm_ot_tip = Util_Format_Sanatize( Util_Format_GetVar( "sm_ot_tip" ), "ln" ) ;

		$sm_fb_url = Util_Format_Sanatize( Util_Format_GetVar( "sm_fb_url" ), "base_url" ) ;
		$sm_tw_url = Util_Format_Sanatize( Util_Format_GetVar( "sm_tw_url" ), "base_url" ) ;
		$sm_yt_url = Util_Format_Sanatize( Util_Format_GetVar( "sm_yt_url" ), "base_url" ) ;
		$sm_li_url = Util_Format_Sanatize( Util_Format_GetVar( "sm_li_url" ), "base_url" ) ;
		$sm_gl_url = Util_Format_Sanatize( Util_Format_GetVar( "sm_gl_url" ), "base_url" ) ;
		$sm_ot_url = Util_Format_Sanatize( Util_Format_GetVar( "sm_ot_url" ), "base_url" ) ;

		Vars_put_Social( $dbh, $deptid, "facebook", $sm_fb, $sm_fb_tip, $sm_fb_url ) ;
		Vars_put_Social( $dbh, $deptid, "twitter", $sm_tw, $sm_tw_tip, $sm_tw_url ) ;
		Vars_put_Social( $dbh, $deptid, "youtube", $sm_yt, $sm_yt_tip, $sm_yt_url ) ;
		Vars_put_Social( $dbh, $deptid, "linkedin", $sm_li, $sm_li_tip, $sm_li_url ) ;
		Vars_put_Social( $dbh, $deptid, "gplus", $sm_gl, $sm_gl_tip, $sm_gl_url ) ;
		Vars_put_Social( $dbh, $deptid, "other", $sm_ot, $sm_ot_tip, $sm_ot_url ) ;
	}
	else if ( $action == "reset" )
	{
		include_once( "../API/Vars/remove.php" ) ;

		Vars_remove_DeptSocials( $dbh, $deptid ) ;
	}

	$socials = Vars_get_Socials( $dbh, $deptid ) ;

	$departments = Depts_get_AllDepts( $dbh ) ;
	if ( $deptid )
		$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
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
	var sms = Array( "fb", "tw", "yt", "li" ) ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		toggle_menu_setup( "marketing" ) ;

		<?php if ( $action && !$error ): ?>do_alert( 1, "Success" ) ;<?php endif ; ?>
		<?php if ( ( $action == "reset" ) && !$error ): ?>do_alert( 1, "Reset Success!" ) ;<?php endif ; ?>

		var help_tooltips = $( 'body' ).find( '.help_tooltip' ) ;
		help_tooltips.tooltip({
			event: "mouseover",
			track: true,
			delay: 0,
			showURL: false,	
			showBody: "- ",
			fade: 0,
			extraClass: "stat"
		});
	});

	function tcolor_focus( thediv )
	{
		$( '*', 'body' ).each( function(){
			var div_name = $(this).attr('id') ;
			if ( div_name.indexOf( "tcolor_li_" ) != -1 )
				$(this).css( { "border": "1px solid #C2C2C2" } ) ;
		} );

		if ( thediv != undefined )
		{
			$( "#color" ).val( thediv ) ;
			$( "#tcolor_li_"+thediv ).css( { "border": "1px solid #444444" } ) ;
		}
	}

	function do_edit( themarketid, theskey, thename, thecolor )
	{
		$( "input#marketid" ).val( themarketid ) ;
		$( "input#skey" ).val( theskey ) ;
		$( "input#name" ).val( thename ) ;
		tcolor_focus( thecolor ) ;
		location.href = "#a_edit" ;
	}

	function do_delete( themarketid )
	{
		if ( confirm( "Delete this campaign?  All data will be lost." ) )
			location.href = "marketing.php?ses=<?php echo $ses ?>&action=delete&marketid="+themarketid ;
	}

	function do_submit()
	{
		var execute = 1 ;
		for ( c = 0; c < sms.length; ++c )
		{
			if ( !check_sm_vals( sms[c] ) )
			{
				execute = 0 ;
				break ;
			}
		}

		if ( execute )
			$('#theform').submit() ;
	}

	function check_sm_vals( thesm )
	{
		var enabled = $('#sm_'+thesm).is(':checked') ;
		var tooltip = $('#sm_'+thesm+'_tip').val() ;
		var url = $('#sm_'+thesm+'_url').val() ;
		var url_ok = ( url.match( /(http:\/\/)|(https:\/\/)/i ) ) ? 1 : 0 ;

		if ( enabled )
		{
			if ( !tooltip || !url )
			{
				$('#sm_'+thesm).attr('checked', false) ;
				do_alert( 0, "Please provide the Tooltip and the Social Media URL." ) ;
				return false ;
			}
			else if ( !url_ok )
			{
				$('#sm_'+thesm).attr('checked', false) ;
				do_alert( 0, "URL should begin with http:// or https:// for correct loading." ) ;
				return false ;
			}
		}
		
		return true ;
	}

	function launch_sm( thesm )
	{
		var unique = unixtime() ;
		var url = $('#sm_'+thesm+'_url').val() ;
		var url_ok = ( url.match( /(http:\/\/)|(https:\/\/)/i ) ) ? 1 : 0 ;

		if ( !url )
			do_alert( 0, "Please provide the Social Media URL." ) ;
		else if ( !url_ok )
			do_alert( 0, "URL should begin with http:// or https:// for correct loading." ) ;
		else
			window.open(url, unique, 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1') ;
	}

	function switch_dept( theobject )
	{
		location.href = "marketing.php?ses=<?php echo $ses ?>&deptid="+theobject.value+"&"+unixtime() ;
	}

	function reset_sm( thedeptid )
	{
		if ( confirm( "Clear current links and use Global Default?" ) )
			location.href = "./marketing.php?ses=<?php echo $ses ?>&action=reset&deptid="+thedeptid ;
	}

//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu_focus">Social Media</div>
			<div class="op_submenu" onClick="location.href='marketing_marquee.php?ses=<?php echo $ses ?>'">Chat Footer Marquee</div>
			<div class="op_submenu" onClick="location.href='marketing_click.php?ses=<?php echo $ses ?>'">Campaign Tracking</div>
			<div class="op_submenu" onClick="location.href='reports_marketing.php?ses=<?php echo $ses ?>'">Report: Campaign Clicks</div>
			<div style="clear: both"></div>
		</div>

		<div style="margin-top: 25px;">
			The social media icons will be visible on the visitor chat window for convenient access to your company profiles.  <a href="JavaScript:void(0)" class="help_tooltip" title="- <img src='../pics/screens/social.gif' width='256' height='95' border='0'>" style="cursor: default;">Example Screenshot</a>

			<form method="POST" action="marketing.php?submit" id="form_theform">
			<select name="deptid" id="deptid" style="font-size: 16px; background: #D4FFD4; color: #009000; margin-top: 15px;" OnChange="switch_dept( this )">
			<option value="0">Global Default</option>
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
			</form>
		</div>

		<div style="margin-top: 25px;">
			<form action="marketing.php?submit" method="POST" id="theform">
			<input type="hidden" name="action" value="submit">
			<input type="hidden" name="ses" value="<?php echo $ses ?>">
			<input type="hidden" name="deptid" value="<?php echo $deptid ?>">
			<table cellspacing=0 cellpadding=0 border=0 width="100%">
			<tr>
				<td width="15"><div class="td_dept_header">Enable</div></td>
				<td width="100"><div class="td_dept_header">&nbsp;</div></td>
				<td><div class="td_dept_header">Tooltip (example: Follow us on Twitter!)</div></td>
				<td><div class="td_dept_header">Social Media URL</div></td>
			</tr>
			<tr>
				<td class="td_dept_td"><input type="checkbox" name="sm_fb" id="sm_fb" value="1" onClick="check_sm_vals('fb')" <?php echo ( isset( $socials["facebook"]["status"] ) && $socials["facebook"]["status"] ) ? "checked" : "" ; ?>></td>
				<td class="td_dept_td" nowrap><img src="../pics/icons/social/facebook.png" width="16" height="16" border="0" alt=""> Facebook</td>
				<td class="td_dept_td"><input type="text" class="input" size="40" maxlength="100" name="sm_fb_tip" id="sm_fb_tip" onKeyPress="return noquotes(event)" value="<?php echo ( isset( $socials["facebook"]["tooltip"] ) && $socials["facebook"]["tooltip"] ) ? $socials["facebook"]["tooltip"] : "" ; ?>"></td>
				<td class="td_dept_td"><input type="text" class="input" size="50" maxlength="255" name="sm_fb_url" id="sm_fb_url" value="<?php echo ( isset( $socials["facebook"]["url"] ) && $socials["facebook"]["url"] ) ? $socials["facebook"]["url"] : "" ; ?>"> &nbsp; <span style="">&middot; <a href="JavaScript:void(0)" onClick="launch_sm('fb')">visit</a></span></td>
			</tr>
			<tr>
				<td class="td_dept_td"><input type="checkbox" name="sm_tw" id="sm_tw" value="1" onClick="check_sm_vals('tw')" <?php echo ( isset( $socials["twitter"]["status"] ) && $socials["twitter"]["status"] ) ? "checked" : "" ; ?>></td>
				<td class="td_dept_td" nowrap><img src="../pics/icons/social/twitter.png" width="16" height="16" border="0" alt=""> Twitter</td>
				<td class="td_dept_td"><input type="text" class="input" size="40" maxlength="100" name="sm_tw_tip" id="sm_tw_tip" onKeyPress="return noquotes(event)" value="<?php echo ( isset( $socials["twitter"]["tooltip"] ) && $socials["twitter"]["tooltip"] ) ? $socials["twitter"]["tooltip"] : "" ; ?>"></td>
				<td class="td_dept_td"><input type="text" class="input" size="50" maxlength="255" name="sm_tw_url" id="sm_tw_url" value="<?php echo ( isset( $socials["twitter"]["url"] ) && $socials["twitter"]["url"] ) ? $socials["twitter"]["url"] : "" ; ?>"> &nbsp; <span style="">&middot; <a href="JavaScript:void(0)" onClick="launch_sm('tw')">visit</a></span></td>
			</tr>
			<tr>
				<td class="td_dept_td"><input type="checkbox" name="sm_yt" id="sm_yt" value="1" onClick="check_sm_vals('yt')" <?php echo ( isset( $socials["youtube"]["status"] ) && $socials["youtube"]["status"] ) ? "checked" : "" ; ?>></td>
				<td class="td_dept_td" nowrap><img src="../pics/icons/social/youtube.png" width="16" height="16" border="0" alt=""> YouTube</td>
				<td class="td_dept_td"><input type="text" class="input" size="40" maxlength="100" name="sm_yt_tip" id="sm_yt_tip" onKeyPress="return noquotes(event)" value="<?php echo ( isset( $socials["youtube"]["tooltip"] ) && $socials["youtube"]["tooltip"] ) ? $socials["youtube"]["tooltip"] : "" ; ?>"></td>
				<td class="td_dept_td"><input type="text" class="input" size="50" maxlength="255" name="sm_yt_url" id="sm_yt_url" value="<?php echo ( isset( $socials["youtube"]["url"] ) && $socials["youtube"]["url"] ) ? $socials["youtube"]["url"] : "" ; ?>"> &nbsp; <span style="">&middot; <a href="JavaScript:void(0)" onClick="launch_sm('yt')">visit</a></span></td>
			</tr>
			<tr>
				<td class="td_dept_td"><input type="checkbox" name="sm_li" id="sm_li" value="1" onClick="check_sm_vals('li')" <?php echo ( isset( $socials["linkedin"]["status"] ) && $socials["linkedin"]["status"] ) ? "checked" : "" ; ?>></td>
				<td class="td_dept_td" nowrap><img src="../pics/icons/social/linkedin.png" width="16" height="16" border="0" alt=""> LinkedIn</td>
				<td class="td_dept_td"><input type="text" class="input" size="40" maxlength="100" name="sm_li_tip" id="sm_li_tip" onKeyPress="return noquotes(event)" value="<?php echo ( isset( $socials["linkedin"]["tooltip"] ) && $socials["linkedin"]["tooltip"] ) ? $socials["linkedin"]["tooltip"] : "" ; ?>"></td>
				<td class="td_dept_td"><input type="text" class="input" size="50" maxlength="255" name="sm_li_url" id="sm_li_url" value="<?php echo ( isset( $socials["linkedin"]["url"] ) && $socials["linkedin"]["url"] ) ? $socials["linkedin"]["url"] : "" ; ?>"> &nbsp; <span style="">&middot; <a href="JavaScript:void(0)" onClick="launch_sm('li')">visit</a></span></td>
			</tr>
			<tr>
				<td class="td_dept_td"><input type="checkbox" name="sm_gl" id="sm_gl" value="1" onClick="check_sm_vals('gl')" <?php echo ( isset( $socials["gplus"]["status"] ) && $socials["gplus"]["status"] ) ? "checked" : "" ; ?>></td>
				<td class="td_dept_td" nowrap><img src="../pics/icons/social/gplus.png" width="16" height="16" border="0" alt=""> Google Plus</td>
				<td class="td_dept_td"><input type="text" class="input" size="40" maxlength="100" name="sm_gl_tip" id="sm_gl_tip" onKeyPress="return noquotes(event)" value="<?php echo ( isset( $socials["gplus"]["tooltip"] ) && $socials["gplus"]["tooltip"] ) ? $socials["gplus"]["tooltip"] : "" ; ?>"></td>
				<td class="td_dept_td"><input type="text" class="input" size="50" maxlength="255" name="sm_gl_url" id="sm_gl_url" value="<?php echo ( isset( $socials["gplus"]["url"] ) && $socials["gplus"]["url"] ) ? $socials["gplus"]["url"] : "" ; ?>"> &nbsp; <span style="">&middot; <a href="JavaScript:void(0)" onClick="launch_sm('gl')">visit</a></span></td>
			</tr>
			<tr>
				<td class="td_dept_td"><input type="checkbox" name="sm_ot" id="sm_ot" value="1" onClick="check_sm_vals('ot')" <?php echo ( isset( $socials["other"]["status"] ) && $socials["other"]["status"] ) ? "checked" : "" ; ?>></td>
				<td class="td_dept_td" nowrap><img src="../pics/icons/social/other.png" width="16" height="16" border="0" alt=""> Other</td>
				<td class="td_dept_td"><input type="text" class="input" size="40" maxlength="100" name="sm_ot_tip" id="sm_ot_tip" onKeyPress="return noquotes(event)" value="<?php echo ( isset( $socials["other"]["tooltip"] ) && $socials["other"]["tooltip"] ) ? $socials["other"]["tooltip"] : "" ; ?>"></td>
				<td class="td_dept_td"><input type="text" class="input" size="50" maxlength="255" name="sm_ot_url" id="sm_ot_url" value="<?php echo ( isset( $socials["other"]["url"] ) && $socials["other"]["url"] ) ? $socials["other"]["url"] : "" ; ?>"> &nbsp; <span style="">&middot; <a href="JavaScript:void(0)" onClick="launch_sm('ot')">visit</a></span></td>
			</tr>
			<tr>
				<td class="td_dept_td" colspan="3">
					<input type="button" value="Update" onClick="do_submit()" class="btn">
					<?php
						if ( !count( $socials ) && $deptid ):
							print " currently using Global Default" ;
						elseif ( count( $socials ) && $deptid ):
							print " or reset and use <a href=\"JavaScript:void(0)\" onClick=\"reset_sm( $deptid )\">Global Default</a>" ;
						endif ;
					?>
				</td>
			</tr>
			</table>
			</form>
		</div>

<?php include_once( "./inc_footer.php" ) ?>
