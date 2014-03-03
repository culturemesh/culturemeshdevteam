var opening = "Find people who ";
var queries = ["speak...", "are from..."]
var q_results = [];
var locations = [];

loadInitialData();
searchBar = new SearchBar();

function loadInitialData() {
	var xmlhttp = new XMLHttpRequest();
	
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
		{
			var raw_data = JSON.parse(xmlhttp.responseText);
		}
	}

	xmlhttp.open("GET", "searchbar-data.php", true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send("data=initial");
}

function SearchBar() {
	var searchOne = document.getElementById("search-1");
	var searchTwo = document.getElementById("search-2");
	var queryUl = document.getElementById("s-query");
	var varUl = document.getElementById("s-var");
	var locUl = document.getElementById("s-location");

	// FILL UP LISTS
	fillUl(queryUl, queries);

	// ADD EVENTS
	searchOne.onfocus = displayMainList;
	searchTwo.onfocus = displayMainList;
	searchOne.onblur = hideList;
	searchTwo.onblur = hideList;
	searchOne.onchange = updateList;
	searchTwo.onchange = updateList;

	queryUl.onmouseover = getChoices;
	queryUl.onmouseout = closeChoices;
	
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

	/**
	 *  Displays choices after asking for data from the db
	 */
	function getChoices(e) {
		searchOne.value = opening + e.target.innerHTML;	
		//searchQuery(e.target.innerHTML);

		// display list
		varUl.style.display = "inline";
		varUl.style.left = queryUl.offsetWidth.toString + "px";
	}

	function closeChoices(e) {

	}

	function updateList() {

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
