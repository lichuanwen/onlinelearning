<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/* Dev team: 615 */
	include_once( "../web/config.php" ) ;
	include_once( "../API/Util_Format.php" ) ;
	include_once( "../API/Util_IP.php" ) ;
	include_once( "../API/Util_Upload.php" ) ;

	$query = Util_Format_Sanatize( Util_Format_GetVar( "q" ), "" ) ;
	if ( !$query ) { $query = Util_Format_Sanatize( Util_Format_GetVar( "v" ), "" ) ; }

	$params = Array() ;
	$tok = strtok( $query, '|' ) ;
	while ( $tok !== false ) { $params[] = $tok ; $tok = strtok( '|' ) ; }

	// proto flags
	// 0 (toggle), 1 (http), 2 (https)

	$deptid = isset( $params[0] ) ? Util_Format_Sanatize( $params[0], "n" ) : 0 ;
	$btn = isset( $params[1] ) ? Util_Format_Sanatize( $params[1], "n" ) : 0 ;
	$proto = isset( $params[2] ) ? Util_Format_Sanatize( rawurldecode( $params[2] ), "n" ) : 0 ;
	$text = isset( $params[3] ) ? Util_Format_Sanatize( rawurldecode( $params[3] ), "ln" ) : "" ;

	$base_url = $CONF["BASE_URL"] ;
	$upload_dir = "$CONF[DOCUMENT_ROOT]/web" ;
	if ( $proto == 1 ) { $base_url = preg_replace( "/(http:)|(https:)/", "http:", $base_url ) ; }
	else if ( $proto == 2 ) { $base_url = preg_replace( "/(http:)|(https:)/", "https:", $base_url ) ; }
	else { $base_url = preg_replace( "/(http:)|(https:)/", "", $base_url ) ; }

	$ip = Util_IP_GetIP() ;
	if ( !isset( $CONF["icon_check"] ) ) { $CONF["icon_check"] = "off" ; }

	$initiate = ( isset( $CONF["auto_initiate"] ) && $CONF["auto_initiate"] ) ? unserialize( html_entity_decode( $CONF["auto_initiate"] ) ) : Array() ;
	$widget_max = 4 ;
	$widget_slider = ( isset( $initiate["pos"] ) ) ? $initiate["pos"] : 1 ;
	// 1 left, 2 right, 3 bottom left, 4 bottom right, 5 bottom center, x center
	if ( $widget_slider == 1 )
	{
		$widget_animate_show = "left: '50px'" ;
		$widget_animate_hide = "left: -800" ;
		$widget_top_left = "top: 190px; left: 50px;" ;
		$widget_cover_top_left = "top: 190px; left: -800px;" ;
	}
	else if ( $widget_slider == 2 )
	{
		$widget_animate_show = "right: '50px'" ;
		$widget_animate_hide = "right: -800" ;
		$widget_top_left = "top: 190px; right: 50px;" ;
		$widget_cover_top_left = "top: 190px; right: -800px;" ;
	}
	else if ( $widget_slider == 3 )
	{
		$widget_animate_show = "bottom: '50px'" ;
		$widget_animate_hide = "bottom: -800" ;
		$widget_top_left = "bottom: 50px; left: 50px;" ;
		$widget_cover_top_left = "bottom: -800px; left: 50px;" ;
	}
	else if ( $widget_slider == 4 )
	{
		$widget_animate_show = "bottom: '50px'" ;
		$widget_animate_hide = "bottom: -800" ;
		$widget_top_left = "bottom: 50px; right: 50px;" ;
		$widget_cover_top_left = "bottom: -800px; right: 50px;" ;
	}
	else { $widget_animate_show = $widget_animate_hide = $widget_top_left = $widget_cover_top_left = "" ; }

	$offline = ( isset( $VALS['OFFLINE'] ) && $VALS['OFFLINE'] ) ? unserialize( $VALS['OFFLINE'] ) : Array() ;
	if ( !isset( $offline[$deptid] ) && isset( $offline[0] ) ) { $offline[$deptid] = $offline[0] ; }

	$redirect_url = ( isset( $offline[$deptid] ) && !preg_match( "/^(icon|hide|embed)$/", $offline[$deptid] ) ) ? $offline[$deptid] : "" ;
	$icon_hide = ( isset( $offline[$deptid] ) && preg_match( "/^(hide)$/", $offline[$deptid] ) ) ? 1 : 0 ;
	$embed_offline = ( isset( $offline[$deptid] ) && preg_match( "/^(embed)$/", $offline[$deptid] ) ) ? 1 : 0 ;

	if ( !isset( $CONF["vsize"] ) ) { $width = $VARS_CHAT_WIDTH ; $height = $VARS_CHAT_HEIGHT ; }
	else { LIST( $width, $height ) = explode( "x", $CONF["vsize"] ) ; }
	Header( "Content-Type: application/javascript" ) ;
?>
if ( typeof( phplive_init_jquery ) == "undefined" )
{
	var phplive_jquery ;
	var phplive_stat_refer = encodeURIComponent( document.referrer.replace("http", "hphp") ) ;
	var phplive_stat_onpage = encodeURIComponent( location.toString().replace("http", "hphp") ) ;
	var phplive_stat_title = encodeURIComponent( document.title ) ;
	var phplive_win_width = screen.width ;
	var phplive_win_height = screen.height ;
	var resolution = escape( phplive_win_width + " x " + phplive_win_height ) ;
	var proto = location.protocol ; // to avoid JS proto error, use page proto for areas needing to access the JS objects

	var phplive_center = function(a){var b=phplive_jquery(window),c=b.scrollTop();return this.each(function(){var f=phplive_jquery(this),e=phplive_jquery.extend({against:"window",top:false,topPercentage:0.5},a),d=function(){var h,g,i;if(e.against==="window"){h=b;}else{if(e.against==="parent"){h=f.parent();c=0;}else{h=f.parents(against);c=0;}}g=((h.width())-(f.outerWidth()))*0.5;i=((h.height())-(f.outerHeight()))*e.topPercentage+c;if(e.top){i=e.top+c;}f.css({left:g,top:i});};d();b.resize(d);});} ;

	var phplive_quirks = 0 ;
	var phplive_IE ;
	//@cc_on phplive_IE = navigator.appVersion ;

	var mode = document.compatMode,m ;
	if ( ( mode == 'BackCompat' ) && phplive_IE )
		phplive_quirks = 1 ;

	window.phplive_init_jquery = function()
	{
		if ( typeof( phplive_jquery ) == "undefined" )
		{
			if ( typeof( window.jQuery ) == "undefined" )
			{
				var script_jquery = document.createElement('script') ;
				script_jquery.type = "text/javascript" ; script_jquery.async = true ;
				script_jquery.onload = script_jquery.onreadystatechange = function () {
					if ( ( typeof( this.readyState ) == "undefined" ) || ( this.readyState == "loaded" || this.readyState == "complete" ) )
					{
						phplive_jquery = window.jQuery.noConflict() ;
						phplive_jquery.fn.center = phplive_center ;
					}
				} ;
				script_jquery.src = "<?php echo $base_url ?>/js/framework.js?<?php echo $VERSION ?>" ;
				var script_jquery_s = document.getElementsByTagName('script')[0] ;
				script_jquery_s.parentNode.insertBefore(script_jquery, script_jquery_s) ;
			}
			else
			{
				phplive_jquery = window.jQuery ;
				phplive_jquery.fn.center = phplive_center ;
			}
		}
	}
	window.phplive_unique = function() { var date = new Date() ; return date.getTime() ; }
	phplive_init_jquery() ;
}

if ( typeof( phplive_initiate_widget ) == "undefined" )
{
	var obj_div, obj_div_cover, obj_iframe ;
	
	// one default invite per page to avoid multiple auto/manual invites
	var phplive_initiate_widget = 0 ;
	var this_position = ( phplive_quirks ) ? "absolute" : "fixed" ;

	var phplive_widget_width = ( phplive_quirks ) ? 270 : 250 ;
	var phplive_widget_height = ( phplive_quirks ) ? 180 : 160 ;
	var phplive_widget_cover_image = '<?php echo Util_Upload_GetInitiate( $base_url, 0 ) ; ?>' ;
	var phplive_widget_cover_image_op = '<?php echo $base_url ?>/themes/initiate/initiate_op_cover.png' ;

	// NOTE: do not modify the width and height of the widget window or it may not function as intended
	var phplive_widget = "<map name='initiate_chat_cover'><area shape='rect' coords='222,2,247,26' href='JavaScript:void(0)' onClick='phplive_widget_decline()'><area shape='rect' coords='0,26,250,160' href='JavaScript:void(0)' onClick='phplive_widget_launch()'></map><div id='phplive_widget' style='display: none; position: "+this_position+"; <?php echo $widget_top_left ?> background: url( <?php echo $base_url ?>/themes/initiate/bg_trans.png ) repeat; padding: 10px; width: "+phplive_widget_width+"px; height: "+phplive_widget_height+"px; -moz-border-radius: 5px; border-radius: 5px; z-Index: 10000;'><iframe id='iframe_widget' name='iframe_widget' style='display: none; width: 100%; height: 100%;' src='<?php echo $base_url ?>/blank.php' scrolling='no' border=0 frameborder=0 onload='phplive_widget_onload()'></iframe></div><div id='phplive_widget_cover' style='display: none; position: "+this_position+"; <?php echo $widget_cover_top_left ?> padding: 10px; z-Index: 10001;'><img src='"+phplive_widget_cover_image+"' id='phplive_widget_cover_img' width='250' height='160' border=0 usemap='#initiate_chat_cover' style='-moz-border-radius: 5px; border-radius: 5px;'></div>" ;

	window.phplive_widget_onload = function()
	{
		// future options
	}
	window.phplive_widget_launch = function()
	{
		if ( phplive_initiate_widget )
		{
			obj_div.fadeOut( "fast" ) ;
			obj_div_cover.hide() ;
			obj_iframe.attr( 'src', "<?php echo $base_url ?>/blank.php" ) ;

			phplive_launch_chat_<?php echo $deptid ?>(1) ;
		}
	}
	window.phplive_widget_close = function()
	{
		obj_div.fadeOut( "fast" ) ;
		obj_div_cover.fadeOut("fast", function() { phplive_jquery( '#phplive_widget_cover_img').attr( 'src', phplive_widget_cover_image ) ; }) ;
		obj_iframe.attr( 'src', "<?php echo $base_url ?>/blank.php" ) ;
	}
	window.phplive_widget_decline = function()
	{
		if ( phplive_initiate_widget )
		{
			var phplive_pullimg_widget = new Image ;
			phplive_pullimg_widget.onload = function() {
				if ( <?php echo $widget_slider ?> > <?php echo $widget_max ?> ) { obj_div_cover.fadeOut("fast") ; }
				else { obj_div_cover.animate({ <?php echo $widget_animate_hide ?> }, 2000) ; }
			};
			phplive_pullimg_widget.src = "<?php echo $base_url ?>/ajax/chat_actions.php?action=disconnect&isop=0&widget=1&ip=<?php echo $ip ?>&"+phplive_unique() ;
			phplive_widget_close() ;
			phplive_initiate_widget = 0 ;
		}
	}
}

if ( typeof( phplive_thec_<?php echo $deptid ?> ) == "undefined" )
{
	var phplive_thec_<?php echo $deptid ?> = 0 ;
	var phplive_fetch_status_image_<?php echo $deptid ?> ;
	var phplive_fetch_footprint_image_<?php echo $deptid ?> ;

	var phplive_fetch_initiate_image_<?php echo $deptid ?> ;

	var phplive_interval_fetch_status_<?php echo $deptid ?> ;
	var phplive_interval_footprint_<?php echo $deptid ?> ;
	var phplive_interval_initiate_<?php echo $deptid ?> ;

	var phplive_fetch_status_url_<?php echo $deptid ?> = "<?php echo $base_url ?>/ajax/status.php?action=js&deptid=<?php echo $deptid ?>&r="+phplive_stat_refer+"&p="+phplive_stat_onpage+"&title="+phplive_stat_title+"&resolution="+resolution+"&jkey=<?php echo md5( $CONF["API_KEY"] ) ?>" ;
	var phplive_request_url_<?php echo $deptid ?> = "<?php echo $base_url ?>/phplive.php?d=<?php echo $deptid ?>&onpage="+phplive_stat_onpage+"&title="+phplive_stat_title ;

	var phplive_offline_redirect_<?php echo $deptid ?> = 0 ;
	var phplive_online_offline_<?php echo $deptid ?> ;
	var phplive_online_offline_prev_<?php echo $deptid ?> ;

	var phplive_image_online_<?php echo $deptid ?> = "<?php echo Util_Upload_GetChatIcon( $base_url, "icon_online", $deptid ) ?>" ;
	var phplive_image_offline_<?php echo $deptid ?> = "<?php echo Util_Upload_GetChatIcon( $base_url, "icon_offline", $deptid ) ?>" ;

	var phplive_widget_offline_div_visible_<?php echo $deptid ?> = 0 ;

	window.phplive_get_thec_<?php echo $deptid ?> = function() { return phplive_thec_<?php echo $deptid ?> ; }
	window.phplive_fetch_status_<?php echo $deptid ?> = function()
	{
		phplive_fetch_status_image_<?php echo $deptid ?> = new Image ;
		phplive_fetch_status_image_<?php echo $deptid ?>.onload = phplive_fetch_status_actions_<?php echo $deptid ?> ;
		phplive_fetch_status_image_<?php echo $deptid ?>.src = phplive_fetch_status_url_<?php echo $deptid ?>+"&"+phplive_unique() ;
	}
	window.phplive_fetch_status_actions_<?php echo $deptid ?> = function()
	{
		var thisflag = phplive_fetch_status_image_<?php echo $deptid ?>.width ;

		if ( thisflag == 1 )
		{
			phplive_online_offline_<?php echo $deptid ?> = 1 ;
		}
		else
		{
			phplive_online_offline_<?php echo $deptid ?> = 0 ;
		}
		++phplive_thec_<?php echo $deptid ?> ;
		if ( typeof( phplive_interval_fetch_status_<?php echo $deptid ?> ) != "undefined" )
			clearInterval( phplive_interval_fetch_status_<?php echo $deptid ?> ) ;
		
		<?php if ( !$text && ( $CONF["icon_check"] != "off" ) ): ?>
		phplive_interval_fetch_status_<?php echo $deptid ?> = setInterval(function(){ phplive_fetch_status_<?php echo $deptid ?>() ; }, <?php echo $VARS_JS_ICON_CHECK ?> * 1000) ;
		<?php endif ; ?>
	}
	window.phplive_initiate_track_<?php echo $deptid ?> = function()
	{
		phplive_fetch_initiate_image_<?php echo $deptid ?> = new Image ;
		phplive_fetch_initiate_image_<?php echo $deptid ?>.onload = phplive_fetch_initiate_actions_<?php echo $deptid ?> ;
		phplive_fetch_initiate_image_<?php echo $deptid ?>.src = "<?php echo $base_url ?>/ajax/status_initiate.php?"+phplive_unique() ;
	}
	window.phplive_fetch_initiate_actions_<?php echo $deptid ?> = function()
	{
		var thisflag = phplive_fetch_initiate_image_<?php echo $deptid ?>.width ;

		// 1=status ok, 2=auto invite ok, 3=op invite ok
		if ( ( thisflag == 1 ) || ( thisflag == 2 ) || ( thisflag == 3 ) )
		{
			if ( ( ( thisflag == 2 ) || ( thisflag == 3 ) ) && !phplive_initiate_widget )
			{
				phplive_jquery( "body" ).append( phplive_widget ) ;

				phplive_initiate_widget = 1 ;
				obj_div = phplive_jquery( "#phplive_widget" ) ;
				obj_div_cover = phplive_jquery( "#phplive_widget_cover" ) ;
				obj_iframe = phplive_jquery( "#iframe_widget" ) ;

				obj_iframe.attr( 'src', "<?php echo $base_url ?>/widget.php?btn=<?php echo $deptid ?>&height="+phplive_widget_height +"&"+phplive_unique() ) ;

				if ( thisflag == 2 ) { obj_iframe.hide() ; }
				else
				{
					obj_iframe.show() ;
					phplive_jquery( '#phplive_widget_cover_img').attr( 'src', phplive_widget_cover_image_op ) ;
				}
				if ( <?php echo $widget_slider ?> > <?php echo $widget_max ?> )
				{
					obj_div_cover.center().show() ;
					obj_div.center().fadeIn("fast") ;
				}
				else
				{
					if ( thisflag == 2 ){ obj_div_cover.show().animate({ <?php echo $widget_animate_show ?> }, 2000, function() { obj_div.fadeIn("fast") ; }) ; }
					else { obj_div_cover.animate({ <?php echo $widget_animate_show ?> }, 2000, function() { obj_div.fadeIn("fast") ; obj_div_cover.show() }) ; }
				}
			}
			else if ( ( thisflag == 1 ) && phplive_initiate_widget )
			{
				phplive_widget_close() ;
				phplive_initiate_widget = 0 ;
			}

			if ( typeof( phplive_interval_initiate_<?php echo $deptid ?> ) != "undefined" )
				clearInterval( phplive_interval_initiate_<?php echo $deptid ?> ) ;
			phplive_interval_initiate_<?php echo $deptid ?> = setTimeout(function(){ phplive_initiate_track_<?php echo $deptid ?>() }, <?php echo $VARS_JS_INITIATE_CHECK ?> * 1000) ;
		}
	}
	window.phplive_footprint_track_<?php echo $deptid ?> = function()
	{
		phplive_fetch_footprint_image_<?php echo $deptid ?> = new Image ;
		phplive_fetch_footprint_image_<?php echo $deptid ?>.onload = phplive_fetch_footprint_actions_<?php echo $deptid ?> ;
		phplive_fetch_footprint_image_<?php echo $deptid ?>.src = "<?php echo $base_url ?>/ajax/footprints.php?deptid=<?php echo $deptid ?>&r="+phplive_stat_refer+"&onpage="+phplive_stat_onpage+"&title="+phplive_stat_title+"&c="+phplive_get_thec_<?php echo $deptid ?>()+"&resolution="+resolution+"&"+phplive_unique() ;
	}
	window.phplive_fetch_footprint_actions_<?php echo $deptid ?> = function()
	{
		var thisflag = phplive_fetch_footprint_image_<?php echo $deptid ?>.width ;

		if ( thisflag == 1 )
		{
			if ( typeof( phplive_interval_footprint_<?php echo $deptid ?> ) != "undefined" )
				clearInterval( phplive_interval_footprint_<?php echo $deptid ?> ) ;
			phplive_interval_footprint_<?php echo $deptid ?> = setTimeout(function(){ phplive_footprint_track_<?php echo $deptid ?>() }, <?php echo $VARS_JS_ICON_CHECK ?> * 1000) ;
		}
		else if ( thisflag == 4 )
		{
			if ( typeof( phplive_interval_footprint_<?php echo $deptid ?> ) != "undefined" )
			{
				clearInterval( phplive_interval_footprint_<?php echo $deptid ?> ) ;
				clearInterval( phplive_interval_initiate_<?php echo $deptid ?> ) ;
			}
		}
	}
	window.phplive_launch_chat_<?php echo $deptid ?> = function( thewidget, thetheme )
	{
		var winname = phplive_unique() ;
		var name = email = ""
		var custom_vars = "&custom=" ;
		if ( typeof( thetheme ) == "undefined" ){ thetheme = "" ; }
		
		if ( typeof( phplive_v ) != "undefined" )
		{
			for ( var key in phplive_v )
			{
				if ( key == "name" ) { name = encodeURIComponent( phplive_v["name"] ) ; }
				else if ( key == "email" ) { email = encodeURIComponent( phplive_v["email"] ) ; }
				else { custom_vars += encodeURIComponent( key )+"-_-"+encodeURIComponent( phplive_v[key] )+"-cus-" ; }
			}
		}

		if ( ( "<?php echo $redirect_url ?>" != "" ) && !thewidget && !phplive_online_offline_<?php echo $deptid ?> ) { location.href = "<?php echo $redirect_url ?>" ; }
		else
		{
			var phplive_request_url_new_<?php echo $deptid ?> = phplive_request_url_<?php echo $deptid ?>+"&theme="+thetheme+"&js_name="+name+"&js_email="+email+"&widget="+thewidget+custom_vars ;

			if ( phplive_online_offline_<?php echo $deptid ?> || !<?php echo $embed_offline ?> )
			{
				window.open( phplive_request_url_new_<?php echo $deptid ?>, winname, 'scrollbars=no,resizable=yes,menubar=no,location=no,screenX=50,screenY=100,width=<?php echo $width ?>,height=<?php echo $height ?>' ) ;
			}
			else
			{
				var phplive_widget_offline_div_<?php echo $deptid ?> = "<div id='phplive_widget_offline_<?php echo $deptid ?>' style='display: none; position: absolute; top: 0px; left: 0px; background: url( <?php echo $base_url ?>/themes/initiate/bg_trans_white.png ) repeat; z-Index: 10001;'></div><div id='phplive_widget_offline_iframe_<?php echo $deptid ?>' style='display: none; position: fixed; width: <?php echo $width ?>px; height: <?php echo $height ?>px; z-Index: 10002;'><iframe id='iframe_widget_offline_<?php echo $deptid ?>' name='iframe_widget_offline_<?php echo $deptid ?>' style='position: absolute; width: 100%; height: 100%; border: 1px solid #DFDFDF; -moz-border-radius: 5px; border-radius: 5px;' src='<?php echo $base_url ?>/blank.php' scrolling='no' border=0 frameborder=0></iframe><div style='position: relative; top: 0px; left: 0px; text-align: right;'><img src='<?php echo $base_url ?>/pics/space.gif' width='145' height='30' border=0 style='cursor: pointer;' onClick='phplive_offline_close_<?php echo $deptid ?>()'></div></div>" ;
				if ( !phplive_widget_offline_div_visible_<?php echo $deptid ?> )
				{
					phplive_jquery( "body" ).append( phplive_widget_offline_div_<?php echo $deptid ?> ) ;
					phplive_widget_offline_div_visible_<?php echo $deptid ?> = 1 ;
				}

				var top = Math.max(phplive_jquery(window).height() / 2 - <?php echo $width ?> / 2, 0) ;
				var left = Math.max(phplive_jquery(window).width() / 2 - <?php echo $height ?> / 2, 0) ;
				var view_width = ( phplive_jquery(window).width() > phplive_jquery('body').width() ) ? phplive_jquery(window).width() : phplive_jquery('body').width() ;
				var view_height = ( phplive_jquery(window).height() > phplive_jquery('body').height() ) ? phplive_jquery(window).height() : phplive_jquery('body').height() ;

				phplive_jquery( "#phplive_widget_offline_<?php echo $deptid ?>" ).css({ 'width': view_width, 'height': view_height}).show() ;
				phplive_jquery( "#phplive_widget_offline_iframe_<?php echo $deptid ?>" ).css({'top': top, 'left': left}) ;
				phplive_jquery( "#iframe_widget_offline_<?php echo $deptid ?>" ).attr( 'src', phplive_request_url_new_<?php echo $deptid ?>+"&embed=1&"+phplive_unique() ).ready(function (){
					phplive_jquery( "#phplive_widget_offline_iframe_<?php echo $deptid ?>" ).show() ;
				}); ;
			}
		}
	}
	window.phplive_offline_close_<?php echo $deptid ?> = function()
	{
		phplive_jquery( "#phplive_widget_offline_iframe_<?php echo $deptid ?>" ).hide() ;
		phplive_jquery( "#phplive_widget_offline_<?php echo $deptid ?>" ).hide() ;
		phplive_jquery( "#iframe_widget_offline_<?php echo $deptid ?>" ).attr( 'src', '<?php echo $base_url ?>/blank.php' ) ;
	}
	phplive_fetch_status_<?php echo $deptid ?>() ;
	phplive_footprint_track_<?php echo $deptid ?>() ;
	phplive_initiate_track_<?php echo $deptid ?>() ;
}
if ( typeof( phplive_btn_loaded_<?php echo $btn ?> ) == "undefined" )
{
	var phplive_btn_loaded_<?php echo $btn ?> = 1 ;
	var phplive_interval_jquery_check_<?php echo $btn ?> ;
	var phplive_interval_status_check_<?php echo $btn ?> ;

	window.phplive_image_refresh_<?php echo $btn ?> = function()
	{
		if ( typeof( phplive_interval_status_check_<?php echo $btn ?> ) != "undefined" )
			clearInterval( phplive_interval_status_check_<?php echo $btn ?> ) ;

		var image_or_text ;
		if ( phplive_online_offline_<?php echo $deptid ?> )
			image_or_text = ( <?php echo ( $text ) ? 1 : 0 ; ?> ) ? "<?php echo $text ?>" : "<img src=\""+phplive_image_online_<?php echo $deptid ?>+"\" border=0>" ;
		else
		{
			if ( <?php echo $icon_hide ?> ) { image_or_text = "" ; }
			else { image_or_text = ( <?php echo ( $text ) ? 1 : 0 ; ?> ) ? "<?php echo $text ?>" : "<img src=\""+phplive_image_offline_<?php echo $deptid ?>+"\" border=0>" ; }
		}

		if ( phplive_online_offline_prev_<?php echo $deptid ?> != image_or_text )
		{
			document.getElementById("phplive_btn_<?php echo $btn ?>").innerHTML = image_or_text ;
			phplive_online_offline_prev_<?php echo $deptid ?> = image_or_text ;
		}
		phplive_interval_status_check_<?php echo $btn ?> = setInterval(function(){ phplive_image_refresh_<?php echo $btn ?>() ; }, 5000) ;
	}
	window.phplive_output_image_or_text_<?php echo $btn ?> = function()
	{
		if ( typeof( phplive_interval_jquery_check_<?php echo $btn ?> ) != "undefined" )
			clearInterval( phplive_interval_jquery_check_<?php echo $btn ?> ) ;

		if ( typeof( phplive_online_offline_<?php echo $deptid ?> ) == "undefined" )
		{
			phplive_interval_status_check_<?php echo $btn ?> = setInterval(function(){
				if ( typeof( phplive_online_offline_<?php echo $deptid ?> ) != "undefined" )
					phplive_image_refresh_<?php echo $btn ?>() ;
			}, 200) ;
		}
		else
			phplive_image_refresh_<?php echo $btn ?>() ;
	}
	window.phplive_process_<?php echo $btn ?> = function()
	{
		if ( phplive_quirks )
		{
			var phplive_btn = document.getElementById('phplive_btn_<?php echo $btn ?>') ;
			if ( ( typeof( phplive_btn.style.position ) != "undefined" ) && phplive_btn.style.position )
				phplive_btn.style.position = "absolute" ;
		}

		if ( typeof( phplive_jquery ) == "undefined" )
		{
			phplive_interval_jquery_check_<?php echo $btn ?> = setInterval(function(){
				if ( typeof( phplive_jquery ) != "undefined" )
					phplive_output_image_or_text_<?php echo $btn ?>() ;
			}, 200) ;
		}
		else
			phplive_output_image_or_text_<?php echo $btn ?>() ;
	}
	phplive_process_<?php echo $btn ?>() ;
}

