

/*
 * Handles call and return of
 * AJAX request
 */
function Ajax(arguments, callback) {
	this.xhr = null;
	if (window.XMLHttpRequest)
	{
		this.xhr = new XMLHttpRequest();
	}
	else
	{ // IE6, IE5
		this.xhr = new ActiveXObject("Microsoft.XMLHTTP");
	}

	this.xhr.open(arguments.requestType, arguments.requestUrl, + arguments.requestParameters, true);
	this.xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	//this.xhr.setRequestHeader("Content-type", "application/json");
	this.xhr.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			requestedData = JSON.parse(this.responseText);
			callback(requestedData);
		}
	};
	alert(arguments.data);
	this.xhr.send(arguments.data);
}

var ajaxRequest = new Ajax({
	requestType: 'POST',
    	requestUrl: 'test_return.php',
    	requestParameters: ' ',
    	data: 'stuff=stuff'
	}, function(data) {
		alert("It works!");
	});

ajaxRequest;
