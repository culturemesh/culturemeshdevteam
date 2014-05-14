// THE ESSENTIAL PLAYERS
var input = document.getElementById("upload-input");
var uploadForm = document.getElementById("profile-pic-upload");
var uploadDiv = document.getElementById("pic-upload-div");
var toggle = document.getElementById("pic-upload-toggle");

// create replacement elements
var promptButton = document.createElement('button');
var promptLabel = document.createElement('p');
var submitButton = document.createElement('input')

promptButton.innerHTML = "Browse...:)";
promptButton.setAttribute('class', 'upload');
submitButton.setAttribute('type', 'submit');
submitButton.setAttribute('value', 'Load File');
submitButton.setAttribute('class', 'upload');
submitButton.value = 'Load File';
promptLabel.setAttribute('class', 'upload');
uploadForm.appendChild(promptButton);
uploadForm.appendChild(promptLabel);
uploadForm.appendChild(submitButton);

// simulate a mouse event to pass to function

// create ajax request
var fileUpload = new Ajax({
	requestType: 'POST',
    	requestUrl: 'profile_img_upload.php',
    	requestParameters: ' ',
    	data: ''
	}, function(data) {

	});
/*
clickEvent = new MouseEvent("click", {
	canBubble:true,
	cancelable:true,
	view:window});
*/

var clickEvent = document.createEvent('MouseEvent');
clickEvent.initMouseEvent('click', true, true, window,
	0, 0, 0, 80, 20, false, false, false, false, 0, null);

// get the correct dispatch event function
// 	XBrowser stuff
if (input.dispatchEvent)
	input.cmDispatchEvent = input.dispatchEvent;
else if (input.fireEvent)
	input.cmDispatchEvent = input.fireEvent;

// onclick, activate input file object
promptButton.onclick = function(event) { 
	event.preventDefault();
	input.cmDispatchEvent(clickEvent); 
}

// show file name in label
input.onchange = function() {
	promptLabel.innerHTML = input.value;	
}

// onclick, toggle div
toggle.onclick = function(e) {
	e.preventDefault();

	// do the thing i said it would do
	if (uploadDiv.style.display == "none" ||
			uploadDiv.style.display == "") {
		uploadDiv.style.display = "block";
		toggle.innerHTML = "Never Mind";
	}
	else if (uploadDiv.style.display == "block") {
		uploadDiv.style.display = "none";
		toggle.innerHTML = "Upload Picture";
	}
}
