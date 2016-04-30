jQuery(function($){

	$('body').on('click', '.kodex_buttons .kodex_button', function(e){
		e.preventDefault();

		var button     = $(this);
		var wrapper    = button.parents('.kodex_buttons');
		//var buttons    = wrapper.find('.cdx_button');
		var post_id    = button.attr('data-id');
		var btn_action = button.attr('data-action');
		var nonce      = wrapper.find('input[name="nonce"]').val();

		button.addClass('kodex_button_loading');
		var xhr = $.ajax({
			url    : kodex_posts_likes.ajaxurl, 
			method : 'post',
			data   : {
				action     : 'kodex_posts_likes_ajax',
				post_id    : post_id,
				nonce      : nonce,
				btn_action : btn_action
			}, 
			success : function(response){
				button.removeClass('kodex_button_loading');
				var wrappers = $('.kodex_button[data-id="'+post_id+'"]').parents('.kodex_buttons').html(response);
			}
		});
	});

});