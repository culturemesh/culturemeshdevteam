var cm = cm || {};

cm.FileUploader = function(o) {

	// load options
	this._options = {
		element: null,
		button: null,
		panel: null,
		errorLabel: null,
		generateFromElement: null,
		maxFiles: 0,
		acceptedFileTypes: [],
		sizeLimit: 0, // in MB
		classes: {
			form: 'fileupload-form',
			panel: 'fileupload-panel',
			button: 'fileupload-button',
			error: 'fileupload-error'
		}
	};

	// extend user things
	cm.extend(this._options, o);
	cm.extend(this, cm.DisposeSupport);

	// important elements
	this._element = this._options.element;
	this._panel = this._createPreviewPanel(this._find(this._element, 'panel'));
	this._button = this._createInputButton(this._find(this._element, 'button'));
	this._errorLabel = this._find(this._element, 'error');

	cm.css(this._errorLabel, {
		color: 'red'
	});

	// input list
	this._inputList = {};
	this._inputList_size = 0;
	this._inputList_i = 0;

	// birth a new input tag
	this._inputList[this._inputList_i] = this._button._createFileInput(this._inputList_i);
	this._inputList_size++;
	this._inputList_i++;
}

cm.FileUploader.prototype = {
	_find: function(parent, type) {
		var element = cm.getByClass(parent, this._options.classes[type])[0];
		if (!element){
		    throw new Error('element not found ' + type);
		}
		return element;
	},
	_displayError: function(msg) {
		// simple for now
		$( this._errorLabel ).text(msg);
		$( this._errorLabel ).show();
		$( this._errorLabel ).delay(4000).fadeOut(1000);
	},
	_checkFile: function(file) {

		var sFileName = file.name;
		var sFileExtension = sFileName.split('.')[sFileName.split('.').length - 1].toLowerCase();
		var iFileSize = file.size;
		var iConvert = (file.size / 1048576).toFixed(2);
		var iSizeLimit = (this._options.sizeLimit / 1048576).toFixed(2);


		// check file size
		if (iFileSize > this._options.sizeLimit) {
			this._displayError('File must be smaller than ' + iSizeLimit + ' MB');
			return false;
		}

		// check if is right extension
		var i = 0;
		for (i; i < this._options.acceptedFileTypes.length; i++) {
			if (sFileExtension == this._options.acceptedFileTypes[i])
				break;
		}

		if (i == this._options.acceptedFileTypes.length) {
			this._displayError('Not an accepted file type');
			return false;
		}

		return true;

		/*
		/// OR together the accepted extensions and NOT it. Then OR the size cond.
		/// It's easier to see this way, but just a suggestion - no requirement.
		if (!(sFileExtension === "pdf" ||
			sFileExtension === "doc" ||
			sFileExtension === "docx") || iFileSize > 10485760) { /// 10 mb
				txt = "File type : " + sFileExtension + "\n\n";
				txt += "Size: " + iConvert + " MB \n\n";
				txt += "Please make sure your file is in pdf or doc format and less than 10 MB.\n\n";
				alert(txt);
			}
			*/
	},
	_clearPost: function() {

		// get parent element
		var length = $( '.fileupload-button' ).children().length;

		// remove all but the last element
		for (var i = 0; i < length-1; i++) {
			$( '.fileupload-button input:first' ).remove();
			delete this._inputList[i];
		}

		// move last thing to first place, if images were uploaded
		if (length > 1) {
			this._inputList[0] = this._inputList[i];
			delete this._inputList[i];
		}
		
		// reset inputList vars
		this._inputListSize = 1;
		this._inputList_i = 0;

		this._panel._clearImages();
	},
	_createInputButton: function(element) {
		var self = this;

		cm.css(element, {
			color:'white',
			position: 'relative',
			overflow: 'hidden'
		});
		
		var button = new cm.InputButton({
			element: element,
		    multiple: this._options.multiple && cm.UploadHandlerXhr.isSupported(),
		    acceptFiles: this._options.acceptFiles,
		    inputName: 'fileupload[]',
		    onChange: function(input){
			    self._onInputChange(input);
		    },
		    inputLabeledBy: $(this._options.element).attr('input-labeled-by')
		});

		return button;	
	},
	_createPreviewPanel: function(element) {
		var self = this;

		var panel = new cm.PreviewPanel({
			element: element
		});

		return panel;
	},
	_onInputChange: function(input) {
		// check to see if there's room
		if (this._button._element.childNodes.length > this._options.maxFiles) {
			this._displayError('You are only allowed to submit ' + this._options.maxFiles + ' files');
			return false;
		}

		// check files to see if they pass muster
		for (var i=0; i<input.files.length; i++) {
			if (!this._checkFile(input.files[i]))
				return false;
		}

		var inputList = this._inputList;
		var InputButton = this._button;

		// create a delete button
		var button = document.createElement('button');
		cm.addClass(button, 'upload-img-delete')

		button.innerHTML = '&#10006';
		button.onclick = function(e) {
			e.preventDefault();
			// remove input from existence
			input.parentElement.removeChild(input);
			delete inputList[input.arrayIndex];
		}

		this._panel._addImages(input, button);

		cm.css(input, {
			display: 'none'
		});

		// tell button to create a new input
		this._inputList[this._inputList_i] = this._button._createFileInput(this.inputList_i);
		this._inputList_i++;	// increment current index 
	},
	_removeBlankInput: function() {
		var button = this._button._element;
		var inputs = button.childNodes;
		var inputList_i = this.inputList_i;
		var lastI = inputs.length - 1;

		if (inputs[lastI].value === "")
			button.removeChild(inputs[lastI]);
	},
	_reinstateInput: function() {
		this._button._createFileInput(this.inputList_i);
	}
}

cm.InputButton = function(o) {

	this._options = {
		element: null,
		multiple: true,
		acceptFiles: true,
		inputLabeledBy: null,
		inputList: null,
		onChange: function(input) {},
		hoverClass: 'qq-upload-button-hover',
		focusClass: 'qq-upload-button-focus'

	}

	// user options n jazz
	cm.extend(this._options, o);
	cm.extend(this, cm.DisposeSupport);

	this._element = this._options.element;
	this._curInput = null;
}

cm.InputButton.prototype = {
	_createFileInput: function(index) {

		var input = document.createElement("input");
		input.arrayIndex = index;

		if (this._options.multiple){
			input.setAttribute("multiple", "multiple");
		}

		if (this._options.acceptFiles) input.setAttribute("accept", this._options.acceptFiles);

		input.setAttribute("type", "file");
		input.setAttribute("name", this._options.inputName);

		if (this._options["inputLabeledBy"]) {
			input.setAttribute("aria-labelledby", this._options["inputLabeledBy"]);
		}

		cm.css(input, {
			position: 'absolute',
			// in Opera only 'browse' button
			// is clickable and it is located at
			// the right side of the input
			right: 0,
			top: 0,
			fontFamily: 'Arial',
			// 4 persons reported this, the max values that worked for them were 243, 236, 236, 118
			fontSize: '118px',
			margin: 0,
			padding: 0,
			cursor: 'pointer',
			opacity: 0
		});

		this._element.appendChild(input);

		var self = this;
		this._attach(input, 'change', function(){
			self._options.onChange(input);
		});

		this._attach(input, 'mouseover', function(){
			cm.addClass(self._element, self._options.hoverClass);
		});
		this._attach(input, 'mouseout', function(){
			cm.removeClass(self._element, self._options.hoverClass);
		});
		this._attach(input, 'focus', function(){
			cm.addClass(self._element, self._options.focusClass);
		});
		this._attach(input, 'blur', function(){
			cm.removeClass(self._element, self._options.focusClass);
		});

		// IE and Opera, unfortunately have 2 tab stops on file input
		// which is unacceptable in our case, disable keyboard access
		if (window.attachEvent){
			// it is IE or Opera
			input.setAttribute('tabIndex', "-1");
		}

		return input;
	}
}

cm.PreviewPanel = function(o) {

	this._options = {
		element: null
	};	

	cm.extend(this._options, o);
	cm.extend(this, cm.DisposeSupport);

	this._element = this._options.element;

	// create an awesome ul
	this._ul = document.createElement('ul');
	this._element.appendChild(this._ul);

	// CSS PARTY!!!
	// make button suitable container for input
	cm.css(this._element, {
		overflow: 'hidden',
		// Make sure browse button is in the right side
		// in Internet Explorer
		direction: 'ltr'
	});

	cm.css(this._ul, {
		display: 'inline',
		'listStyleType': 'none'
	});
}

cm.PreviewPanel.prototype = {

	_addImages: function(input, deleteButton) {
		var reader = new FileReader();

		var ul = this._ul;
		var div = document.createElement('div');
		var li = document.createElement('li');
		
		deleteButton.ul = ul;
		deleteButton.div = div;
		deleteButton.li = li;

		cm.css(li, {
			width: '100px',
			'cssFloat': 'left'
		});

		cm.css(deleteButton, {
			width: '100px',
		});

		this._attach(deleteButton, 'click', function() {

			// redeclare for closure purposes

			$( deleteButton.li ).slideUp('slow', 'easeInQuad', function() {
				deleteButton.ul.removeChild(deleteButton.li);
			});
		});

		// counts how many times event has fired
		var counter = 0;
		var target = input.files.length;
		
		// create new images,
		// append to div
		// finally, append to panel
		reader.onload = function (e) {

			var img = document.createElement('img');
			img.src = e.target.result;

			// css
			cm.css(img, {
				width: '100px',
				height: '100px'
			});

			div.appendChild(img);
			counter++;

			// if this is the last image
			if (counter >= target) {
				// append stuff
				div.appendChild(deleteButton);
				li.appendChild(div);
				$( li ).hide();
				ul.appendChild(li);
				$( li ).slideDown('slow', 'easeInQuad');
			}
		}

		// add images
		for (var i=0; i < target; i++)
			reader.readAsDataURL(input.files[i]);
	},
	_clearImages: function() {
		// probably add a fade
		$( this._ul ).children().fadeOut('slow', function() {
			$( this._ul ).empty();
		});
	}
}

cm.PostSubmit = function(o, FileUpload) {
	// this._form
	// this._action
	// this._onSuccess
	// this._onFailure
	
	this._options = {
		submit: null,
		form: null,
		action: null,
		ajax: null,
		submitInsert: function() {},
		onSuccess: function(data) {},
		onFailure: function(data) {},
		classes: {
			submit: 'fileupload-submit'
		}
	};

	cm.extend(this._options, o);
	cm.extend(this, cm.DisposeSupport);

	// elements
	this._form = this._options.form;
	this._clickStart = this._find(this._form, 'submit');

	// stuff
	this._action = this._options.action;

	// functions
	this._submitInsert = this._options.submitInsert;
	this._onSuccess = this._options.onSuccess;
	this._onFailure = this._options.onFailure;

	// file uploader
	this._FileUpload = FileUpload;

	// attach on click event
	var self = this;


	if (this._options.ajax === true) {

		// prevent action
		this._attach(self._clickStart, 'click', function(e) {
			e.preventDefault();
			self._submit(e);
		});
	}
}

cm.PostSubmit.prototype = {
	_find: function(parent, type) {
		var element = cm.getByClass(parent, this._options.classes[type])[0];
		if (!element){
			throw new Error('element not found ' + type);
		}
		return element;
	},
	_submit: function(e) {

		var self = this;
		var fup = this._FileUpload;
		var resetInput = fup._removeBlankInput();

		// get form data
		var formData = new FormData(this._form);

		var ajx = new cm.Ajax({
		    requestType: 'POST',
		    requestUrl: this._action,
		    requestParameters: ' ',
		    data: formData,
		    dataType: 'FormData',
		    sendNow: true
		}, function(data) { 
			fup._reinstateInput();
			data = JSON.parse(data);
			self._onSuccess(data);
		}, function(data) {
			fup._reinstateInput();
			var ff = self._onFailure.bind(data);
			ff();
		});
	},
	_clearPost: function() {

		// replace with specified post area later
		$('.post-text').val('');

		// cancel images
		this._FileUpload._clearPost();
	},
	_setOnSuccess: function(f) {
		this._onSuccess = f;
	},
}
