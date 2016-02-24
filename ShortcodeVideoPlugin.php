<?php

class ShortcodeVideoPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array('public_head','install','uninstall','initialize');
    protected $_filters = array(
        'exhibit_layouts');

    public function setUp()
    {
        add_shortcode('vidplayer', array('ShortcodeVideoPlugin', 'vidplayer'));
        parent::setUp();
    }
    
	public function hookInitialize()
	{
		get_view()->addHelperPath(dirname(__FILE__) . '/views/helpers', 'ShortcodeVideo_View_Helper_');
	}

	public function hookInstall()
    {
	    $db = get_db();
		//if (!$db->query("Select name from {$db->prefix}plugins where name = 'VideoStream'")) {
		// Don't install if an element set named "Streaming Video" already exists.
	    if ($db->getTable('ElementSet')->findByName('Streaming Video')) {
	          throw new Exception('An element set by the name "Streaming Video" already exists. You must delete that '
	                         . 'element set to install this plugin.');
			}
			
			$elementSetMetadata = array(
				'record_type'        => "Item", 
				'name'        => "Streaming Video", 
				'description' => "Elements needed for streaming video for the VideoStream Plugin"
			);
			$elements = array(
				array(
					'name'           => "Video Filename",
					'description'    => "Actual filename of the video on the video source server"
				), 
				array(
					'name'           => "Video Streaming URL",
					'description'    => "Actual URL of the streaming server without the filename"
				), 
				array(
					'name'           => "Video Type",
					'description'    => "Encoding for the video; mp4, flv, mov, and so forth"
				), 
				array(
					'name'           => "HLS Streaming Directory",
					'description'    => "Directory location on your server for the HLS .m3u8 file."
				), 
				array(
					'name'           => "HLS Video Filename",
					'description'    => "Filename for HLS video file. Include any subdirectories."
				), 
				array(
					'name'           => "HTTP Streaming Directory",
					'description'    => "Directory location for files to HTTP stream directly from Web Server."
				), 
				array(
					'name'           => "HTTP Video Filename",
					'description'    => "Actual filename of the video on the web server"
				), 
				array(
					'name'           => "Segment Start",
					'description'    => "Start point in video in either seconds or hh:mm:ss"
				), 
				array(
					'name'           => "Segment End",
					'description'    => "End point in video in either seconds or hh:mm:ss"
				), 
				array(
					'name'           => "Segment Type",
					'description'    => "Use segment type to help determine how segment is to be displayed. For instance, an event may encompass many scenes, etc."
				), 
				array(
					'name'           => "Show Item",
					'description'    => "Should item be shown in a list. Can be useful in cetain types of displays where you may not want to have all items shown."
				), 
				array(
					'name'           => "Video Source",
					'description'    => "Source of video. Streaming server, YouTube, etc."
				) 
				// etc.
			);
			insert_element_set($elementSetMetadata, $elements);
		//}
    }
	
	public function hookUninstall()
	{
		$db=get_db();
       	//if (!$db->query("Select name from {$db->prefix}plugins where name = 'VideoStream'")) {
			if ($elementSet = $db->getTable('ElementSet')->findByName("Streaming Video")) {
            	$elementSet->delete();
        	}
		//}
	}
	
    public function hookPublicHead($args)
    {
        
        echo queue_css_file("vidStyle");
        echo queue_css_file("video-js"); 
        echo queue_css_file("jquery-ui");
        echo queue_css_file("bootstrap");
		echo js_tag('video');
		echo js_tag('youtube');
        echo js_tag('pfUtils');
        echo js_tag('jquery');
        echo js_tag('jquery-ui');
        echo js_tag('bootstrap');?>
<?php
    }
    public function hookExhibitBuilderPageHead($args)
    {
        echo queue_css_file("vidStyle");
        echo queue_css_file("video-js"); 
        echo queue_css_file("jquery-ui");
		echo js_tag('video');
		echo js_tag('youtube');
        echo js_tag('pfUtils');
        echo js_tag('jquery');
        echo js_tag('jquery-ui');
    }

    public function filterExhibitLayouts($layouts)
    {
        $layouts['vidplayer'] = array(
            'name' => __('Video Player'),
            'description' => __('Select video ids to display in the player')
        );
        return $layouts;
    }
    /**
     * Build HTML for the carousel
     * @param array $args
     * @param Zend_View $view
     * @return string HTML to display
     */
    public static function vidplayer($args, $view)
    {
        static $id_suffix = 0;
        if (isset($args['float'])) {
            $params['float'] = $args['float'];
        }else{
			$params['float'] = 'left';
		}

        if (isset($args['width'])) {
            $params['width'] = $args['width'];
        }
		
        if (isset($args['height'])) {
            $params['height'] = $args['height'];
		}
		
        if (isset($args['ids'])) {
            $params['range'] = $args['ids'];
        }

        if (isset($args['sort'])) {
            $params['sort_field'] = $args['sort'];
        }

        if (isset($args['ext'])) {
            $params['ext'] = $args['ext'];
        }

        if (isset($args['current'])) {
            $params['current'] = $args['current'];
        }

		$result = preg_replace_callback('/(\d+)-(\d+)/', function($m) {
		    return implode(',', range($m[1], $m[2]));
		}, $args['ids']);
		$ids = explode(',',$result);
		foreach ($ids as $key => $item){
			if (get_record_by_id('item',$item)):
        		$items[$key] = get_record_by_id('item', $item);
			endif;
		};
        //handle the configs for jCarousel
        $configs = array('vidplayer' => array());

        //carousel configs
        if(isset($args['speed'])) {
            if(is_numeric($args['speed'])) {
                $configs['vidplayer']['animation'] = (int) $args['speed'];
            } else {
                $configs['carousel']['animation'] = $args['speed'];
            }
        }
        if(isset($args['showtitles']) && $args['showtitles'] == 'true') {
            $configs['vidplayer']['showTitles'] = true;
        }
        //autoscroll configs
        if(isset($args['autoscroll']) && $args['autoscroll'] == 'true') {
            $configs['autoscroll'] = array();
            if(isset($args['interval'])) {
                $configs['autoscroll']['interval'] = (int) $args['interval'];
            }
        }
        $html = $view->partial('vidplayer.php', array('items' => $items, 'id_suffix' => $id_suffix, 'params' => $params));
        $id_suffix++;
        return $html;
    }
}
