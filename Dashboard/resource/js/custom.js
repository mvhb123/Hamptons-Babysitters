var WindowsSize=function(){
    	var w=$(window).width();
    	console.log(w);
    	if(w >= 992){
    	var newheight = $('.fixed-position').height() +5;
    	var newheight1= $('.fixed-navtab').height()+5;
    	$('.list_margin').css('margin-top', newheight );
    	
    	}
    	
 };
 $(document).ready(function(){
 
 WindowsSize();
 
 }); 
 $(window).resize(function(){WindowsSize();}); 