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
	operation.reset();
	operation.loadPanel();
	operation.activateThings();
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
