var cm = cm || {};

cm.Autoloader = function(func) {

	var prev_load = window.onload;

	if (prev_load == undefined) {
		window.onload = func;
	}
	else {
		window.onload = function() {
			// execute first function
			if (prev_load) {
				prev_load();
			}
			// execute new function
			func();
		}
	}
}

