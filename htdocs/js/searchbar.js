var opening = "Find people who ";
var queries = ["speak", "are from"]
var queryList = [];

// Fill up queryList with List Items
for (var i = 0; i < queries.length; i++) {
	item = new ListItem();
	item.name = queries[i];
	queryList.push(item);
}

var prompt_strings = ["Find people who speak",
			"Find people who are from",
			"Near"];
var q_results, locations, origins, li_origins, li_locations, locationClasses, languages, li_languages;

origins = new LocList();
locations = new LocList();
/*
var locations = [];
var li_origins = [];
var cur_locations = [];
var languages = [];
var li_languages = [];
*/

loadInitialData();
searchBar = new SearchBar();

function ListItem() {
	this.name = null;
	this.type = null;
}

function LocList() {
	this.list = [];


}
/* Push an object onto the list
 */
LocList.prototype.push = function(item) {
	this.list.push(item);
}

LocList.prototype.slice = function(start, end) {
	return this.list.slice(start, end);
}

/* Returns a list of matches to 
 * a search
 */
LocList.prototype.search = function(term) {
	var results = [];
	for (var i = 0; i < this.list.length; i++) {
		match = this.list[i].name.indexOf(term) == 0;
		if (match != false) 
			results.push(this.list[i]);
	}
	return results;
}

function loadInitialData() {
	// Create ajax request
	var xmlhttp = new XMLHttpRequest();
	
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{
			raw_data = JSON.parse(xmlhttp.responseText); 
			//locations = [];
			//origins = [];
			languages = [];

			for (var i = 0; i < raw_data["languages"].length; i++) {
				item = new ListItem();
				item.name = raw_data["languages"][i];
				languages.push(item);
			}

			for (var key in raw_data)
			{
				if ( key == "languages" ) {
					continue;
				}

				// organize location data
				for (var i = 0; i < raw_data[key].length; i++) {
					loc = new ListItem();
					loc.name = raw_data[key][i]["name"];
					if (key == "regions" || key == "cities")
					{
						loc.name += ", " + raw_data[key][i]["country_name"];
						if (key == "regions")
							loc.type = "rc";
						if (key == "cities")
							loc.type = "cc";
					}
					else
						// it's a country
						loc.type = "co";
					
					origins.push(loc);

					// also push to locations,
					// BUT ONLY CITIES
					if (key == "cities")
						locations.push(loc);
				}
			}
				
			// get an initial sample of list items
			li_languages = languages.slice(0,4);
			li_origins = origins.slice(0,4);
			li_locations = locations.slice(0,4);

			searchBar.initialize();
		}
	}

	xmlhttp.open("POST", "searchbar-data.php", true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send("data=initial");
}

function SearchBar() {
	var searchOne = document.getElementById("search-1");
	var searchTwo = document.getElementById("search-2");
	var topic = document.getElementById("search-topic");
	var queryUl = document.getElementById("s-query");
	var varUl = document.getElementById("s-var");
	var locUl = document.getElementById("s-location");
	var loc_select, q_select, cur_query;

	this.initialize = function() {
		// FILL UP LISTS
		fillUl(queryUl, queryList);
		fillUl(locUl, li_locations);

		// ADD EVENTS
		searchOne.onfocus = displayMainList;
		searchTwo.onfocus = displayMainList;
		searchOne.onblur = hideList;
		searchTwo.onblur = hideList;
		searchOne.onkeydown = updateList;
		searchTwo.onkeydown = updateList;

		queryUl.onmouseover = getChoices;
		queryUl.onmouseout = closeChoices;
		varUl.onmouseover = markSelection;
		locUl.onmouseover = markSelection;

		// position uls
		oneX = searchOne.offsetLeft;
		oneY = searchOne.offsetTop + searchOne.offsetHeight;
		twoX = searchTwo.offsetLeft;
		queryX = oneX;

		queryUl.style.display = "block";
		varX = oneX + queryUl.offsetWidth;
		queryUl.style.display = "none";

		queryUl.style.left = queryX.toString() + "px";
		varUl.style.left = varX.toString() + "px";
		locUl.style.left = twoX.toString() + "px";
		queryUl.style.top = oneY.toString() + "px";
		varUl.style.top  = oneY.toString() + "px";
		locUl.style.top = oneY.toString() + "px";

		//queryUl.style.display = "none";
	}
	
	/* Displays main list
	 *
	 */
	function displayMainList(e) {
		var barId = e.target.getAttribute('id');
		switch(barId)
		{
			case "search-1":
				queryUl.style.display = "block";
				break;
			case "search-2":
				locUl.style.display = "inline";
				break;
		}
	}

	function hideList(e) {
		var barId = e.target.getAttribute('id');
		switch(barId)
		{
			case "search-1":
				if (q_select == undefined)
					q_select = "";
				if (prompt_strings[cur_query] == undefined)
					searchOne.value = opening;
				else
					searchOne.value = prompt_strings[cur_query] + " " + q_select;
				queryUl.style.display = "none";
				varUl.style.display = "none";
				break;
			case "search-2":
				if (loc_select == undefined)
					loc_select = "";
				searchTwo.value = prompt_strings[2] + " " + loc_select;
				locUl.style.display = "none";
				break;
		}

		// RESET
		cur_query = 0;
	}

	/** takes an unordered list and fills them
	 * @param - ul, unordered list dom element
	 * 	  - data, a list of text
	**/
	function fillUl(ul, data) {
		for (var i = 0; i < data.length; i++) {
			var item = document.createElement("LI");
			var itemText = document.createTextNode(data[i].name);
			item.appendChild(itemText);
			ul.appendChild(item);
		}
	}

	function clearUl(ul) {
		while (ul.firstChild)
			ul.removeChild(ul.firstChild);
	}

	/**
	 *  Displays choices after asking for data from the db
	 */
	function getChoices(e) {
		searchOne.value = opening + e.target.innerHTML;
		clearUl(varUl);
		switch (e.target.innerHTML)
		{
			case "speak":
				fillUl(varUl, li_languages.slice(0,4));
				cur_query = 0;	// 0 for lingos
				topic.value = "_l";
				break;
			case "are from":
				fillUl(varUl, li_origins.slice(0,4));
				cur_query = 1; 	// 1 for origin
				topic.value = "";
				break;
		}
			
		// display list
		varUl.style.display = "inline";
		varUl.style.left = "-" + queryUl.offsetWidth.toString + "px";
	}

	function closeChoices(e) {
		if (document.activeElement != searchOne)
		{
			clearUl(varUl);
			// close var list
			varUl.style.display = "none";
		}
	}

	function updateList(e) {
		switch (e.target.getAttribute('id'))
		{
			case "search-1":
				// Clear ul
				clearUl(varUl);
				

				// get value of search bar
				raw_val = searchOne.value;
				var vals = raw_val.split(" ");

				// separate prompt from the user query
				// get query, and update the list based on
				//   what the user has chosen
				var query_str = null;
				var index = null;
				var prompt_str = null;
				for (var i = 0; i < queries.length; i++)
				{
					prompt_str = queries[i];
					index = raw_val.indexOf(queries[i]);
					if(index > -1)	
					{
						query_str = raw_val.slice(index + queries[i].length + 1)
						break;
					}
				}

				switch (prompt_str) 
				{
					case "are from" :
						li_origins = origins.search(query_str);
						//li_origins = searchList(query_str, origins);
						// Fill up Ul
						fillUl(varUl, li_origins.slice(0,4));
						
						// accounting for answers that are typed in
						// 	assumes that if the length is one, 
						// 	we havea match
						if (li_origins.length <=  1)
							topic.value = li_origins[0].type; // get type of first and only element
						break;
					case "speak" :
						li_languages = searchList(query_str, languages);
						// Fill up Ul
						fillUl(varUl, li_languages.slice(0,4));
						break;
				}

				// Display the list
				varUl.style.display = "inline";

				// set q_select
				//q_select = query_str;
				break;
			case "search-2":
				// clear
				clearUl(locUl);

				// extract the "Near"
				var query_str = searchTwo.value.slice(5);
				li_locations = locations.search(query_str);
				fillUl(locUl, li_locations.slice(0,4));

				// display the list
				locUl.style.display = "inline";

				// set loc_selected
				//loc_select = query_str;
				break;
		}
	}

	function searchList(term, src) {
		var list = [];
		for (var i = 0; i < src.length; i++)
		{
			match = src[i].name.indexOf(term) == 0;
			if (match)
				list.push(src[i]);
		}
;
		return list;
	}
;
	function markSelection(e) {
		switch(e.target.parentNode.getAttribute('id'))
		{
			case "s-var":
				q_select = e.target.innerHTML;
				if (cur_query == 1) {
					var children = e.target.parentNode.childNodes;
					for (var i = 0; i < children.length; i++) 
						if (e.target == children[i]) break;
					// get location type from parallel list
					topic.value = li_origins[i].type;
				}
				break;
			case "s-location":
				loc_select = e.target.innerHTML;
				cur_query = prompt_strings.length -1;	 // the index for the last prompt
				break;
		}
	}
}

function searchQuery(query, sText = "") {
	var pText = "query=" + query;
	// maybe process sText, then add
	pText += sText;

	// Open xmlhttp request
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{
			return JSON.parse(xmlhttp.responseText);
		}
	}

	xmlhttp.open("POST", "", true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send(pText);
}
