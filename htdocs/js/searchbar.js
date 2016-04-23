
cm.SearchField = function(user_options) {

	this._options = {
		input_field : null,
		clicked : null,
		id_field : null,
		class_field : null,
		ul : null,
		MIN_LENGTH : 2,
		KEY_DELAY : 800,
		/////// OPTIONAL /////////////
		div_id : null,
		selector : null,
		topic : null,
		display_table : null,
		submit_button : null,
		loading_image : "/images/searchbar-loading.gif",
		ul_class : "search",
		template : null,
		render_target : null,
		search_table : null
	}

	cm.extend(this._options, user_options);

	this._input_field = this._options.input_field;
	this._clicked = this._options.clicked;
	this._ul = this._options.ul;
	this._id_field = this._options.id_field;
	this._class_field = this._options.class_field;

	// MORE OPTIONAL VALUES
	this._selector = this._options.selector;
	this._topic = this._options.topic; // optional
	this._search_table = this._options.search_table;
	this._template = this._options.template;
	this._render_target = this._options.render_target;

	// submit button
	this._submit_button = this._options.submit_button;
	this._display_table = this._options.display_table;

	this.MIN_LENGTH = this._options.MIN_LENGTH;
	this.KEY_DELAY = this._options.KEY_DELAY;

	this.NAME_SEARCH;

	this._tableDiv;

	// initialize search field
	this._init();
};

cm.SearchField.prototype = {

	_init : function() {

		var self = this;

		if (this._options.template != null) {
			this._templateInit();
		}

		// make ul a node of input field
		//   to prevent bad behavior on list item clicks

		///////////////////////////////////
		//////// ADD EVENTS
		////////////////////////////////
		//

		// Allow for exit by clicking
		// outside search boxes
		window.onclick = function() {
			clearTimeout(self.NAME_SEARCH);
			self._hideUl();
			self._clearErrorLi();
		}

		this._input_field.onclick = function(e) {
			e.stopPropagation();	
		}

		//  display autocompletes
		this._input_field.onfocus = function() {
			self._showUl();
			self._hideUl();
		}

		this._input_field.onblur = function(e) {
			clearTimeout(self.NAME_SEARCH);
			self._hideUl();
			self._clearErrorLi();
		}

		this._input_field.onkeydown = function(e) {

			// clear event (if necessary...don't know how to 'if' this)
			clearTimeout(self.NAME_SEARCH);

			self._clearErrorLi();

			// get new value
			var key_code = self._checkKey(e);

			if (key_code === false) {
				self._clearLoadingLi();
				return False;
			}

			var input_value = self._updateValue(key_code);
			var search_class;

			// figure out selector (IF SELECTOR IS EVEN PRESENT)
			if (self._selector != null) {

				// Location or language, pretty simple
				if (self._selector.selectedIndex == 0) {
				  search_class = 'location';
				}
				else if (self._selector.selectedIndex == 1) {
				  search_class = 'language';
				}
			}
			
			var search_array = {
				input_value : input_value,
				search_class : search_class,
				ul : self._ul,
				input : self._input_field,
				click_tracker : self._clicked,
				id_field : self._id_field,
				class_field : self._class_field,
				search_table : self._search_table
			};

			// If past the minimum length
			if (input_value.length >= self.MIN_LENGTH) {
			  self.NAME_SEARCH = self._searchCall( search_array );
			}

			// If length is 0, clear ul
			if (input_value.length < self.MIN_LENGTH) {
			  self._clearUl();
			}
		}


		if (this._selector != null) {

			this._selector.onchange = function() {
				self._topic.value = '';
				self._clearUl();
			}
		}

		// Might be a dumb idea to do this,
		// but eh, wth
		if (this._submit_button != null) {
			this._submit_button.onclick = function(e) {
				e.preventDefault();
			}
		}

		this._positionUl();
	},
	_templateInit : function() {

		///////////////////////////////////////////
		// ADD TEMPLATE
		// //////////

		// render search bar
		this._render_target.innerHTML = Mustache.render( this._template.innerHTML );

		/////////////////////////////////////////////
		// ADD ELEMENTS
		// //////////
		this._target_div = document.getElementById( this._options.target_div );

		this._input_field = $( this._target_div ).find('#' + this._options.input_field).get(0);
		//this._submit_button = document.getElementById( this._options.submit_button );
		this._submit_button = $( this._target_div ).find('#' + this._options.submit_button).get(0); 
		//this._clicked = document.getElementById( this._options.clicked );
		this._clicked = $( this._target_div ).find('#' + this._options.clicked).get(0); 
		//this._ul = document.getElementById( this._options.ul );
		this._ul = $( this._target_div ).find('#' + this._options.ul).get(0); 
		//this._id_field = document.getElementById( this._options.id_field );
		this._id_field = $( this._target_div ).find('#' + this._options.id_field).get(0); 
		//this._class_field = document.getElementById( this._options.class_field );
		this._class_field = $( this._target_div ).find('#' + this._options.class_field).get(0); 
	},
	_searchCall : function(search_array) {

		var values;
		var self = this;

		if (search_array['search_class'] == null || search_array['search_class'] == undefined) {
		  search_array['search_class'] = 'location';
		}

		var values = {
			input_value : search_array['input_value'],
			search_class : search_array['search_class'],
			search_table : search_array['search_table']
		};

		this._clearLoadingLi(search_array['ul']);
		this._showLoadingLi(search_array['ul']);
		this._showUl();

		return setTimeout(
			function() {
				var search = new Ajax({
					requestType: 'POST',
					requestUrl: cm.home_path + '/api/search/search_names.php',
					data: values,
					dataType: 'JSON',
					requestParameters: ' ',
					sendNow: true
					}, 
					function(data) {

						// should return a series of possible names
						results = JSON.parse(data);

						///////////////////////////////
						// VARIATION
						//
						self._clearUl();

						if (results.results != false) {
							self._fillUl(results.results);
							self._showUl();
						}
						else {
							//showErrorLi(search_array['ul']);
							//showUl(search_array['ul']);
						}
					});
				}, self.KEY_DELAY
				);
	},
	/*
	 * If this is something we don't like, (like TAB)
	 * return False
	 */
	_checkKey : function(e) {

		var keyCode = ('which' in e) ? e.which : e.keyCode;

		// check for tab
		if (keyCode === 9) {
		  return false;
		}
		else {
		  return keyCode;
		}
	},
	_updateValue : function(keyCode) {

		// if it's a smelly shift, 
		//  don't care
		if (keyCode === 16)
			return this._input_field.value;

		// if backspace, return string
		// minus 1
		if (keyCode === 8)
			return this._input_field.value.substring(0, this._input_field.value.length-1);

		// otherwise add to value
		keyCode = String.fromCharCode(keyCode).toLowerCase();

		return this._input_field.value + keyCode;
	},
	_positionUl : function() {

		// position uls
		oneX = this._input_field.offsetLeft;
		oneY = this._input_field.offsetTop + this._input_field.offsetHeight;

		// set varX to searchbar offset
		varX = oneX;

		this._ul.style.left = varX.toString() + "px";
		this._ul.style.top  = oneY.toString() + "px";
	},
	_fillUl : function(data) {

		//
		var name = null;	

		var self = this;

		for (var i = 0; i < data.length; i++) {

			// check for duplicate names
			// if they're in there, forget em
			if (name == data[i].fullname) 
			  {continue;}
			else 
			  {name = data[i].fullname;}
			
			var item = document.createElement("LI");
			item.srch_name = data[i].fullname;
			item.srch_topic = data[i].type;
			item.srch_id = data[i].id;
			item.srch_class = data[i].obj_class;

			var itemText = document.createTextNode(data[i].fullname);
			item.appendChild(itemText);
			this._ul.appendChild(item);

			var curIndex = i;
			var dataItem = data[i];

			// add onmousedown function to
			// add value to element
			item.onmousedown = function(e) {

				e.stopPropagation();

				// get element
				var elem = e.target;

				// update id tag
				self._id_field.value = this.srch_id;
				self._class_field.value = this.srch_class;

				//alert (e.target.tagName);
				// check if we've got a bold tag by accident
				//  if so, get the parent (li) 
				if (elem.tagName == 'B')
					elem = elem.parentNode;

				// remove tags
				var value = elem.innerHTML.replace(/(<([^>]+)>)/ig,"");

				// change value of target
				self._input_field.value = value;

				// change value of track
				self._clicked.value = 1;

				// mark down topic if we're varUl
				//if (e.target.parentNode.id == 's-var') {
				//	topic.value = this.srch_topic;
				//}
				if (self._topic != null) {
					self._topic.value = this.srch_topic;
				}

				// disappear parent
				self._ul.style.display = 'none';

				// clear ul
				self._clearUl();
			}
		}
	},
	_showUl : function() {

		this._ul.style.display = 'block';
	},
	_hideUl : function() {

		this._ul.style.display = 'none';
	},
	_clearUl : function() {
		while (this._ul.firstChild)
			this._ul.removeChild(this._ul.firstChild);
	},
	_boldifyMatch : function() {

		var sub;

		// for each term in the list
		for (var i = 0; i < this._ul.children.length; i++) {
			sub = term.toLowerCase();

			// get start and endpoints
			//  of term, and li string
			var tStart = this._ul.children[i].innerHTML.toLowerCase().indexOf(sub);
			var tEnd = tStart + term.length;
			var iEnd = this._ul.children[i].innerHTML.length;

			var t1 = this._ul.children[i].innerHTML.substr(0,tStart);
			var t2 = this._ul.children[i].innerHTML.substr(tStart, term.length);
			var t3 = this._ul.children[i].innerHTML.substr(tEnd,iEnd);

			// if match starts @ beginning...
			if (tStart == 0)
				this._ul.children[i].innerHTML = '<b>' + t2 + '</b>' + t3;
			else
				this._ul.children[i].innerHTML = t1 + '<b>' + t2 + '</b>' + t3;
		}
	},
	_resetTopic : function() {

		this._clicked.value = 0;
		this._topic.value = '';
	},
	_clearErrorLi : function() {

		if (this._ul.childNodes[0]) {
			if (this._ul.childNodes[0].className == this._options.ul_class + " " + "sb-li sb-error") {
			  this._ul.removeChild(this._ul.childNodes[0]);
			}
		}
	},
	_showErrorLi : function() {

		var item = document.createElement("LI");
		item.className = this._options.ul_class + " " + "sb-li sb-loading";

		var itemText = document.createTextNode("No results found");
		item.appendChild(itemText);
		this._ul.appendChild(item);

		// insert as first element
		this._ul.insertBefore(item, this._ul.childNodes[0]);
	},
	_showLoadingLi : function() {

		var item = document.createElement("LI");
		item.className = this._options.ul_class + " " + "sb-li sb-loading";

		// get image ...streamline later
		var img = document.createElement("IMG");
		img.src = cm.home_path + this._options.loading_image; 
		item.width = "60px";

		item.appendChild(img);

		// insert as first element
		this._ul.insertBefore(item, this._ul.childNodes[0]);
	},
	_clearLoadingLi : function() {

		if (this._ul.childNodes[0]) {
			if (this._ul.childNodes[0].className == this._options.ul_class + " " + "sb-li sb-loading") {
			  this._ul.removeChild(this._ul.childNodes[0]);
			}
		}
	},
	_setTableDiv : function (id) {
		this._tableDiv = document.getElementById(id);
	},
	_setSearchTable : function(table) {
		this._search_table = table;
	},
	_setSubmit : function(callback) {

		if (this._submit_button == null) {
		  return false;
		}

		this._submit_button.onclick = callback;
	},
	_getValue : function() {
		return $( this._input_field ).val();
	},
	_getSearchValues : function() {
		return {
			value : $( this._input_field ).val(),
			query_id : $( this._id_field ).val(),
			query_class : $( this._class_field ).val(),
			clicked : $( this._clicked ).val()

		};
	}
};

cm.Autofill = function(user_options) {

};

cm.Autofill.prototype = {

};
