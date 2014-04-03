var wallDiv = document.getElementById("post-wall");
wallDiv.appendChild();

// Top of event wall

// People post
var post = "<li></li>";

loadPostData();
function loadPostData() {
	// Create ajax request
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status)
		{
			// grab all the posts
			//
			var posts = JSON.parse(xmlhttp.responseText);
		}
	};

	xmlhttp.open("POST", "network-post-data.php", true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send("id="+id);
}
