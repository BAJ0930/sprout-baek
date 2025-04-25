/***************************/
//@Author: Adrian "yEnS" Mato Gondelle
//@website: www.yensdesign.com
//@email: yensamg@gmail.com
//@license: Feel free to use it, but keep this credits please!					
/***************************/

//SETTING UP OUR POPUP
//0 means disabled; 1 means enabled;
var popupStatus = 0;

//loading popup with jQuery magic!
function loadPopup(){
	
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.3"
		});
		//$("#backgroundPopup").fadeIn("slow");
		//$("#popupContact").fadeIn("slow");
		$("#backgroundPopup").fadeIn(0);
		$("#popupContact").fadeIn(0);
	}
	popupStatus = 1;
}

//disabling popup with jQuery magic!
function disablePopup(){
	//disables popup only if it is enabled
	if(popupStatus==1){
		$("#backgroundPopup").fadeOut(0);
		$("#popupContact").fadeOut(0);
		
		$("SELECT").css("visibility","visible");
		popupStatus = 0;
	}
}

//centering popup
function centerPopup(){
	var windowWidth = document.documentElement.scrollWidth;
	var windowHeight = document.documentElement.scrollHeight;
	var popupWidth = $("#popupContact").width();
		
	var t = 0;
	if(document.body.scrollTop) t = document.body.scrollTop;
	else if(document.documentElement.scrollTop) t = document.documentElement.scrollTop;
	
	if(t<50) t = 50;
	t = t + 10;

	var rightMarginValue = (popupWidth/2*-1) + "px";
	
	$("#popupContact").css("position","fixed");
	$("#popupContact").css("top",t);
	$("#popupContact").css("left","50%");
	$("#popupContact").css("margin","0 0 0 " + rightMarginValue);

	//only need force for IE6
	$("#backgroundPopup").css({
		"height": windowHeight
	});
}

//CONTROLLING EVENTS IN jQuery
$(document).ready(function(){

	//Click out event!
	/*$("#backgroundPopup").click(function(){
		disablePopup();
	});*/

});