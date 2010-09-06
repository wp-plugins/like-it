jQuery(function($) {
	$('.likeit-canvote .likeit-text').click(function() {
		var $clicked = $(this);
		$.post(likeit.ajaxurl, {
			id: $clicked.attr('id').split('_')[1],
			action: 'likeit_register_vote'
		}, function(data) {
			$clicked.parent().find('.likeit-count span').fadeOut(350, function() {
				$(this).text(data).show();
			});
		});
	});
});