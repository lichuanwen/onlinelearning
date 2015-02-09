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

	include_once( "../API/Chat/get_ext.php" ) ;
	include_once( "../API/Ops/update_itr.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$console = Util_Format_Sanatize( Util_Format_GetVar( "console" ), "ln" ) ; $body_width = ( $console ) ? 800 : 900 ;
	$m = Util_Format_Sanatize( Util_Format_GetVar( "m" ), "ln" ) ;
	$d = Util_Format_Sanatize( Util_Format_GetVar( "d" ), "ln" ) ;
	$y = Util_Format_Sanatize( Util_Format_GetVar( "y" ), "ln" ) ;

	if ( !$m )
		$m = date( "m", time() ) ;
	if ( !$d )
		$d = date( "j", time() ) ;
	if ( !$y )
		$y = date( "Y", time() ) ;

	$stat_end = mktime( 0, 0, 1, $m+1, 0, $y ) ;
	$stat_end_day = date( "j", $stat_end ) ;

	$menu = "reports" ;
	$error = "" ;

	Ops_update_itr_IdleOps( $dbh ) ;
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> Operator </title>

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
		toggle_menu_op( "<?php echo $menu ?>", '<?php echo $ses ?>' ) ;

		if ( ( typeof( window.external ) != "undefined" ) && ( typeof( window.external.wp_save_history ) != "undefined" ) ) { $('#menu_dn').hide() ; }
		else { $('#menu_dn').show() ; }
	});

//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ; ?>

		<div id="op_reports" style="display: none; margin: 0 auto;">
			<img src="../pics/icons/calendar.png" width="16" height="16" border="0" alt=""> Online/Offline activity report. The display outputs are for days with online activities only.
			<div style="margin-top: 25px;">
				<?php include_once( "./inc_select_cal.php" ) ; ?>
			</div>

			<div id="reports" style="margin-top: 25px;">
				<?php
					$overall_duration = null ;
					for ( $c = 1; $c <= $stat_end_day; ++$c )
					{
						$stat_day = mktime( 0, 0, 0, $m, $c, $y ) ;
						$stat_day_expand = date( "D M j, Y", mktime( 0, 0, 0, $m, $c, $y ) ) ;

						$stat_start = mktime( 0, 0, 0, $m, $c, $y ) ;
						$stat_end = mktime( 23, 60, 60, $m, $c, $y ) ;

						$reports = Chat_ext_get_OpStatusLog( $dbh, $opinfo["opID"], $stat_start, $stat_end ) ;

						$output = "" ;
						if ( count( $reports ) > 0 )
						{
							$prev_created = $total_duration = 0 ;
							for( $c2 = 0; $c2 < count( $reports ) ; ++$c2 )
							{
								$report = $reports[$c2] ;

								$diff = 0 ;
								$created = $report["created"] ;
								$created_expand = date( "M j (g:i:s a)", $created ) ;
								$status_text = ( $report["status"] ) ? "Online" : "Offline" ;

								if ( $report["status"] && !$prev_created )
									$prev_created = $created ;
								else if ( !$report["status"] && !$prev_created && !$c2 && $overall_duration )
								{
									// is online from previous day
									$created_ = mktime( 0, 0, 0, date( "m", $created ), date( "j", $created ), date( "Y", $created ) ) ;
									$created_expand_ = date( "M j (g:i:s a)", $created_ ) ;
									$total_duration += $created - $created_ ;

									$output .= "<div class=\"td_dept_td\"><img src=\"../pics/icons/online_green.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"\"> <span style=\"padding-left: 25px;\">reset at: $created_expand_</span></div>" ;
								}
								else if ( !$report["status"] && $prev_created )
								{
									$diff = $report["created"] - $prev_created ;
									$total_duration += $diff ;
									$prev_created = 0 ;
								}

								$class = "td_dept_td" ;
								$status = ( $report["status"] ) ? "<img src=\"../pics/icons/online_green.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"\">" : "<img src=\"../pics/icons/online_grey.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"\">" ;

								$output .= "<div class=\"$class\">$status <span style=\"padding-left: 25px;\">$created_expand <small>- $status_text</small></span></div>" ;
							}

							if ( $prev_created )
							{
								$now = time() ;
								$diff = $now - $prev_created ;

								if ( date( "j", ( $diff + $now ) ) != date( "j", $now ) )
								{
									// when the online time goes past the end of the day
									$total_duration += mktime( 0, 0, 0, $m,date( "j", $prev_created )+1, $y ) - $prev_created ;
								}
								else
									$total_duration += $diff ;
							}

							if ( $total_duration && ( $total_duration < 60 ) )
								$total_duration = 60 ;

							$overall_duration += $total_duration ;
							$duration = Util_Format_Duration( $total_duration ) ;

							print "<div style=\"margin-bottom: 5px; float: left; width: 33%;\"><div class=\"td_dept_header\"><table cellspacing=0 cellpadding=0 border=0><tr><td width=\"120\">$stat_day_expand</td><td style=\"font-weight: normal;\">Online: &nbsp; <span class=\"info_neutral\">$duration</span></td></tr></table></div><div id=\"stat_$c\" style=\"height: 100px; overflow: auto;\">$output</div></div>" ;
						}
					}
					if ( !is_null( $overall_duration ) )
						print "<div style=\"clear: both;\"></div>" ;
					else
						print "<div style=\"\">blank results</div>" ;

					$duration = Util_Format_Duration( $overall_duration ) ;
					$month = date( "F", $stat_start ) ;
					$year = date( "Y", $stat_start ) ;
					print "<div style=\"margin-top: 15px;\"><table cellspacing=0 cellpadding=0 border=0><tr><td nowrap><div style=\"\">Total duration online </div></td><td style=\"font-weight: normal; padding-left: 15px;\"><span class=\"info_good\">$duration</span></td></tr></table></div>" ;
				?>
			</div>
		</div>

<?php include_once( "./inc_footer.php" ) ; ?>
