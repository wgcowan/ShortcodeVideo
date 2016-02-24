<?php 
$float = isset($params['float'])
	? html_escape($params['float'])
	: 'left';
$width = isset($params['width'])
	? html_escape($params['width'])
	: '100%';
$height = isset($params['height'])
	? html_escape($params['height'])
	: '360';
$external = isset($params['ext'])?html_escape($params['ext']) : false;
$current = isset($params['current'])?html_escape($params['current']) : false;
?>
<?php 
	$orig=$items[0];
	foreach($items as $item):
	set_current_record('Item',$item);
	if (metadata($item,'has files')){
	$files = $item->Files;
	    foreach($files as $file) {
	        if($file->hasThumbnail()) {
				$poster[]= file_display_url($file,'thumbnail');	
	        }
		}
	}
	endforeach;
	set_current_record('item',$orig);
	?>
		<div id="vid_player-<?php echo $id_suffix;?>" style="width:<?php echo $width;?>; height:<?php echo $height;?>;  float:<?php echo $float;?>; padding: 0 7% 0% 3%;">
			<video id="video<?php echo $id_suffix;?>" class="video-js vjs-default-skin" 
			  controls autoplay preload="auto" width=100% height=<?php echo $height;?>
			ytcontrols playsInline
			<?php if (isset($poster[0])){?>
			poster="<?php echo $poster[0];?>"
			<?php }?>
			<?php if (metadata('item', array('Streaming Video','Segment Type')) == 'youtube'){?>
				data-setup='{ "inactivityTimeout": 2000, "techOrder": ["youtube"], "sources": [{ "type": "video/youtube", "src": "<?php echo metadata('item',array('Streaming Video','HTTP Streaming Directory'));?><?php echo metadata('item',array('Streaming Video','HTTP Video Filename'));?>"}], "Youtube": { "iv_load_policy": 1 }, "Youtube": { "ytControls": 2 } }'>
			<?php } else { ?>
		  		data-setup='{"example_option":true, "inactivityTimeout": 2000, "nativeControlsForTouch": false}'>
				<?php if (metadata('item',array('Streaming Video','Video Streaming URL'))){?>
					<source src="<?php echo metadata('item',array('Streaming Video','Video Streaming URL'));?><?php echo $this->getVideoType(metadata('item',array('Streaming Video','Video Type')));?><?php echo metadata('item',array('Streaming Video','Video Filename'));?>" type='rtmp/mp4'/>
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
			<div class="btn-group-horizontal" style="margin: 0 0 0 27%; font-size:15px;">
		        <div class="btn-group-horizontal">
                	<input type="text" id="CurrentPos" style="border: 0; color: #333333 !important; width:17%; float:left;" />
				</div>
				<div class="btn-group-horizontal btn-group-xs" style="float:left; width:40%; margin-right:-2em; margin-left: 2em;">
					<bb1Button type="button" class="btn" style="border: 0 !important; background: url('<?php echo WEB_ROOT;?>/plugins/ShortcodeVideo/views/public/images/arrow_clockwise_10.png') no-repeat !important; width:25px; height:25px;"></bb1Button>
					<bb10Button type="button" class="btn" style="border: 0 !important; background: url('<?php echo WEB_ROOT;?>/plugins/ShortcodeVideo/views/public/images/arrow_clockwise_30.png') no-repeat !important; width:30px; height:30px;"></bb10Button>
					<pauseButton type="button" class="btn btn-secondary" style="background: #F8F4E9 !important;">&#62;&#47;&#61; </pauseButton>
					<ff10Button type="button" class="btn" style="border: 0 !important; background: url('<?php echo WEB_ROOT;?>/plugins/ShortcodeVideo/views/public/images/arrow_cclockwise_30.png') no-repeat !important; width:30px; height:30px;"></ff10Button>				
					<ff1Button type="button" class="btn" style="border: 0 !important; background: url('<?php echo WEB_ROOT;?>/plugins/ShortcodeVideo/views/public/images/arrow_cclockwise_10.png') no-repeat !important; width:25px; height:25px;"></ff1Button>
				</div>
				<div class="btn-group-horizontal">
					<input type="text" id="segmentStart<?php echo $id_suffix;?>" style="border: 0; color: #333333 !important; width: 25%; float:left;"/>
			    </div>
			</div>
	        <hr style="color:lt-gray;"/>
	        
		<?php }?>
		<?php if ($current) {?>
			   <?php
				$orig_item=get_current_record('item');
				$orig_video = metadata("item", array("Streaming Video","Video Filename"));?>
	            <?php $sitems=get_records('item',array('collection'=>metadata('item','collection id'),null),null);?>
		        <?php $startTime=$this->getCalculatedTime(metadata('item',array('Streaming Video','Segment Start')));
				foreach($items as $item):
					set_current_record('Item',$item);
					$endTime=$this->getCalculatedTime(metadata('item',array('Streaming Video','Segment End')));
				endforeach;?>
		       <?php
				foreach($sitems as $attItem):
				set_current_record('Item',$attItem);
				if ($orig_video == metadata("item", array("Streaming Video","Video Filename"))){
				if (($this->getCalculatedTime(metadata('item',array('Streaming Video','Segment Start'))) >= $startTime) && ($this->getCalculatedTime(metadata('item',array('Streaming Video','Segment End'))) <= $endTime)){
		        	if (metadata('item',array('Streaming Video','Show Item'))){ ?>
		            	<div class="scene<?php echo $id_suffix;?>" id="<?php echo metadata('item',array('Streaming Video','Segment Start'));?>"
							title="<?php echo metadata('item',array('Streaming Video','Segment End'));?>" style="display:none;">
		            		<h4><p style="float:left; width:100%;">Current video segment:</p></h4>
		            		<p><?php echo metadata('item',array('Dublin Core','Title'));?></p>
		            		<div>
		            		<p> <?php echo metadata('item',array('Dublin Core', 'Description'));?> </p>
		            		</div>
		            		<p>Segment:&nbsp<?php echo $this->getFormattedTimeString(metadata('item',array('Streaming Video','Segment Start')));?>--<?php echo $this->getFormattedTimeString(metadata('item',array('Streaming Video','Segment End')));?>
		            		</p>

					        <hr style="color:lt-gray;"/>
		
		            	</div> <!-- end of loop div for display -->
		        	<?php };?>
				<?php };?>
				<?php };?>
		        <?php endforeach;?>
		        <?php set_current_record('item',$orig_item);?>

		<?php }?>
<script type="text/javascript">

var startTime<?php echo $id_suffix;?> = new Array();
var endTime<?php echo $id_suffix;?> = new Array();
<?php $a=0;
foreach($items as $vitem):
	set_current_record('Item',$vitem); ?>

	startTime<?php echo $id_suffix;?>[<?php echo $a;?>] = calculateTime("<?php echo metadata('item', array('Streaming Video','Segment Start'));?>");
	endTime<?php echo $id_suffix;?>[<?php echo $a++;?>] = calculateTime("<?php echo metadata('item', array('Streaming Video','Segment End'));?>") ;
<?php endforeach;?>
var checkTime = function(){
  var myPlayer<?php echo $id_suffix;?> = videojs("video<?php echo $id_suffix;?>");
  var ctime = "0:00:00";
  var scenes;
  var sel;
  var i = 0;
  ctime = calculateTime(videojs("video<?php echo $id_suffix;?>").currentTime());
	jQuery('#video<?php echo $id_suffix;?> > div.vjs-control-bar > div.vjs-duration.vjs-time-controls.vjs-control > div').text(getFormattedTimeString(endTime<?php echo $id_suffix;?>[endTime<?php echo $id_suffix;?>.length-1]));
	jQuery("#CurrentPos").val(getFormattedTimeString(myPlayer<?php echo $id_suffix;?>.currentTime()));
	if (myPlayer<?php echo $id_suffix;?>.currentTime() > endTime<?php echo $id_suffix;?>[endTime<?php echo $id_suffix;?>.length-1]) {
		myPlayer<?php echo $id_suffix;?>.pause();
		myPlayer<?php echo $id_suffix;?>.on("pause",newEndTime);
		};
	if (myPlayer<?php echo $id_suffix;?>.currentTime() < startTime<?php echo $id_suffix;?>[0]) {
		myPlayer<?php echo $id_suffix;?>.pause();
		myPlayer<?php echo $id_suffix;?>.on("pause",newStartTime);
		};
		<?php if ($current) {
		        ?>
        scenes = getElementsByClass("scene<?php echo $id_suffix;?>");
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
videojs("video<?php echo $id_suffix;?>").ready(function(){
  var myPlayer<?php echo $id_suffix;?> = this;
	jQuery('#video<?php echo $id_suffix;?> > div.vjs-control-bar > div.vjs-duration.vjs-time-controls.vjs-control > div').text(getFormattedTimeString(endTime<?php echo $id_suffix;?>[endTime<?php echo $id_suffix;?>.length-1]));
	jQuery("#CurrentPos").val(getFormattedTimeString(myPlayer<?php echo $id_suffix;?>.currentTime()));
  // EXAMPLE: Start playing the video.
  myPlayer<?php echo $id_suffix;?>.currentTime(startTime<?php echo $id_suffix;?>[0]);
  myPlayer<?php echo $id_suffix;?>.pause();
  videojs("video<?php echo $id_suffix;?>").on("timeupdate",checkTime);

});
var newStartTime = function(){
	var myPlayer<?php echo $id_suffix;?> = videojs("video<?php echo $id_suffix;?>");
	jQuery('#video<?php echo $id_suffix;?> > div.vjs-control-bar > div.vjs-duration.vjs-time-controls.vjs-control > div').text(getFormattedTimeString(endTime<?php echo $id_suffix;?>[endTime<?php echo $id_suffix;?>.length-1]));
	jQuery("#CurrentPos").val(getFormattedTimeString(myPlayer<?php echo $id_suffix;?>.currentTime()));  
	
	myPlayer<?php echo $id_suffix;?>.currentTime(startTime<?php echo $id_suffix;?>[0]);
	myPlayer<?php echo $id_suffix;?>.off("pause",newStartTime);
	myPlayer<?php echo $id_suffix;?>.off("pause",newEndTime);
		myPlayer<?php echo $id_suffix;?>.play();
};
var newEndTime = function(){
	var myPlayer<?php echo $id_suffix;?> = videojs("video<?php echo $id_suffix;?>");
	jQuery('#video<?php echo $id_suffix;?> > div.vjs-control-bar > div.vjs-duration.vjs-time-controls.vjs-control > div').text(getFormattedTimeString(endTime<?php echo $id_suffix;?>[endTime<?php echo $id_suffix;?>.length-1]));
	jQuery("#CurrentPos").val(getFormattedTimeString(myPlayer<?php echo $id_suffix;?>.currentTime()));  
	
	myPlayer<?php echo $id_suffix;?>.currentTime(endTime<?php echo $id_suffix;?>[endTime<?php echo $id_suffix;?>.length-1]);
	myPlayer<?php echo $id_suffix;?>.off("pause",newEndTime);
	myPlayer<?php echo $id_suffix;?>.off("pause",newStartTime);
	
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
	<script type="text/javascript">
	var myPlayer<?php echo $id_suffix;?> = videojs("video<?php echo $id_suffix;?>");
	jQuery("#segmentStart<?php echo $id_suffix;?>").val(getFormattedTimeString(endTime<?php echo $id_suffix;?>[endTime<?php echo $id_suffix;?>.length-1]));
//	jQuery("pauseButton").button();
//	jQuery("bb10Button").button();
//	jQuery("ff10Button").button();
//	jQuery("bb1button").button();
//	jQuery("ff1Button").button();
	jQuery("pauseButton").click(function(){
		if(myPlayer<?php echo $id_suffix;?>.paused())
			{ myPlayer<?php echo $id_suffix;?>.play()}
		else
	 		{ myPlayer<?php echo $id_suffix;?>.pause(); }
	});  
	jQuery("bb1Button").click(function(){
		var newbTime=myPlayer<?php echo $id_suffix;?>.currentTime()-10;	
		myPlayer<?php echo $id_suffix;?>.currentTime(newbTime);

	});
	jQuery("ff1Button").click(function(){
		var newfTime=myPlayer<?php echo $id_suffix;?>.currentTime()+10;	
		myPlayer<?php echo $id_suffix;?>.currentTime(newfTime);

	});
	jQuery("bb10Button").click(function(){
		var newbbTime=myPlayer<?php echo $id_suffix;?>.currentTime()-30;	
		myPlayer<?php echo $id_suffix;?>.currentTime(newbbTime);

	});
	jQuery("ff10Button").click(function(){
		var newffTime=myPlayer<?php echo $id_suffix;?>.currentTime()+30;	
		myPlayer<?php echo $id_suffix;?>.currentTime(newffTime);

	});
	</script>