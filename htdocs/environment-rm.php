<?php

define("DOMAIN_URL","http://www.culturemesh.com");
define("SHORT_DOMAIN_URL","culturemesh.com");
define("DOMAIN_NAME","CultureMesh");
define("FACEBOOK_URL","");
define("TWITTER_URL","");
define("SUPPORT_EMAIL", "");
define("WEBSITE_BY_URL", "http://www.kostocoastdev.com");

define("JS_HOLDER_64x64", "http://www.kostocoastdev.com/clients/hosted/js/holder.js/64x64");

if ( file_exists('../localdbconn.php'))
{
    include  "../localdbconn.php";
}
else if ( file_exists('../../localdbconn.php'))
{
    include  "../../localdbconn.php";
}
else
{
    define("DB_SERVER", "localhost");
    define("DB_USER", "culturp7");

    if ( file_exists("../../../abcd123.php")) {
    	include "../../../abcd123.php";
    }
    else if ( file_exists("../../../../abcd123.php")) {
  	include "../../../../abcd123.php";
    }
}
 
define("IMG_DIR", "../../user_images/");
define("BLANK_IMG", 'images/blank_profile.png');

?>
