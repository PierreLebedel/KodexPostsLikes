<?php

//$this->debug($this);

?><div class="wrap">
	<h2><?php echo $this->plugin_title; ?></h2>

	<?php echo $this->message; ?>

	<div id="poststuff">
		<div id="kodex_posts_likes_doc">
			<div class="postbox">
				<h3 class="hndle"><span><?php _e("Documentation", 'kodex'); ?></span></h3>
				<div class="inside">
					<h4><?php _e("Display the buttons", 'kodex'); ?></h4>
					<p><code>[kodex_post_like_buttons]</code></p>

					<h4><?php _e("Display the counter", 'kodex'); ?></h4>
					<p><code>[kodex_post_like_count]</code></p>
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
			<div class="postbox">
				<h3 class="hndle"><span><?php _e("Like this plugin?", 'kodex'); ?></span></h3>
				<div class="inside">
					<p><?php _e("Do not hesitate to click on Like, it will make me happy!", 'kodex'); ?></p>
					<div id="kodex_demo_buttons" class="kodex_buttons">
						<button type="button" class="kodex_like_button"><span class="icon"></span><span class="text">Like</span><span class="counter"></span></button>
						<button type="button" class="kodex_dislike_button"><span class="icon"></span><span class="counter"></span></button>
					</div>
				</div>
			</div>
			<div class="postbox">
				<h3 class="hndle"><span><?php _e("Hey!", 'kodex'); ?></span></h3>
				<div class="inside">
					<p><?php _e("For more ressources and snippets, vitit us at", 'kodex'); ?> <a href="http://kodex.pierros.fr" target="_blank">Codex.Pierros.fr</a></p>
				</div>
			</div>
		</div>

		<form action="<?php echo $this->settings_url; ?>" method="post" id="kodex_posts_likes_form">
			<table class="widefat">
			<thead>
				<tr>
					<th colspan="2"><b><?php _e('Options', 'kodex'); ?></b></th>
				</tr>
			</thead>
			<tbody>
				<?php $i=0;
				//$this->debug($this->options);
				foreach($this->options as $k=>$object):
					$i++;
					$class = ($i%2) ? ' alternate' : ''; 

					if(!isset($this->defaults[$k])) continue;
					$object = (object) $this->defaults[$k];
					$value = $this->get_option($k);

					?><tr class="<?php echo $class; ?>">
						<td><?php echo $object->label; ?></td>
						<td>
							<?php if($object->type=='checkbox'): ?>
							<input type="hidden" name="<?php echo $this->plugin_name; ?>[<?php echo $k; ?>]" value="0">
							<input type="checkbox" name="<?php echo $this->plugin_name; ?>[<?php echo $k; ?>]" value="1" <?php echo($value)?'checked':''; ?>>
							
							<?php elseif($object->type=='text'): ?>
							<input type="text" name="<?php echo $this->plugin_name; ?>[<?php echo $k; ?>]" value="<?php echo esc_attr($value); ?>">
							
							<?php elseif($object->type=='array'):
								foreach($object->maybe as $p): ?>
									<label>
										<input type="checkbox" name="<?php echo $this->plugin_name; ?>[<?php echo $k; ?>][]" value="<?php echo esc_attr($p); ?>" <?php echo(in_array($p, $value))?'checked':''; ?>>
										<?php echo $p; ?>
									</label><br>
								<?php endforeach; ?>

							<?php elseif($object->type=='select'): ?>
								<select name="<?php echo $this->plugin_name; ?>[<?php echo $k; ?>]">
								<?php foreach($object->maybe as $k=>$v): ?>
									<option value="<?php echo esc_attr($k); ?>" <?php echo($k==$value)?'selected':''; ?>><?php echo $v; ?></option>
									</label><br>
								<?php endforeach; ?>
								</select>
						
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
			</table>

			<br><button type="submit" class="button button-primary"><?php _e("Save", 'kodex'); ?></button>
		</form>
	</div>



</div>