var opening = "Find people who ";
var queries = ["speak", "are from"]
var prompt_strings = ["Find people who speak",
			"Find people who are from"];
var q_results, locations, origins, li_origins, li_locations, languages, li_languages;
/*
var locations = [];
var li_origins = [];
var cur_locations = [];
var languages = [];
var li_languages = [];
*/

loadInitialData();
searchBar = new SearchBar();

function loadInitialData() {
	// Create ajax request
	var xmlhttp = new XMLHttpRequest();
	
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{
			raw_data = JSON.parse(xmlhttp.responseText); 
			languages = raw_data["languages"];
			locations = [];
			origins = [];
			
			for (var key in raw_data)
			{
				if ( key == "languages" ) {
					languages = raw_data["languages"];
					continue;
				}

				for (var i = 0; i < raw_data[key].length; i++) {
					loc_string = raw_data[key][i]["name"];
					if (key == "regions" || key == "cities")
					{
						loc_string += ", " + raw_data[key][i]["country_name"];
					}
					origins.push(loc_string);

					// also push to locations,
					// BUT ONLY CITIES
					if (key == "cities")
						locations.push(loc_string);
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
	var queryUl = document.getElementById("s-query");
	var varUl = document.getElementById("s-var");
	var locUl = document.getElementById("s-location");

	this.initialize = function() {
		// FILL UP LISTS
		fillUl(queryUl, queries);
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
				queryUl.style.display = "none";
				varUl.style.display = "none";
				break;
			case "search-2":
				locUl.style.display = "none";
				break;
		}
	}

	/** takes an unordered list and fills them
	 * @param - ul, unordered list dom element
	 * 	  - data, a list of text
	**/
	function fillUl(ul, data) {
		for (var i = 0; i < data.length; i++) {
			var item = document.createElement("LI");
			var itemText = document.createTextNode(data[i]);
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
				fillUl(varUl, li_languages);
				break;
			case "are from":
				fillUl(varUl, li_origins);
				break;
		}
			
		// display list
		varUl.style.display = "inline";
		varUl.style.left = queryUl.offsetWidth.toString + "px";
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
						li_origins = searchList(query_str, origins);
						// Fill up Ul
						fillUl(varUl, li_origins.slice(0,4));
						break;
					case "speak" :
						li_languages = searchList(query_str, languages);
						// Fill up Ul
						fillUl(varUl, li_languages.slice(0,4));
						break;
				}

				// Display the list
				varUl.style.display = "inline";
				break;
			case "search-2":
				// clear
				clearUl(locUl);

				// extract the "Near"
				var query_str = searchTwo.value.slice(5);
				li_locations = searchList(query_str, locations);
				fillUl(locUl, li_locations.slice(0,4));

				// display the list
				locUl.style.display = "inline";
				break;
		}
	}

	function searchList(term, src) {
		var list = [];
		for (var i = 0; i < src.length; i++)
		{
			match = src[i].indexOf(term) == 0;
			if (match)
				list.push(src[i]);
		}

		return list;
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
