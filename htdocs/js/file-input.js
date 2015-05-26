// THE ESSENTIAL PLAYERS
var input = document.getElementById("upload-input");
var upId = document.getElementById("upload-id");
var uploadForm = document.getElementById("profile-pic-upload");
var uploadDiv = document.getElementById("pic-upload-div");
var toggle = document.getElementById("pic-upload-toggle");
var successLabel = document.getElementById('success-label');

// create replacement elements
var promptButton = document.createElement('button');
var promptLabel = document.createElement('p');
var submitButton = document.createElement('input')

promptButton.innerHTML = "Browse...:)";
promptButton.setAttribute('class', 'upload');
submitButton.setAttribute('type', 'submit');
submitButton.setAttribute('value', 'Upload File');
submitButton.setAttribute('class', 'upload');
submitButton.value = 'Upload File';
submitButton.style.display = 'none';
promptLabel.setAttribute('class', 'upload');
uploadForm.appendChild(promptButton);
uploadForm.appendChild(promptLabel);
uploadForm.appendChild(submitButton);

// simulate a mouse event to pass to function

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
	if (input.value != null)
	   submitButton.style.display = 'inline';
}

// toggles the visibility of uploadDiv
function uploadToggle() {
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

// onclick, toggle div
toggle.onclick = function(e) {
	e.preventDefault();
	uploadToggle();
}

// SUBMIT IMAGE TO SERVER
uploadForm.onsubmit = function (e) {
	// prevent regular form submit
	e.preventDefault();
	
	// keep user up to date
	submitButton.innerHTML = 'Submitting';

	// get the files from input
	var files = input.files;

	// create FormData object
	var formData = new FormData(uploadForm);
	formData.append('ajax', true);

	// user may only upload one file at a time
	if (files.length > 1) {
		alert('Only one file at a time');
		return -1;
	}

	var file = files[0];
	
	// check file type
	//if (!file.type.match('
	
	// add all the stuff to form data
	//formData.append('picfile', file, file.name);
	//formData.append(upId.name, upId.value);
	//alert(formData);

	// create ajax request
	var fileUpload = new Ajax({
		requestType: 'POST',
		requestUrl: cm.home_path + '/profile_img_upload.php',
		requestParameters: ' ',
		data: formData,
		dataType: 'FormData',
	    	sendNow: true
		}, function(data) {
			// success function
			var response = JSON.parse(data);
			if (response['success'] == true) {
				// update successLabel
				successLabel.innerHTML = "File uploaded successfully!";
				var myImages = document.getElementsByClassName('usr_image');

				// try and reload all the images
				for (var i = 0; i < myImages.length; i++)
					myImages[i].src = myImages[i].src + "#" + new Date().getTime(); // add datetime to force browser to reload image

				// disappear upload div
				// additionally: booger
				//uploadToggle();
				location.reload();
			} 
			else {
				successLabel.innerHTML = response['error'];
			}
		}, function(response, rStatus) {
			// failure function
			successLabel.innerHTML = "There was a problem on our end. Try again later";
		});
}
