<?php	
define("DOMAIN_URL","http://www.culturemesh.com");
define("SHORT_DOMAIN_URL","culturemesh.com");
define("DOMAIN_NAME","CultureMesh");
define("FACEBOOK_URL","");
define("TWITTER_URL","");
define("SUPPORT_EMAIL", "");
define("WEBSITE_BY_URL", "http://www.kostocoastdev.com");

define("JS_HOLDER_64x64", "http://www.kostocoastdev.com/clients/hosted/js/holder.js/64x64");
define("DB_SERVER", "localhost");
define("DB_USER", "culturp7_ktc");
define("DB_PASS", "d4T48@$3");
define("DB_NAME", "culturp7_ktc");

class Email{
    /*public function __construct(){
        $this->Headers = 'From: '.DOMAIN_NAME.' Team <noreply@'.SHORT_DOMAIN_URL.'>' . "\r\n" .
	'Reply-To: noreply@'.SHORT_DOMAIN_URL . "\r\n" .
	'X-Mailer: PHP/' . phpversion();
    }
    public function __construct(){
        $this->Headers = 'From: '.$this->From. "\r\n" .
	'Reply-To: '.$this->ReplyTo . "\r\n" .
	'X-Mailer: PHP/' . phpversion();
    }*/
    public function __construct(){
        $this->Headers = 'From: noreply@culturemesh.com'. "\r\n" .
	'Reply-To: noreply@culturemesh.com'. "\r\n" .
	'X-Mailer: PHP/' . phpversion();
    }
    public function setTo($to){
        $this->To = $to;
    }
    public function setFrom($from){
        $this->From = $from;
    }
    public function setReplyTo($to){
        $this->ReplyTo = $to;
    }
    public function setSubject($subject){
        $this->Subject = $subject;
    }
    public function setMessage($message){
        $this->Message = $message;
    }
    public function Send(){
        if(gettype($this->To) == "array"){
            foreach($this->To as $addressee){
                mail($addressee, $this->Subject, $this->Message, $this->Headers);
            }
        }//if list of emails
        else{
            mail($this->To, $this->Subject, $this->Message, $this->Headers);
        }//if 1
    }
    public function __destruct(){
        //echo "Successfully ";
    }
}

class SiteEmail extends Email{
    /*public function __construct(){
        $this->Headers = 'From: CultureMesh Website Form Submission <noreply@culturemesh.com>' . "\r\n" .
	'Reply-To: '.$this->ReplyTo . "\r\n" .
	'X-Mailer: PHP/' . phpversion();
    }*/
}

function getStates(){
    $states_array = array("al"=>"alabama","ak"=>"alaska", "az"=>"arizona", "ar"=>"arkansas","ca"=>"california",
       "co"=>"colorado","ct"=>"connecticut","de"=>"delaware","fl"=>"florida","ga"=>"georgia","hi"=>"hawaii",
       "id"=>"idaho","il"=>"illinois","in"=>"indiana","ia"=>"iowa","ks"=>"kansas","ky"=>"kentucky","la"=>"louisiana",
       "me"=>"maine","md"=>"maryland","ma"=>"massachusetts","mi"=>"michigan","mn"=>"minnesota","ms"=>"mississippi",
       "mo"=>"missouri","mt"=>"montana","ne"=>"nebraska","nv"=>"nevada","nh"=>"new hampshire","nj"=>"new jersey",
       "nm"=>"new mexico","ny"=>"new york","nc"=>"north carolina","nd"=>"north dakota","oh"=>"ohio","ok"=>"oklahoma",
       "or"=>"oregon","pa"=>"pennsylvania","ri"=>"rhode island","sc"=>"south carolina","sd"=>"south dakota",
       "tn"=>"tennessee","tx"=>"texas","ut"=>"utah","vt"=>"vermont","va"=>"virginia","wa"=>"washington",
       "wv"=>"west virginia","wi"=>"wisconsin","wy"=>"wyoming");
    return array_keys($states_array);
}
function getCountries(){
    $countries_array = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");
    return $countries_array;
}
function getDBConnection(){
    $conn = new mysqli(DB_SERVER,DB_USER,DB_PASS, DB_NAME);
    return $conn;
}
function insertQuery($query){
    $conn = getDBConnection();
    $stmt = $conn->prepare($query);
    $stmt->execute();
    return $conn->affected_rows;
}
function deleteQuery($query){
    $conn = getDBConnection();
    $stmt = $conn->prepare($query);
    $stmt->execute();
    return $conn->affected_rows;
}
function getRowQuery($query){
    $conn = getDBConnection();
    $result = $conn->query($query);
    return $result->fetch_assoc();
}
function getRowsQuery($query){
    $data = array();
    $conn = getDBConnection();
    $result = $conn->query($query);
    while($row = $result->fetch_assoc()){
        $data[] = $row;
    }
    return $data;
}
function getAffectedRows(){
    $conn = getDBConnection();
    return $conn->affected_rows;
}
function adminUpdateRegion($spec, $name){
    if(insertQuery("INSERT INTO networks (".$spec.",date_added) values('".$name."',".time().")") == 1){
        return '1';
    }
}
function getNetworkCities(){
    return getRowsQuery("SELECT city FROM networks");
}
function getNetworkCountries(){
    return getRowsQuery("SELECT country FROM networks");
}
function getSuggestedNetworks(){
    return getRowsQuery("SELECT * FROM suggested_networks");
}
function sendEmailNotification($email, $mailsubject, $message){
	$headers = 'From: '.DOMAIN_NAME.' <noreply@'.SHORT_DOMAIN_URL.'>' . "\r\n" .
	'Reply-To: noreply@'.SHORT_DOMAIN_URL. "\r\n" .
	'X-Mailer: PHP/' . phpversion();
	mail($email, $mailsubject, $message, $headers);
}
function getIsEmailAvailable($email){
    $code = "";
    $conn = getDBConnection();
    $result = $conn->query("SELECT email_address FROM users WHERE email_address='{$email}'");
    if(!$result){
        $code = "1";
    }
    return $code;
}
function buildModal($header, $body, $footer, $modal_id = "modal"){
    $modal = '<div id="'.$modal_id.'" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="blogPostLabel" aria-hidden="true">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>'.
            $header.'
        </div>
        <div class="modal-body">'.
          $body.'
        </div>
        <div class="modal-footer">'.
            $footer.'
        </div>
      </div>';
    return $modal;
}
function buildAdminEditNetworkModal(){
    $modal = '<div id="admin_edit_network_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="blogPostLabel" aria-hidden="true">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h2 class="text-center">Edit Network</h2>
        </div>
        <div class="modal-body text-center">
            Type: <span style="text-transform:uppercase;" id="emodal_network_type"></span>
            <span id="emodal_network_name" class="hide"></span>
            <br><input type="text" id="admin_add_network_input" />
        </div>
        <div class="modal-footer">
            <input type="submit" id="admin_add_network_btn" class="center-elem cm-button" value="Save Changes & Add" />
        </div>
        <script>
        $("#admin_add_network_btn").click(function(){
            $.post("ajx/ps.php", {admin_add_network:$("#admin_add_network_input").val(),admin_add_network_type:$("#modal_network_type").html()})
            .done(function(data){
                if(data == "1"){
                    window.location.reload();
                }
            });
        });
        </script>
      </div>';
    return $modal;
}
function buildAdminRemoveNetworkModal(){
    $modal = '<div id="admin_remove_network_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="blogPostLabel" aria-hidden="true">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h2 class="text-center">Remove Network</h2>
        </div>
        <div class="modal-body text-center">
            <span id="rmmodal_network_type" class="hide"></span>
          Are you sure you want to remove \'<span id="rmmodal_network_name"></span>\' from \'Suggested Networks\'?
        </div>
        <div class="modal-footer">
            <input type="submit" class="center-elem cm-button" id="admin_remove_network_confirm_btn" value="Confirm Removal" />
        </div>
        <script>
        $("#admin_remove_network_confirm_btn").click(function(){
            $.post("ajx/ps.php", {admin_remove_network:$("#modal_network_name").html(),admin_remove_network_type:$("#modal_network_type").html()})
            .done(function(data){
                if(data == "1"){
                    window.location.reload();
                }
            });
        });
        </script>
      </div>';
    return $modal;
}
function getCurrentFilename($path){
    $finfo = pathinfo($path);
    return $finfo['filename'];
}
    $q_prefix = $finfo['filename'];
/*
function getMonths(){
	$months = array("january","february","march","april","may","june","july","august","september","october","november","december");
	return $months;
}

/*******end sql functions******************************************************************/

/*
function returnFormattedDate($utc, $format){
	$formatteddate = date($format, $utc);
	return $formatteddate;
}

function returnMDYFormat($utc){
	$MDYformat = date('F j, Y', $utc);
	return $MDYformat;
}

function returnMDYTFormat($utc){
	$MDYTformat = date('F j, Y - g:i a', $utc);
	return $MDYTformat;
}

function getYesterdayUnix(){
	$yesterdayunix = mktime(0, 0, 0, date('n'), (date('j')-1), date('Y'));
	return $yesterdayunix;
}

function getSameTimeYesterdayUnix(){
	$yesterdayunix = mktime(date('G'), date('i'), date('s'), date('n'), (date('j')-1), date('Y'));
	return $yesterdayunix;
}

function getPresentUnix(){
	$presentunix = mktime(date('G'), date('i'), date('s'), date('n'), date('j'), date('Y'));
	return $presentunix;
}

function getTomorrowUnix(){
	$tomorrowunix = mktime(0, 0, 0, date('n'), (date('j')+1), date('Y'));
	return $tomorrowunix;
}

function getSameTimeTomorrowUnix(){
	$tomorrowunix = mktime(date('G'), date('i'), date('s'), date('n'), (date('j')+1), date('Y'));
	return $tomorrowunix;
}

function timeStringToUTC($hour, $minutes, $timeofday){
	if(strtolower($timeofday) == "am"){
		if($hour == 12){
			$hour = 0;
		}
	}
	else if(strtolower($timeofday) == "pm"){
		if($hour != 12){
			$hour = $hour+12;
		}
	}
	$UTCtime = strtotime($hour.":".$minutes);
	
	return $UTCtime;
}

function stringDateTimeToUTC($hour, $minutes, $month, $day, $year, $timeofday){
	if(gettype($month) == "string"){
		$m = date_parse($month);
		$month = $m['month'];
	}//end if text month is given
	//TODO:replace with better checker
	if(strtolower($timeofday) == "am"){
		if($hour == 12){
			$hour = 0;
		}
	}
	else if(strtolower($timeofday) == "pm"){
		if($hour != 12){
			$hour = $hour+12;
		}
	}
	$UTCtime = mktime($hour, $minutes, 0, $month, $day, $year);
	return $UTCtime;
}
function sendNotification($school, $to, $from, $message){
	if(!is_dir($school.'/'.$to.'/notifications')){
		mkdir($school.'/'.$to.'/notifications');
	}
	$notfile = $school.'/'.$to.'/notifications/'.strtotime("now").".txt";
	$notificationfh = fopen($notfile, 'w');
	$message ="<img src='".displayPic($from, $school)."' height='15'/>".$message."\nunchecked";
	fwrite($notificationfh, $message);
	fclose($notificationfh);
}

function curPageURL() {
	 $pageURL = 'http';
	 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	 $pageURL .= "://";
	 if ($_SERVER["SERVER_PORT"] != "80") {
	  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	 } else {
	  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	 }
	 return $pageURL;
}

function getDir($filenameparam){
	$finfo = pathinfo($filenameparam);
	$dir = strtolower($finfo['dirname']);
	return $dir;
}

function getExt($filenameparam){
	$finfo = pathinfo($filenameparam);
	$ext = strtolower($finfo['extension']);
	return $ext;
}

function getBaseName($filenameparam){
	$finfo = pathinfo($filenameparam);
	$bname = $finfo['basename'];
	return $bname;
}
*/
function getFileName($filenameparam){
    $finfo = pathinfo($filenameparam);
    $fname = $finfo['filename'];
    return $fname;
}
/*
function buildModal($header, $body, $footer, $modal_id = "modal"){
    $modal = '<div id="'.$modal_id.'" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="blogPostLabel" aria-hidden="true">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>'.
            $header.'
        </div>
        <div class="modal-body">'.
          $body.'
        </div>
        <div class="modal-footer">'.
            $footer.'
        </div>
      </div>';
    return $modal;
}

function rrmdir($dir) { 
   if (is_dir($dir)) { 
     $objects = scandir($dir); 
     foreach ($objects as $object) { 
       if ($object != "." && $object != "..") { 
         if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object); 
       } //
     } //foreach
     reset($objects); 
     rmdir($dir); 
   } //if
 } //function
*/
?>