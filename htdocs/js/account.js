
// An ajax call to sign_out.php
// no big deal
function signOut() {
	var xmlhttp;
	if (window.XMLHttpRequest)
	  {
	  	  xmlhttp=new XMLHttpRequest();
	  }
	else
	  {
	  	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
	  
	  xmlhttp.onreadystatechange=function()
	  {
	  	  if (xmlhttp.readyState==4 && xmlhttp.status==200)
		  {
		    	    $("#login-link").show();
                            $("#register-link").show();
                            $("#welcome").hide();
                            $("#sign-out").hide();
		  }
	  }
	  
	  xmlhttp.open("GET", "sign_out.php", true);
	  xmlhttp.send();
}
