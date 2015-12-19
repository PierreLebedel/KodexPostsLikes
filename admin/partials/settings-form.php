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

				<?php elseif($object->type=='number'): ?>
					<input type="number" name="<?php echo $this->plugin_name; ?>[<?php echo $k; ?>]" value="<?php echo esc_attr($value); ?>" min="1" max="31" step="1" required>
					
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