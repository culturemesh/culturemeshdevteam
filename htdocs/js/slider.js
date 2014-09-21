addClassNameListener("event-wall", Slider);

function Slider() {
	var content = document.getElementById("slider-content");
	var table = document.getElementById("slider-table");
	var leftButton = document.getElementById("slider-left");
	var rightButton = document.getElementById("slider-right");

	// just for a moment, activate nav
	//var postTab = document.getElementById("post-wall");
	//eventTab.className = "active";


	var cardLength = 275;
	var timeLength = 75;
	var interval = 15;
	var MOVE_SPEED = 30;

	layEmOut();
	content.cmMaxScroll = content.scrollLeftMax | content.scrollWidth; 

	leftButton.onclick = function () {
		//moveRight();
		var interval = 0;

		while (interval < 300)
		{
			setTimeout(moveLeft, interval);
			interval += MOVE_SPEED;
		}
	}

	rightButton.onclick = function () {
		//moveRight();
		var interval = 0;

		while (interval < 300)
		{
			setTimeout(moveRight, interval);
			interval += MOVE_SPEED;
		}
	}

	// organizes all the tds
	function layEmOut() {
		var totalX = 0;
		for (var i = 0, col; col = table.rows[0].cells[i]; i++)
		{
			col.style.left = totalX;
			// increment totalX
			if (col.className == "event-card card")
				totalX += cardLength + interval;
			if (col.className == "event-card month")
				totalX += timeLength + interval;
		}

		//alert(totalX);
		// get maxscroll
		content.cmMaxScroll = totalX;//content.scrollLeftMax | content.scrollWidth;
	}

	function moveRight() {
		if (content.scrollLeft < content.cmMaxScroll)
			content.scrollLeft += MOVE_SPEED;
	}

	function moveLeft() {
		if (content.scrollLeft >= 0)
			content.scrollLeft -= MOVE_SPEED;
	}

}

function addClassNameListener(elemId, callback) {
    var elem = document.getElementById(elemId);
    var lastClassName = elem.className;
    window.setInterval( function() {   
       var className = elem.className;
        if (className !== lastClassName) {
            callback();   
            lastClassName = className;
        }
    },10);
}
