jQuery(function($){

if( $('#kodex-posts-likes').length ){
	
	$('#kodex-posts-likes .handlediv').mouseenter(function(){
		$('#kodex-posts-likes h3.hndle').addClass('hover');
	}).mouseleave(function(){
		$('#kodex-posts-likes h3.hndle').removeClass('hover');
	});

	$('#kodex-posts-likes h3.hndle').append('<a href="'+kodex_posts_likes.settings+'" class="dashicons dashicons-admin-generic kodex_settings" title="Settings"></a>');
	
}

if( $('#kodex_likes_dashboard').length ){
	

	$('#kodex_likes_dashboard .handlediv').mouseenter(function(){
		$('#kodex_likes_dashboard h2.hndle').addClass('hover');
	}).mouseleave(function(){
		$('#kodex_likes_dashboard h2.hndle').removeClass('hover');
	});

	$('#kodex_likes_dashboard h2.hndle').append('<a href="#" class="dashicons dashicons-admin-generic kodex_dashboard_settings" title="Settings"></a>');

	$('.kodex_dashboard_settings').click(function(e){
		e.preventDefault();
		$('#kodex_dashboard_settings').slideToggle(200);
	});

	$('#kodex_likes_dashboard').on('submit', '#kodex_dashboard_settings', function(e){
		e.preventDefault();
		var form     = $(this);
		var ajaxurl  = form.attr('action');
		var postdata = form.serialize();
		var number   = form.find('input[type="number"]').val();

	    var xhr = $.ajax({
			url: ajaxurl,
			method: 'post',
			data: postdata, 
			success: function(response){
				$('#kodex_likes_dashboard .inside').html(response);
				$('#kodex_likes_dashboard h2.hndle em').text(number);
			}
		});
	});

	$('#kodex_likes_dashboard').on('click', '#kodex_dashboard_more button', function(e){
		e.preventDefault();
		$('#kodex_likes_dashboard table tr.hidden').removeClass('hidden');
		$('#kodex_dashboard_more').remove();
	});
	
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