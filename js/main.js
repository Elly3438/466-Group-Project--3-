/** Javascript functions go in here **/

/** $(document).ready(function() {
	tabs_clicked();
	edit_links_clicked();
});

function tabs_clicked(){
	$('.tabs-click').click(function(){
		var active = $(".tabs-click.active");
		$(this).addClass('active');
		active.removeClass('active');
		
		var oldtab = "#" + active.attr('name');
		var tab = "#" + $(this).attr('name');
		$(oldtab).hide();
		$(tab).show();
	});
}

function edit_links_clicked(){
	$('.edit-info').click(function(){
		var active = $(".tabs-click.active");
		active.removeClass('active');
		
		var oldtab = "#" + active.attr('name');
		var tab = $(this).attr('name');
		$('.tabs-click[name = "' + tab + '"]').addClass('active');
		$(oldtab).hide();
		$("#" + tab).show();
	});
}**/