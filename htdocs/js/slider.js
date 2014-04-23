var content = document.getElementById("slider-content");
var table = document.getElementById("slider-table");
var leftButton = document.getElementById("slider-left");
var rightButton = document.getElementById("slider-right");

content.cmMaxScroll = content.scrollLeftMax | content.scrollWidth;

var cardLength = 275;
var timeLength = 75;
var interval = 15;
var MOVE_SPEED = 30;

layEmOut();

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
}

function moveRight() {
	if (content.scrollLeft < content.cmMaxScroll)
		content.scrollLeft += MOVE_SPEED;
}

function moveLeft() {
	if (content.scrollLeft >= 0)
		content.scrollLeft -= MOVE_SPEED;
}


