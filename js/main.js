/** Javascript functions go in here **/
var slidenum = 0;
var next;
var interval;

$(document).ready(function() {
	/*child_delete_clicked();
	inst_delete_clicked();*/
	featured_button_click();
	interval = setInterval(function() {
		  advance_slide(slidenum);
		  }, 5000);
});
/*
function child_delete_clicked(){
	$('[name="delete_child"]').click(function(){

		$('form#child-update').submit(function() {
			var answer = confirm('Are you sure you wish to delete this child?');
			
			if(answer){
				return true;
			}
			else{
				return false;
			}
		});
		
		$('[name="delete_child"]').unbind('click');
		$('form#child-update').unbind('submit');
	});
}

function inst_delete_clicked(){
	$('[name="inst_cancel"]').click(function(){

		$('form#inst-cancel').submit(function() {
			var answer = confirm('Are you sure you wish to cancel this rental?');
			
			if(answer){
				alert('We will contact you shortly. Please return your instrument to the store.');
				return true;
			}
			else{
				return false;
			}
		});
		$('[name="inst_cancel"]').unbind('click');
	});
}
*/
function advance_slide(snum){
	slidenum++;
	var active = $("div.featured-slide.slide-active");
	if(slidenum > 3) {
		next = $("div.featured-slide").first();
		slidenum = 0;
	} else {
	next = $("div.featured-slide").eq(slidenum); }
	button_advance(slidenum);
	active.addClass("slide-last-active");
	next.css({opacity: 0}).addClass("slide-active").animate({opacity: 1}, 1000, function() {
		active.removeClass('slide-active slide-last-active'); });
}

function click_advance_slide(snum){
	var active = $("div.featured-slide.slide-active");
	next = $("div.featured-slide").eq(snum);
	button_advance(snum);
	active.addClass("slide-last-active");
	next.css({opacity: 0}).addClass("slide-active").animate({opacity: 1}, 1000, function() {
		active.removeClass('slide-active slide-last-active'); });
	slidenum = snum;
}

function button_advance(snum){
	$(".featured-buttons li.button-active").removeClass("button-active");
	$(".featured-buttons li").eq(snum).addClass("button-active");
}

function featured_button_click(){
	$(".featured-buttons li").click(function(){
		if($(".slide-active:animated").length == 0){
		var index = $(".featured-buttons li").index(this);
		click_advance_slide(index);
		clearInterval(interval);
			interval = setInterval(function() {
				advance_slide(slidenum);
				}, 5000);
		}
	});
}