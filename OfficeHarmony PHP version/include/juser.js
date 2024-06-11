$(function() {
	var url = window.location.href;
	var id= url.substring(url.lastIndexOf("#")+1);
		$("html, body").animate({
			scrollTop: $("#scroll-" + id).offset().top
		},1000);
	var editerror=$(".editerror").text();
	if(editerror!=""){
	 alert(editerror);
	}
});