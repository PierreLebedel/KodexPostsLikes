jQuery(function($){

if( $('#kodex-posts-likes').length ){
	
	$('#kodex-posts-likes .handlediv').mouseenter(function(){
		$('#kodex-posts-likes h3.hndle').addClass('hover');
	}).mouseleave(function(){
		$('#kodex-posts-likes h3.hndle').removeClass('hover');
	});

	$('#kodex-posts-likes h3.hndle').append('<a href="'+kodex_posts_likes.settings+'" class="dashicons dashicons-admin-generic cdx_settings" title="Settings"></a>');
	
}

});