

/*
 * Handles call and return of
 * AJAX request
 */
function Ajax(arguments, success, failure) {
	this.xhr = null;
	this.sendingData = false;
	this.dataType = null;
	
	// check to see if there's post data
	if (arguments.data != null) {
		this.sendingData = true;

		// set data type
		if(arguments.dataType) // user has passed it in
			this.dataType = arguments.dataType;
		else {
			switch (typeof arguments.data) {
				case 'object':
					this.dataType = 'JSON';
					break;
				case 'string':
					this.dataType = 'string';
					break;
			}
		}
	}

	if (window.XMLHttpRequest)
	{
		this.xhr = new XMLHttpRequest();
	}
	else
	{ // IE6, IE5
		this.xhr = new ActiveXObject("Microsoft.XMLHTTP");
	}

	this.xhr.open(arguments.requestType, arguments.requestUrl, + arguments.requestParameters, true);

	if (this.dataType === 'JSON') {
		this.xhr.setRequestHeader("Content-type", "application/json");
	}
	
	else {
		this.xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	}

	this.xhr.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			requestedData = this.responseText;
			success(requestedData);
		}
		else {
			failure (this.responseText, this.status)
		}
	};

	this.data = arguments.data;

	this.send = function() {
		if (this.sendingData) {
			if (this.dataType === 'JSON')
				this.xhr.send(JSON.stringify(this.data));
			else
				this.xhr.send(this.data);
		}
		else
			this.xhr.send();
	}

	if (arguments.sendNow == true) {
		this.send();
	}
}

/*
var ajaxRequest = new Ajax({
	requestType: 'POST',
    	requestUrl: 'test_return.php',
    	requestParameters: ' ',
    	data: 'key=value',
	dataType: 'string',
    	sendNow: true
	}, function(data) {
		alert("It works!");
	});

	*/
//ajaxRequest.send();
