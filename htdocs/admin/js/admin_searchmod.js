// declare loading spinner
var opts = {
  lines: 9, // The number of lines to draw
  length: 25, // The length of each line
  width: 15, // The line thickness
  radius: 29, // The radius of the inner circle
  corners: 1, // Corner roundness (0..1)
  rotate: 28, // The rotation offset
  direction: 1, // 1: clockwise, -1: counterclockwise
  color: '#000', // #rgb or #rrggbb or array of colors
  speed: 1, // Rounds per second
  trail: 27, // Afterglow percentage
  shadow: false, // Whether to render a shadow
  hwaccel: false, // Whether to use hardware acceleration
  className: 'spinner', // The CSS class to assign to the spinner
  zIndex: 2e9, // The z-index (defaults to 2000000000)
  top: '50%', // Top position relative to parent
  left: '50%' // Left position relative to parent
};

//
var batchParse = function(e) {

	// prevent default behavior
	e.preventDefault();

	var form = document.getElementById('insert-val');

	var adtRow = document.getElementById('batch-tr').innerHTML;

	var formData = new FormData(form);
	// add other stuff
	// ajax
	formData.append('ajax', true);

	// add table
	formData.append('table', operation.getTable());
	
	var insval = new Ajax({
		requestType: 'POST',
		requestUrl: 'admin/admin_ops.php',
		requestParameters: ' ',
		data: formData,
		dataType: 'FormData',
		sendNow: true
		}, function(data) {
			var result = JSON.parse(data);

			if (result.error == 0) {
				
				//alert('It worked');
				// clear table
//				clearTable();

				// load up table with stuff
				var json_cols = result.cols;
				var table_cols = result.table_cols;

				var ic_table = $('#insert-cols > tbody');

				// make row data
				var ic_tdata = {};

				var tc_count = 0;

				for (var i = 0; i < table_cols.length; i++) {

					// important vars
					var tableVal = table_cols[i]['COLUMN_NAME'];

					if (table_cols[i]['IS_NULLABLE'] == 'YES')
					{}

					var row = Mustache.render(adtRow, {
						class: '',
						col : tableVal,
						vals : result.cols
					});

					// add first row
					$( ic_table ).append(row);
				}
					// make a row out of table column
					//
				/*
				for (var key in table_cols) {
					if (key == 'conditional')
						continue;

					if (!table_cols.hasOwnProperty(key)) {
						continue;
				  	}

					var track = table_cols[key];

					if (table_cols[key].length <= 0) {
						continue;
					}

					// render header for new section
					var header = Mustache.render(adtHeader, {
						key : key
					});

					// append header to table
					$( ic_table ).append(header);

					// add db column picker and append row
					for (var i = 0; i < table_cols[key].length; i++) {
						// important vars
						var tableVal = table_cols[key][i];
						var possibility = table_cols['conditional'][tableVal]; // maybe a conditional value

						$( ic_table ).append(row);

						// check for conditional thing
						if (possibility != undefined) {
							var row = Mustache.render(adtRow, {
								class: 'cond',
								col : tableVal,
								vals : result.cols
							});

							// add first row
							$( ic_table ).append(row);

							var row = Mustache.render(adtRow, {
								class : 'cond_hide',
								col : possibility,
								vals : result.cols
							});

							// add second row
							$( ic_table ).append(row);
						}
						else {
							// generate row
							var row = Mustache.render(adtRow, {
								col : tableVal,
								vals : result.cols
							});

							// add row
							$( ic_table ).append(row);
						}
					}
				}
				*/

				/*
				// hide all hide rows
				$('.cond_hide').hide();

				// add function for conditional table rows
				$('.cond').on('change', function(e) {

					// activate row below
					if ( e.target.value != '-') {
						var thing = $( e.target ).parents('tr').next();
						$( thing ).show();
					}

					// deactivate row below
					else {
						$( e.target ).next().hide();
					}
				});
				*/
			}
		});
};

var batchSend = function(e) {
	e.preventDefault();

	// itemize table data
	var tRows = $('#insert-cols > tbody').children();

	// json object structure
	// [{dbcol : ____,
	//   inscol : ____},
	//   ...]
	
	var tableData = [];
	var colMap = {};

	var modCols;
	if (operation.operation == 'update')
		modCols = [];

	$('#insert-cols > tbody > tr').each(function() {
		var rowData = {};

		// get table stuff
		//  it's a bit unruly, i know
		rowData['dbcol'] = $('td.db_col b', this).text()
		rowData['inscol'] = $('td.col_match select option:selected', this).val();

		if ( $('td.update-cb input', this).prop('checked') )
			modCols.push(rowData['dbcol']);		

		// add to colmap
		colMap[rowData['dbcol']] = rowData['inscol'];

		tableData.push(rowData);

	});

	/*
	for (var i = 0; i < tRows.length; i++) {
		var rowData = {};

		// skip headers
		if ( $(tRows[i]).hasClass('sec_header'))
			continue;

		// get table stuff
		//  it's a bit unruly, i know
		rowData['dbcol'] = $(tRows[i]).children(0)[0].textContent
			|| $(tRows[i]).children(0)[0].innerText; //.children().text();
		rowData['inscol'] = $(tRows[i]).children(1).children().children('option:selected')[0].value;

		// add to colmap
		colMap[rowData['dbcol']] = rowData['inscol'];

		// if it's blank, there weren't any mods made
		// to it, so we DON'T CARE!!
		if (rowData['inscol'] != '-') {

			if (modCols != undefined) {
				modCols.push(rowData['dbcol']);
				
			}
		}

		tableData.push(rowData);
	}
	*/

	// init form DATA stuff
	var form = document.getElementById('insert-form');
	var fileElem = document.getElementById('insert-obj');

	var formData = new FormData(form);

	// add other stuff
	// a) ajax
	// b) file
	// c) table rows
	formData.append('ajax', true);
	formData.append('object', fileElem.files[0]);
	formData.append('columnData', JSON.stringify(tableData));
	formData.append('colmap', JSON.stringify(colMap));
	formData.append('op', operation.operation);
	formData.append('singobatch', operation.singobatch);

	// add modCols
	if (modCols != undefined) {
		formData.append('mod_cols', modCols);
	}

	// add table
//	var ts_text = $('#table-select option:selected').text();
//	formData.append('table', ts_text);
	formData.append('table', operation.table);

	// start the nifty spinner-de-thing
	var target = document.getElementById('spin');
	var spinner = new Spinner(opts).spin(target);

	var is = new Ajax({
		requestType: 'POST',
		requestUrl: 'admin/admin_ops.php',
		requestParameters: ' ',
		data: formData,
		dataType: 'FormData',
		sendNow: true
		}, function(data) {
			// stop the spinner
			spinner.stop();

			// get result
			var result = JSON.parse(data);
		});
};

var singSend = function(e) {

	var mp = new ModPackage(operation.operation, operation.singobatch,
			operation.table);

	// get data
	mp.parseTable('table-div');

	//mp.processPackage();

	// submit
	mp.submitPackage();
}

var editValue = function(e) {

	// prevent default behavior like a person
	e.preventDefault();

	// navigate to brother td
	var otherTd = $( e.target ).parents('.td-edit-toggle').siblings('.td-edit-value');

	// make the alteraaaations
	if ( $( this ).text() == 'Edit' ) {

		$( this ).text('Cancel');

		// hide one thing, show the other
		$( otherTd ).children('span').removeClass('on');
		$( otherTd ).children('span').hide();
		$( otherTd ).children('input').show();
	}
	else {

		$( this ).text('Edit');

		// hide one thing, show the other
		$( otherTd ).children('span').addClass('on');
		$( otherTd ).children('span').show();
		$( otherTd ).children('input').hide();
	}
}

var fk_ref = {
	'region_id' : 'regions',
	'country_id' : 'countries'
};

var callSearchBar = function(col) {

	var sbDiv = document.getElementById('ssearchbar-extra');

	var table = fk_ref[col];

	var temp_bar = new SSearchBar('ssearchbar-extra', operation.operation, operation.singobatch,
       		table);

	temp_bar.setTableDiv('table-search-div');
	temp_bar.fetchValues(table);

	// declare important variables and jazz
	var tableDiv = document.getElementById('table-search-div');
	var template = document.getElementById('disp-searchable-tmpl').innerHTML;
	// sets searchbar submit function,
	// makes it want to put things into
	// a different table
	temp_bar.setSubmit( function(e) {

		// prevent default event behavior.
		e.preventDefault();
		// what did you 
		// think it was gonna do? Cure cancer?
		//
		var formData = {
			op : 'searchSearchables',
			table : table,
			query : temp_bar.getValue()
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
				$('#table-search-div .imp').remove();
				$('#table-search-div .edit-toggle').remove();

				// hide inputs
				$('#table-search-div .edit-input').remove();
			});
		
		// set up fill of the main form
		// get td
		var relevantTd = $( '#table-search-div table#searchable tfoot tr td');

		// delete stupid update button cause it's for tools
		$( '#table-search-div button#update-search' ).remove();

		// add fill button for winners
		$( relevantTd ).html("<button id='table_fill'>Fill</button>");

		// set event for fill button
		$('button#table_fill').on('click', function() {

			var dada = getRowsAndCols(col, 'searchable', 'td-edit-value', 'span');

			// the table that we wanna love,
			// but it just don't got the data yet
			$('#table-div table tbody tr').each(function() {
				
				var curCol = $( this ).children('td.db_col').text()
				curCol = stringParse(curCol);

				// html and check
				var className = 'td-edit autofill';
				var jqClassName = '.td-edit.autofill';
				var newTd = "<td class='" + className + "'>" + dada[curCol] + "</td>";
				var oldTd = '.td-edit-value';

				// find fk and change it
				if (dada[curCol] != undefined &&
					dada[curCol] != "") {

					// delete old version
					$( this ).children( oldTd ).remove();

					var test = $( this ).children( jqClassName );

					// if the td already exists, just rewrite it
					if ( $( this ).children( jqClassName ).length > 0)
						$( this ).children( jqClassName ).html(dada[curCol] );
					else {
						$( this ).children('td.db_col').after( newTd );//children('td.td-edit-value span').text( dada[curCol] );
					}
				}

			});

			// reset divs to NOTHING
			$('#ssearchbar-extra').html('');
			$('#table-search-div').html('');
		});
	});
}

/*
// when we're ready to send the data in
document.getElementById('send').onclick = function() {

	var mp = new ModPackage(operation.op, 
			operation.singobatch);

	mp.submitPackage();
}
*/

function getRowsAndCols(colName, type, valClass, childElem) {

	var tableSelector = null;

	if (type == 'main') {
		tableSelector = '#table table';
	}
	else if (type == 'alt') {
		tableSelector = '#table-search table';
	}

	// check variables
	if (valClass == undefined)
		valClass = '';

	if (childElem == undefined)
		childElem = 'span';

	var data = {};

	// get all the rows and columns in the body
	$( tableSelector + ' tbody tr').each(function() {
		var tabCol = $( this ).children('td.db_col').text();
		var tabVal = $( this ).children('td.' + valClass).children( childElem ).text();

		// get rid of bad stuff
		tabCol = stringParse(tabCol);

		// get rid of annoying suffix
		var colNameFix = colName.replace('_id', '');

		// THIS IS THE LIST OF COLUMN TYPES THAT MAY HAVE PARENTS
		if (tabCol.indexOf('id') > -1 ||

			tabCol.indexOf('name') > -1 || 
			tabCol.indexOf('tweet_terms') > -1) {

		/* MASTER CHANGES
			tabCol.indexOf('name') > -1 ||
			tabCol.indexOf('tweet_terms') > -1 ||
			tabCol.indexOf('tweet_terms_override') > -1) {
			*/
			
			// check for things
			if (tabCol.indexOf('id') == 0)
				tabCol = colNameFix + '_' + 'id';

			if (tabCol.indexOf('name') == 0)
				tabCol = colNameFix + '_' + 'name';

			if (tabCol.indexOf('tweet_terms') == 0 && tabCol.indexOf('tweet_terms_override') != 0)
				tabCol = colNameFix + '_' + 'tweet_terms';

			if (tabCol.indexOf('tweet_terms_override') == 0)
				tabCol = colNameFix + '_' + 'tweet_terms_override';

	/* MASTER CHANGES
			if (tabCol.indexOf('tweet_terms') == 0) {

				if (tabCol.indexOf('tweet_terms_override') == 0)
				  tabCol = colNameFix + '_' + 'tweet_terms_override';
				else
				  tabCol = colNameFix + '_' + 'tweet_terms';
			}
			*/

	/* IGNORE THIS SECTION
			if (tabCol.indexOf('tweet_terms_override') == 0)
				tabCol = colNameFix + '_' + 'tweet_terms_override';

			if (tabCol.indexOf('tweet_terms') == 0 &&
				tabCol.indexOf('tweet_terms_override') == -1)
				tabCol = colNameFix + '_' + 'tweet_terms';
				*/

			data[tabCol] = tabVal;
		}
	});

	return data;
}

stringParse = function(val) {
	if (val == undefined) 
		return undefined;
	else
		return val.replace('\n', '').replace('\n', '').trim();
}
