<div style="float:left; width:50%;">
<div id="vid_player-<?php echo $id_suffix;?>" style="height:320px" >
	<?php if (false){?>
 	   <ul class="vidControlsLayout" style="width:100%">
	        <li id="playback-display"><span class="current-<?php echo $id_suffix;?>">0:00:00</span></li>
	        <li id="start_img"><img src="<?php echo img('vid_white.png'); ?>" style="width:20px; height:20px;" title="Start/Stop" class="btnPlay"/></li>	            	
		</ul>
	<?php }?>
    <div id="jwplayer_plugin-<?php echo $id_suffix;?>">Player failed to load...  
	</div>
</div>
</div>

<script type='text/javascript'>

jwplayer("jwplayer_plugin-<?php echo $id_suffix;?>").setup({
playlist:  [
	<?php 
	$loops = 0;
	foreach ($items as $item){
		set_current_record('item',$item);?>
	<?php
	$startTime . $id_suffix[$loops] = metadata('item',array('Streaming Video','Segment Start'));
	$endTime . $id_suffix[$loops] = metadata('item',array('Streaming Video','Segment End'));?>
{
file: '<?php echo metadata("item",array ("Streaming Video","Video Streaming URL"));?><?php echo metadata("item",array("Streaming Video","Video Type"));?><?php echo metadata("item",array("Streaming Video","Video Filename"));?>',
title: "<?php echo metadata('item', array('Dublin Core','Title')); ?>",
start: "<?php echo $startTime . $id_suffix[$loops++];?>",
},
<?php };?>
],

primary: "flash",
autostart: true,
controls: false,
width: "100%",
height: "100%",
listbar: {
	position: "right",
	size: 100,
	layout: "basic",
},
}
);
jwplayer("jwplayer_plugin-<?php echo $id_suffix;?>").onReady(function(){
		jQuery('.current-<?php echo $id_suffix;?>').text(getFormattedTimeString(startTime<?php echo $id_suffix;?>));
		jQuery('.duration-<?php echo $id_suffix;?>').text(getFormattedTimeString(endTime<?php echo $id_suffix;?>));
		jwplayer("jwplayer_plugin-<?php echo $id_suffix;?>").seek(startTime<?php echo $id_suffix;?>);
		jwplayer("jwplayer_plugin-<?php echo $id_suffix;?>").pause(true);			
	});
		
jwplayer("jwplayer_plugin-<?php echo $id_suffix;?>").onTime(function(event){
	jQuery('.current').text(getFormattedTimeString(jwplayer("jwplayer_plugin-<?php echo $id_suffix;?>").getPosition()));	
	if (jwplayer("jwplayer_plugin-<?php echo $id_suffix;?>").getPosition() >=  endTime<?php echo $id_suffix;?> ||
		jwplayer("jwplayer_plugin-<?php echo $id_suffix;?>").getPosition() <  startTime<?php echo $id_suffix;?> ){
		jwplayer("jwplayer_plugin-<?php echo $id_suffix;?>").seek(startTime<?php echo $id_suffix;?>);
		jwplayer("jwplayer_plugin-<?php echo $id_suffix;?>").pause(true);		
	}			
	});
		
	jwplayer("jwplayer_plugin-<?php echo $id_suffix;?>").onPause(function(event){
		if (jwplayer("jwplayer_plugin-<?php echo $id_suffix;?>").getFullscreen()){
			jwplayer("jwplayer_plugin-<?php echo $id_suffix;?>").setControls(true);			
		} else {
			jwplayer("jwplayer_plugin-<?php echo $id_suffix;?>").setControls(false);
		};		
	});

	jwplayer("jwplayer_plugin-<?php echo $id_suffix;?>").onResize(function(event){
		jwplayer("jwplayer_plugin-<?php echo $id_suffix;?>").setControls(false);	
	});

	jwplayer("jwplayer_plugin-<?php echo $id_suffix;?>").onFullscreen(function(event){
		jwplayer("jwplayer_plugin-<?php echo $id_suffix;?>").setControls(true);			
		jwplayer("jwplayer_plugin-<?php echo $id_suffix;?>").play(true);
	});
	
	jQuery('.btnPlay').on('click', function() {
			   jwplayer("jwplayer_plugin-0").pause();
			   jwplayer("jwplayer_plugin-1").pause();
			   jwplayer("jwplayer_plugin-2").pause();
			   jwplayer("jwplayer_plugin-3").pause();
			   return true;
			});
				
	jQuery('#vid_player-<?php echo $id_suffix;?>')[0].onmousedown = (function() {
	    var moveend = function() {
	    }, thread;

	  return function() {
		jwplayer("jwplayer_plugin-<?php echo $id_suffix;?>").setControls(true);
	        clearTimeout(thread);
	        thread = setTimeout(moveend, 3000);
	   };
	})();
	
</script>
