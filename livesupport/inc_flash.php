<div id="flash_detect" style="display: none; position: absolute; top: 0px; left: 0px; width: 100%; height: 2000px; background: url( ./pics/bg_trans_white.png ) repeat; overflow: hidden; z-index: 1000;">
	<div style="position: relative; width: 350px; margin: 0 auto; top: 115px; text-align: center;">
		<div class="info_neutral" style="padding: 20px; border: 1px solid #BB0210;">
			<h3><span style="font-size: 14px; font-weight: bold;"><?php echo $LANG["FLASH_NOTFOUND"] ?></span></h3>

			<div style="margin-top: 5px; font-size: 12px;"><?php echo $LANG["FLASH_WARNING"] ?></div>
			<div style="margin-top: 5px;"><img src="pics/icons/flash.png" width="25" height="23" border="0" alt=""> <a href="http://get.adobe.com/flashplayer/" target="_blank"><?php echo $LANG["FLASH_DOWNLOAD"] ?></a></div>

			<div style="margin-top: 15px;"><button onClick="$('#flash_detect').hide()"><?php echo $LANG["FLASH_CONTINUE_CHAT"] ?></button></div>

			<div id="flash_result" style="display: none;"></div>
		</div>
	</div>
</div>