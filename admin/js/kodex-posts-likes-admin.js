jQuery(function($){

if( $('#kodex-posts-likes').length ){
	
	$('#kodex-posts-likes .handlediv').mouseenter(function(){
		$('#kodex-posts-likes h3.hndle').addClass('hover');
	}).mouseleave(function(){
		$('#kodex-posts-likes h3.hndle').removeClass('hover');
	});

	$('#kodex-posts-likes h3.hndle').append('<a href="'+kodex_posts_likes.settings+'" class="dashicons dashicons-admin-generic cdx_settings" title="Settings"></a>');
	
}

if( $('#kodex_demo_buttons').length ){
	
	var xhr = $.ajax({
		url: kodex_posts_likes.ws+'kpl_buttons/',
		method: 'post',
		data: {}, 
		success: function(response){
			var data = $.parseJSON(response);
			console.log(data);
			$('#kodex_demo_buttons .kodex_like_button .counter').text(data.likes);
			$('#kodex_demo_buttons .kodex_dislike_button .counter').text(data.dislikes);

			$('#kodex_demo_buttons .kodex_like_button').removeClass('kodex_button_active');
			if(data.like_active) $('#kodex_demo_buttons .kodex_like_button').addClass('kodex_button_active');

			$('#kodex_demo_buttons .kodex_dislike_button').removeClass('kodex_button_active');
			if(data.dislike_active) $('#kodex_demo_buttons .kodex_dislike_button').addClass('kodex_button_active');
		}
	});


	$('#kodex_demo_buttons button').click(function(e){
		e.preventDefault();
		var button = $(this);
		var btn_action = (button.is('.kodex_dislike_button')) ? 'dislike' : 'like';
		var xhr = $.ajax({
			url: kodex_posts_likes.ws+'kpl_vote/'+btn_action, 
			method: 'post',
			data: {}, 
			success: function(response){
				var data = $.parseJSON(response);
				console.log(data);
				$('#kodex_demo_buttons .kodex_like_button .counter').text(data.likes);
				$('#kodex_demo_buttons .kodex_dislike_button .counter').text(data.dislikes);

				$('#kodex_demo_buttons .kodex_like_button').removeClass('kodex_button_active');
				if(data.like_active) $('#kodex_demo_buttons .kodex_like_button').addClass('kodex_button_active');

				$('#kodex_demo_buttons .kodex_dislike_button').removeClass('kodex_button_active');
				if(data.dislike_active) $('#kodex_demo_buttons .kodex_dislike_button').addClass('kodex_button_active');
			}
		});
	});
}

});