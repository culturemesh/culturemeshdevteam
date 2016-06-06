//
// Helper functions
//

var cm = cm || {};

// Guess...
cm.isArray = function( enigma ) {
	if (Object.prototype.toString.call( enigma ) === '[object Array]')
	  return true;
	else
	  return false;
}

cm.isElement = function( enigma ) {
	return enigma.tagName ? true : false;
}

/**
 * Adds all missing properties from second obj to first obj
 */
cm.extend = function(first, second){
    for (var prop in second){
        first[prop] = second[prop];
    }
};

/**
 * Searches for a given element in the array, returns -1 if it is not present.
 * @param {Number} [from] The index at which to begin the search
 */
cm.indexOf = function(arr, elt, from){
    if (arr.indexOf) return arr.indexOf(elt, from);

    from = from || 0;
    var len = arr.length;

    if (from < 0) from += len;

    for (; from < len; from++){
        if (from in arr && arr[from] === elt){
            return from;
        }
    }
    return -1;
};

cm.getUniqueId = (function(){
    var id = 0;
    return function(){ return id++; };
})();

//
// Browsers and platforms detection

cm.ie       = function(){ return navigator.userAgent.indexOf('MSIE') != -1; };
cm.safari   = function(){ return navigator.vendor != undefined && navigator.vendor.indexOf("Apple") != -1; };
cm.chrome   = function(){ return navigator.vendor != undefined && navigator.vendor.indexOf('Google') != -1; };
cm.firefox  = function(){ return (navigator.userAgent.indexOf('Mozilla') != -1 && navigator.vendor != undefined && navigator.vendor == ''); };
cm.windows  = function(){ return navigator.platform == "Win32"; };

//
// Events

/** Returns the function which detaches attached event */
cm.attach = function(element, type, fn){
    if (element.addEventListener){
        element.addEventListener(type, fn, false);
    } else if (element.attachEvent){
        element.attachEvent('on' + type, fn);
    }
    return function() {
      cm.detach(element, type, fn)
    }
};
cm.detach = function(element, type, fn){
    if (element.removeEventListener){
        element.removeEventListener(type, fn, false);
    } else if (element.attachEvent){
        element.detachEvent('on' + type, fn);
    }
};

cm.preventDefault = function(e){
    if (e.preventDefault){
        e.preventDefault();
    } else{
        e.returnValue = false;
    }
};

//
// Node manipulations

/**
 * Insert node a before node b.
 */
cm.insertBefore = function(a, b){
    b.parentNode.insertBefore(a, b);
};
cm.remove = function(element){
    element.parentNode.removeChild(element);
};

cm.contains = function(parent, descendant){
    // compareposition returns false in this case
    if (parent == descendant) return true;

    if (parent.contains){
        return parent.contains(descendant);
    } else {
        return !!(descendant.compareDocumentPosition(parent) & 8);
    }
};

/**
 * Creates and returns element from html string
 * Uses innerHTML to create an element
 */
cm.toElement = (function(){
    var div = document.createElement('div');
    return function(html){
        div.innerHTML = html;
        var element = div.firstChild;
        div.removeChild(element);
        return element;
    };
})();

//
// Node properties and attributes

/**
 * Sets styles for an element.
 * Fixes opacity in IE6-8.
 */
cm.css = function(element, styles){
    if (styles.opacity != null && typeof styles.opacity != 'undefined'){
        if (typeof element.style.opacity != 'string' && typeof(element.filters) != 'undefined'){
            styles.filter = 'alpha(opacity=' + Math.round(100 * styles.opacity) + ')';
        }
    }
    cm.extend(element.style, styles);
};
cm.hasClass = function(element, name){
    var re = new RegExp('(^| )' + name + '( |$)');
    return re.test(element.className);
};
cm.addClass = function(element, name){
    if (!cm.hasClass(element, name)){
        element.className += ' ' + name;
    }
};
cm.removeClass = function(element, name){
    var re = new RegExp('(^| )' + name + '( |$)');
    element.className = element.className.replace(re, ' ').replace(/^\s+|\s+$/g, "");
};
cm.setText = function(element, text){
    element.innerText = text;
    element.textContent = text;
};

//
// Selecting elements

cm.children = function(element){
    var children = [],
    child = element.firstChild;

    while (child){
        if (child.nodeType == 1){
            children.push(child);
        }
        child = child.nextSibling;
    }

    return children;
};

// 
// Getting keys from an object

cm.objectKeys = function(object) {
	var keys = [];

	for(var key in object){
		keys.push(key);
	}

	return keys;
}

cm.getByClass = function(element, className){
    if (element.querySelectorAll){
        return element.querySelectorAll('.' + className);
    }

    var result = [];
    var candidates = element.getElementsByTagName("*");
    var len = candidates.length;

    for (var i = 0; i < len; i++){
        if (cm.hasClass(candidates[i], className)){
            result.push(candidates[i]);
        }
    }
    return result;
};

/**
 * obj2url() takes a json-object as argument and generates
 * a querystring. pretty much like jQuery.param()
 *
 * how to use:
 *
 *    `cm.obj2url({a:'b',c:'d'},'http://any.url/upload?otherParam=value');`
 *
 * will result in:
 *
 *    `http://any.url/upload?otherParam=value&a=b&c=d`
 *
 * @param  Object JSON-Object
 * @param  String current querystring-part
 * @return String encoded querystring
 */
cm.obj2url = function(obj, temp, prefixDone){
    var uristrings = [],
        prefix = '&',
        add = function(nextObj, i){
            var nextTemp = temp
                ? (/\[\]$/.test(temp)) // prevent double-encoding
                   ? temp
                   : temp+'['+i+']'
                : i;
            if ((nextTemp != 'undefined') && (i != 'undefined')) {
                uristrings.push(
                    (typeof nextObj === 'object')
                        ? cm.obj2url(nextObj, nextTemp, true)
                        : (Object.prototype.toString.call(nextObj) === '[object Function]')
                            ? encodeURIComponent(nextTemp) + '=' + encodeURIComponent(nextObj())
                            : encodeURIComponent(nextTemp) + '=' + encodeURIComponent(nextObj)
                );
            }
        };

    if (!prefixDone && temp) {
      prefix = (/\?/.test(temp)) ? (/\?$/.test(temp)) ? '' : '&' : '?';
      uristrings.push(temp);
      uristrings.push(cm.obj2url(obj));
    } else if ((Object.prototype.toString.call(obj) === '[object Array]') && (typeof obj != 'undefined') ) {
        // we wont use a for-in-loop on an array (performance)
        for (var i = 0, len = obj.length; i < len; ++i){
            add(obj[i], i);
        }
    } else if ((typeof obj != 'undefined') && (obj !== null) && (typeof obj === "object")){
        // for anything else but a scalar, we will use for-in-loop
        for (var i in obj){
            add(obj[i], i);
        }
    } else {
        uristrings.push(encodeURIComponent(temp) + '=' + encodeURIComponent(obj));
    }

    return uristrings.join(prefix)
                     .replace(/^&/, '')
                     .replace(/%20/g, '+');
};

/**
 * A generic module which supports object disposing in dispose() method.
 * */
cm.DisposeSupport = {
  _disposers: [],

  /** Run all registered disposers */
  dispose: function() {
    var disposer;
    while (disposer = this._disposers.shift()) {
      disposer();
    }
  },

  /** Add disposer to the collection */
  addDisposer: function(disposeFunction) {
    this._disposers.push(disposeFunction);
  },

  /** Attach event handler and register de-attacher as a disposer */
  _attach: function() {
    this.addDisposer(cm.attach.apply(this, arguments));
  }
};

//
// VALIDATION
//
var badChars = ["'", "\"", "?", "\\"];

/*
cm.validateInput = function(element, errorElement, charLimit = -1)
{
    // search for asshole characters ', ", 
    var badCharFound = -1;
    
    for(var i = 0; i < badChars.length; i++)
    {
    	    badCharFound = element.value.indexOf(badChars[i]);
    	
    	//alert(badCharFound);
    	// stop as soon as bad character is found
    	if (badCharFound > -1)
    	{
    		break;
    	}
    }
    if (charLimit > -1)
    {
	    if (element.value.length > charLimit)
	    {
		    /// Input was too long
		errorElement.innerHTML = "Too many characters. Use " + charLimit + " or less.";    
	    }
    }
    else if (badCharFound > -1)
    {
    	    /// Use of invalid characters
    	errorElement.innerHTML = "Only use the specified characters"; // figure out what these are
    }
    else
    {
    	errorElement.innerHTML = "";    
    }
}
*/

cm.comparePasswordInput = function(element, compareElement, errorElement)
{
    if (element.value != compareElement.value)
    {
    	errorElement.innerHTML = "Passwords do not match.";
    }
    
    else
    {
    	errorElement.innerHTML = "";
    }
}

// MISCELLANEOUS THINGS
//
//for spinner
var delay = (function(){
  var timer = 0;
  return function(callback, ms){
    clearTimeout (timer);
    timer = setTimeout(callback, ms);
  };
})();

cm.refresh = function(){
    location.reload();
}

cm.getMembers = function(){
    var members = "[&quot;stall&quot;,&quot;bathroom&quot;,&quot;clothing&quot;,&quot;shoes&quot;]";//["apples","oranges","houses","grapes"];
    return members;
}

//
// FROM AJAX
//
cm.postStringify = function(obj) {
	var keys = Object.keys(obj);
	var postString = '';

	for (var i = 0; i < keys.length; i++) {
		// add an ampersand
		if (i > 0) {
			postString += '&';
		}

		postString += keys[i] + '=' + obj[keys[i]];
	}

	return postString;
}

/*
 * Handles call and return of
 * AJAX request
 */
cm.Ajax = function(arguments, success, failure) {
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
	
	else if (this.dataType === 'string') {
		this.xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	}

	this.xhr.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			requestedData = this.responseText;
			success(requestedData);
		}
		else {
			//failure (this.responseText, this.status)
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

// Page autoloader
//
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

// ElementMap so you don't have to specify all these different
// elements when creating dom modules

cm.ElementMap = function(element) {

	var child = element.firstChild;

	while (child && child != null) {

		if (child.nodeType == 1) {

			childClass = child.className;
			childId = child.id;

			// HANDLE ID
			if (childId !== "") {
				this._addChild(childId, child);
			}

			// HANDLE CLASS
			//
			// Cases:
			// 0) No class name
			// 1) Single class (no spaces)
			// 2) Multiple classes
			//
			if (childClass !== "") {
				class_split = childClass.split(' ');

				if (class_split.length == 1)
				  this._addChild(childClass, child);
				else {

					for(var i = 0; i < class_split.length; i++) {
					  this._addChild(class_split[i], child);
					}
				}
			}

			// check to see if this has children
			//   if so, make recursive call
			if (child.childNodes.length > 0) {
				//this.extendMap( cm.mapChildren( child ) );
				//

				// VERSION 1.0
				var childMap = new cm.ElementMap( child );
				this._extendMap( childMap );
			} // endif

		} // endif

		child = child.nextSibling;
	}
};

cm.ElementMap.prototype = {

	// Registers an element in the map
	//  should skip functions
	//  
	//  Makes array if not already an array
	//
	_addChild : function(key, child) {

		if (this[key] !== undefined) {

			// check if is array
			if ( cm.isArray( this[key]) ) {
			  this[key].push( child );
			}
			else { // make it an array

				// create array and fill it with values
				var array = [];
				array.push(this[key]);
				array.push(child);

				// replace
				this[key] = array;
			}
		}
		else {
		  this[key] = child;
		}
	},
	// Can extend this map with other maps
	//
	//   Used primarily to drill down into other elements
	//      But can also cross-pollinate with other 
	//      elements across DOM
	_extendMap : function(map) {

		for (var prop in map){

			// if not in 
			if (!this[prop]) {
			  this[prop] = map[prop];
			}
			else {
				
				if (cm.isArray(this[prop])) {

					// concat if array, push if not
					if (cm.isArray(map[prop])) {
					  this[prop].concat( map[prop] );
					}
					else {
					  this[prop].push( map[prop] );
					}

				} 
				else if (typeof this[prop] === 'function') {
				  continue;
				}
				else { // make it an array
				  
					var arr = [];
					arr.push( this[prop] );

					// concat if array, push if not
					if (cm.isArray(map[prop])) {
					  arr.concat( map[prop] );
					}
					else {
					  arr.push( map[prop] );
					}

					this[prop] = arr;

				} // end if

			} // end if 
		}
	}
};

/*
 * Specify a div to contain an error message
 *
 * Also specify a delay time and a hide time
 */
cm.ErrorMessage = function(o) {

	this._options = {
		display_element: null,
		error_class : 'cm_error',
		success_class : 'cm_success',
		show_time: 500,
		delay_time: 1000,
		hide_time: 500
	};

	cm.extend(this._options, o);

	this._display_element = this._options.display_element;
};

cm.ErrorMessage.prototype = {

	_formatElement: function(msg_type) {
		// remove all my classes
		$( this._display_element ).removeClass( this._options.error_class + ' ' + this._options.success_class );

		if (msg_type == 'Error' || msg_type == 'error') {
			$( this._display_element ).addClass( this._options.error_class );
		}
		else if (msg_type == 'Success' || msg_type == 'success') {
			$( this._display_element ).addClass( this._options.success_class );
		}
	},

	_display: function(msg_type, text) {

		this._formatElement( msg_type );
		$( this._display_element ).clearQueue();	// get rid of extra animations (in case there are many clicks)
		$( this._display_element ).text( text );
		$( this._display_element ).fadeIn( this._options.show_time );
		$( this._display_element ).delay( this._options.delay_time ).fadeOut( this._options.hide_time );
	}
};

/*
 * Two buttons and an input used as
 * a simple counter. I'm unsure why this
 * doesn't already exist.
 *
 */
cm.Counter = function(o) {

	// load options
	this._options = {
		element: null,
		left_button: null,
		right_button: null,
		maxCount: 99,
		minCount: -99
	};

	// extend user things
	cm.extend(this._options, o);
	cm.extend(this, cm.DisposeSupport);

	// important elements
	this._element = this._options.element;
	this._left_button = this._createLeftButton(this._options.left_button);
	this._right_button = this._createRightButton(this._options.right_button);

	// stuff
	this._count = 0;
//	this._button = this._createInputButton(this._find(this._element, 'button'));
//	this._errorLabel = this._find(this._element, 'error');
}

cm.Counter.prototype = {
	_increment: function() {

		if (this._count < this._options.maxCount) {
			this._count++;
			$( this._element ).val(this._count);
		}
	},
	_decrement: function() {

		if (this._count > this._options.minCount) {
			this._count--;
			$( this._element ).val(this._count);
		}
	},
	_update: function(o) {
		cm.extend(this._options, o);
	},
	_setValue: function(value) {

		$( this._element ).val(value);
		this._count = value;
	},
	_createLeftButton: function(button) {

		var self = this;

		button.onclick = function(e) {
			
			e.preventDefault();
			self._decrement();
		}

		return button;
	},
	_createRightButton: function(button) {

		var self = this;

		button.onclick = function(e) {
			
			e.preventDefault();
			self._increment();
		}

		return button;
	}
}

cm.NetworkSearcher = function(o) {

	this._options = {
		searchable_selector : null,
		results : null,
		best_match_container : null,
		related_networks_container : null,
		error_element : null,
		location_radio_name : 'location',
		origin_radio_name : 'origin',
	};

	cm.extend(this._options, o);
	cm.extend(this, cm.DisposeSupport);

	this._originRadioDiv;
	this._locationRadioDiv;

	this._registerElements();
	this._registerRadioEvents();

	this._radio_intro = 'input[name="';
	this._radio_end = '"]:checked';

	// set error element
	if (this._options.error_element != null) {
	 	this._error_message = new cm.ErrorMessage({ display_element: this._options.error_element });
	}
	else {
		alert ('No error message has been assigned');
	}
};

cm.NetworkSearcher.prototype = {
	_registerElements: function() {

		//var self = this;

		this._originRadioDiv = document.getElementById('origin-results');
		this._locationRadioDiv = document.getElementById('location-results');

		// check if two things not undefined 

		var originRadios = $( this._originRadioDiv ).children('ul').children('div.searchable-radio');
		var locationRadios = $( this._locationRadioDiv ).children('ul').children('div.searchable-radio');

		// check for named radio values
 
		this._allRadios = $( originRadios ).add( locationRadios );
	},
	_registerRadioEvents: function() {

		var self = this;

		$( this._allRadios ).change( function(e) {

			// Proceed if both have been selected
			if (self._bothSelected()) {

				var values = self._gatherValues();
				self._runSearch(values, self._processResults);
			}
		});
	},
	_bothSelected: function() {

		var lname = this._options.location_radio_name;
		var oname = this._options.origin_radio_name;

		var location_selected = 1 == $( this._radio_intro + lname + this._radio_end ).length;
		var origin_selected = 1 == $( this._radio_intro + oname + this._radio_end ).length;

		if (location_selected && origin_selected)
			return true;
		else
			return false;
	},
	_gatherValues: function() {

		var locationJSON = {
			name : null,
			fullname : null,
			id : null,
			searchable_class : null
		};

		var originJSON = {
			name : null,
			fullname : null,
			id : null,
			searchable_class : null
		};

		var lname = this._options.location_radio_name;
		var oname = this._options.origin_radio_name;

		// get location searchable
		var location_searchable = $( this._radio_intro + lname + this._radio_end ).parent('div.searchable-radio');

		locationJSON.name = $( location_searchable ).children( 'input[name="name"]' ).val();
		locationJSON.id = $( location_searchable ).children( 'input[name="id"]' ).val();
		locationJSON.searchable_class = $( location_searchable ).children( 'input[name="class"]' ).val();
		locationJSON.fullname = $( location_searchable ).children( 'input[type="radio"]' ).val();

		// get origin searchable
		var origin_searchable = $( this._radio_intro + oname + this._radio_end ).parent('div.searchable-radio');

		originJSON.name = $( origin_searchable ).children( 'input[name="name"]' ).val();
		originJSON.id = $( origin_searchable ).children( 'input[name="id"]' ).val();
		originJSON.searchable_class = $( origin_searchable ).children( 'input[name="class"]' ).val();
		originJSON.fullname = $( origin_searchable ).children( 'input[type="radio"]' ).val();

		return { location : locationJSON, origin : originJSON };
	},
	_runSearch: function(values, returnFunction) {

		var self = this;

		var networkSearchAjax = new cm.Ajax({
			requestType: 'POST',
			requestUrl: cm.home_path + '/api/search/network-post.php',
		    	data: values,
			dataType: 'JSON',
			requestParameters: ' ',
			sendNow: true
			}, 
			function(data) {
				data = JSON.parse(data);
				self._processResults(data);
			});
	},
	_processResults: function(data) {

		if (data.error != 0) {
			this._error_message._display('success', data.error);
		}

		// I feel like this oughta be the first
		// thing to use js mustache
		this._options.results;

		var possible_network_template = document.getElementById('possible-network-template').innerHTML;
		var active_network_template = document.getElementById('active-network-template').innerHTML;

		// render things
		var best_match_html = null;

		if (data.main_network.existing == false || data.main_network.existing == null) {
			best_match_html = Mustache.render( possible_network_template, {'network' : data.main_network, 'vars' : data.vars } );
		}
		else {
			best_match_html = Mustache.render( active_network_template, {'network' : data.main_network, 'vars' : data.vars } );
		}

		// clear prior results
		$( this._options.best_match_container ).empty();
		$( this._options.related_networks_container ).empty();

		// show networks (because they may be hidden)
		$( this._options.best_match ).removeClass('cmhide');
		$( this._options.best_match ).show();
		$( this._options.related_networks ).removeClass('cmhide');
		$( this._options.related_networks ).show();

		// add best match
		$( this._options.best_match_container ).append( best_match_html );

		// add related networks
		for (var i=0; i < data.related_networks.length; i++) {

			var html;

			if (data.related_networks[i].existing == false || data.related_networks[i].existing == null) {
				html = Mustache.render( possible_network_template, {'network' : data.related_networks[i], 'vars' : data.vars } );
			}
			else {
				html = Mustache.render( active_network_template, data.related_networks[i] );
			}

			$( this._options.related_networks_container ).append( html );
		}

	}
};

cm.Overlay = function(user_options) {

	this._options = {
		root: null,
		toggle: null,
		top_coord: 0,
		left_coord: 0 
	};

	cm.extend(this._options, user_options);

	this.showing = false;
	this.overlay = new cm.ElementMap( this._options.root );

	this._init();
}

cm.Overlay.prototype = {

	_init : function() {

		var self = this;

		// EVENTS
		//
	
		// Activate toggle
		// adding cursor
		$( this._options.toggle ).css('cursor', 'pointer');
		$( this._options.toggle ).on('click touchstart', function(e) {
		
			e.stopPropagation();
			e.preventDefault();

			if (!self.showing) {
			  self._show();
			}
			else {
			  self._hide();
			}
		});

		// set close button
		this.overlay['close-cm-overlay'].onclick = function(e) {

			e.preventDefault();
			self._hide();
		}

		// set close button
		this.overlay['close-cm-overlay'].ontouchstart = function(e) {

			e.preventDefault();
			self._hide();
		}
	},
	_show : function() {

		if (!this.showing) {

			$( this._options.root ).show().animate({left : this._options.left_coord },
				200);

			this.showing = true;
		}
	},
	_hide : function() {


		// if already set over, change to the other way
		if (this.showing) {

			$( this._options.root ).animate({left : $( window ).width() },
				200, function() {
					$( this ).hide();
				});
		
			this.showing = false;
		}
	}
}
