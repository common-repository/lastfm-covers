<?php
if(isset($_POST["lastfm_hidden"])){
	extract($_POST);
	update_option('lastfm_api',$lastfm_api);
	update_option('lastfm_jquery',isset($lastfm_jquery));
	?>
	<div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>
	<?php
}else{
	$lastfm_api = get_option("lastfm_API");
	$lastfm_jquery = get_option("lastfm_jquery",true);

}
?>

<div class="wrap">
	<?php    echo "<h2>" . __( 'LastFM Configuration',"lastfm" ) . "</h2>"; ?>
			
			<form name="lastfm_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
				<input type="hidden" name="lastfm_hidden" value="Y">
				<p>
					<?php _e("API Key :" , 'lastfm' ); ?>
					<input type="text" name="lastfm_api" value="<?php echo $lastfm_api; ?>" size="40">
					<em><?php printf(__("Get your lastFM API Key %shere%s", 'lastfm' ),'<a href="http://www.lastfm.fr/api/account">','</a>'); ?></em>
				</p>
				<p>
					<label><?php _e("Import jQuery Library ?","lastfm"); ?></label>
					<input type="checkbox" name="lastfm_jquery" <?php if($lastfm_jquery){?> checked="checked" <?php } ?>/>
				</p>
				
				<p class="submit">
					<input type="submit" name="Submit" value="<?php _e('Update Options', 'lastfm' ) ?>" />
				</p>
			</form>
</div>