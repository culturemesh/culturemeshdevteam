/*
 * <li class='network-post'>
 * 	<div class='post-img'>
 * 		<img id='profile-post' src='images/blank_profile.png' width= 
 * 	</div>
 * 	<div class='post-info'>
 * 		<h5 class='h-network'>
 * 		<p class='network'>
 * 		<a class'network member'>
 * 	</div>
 * 	<div class='post-replies'>
 * 	</div>
 * 	<div class='clear'></div>
 * </li>
*/
/*
	<form method="POST" class="member" action="network_post.php">
		<img id="profile-post" src="images/blank_profile.png" width="45" height="45">
		<textarea class="post-text" name="post_text" placeholder="Post something..."></textarea>
		<div class="clear"></div>
		<input type="submit" class="network" value="Send"></input>
	</form>
*/
/*
var wallDiv = document.getElementById("post-wall-ul");
wallDiv.appendChild();

// Top of event wall

// People post
var post = "<li></li>";

function addElement() {
  var ni = document.getElementById('myDiv');
  var numi = document.getElementById('theValue');
  var num = (document.getElementById('theValue').value -1)+ 2;
  numi.value = num;
  var newdiv = document.createElement('div');
  var divIdName = 'my'+num+'Div';
  newdiv.setAttribute('id',divIdName);
  newdiv.innerHTML = 'Element Number '+num+' has been added! <a href=\'#\' onclick=\'removeElement('+divIdName+')\'>Remove the div "'+divIdName+'"</a>';
  ni.appendChild(newdiv);
}
*/

function createPost(data) {
	// create li
	var post = document.createElement('li');
	var postClass = 'network-post ' + data["post_class"];
	
	post.setAttribute('class', postClass);

	// create divs (image, post, clear)
	var imgDiv = document.createElement('div');
	imgDiv.setAttribute('class', 'post-img');
	post.appendChild(imgDiv);

	var postDiv = document.createElement('div');
	postDiv.setAttribute('class', 'post-info');
	post.appendChild(postDiv);

	var replyDiv = document.createElement('div');
	replyDiv.setAttribute('class', 'post-replies');
	post.appendChild(replyDiv);

	var clearDiv = document.createElement('div');
	clearDiv.setAttribute('class', 'clear');
	post.appendChild(clearDiv);

	// set up image div
	var imgTag = document.createElement('img');
	imgTag.setAttribute('class', 'profile-post');
	imgTag.src = 'images/blank_profile.png';
	imgTag.width = '45';
	imgTag.height = '45';
	imgDiv.appendChild(imgTag);

	// set up post div
	var postHeader = document.createElement('h5');
	postHeader.setAttribute('class', 'h-network');
	postHeader.innerHTML = data['email'];
	var postText = document.createElement('p');
	postText.setAttribute('class', 'network');
	postText.innerHTML = data['post_text'];
	postDiv.appendChild(postHeader);
	postDiv.appendChild(postText);

	return post;
}

function createParent(data, index) {
	// create li
	var post = document.createElement('li');
	var postClass = 'network-post ' + data[index]["post_class"];
	
	post.setAttribute('class', postClass);

	// create divs (image, post, reply, clear)
	var imgDiv = document.createElement('div');
	imgDiv.setAttribute('class', 'post-img');
	post.appendChild(imgDiv);

	var postDiv = document.createElement('div');
	postDiv.setAttribute('class', 'post-info');
	post.appendChild(postDiv);

	var replyDiv = document.createElement('div');
	replyDiv.setAttribute('class', 'post-replies');
	post.appendChild(replyDiv);

	var clearDiv = document.createElement('div');
	clearDiv.setAttribute('class', 'clear');
	post.appendChild(clearDiv);

	// set up image div
	var imgTag = document.createElement('img');
	imgTag.setAttribute('class', 'profile-post');
	imgTag.src = 'images/blank_profile.png';
	imgTag.width = '45';
	imgTag.height = '45';
	imgDiv.appendChild(imgTag);

	// set up post div
	var postHeader = document.createElement('h5');
	postHeader.setAttribute('class', 'h-network');
	postHeader.innerHTML = data[index]['email'];
	var postText = document.createElement('p');
	postText.setAttribute('class', 'network');
	postText.innerHTML = data[index]['post_text'];
	postDiv.appendChild(postHeader);
	postDiv.appendChild(postText);

	// set up reply div
	var replyButton = document.createElement('button');
	var replyViewer = document.createElement('div');
	var replyBox = document.createElement('div');
	
	// get lis for replies
	var replyUl = document.createElement('ul');
	var replyCount = 0;
	for (var i = 0; i < data.length; i++)
	{
		if (i == index)
		  { continue; }
		if ( data[i]['post_original'] == data[index]['id'])
		  {
			var newPost = createPost(data[i]);
			replyUl.appendChild(newPost);
			replyCount++;
		  }
	}

	replyUl.setAttribute('class', 'post-wall-ul child');
	// create a little div to tell how many replies you have
	replyButton.innerHTML = "Reply to this post";
	replyViewer.innerHTML += "See all " + replyCount + " replies";
	replyViewer.onclick = function() {
		if (replyUl.style.display == "none")
		  { replyUl.style.display = "block"; }
		else
		  { replyUl.style.display = "none"; }
	}

	// set up reply box
	var replyForm = document.createElement('form');
	var replyTxtArea = document.createElement('textarea');
	var replySbmt = document.createElement('button');
	var parentId = document.createElement('input');
	var classInput = document.createElement('input');

	replySbmt.onclick = function() {
		
	}

	// toggle the appearance of the reply box
	replyButton.onclick = function() {
		if (replyBox.style.display == "none")
		  { replyBox.style.display = "block"; }
		else
		  { replyBox.style.display = "none"; }
	}
	replyBox.style.display = "none";

	// add attributes
	replyTxtArea.name = 'post_text';

	// add a hidden input
	parentId.type = 'hidden';
	parentId.value = data[index]['id'];
	parentId.name = 'post_original';
	classInput.type =  'hidden';
	classInput.value = 'r';
	classInput.name = 'post_class';

	// add all the reply box children
	replyForm.appendChild(replyTxtArea);
	replyForm.appendChild(replySbmt);
	replyForm.appendChild(parentId);
	replyForm.appendChild(classInput);
	replyForm.setAttribute('action', "network_post.php");
	replyForm.setAttribute('method', "POST");
	replyBox.appendChild(replyForm);

	replyDiv.appendChild(replyViewer);
	replyDiv.appendChild(replyButton);
	replyDiv.appendChild(replyBox);
	replyDiv.appendChild(replyUl);
	return post;
}

function submitPost() {
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status)
		{

		}
	}

	xmlhttp.open("POST", "network_post.php", true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send("id="+id);
}
//loadPostData();
function loadPostData(id, callback) {
	// Create ajax request
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status)
		{
			// grab all the posts
			//
			var posts = JSON.parse(xmlhttp.responseText);
			callback.apply(this, [posts]);
		}
		else {}
	};

	// grab all the relevant data
	//var text = document.getElementById();
	xmlhttp.open("POST", "network-post-data.php", true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send("id="+id);
}
