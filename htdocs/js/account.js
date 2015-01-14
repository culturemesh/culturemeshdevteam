
// An ajax call to sign_out.php
// no big deal
/*
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
			// will probably change this soon,
			// 	USERS WILL BE ABLE TO SEE EACH OTHER's
			// 	PROFILES, SO NO REDIRECT AT LOGOFF
			    if (document.URL.indexOf("profile") != -1)
				{ window.location.assign(cm.home_path + "/index.php?signout=true"); }

			    $("#signout_panel").show();
			    $("#signout_panel").children('p').text('You have successfully signed out.');
			    $("#signout_panel").fadeOut(5000);
		    	    $("#login-link").show();
                            $("#register-link").show();
                            $("#welcome").hide();
                            $("#sign-out").hide();
                            $(".member").hide();
			    $(".personal").hide();
                            $(".reg-guest").hide();
                            $(".guest").show();
		  }
	  }
	  
	  xmlhttp.open("GET", cm.home_path + "/sign_out.php", true);
	  xmlhttp.send();
}
*/
