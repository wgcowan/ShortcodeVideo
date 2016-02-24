<?php
$float = isset($options['video-float'])
	? html_escape($options['video-float'])
	: 'left';
$width = isset($options['video-width'])
	? html_escape($options['video-width'])
	: '95%';
if ($width == '100'){
	$width = '95';
};
$height='360';
$awidth=array(
	"100%"=> "85%",
	"90%" => "85%",
	"80%" => "95%",
	"70%" => "100%",
	"60%" => "100%",
	"50%" => "100%",
	"40%" => "100%",
);
$external = isset($options['video-controls'])
	? html_escape($options['video-controls'])
	: false;
$current = isset($options['current-seg'])
	? html_escape($options['current-seg'])
	: true;
$eid_suffix = 0;
global $eid_suffix;

?>

<?php 
	$poster=array();
	$caption=array();
	$ci = 0;
	foreach($attachments as $attItem):
	$item = $attItem->getItem();	
	set_current_record('Item',$item);
	if (metadata($item,'has files')){
				$files = $item->Files;
				    foreach($files as $file) {
				        if($file->hasThumbnail()) {
							$poster[$ci]= file_display_url($file,'thumbnail');
				        }
					}
				}
	endforeach;
	?>
	
			<video id="video<?php echo $eid_suffix;?>" class="video-js vjs-default-skin" style="float:<?php echo $float?>;"
				  controls preload="auto" width=100% height=<?php echo $height;?>
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
						<source src="<?php echo metadata('item',array('Streaming Video','HLS Streaming Directory'));?><?php echo metadata('item',array('Streaming Video','HLS Video Filename'));?>" type='application/vnd.apple.mpegurl'/>
					<?php } ?>
					<?php if (metadata('item',array('Streaming Video','HTTP Streaming Directory'))){?>
						<source src="<?php echo metadata('item',array('Streaming Video','HTTP Streaming Directory'));?><?php echo metadata('item',array('Streaming Video','HTTP Video Filename'));?>" type='video/mp4'/>
					<?php } ?>
				 <p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
				<?php } ?>
			</video>

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

  function resizeVideoJS(){
	var width = (document.getElementById("video<?php echo $eid_suffix;?>").parentElement.offsetWidth)*<?php echo $width / 100 ;?>;
	myPlayer<?php echo $eid_suffix;?>.width(width).height( width * aspectRatio );
}

	resizeVideoJS();
	window.onresize = resizeVideoJS;
  // EXAMPLE: Start playing the video.
  //myPlayer.pause();
  myPlayer<?php echo $eid_suffix;?>.currentTime(startTime<?php echo $eid_suffix;?>[0]);
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
		myPlayer<?php echo $eid_suffix;?>.on("pause",newEndTime<?php echo $eid_suffix;?>);
		};
	if (myPlayer<?php echo $eid_suffix;?>.currentTime() < startTime<?php echo $eid_suffix;?>[0]) {
		myPlayer<?php echo $eid_suffix;?>.pause();
		myPlayer<?php echo $eid_suffix;?>.on("pause",newStartTime<?php echo $eid_suffix;?>);
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
var newStartTime<?php echo $eid_suffix;?> = function(){
	var myPlayer<?php echo $eid_suffix;?> = videojs("video<?php echo $eid_suffix;?>");
	jQuery('#video<?php echo $eid_suffix;?> > div.vjs-control-bar > div.vjs-duration.vjs-time-controls.vjs-control > div').text(getFormattedTimeString(endTime<?php echo $eid_suffix;?>[endTime<?php echo $eid_suffix;?>.length-1]));
	jQuery("#CurrentPos<?php echo $eid_suffix;?>").val(getFormattedTimeString(myPlayer<?php echo $eid_suffix;?>.currentTime()));  
	
	myPlayer<?php echo $eid_suffix;?>.currentTime(startTime<?php echo $eid_suffix;?>[0]);
	myPlayer<?php echo $eid_suffix;?>.off("pause",newStartTime<?php echo $eid_suffix;?>);
	myPlayer<?php echo $eid_suffix;?>.off("pause",newEndTime<?php echo $eid_suffix;?>);
		myPlayer<?php echo $eid_suffix;?>.play();
};
var newEndTime<?php echo $eid_suffix;?> = function(){
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
<?php if ($external){?>
	<style type="text/css">
	#hideoverflow { overflow: hidden; }
	#outer { position: relative; left: 27%; float: left; }
	#inner { position: relative; left: -10%; float: left; }
	</style>
	
	<div id="hideoverflow" class="btn-group-horizontal" style="width: 100%;font-size:15px;">
        <div id="outer" class="btn-group-horizontal">
		<div id="inner">
        <input type="text" id="CurrentPos" style="width:100px; border: 0; color: #333333 !important; "/>
		</div>
		</div>
		<div id="outer" class="btn-group-horizontal btn-group-xs">
		<bb1Button id="inner" type="button" class="btn" style="border: 0 !important; background: url('<?php echo WEB_ROOT;?>/plugins/ShortcodeVideo/views/public/images/arrow_clockwise_10.png') no-repeat !important; width:25px; height:25px;"></bb1Button>
		<bb10Button id="inner" type="button" class="btn" style="border: 0 !important; background: url('<?php echo WEB_ROOT;?>/plugins/ShortcodeVideo/views/public/images/arrow_clockwise_30.png') no-repeat !important; width:30px; height:30px;"></bb10Button>
		<pauseButton id="inner" type="button" class="btn btn-secondary" style="background: #F8F4E9 !important;">&#62;&#47;&#61; </pauseButton>
		<ff10Button id="inner" type="button" class="btn" style="border: 0 !important; background: url('<?php echo WEB_ROOT;?>/plugins/ShortcodeVideo/views/public/images/arrow_cclockwise_30.png') no-repeat !important; width:30px; height:30px;"></ff10Button>				
		<ff1Button id="inner" type="button" class="btn" style="border: 0 !important; background: url('<?php echo WEB_ROOT;?>/plugins/ShortcodeVideo/views/public/images/arrow_cclockwise_10.png') no-repeat !important; width:25px; height:25px;"></ff1Button>
		</div>
		<div id="outer" class="btn-group-horizontal">
			<div id="inner">
		<input type="text" id="segmentStart<?php echo $eid_suffix;?>" style="width:80px; border: 0; color: #333333 !important;"/>
			</div>
	    </div>
	</div>
<?php }?>
		<?php if ($current) {
		        ?>
		        <?php 
				$orig_item=get_current_record('item');
				$orig_video = metadata("item", array("Streaming Video","Video Filename"));
		        ?>
				<?php $sitems=get_records('item',array('collection'=>metadata('item','collection id'),null),null);?>
		       <?php
				foreach($attachments as $aItem):
				$item = $aItem->getItem();	
				set_current_record('Item',$item);
				$startTime=$this->getCalculatedTime(metadata('item',array('Streaming Video','Segment Start')));
				$endTime=$this->getCalculatedTime(metadata('item',array('Streaming Video','Segment End')));
				$sitems=get_records('item',array('collection'=>metadata('item','collection id'),null),null);?>
		       <?php
				foreach($sitems as $attItem):
				set_current_record('Item',$attItem);
				if ($orig_video == metadata("item", array("Streaming Video","Video Filename"))){
				if (($this->getCalculatedTime(metadata('item',array('Streaming Video','Segment Start'))) >= $startTime) && ($this->getCalculatedTime(metadata('item',array('Streaming Video','Segment End'))) <= $endTime)){
		        if (metadata('item',array('Streaming Video','Show Item'))){
		            ?>
		            <div class="scene<?php echo $eid_suffix;?>" id="<?php echo metadata('item',array('Streaming Video','Segment Start'));
		            ?>" title="<?php echo metadata('item',array('Streaming Video','Segment End'));
		            ?>" style="display:none;">
					<ul style="width:100%;list-style-type: none; margin:3%; padding:0;">
					<li class=caption style="width:<?php echo $width;?>; float:left;"><b><?php echo metadata('item',array('Dublin Core', 'Title'));?></b>
					<?php if ($aItem->caption) { ?>
						<li class=caption style="width:<?php echo $width;?>; float:left;"><?php echo $aItem->caption;?></li>
					<?php }else{ ?>
		            	<li class=caption style="width:<?php echo $width;?>; float:left;"><p><?php echo metadata('item',array('Dublin Core', 'Description'));?></p>
			 			</li>
					<?php }; ?>
		
		            <li class=caption style="float:left;">Segment:&nbsp;<?php echo $this->getFormattedTimeString(metadata('item',array('Streaming Video','Segment Start')));
		            ?>
		            &nbsp;--&nbsp;
		            <?php echo $this->getFormattedTimeString(metadata('item',array('Streaming Video','Segment End')));
		            ?>
		            </li>
		            </div> <!-- end of loop div for display -->
		        	<?php };?>
			        <?php };?>
			        <?php };?>
		            <?php endforeach;?>
			    <?php endforeach;?>
		        <hr style="color:lt-gray;"/>
			        <?php } ?>
<script type="text/javascript">
var myPlayer<?php echo $eid_suffix;?> = videojs("video<?php echo $eid_suffix;?>");
jQuery("#segmentStart<?php echo $eid_suffix;?>").val(getFormattedTimeString(endTime<?php echo $eid_suffix;?>[endTime<?php echo $eid_suffix;?>.length-1]));
jQuery("pauseButton<?php echo $eid_suffix;?>").button();
jQuery("bb10Button<?php echo $eid_suffix;?>").button();
jQuery("ff10Button<?php echo $eid_suffix;?>").button();
jQuery("bb1button<?php echo $eid_suffix;?>").button();
jQuery("ff1Button<?php echo $eid_suffix;?>").button();
jQuery("pauseButton<?php echo $eid_suffix;?>").click(function(){
	if(myPlayer<?php echo $eid_suffix;?>.paused())
		{ myPlayer<?php echo $eid_suffix;?>.play()}
	else
 		{ myPlayer<?php echo $eid_suffix;?>.pause(); }
});  
jQuery("bb1Button<?php echo $eid_suffix;?>").click(function(){
	var newbTime=myPlayer<?php echo $eid_suffix;?>.currentTime()-10;	
	myPlayer<?php echo $eid_suffix;?>.currentTime(newbTime);

});
jQuery("ff1Button<?php echo $eid_suffix;?>").click(function(){
	var newfTime=myPlayer<?php echo $eid_suffix;?>.currentTime()+10;	
	myPlayer<?php echo $eid_suffix;?>.currentTime(newfTime);

});
jQuery("bb10Button<?php echo $eid_suffix;?>").click(function(){
	var newbbTime=myPlayer<?php echo $eid_suffix;?>.currentTime()-30;	
	myPlayer<?php echo $eid_suffix;?>.currentTime(newbbTime);

});
jQuery("ff10Button<?php echo $eid_suffix;?>").click(function(){
	var newffTime=myPlayer<?php echo $eid_suffix;?>.currentTime()+30;	
	myPlayer<?php echo $eid_suffix;?>.currentTime(newffTime);

});
videojs("video<?php echo $eid_suffix;?>").on("timeupdate",checkTime<?php echo $eid_suffix++;?>);
</script>

