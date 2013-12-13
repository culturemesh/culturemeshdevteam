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

function getDBConnection(){
    $conn = new mysqli(DB_SERVER,DB_USER,DB_PASS, DB_NAME);
    return $conn;
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