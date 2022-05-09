// custom-scripts.js

$(function() {
	$('a.disabled').click(function(e) {
		e.preventDefault();
	});
	
	$('a.acct-btn').click(function(e) {
		e.preventDefault();
		$(this).toggleClass('active');
		$('ul#account-menu').toggleClass('active');
	});
	
	$('a.quick-view-profile').click(function(e) {
		e.preventDefault();
		$('ul#account-menu').removeClass('active');
		$('div#profile-quick-view').addClass('active');
	});
	
	$('a.menu-close').click(function(e) {
		e.preventDefault();
		$('a.acct-btn, .header-menu').removeClass('active');
	});
	
	$('a.modal-close').click(function(e) {
		e.preventDefault();
		$('.modal, #shader').removeClass('active');
	});
	
	$('a.video-link').click(function(e) {
		e.preventDefault();
		var thisVideo = $(this).attr('href');
		
		$('#video-container, #shader').addClass('active');
			
		var thisPlayer = videojs('#speaker-video');
			thisPlayer.poster('assets/' + thisVideo + '.png');
			thisPlayer.src({
			  type: 'video/mp4',
			  src: 'assets/' + thisVideo + '.mp4'
			});
	});
	
	$('a.video-close').click(function(e) {
		e.preventDefault();
		videojs('#speaker-video').pause();
		$('#video-container, #shader').removeClass('active');
	})
});