var operation = new Operation();

function Operation() {
	
	// selex an divs
	this.opselect = document.getElementById('op-select');
	this.sibatselect = document.getElementById('sibat-select');
	this.tabselect = document.getElementById('table-select');

	// party div
	this.opPanel = document.getElementById('op-panel');

	// templates
	this.tsTmpl = $('#table-select-tmpl').html();

	// other stuff
	this.operation = this.opselect.value;
	this.singobatch = this.sibatselect.value;
	this.table = this.tabselect.value;
}

Operation.prototype.getTable = function() {
	return this.tabselect.value;
}

Operation.prototype.reset = function() {

	// other stuff
	this.operation = this.opselect.value;
	this.singobatch = this.sibatselect.value;
	this.table = this.tabselect.value;
}

/*
Operation.prototype.loadPanel = function() {

	// stuff
	var id;
	var validator = new Validator();
	var opname = this.operation;

	// get id of template
	if (this.singobatch === 'single') {
		id = this.operation + '-' + this.singobatch + '-tmpl';
	} else {
		id = 'batch-tmpl';
	}

	var opPanel = this.opPanel;
	var opTmpl = document.getElementById(id).innerHTML;

	var formData = {
		op : 'getTableStructure',
		table : this.table
	};

	// ajax stuff gets 
	var get = new Ajax({
		requestType: 'POST',
		requestUrl: 'admin/admin_ops.php',
		requestParameters: ' ',
		data: postStringify(formData),
		dataType: 'string',
		sendNow: true
		}, function(data) {
			var result = JSON.parse(data);

			// init cols
			var cols = [];

			// get table columns, figure out their
			// validations
			var keys = result['keys'];

			for (var i=0; i< result['description'].length; i++) {
			
				var obj = result['description'][i];

				var vObj = new ValidationObject(
						obj['IS_NULLABLE'],
						obj['DATA_TYPE'],
						{ 
							CHARACTER_MAX_LENGTH : obj['CHARACTER_MAX_LENGTH'],
							NUMERIC_PRECISION : obj['NUMERIC_PRECISION'],
				    			NUMERIC_SCALE : obj['NUMERIC_SCALE']
						});

				var validationString = validator.getParsleyString(vObj);

				var colClass = '';

				if (obj['COLUMN_NAME'].indexOf('id') > 0)
					colClass = 'fk';

				else if (obj['COLUMN_NAME'].indexOf('id') == 0)
					colClass = 'ignore';

				else if (obj['COLUMN_NAME'].indexOf('name') > 0)
					colClass = 'fk_child';

				// add to awesomeness array
				cols.push({
					name : obj['COLUMN_NAME'],
					validation : validationString,
					colClass : colClass
				});
			}

			// render and set template
			opPanel.innerHTML = Mustache.render(opTmpl, 
					{ columns : cols });

			// destroy bad things, hide other things
			// show important things
			$('.db_edit.fk').remove();
			$('.db_edit.ignore').html('');
			$('.db_edit.fk_child').html('<span></span>');
			$('.db_find.fk_child').remove();
			$('.db_find').hide();
			$('.db_find.fk').show();
		});
}
*/

// loads template and puts it in panel
Operation.prototype.loadPanel = function() {

	// load panel
	var opPanel = document.getElementById('op-panel');
	var tmpl = document.getElementById('single-op-tmpl').innerHTML; 

	// set panel's html
	opPanel.innerHTML = Mustache.render(tmpl);
}

// activates first thing, and sets the stage
// for what follows
Operation.prototype.activatePanel = function() {

	// ALL - get table structure
	// if create
	// 	- load searchable w/ blanks
	//
	if (this.operation == 'create' &&
			this.singobatch == 'single') {

		var tableInfo;

		// create form data
		var formData = {
			op : 'getTableStructure',
			table : this.table
		};

		// get structure of the table
		var get = new Ajax({
			requestType: 'POST',
			requestUrl: 'admin/admin_ops.php',
			requestParameters: ' ',
			data: postStringify(formData),
			dataType: 'string',
			sendNow: true
			}, function(data) {
				tableInfo = JSON.parse(data);
			});

		this.fillSearchable(tableInfo, 'main', this.operation);
		this.activateFinds();
		this.activateSend();
	}

	// if update
	// 	- load searchbar and tell it to
	// 	create a searchable later with
	// 	awesome function
	if (this.operation == 'update' &&
			this.singobatch == 'single') {

		// activate search bar
		var sbDiv = document.getElementById('ssearchbar');

		// create search bar
		var searchbar = new SSearchBar('ssearchbar', this.operation, this.singobatch, this.table);
		searchbar.setTableDiv('table-div');
		searchbar.fetchValues(this.table);

		// give the searchbar a reference to this operation
		var sbOp = this;
		var tables = this.table;
		var tableDiv = document.getElementById('table-div');

		// somehow, i must give the searchbar
		// the target div		
		searchbar.setSubmit(function(e) {
			// prevent default event behavior.
			e.preventDefault();

			// it's submitting the search results
			// i could get it to return the search results here
			var val = searchbar.getValue();

			// what did you 
			// think it was gonna do? Cure cancer?
			var formData = {
				op : 'searchSearchables',
				table : tables,
				query : val
			};

			var result = null;

			// get search results, and never
			// darken our doorway with this again
			var searchSbmt = new Ajax({
				requestType: 'POST',
				requestUrl: 'admin/admin_ops.php',
				requestParameters: ' ',
				data: formData,
				dataType : 'JSON',
				sendNow: true
				}, function(data) { 
				// should return with a handsome table
				result = JSON.parse(data);
			});

			// use operation to fill searchable
			// I did it! I gave it the target div!
			// And I only have to define it once!
			sbOp.fillSearchable(result, 'main', sbOp.operation);
			sbOp.activateFinds();
			sbOp.activateUpdate();
			sbOp.activateSend();
		});
	}

	// if batch
	// 	- load batch template
	if (this.operation == 'update' &&
			this.singobatch == 'batch') {

				/*
			// get search results, and never
			// darken our doorway with this again
			var addJSON = new Ajax({
				requestType: 'POST',
				requestUrl: 'admin/admin_ops.php',
				requestParameters: ' ',
				data: formData,
				dataType : 'JSON',
				sendNow: true
				}, function(data) { 
				// should return with a handsome table
				result = JSON.parse(data);
			});
			*/
	}
	
}

Operation.prototype.fillSearchable = function(tableInfo, rank, operation) {

	// search div
	var tabDiv;

	var tableCond = {
		main : null,
		alt : null,
	};

	// figure out which div we're working with
	if (rank == 'main') {
		tabDiv = document.getElementById('table');
		tableCond.main = true;
	}
	else if (rank == 'alt') {
		tabDiv = document.getElementById('table-search');
		tableCond.alt = true;
	}

	// get searchable template
	var tmpl = document.getElementById('re-searchable-tmpl').innerHTML;

	// NOW BEGIN FILLING THE JAZZ
	// init cols
	var cols = [];

	// get table columns, figure out their
	// validations
	var keys = tableInfo['keys'];

	for (var i=0; i< tableInfo['description'].length; i++) {
	
		// a json of row conditionals fa yo ass
		var colCond = {
			update : null,
			display : null,
			create : null,
			find : null,
		};

		if (operation == 'create') {
			colCond.create = true;
		}
		else if (operation == 'update') {
			colCond.update = true;
		}
		else if (operation == undefined) {
			colCond.display = true;
		}

		// get col info
		var obj = tableInfo['description'][i];

		/*
		var vObj = new ValidationObject(
				obj['IS_NULLABLE'],
				obj['DATA_TYPE'],
				{ 
					CHARACTER_MAX_LENGTH : obj['CHARACTER_MAX_LENGTH'],
					NUMERIC_PRECISION : obj['NUMERIC_PRECISION'],
					NUMERIC_SCALE : obj['NUMERIC_SCALE']
				});

		var validationString = validator.getParsleyString(vObj);
		*/

		//var colClass = '';

		// IF WE AREN'T AN ALT S-ABLE
		//
		// check for id or name columns,
		// treat accordingly
		//
		// - find buttons for region_id, country_id
		// - only display for id col
		// - only display for name col
		//
		if (rank == 'main') {

			if (obj['COLUMN_NAME'].indexOf('id') > 0) {
				colCond.display = true;
				colCond.find = true;
				colCond.create = null;
				colCond.update = null;
			}

			else if (obj['COLUMN_NAME'].indexOf('id') == 0) {
				colCond.display = true;
				colCond.create = null;
				colCond.update = null;
			}

			else if (obj['COLUMN_NAME'].indexOf('name') > 0) {
				colCond.display = true;
				colCond.create = null;
				colCond.update = null;
			}
		}

		colCond['fart'] = true;

		// add to awesomeness array
		cols.push({
			name : obj['COLUMN_NAME'],
			value : obj['value'],
		//	validation : validationString,
			colCond : colCond
		});


	}

	tmplData = {
		columns : cols,
		cond : tableCond,
		george : {
			test : true
		}};

	// render and set template
	tabDiv.innerHTML = Mustache.render(tmpl, tmplData);

	// destroy bad things, hide other things
	// show important things
	/*
	$('.db_edit.fk').remove();
	$('.db_edit.ignore').html('');
	$('.db_edit.fk_child').html('<span></span>');
	$('.db_find.fk_child').remove();
	$('.db_find').hide();
	$('.db_find.fk').show();
	*/
}

Operation.prototype.activateFinds = function() {

	// give a ref to mr operation
	var sbOp = this;

	$('.find').on('click', function(e) {
		
		// the old standby
		e.preventDefault();

		//
		// get column name
		var curCol = $( this ).parents('td.op_action').siblings('td.db_col').text();
		curCol = stringParse(curCol);
		var table = fk_ref[curCol];

		// call searchbar
		var temp_bar = new SSearchBar('ssearchbar-alt', operation.operation, operation.singobatch,
			table);

		temp_bar.setTableDiv('table-search');
		temp_bar.fetchValues(table);

		// tell searchbar how to fill searchable
		temp_bar.setSubmit(function(e) {

			// ehhhhhhhhhh prevent default
			e.preventDefault();

			// it's submitting the search results
			// i could get it to return the search results here
			var val = temp_bar.getValue();

			// what did you 
			// think it was gonna do? Cure cancer?
			var formData = {
				op : 'searchSearchables',
				table : table,
				query : val
			};

			var result = null;

			// get search results, and never
			// darken our doorway with this again
			var searchSbmt = new Ajax({
				requestType: 'POST',
				requestUrl: 'admin/admin_ops.php',
				requestParameters: ' ',
				data: formData,
				dataType : 'JSON',
				sendNow: true
				}, function(data) { 
				// should return with a handsome table
				result = JSON.parse(data);
			});

			// use operation to fill searchable
			// I did it! I gave it the target div!
			// And I only have to define it once!
			sbOp.fillSearchable(result, 'alt');

			// set up fill of the main form
			// get td @ footer
			var relevantTd = $( '#table-search table#searchable tfoot tr td');

			// set event for fill button
			$('button.fill').on('click', function() {

				// dada is an object full of all the wonderful searchable
				// data that we want
				var dada = getRowsAndCols(curCol, 'alt', 'op_val', 'span');

				// the table that we wanna love,
				// but it just don't got the data yet
				$('#table table tbody tr').each(function() {
					
					var curCol = $( this ).children('td.db_col').text()
					curCol = stringParse(curCol);

					// find fk and change it
					if (dada[curCol] != undefined &&
						dada[curCol] != "") {

						//var test = $( jqClassName, this ).html(dada[curCol]);
						// insert value
						$( 'td.op_val span', this ).html(dada[curCol]);

						// may have to change classes,
						// but that's for aother time
						if (!$( 'td.op_val', this ).hasClass('filled'))
							$( 'td.op_val', this ).addClass('filled');
					}

				});

				// reset divs to NOTHING
				$('#ssearchbar-alt').html('');
				$('#table-search').html('');
			});
		});
	});
}

Operation.prototype.activateUpdate = function() {

	// hide input fields
	$( 'td.op_input input' ).hide();
	$( 'td.op_action button.submit-edit').hide();

	// give change button its action
	// when clicked, show the input field,
	// show the submit-edit field
	$( 'td.op_action button.edit-toggle' ).on('click', function(e) {

		// prevent default action
		e.preventDefault();

		// get things
		var input = $( this ).parents( 'td.op_action' ).siblings( 'td.op_input' ).children( 'input' );
		var editSbmt = $( this ).siblings( 'button.submit-edit' );
		var valSpan = $( this ).parents( 'td.op_action' ).siblings( 'td.op_val' ).children( 'span' );

		// show if not shown
		if ( $( input ).is(':hidden')) {
			$( input ).show();
			$( valSpan ).hide();
			$( editSbmt ).show();
			$( this ).html('Cancel');
		}
		// or close if shown
		else {
			$( input ).hide();
			$( valSpan ).show();
			$( editSbmt ).hide();
			$( this ).html('Edit');
		}
	});

	// give submit-edit its action
	// when clicked, fill sibling with input value
	$( 'td.op_action button.submit-edit').on('click', function(e) {

		// prevent default
		e.preventDefault();

		// get value
		var val = $( this ).parents('td.op_action').siblings('td.op_input').children('input').val();

		// get input elem and edit toggle
		var input = $( this ).parents( 'td.op_action' ).siblings( 'td.op_input' ).children( 'input' );
		var editTgl = $( this ).siblings( 'button.edit-toggle' );

		// parse, because I know i'll have to
		val = stringParse(val);

		// mark as filled if it hasn't been
		// too long a thing, will shorten later
		if (! $( this ).parents('td.op_action').siblings('td.op_val').hasClass('filled'))
			$( this ).parents('td.op_action').siblings('td.op_val').addClass('filled');

		// fill and show value
		$( this ).parents('td.op_action').siblings('td.op_val').children('span').html(val);
		$( this ).parents('td.op_action').siblings('td.op_val').children('span').show();

		// hide things
		$( input ).val('');
		$( input ).hide();
		$( this ).hide();
		$( editTgl ).html('Edit');
	});
}

Operation.prototype.activateSend = function() {

	// set up submit button
	sendBtn = document.getElementById('sendBtn');

	// operation variable
	var sbOp = this;

	// send function
	sendBtn.onclick = function(e) {

		// prevent world destruction by ants
		e.preventDefault();

		var mp = new ModPackage(sbOp.operation, sbOp.singobatch,
			sbOp.table);

		// get data
		mp.parseTable('table');

		mp.operation = 'test';

		var target = document.getElementById('spin');
		var spinner = new Spinner(opts).spin(target);

		var submitObj = this;
		var submit = new Ajax({
			requestType: 'POST',
			requestUrl: 'admin/admin_ops.php',
			requestParameters: ' ',
			data: mp,
			dataType: 'JSON',
			sendNow: true
			}, function(data) { 

				// stop loading screen
				spinner.stop();

				// get result
				var result = JSON.parse(data);

				if (result.error == 0) {
					$('#error-msg').show();
					$('#error-msg').text('Operation successful!');					
					$('#error-msg').delay(1000).hide(500);

					// other things
				}
				else {
					$('#error-msg').show();
					$('#error-msg').text(result.error_msg);
					$('#error-msg').delay(1000).hide(1500);
				}
				
			});
	}

}
Operation.prototype.activateThings = function() {
	
	// activate search bar
	var sbDiv = document.getElementById('ssearchbar');

	if (sbDiv != null){
		var searchbar = new SSearchBar('ssearchbar', this.operation, this.singobatch, this.table);
		searchbar.setTableDiv('table-div');
		searchbar.fetchValues(this.table);
	}

	// activate send buttons 
	if (document.getElementById('batch-parse') != null)
		document.getElementById('batch-parse').onclick = batchParse;

	if (document.getElementById('send') != null)
		if (this.singobatch == 'batch') 
			document.getElementById('send').onclick = batchSend;
		else
			document.getElementById('send').onclick = singSend;


	// find buttons need call search bar function
	$( '.find' ).on('click', function(e) {
		var col = $( e.target ).parents('td.db_find').siblings('td.db_col').text();
		callSearchBar(col);
	});
}

document.getElementById('commence').onclick = function() {

	operation.reset();	// get rid of old stuff
	operation.loadPanel();	// fill panel
	operation.activatePanel(); // activate all gadgets
}

valid_key = {
	varchar : 'string',
	char : 'string',
	int : 'number',
	bigint : 'number',
	float : 'number'
};

num_key = {
	int : 'int',
	bigint : 'int',
	float : 'float'
};

function ValidationObject(required, type, length) {

	this.required = required;
	this.type = type;

	var typeTrans = valid_key[type];
	var numTrans = num_key[type];

	// decide length
	if (typeTrans === 'string')
		this.length = length['CHARACTER_MAXIMUM_LENGTH'];
	
	if (numTrans === 'int') {
		this.length = length['NUMERIC_SCALE']
	}
	else if (numTrans === 'float') {
		this.length = [length['NUMERIC_PRECISION'],
			length['NUMERIC_SCALE']];
	}
	
}

function Validator() {


	this.parPre = 'data-parsley-';

	this.parsleyNigma = {
		string : "type='alphanum'",
		number : "type='number'",
		maxLength : "maxlength=",
		min : "min=",
		max : "max="
	}
}

Validator.prototype.decideValidation = function(string, nullable) {

	// match
	var len_re = '';
	var pos_len_re = string.search(len_re);

	// type is first thing, length is last
	var type = string.slice(0, len_re);
	var len_raw = string.slice(len_re);

	// break up length
	var len = this.prototype.parseLength(len_raw);

	// still no way to tell if required
	var vObj = new ValidationObject(type, len);

	return vObj;
}

Validator.prototype.getParsleyString = function(vobj) {
	var pString = '';

	// append required
	if (vobj.required == true) {
		pString += this.parPre += 'required' + ' ';
	}

	// append type
	pString += this.parPre + this.parsleyNigma[vobj.type] + ' ';

	if (vobj.length instanceof Array) {
		// do nothing for now
	}
	else {
		// append length
		pString += this.parPre + this.parsleyNigma.maxLength + "\"" + vobj.length + "\"";
	}

	return pString;
}

Validator.prototype.parseLength = function(string) {

	// take off first and last characters
	var phaseOne = string.substring(1, string.length-1);

	// remove any commas
	var length = phaseOne.split(',');

	return {
		length : length[0],
		depth : length[1]
	};
}
