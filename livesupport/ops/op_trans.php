<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/* Dev team: 615 */
	/****************************************/
	// STANDARD header for Setup
	include_once( "../web/config.php" ) ;
	include_once( "../API/Util_Format.php" ) ;
	include_once( "../API/Util_Error.php" ) ;
	include_once( "../API/SQL.php" ) ;
	include_once( "../API/Util_Security.php" ) ;
	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	if ( !$opinfo = Util_Security_AuthOp( $dbh, $ses ) ){ ErrorHandler( 602, "Invalid operator session or session has expired.", $PHPLIVE_FULLURL, 0, Array() ) ; }
	// STANDARD header end
	/****************************************/

	include_once( "../API/Util_Functions.php" ) ;
	include_once( "../API/Ops/get.php" ) ;
	include_once( "../API/Depts/get.php" ) ;
	include_once( "../API/Chat/get_ext.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$page = ( Util_Format_Sanatize( Util_Format_GetVar( "page" ), "ln" ) ) ? Util_Format_Sanatize( Util_Format_GetVar( "page" ), "ln" ) : 0 ;
	$index = ( Util_Format_Sanatize( Util_Format_GetVar( "index" ), "ln" ) ) ? Util_Format_Sanatize( Util_Format_GetVar( "index" ), "ln" ) : 0 ;
	$theme = $opinfo["theme"] ;

	$operators = Ops_get_AllOps( $dbh ) ;
	$departments = Depts_get_OpDepts( $dbh, $opinfo["opID"] ) ;

	// make hash for quick refrence
	$operators_hash = Array() ;
	for ( $c = 0; $c < count( $operators ); ++$c )
	{
		$operator = $operators[$c] ;
		$operators_hash[$operator["opID"]] = $operator["name"] ;
	}

	$dept_hash = Array() ;
	for ( $c = 0; $c < count( $departments ); ++$c )
	{
		$department = $departments[$c] ;
		$dept_hash[$department["deptID"]] = $department["name"] ;
	}

	$text = Util_Format_Sanatize( Util_Format_GetVar( "text" ), "" ) ;
	$text = ( $text ) ? $text : "" ;
	$text_query = urlencode( $text ) ;
	$transcripts = Chat_ext_get_OpDeptTrans( $dbh, $opinfo["opID"], $text, $page, 20 ) ;
	
	$total_index = count($transcripts) - 1 ;
	$pages = Util_Functions_Page( $page, $index, 20, $transcripts[$total_index], "op_trans.php", "ses=$ses&text=$text_query" ) ;
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> PHP Live! Support <?php echo $VERSION ?> </title>

<meta name="description" content="PHP Live! Support <?php echo $VERSION ?>">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8"> 
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<link rel="Stylesheet" href="../themes/<?php echo $opinfo["theme"] ?>/style.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/global_chat.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/tooltip.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var reset_url = "op_trans.php?ses=<?php echo $ses ?>" ;
	var ces, newwin ;
	var widget ;

	$(document).ready(function()
	{
		init_trs() ;
		if ( !parent.mobile ) { init_tooltips() ; }

		$('#input_search').focus() ;
		$('#form_search').bind("submit", function() { return false ; }) ;

		$('#canned_wrapper').fadeIn() ;

		//$(document).dblclick(function() {
		//	parent.close_extra( "trans" ) ;
		//});
	});

	function init_trs()
	{
		$('#table_trs tr:nth-child(2n+3)').addClass('chat_info_tr_traffic_row') ;
	}

	function open_transcript( theces )
	{
		if ( typeof( ces ) != "undefined" )
			$('#img_'+ces).removeClass('chat_info_td_traffic_img') ;

		ces = theces ;
		$('#img_'+ces).addClass('chat_info_td_traffic_img') ;

		//$('#transcript_'+theces).html( "<img src=\"../themes/<?php echo $opinfo["theme"] ?>/loading_fb.gif\" border=\"0\" alt=\"\">" ) ;
		parent.open_transcript( theces ) ;
	}

	function input_text_listen_search( e )
	{
		var key = -1 ;
		var shift ;

		key = e.keyCode ;
		shift = e.shiftKey ;

		if ( !shift && ( ( key == 13 ) || ( key == 10 ) ) )
			$('#btn_page_search').click() ;
	}

	function init_tooltips()
	{
		var help_tooltips = $( '#table_trs' ).find( '.help_tooltip' ) ;
		help_tooltips.tooltip({
			event: "mouseover",
			track: true,
			delay: 0,
			showURL: false,
			showBody: "- ",
			fade: 0,
			positionLeft: false
		});
	}

//-->
</script>
</head>
<body>

<div id="canned_wrapper" style="display: none; height: 100%; overflow: auto;">
	<table cellspacing=0 cellpadding=0 border=0 width="100%"><tr><td class="t_tl"></td><td class="t_tm"></td><td class="t_tr"></td></tr>
	<tr>
		<td class="t_ml"></td><td class="t_mm">

			<div class="page_top_wrapper"><?php echo $pages ?></div>
			<table cellspacing=0 cellpadding=0 border=0 width="100%" id="table_trs">
			<tr>
				<td width="28" nowrap><div class="chat_info_td_t">&nbsp;</div></td>
				<td width="150"><div class="chat_info_td_t">Operator</div></td>
				<td width="80"><div class="chat_info_td_t">Visitor</div></td>
				<td><div class="chat_info_td_t">Rating</div></td>
				<td><div class="chat_info_td_t">Created</div></td>
				<td width="80"><div class="chat_info_td_t">Duration</div></td>
				<td width="100%"><div class="chat_info_td_t">Question</div></td>
			</tr>
			<?php
				for ( $c = 0; $c < count( $transcripts )-1; ++$c )
				{
					$transcript = $transcripts[$c] ;

					// weed out random bugs of no operator data
					if ( $transcript["opID"] )
					{
						$operator = ( $transcript["op2op"] ) ? $operators_hash[$transcript["op2op"]] : $operators_hash[$transcript["opID"]] ;
						$created = date( "M j (g:i:s a)", $transcript["created"] ) ;
						$duration = $transcript["ended"] - $transcript["created"] ;
						$duration = ( ( $duration - 60 ) < 1 ) ? " 1 min" : Util_Format_Duration( $duration ) ;
						$question = $transcript["question"] ;
						$vname = ( $transcript["op2op"] ) ? $operators_hash[$transcript["opID"]] : $transcript["vname"] ;
						$rating = Util_Functions_Stars( $transcript["rating"] ) ;
						$initiated = ( $transcript["initiated"] ) ?  "<img src=\"../themes/$opinfo[theme]/info_initiate.gif\" width=\"10\" height=\"10\" border=\"0\" alt=\"\" class=\"help_tooltip\" title=\"- Operator Initiated Chat\" style=\"cursor: help;\"> " : "" ;

						if ( $transcript["op2op"] )
							$question = "<div class=\"text_grey\"><img src=\"../themes/$opinfo[theme]/agent.png\" width=\"16\" height=\"16\" border=\"0\" class=\"help_tooltip\" title=\"- Operator to Operator Chat\" style=\"cursor: help;\"></div>" ;

						print "<tr><td class=\"chat_info_td_traffic\" width=\"16\" id=\"img_$transcript[ces]\" style=\"-moz-border-radius: 5px; border-radius: 5px;\"><img src=\"../themes/$opinfo[theme]/view.png\" onClick=\"open_transcript('$transcript[ces]')\" width=\"16\" height=\"16\" class=\"help_tooltip\" title=\"- view transcript\"></td><td class=\"chat_info_td_traffic\" nowrap><b><div id=\"transcript_$transcript[ces]\">$initiated $operator</div></b></td><td class=\"chat_info_td_traffic\">$vname</td><td class=\"chat_info_td_traffic\" align=\"center\">$rating</td><td class=\"chat_info_td_traffic\" nowrap>$created</td><td class=\"chat_info_td_traffic\">$duration</td><td class=\"chat_info_td_traffic\">$question</td></tr>" ;
					}
				}
				if ( $c == 0 )
					print "<tr><td class=\"chat_info_td_traffic\">&nbsp;</td><td colspan=7 class=\"chat_info_td_traffic\">&nbsp;</td></tr>" ;
			?>
			</table>
			<div class="chat_info_end"></div>

		</td><td class="t_mr"></td>
	</tr>
	<tr><td class="t_bl"></td><td class="t_bm"></td><td class="t_br"></td></tr>
	</table>

	<div style="height: 25px; width: 10px;"></div>
</div>

</body>
</html>
<?php database_mysql_close( $dbh ) ; ?>