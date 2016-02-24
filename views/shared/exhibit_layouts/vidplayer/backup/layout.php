 <?php
$float = isset($options['video-float'])
	? html_escape($options['video-float'])
	: 'left';
$width = isset($options['video-width'])
	? html_escape($options['video-width'])
	: '95%';
if ($width == '100%'){
	$width = '95%';
};
//$height='100%';
//$height=array(
//	"95%" => "42%",
//	"80%" => "36%",
//	"70%" => "3%",
//	"60%" => "24%",
//	"50%" => "20%",
//	"40%" => "15%",
//);
$external = isset($options['video-controls'])
	? html_escape($options['video-controls'])
	: false;
$current = isset($options['current-seg'])
	? html_escape($options['current-seg'])
	: false;
$eid_suffix = 0;
global $eid_suffix;
?>

<?php 
	$poster=array();
	$caption=array();
	foreach($attachments as $attItem):
	$item = $attItem->getItem();	
	set_current_record('Item',$item);
	if (metadata($item,'has files')){
				$files = $item->Files;
				    foreach($files as $file) {
				        if($file->hasThumbnail()) {
							$poster[]= file_display_url($file,'thumbnail');
							if (metadata($file,array('Dublin Core','Title'))){
								$caption[]= metadata($itemfile,array('Dublin Core','Title'));
								}else{
								$caption[]=html_escape(metadata('Item', array('Dublin Core', 'Title')));    
								} 		    
				        }
					}
				}
	endforeach;
	?>
	<div id="vid_player-<?php echo $eid_suffix;?>" style="width:<?php echo $width;?>;   float:<?php echo $float;?>;">
	
			<video id="video<?php echo $eid_suffix;?>" class="video-js vjs-default-skin" 
				  controls preload="auto" width=100% 
				ytcontrols playsInline
				<?php if (isset($poster[0])){?>
				poster="<?php echo $poster[0];?>"
				<?php }?>
				<?php if (metadata('item', array('Streaming Video','Segment Type')) == 'youtube'){?>
					data-setup='{ "inactivityTimeout": 2000, "techOrder": ["youtube"], "sources": [{ "type": "video/youtube", "src": "<?php echo metadata('item',array('Streaming Video','HTTP Streaming Directory'));?><?php echo metadata('item',array('Streaming Video','HTTP Video Filename'));?>"}], "Youtube": { "iv_load_policy": 1 }, "Youtube": { "ytControls": 2 } }'>
				<?php } else { ?>
			  		data-setup='{"example_option":true, "inactivityTimeout": 2000, "nativeControlsForTouch": false}'>
					<?php if (metadata('item',array('Streaming Video','Video Streaming URL'))){?>
						<source src="<?php echo metadata('item',array('Streaming Video','Video Streaming URL'));?><?php echo metadata('item',array('Streaming Video','Video Type'));?><?php echo metadata('item',array('Streaming Video','Video Filename'));?>" type='rtmp/mp4'/>
					<?php } ?>
					<?php if (metadata('item',array('Streaming Video','HLS Streaming Directory'))){?>
						<source src="<?php echo metadata('item',array('Streaming Video','HLS Streaming Directory'));?><?php echo metadata('item',array('Streaming Video','HLS Video Filename'));?>" type='application/x-mpegurl'/>
					<?php } ?>
					<?php if (metadata('item',array('Streaming Video','HTTP Streaming Directory'))){?>
						<source src="<?php echo metadata('item',array('Streaming Video','HTTP Streaming Directory'));?><?php echo metadata('item',array('Streaming Video','HTTP Video Filename'));?>" type='video/mp4'/>
					<?php } ?>
				 <p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
				<?php } ?>
			</video>
			<?php if ($external){?>
				<div class="btn-group-horizontal" style="padding-left: 35%;">
			<!--	        <div class="btn-group-horizontal">
				<label for="CurrentPos" style="float:left; padding: 1% 1% 1% 1%;" >Current:&nbsp;</label>
			            <input type="text" id="CurrentPos" style="border: 0; color: #333333 !important; width:12%; float:left; padding: 1% 1% 1% 1%;" />
						</div> -->
						<div class="btn-group-horizontal btn-group-xs" style="float:left; width:50%; padding: 1% 1% 1% 1%;">
						<bb1Button type="button" class="btn" style="border: 0 !important; background: url('<?php echo WEB_ROOT;?>/plugins/VideoStream/views/public/images/arrow_clockwise_10.png') no-repeat !important; width:25px; height:25px;"></bb1Button>
						<bb10Button type="button" class="btn" style="border: 0 !important; background: url('<?php echo WEB_ROOT;?>/plugins/VideoStream/views/public/images/arrow_clockwise_30.png') no-repeat !important; width:30px; height:30px;"></bb10Button>
						<pauseButton type="button" class="btn btn-secondary" style="background: #F8F4E9 !important;">&#62;&#47;&#61; </pauseButton>
						<ff10Button type="button" class="btn" style="border: 0 !important; background: url('<?php echo WEB_ROOT;?>/plugins/VideoStream/views/public/images/arrow_cclockwise_30.png') no-repeat !important; width:30px; height:30px;"></ff10Button>				
						<ff1Button type="button" class="btn" style="border: 0 !important; background: url('<?php echo WEB_ROOT;?>/plugins/VideoStream/views/public/images/arrow_cclockwise_10.png') no-repeat !important; width:25px; height:25px;"></ff1Button>
						</div>
			<!--			<div class="btn-group-horizontal">
						<label for="segmentStart<?php echo $eid_suffix;?>">Playback:&nbsp;</label>
						<input type="text" id="segmentStart<?php echo $eid_suffix;?>" style="border: 0; color: #333333 !important; width: 25%; font-style:italic;"/>
					    </div> -->
				</div>
			<?php }?>
</div>
<script type="text/javascript">
var startTime<?php echo $eid_suffix;?> = new Array();
var endTime<?php echo $eid_suffix;?> = new Array();
<?php $a=0;
foreach($attachments as $vattItem):
	$vitem = $vattItem->getItem();	
	set_current_record('Item',$vitem); ?>

	startTime<?php echo $eid_suffix;?>[<?php echo $a;?>] = calculateTime("<?php echo metadata('item', array('Streaming Video','Segment Start'));?>");
	endTime<?php echo $eid_suffix;?>[<?php echo $a++;?>] = calculateTime("<?php echo metadata('item', array('Streaming Video','Segment End'));?>") ;
<?php endforeach;?>
videojs("video<?php echo $eid_suffix;?>").ready(function(){
  var myPlayer<?php echo $eid_suffix;?> = this;    // Store the video object
  var aspectRatio = 3/4;
	jQuery('#video<?php echo $eid_suffix;?> > div.vjs-control-bar > div.vjs-duration.vjs-time-controls.vjs-control > div').text(getFormattedTimeString(endTime<?php echo $eid_suffix;?>[endTime<?php echo $eid_suffix;?>.length-1]));
	jQuery("#CurrentPos").val(getFormattedTimeString(myPlayer<?php echo $eid_suffix;?>.currentTime()));

  function resizeVideoJS(){
	var width = (document.getElementById("video<?php echo $eid_suffix;?>").parentElement.offsetWidth)*<?php echo $width / 100 ;?>;
	myPlayer<?php echo $eid_suffix;?>.width(width).height( width * aspectRatio );
}

	resizeVideoJS();
	window.onresize = resizeVideoJS;
  // EXAMPLE: Start playing the video.
  myPlayer<?php echo $eid_suffix;?>.currentTime(startTime<?php echo $eid_suffix;?>[0]);
	myPlayer<?php echo $eid_suffix;?>.play();
  
  videojs("video<?php echo $eid_suffix;?>").on("timeupdate",checkTime);
});
var checkTime<?php echo $eid_suffix;?> = function(){
  var myPlayer<?php echo $eid_suffix;?> = videojs("video<?php echo $eid_suffix;?>");
  var ctime = "0:00:00";
  var scenes;
  var sel;
  var i = 0;
  ctime = calculateTime(videojs("video<?php echo $eid_suffix;?>").currentTime());
	jQuery('#video<?php echo $eid_suffix;?> > div.vjs-control-bar > div.vjs-duration.vjs-time-controls.vjs-control > div').text(getFormattedTimeString(endTime<?php echo $eid_suffix;?>[endTime<?php echo $eid_suffix;?>.length-1]));
	jQuery("#CurrentPos<?php echo $eid_suffix;?>").val(getFormattedTimeString(myPlayer<?php echo $eid_suffix;?>.currentTime()));
	if (myPlayer<?php echo $eid_suffix;?>.currentTime() > endTime<?php echo $eid_suffix;?>[endTime<?php echo $eid_suffix;?>.length-1]) {
		myPlayer<?php echo $eid_suffix;?>.pause();
		myPlayer<?php echo $eid_suffix;?>.on("pause",newEndTime);
		};
	if (myPlayer<?php echo $eid_suffix;?>.currentTime() < startTime<?php echo $eid_suffix;?>[0]) {
		myPlayer<?php echo $eid_suffix;?>.pause();
		myPlayer<?php echo $eid_suffix;?>.on("pause",newStartTime);
		};
 
		<?php if ($current) {
		        ?>
        scenes = getElementsByClass("scene<?php echo $eid_suffix;?>");
        for (i; i < scenes.length; i++) {
            sel = scenes[i];
            if (calculateTime(sel.getAttribute('id')) < ctime && calculateTime(sel.getAttribute('title')) > ctime) 			
			{
                sel.style.display = 'block';
            } else {
                sel.style.display = 'none';
            }
        }
		<?php }?>
  	};
var newStartTime = function(){
	var myPlayer<?php echo $eid_suffix;?> = videojs("video<?php echo $eid_suffix;?>");
	jQuery('#video<?php echo $eid_suffix;?> > div.vjs-control-bar > div.vjs-duration.vjs-time-controls.vjs-control > div').text(getFormattedTimeString(endTime<?php echo $eid_suffix;?>[endTime<?php echo $eid_suffix;?>.length-1]));
	jQuery("#CurrentPos<?php echo $eid_suffix;?>").val(getFormattedTimeString(myPlayer<?php echo $eid_suffix;?>.currentTime()));  
	
	myPlayer<?php echo $eid_suffix;?>.currentTime(startTime<?php echo $eid_suffix;?>[0]);
	myPlayer<?php echo $eid_suffix;?>.off("pause",newStartTime<?php echo $eid_suffix;?>);
	myPlayer<?php echo $eid_suffix;?>.off("pause",newEndTime<?php echo $eid_suffix;?>);
		myPlayer<?php echo $eid_suffix;?>.play();
};
var newEndTime = function(){
	var myPlayer<?php echo $eid_suffix;?> = videojs("video<?php echo $eid_suffix;?>");
	jQuery('#video<?php echo $eid_suffix;?> > div.vjs-control-bar > div.vjs-duration.vjs-time-controls.vjs-control > div').text(getFormattedTimeString(endTime<?php echo $eid_suffix;?>[endTime<?php echo $eid_suffix;?>.length-1]));
	jQuery("#CurrentPos<?php echo $eid_suffix;?>").val(getFormattedTimeString(myPlayer<?php echo $eid_suffix;?>.currentTime()));  
	
	myPlayer<?php echo $eid_suffix;?>.currentTime(endTime<?php echo $eid_suffix;?>[endTime<?php echo $eid_suffix;?>.length-1]);
	myPlayer<?php echo $eid_suffix;?>.off("pause",newEndTime<?php echo $eid_suffix;?>);
	myPlayer<?php echo $eid_suffix;?>.off("pause",newStartTime<?php echo $eid_suffix;?>);
	
};
        function getElementsByClass(searchClass, domNode, tagName)
        {
            if (domNode == null) {
                domNode = document;
            }
            if (tagName == null) {
                tagName = '*';
            }
            var el = new Array();
            var tags = domNode.getElementsByTagName(tagName);
            var tcl = " "+searchClass+" ";
            for (i=0,j=0; i<tags.length; i++) {
                var test = " " + tags[i].className + " ";
                if (test.indexOf(tcl) != -1) {
                    el[j++] = tags[i];
                }
            }
            return el;
        }
</script>

		<?php if ($current) {
		        ?>
		        <?php $orig_item=get_current_record('item');
				$orig_video = metadata("item", array("Streaming Video","Video Filename"));
		        ?>
		       <?php
				foreach($attachments as $attItem):
				$item = $attItem->getItem();	
				set_current_record('Item',$item);
		        if (metadata('item',array('Streaming Video','Show Item'))){
		            ?>
		            <div class="scene<?php echo $eid_suffix;?>" id="<?php echo metadata('item',array('Streaming Video','Segment Start'));
		            ?>" title="<?php echo metadata('item',array('Streaming Video','Segment End'));
		            ?>" style="display:none;">
		            <h2>Current video segment:</h2>
		            <h3><?php echo link_to_item(metadata('item',array('Dublin Core','Title')));
		            ?></h3>
		            <div style="overflow:auto; max-height:150px;">
		            <p> <?php echo metadata('item',array('Dublin Core', 'Description'));
		            ?> </p>
		            </div>
		            <p>Segment:&nbsp;<?php echo metadata('item',array('Streaming Video','Segment Start'));
		            ?>
		            &nbsp;--&nbsp;
		            <?php echo metadata('item',array('Streaming Video','Segment End'));
		            ?>
		            </p>
		            </div> <!-- end of loop div for display -->
		        <?php }
		        ;
		        ?>
		        <?php endforeach;
		        ?>
		        <hr style="color:lt-gray;"/>
		        <?php set_current_record('item',$orig_item);
		        ?>

			        <?php
			    }
			    ?>
<script type="text/javascript">
var myPlayer<?php echo $eid_suffix;?> = videojs("video<?php echo $eid_suffix;?>");
jQuery("#segmentStart<?php echo $eid_suffix;?>").val(getFormattedTimeString(startTime<?php echo $eid_suffix;?>[0])+'--'+getFormattedTimeString(endTime<?php echo $eid_suffix;?>[endTime<?php echo $eid_suffix;?>.length-1]));
//jQuery("#segmentEnd<?php echo $eid_suffix;?>").val(getFormattedTimeString(endTime<?php echo $eid_suffix;?>[endTime<?php echo $eid_suffix;?>.length-1]));
//jQuery("pauseButton").button();
//jQuery("bb10Button").button();
//jQuery("ff10Button").button();
//jQuery("bb1button").button();
//jQuery("ff1Button").button();
jQuery("pauseButton").click(function(){
	if(myPlayer<?php echo $eid_suffix;?>.paused())
		{ myPlayer<?php echo $eid_suffix;?>.play()}
	else
 		{ myPlayer<?php echo $eid_suffix;?>.pause(); }
});  
jQuery("bb1Button").click(function(){
	var newbTime=myPlayer<?php echo $eid_suffix;?>.currentTime()-10;	
	myPlayer<?php echo $eid_suffix;?>.currentTime(newbTime);

});
jQuery("ff1Button").click(function(){
	var newfTime=myPlayer<?php echo $eid_suffix;?>.currentTime()+10;	
	myPlayer<?php echo $eid_suffix;?>.currentTime(newfTime);

});
jQuery("bb10Button").click(function(){
	var newbbTime=myPlayer<?php echo $eid_suffix;?>.currentTime()-30;	
	myPlayer<?php echo $eid_suffix;?>.currentTime(newbbTime);

});
jQuery("ff10Button").click(function(){
	var newffTime=myPlayer<?php echo $eid_suffix;?>.currentTime()+30;	
	myPlayer<?php echo $eid_suffix;?>.currentTime(newffTime);

});
videojs("video<?php echo $eid_suffix;?>").on("timeupdate",checkTime<?php echo $eid_suffix++;?>);
</script>

