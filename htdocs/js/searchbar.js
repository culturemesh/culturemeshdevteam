var LIST_SIZE = 4;
//var queries = ["speak", "are from"]
var queryList = [];

var exceptions = [
	'Washington, D.C.'
];

var q_results, locations, origins, 
    li_origins, li_locations,
    languages, li_languages;

origins = new LocList();
locations = new LocList();
languages = new LocList();

//var locations = [];
li_origins = [];
var cur_locations = [];
li_languages = [];
li_locations = [];


searchBar = new SearchBar();
loadInitialData();

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

function fillACArray(acArray, data, type) {
	acArray.push = function(item) {
		acArray.list.push(item);
	}

	var items = data.split('\n');

	// for each line, excluding the first
	for (var i = 1; i < items.length; i++) {
		// create list item
		var item = new ListItem();
		item.type = type;
		item.name = '';

		// split line (by tab)
		iSplit = items[i].split('\t');

		// if there is a split to worry about,
		// we gotta do some looping, b/c there's
		// comma stuff to worry about - easier this way
		if (iSplit.length > 1) {
			var j = 0;
			for (; j < iSplit.length - 1; j++) {
				if (iSplit[j] != 'NULL' &&
						iSplit[j] != '')
					item.name += iSplit[j] + ', ';
			}

			// don't add comma on last item
			item.name += iSplit[j];
		}
		else
		  { item.name = items[i]; }

		// go ahead and push the item into the list
		acArray.push(item);
	}
}

function loadInitialData() {
	// search bar
	searchBar.initialize();
	
	// load countries
	var countryRequest = new Ajax({
		requestType: 'GET',
		requestUrl: 'data/s_countries.txt',
		requestParameters: ' ',
		sendNow: true
		}, function(data) {
			fillACArray(locations, data, 'co');
		});

	// load languages
	var langRequest = new Ajax({
		requestType: 'GET',
		requestUrl: 'data/s_languages.txt',
		requestParameters: ' ',
		sendNow: true
		}, function(data) {
			fillACArray(languages, data, '_l');
		});

	// load regions
	var regionRequest = new Ajax({
		requestType: 'GET',
		requestUrl: 'data/s_regions.txt',
		requestParameters: ' ',
		sendNow: true
		}, function(data) {
			fillACArray(locations, data, 'rc');
		});

	// load cities
	var cityRequest = new Ajax({
		requestType: 'GET',
		requestUrl: 'data/s_cities.txt',
		requestParameters: ' ',
		sendNow: true
		}, function(data) { 
			fillACArray(locations, data, 'cc');
		});
}

//searchBar = new SearchBar();

function SearchBar() {
	// indices
	//  1 : are from
	//  2 : speak
	var searchOne = document.getElementById("search-1");
	var clik1 = document.getElementById('clik1');
	var searchTwo = document.getElementById("search-2");
	var clik2 = document.getElementById('clik2');

	var topic = document.getElementById("search-topic");
	var varUl = document.getElementById("s-var");
	var locUl = document.getElementById("s-location");
//	var loc_select, q_select, cur_query;

	var selector = document.getElementById("verb-select");

	this.initialize = function() {

		///////////////////////////////////
		//////// ADD EVENTS
		////////////////////////////////

		// Allow for exit by clicking
		// outside search boxes
		window.onclick = function() {
			hideUl(varUl);
			hideUl(locUl);		
		}

		searchOne.onclick = function(e) {
			e.stopPropagation();	
		}

		searchTwo.onclick = function(e) {
			e.stopPropagation();
		}

		selector.onchange = function() {
			clearUl(varUl);
		}

		//  display autocompletes
		searchOne.onfocus = function() {
			showUl(varUl);
			hideUl(locUl);
		}

		searchTwo.onfocus = function() {
			showUl(locUl);
			hideUl(varUl);
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

			// handle different verbs different-like
			switch (selector.selectedIndex) {
				case 0:
					li_locations = locations.searchLocations(value);
					// Rank search results
					//li_locations = rankLocations(li_locations);
					// Fill up Ul
					fillUl(varUl, li_locations.slice(0,4), searchOne, clik1);
					boldifyMatch(varUl, value);
					break;
				case 1:
					// search for language
					li_languages = languages.search(value);
					// fill ul
					fillUl(varUl, li_languages.slice(0,4), searchOne, clik1);
					boldifyMatch(varUl, value);
					break;
			}

			// displayUl
			showUl(varUl);
		}

		// update list
		searchTwo.onkeydown = function(e) {
			// clear ul
			clearUl(locUl);
			// get new value
			var value = updateValue(e, searchTwo);

			// if value changes, unclick
			if (value != searchTwo.value)
				clik2.value = 0;

			if (value == '')
				return;

			// get locations
			li_locations = locations.searchLocations(value);
			// Rank search results
			//li_locations = rankLocations(li_locations);
			// Fill up Ul
			fillUl(locUl, li_locations.slice(0,4), searchTwo, clik2);
			boldifyMatch(locUl, value);
		}

		selector.onchange = function() {
			topic.value = '';
		}

		// position uls
		oneX = searchOne.offsetLeft;
		oneY = searchOne.offsetTop + searchOne.offsetHeight;
		twoX = searchTwo.offsetLeft;
		//queryX = oneX;

		//queryUl.style.display = "block";
		//varX = oneX + queryUl.offsetWidth;
		//queryUl.style.display = "none";

		// set varX to searchbar offset
		varX = oneX;

		//queryUl.style.left = queryX.toString() + "px";
		varUl.style.left = varX.toString() + "px";
		locUl.style.left = twoX.toString() + "px";
		//queryUl.style.top = oneY.toString() + "px";
		varUl.style.top  = oneY.toString() + "px";
		locUl.style.top = oneY.toString() + "px";
	}


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
		// in this rudimentary list, we just
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


/*
// create prototype chain for single search bar
SingleSearch.prototype = Object.create(SearchBar.prototype);

SingleSearch.prototype.constructor = SingleSearch;
*/
