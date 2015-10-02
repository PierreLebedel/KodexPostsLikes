<?php

//$this->debug($this);

global $post_id;

$likes   = get_post_meta($post_id, 'kodex_post_likes_count', true);
$likes_i = ($likes) ? $likes : 0;

$dislikes   = get_post_meta($post_id, 'kodex_post_dislikes_count', true);
$dislikes_i = ($dislikes) ? $dislikes : 0;

?><div style="line-height:22px">
	<table width="100%">
		<tr>
			<td width="50%" align="center">
				<span class="dashicons dashicons-thumbs-up"></span>
				<span><?php echo $this->get_option('like_text'); ?></span>
				<b>&nbsp;<?php echo $likes_i; ?></b>
			</td>
			<td width="50%" align="center">
				<span class="dashicons dashicons-thumbs-down"></span>
				<span><?php echo $this->get_option('dislike_text'); ?></span>
				<b>&nbsp;<?php echo $dislikes_i; ?></b>
			</td>
		</tr>
	</table>
</div>