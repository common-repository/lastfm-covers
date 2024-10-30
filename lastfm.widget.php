<?php
class LastFM_Widget extends WP_Widget {
	
	function LastFM_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'LastFM', 'description' => __('A widget that displays recent lastFM tracks played',"lastfm") );
	
		/* Widget control settings. */
		$control_ops = array( 'width' => 200, 'height' => 350, 'id_base' => 'lastfm-widget' );
	
		/* Create the widget. */
		$this->WP_Widget( 'lastfm-widget', __('LastFM Widget'), $widget_ops, $control_ops );
	}
	
	function getXML($xmlURL,$username,$type){
		$cachedFile = WP_CONTENT_DIR.'/plugins/'.plugin_basename(dirname(__FILE__))."/cache/$username-$type.xml";
		if(!file_exists($cachedFile)){
			$fp = fopen($cachedFile,"w");
			fclose($fp);
		}
		$contenu = file_get_contents($cachedFile);
		if((time()-filemtime($cachedFile)<120) && $contenu!=""){
			return $contenu;
		}
		else{
			$curl = curl_init();
			curl_setopt($curl,CURLOPT_URL,$xmlURL);
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($curl,CURLOPT_TIMEOUT,2);
			$contenu = curl_exec($curl);
			file_put_contents($cachedFile,$contenu);
		}
		return $contenu;
	}
	
	function widget( $args, $instance ) {
		
		// Needed URL
		global $pluginURL;
		
		// If no API Key entered
		if(get_option("lastfm_api","no")=="no"){
			_e("No API key entered, you have to configure LastFM Settings");
		}
		
		// Let's Go,
		extract( $args );
		// Extracting settings
		$title = apply_filters('widget_title', $instance['title'] );
		$username = $instance['user_id'];
		$limit = $instance['limit'];
		$api_key = $instance['api_key'];
		$type = $instance['type'];
		
		// Begining the dirty job
		$xml = $this->getXML("http://ws.audioscrobbler.com/2.0/?method=user.get$type&limit=$limit&user=$username&api_key=".get_option("lastfm_api"),$username,$type);
		if($xml==""){ return false; } else{ $xml = new simpleXMLElement($xml); };	

		// Before widget (defined by themes).
		echo $before_widget;
		echo '<div class="LastFM">';
		// Widget Title
		if ( $title )
			echo $before_title . $title . $after_title;
		// The funny part begins
		$i = 0;
		// Debug to check the XML format
		//echo "<pre>".print_r($xml,true)."</pre>";
		echo '<div class="tracks">';
		$nbTracks = count($xml->$type->track);
		foreach($xml->$type->track as $v){
			// Tracks Info
			$artist = ""; $track= ""; $album="";
			if(isset($v->artist)){ $artist = $v->artist; }		if(isset($v->artist->name)){ $artist = $v->artist->name; }
			if(isset($v->name)){ $track = $v->name; }
			if(isset($v->album)){ $album = $v->album; }
		?>
			      <div class="track slide" id="slide<?php echo $i+1; ?>">
				  <div class="CDCase">
				    <img src="
				    <?php
				    if(!empty($v->image[2])){
					echo $v->image[2];
				    }
				    else{
					echo $pluginURL."/lastfm/theme/nocover.png";
				    }
				    ?>
				    "/>
				    <div class="case"></div>
				    <?php if($i!=0){ ?><div class="next"></div> <?php } ?>
				    <?php if($i!=$nbTracks-1){ ?><div class="prev"></div> <?php } ?>
				  </div>
				  <div class="info">
					<div class="piste"><?php echo $track; ?></div>
					<div class="artiste"><?php echo $artist; ?></div>
					<div class="album"><?php echo $album; ?></div>
					<div class="time">
					      <?php if(isset($v->date)){ ?>
						  <?php
						  $decal = (substr(date("O"),0,1) == "-" ? -1 : +1) * (int)substr(date("O"),-4,4)/100;
						  $duree = abs(time()-strtotime($v->date)-$decal*3600);
						  if($duree<60){
							$unit = $duree==1 ? __("seconde","lastfm") : __("secondes","lastfm");
						  }
						  else if( $duree<=3600 ){
							$duree= round($duree/60);
							$unit = $duree==1 ? __("minute","lastfm") : __("minutes","lastfm");
						  }
						  else if( $duree<=86400 ){
							$duree= round($duree/3600);
							$unit = $duree==1 ? __("hour","lastfm") : __("hours","lastfm");
						  }
						  else{
							$duree= round($duree/86400);
							$unit = $duree==1 ? __("day","lastfm") : __("days","lastfm");
						  }
						  printf(__("%d %s ago","lastfm"),$duree,$unit);
					      } ?>
					</div>
				  </div>
				  <div class="clear"></div>
			      </div>
		<?php
		  $i++;
		}
		echo "</div>";
		echo "</div>";
		echo $after_widget;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['user_id'] = $new_instance['user_id'];
		$instance['limit'] = $new_instance['limit'];
		$instance['api_key'] = $new_instance['api_key'];
		$instance['type'] = $new_instance['type'];

		return $instance;
	}
	
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => 'LastFM', "limit"=>5 );
		$instance = wp_parse_args( (array) $instance, $defaults );
		$options = array(
			__("Recent tracks","lastfm") => "recenttracks",
			__("Loved tracks","lastfm") => "lovedtracks",
			__("Top tracks","lastfm") => "toptracks",
		)
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e("Title","lastfm"); ?> :</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'user_id' ); ?>"><?php _e("LastFM username","lastfm"); ?> :</label>
			<input id="<?php echo $this->get_field_id( 'user_id' ); ?>" name="<?php echo $this->get_field_name( 'user_id' ); ?>" value="<?php echo $instance['user_id']; ?>" style="width:80%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e("How many Tracks","lastfm"); ?> :</label>
			<input id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php echo $instance['limit']; ?>" style="width:2em;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e("What do you want to display ?","lastfm"); ?></label>
			<select id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>">
					<?php foreach($options as $o=>$v): ?>
						<option value="<?php echo $v ?>" <?php if($instance['type']==$v) echo 'selected="selected"'; ?>><?php echo $o; ?></option>
					<?php endforeach; ?>
			</select>
		</p>
	<?php
	}
}