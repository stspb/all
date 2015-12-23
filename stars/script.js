$(function() { 
		//init
		$.each( $(".control[rate!=0]"), function() {
		  $(this).find(".star:lt("+$(this).attr("rate")+")").addClass("star-select");
		});
	
		$(".control .star").hover(
			function(){
				cont=$(this).parent();
				if(!cont.hasClass("fixed")){//if allowed set new value
					ind = $(this).index();
					ind++;
					cont.find(".star:lt("+ind+")").addClass("star-select");
				}
			},
			function(){
				if(!cont.hasClass("fixed")){
					cont.find(".star").removeClass("star-select");
					cont.find(".star:lt("+cont.attr("rate")+")").addClass("star-select");
				}
			}
		);
		
		$(".control .star").click(function(){
			if(!$(this).parent().hasClass("fixed")){
				ind = $(this).index();
				ind++;
				$(this).parent().find(".star:lt("+ind+")").addClass("star-select");
				$(this).parent().attr("rate",ind).addClass("fixed");
			}
		});
});