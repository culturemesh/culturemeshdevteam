// imps
var ts = $('#table-select');
var json_cols = [];
var table_cols;
var reqd_fields;

// prime template
var adtRow = $('#table_temp').html();
Mustache.parse(adtRow);

var adtRowCond = $('#table_temp_cd_show').html();
Mustache.parse(adtRowCond);

var adtRowCondHide = $('#table_temp_cd_hide').html();
Mustache.parse(adtRowCondHide);

//prime header
var adtHeader = $('#table_header').html();
Mustache.parse(adtHeader);

// reset selectse`
resetSelects();

/*
var test = $('#test').html();
Mustache.parse(test);
var tr = Mustache.render(test, {text: " is there anybody in there?"});
$('#track').html(tr);
*/

var divs = ['Insert', 'Update'];
/*
var divs = ['insert-single', 'insert-batch',
   		'update-single', 'update-batch'];
	*/

closeAllDivs($('op-select option:selected').text());

// resets initial selects
function resetSelects() {
	$('#table-select :nth-child(1)').prop('selected', true);
	$('#op-select :nth-child(1)').prop('selected', true);
}

function closeAllDivs(exception) {
	if (exception === undefined) {
		exception = '';
	}

	// CLOSE ALL DIVS
	for (var i = 0; i < divs.length; i++)
	{
		// skip if there's an exception
		if (exception == divs[i])	
			continue;
		
		$('#' + divs[i]).hide();
	}
	
	$('#insert-single').hide();
	$('#insert-batch').hide();
	$('#update-single').hide();
	$('#update-batch').hide();

}

// fils insert cols table
function fillTable() {
//	$('#track').text(JSON.stringify(table_cols));
	for (var i = 0; i < reqd_fields['required'].length; i++) {
		var r1 = reqd_fields['required'][i];
		var render = Mustache.render(template, 
				{col : r1,
				 val : null});
		
		$('#insert-cols > tbody:last').append(render);
	}
}

// clears insert cols table
function clearTable() {
	$('#insert-cols > tbody').html('');
}

// 1) PICK TABLE
$('#table-select').on('change', function() {

	// get select data
	var ts_text = $('#table-select option:selected').text();

	// only if 
	/*
	if (reqd_fields == undefined) {
	*/

		/*
		var ts = new Ajax({
			requestType: 'GET',
			requestUrl: 'admin/reqd-fields.json',
			requestParameters: ' ',
			data: null,
			dataType: null,
			sendNow: true
			}, function(data) {
				var stuff = JSON.parse(data);

				reqd_fields = data[ts_text];
				table_cols = reqd_fields[ts_text];
			});
			*/
		$.get('admin/reqd-fields.json', null,
			function( data ) {
				reqd_fields = data[ts_text];
				table_cols = reqd_fields;
				//fillTable();
			}, 'json');
		/*
	}
	else {
		// just check to see if table is in the thing
		table_cols = reqd_fields;
		//fillTable();
	}
	*/
});

// 2) PICK OPERATION
$('#op-select').on('change', function() {

	// get select data
	var opsel_text = $('#op-select option:selected').text();

	closeAllDivs(opsel_text);

	$('#'+opsel_text).show();
});

// TOGGLE BATCH OR SINGLE INPUT
$("input:radio").change(function() {//on('change', function() {

	
	// glad i went with batosing instead of singobat

	// get selected option
	var opsel_text = $('#op-select option:selected').text();

	// change form that is displayed
	if ($('input:radio[name=batising]:checked').val() == 'Single') {
		// display single input form
		//
		$('#insert-batch').hide();
		$('#insert-single').show();
	}
	else {
		// display batch input form
		$('#insert-single').hide();
		$('#insert-batch').show();
	}

	// change form that is displayed
	if ($('input:radio[name=batusing]:checked').val() == 'Single') {
		// display single input form
		//
		$('#update-batch').hide();
		$('#update-single').show();
	}
	else {
		// display batch input form
		$('#update-single').hide();
		$('#update-batch').show();
	}
});

// VALIDATE
// -- insert
$('#insert-parse').on('click', function(e) {
	e.preventDefault();
	var form = document.getElementById('insert-val');


	var formData = new FormData(form);
	// add other stuff
	// ajax
	formData.append('ajax', true);

	// add table
	var ts_text = $('#table-select > option:selected').text();
	formData.append('table', ts_text);
	
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
				clearTable();

				// load up table with stuff
				json_cols = result.cols;

				var ic_table = $('#insert-cols > tbody');

				// make row data
				var ic_tdata = {};

				var tc_count = 0;

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
			}
		});
});

$('#insert-send').on('click', function(e) {
	e.preventDefault();

	// itemize table data
	var tRows = $('#insert-cols > tbody').children();

	// json object structure
	// [{dbcol : ____,
	//   inscol : ____},
	//   ...]
	
	tableData = [];

	for (var i = 0; i < tRows.length; i++) {
		var rowData = {}

		// skip headers
		if ( $(tRows[i]).hasClass('sec_header'))
			continue;

		// get table stuff
		//  it's a bit unruly, i know
		rowData['dbcol'] = $(tRows[i]).children(0)[0].innerHTML; //.children().text();
		rowData['inscol'] = $(tRows[i]).children(1).children().children('option:selected')[0].value;

		tableData.push(rowData);
	}

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

	// add table
	var ts_text = $('#table-select option:selected').text();
	formData.append('table', ts_text);

	var is = new Ajax({
		requestType: 'POST',
		requestUrl: 'admin/admin_ops.php',
		requestParameters: ' ',
		data: formData,
		dataType: 'FormData',
		sendNow: true
		}, function(data) {
			var result = JSON.parse(data);
		});
});

// -- update

$('#update-validate').on('click', function() {
	var formData = $('update-form').serialize();

});
