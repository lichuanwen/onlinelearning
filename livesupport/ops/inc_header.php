<body style="">

<script type="text/javascript">
<!--
	$(init_inview) ;
	$(window).scroll( init_inview ) ;

	function check_inview( theobject )
	{
		var scroll_top = $(window).scrollTop() ;
		var scroll_view = scroll_top + $(window).height() ;

		var pos_top = $(theobject).offset().top ;
		var pos_bottom = pos_top + $(theobject).height() ;

		return ((pos_bottom <= scroll_view) && (pos_top >= scroll_top) ) ;
	}

	function init_inview() {
		if ( check_inview( $('#menu_wrapper') ) )
			$('#div_scrolltop').fadeOut("fast") ;
		else
			$('#div_scrolltop').fadeIn("fast") ;
	}

	function scroll_top()
	{
		$('html, body').animate({
			scrollTop: 0
		}, 200);
	}

//-->
</script>

<div id="div_scrolltop" style="display: none; position: fixed; top: 25%; right: 0px; z-index: 1000;">
	<div style="padding: 5px; background: #DFDFDF; border: 1px solid #B9B9B9; border-right: 0px; text-shadow: 1px 1px #FFFFFF; border-top-left-radius: 5px 5px; -moz-border-radius-topleft: 5px 5px; border-bottom-left-radius: 5px 5px; -moz-border-radius-bottomleft: 5px 5px; cursor: pointer;" onClick="scroll_top()"><img src="../pics/icons/arrow_top.png" width="15" height="16" border="0" alt=""> top</div>
</div>

<div id="header_wrapper" style="background: #77B6C5;">
	<div style="background: url( <?php echo $CONF["BASE_URL"] ?>/pics/clouds.png ) repeat-x; background-position: bottom;">
		<div style="width: <?php echo $body_width  ?>px; margin: 0 auto;">
			<div id="menu_wrapper" style="padding-top: 20px;">
				<?php if ( !$console ): ?>
				<div id="menu_go" class="menu" onClick="<?php echo ( preg_match( "/(cans)|(transcript)|(report)/", $menu ) ) ? "location.href='./?ses=$ses'" : "toggle_menu_op('go', '$ses')" ; ?>"><img src="../pics/icons/bulb.png" width="12" height="12" border="0" alt=""> Go ONLINE!</div>
				<div id="menu_trans" class="menu" onClick="location.href='transcripts.php?console=<?php echo $console ?>&ses=<?php echo $ses ?>'"><img src="../pics/icons/menu_trans.png" width="12" height="12" border="0" alt=""> Transcripts</div>
				<?php endif ; ?>
				<div id="menu_themes" class="menu" onClick="<?php echo ( preg_match( "/(cans)|(transcript)|(report)/", $menu ) ) ? "location.href='./?menu=themes&console=$console&ses=$ses'" : "toggle_menu_op('themes', '$ses')" ; ?>"><img src="../pics/icons/menu_icons.png" width="12" height="12" border="0" alt=""> Themes</div>
				<div id="menu_sounds" class="menu" onClick="<?php echo ( preg_match( "/(cans)|(transcript)|(report)/", $menu ) ) ? "location.href='./?menu=sounds&console=$console&ses=$ses'" : "toggle_menu_op('sounds', '$ses')" ; ?>"><img src="../pics/icons/menu_sound.png" width="12" height="12" border="0" alt=""> Sounds</div>
				<div id="menu_dn" class="menu" onClick="<?php echo ( preg_match( "/(cans)|(transcript)|(report)/", $menu ) ) ? "location.href='./?menu=dn&console=$console&ses=$ses'" : "toggle_menu_op('dn', '$ses')" ; ?>" style="display: none;"><img src="../pics/icons/menu_dn.png" width="12" height="12" border="0" alt=""> Desktop Alert</div>
				<?php if ( $opinfo["sms"] ): ?><div id="menu_mobile" class="menu" onClick="<?php echo ( preg_match( "/(cans)|(transcript)|(report)/", $menu ) ) ? "location.href='./?menu=mobile&console=$console&ses=$ses'" : "toggle_menu_op('mobile', '$ses')" ; ?>"><img src="../pics/icons/menu_mobile.png" width="12" height="12" border="0" alt=""> SMS</div><?php endif ; ?>
				<?php if ( !$console ): ?><div id="menu_cans" class="menu" onClick="location.href='./cans.php?ses=<?php echo $ses ?>'"><img src="../pics/icons/menu_cans.png" width="12" height="12" border="0" alt=""> Canned Responses</div><?php endif ; ?>
				<div id="menu_reports" class="menu" onClick="location.href='./reports.php?console=<?php echo $console ?>&ses=<?php echo $ses ?>'"><img src="../pics/icons/menu_calendar.png" width="12" height="12" border="0" alt=""> Online Activity</div>
				<div id="menu_password" class="menu" onClick="<?php echo ( preg_match( "/(cans)|(transcript)|(report)/", $menu ) ) ? "location.href='./?menu=password&console=$console&ses=$ses'" : "toggle_menu_op('password', '$ses')" ; ?>"><img src="../pics/icons/menu_settings.png" width="12" height="12" border="0" alt=""> Password</div>
				<div style="clear: both;"></div>
			</div>
		</div>
		<div style="margin-top: 15px; padding-top: 15px;">
			<div style="width: <?php echo $body_width  ?>px; margin: 0 auto; padding-bottom: 10px;">
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td align="right" valign="top">
						<div style="display: inline-block; padding: 5px; background: #FFFFFF; border: 5px solid #EDF6FA; color: #A3A3A3; text-align: center;" class="round_top"><img src="../pics/icons/vcard.png" width="16" height="16" border="0" alt=""> <b><?php echo $opinfo["name"] ?></b> (<?php echo $opinfo["login"] ?>) <?php if ( !$console ): ?>&bull; <a href="JavaScript:void(0)" onClick="logout_op('$ses')">sign out</a><?php endif ; ?></div>
					</td>
				</tr>
				</table>
			</div>
		</div>
	</div>
</div>

<div style="width: 100%; padding-top: 60px; padding-bottom: 100px; background: url( ../pics/bg_header.gif ) repeat-x #FFFFFF;">
	<div style="width: <?php echo $body_width  ?>px; margin: 0 auto;">

