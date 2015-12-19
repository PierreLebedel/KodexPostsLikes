<div id="kodex_posts_likes_doc">
	
	<div class="postbox">
		<h3 class="hndle"><span><?php _e("Shortcode:", 'kodex'); ?> <?php _e("Display the buttons", 'kodex'); ?></span></h3>
		<div class="inside">
			<p><?php _e("Inside the loop:", 'kodex'); ?> </p>
			<p><code>[kodex_post_like_buttons]</code></p>
			<p><?php _e("Outside the loop:", 'kodex'); ?> </p>
			<p><code>[kodex_post_like_buttons postid="1"]</code></p>
			<p><?php _e("Displayed text inside the buttons can be changed by adding these attributes:", 'kodex'); ?></p>
			<p><code>[kodex_post_like_buttons liketext="Lol" disliketext="Arf"]</code></p>
		</div>
	</div>

	<div class="postbox">
		<h3 class="hndle"><span><?php _e("Shortcode:", 'kodex'); ?> <?php _e("Display the counter", 'kodex'); ?></span></h3>
		<div class="inside">
			<p><?php _e("Inside the loop:", 'kodex'); ?> </p>
			<p><code>[kodex_post_like_count]</code></p>
			<p><?php _e("Outside the loop:", 'kodex'); ?> </p>
			<p><code>[kodex_post_like_count postid="1" format="html|number"]</code></p>
		</div>
	</div>


	<div class="postbox">
		<h3 class="hndle"><span><?php _e("Custom WP_Query", 'kodex'); ?></span></h3>
		<div class="inside">
			<p><?php _e("Ordering posts by descending likes count in a custom query in your theme:", 'kodex'); ?></p>
<pre>$custom_query = new WP_Query(array(
	'post_type' => array('post'),
	'meta_key'  => 'kodex_post_likes_count',
	'orderby'   => 'meta_value_num',
	'order'     => 'DESC'
));</pre>
		</div>
	</div>

</div>