<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/* Dev team: 615 */
	/****************************************/
	// STANDARD header for Setup
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/SQL.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;
	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	if ( !$opinfo = Util_Security_AuthOp( $dbh, $ses ) ){ ErrorHandler( 602, "Invalid operator session or session has expired.", $PHPLIVE_FULLURL, 0, Array() ) ; }
	// STANDARD header end
	/****************************************/

	include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/$CONF[lang].php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Canned/get.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$agent = isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : "&nbsp;" ;
	LIST( $os, $browser ) = Util_Format_GetOS( $agent ) ;
	$mobile = ( $os == 5 ) ? 1 : 0 ;
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
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/tooltip.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var loaded = 1 ;
	var mobile = <?php echo $mobile ?> ;
	var secondtime = 0 ;
	var map_left ;
	var global_div_width ;
	var global_div_height ;

	$(document).ready(function()
	{
		populate_traffic() ;
		init_depts() ;

		$('#canned_wrapper').fadeIn() ;

		//$(document).dblclick(function() {
		//	parent.close_extra( "traffic" ) ;
		//});

		$('#div_reference').fadeTo( 'fast', 0.6 ) ;
	});

	function init_depts()
	{
		var depts_select = "<select name=\"ini_deptid\" id=\"ini_deptid\" onChange=\"parent.initiate_deptid = this.value;\" style=\"min-width: 240px;\"><option value=0></option>" ;
		for ( var thisdeptid in parent.op_depts_hash )
		{
			var selected = "" ;
			if ( thisdeptid == parent.initiate_deptid )
				selected = "selected" ;

			depts_select += "<option value=\""+thisdeptid+"\" "+selected+">"+parent.op_depts_hash[thisdeptid]+"</option>" ;
		}
		depts_select += "</select>" ;

		$('#depts_select').html( depts_select ) ;
	}

	function init_trs( thetable )
	{
		$('#'+thetable+' tr:nth-child(2n+3)').addClass('chat_info_tr_traffic_row') ;
	}

	function populate_traffic()
	{
		var json_data = new Object ;
		var unique = unixtime() ;
		var image, image_info ;
		var footprints = new Object ;

		if ( parent.extra == "traffic" )
		{
			$.get("../ajax/chat_actions_op_itr.php", { action: "traffic", unique: unique },  function(data){
				eval( data ) ;

				if ( json_data.status )
				{
					var ip_exist = 0 ;
					var td_geoip = ( <?php echo $geoip ?> ) ? "<td width=\"18\"><div class=\"chat_info_td_t noround\" style=\"margin-right: 0px;\">&nbsp;</div></td>" : "" ;
					var td_ip = ( parent.viewip ) ? "<td width=\"90\"><div class=\"chat_info_td_t noround\" style=\"margin-right: 0px;\">IP</div></td>" : "" ;
					var traffic_string = "<table cellspacing=0 cellpadding=0 border=0 width=\"100%\" id=\"table_trs_traffic\"><tr><td width=\"23\"><div class=\"chat_info_td_t noround\" style=\"margin-right: 0px;\">&nbsp;</div></td><td width=\"80\"><div class=\"chat_info_td_t noround\" style=\"margin-right: 0px;\">Duration</div></td><td width=\"80\"><div class=\"chat_info_td_t noround\" style=\"margin-right: 0px;\">Campaign</div></td>"+td_geoip+td_ip+"<td width=\"70\"><div class=\"chat_info_td_t noround\" style=\"margin-right: 0px;\">Platform</div></td><td width=\"25\"><div class=\"chat_info_td_t noround\" style=\"margin-right: 0px;\">*F</div></td><td width=\"25\"><div class=\"chat_info_td_t noround\" style=\"margin-right: 0px;\">*C</div></td><td width=\"25\"><div class=\"chat_info_td_t noround\" style=\"margin-right: 0px;\">*I</div></td><td><div class=\"chat_info_td_t noround\" style=\"margin-right: 0px;\">On Page</div></td><td nowrap><div class=\"chat_info_td_t noround\" style=\"margin-right: 0px;\">Refer</div></td></tr>" ;

					for ( c = 0; c < json_data.traffics.length; ++c )
					{
						var market_name = ( typeof( parent.markets[json_data.traffics[c]["marketid"]]["name"] ) != "undefined" ) ? parent.markets[json_data.traffics[c]["marketid"]]["name"] : "&nbsp;" ;
						image = "actions.png" ; image_info = "&nbsp;" ;
						var td_map = ( <?php echo $geoip ?> ) ? "<td class=\"chat_info_td_traffic\" onClick=\"expand_map('"+json_data.traffics[c]["md5"]+"', '"+json_data.traffics[c]["ip"]+"', 1)\" id=\"footprint_map_"+json_data.traffics[c]["md5"]+"\"><span class=\"help_tooltip\" title=\"- Country: <b>"+parent.countries[json_data.traffics[c]["country"]]+"</b>, Region: <b>"+json_data.traffics[c]["region"]+"</b>, City: <b>"+json_data.traffics[c]["city"]+"</b>\"><img src=\"../pics/maps/"+json_data.traffics[c]["country"].toLowerCase()+".gif\" width=\"18\" height=\"12\" border=0 id=\"map_"+json_data.traffics[c]["md5"]+"\"></span></td>" : "" ;
						var td_viewip = ( parent.viewip ) ? "<td class=\"chat_info_td_traffic\"><span class=\"help_tooltip_help\" title=\"- Hostname: <b>"+json_data.traffics[c]["hostname"]+"</b>\">"+json_data.traffics[c]["ip"]+"</span></td>" : "" ;

						if ( json_data.traffics[c]["ip"] == parent.ip )
							ip_exist = 1 ;

						if ( json_data.traffics[c]["chatting"] )
						{
							image = "chats.png" ;
							image_info = "class=\"help_tooltip\" title=\"- currently chatting\"" ;
						}

						var url_raw = json_data.traffics[c]["onpage"] ;
						if ( url_raw == "livechatimagelink" )
							url_raw = "JavaScript:void(0)" ;
						
						traffic_string += "<tr style=\"\"><td class=\"chat_info_td_traffic help_tooltip\" title=\"- View Visitor Details\" width=\"23\" style=\"-moz-border-radius: 5px; border-radius: 5px;\" id=\"td_"+json_data.traffics[c]["md5"]+"\"><img src=\"../themes/<?php echo $opinfo["theme"] ?>/"+image+"\" border=\"0\" alt=\"\" style=\"cursor: pointer;\" "+image_info+" onClick=\"expand_footprint('"+json_data.traffics[c]["md5"]+"', '"+json_data.traffics[c]["duration"]+"', '"+market_name+"', '"+json_data.traffics[c]["ip"]+"', '"+json_data.traffics[c]["hostname"]+"', '"+json_data.traffics[c]["os"]+"', '"+json_data.traffics[c]["browser"]+"', '"+json_data.traffics[c]["resolution"]+"', '"+json_data.traffics[c]["t_footprints"]+"', '"+json_data.traffics[c]["t_requests"]+"', '"+json_data.traffics[c]["t_initiates"]+"', '"+json_data.traffics[c]["title"]+"', '"+json_data.traffics[c]["onpage"]+"', '"+json_data.traffics[c]["refer_snap"]+"', '"+json_data.traffics[c]["refer_raw"]+"', '"+json_data.traffics[c]["country"]+"', '"+json_data.traffics[c]["region"]+"', '"+json_data.traffics[c]["city"]+"' )\" id=\"footprint_"+json_data.traffics[c]["md5"]+"\"></td><td class=\"chat_info_td_traffic\" nowrap>"+json_data.traffics[c]["duration"]+"</td><td class=\"chat_info_td_traffic\">"+market_name+"</td>"+td_map+td_viewip+"<td class=\"chat_info_td_traffic\" style=\"text-align: center;\"><img src=\"../themes/<?php echo $opinfo["theme"] ?>/os/"+json_data.traffics[c]["os"]+".png\" border=0 alt=\""+json_data.traffics[c]["os"]+"\" class=\"help_tooltip_help\" title=\"- "+json_data.traffics[c]["os"]+"\" width=\"14\" height=\"14\"> &nbsp; <img src=\"../themes/<?php echo $opinfo["theme"] ?>/browsers/"+json_data.traffics[c]["browser"]+".png\" border=0 alt=\""+json_data.traffics[c]["browser"]+"\" class=\"help_tooltip_help\" title=\"- "+json_data.traffics[c]["browser"]+"\" width=\"14\" height=\"14\"></td><td class=\"chat_info_td_traffic\">"+json_data.traffics[c]["t_footprints"]+"</td><td class=\"chat_info_td_traffic\">"+json_data.traffics[c]["t_requests"]+"</td><td class=\"chat_info_td_traffic\">"+json_data.traffics[c]["t_initiates"]+"</td><td class=\"chat_info_td_traffic help_tooltip\" title=\"- "+json_data.traffics[c]["onpage"]+"\"><a href=\""+url_raw+"\" target=\"_blank\">"+json_data.traffics[c]["title"]+"</a></td><td class=\"chat_info_td_traffic\" nowrap><span class=\"help_tooltip\" title=\"- "+json_data.traffics[c]["refer_raw"]+"\"><a href=\""+json_data.traffics[c]["refer_raw"]+"\" target=\"_blank\">"+json_data.traffics[c]["refer_snap"]+"</a></span></td></tr>" ;

						footprints[json_data.traffics[c]["md5"]] = 1 ;
					}

					traffic_string += "</table>" ;

					$('#canned_body').empty().html( traffic_string ) ;
					if ( !parent.mobile ) { init_tooltips( 'canned_body' ) ; }
					init_trs( 'table_trs_traffic' ) ;

					if ( $('#canned_wrapper').is(':visible') )
					{
						// set traffic info div for browser compatibility
						global_div_width = $('#canned_body').outerWidth() ;
						global_div_height = parent.$('#chat_extra_wrapper').outerHeight() - 200 ;
					}

					for ( thismd5 in parent.maps_his_ )
					{
						if ( typeof( footprints[thismd5] ) == "undefined" )
							parent.delete_map( thismd5 ) ;
					}

					if ( ip_exist )
					{
						// todo: open up the ip automatically
						// still needs to be thought out on workings
					}
					$('#canned_body').show() ;

					
					if ( secondtime && !$('#footprint_info_wrapper').is(':visible') )
						do_alert( 1, "Refresh Success" ) ;

					++secondtime ;
				}
			});
		}
	}

	function expand_footprint( themd5, theduration, themarket, theip, thehostname, theos, thebrowser, theresolution, thet_footprints, thet_requests, thet_initiates, thetitle, theonpage, therefer_snap, therefer_raw, thecountry, theregion, thecity )
	{
		parent.ip = theip ;

		select_footprint( themd5 ) ;
		populate_footprint( themd5, theduration, themarket, theip, thehostname, theos, thebrowser, theresolution, thet_footprints, thet_requests, thet_initiates, thetitle, theonpage, therefer_snap, therefer_raw, thecountry, theregion, thecity ) ;
	}

	function select_footprint( themd5 )
	{
		$( '*', '#canned_body' ).each( function(){
			var div_name = $( this ).attr('id') ;
			if ( div_name.indexOf("td_") != -1 )
				$(this).removeClass('chat_info_td_traffic_img') ;
		} );

		$('#td_'+themd5).addClass('chat_info_td_traffic_img') ;
	}

	function populate_footprint( themd5, theduration, themarket, theip, thehostname, theos, thebrowser, theresolution, thet_footprints, thet_requests, thet_initiates, thetitle, theonpage, therefer_snap, therefer_raw, thecountry, theregion, thecity )
	{
		var unique = unixtime() ;
		var json_data = new Object ;
		var theip_q = theip.replace(/<span class=info_error>(.*?)<\/span>/, "$1") ;

		$.get("../ajax/chat_actions_op.php", { action: "footprints", ip: theip_q, unique: unique },  function(data){
			eval( data ) ;

			if ( json_data.status )
			{
				var footprints_string = "<table cellspacing=0 cellpadding=0 border=0>" ;
				for ( c = 0; c < json_data.footprints.length; ++c )
				{
					var url_raw = json_data.footprints[c]["onpage"] ;
					if ( url_raw == "livechatimagelink" )
						url_raw = "JavaScript:void(0)" ;

					footprints_string += "<tr><td width=\"30\" style=\"text-align: center\" class=\"chat_info_td\"><b>"+json_data.footprints[c]["total"]+"</b></td><td width=\"100%\" class=\"chat_info_td\"><div class=\"help_tooltip\" title=\"- "+json_data.footprints[c]["onpage"]+"\"><a href=\""+url_raw+"\" target=\"_blank\">"+json_data.footprints[c]["title"]+"</a></div></td></tr>" ;
				}
				footprints_string += "<tr><td colspan=2 class=\"chat_info_end\"></td></tr></table>" ;

				var url_raw = theonpage ; var refer_vis = therefer_raw ;
				if ( url_raw == "livechatimagelink" ) { url_raw = "JavaScript:void(0)" ; }

				if ( refer_vis.length > 80 ) { refer_vis = therefer_raw.substring(0,80)+"..." ; }

				$('#info_market').empty().html( themarket ) ;
				$('#info_duration').empty().html( theduration ) ;
				$('#info_requests').empty().html( thet_requests ) ;
				$('#info_initiates').empty().html( thet_initiates ) ;
				$('#info_platform').empty().html( "<img src=\"../themes/<?php echo $opinfo["theme"] ?>/os/"+theos+".png\" border=0 alt=\""+theos+"\" class=\"help_tooltip_help\" title=\"- "+theos+"\" width=\"14\" height=\"14\"> &nbsp; <img src=\"../themes/<?php echo $opinfo["theme"] ?>/browsers/"+thebrowser+".png\" border=0 alt=\""+thebrowser+"\" class=\"help_tooltip_help\" title=\"- "+thebrowser+"\" width=\"14\" height=\"14\">" ) ;
				$('#info_resolution').empty().html( theresolution ) ;
				$('#info_onpage').empty().html( "<div class=\"help_tooltip\" title=\"- "+theonpage+"\"><a href=\""+url_raw+"\" target=\"_blank\">"+thetitle+"</a></div>" ) ;
				$('#info_refer').empty().html( "<div class=\"help_tooltip\" title=\"- "+therefer_raw+"\"><a href=\""+therefer_raw+"\" target=\"_blank\">"+refer_vis+"</a></div>" ) ;
				$('#info_footprints').empty().html( footprints_string ) ;

				$('#footprint_info_wrapper').fadeIn('fast', function() {
					<?php if ( $geoip ): ?>
					var pos = $('#map_'+themd5).position() ;
					map_left = pos.left + 25 ;
					<?php endif ; ?>
				
					toggle_menu_info( "info" ) ;
					$('#canned_wrapper').hide() ;
					populate_requestinfo( theip, thehostname, themd5, thecountry, theregion, thecity ) ;
					populate_transcripts( theip ) ;
				}) ;
			}
		});
	}

	function populate_requestinfo( theip, thehostname, themd5, thecountry, theregion, thecity )
	{
		var unique = unixtime() ;
		var json_data = new Object ;
		var theip_q = theip.replace(/<span class=info_error>(.*?)<\/span>/, "$1") ;

		$.get("../ajax/chat_actions_op.php", { action: "requestinfo", ip: theip_q, unique: unique },  function(data){
			eval( data ) ;

			var span_map = ( <?php echo $geoip ?> ) ? "<span class=\"help_tooltip\" title=\"- Country: <b>"+parent.countries[thecountry]+"</b>, Region: <b>"+theregion+"</b>, City: <b>"+thecity+"</b>\" onClick=\"expand_map('"+themd5+"', '"+theip+"', 0)\"><img src=\"../pics/maps/"+thecountry.toLowerCase()+".gif\" width=\"18\" height=\"12\" border=0></span> &nbsp; " : "" ;
			var span_ip = ( parent.viewip ) ? "<span class=\"help_tooltip_help\" title=\"- "+thehostname+"\">"+theip+"</span>" : "" ;

			$('#info_ip').empty().html( span_map+span_ip ) ;
			if ( json_data.status )
			{
				$('#info_duration').empty().html( " <span class=\"info_good\"><img src=\"../themes/<?php echo $opinfo["theme"] ?>/info_chats.gif\" width=\"10\" height=\"10\" border=\"0\" alt=\"\" style=\"cursor: pointer;\" class=\"help_tooltip_help\" title=\"- currently in a chat session with "+json_data.name+"\"></span>" ) ;
			}
			$('#info_trans').empty().html( json_data.total_trans ) ;

			$('#chat_info_cans_select').empty().html( "<select id=\"canned_info_select\" style=\"min-width: 240px;\" onChange=\"select_canned()\"><option value=\"\"></option>"+parent.cans_string+"</select>" ) ;

			if ( typeof( parent.initiate_canid ) != "undefined" )
			{
				$('#canned_info_select').attr( 'selectedIndex', parent.initiate_canid ) ;
				select_canned() ;
			}

			if ( !parent.mobile ) { init_tooltips( 'footprint_info_wrapper' ) ; }
		});
	}

	function select_canned()
	{
		$( "#chat_info_initiate_message" ).val( $('#canned_info_select').val().stripv().replace( /<br>/g, "\r" ) ) ;
		parent.initiate_canid = $('#canned_info_select' ).attr( 'selectedIndex' ) ;
	}

	function close_footprint_info( theflag )
	{
		parent.ip = this.undefined ;

		$('#div_info_trans_list').hide() ;
		$('#div_info_trans_loading').show() ;
	
		if ( theflag )
			$('#footprint_info_wrapper').hide() ;
		else
		{
			$('#footprint_info_wrapper').fadeOut('fast', function() {
				$('#chatting_with').empty().html( "" ) ;
				setTimeout( function(){ toggle_menu_info( "info" ) ; }, 500 ) ;
			}) ;
		}

		$('#canned_wrapper').show() ;
	}

	function initiate_chat()
	{
		var unique = unixtime() ;
		var json_data = new Object ;
		var deptid = parseInt( $('#ini_deptid').val() ) ;
		var message = encodeURIComponent( $('#chat_info_initiate_message').val() ) ;
		
		if ( parent.ip.indexOf( "error" ) != -1 )
			parent.do_alert( 0, "IP is marked spam.  Request was not processed." ) ;
		else
		{
			if ( deptid && message )
			{
				$('#btn_initiate').html( "Connecting <img src=\"../themes/<?php echo $opinfo["theme"] ?>/loading_fb.gif\" width=\"16\" height=\"11\" border=\"0\" alt=\"\">" ).attr("disabled", true) ;
				$.get("../ajax/chat_actions_op.php", { action: "initiate", ip: parent.ip, deptid: deptid, question: message, unique: unique },  function(data){
					eval( data ) ;

					if ( json_data.status )
					{
						// should automatically close, but as a safe measure close it again with timeout
						setTimeout( function(){
							parent.input_focus() ;
							parent.close_extra( "traffic" ) ;
							$('#btn_initiate').attr("disabled", false) ;
						}, 5000 ) ;
					}
					else
					{
						do_alert( 0, json_data.error ) ;
						$('#btn_initiate').html('Initiate Chat').attr('disabled', false) ;
					}
				});
			}
			else if ( !message )
			{
				$('#chat_info_initiate_message').focus() ;
				do_alert( 0, "Blank Initiate Message is invalid." ) ;
			}
			else if ( !deptid )
			{
				$('#ini_deptid').focus() ;
				do_alert( 0, "Blank Department is invalid" ) ;
			}
		}
	}

	function expand_map( themd5, theip, theflag )
	{
		if ( theflag )
		{
			var pos = $('#map_'+themd5).position() ;
			map_left = pos.left + 25 ;
		}

		select_footprint( themd5 ) ;
		parent.expand_map( map_left, themd5, theip ) ;
	}

	function toggle_menu_info( themenu )
	{
		var divs = Array( "info", "trans", "initiate" ) ;

		$('#div_traffic_info_info').css({'width': global_div_width, 'height': global_div_height}) ;
		$('#div_traffic_info_trans').css({'width': global_div_width, 'height': global_div_height}) ;
		$('#div_traffic_info_initiate').css({'width': global_div_width, 'height': global_div_height}) ;

		for ( c = 0; c < divs.length; ++c )
		{
			$('#div_traffic_info_'+divs[c]).hide() ;
			$('#menu_traffic_info_'+divs[c]).removeClass('menu_traffic_info_focus').addClass('menu_traffic_info') ;
		}

		$('#div_traffic_info_'+themenu).show() ;
		$('#menu_traffic_info_'+themenu).removeClass('menu_traffic_info').addClass('menu_traffic_info_focus') ;

		if ( themenu == "trans" ) { init_trs( 'table_trs_trans' ) ; }
	}

	function populate_transcripts( theip )
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		$.get("../ajax/chat_actions_op.php", { action: "transcripts", ip: theip, unique: unique },  function(data){
			eval( data ) ;

			$('#div_info_trans_loading').hide() ;
			$('#div_info_trans_list').show() ;
			if ( json_data.status )
			{
				var transcripts_string = "<table cellspacing=0 cellpadding=0 border=0 width=\"100%\" id=\"table_trs_trans\"> <tr> <td width=\"28\" nowrap><div class=\"chat_info_td_t\">&nbsp;</div></td> <td width=\"150\"><div class=\"chat_info_td_t\">Operator</div></td> <td width=\"80\"><div class=\"chat_info_td_t\">Visitor</div></td> <td><div class=\"chat_info_td_t\">Rating</div></td> <td><div class=\"chat_info_td_t\">Created</div></td> <td width=\"80\"><div class=\"chat_info_td_t\">Duration</div></td> <td width=\"100%\"><div class=\"chat_info_td_t\">Question</div></td> </tr>" ;
				for ( c = 0; c < json_data.transcripts.length; ++c )
				{
					transcripts_string += "<tr><td class=\"chat_info_td_traffic\" width=\"16\" id=\"img_"+json_data.transcripts[c]["ces"]+"\" style=\"-moz-border-radius: 5px; border-radius: 5px;\"><img src=\"../themes/<?php echo $opinfo["theme"] ?>/view.png\" onClick=\"parent.open_transcript('"+json_data.transcripts[c]["ces"]+"')\" width=\"16\" height=\"16\" class=\"help_tooltip\" title=\"- view transcript\"></td><td class=\"chat_info_td_traffic\"><b>"+json_data.transcripts[c]["operator"]+"</b></td><td class=\"chat_info_td_traffic\">"+json_data.transcripts[c]["vname"]+"</td><td class=\"chat_info_td_traffic\">rating</td><td class=\"chat_info_td_traffic\" nowrap>"+json_data.transcripts[c]["created"]+"</td><td class=\"chat_info_td_traffic\" nowrap>"+json_data.transcripts[c]["duration"]+"</td><td class=\"chat_info_td_traffic\">"+json_data.transcripts[c]["question"]+"</td></tr>" ;
				}

				if ( json_data.transcripts.length == 0 )
					transcripts_string += "<tr><td colspan=7>blank results</td></tr>" ;

				transcripts_string += "</table>" ;
				$('#div_info_trans_list').empty().html( transcripts_string ) ;

				if ( !mobile ) { init_tooltips( 'div_info_trans_list' ) ; }
			}
		});
	}

	function set_trans_img( theces )
	{
		if ( typeof( parent.ces_trans ) != "undefined" )
			$('#img_'+parent.ces_trans).removeClass('chat_info_td_traffic_img') ;

		parent.ces_trans = theces ;
		$('#img_'+theces).addClass('chat_info_td_traffic_img') ;
	}

	function init_tooltips( thediv )
	{
		var help_tooltips = $( '#'+thediv ).find( '.help_tooltip' ) ;
		help_tooltips.tooltip({
			track: true, 
			delay: 0, 
			showURL: false, 
			showBody: "- ", 
			fade: 0
		});
		var help_tooltips_help = $( '#'+thediv ).find( '.help_tooltip_help' ) ;
		help_tooltips_help.tooltip({
			track: true, 
			delay: 0, 
			showURL: false, 
			showBody: "- ", 
			fade: 0
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
			<div class="chat_info_td_t" style="font-size: 10px; text-align: right; font-weight: normal; margin-right: 0px;"><span id="div_reference">*F = Total Footprints, *C = Total Chat Requests, *I = Total Operator Initiated Chat</span></div>
			<div id="canned_body" style="padding-bottom: 10px;"><img src="../themes/<?php echo $opinfo["theme"] ?>/loading_fb.gif" width="16" height="11" border="0"></div>
		</td><td class="t_mr"></td>
	</tr>
	<tr><td class="t_bl"></td><td class="t_bm"></td><td class="t_br"></td></tr>
	</table>
</div>

<div id="footprint_info_wrapper" style="position: absolute; display: none; top: 0px; left: 0px; height: 100%;">
	<table cellspacing=0 cellpadding=0 border=0 width="100%"><tr><td class="t_tl"></td><td class="t_tm"></td><td class="t_tr"></td></tr>
	<tr>
		<td class="t_ml"></td><td class="t_mm">
			<div><span onClick="close_footprint_info(0)" style="cursor: pointer;"><img src="../themes/<?php echo $opinfo["theme"] ?>/close_extra.png" width="12" height="12" border="0" alt="close info" title="close info"> close</span></div>
			<div style="margin-top: 15px;">
				<div id="menu_traffic_info_info" class="menu_traffic_info_focus" onClick="toggle_menu_info('info')">Visitor Information</div>
				<div id="menu_traffic_info_trans" class="menu_traffic_info" onClick="toggle_menu_info('trans')">Past Transcript (<span id="info_trans"></span>)</div>
				<div id="menu_traffic_info_initiate" class="menu_traffic_info" onClick="toggle_menu_info('initiate')">Initiate Chat</div>
				<div style="clear: both;"></div>
			</div>
			<div id="div_traffic_info_info" style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td valign="top" width="30%">
						<table cellspacing=0 cellpadding=0 border=0 width="100%">
						<tr>
							<td class="chat_info_td_traffic">Platform</td>
							<td class="chat_info_td_traffic_info" width="100%"><span id="info_resolution"></span> &nbsp; <span id="info_platform"></span></td>
						</tr>
						<tr>
							<td class="chat_info_td_traffic">Duration</td>
							<td class="chat_info_td_traffic_info"><span id="info_duration"></span></td>
						</tr>
						<tr>
							<td class="chat_info_td_traffic" nowrap>Requested Chat</td>
							<td class="chat_info_td_traffic_info"><span id="info_requests"></span></td>
						</tr>
						<tr>
							<td class="chat_info_td_traffic" nowrap>Op Initiated Chat</td>
							<td class="chat_info_td_traffic_info"><span id="info_initiates"></span></td>
						</tr>
						<tr>
							<td class="chat_info_td_traffic">Location</td>
							<td class="chat_info_td_traffic_info"><div id="info_ip"></div></td>
						</tr>
						</table>
					</td>
					<td valign="top" width="70%">
						<table cellspacing=0 cellpadding=0 border=0 width="100%">
						<tr>
							<td class="chat_info_td_traffic" width="100">On Page</td>
							<td class="chat_info_td_traffic_info" width="100%"><div id="info_onpage"></div></td>
						</tr>
						<tr>
							<td class="chat_info_td_traffic" nowrap>Refer URL</td>
							<td class="chat_info_td_traffic_info"><div id="info_refer"></div></td>
						</tr>
						<tr>
							<?php if ( !isset( $CONF["foot_log"] ) || ( $CONF["foot_log"] == "on" ) ): ?>
							<td class="chat_info_td_traffic">Footprints</td>
							<td class="chat_info_td_traffic">
								<div style="max-height: 180px; width: 100%; overflow: auto;" id="info_footprints"></div>
							</td>
							<?php endif;  ?>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</div>
			<div id="div_traffic_info_trans" style="display: none; margin-top: 15px; overflow: auto;">
				<div id="div_info_trans_loading"><img src="../themes/<?php echo $opinfo["theme"] ?>/loading_fb.gif" width="16" height="11" border="0" alt=""></div>
				<div id="div_info_trans_list" style="display: none;"></div>
			</div>
			<div id="div_traffic_info_initiate" style="display: none; margin-top: 15px;">
				<div>Chat Introduction Message:<br><span style="font-size: 10px;">* brief message (recommended: 25 words or fewer)</span></div>
				<div style="margin-top: 5px;"><textarea id="chat_info_initiate_message" class="input_text" cols="80" rows="3" wrap="virtual" style="resize: none;"></textarea></div>
				<form action="#">
				<div style="margin-top: 15px;">
					<div>OR choose from available Canned Responses:</div>
					<div style="margin-top: 5px;"><span id="chat_info_cans_select" style="padding-right: 10px;"></span></div>
				</div>
				<div style="margin-top: 15px;">
					<div>Select Department:</div>
					<div style="margin-top: 5px;"><span id="depts_select"></span></div>
				</div>
				<div style="margin-top: 15px;"><button type="button" onClick="initiate_chat()" id="btn_initiate">Initiate Chat</button></div>
				</form>
			</div>
		</td><td class="t_mr"></td>
	</tr>
	<tr><td class="t_bl"></td><td class="t_bm"></td><td class="t_br"></td></tr>
	</table>
</div>

</body>
</html>
<?php database_mysql_close( $dbh ) ; ?>
