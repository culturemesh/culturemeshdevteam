var badChars = ["'", "\"", "?", "\\"];

function validateInput(element, errorElement, charLimit = -1)
{
    // search for asshole characters ', ", 
    var badCharFound = -1;
    
    for(var i = 0; i < badChars.length; i++)
    {
    	    badCharFound = element.value.indexOf(badChars[i]);
    	
    	//alert(badCharFound);
    	// stop as soon as bad character is found
    	if (badCharFound > -1)
    	{
    		break;
    	}
    }
    if (charLimit > -1)
    {
	    if (element.value.length > charLimit)
	    {
		    /// Input was too long
		errorElement.innerHTML = "Too many characters. Use " + charLimit + " or less.";    
	    }
    }
    else if (badCharFound > -1)
    {
    	    /// Use of invalid characters
    	errorElement.innerHTML = "Only use the specified characters"; // figure out what these are
    }
    else
    {
    	errorElement.innerHTML = "";    
    }
}

function comparePasswordInput(element, compareElement, errorElement)
{
    if (element.value != compareElement.value)
    {
    	errorElement.innerHTML = "Passwords do not match.";
    }
    
    else
    {
    	errorElement.innerHTML = "";
    }
}
