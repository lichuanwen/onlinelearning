var phplive_wp ; if ( parent.location != window.location  ) { phplive_wp = 1 ; }
function init_menu()
{
	$( '*', 'body' ).each( function(){
		var div_name = $( this ).attr('id') ;
		var class_name = $( this ).attr('class') ;
		if ( class_name == "menu" )
		{
			$(this).hover(
				function () {
					$(this).removeClass('menu').addClass('menu_hover') ;
				}, 
				function () {
					$(this).removeClass('menu_hover').addClass('menu') ;
				}
			);
		}
	} );
}

function init_menu_op()
{
	$( '*', 'body' ).each( function(){
		var div_name = $( this ).attr('id') ;
		var class_name = $( this ).attr('class') ;
		if ( class_name == "menu" )
		{
			$(this).hover(
				function () {
					$(this).removeClass('menu').addClass('menu_hover') ;
				}, 
				function () {
					$(this).removeClass('menu_hover').addClass('menu') ;
				}
			);
		}
	} );
}

function toggle_menu_op( themenu )
{
	var divs = Array( "go", "cans", "transcripts", "reports", "themes", "sounds", "password", "language", "mobile", "dn" ) ;

	for ( c = 0; c < divs.length; ++c )
	{
		$('#menu_'+divs[c]).removeClass('menu_focus').addClass('menu') ;
		$('#op_'+divs[c]).hide() ;
	}

	if ( typeof( opwin ) != "undefined" )
		demo_sound1(0) ;

	menu = themenu ;
	$('#menu_'+themenu).removeClass('menu').addClass('menu_focus') ;
	$('#op_'+themenu).show() ;
}

function logout_op( theses )
{
	location.href = "../index.php?action=logout&ses="+theses ;
}

function toggle_menu_setup( themenu )
{
	// place actions that should always trigger in setup here
	if ( phplive_wp )
		$('#popout').show() ;

	var divs = Array( "home", "depts", "ops", "icons", "html", "trans", "rchats", "rtraffic", "marketing", "settings", "extras" ) ;

	for ( c = 0; c < divs.length; ++c )
		$('#menu_'+divs[c]).removeClass('menu_focus').addClass('menu') ;

	$('#menu_'+themenu).removeClass('menu').addClass('menu_focus') ;
	menu = themenu ;
}

function preview_theme( thetheme, thewidth, theheight, thedeptid )
{
	var unique = unixtime() ;

	window.open( "../phplive.php?ga=1&d="+thedeptid+"&theme="+thetheme+"&"+unique, "themed"+unique, 'scrollbars=no,resizable=yes,menubar=no,location=no,screenX=50,screenY=100,width='+thewidth+',height='+theheight ) ;
}
