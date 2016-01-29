/*
 * package {
 * 	table : "",
 * 	op : "",
 * 	singobatch : "",
 *	data : {
 *		col1 : val1,
 *		col2 : val2,
 *		.... : ....
 *		}
 *	}
 */

var sqlTypeDict = {
	'name' : 'string',
	'id' : 'int',
	'region_id': 'int',
	'region_name': 'string',
	'country_id': 'int',
	'country_name': 'string',
	'latitude': 'float',
	'longitude': 'float',
	'population': 'int',
	'num_speakers': 'int'
};

function ModPackage(operation, singobatch, table) {
	// constructor props
	this.operation = operation;
	this.singobatch = singobatch;

	// other props
	this.table = table;
	this.data = {};
	this.original_data = {};

	this.op = 'MP';

	if (operation == 'update')
	{
		this.modCols = [];
	}
}

// set the table name
ModPackage.prototype.setTable = function(table) {
	this.table = table;
}

// set singobatch
ModPackage.prototype.setSingobatch = function(value) {
	this.singobatch = value;
}

// set operation
ModPackage.prototype.setOperation = function(op) {
	this.operation = op;
}

// set package contents 
ModPackage.prototype.setPackage = function(cols, values) {

	// clear data
	this.data = {};

	// set key value pairs
	for (var i = 0; i < cols.length; i++) {
		this.data[cols[i]] = values[i];		
	}
}

ModPackage.prototype.parseTable = function(tableId) {

	// name => db_col : first thing
	// value => td-edit-value.span,
	// 	   td-edit-value.input
	//	   db_edit
	//	   col_match
	var data = {};

	var tdClasses = ['td.op_val.filled span',
	    'td.op_val span',
	    'td.op_input input'
	];

	var modCols = this.modCols;

	var tableString = '#' + tableId + ' table tbody tr';

	$( tableString ).each(function() {
		
		// GET TDs
		var key = $( this ).children('td.db_col').text(); 
		key = stringParse(key);

		// loop through possible table values
		var value = "";
		var count = 0;

		while ( value == "" && count < tdClasses.length) { 
			var elem = $( tdClasses[count], this )

			if (stringParse($( elem ).text()) === "")
				value = stringParse($( elem ).val());
			else
				value = stringParse($( elem ).text());

			// reset to this
			if (value == undefined)
				value = "";

			count++;
		}

		// add to modified columns
		//
		if (modCols != undefined)
		{
			if (count == 1) 
				modCols.push(key);
		}

		// complete col object
		data[key] = value;
	});

	// assign this.data
	this.data = data;
}

// submit to the server
ModPackage.prototype.submitPackage = function() {

	var target = document.getElementById('spin');
	var spinner = new Spinner(opts).spin(target);

	var submitObj = this;
	var submit = new Ajax({
		requestType: 'POST',
		requestUrl: 'admin/admin_ops.php',
		requestParameters: ' ',
	  	data: submitObj,
	 	dataType: 'JSON',
		sendNow: true
		}, function(data) { 

			// stop loading scrn
			spinner.stop();

			// get result
			var result = JSON.parse(data);
		});

}

ModPackage.prototype.processPackage = function() {

	var keys = Object.keys(this.data);

	// add escape quotes to string things
	for (var i = 0; i < keys.length; i++) {

		if (sqlTypeDict[keys[i]] == 'string') {
			this.data[keys[i]] = "\'" + this.data[keys[i]] + "\'";
		}
	}
}
