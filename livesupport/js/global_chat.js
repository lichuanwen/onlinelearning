function add_text( thetext )
{
	if ( ( thetext != "" ) && ( typeof( chats[ces] ) != "undefined" ) )
	{
		thetext = init_timestamps( thetext.nl2br() ) ;
		chats[ces]["trans"] += thetext ;
		$('#chat_body').append( thetext ) ;

		// on IE 8 (standard view) it's not remembering the srollTop... when any div takes focus away
		// scrolling goes back to ZERO... effects are minimal
		init_scrolling() ;
	}
}

function add_text_prepare()
{
	if ( isop )
		thetext = $( "textarea#input_text" ).val().trimreturn().stripv().noreturns().tags().vars() ;
	else
	{
		thetext = $( "textarea#input_text" ).val().trimreturn().stripv().noreturns().tags() ;
		thetext = thetext.autoLink({ target: "new" }) ;
	}

	if ( ( thetext != "" ) && ( typeof( chats[ces] ) != "undefined" ) )
	{
		var cdiv ;
		var now = unixtime() ;

		if ( isop )
		{
			if ( chats[ces]["op2op"] )
			{
				if ( chats[ces]["op2op"] == isop )
					cdiv = "co" ;
				else
					cdiv = "cv" ;
			}
			else
				cdiv = "co" ;
		}
		else
			cdiv = "cv" ;

		thetext = "<"+cid+"><div class='"+cdiv+"'><span class='notranslate'><b>"+cname+"<timestamp_"+now+"_"+cdiv+">:</b></span> "+thetext+"</div></"+cid+">" ;

		$('#chat_processing').html( "<img src=\""+base_url+"/themes/"+theme+"/loading_chat.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"\">" ) ;
		add_text( thetext ) ;
		http_text( thetext ) ;
	}
	$('button#input_btn').attr( "disabled", true ) ;
	$('textarea#input_text').val( "" ) ;

	if ( !mobile ) { $('textarea#input_text').focus() ; }
}

function http_text( thetext )
{
	var json_data = new Object ;
	var unique = unixtime() ;
	var wname = encodeURIComponent( cname ) ;
	var rname ;
	
	var thesalt = ( typeof( salt ) != "undefined" ) ? salt : "nosalt" ;

	if ( isop )
		rname = encodeURIComponent( chats[ces]["vname"] ) ;
	else
		rname = encodeURIComponent( chats[ces]["oname"] ) ;

	if ( typeof( chats[ces] ) != "undefined" )
	{
		$.post( base_url+"/ajax/chat_submit.php", { requestid: chats[ces]["requestid"], isop: isop, isop_: isop_, isop__: isop__, wname: wname, rname: rname, ces: ces, text: thetext, salt: thesalt, unique: unique },  function(data){
			eval( data ) ;
			if ( json_data.status ) {
				$('#chat_processing').html( "<img src=\""+base_url+"/pics/space.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"\">" ) ;
				clearTimeout( st_typing ) ;
				st_typing = this.undefined ;
			}
			else
				do_alert( 0, "Error.  Please try again. [e1]" ) ;
		});
	}
}

function input_text_listen( e )
{
	var key = -1 ;
	var shift ;

	key = e.keyCode ;
	shift = e.shiftKey ;

	if ( !shift && ( ( key == 13 ) || ( key == 10 ) ) )
		add_text_prepare() ;
	else if ( ( key == 8 ) || ( key == 46 ) )
	{
		if ( $( "textarea#input_text" ).val() == "" )
			$( "button#input_btn" ).attr( "disabled", true ) ;
	}
	else if ( $( "textarea#input_text" ).val() == "" )
		$( "button#input_btn" ).attr( "disabled", true ) ;
	else
		$( "button#input_btn" ).attr( "disabled", false ) ;
}

function input_text_typing( e )
{
	input_focus() ;

	if ( $( "textarea#input_text" ).val() )
	{
		if ( typeof( st_typing ) == "undefined" )
		{
			send_istyping() ;
			st_typing = setTimeout( function(){ clear_istyping() ; }, 5000 ) ;
		}
	}
}

function init_typing()
{
	si_typing = setInterval(function(){
		if ( typeof( chats[ces] ) != "undefined" )
		{
			if ( chats[ces]["istyping"] )
				$('#chat_vistyping').empty().html( " is typing..." ) ;
			else
				$('#chat_vistyping').empty().html( "" ) ;
		}
	}, 1500) ;
}

function send_istyping()
{
	var json_data = new Object ;
	var unique = unixtime() ;
	var wname = encodeURIComponent( cname ) ;
	var rname ;

	if ( isop )
		rname = encodeURIComponent( chats[ces]["vname"] ) ;
	else
		rname = encodeURIComponent( chats[ces]["oname"] ) ;

	if ( typeof( chats[ces] ) != "undefined" )
	{
		$.get( base_url+"/ajax/chat_actions.php", { action: "istyping", isop: isop, isop_: isop_, isop_: isop_, wname: wname, rname: rname, ces: ces, flag: 1, unique: unique },  function(data){
			eval( data ) ;

			if ( json_data.status ) {
				return true ;
			}
			else
				do_alert( 0, "Error: Error.  Please try again. [e2]" ) ;
		});
	}
}

function clear_istyping()
{
	var json_data = new Object ;
	var unique = unixtime() ;
	var wname = encodeURIComponent( cname ) ;
	var rname ;

	if ( isop )
		rname = encodeURIComponent( chats[ces]["vname"] ) ;
	else
		rname = encodeURIComponent( chats[ces]["oname"] ) ;

	if ( typeof( chats[ces] ) != "undefined" )
	{
		$.get( base_url+"/ajax/chat_actions.php", { action: "istyping", isop: isop, isop_: isop_, isop_: isop_, wname: wname, rname: rname, ces: ces, flag: 0, unique: unique },  function(data){
			eval( data ) ;
			
			if ( json_data.status ) {
				clearTimeout( st_typing ) ;
				st_typing = this.undefined ;
			}
		});
	}
}

function init_scrolling()
{
	if ( ( typeof( chats[ces] ) != "undefined" ) && ( chats[ces]["status"] != 2 ) && !widget )
		$('#chat_body').attr( "scrollTop", $('#chat_body').attr( "scrollHeight" ) ) ;
}

function init_textarea()
{
	if ( typeof( chats[ces] ) != "undefined" )
	{
		if ( ( chats[ces]["status"] == 1 ) && !chats[ces]["disconnected"] )
			$('textarea#input_text').attr("disabled", false) ;
		else if ( chats[ces]["op2op"] && !chats[ces]["disconnected"] )
			$('textarea#input_text').attr("disabled", false) ;
		else if ( chats[ces]["initiated"] && !chats[ces]["disconnected"] )
			$('textarea#input_text').attr("disabled", false) ;
		else
			$('textarea#input_text').val( "" ).attr("disabled", true) ;
	}
	else
		$('textarea#input_text').val( "" ).attr("disabled", true) ;
}

function init_divs( theresize )
{
	var chat_body_padding = $('#chat_body').css('padding-left') ;
	var chat_body_padding_diff = ( typeof( chat_body_padding ) != "undefined" ) ? 20 - ( chat_body_padding.replace( /px/, "" ) * 2 ) : 0 ;

	var browser_height = $(window).height() ; var browser_width = $(window).width() ;
	var body_height = ( ( typeof( isop ) != "undefined" ) && isop ) ? browser_height - $('#chat_footer').height() - 152 : browser_height - $('#chat_footer').height() - 132 ;
	var body_width = ( ( typeof( isop ) != "undefined" ) && isop ) ? browser_width - 450 : browser_width - 42 ;
	var chat_body_width = body_width + chat_body_padding_diff ;
	var chat_body_height = body_height + chat_body_padding_diff - $('#chat_options').outerHeight() ;
	var input_text_width = ( ( typeof( isop ) != "undefined" ) && isop ) ? body_width + 17 : body_width - 100 ;
	var intro_top = ( ( typeof( isop ) != "undefined" ) && isop ) ? 30 : 12 ; var intro_left = ( ( typeof( isop ) != "undefined" ) && isop ) ? body_width + 40 : input_text_width + 30 ;
	var chat_btn_top, chat_text_powered_top, intro_width, intro_height ;
	var chat_btn_left = intro_left ;

	if ( widget )
		return true ;
	else if ( ( typeof( isop ) != "undefined" ) && isop )
	{
		extra_top = browser_height - $('#chat_footer').outerHeight() ; // css top val of footer
		intro_height = browser_height - 153 ; // tweak it depending on footer height
		chat_btn_top = intro_height + 40 ;
		chat_btn_left += 5 ;
		var chat_info_body_height = intro_height - ( $('#chat_info_header').height() + $('#chat_info_menu_list').height() ) - 24 ;
		var chat_panel_left = intro_left + $('#chat_btn').outerWidth() ;
		var chat_status_offline_left = chat_panel_left + $('#chat_cans').outerWidth() ;
		var chat_status_offline_top = intro_height - 55 ;
		var chat_data_height = intro_height ;
		var chat_extra_wrapper_height = browser_height - 90 ;

		$('#chat_body').css({'height': chat_body_height, 'width': chat_body_width}) ;
		$('#chat_data').css({'top': intro_top, 'left': intro_left, 'height': chat_data_height, 'width': 410}) ;
		$('#chat_info_body').css({'max-height': chat_info_body_height}) ;

		$('#chat_panel').css({'top': chat_btn_top, 'left': chat_panel_left}) ;
		$('#chat_status_offline').css({'top': chat_status_offline_top, 'left': chat_status_offline_left}) ;

		$('#chat_extra_wrapper').css({'height': chat_extra_wrapper_height}) ;
		$('#chat_extra_wrapper').hide() ;

		if ( theresize )
		{
			clearTimeout( st_resize ) ;
			st_resize = setTimeout( function(){ close_extra( extra ) ; }, 800 ) ;
		}
		else
			close_extra( extra ) ;
	}
	else
	{
		// only applies to op_trans_view.php
		if ( typeof( view ) != "undefined" )
		{
			if ( view == 1 )
				chat_body_height -= 90 ; // lift it up so more stats show
			else
				chat_body_height += 50 ;
		}
		else if ( widget )
			chat_body_height += 20 ;

		$('#chat_body').css({'height': chat_body_height, 'width': chat_body_width}) ;

		if ( mobile )
			chat_btn_top = browser_height - 85 ;
		else
			chat_btn_top = browser_height - 115 ;

		//chat_body_width -= 117 ;
		//$('#chat_body').css({'height': chat_body_height, 'width': chat_body_width}) ;
		//$('#profile_pic').css({'top': 50, 'left': chat_btn_left}) ;
	}

	chat_text_powered_top = chat_btn_top + $('#chat_btn').height() ;

	$('#input_text').css({'width': input_text_width}) ;
	$('#chat_btn').css({'top': chat_btn_top, 'left': chat_btn_left}) ;
	$('#chat_text_powered').css({'top': chat_text_powered_top, 'left': chat_btn_left}) ;
}

function update_ces( thejson_data )
{
	var thisces = thejson_data["ces"] ;
	var append_text = init_timestamps( thejson_data["text"] ) ;

	if ( ( typeof( chats[thisces] ) != "undefined" ) )
	{
		chats[thisces]["chatting"] = 1 ;
		chats[thisces]["trans"] += append_text ;

		// parse for flags before doing functions
		if ( ( append_text.indexOf("</top>") != -1 ) && !isop )
		{
			var regex_trans = /<top>(.*?)</ ;
			var regex_trans_match = regex_trans.exec( append_text ) ;
			
			chats[ces]["oname"] = regex_trans_match[1] ;
			$('#chat_vname').empty().html( regex_trans_match[1] ) ;

			var regex_opid = /<!--opid:(.*?)-->/ ;
			var regex_opid_match = regex_opid.exec( append_text ) ;
			isop_ = regex_opid_match[1] ;
		}

		if ( ( thejson_data["text"].indexOf( "<disconnected>" ) != -1 ) )
		{
			chats[thisces]["disconnected"] = unixtime() ;
			if ( isop ) { clearInterval( si_timer ) ; si_timer = this.undefined ; } else { $('#iframe_chat_engine').attr( "contentWindow" ).stopit(0) ; }
		}
		if ( ( thejson_data["text"].indexOf( "<restart_router>" ) != -1 ) && !isop )
		{
			chats[thisces]["status"] = 2 ;
			$('#iframe_chat_engine').attr( "contentWindow" ).routing() ;
		}

		if ( ces == thisces )
		{
			$('#chat_body').append( append_text ) ;
			init_scrolling() ;
			init_textarea() ;
			$('#chat_vistyping').empty().html( "" ) ;

			if ( $('#iframe_chat_engine').attr( "contentWindow" ).stopped )
				chat_survey() ;
		}
		else
			menu_blink( "green", thisces ) ;

		// starting from v4.2.105, activate for all chats
		if ( chats[thisces]["status"] )
		{
			if ( chat_sound )
				play_sound( "new_text", "new_text_"+sound_new_text ) ;
			title_blink_init() ;
		}
	}
}

function disconnect()
{
	if ( typeof( widget ) == "undefined" )
		widget = 0 ;

	if ( ( ( typeof( ces ) != "undefined" ) && ( typeof( chats[ces] ) != "undefined" ) ) || widget )
	{
		var json_data = new Object ;
		var unique = unixtime() ;
		var wname = encodeURIComponent( cname ) ;
		var rname ;

		if ( isop )
			rname = encodeURIComponent( chats[ces]["vname"].replace( /(%3Cv%3E)|(<v>)/i, "" ) ) ;
		else
			rname = encodeURIComponent( chats[ces]["oname"].replace( /(%3Cv%3E)|(<v>)/i, "" ) ) ;

		$.get(base_url+"/ajax/chat_actions.php", { action: "disconnect", isop: isop, isop_: isop_, isop__: isop__, ces: ces, ip: chats[ces]["ip"], wname: wname, rname: rname, widget: widget, unique: unique },  function(data){
			eval( data ) ;

			if ( json_data.status )
				cleanup_disconnect( json_data.ces ) ;
			else
				alert( "ERROR: 102" ) ;
		});
	}
}

function init_disconnect()
{
	$('#info_disconnect').hover(
		function () {
			$(this).removeClass('info_disconnect').addClass('info_disconnect_hover') ;
		}, 
		function () {
			$(this).removeClass('info_disconnect_hover').addClass('info_disconnect') ;
		}
	);
}

function init_timer()
{
	start_timer( chats[ces]["timer"] ) ;
	if ( typeof( chats[ces] ) != "undefined" )
	{
		if ( ( ( chats[ces]["status"] == 1 ) && !chats[ces]["disconnected"] ) || ( chats[ces]["initiated"] && !chats[ces]["disconnected"] ) )
		{
			if ( typeof( si_timer ) != "undefined" )
				clearInterval( si_timer ) ;
			
			si_timer = setInterval(function(){ if ( typeof( chats[ces] ) != "undefined" ) { start_timer( chats[ces]["timer"] ) ; } }, 1000) ;
		}
	}
}

function start_timer( thetimer )
{
	var diff ;
	if ( chats[ces]["disconnected"] )
		diff = chats[ces]["disconnected"] - thetimer ;
	else
		diff = unixtime() - thetimer ;

	var hours = Math.floor( diff/3600 ) ;
	var mins =  Math.floor( ( diff - ( hours * 3600 ) )/60 ) ;
	var secs = diff - ( hours * 3600 ) - ( mins * 60 ) ;

	var display = pad( hours, 2 )+":"+pad( mins, 2 )+":"+pad( secs, 2 ) ;

	if ( chats[ces]["status"] || chats[ces]["initiated"] )
		$('#chat_vtimer').empty().html( "["+display+"]" ) ;
	else
		$('#chat_vtimer').empty().html( "" ) ;
}

function init_marquees()
{
	start_marquees() ;
	setInterval( "start_marquees()", 10000 ) ;
}

function start_marquees()
{
	if ( typeof( marquees_messages[marquee_index] ) != "undefined" )
	{
		$('#chat_footer').empty().html( "<div class=\"marquee\">"+parse_marquee(marquees_messages[marquee_index])+"</div>" ) ;
		++marquee_index ;
		if ( marquee_index >= marquees.length )
			marquee_index = 0 ;
	}
}

function chat_survey()
{
	if ( !chats[ces]["survey"] && chats[ces]["vsurvey"] )
	{
		chats[ces]["survey"] = 1 ;
		add_text( survey ) ;
		add_text( "<div class='cc'><button style='padding: 5px;' onClick='window.close()'>"+lang_close+"</button></div>" ) ;
	}
	window.onbeforeunload = null ;

	if ( !widget )
		$('#info_disconnect').hide() ;
}

function submit_survey( theobject, thetexts )
{
	var json_data = new Object ;
	var unique = unixtime() ;

	if ( chats[ces]["survey"] != 2 )
	{
		$.get(base_url+"/ajax/chat_actions.php", { action: "rating", requestid: chats[ces]["requestid"], ces: ces, opid: chats[ces]["opid"], deptid: chats[ces]["deptid"], rating: theobject.value, unique: unique },  function(data){
			eval( data ) ;

			if ( json_data.status )
			{
				chats[ces]["survey"] = 2 ;
				do_alert( 1, thetexts[0] ) ;

				$("input[name='rating']").each(function(i) {
					$(this).attr('disabled', true) ;
				});
			}
		});
	}
}

function do_print( theces, thedeptid, theopid, thewidth, theheight )
{
	var winname = "Print"+theces ;
	var deptid = ( typeof( chats[theces]["deptid"] ) != "undefined" ) ? chats[theces]["deptid"] : thedeptid ;
	var opid = ( typeof( chats[theces]["opid"] ) != "undefined" ) ? chats[theces]["opid"] : theopid ;

	var url = base_url_full+"/ops/op_print.php?ces="+theces+"&deptid="+deptid+"&opid="+theopid+"&"+unixtime() ;

	if ( !wp )
		newwin_print = window.open( url, winname, "scrollbars=yes,menubar=no,resizable=1,location=no,width="+thewidth+",height="+theheight+",status=0" ) ;
	else
	{
		if ( typeof( isop ) != "undefined" )
			wp_new_win( url, winname, thewidth, theheight ) ;
		else
			location.href = url ;
	}
}

function init_timestamps( thetranscript )
{
	var lines = thetranscript.split( "<>" ) ;

	var transcript = "" ;
	for ( c = 0; c < lines.length; ++c )
	{
		var line = lines[c] ;
		var matches = line.match( /timestamp_(\d+)_/ ) ;
		
		var timestamp = "" ;
		if ( matches != null )
		{
			var time = extract_time( matches[1] ) ;
			timestamp = " (<span class='ct'>"+time+"</span>) " ;
			transcript += ( !widget ) ? line.replace( /<timestamp_(\d+)_((co)|(cv))>/, timestamp ) : line.replace( /<timestamp_(\d+)_((co)|(cv))>/, '' ) ;
		}
		else
			transcript += line ;
	}
	return transcript ;
}

function extract_time( theunixtime )
{
	var time_expanded = new Date( parseInt( theunixtime ) * 1000) ;
	var hours = time_expanded.getHours() ;
	if( hours >= 13 ) hours -= 12 ;
	var output = pad(hours,2)+":"+pad(time_expanded.getMinutes(), 2)+":"+pad(time_expanded.getSeconds(), 2) ;
	return output ;
}

function input_focus()
{
	focused = 1 ;
}

function play_sound( thediv, thesound )
{
	var unique = unixtime() ;

	flashembed( "div_sounds_"+thediv, base_url+'/media/'+thesound+'.swf' ) ;
}

function clear_sound( thediv )
{
	$('#div_sounds_'+thediv).empty().html( "" ) ;
}

function title_blink_init()
{
	if ( ( typeof( title_orig ) != "undefined" ) && !focused )
	{
		if ( typeof( si_title ) != "undefined" )
			clearInterval( si_title ) ;

		si_title = setInterval(function(){ title_blink( 1, title_orig, "Alert __________________ " ) ; }, 800) ;
	}
}

function title_blink( theflag, theorig, thenew )
{
	if( !focused && ( thenew != "reset" ) )
	{
		if ( ( si_counter % 2 ) && theflag )
			document.title = thenew ;
		else
			document.title = theorig ;

		++si_counter ;
	}
	else
	{
		if ( typeof( si_title ) != "undefined" )
		{
			clearInterval( si_title ) ; si_title = this.undefined ;
			document.title = theorig ;
		}
	}
}

function toggle_chat_sound( thetheme )
{
	if ( chat_sound )
	{
		chat_sound = 0 ;
		if ( isop && !wp )
			do_alert( 1, "For new chat requests, the operator console will now blink." ) ;
	}
	else
		chat_sound = 1 ;

	print_chat_sound_image( thetheme ) ;
}

function print_chat_sound_image( thetheme )
{
	if ( chat_sound )
		$('#chat_sound').attr('src', base_url+'/themes/'+thetheme+'/sound_on.png') ;
	else
		$('#chat_sound').attr('src', base_url+'/themes/'+thetheme+'/sound_off.png') ;
}
