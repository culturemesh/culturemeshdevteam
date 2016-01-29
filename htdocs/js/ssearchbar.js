function ListItem() {
	this.name = null;
	this.type = null;
}

function LocList() {
	this.list = [];
}
// Push an object onto the list
LocList.prototype.push = function(item) {
	this.list.push(item);
}

LocList.prototype.slice = function(start, end) {
	return this.list.slice(start, end);
}

// Returns a list of matches to 
// a search
LocList.prototype.search = function(term) {
	// list aren't sorted, so no binary search
	// --- maybe a sort would be in order...
	var results = [];
	// unshift count, stop at some number
	var uCount = 0;
	var uMax = 20;
	for (var i = 0; i < this.list.length; i++) {
		// stop count at uMax
		// prevent from taking too long
		if (uCount >= uMax)
			break;

		// convert list item to lowercase,
		// 	may move somewhere else 
		var lower_name = this.list[i].name.toLowerCase();
		// check if name is in string
		match = lower_name.indexOf(term.toLowerCase());
		if (match >= 0) {
			// put beginning matches at front of list
			if (match == 0) {
				results.unshift(this.list[i]);
				uCount++;
			}
			else
				results.push(this.list[i]);
		}
	}
	return results;
}

// Returns a list of matches to 
// a search
LocList.prototype.searchLocations = function(term) {
	// list aren't sorted, so no binary search
	// --- maybe a sort would be in order...
	var results = [];
	var cMatches = [];
	var fcMatches = [];
	// unshift count, stop at some number
	var uCount = 0;
	var uMax = 20;
	for (var i = 0; i < this.list.length; i++) {
		// stop count at uMax
		// prevent from taking too long
		if (uCount >= uMax)
			break;

		// convert list item to lowercase,
		// 	may move somewhere else 
		var lower_name = this.list[i].name.toLowerCase();
		// check if name is in string
		match = lower_name.indexOf(term.toLowerCase());
		if (match >= 0) {
			// unshift beginning match stuff
			if (match == 0)
			{
				fcMatches.unshift(this.list[i]);
				uCount++;
			}
			// unshift us stuff
			else if (lower_name.indexOf('united states') > -1) {
				//cMatches.unshift(this.list[i]);
				//uCount ++;
			}
			else
				results.push(this.list[i]);
		}
	}
	//
	// sort matches
	fcMatches = fcMatches.sort(function(a, b) {
		if (a.name > b.name) {
			return 1;
		}
		else {
			return -1;
		}
		});

	return fcMatches.concat(cMatches,results);
}


/*
 * parameters for a search bar
 * (searchable, network)
 * (tablediv)
 * (type : single, batch)
 * (table : Array or string)
 */
function SSearchBar(divName, operation, type, tables) {
	
	if (type == 'single') {
		this.template = document.getElementById('single-search-tmpl').innerHTML;
	}
	else {
		this.template = document.getElementById('search-tmpl').innerHTML;
	}

	this.itemList = new LocList();
	this.li_list = []
	this.tables = tables;
	var tables = this.tables;



	var itemList = this.itemList;
	var li_list = this.li_list;

	var getItemList = function() {
		return itemList;
	}

	var getLiList = function() {
		return li_list;
	}

	// render search bar
	document.getElementById(divName).innerHTML = Mustache.render(this.template);

	// get all the fun elements
	/*
	var searchOne = document.getElementById("search-1");
	this.searchOne = searchOne;
	var clik1 = document.getElementById('clik1');

	var topic = document.getElementById("search-topic");
	var varUl = document.getElementById("s-var");

	var submtBtn = document.getElementById('search-submit');
	this.submtBtn = submtBtn;

	// vars for displaying searchables
	this.tableDiv = document.getElementById('table-div');
	var tableDiv = this.tableDiv;
	
	var getTableDiv = function() {
		return tableDiv;
	}
	*/

	var searchOne = $("#" + divName + " #search-1")[0];
	this.searchOne = searchOne;
	var clik1 = $("#" + divName + " #clik1")[0];

	var topic = $("#" + divName + " #search-topic")[0];
	var varUl = $("#" + divName + " #s-var")[0];

	var submtBtn = $("#" + divName + " #search-submit")[0];
	this.submtBtn = submtBtn;

	// vars for displaying searchables
	this.tableDiv = $("#table-div")[0];
	var tableDiv = this.tableDiv;
	
	var getTableDiv = function() {
		return tableDiv;
	}

	///////////////////////////////////
	//////// ADD EVENTS
	////////////////////////////////

	// Allow for exit by clicking
	// outside search boxes
	window.onclick = function() {
		hideUl(varUl);
	}

	searchOne.onclick = function(e) {
		e.stopPropagation();	
	}

	//  display autocompletes
	searchOne.onfocus = function() {
		showUl(varUl);
	}

	// update list
	searchOne.onkeydown = function(e) {
		// clear ul
		clearUl(varUl);
		
		// get new value
		var value = updateValue(e, searchOne);

		// if value has changed(could have been nothing), unclick
		// and reset topic
		if (value != searchOne.value) {
			clik1.value = 0;
			topic.value = '';
		}

		if (value == '')
			return;

		var li_list = getLiList();

		// handle different verbs different-like
		li_list = getItemList().searchLocations(value);
		// Rank search results
		//li_locations = rankLocations(li_locations);
		// Fill up Ul
		fillUl(varUl, li_list.slice(0,4), searchOne, clik1);
		boldifyMatch(varUl, value);

		// displayUl
		showUl(varUl);
	}

	// submit search for create s-ble
	submtBtn.onclick = function(e) {
		
		/*
		var tableDiv = getTableDiv();
		// prevent default event behavior.
		e.preventDefault();
		// what did you 
		// think it was gonna do? Cure cancer?
		//
		var formData = {
			op : 'searchSearchables',
			table : tables,
			query : searchOne.value
		};

		var template = document.getElementById('disp-searchable-tmpl').innerHTML;

		var searchSbmt = new Ajax({
			requestType: 'POST',
			requestUrl: 'admin/admin_ops.php',
			requestParameters: ' ',
		    	data: formData,
		    	dataType : 'JSON',
			sendNow: true
			}, function(data) { 

				// should return with a handsome table
				var result = JSON.parse(data);

				// set tableDiv to display the thing
				tableDiv.innerHTML = Mustache.render(template, 
					{ 'columns': result.cols});

				// remove buttons for important fields
				$('.edit-toggle.imp').remove();

				// remove buttons for nonfk posers
				// YOU KNOW WHO YOU ARE!
				$('.db_find.bleh').remove();
				$('.db_find.imp_fk').remove();

				// hide inputs
				$('.edit-input').hide();

				// remove edit toggle for important things
				$('.edit-toggle.imp_fk').remove();
			});

		$('.edit-toggle').on('click', editValue);

		// find buttons need call search bar function
		$( '.find' ).on('click', function(e) {
			var col = $( e.target ).parents('td.db_find').siblings('td.db_col').text();
			col = stringParse(col);
			callSearchBar(col);
		});
		*/
	}

	// position uls
	oneX = searchOne.offsetLeft;
	oneY = searchOne.offsetTop + searchOne.offsetHeight;
	//queryX = oneX;

	//queryUl.style.display = "block";
	//varX = oneX + queryUl.offsetWidth;
	//queryUl.style.display = "none";

	// set varX to searchbar offset
	varX = oneX;

	//queryUl.style.left = queryX.toString() + "px";
	varUl.style.left = varX.toString() + "px";
	//queryUl.style.top = oneY.toString() + "px";
	varUl.style.top  = oneY.toString() + "px";

	/** takes an unordered list and fills them
	 * @param - ul, unordered list dom element
	 * 	  - data, a list of text
	 * 	  - clickTarg, to receive click value
	**/
	function fillUl(ul, data, clickTarg, clickTrk) {
		var name = null;	
		for (var i = 0; i < data.length; i++) {
			// check for duplicate names
			// if they're in there, forget em
			if (name == data[i].name) 
			  {continue;}
			else 
			  {name = data[i].name;}
			
			var item = document.createElement("LI");
			var itemText = document.createTextNode(data[i].name);
			item.appendChild(itemText);
			ul.appendChild(item);

			// add onclick function to
			// add value to element
			item.onclick = function(e) {
				e.stopPropagation();
				// get element
				var elem = e.target;

				//alert (e.target.tagName);
				// check if we've got a bold tag by accident
				//  if so, get the parent (li) 
				if (elem.tagName == 'B')
					elem = elem.parentNode;

				// remove tags
				var value = elem.innerHTML.replace(/(<([^>]+)>)/ig,"");

				// change value of target
				clickTarg.value = value;

				// change value of track
				clickTrk.value = 1;

				// mark down topic if we're varUl
				if (e.target.parentNode.id == 's-var') {
					// get type from parallel list
					var children = e.target.parentNode.childNodes;
					var i = 0;
					for (; i < children.length; i++) 
						if (e.target == children[i]) 
							break;
					// set type
					topic.value = data[i].type;
				}
				// disappear parent
				ul.style.display = 'none';

				// clear ul
				clearUl(ul);
			}
		}
	}

	function clearUl(ul) {
		while (ul.firstChild)
			ul.removeChild(ul.firstChild);
	}

	function hideUl(ul) {
		ul.style.display = 'none';
	}

	function showUl(ul) {
		ul.style.display = 'block';
	}

	function updateValue(e, box) {
		// get pressed key
		var keyCode = ('which' in e) ? e.which : e.keyCode;

		// if it's a smelly shift, 
		//  don't care
		if (keyCode === 16)
			return box.value;

		// if backspace, return string
		// minus 1
		if (keyCode === 8)
			return box.value.substring(0, box.value.length-1);

		// otherwise add to value
		keyCode = String.fromCharCode(keyCode).toLowerCase();
		return box.value + keyCode;
	}


	function searchList(term, src) {
		var list = [];
		for (var i = 0; i < src.length; i++)
		{
			var lower_src = src[i].name.toLowerCase();
			match = lower_src.indexOf(term.toLowerCase());

			// if it's a hit,
			// 	prioritize matches at beginning
			if (match >= 0)
				if (match == 0)
					list.unshift(src[i]);
				else
					list.push(src[i]);
		}

		return list;
	}

	function boldifyMatch(ul, term)
	{
		var sub;

		// for each term in the list
		for (var i = 0; i < ul.children.length; i++) {
			sub = term.toLowerCase();

			// get start and endpoints
			//  of term, and li string
			var tStart = ul.children[i].innerHTML.toLowerCase().indexOf(sub);
			var tEnd = tStart + term.length;
			var iEnd = ul.children[i].innerHTML.length;

			var t1 = ul.children[i].innerHTML.substr(0,tStart);
			var t2 = ul.children[i].innerHTML.substr(tStart, term.length);
			var t3 = ul.children[i].innerHTML.substr(tEnd,iEnd);

			// if match starts @ beginning...
			if (tStart == 0)
				ul.children[i].innerHTML = '<b>' + t2 + '</b>' + t3;
			else
				ul.children[i].innerHTML = t1 + '<b>' + t2 + '</b>' + t3;
		}
	}

	function rankLocations(list) {
		// want to push items with United States
		// to the top
		var rankedList = [];
		
		// if list is short enough, return
		if (list.length <= LIST_SIZE)
			return list;

		var unshiftCount = 0;

		for (var i = 0; i < list.length; i++)
		{
			/*
			// break if you've got
			// four priority things
			if (unshiftCount >= 4)
				break;
				*/

			// if it's american,
			// add to beginning of list,
			// else add to end
			if (list[i].name.indexOf("United States") != -1)
			  { 
				  rankedList.unshift(list[i]); 
				  //unshiftCount += 1;
			  }
			else
			  { rankedList.push(list[i]); }
		}

		return rankedList;
	}

}

SSearchBar.prototype.fetchValues = function(tables) {

	// create an array to handle different
	// ajax requests
	var requests = [];
	var itemListC = this.itemList;

	// nice little function for ajax use
	// CLOSURE!!!!
	var processTextData = function(data) {
		var items = data.split('\n');

		// for each line, excluding the first
		for (var i = 1; i < items.length; i++) {
			// create list item
			var item = new ListItem();
			item.type = tables;
			item.name = '';
			item.id = -1;

			// split line (by tab)
			iSplit = items[i].split('\t');

			// if there is a split to worry about,
			// we gotta do some looping, b/c there's
			// comma stuff to worry about - easier this way
			if (iSplit.length > 1) {
				var j = 0;
				for (; j < iSplit.length - 2; j++) {
					if (iSplit[j] != 'NULL')
						item.name += iSplit[j] + ', ';
				}

				// don't add comma on last item
				item.name += iSplit[j];
				item.id = iSplit[j+1];
			}
			else
			  { item.name = items[i]; }

			// go ahead and push the item into the list
			// CLOSURE!!!
			itemListC.push(item);
		}
	}	

	if (tables instanceof Array) {
		for (var i = 0; i < tables.length; i++) {
			// load cities
			var fileRequest = new Ajax({
				requestType: 'GET',
				requestUrl: 'data/s_' + tables[i] + '.txt',
				requestParameters: ' ',
				sendNow: true
				}, function(data) { 
					processTextData(data);
				});
		}
	}
	else {
		// load whatever
		var fileRequest = new Ajax({
			requestType: 'GET',
			requestUrl: 'data/s_' + tables + '.txt#',
			requestParameters: ' ',
			sendNow: true
			}, function(data) { 
				processTextData(data);
			});
	}
}

SSearchBar.prototype.submitSearch = function() {
		// load cities
		var fileRequest = new Ajax({
			requestType: 'POST',
			requestUrl: 'data/s_' + tables + '.txt',
			requestParameters: ' ',
			sendNow: true
			}, function(data) { 
				fillACArray(locations, data);
			});
}


SSearchBar.prototype.setTableDiv = function(id) {
	this.tableDiv = document.getElementById(id);
}

SSearchBar.prototype.setSubmit = function(func) {
	this.submtBtn.onclick = func;
}

SSearchBar.prototype.getValue = function() {
	return this.searchOne.value;
}
