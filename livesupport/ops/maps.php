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

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Hash.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/IPs/Util.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/GeoIP/get.php" ) ;

	$ip = Util_Format_Sanatize( Util_Format_GetVar( "ip" ), "ln" ) ;

	$countries = Util_Hash_Countries() ;
	LIST( $ip_num, $network ) = UtilIPs_IP2Long( $ip ) ;
	$geoinfo = GeoIP_get_GeoInfo( $dbh, $ip_num, $network ) ;

	$geo_country = ( isset( $geoinfo["country"] ) && $geoinfo["country"] ) ? $countries[$geoinfo["country"]] : "Location Unknown" ;
	$geo_region = ( isset( $geoinfo["region"] ) && $geoinfo["region"] ) ? $geoinfo["region"] : "-" ;
	$geo_city = ( isset( $geoinfo["city"] ) && $geoinfo["city"] ) ? $geoinfo["city"] : "-" ;
	$geo_lat = ( isset( $geoinfo["latitude"] ) && $geoinfo["latitude"] ) ? $geoinfo["latitude"] : 28.613459424004414 ;
	$geo_long = ( isset( $geoinfo["longitude"] ) && $geoinfo["longitude"] ) ? $geoinfo["longitude"] : -40.4296875 ;
?>
<!DOCTYPE html>
<?php include_once( "$CONF[DOCUMENT_ROOT]/inc_doctype.php" ) ?>
<head>
<title> PHP Live! Support <?php echo $VERSION ?> </title>

<meta name="description" content="PHP Live! Support <?php echo $VERSION ?>">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<link rel="Stylesheet" href="../themes/<?php echo $opinfo["theme"] ?>/style.css?<?php echo $VERSION ?>" id="stylesheet">
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/tooltip.js?<?php echo $VERSION ?>"></script>
<?php if ( $geoip && $geomap ): ?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $geomap ?>&sensor=false" type="text/javascript"></script>
<?php endif; ?>
<script type="text/javascript">
<!--
	$(document).ready(function()
	{
		<?php if ( $geoip && $geomap ): ?>
		draw_map() ;
		<?php else: ?>
		$('#map_default').show() ;
		<?php endif ; ?>

	});

	function draw_map()
	{
		var latlng = new google.maps.LatLng( <?php echo $geo_lat ?>, <?php echo $geo_long ?> ) ;
		var myOptions = {
			zoom: 3,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		} ;

		var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions) ;

		var marker = new google.maps.Marker({
			position: latlng,
			title: "Country: <?php echo $geo_country ?>, Region: <?php echo $geo_region ?>, City: <?php echo $geo_city ?>"
		}) ;
		marker.setMap(map) ;
		
		adjust_height() ;
	}

	function adjust_height()
	{
		var canvas_height = $(window).height() ;
		<?php if ( $geoip && $geomap ): ?>$('#map_canvas').css({'height': canvas_height}).show() ;<?php endif ; ?>
	}
//-->
</script>
</head>
<body id="chat_info_body" style="margin: 0px; border: 0px; padding: 0px;">
	<div id="map_canvas" style="display: none; height: 100%;"></div>
	<div id="map_default" style="display: none; height: 100%;">
		<div class="chat_info_td_h" style="padding: 4px;"><b>Google Maps Is Not Enabled</b></div>
		<div class="chat_info_td" style="text-align: justify;">
			<table cellspacing=0 cellpadding=0 border=0>
			<tr>
				<td valign="top">Google Maps integration will display the approximate location of the visitor's IP address.  To enable Google Maps, login to the Setup area and access the "Extras" menu and click on the "GeoIP and GeoMap" sub menu to enable this feature.</td>
				<td valign="top"><div style="padding-left: 25px;"><img src="../pics/icons/google_maps.png" width="173" height="80" border="0" alt=""></div></td>
			</tr>
			</table>
		</div>
	</div>
</body>
</html>