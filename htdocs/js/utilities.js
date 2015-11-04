//
// Helper functions
//

var cm = cm || {};

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
		location_radio_name : 'location',
		origin_radio_name : 'origin',
		error_span : null
	};

	cm.extend(this._options, o);
	cm.extend(this, cm.DisposeSupport);

	this._originRadioDiv;
	this._locationRadioDiv;

	this._registerElements();
	this._registerRadioEvents();

	this._radio_intro = 'input[name="';
	this._radio_end = '"]:checked';
}

cm.NetworkSearcher.prototype = {
	_registerElements: function() {

		//var self = this;

		this._originRadioDiv = document.getElementById('origin-results');
		this._locationRadioDiv = document.getElementById('location-results');

		// check if two things not undefined 

		var originRadios = $( this._originRadioDiv ).children('div.searchable-radio');
		var locationRadios = $( this._locationRadioDiv ).children('div.searchable-radio');

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

		var networkSearchAjax = new Ajax({
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
			$( this._options.error_span ).text(data.error);
		}

		// I feel like this oughta be the first
		// thing to use js mustache
		this._options.results;
	}
}
