var badChars = ["\'", "\"", "?", "\\"];
function validateInput(element, errorElement, charLimit)
{
    // search for asshole characters ', ", 
    var badCharFound = -1;
    
    for(c in badChars)
    {
    	badCharFound = element.value.indexOf(c);
    	
    	// stop as soon as bad character is found
    	if (badCharFound > -1)
    		alert("bad char found");
    		break;
    }
    if (element.value.length > charLimit)
    {
    	    /// Input was too long
    	errorElement.innerHTML = "Too many characters. Use " + charLimit + " or less.";    
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
