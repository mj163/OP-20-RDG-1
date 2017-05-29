$(document).ready(function(){	
	$("#post").validationEngine();
	$("#title").addClass("validate[required]");
	$("#content").addClass("validate[required]");
	/* $("iframe#content_ifr").addClass("validate[required]"); */
	$("#wp-content-editor-tools").css("z-index","1");	
	
	
});
