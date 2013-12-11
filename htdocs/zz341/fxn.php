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
define("DB_USER", "culturp7");
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

function escape_string($string)
{
    $newstring = "";
    
    for ($i = 0; $i < strlen($string); $i = $i + 1)
    {
    	$char = $string[$i];
    	
    	if ($char == "'")
    	{
    	    $newstring = $newstring."\\".$char;
    	}
    	else if ($char == "\"")
    	{
    	    $newstring = $newstring."\\".$char;
    	}
    	else if ($char == "\\")
    	{
    		if (substr($i, $i+1) == "\n")
    		{
    		    $newstring = $newstring."'".substr($i, $i+1)."'";
    		    $i = $i + 1;
    		}
    		else if (substr($i, $i+1) == "\r")
    		{
    		    $newstring = $newstring."'".substr($i, $i+1)."'";
    		    $i = $i + 1;
    		}
    		else if (substr($i, $i+3) == "\x00")
    		{
    		    $newstring = $newstring."'".substr($i, $i+3)."'";
    		    $i = $i + 3;
    		}
    		else if (substr($i, $i+3) == "\x1a")
    		{
    		    $newstring = $newstring."'".substr($i, $i+1)."'";
    		    $i = $i + 3;
    		}
    		else
    		{
    		    $newstring = $newstring."'".$char."'";
    		}
    	}
    	else
    	{
    	    $newstring = $newstring.$char;
    	}
    }
    
    return $newstring;
}
/*
function getMonths(){
	$months = array("january","february","march","april","may","june","july","august","september","october","november","december");
	return $months;
}



function getIsValidCourse($course, $school){
    $conn = getDBConnection();
    $qc = mysql_query("SELECT * FROM courses WHERE name='$course' AND schooldomain='$school'");
    if(mysql_num_rows($qc) >= 1){
        return 1;
    }
    else{
        return 0;
    }
}

function getSchoolCourses($schooldomain){
    $conn = getDBConnection(); 
    $qc = mysql_query("SELECT * FROM courses WHERE schooldomain='$schooldomain'");
    
    $data = array();
        while($row = mysql_fetch_array($qc)){
            $data[] = $row;
        }
	mysql_close($conn);
        return $data;
}

function getMemberID($u_email){
    $conn = getDBConnection();
    $qg = mysql_query("SELECT id FROM users WHERE schemail='$u_email'");
    if(mysql_num_rows($qg) == 1){
        $data = mysql_fetch_array($qg);
        mysql_close($conn);
        return $data['id'];
    }
    
}
//returns school object
function getSchool($member){
	$schoolObject = new School();
	$atfinder = strpos($member, "@");
	$school = strtolower(substr($member, $atfinder+1, strlen($member)));
	
	$schoolObject->setDomain($school);
	switch($school){
		case "msu.edu":
			$schoolObject->setName("Michigan State University");
			$schoolObject->setColor("#18453B");
			$schoolObject->setSecondaryColor("#FFFFFF");
			$schoolObject->setSelectedColor("#113029");
			$schoolObject->setSelectedSecondaryColor("#B2B2B2");
			$schoolObject->setLightGradientColor("#02B26E");
			break;
		case "umich.edu":
			$schoolObject->setName("University of Michigan");
			$schoolObject->setColor("#FFCC33");
			$schoolObject->setSecondaryColor("#000066");
			$schoolObject->setSelectedColor("#B28F24");
			$schoolObject->setSelectedSecondaryColor("#000047");
			$schoolObject->setLightGradientColor("#FFE570");
			break;
		case "stanford.edu":
			$schoolObject->setName("Stanford University");
			$schoolObject->setColor("#8C1515");
			$schoolObject->setSecondaryColor("#FFFFFF");
			$schoolObject->setSelectedColor("#610F0F");
			$schoolObject->setSelectedSecondaryColor("#B2B2B2");
			$schoolObject->setLightGradientColor("#DF4F4F");
			break;
	}
	return $schoolObject;
}
function getSchoolCourseList($school){
    $list = file($_SERVER['DOCUMENT_ROOT']."/static/schools/".$school."/courselist.txt");
    sort($list);
    return $list;
}
function getCurrentFile(){
	$file = str_replace("/", "", $_SERVER["PHP_SELF"]);
	return $file;
}
function getMemberProfilePosts($member){
	$atfinder = strpos($member, "@");
	$school = substr($member, $atfinder+1, strlen($member));
	$posts = glob($_SERVER['DOCUMENT_ROOT']."/".$school."/".$member."/posts/*.post");
	return $posts;
}
function getStudentCourses($user){
	$ca = array();
	$conn = getDBConnection();
	$cq = mysql_query("SELECT * FROM enrolled_courses WHERE schemail='$user' ORDER BY name ASC");
	while($row = mysql_fetch_array($cq)){
		$ca[] = $row;
	}
	return $ca;
}
function getStudentHomework($user){
	$ha = array();
	$conn = getDBConnection();
	$hq = mysql_query("SELECT hw_sort_order FROM users WHERE schemail='$user'");
	$s_data = mysql_fetch_array($hq);
	$sort_order = (!is_null($s_data['sort_order'])) ? $s_data['sort_order'] : "due_date";
	$hwq = mysql_query("SELECT * FROM homework WHERE schemail='$user' ORDER BY ".$sort_order." ASC");
	while($row = mysql_fetch_array($hwq)){
		$ha[] = $row;
	}
	return $ha;
}
function getSectionCourseStudents($course, $section){
	$ca = array();
	$conn = getDBConnection();
	$cq = mysql_query("SELECT * FROM enrolled_courses WHERE name='$course' AND section='$section' ORDER BY name ASC");
	while($row = mysql_fetch_array($cq)){
		$ca[] = $row;
	}
	return $ca;
}
function getCourseStudents($course, $section){
	$ca = array();
	$conn = getDBConnection();
	$cq = mysql_query("SELECT * FROM enrolled_courses WHERE name='$course' ORDER BY name ASC");
	while($row = mysql_fetch_array($cq)){
		$ca[] = $row;
	}
	return $ca;
}
function getMemberCourses($member){
	$atfinder = strpos($member, "@");
	$school = substr($member, $atfinder+1, strlen($member));
	$courses = glob($_SERVER['DOCUMENT_ROOT']."/".$school."/".$member."/courses/*.course");
	return $courses;
}
function removeStudentCourse($course, $section, $member){
	$atfinder = strpos($member, "@");
	$school = substr($member, $atfinder+1, strlen($member));
	$course = $_SERVER['DOCUMENT_ROOT']."/".$school."/".$member."/courses/".$course.$section.".course";
	$bool = 0;
	if(file_exists($course)){
		echo "object";
		unlink($course);
		//$bool = 1;
	}
	else{
		$conn = getDBConnection();
		$cq = mysql_query("SELECT * FROM enrolled_courses WHERE name='$name' AND section='$section' AND schemail='$member'");
		while($row = mysql_fetch_array($cq)){
			$id = $row['id'];
			echo $id;
			mysql_query("DELETE FROM enrolled_courses WHERE id=$id");
		}
		mysql_close($conn);
		//$bool = 1;
	}
	return $bool;
}

function getMemberEvents($member){
	$atfinder = strpos($member, "@");
	$school = substr($member, $atfinder+1, strlen($member));
	$events = glob($_SERVER['DOCUMENT_ROOT']."/".$school."/".$member."/events/*.event");
	return $events;
}
function getMemberDiscussionPosts($member){
	$atfinder = strpos($member, "@");
	$school = substr($member, $atfinder+1, strlen($member));
	$discussionposts = glob($_SERVER['DOCUMENT_ROOT']."/".$school."/".$member."/discussions/*.discussion");
	return $discussionposts;
}
function getMemberNotes($member){
	$atfinder = strpos($member, "@");
	$school = substr($member, $atfinder+1, strlen($member));
	$notes = glob($_SERVER['DOCUMENT_ROOT']."/".$school."/".$member."/notes/*.note");
	return $notes;
}
function createStudyGroup($data, $member){
    $dat = explode("&", urldecode($data));
    $group_invitees = array();
    $error_counter = 0;
    $conn = getDBConnection();
    foreach($dat as $d){
        $spec = explode("=",$d);
        $s = $spec[0];
        $val = $spec[1];
        switch($s){
            case "group_name":
                $group_name = mysql_real_escape_string($val);
                break;
            case "group_course":
                $group_course = mysql_real_escape_string($val);
                break;
            case "group_desc":
                $group_desc = mysql_real_escape_string($val);
                break;
            case "group_invitee[]":
                if(strlen($val) > 0){
                    $group_invitees[] = mysql_real_escape_string($val);
                }
                break;
        }
    }
    if(count($group_invitees)>0){
        foreach($group_invitees as $gi){
            if(!verifySameSchool($gi, $member)){
                $error_counter +=1;
            }
        }
    }
    
    if($error_counter > 0){
        return "2";
    }
    else{
        $mid = intval(getMemberID($member));
        $qg = mysql_query("INSERT INTO study_groups (group_name,group_desc,group_course,created_by,date_created) values(
                '".$group_name."',
                '".$group_desc."',
                '".$group_course."',
                4,
                ".time().")");
        echo 'd'.$mid.'d';
        if(count($group_invitees)>0){
            $sub = DOMAIN_NAME." - Study group invitation";
            $pronouns = getMemberPronouns($member);
            $msg = getMemberFullName($member)." invited you to join ".$pronouns['possessive']." study group: ".$group_name.". Log in to accept or reject this invitation,\r\rRegards, ".DOMAIN_NAME;
            sendBatchEmailNotification($group_invitees, $sub, $msg);
        }        
        return "1";
    }
}
function verifySameSchool($user, $member){
    $u_email = getSchoolDomain($user);//substr($user, strpos($user, "@"), strlen($user));
    $m_email = getSchoolDomain($member);//substr($member, strpos($member, "@"), strlen($member));
    if($u_email == $m_email)
        return "1";
    else
        return "0";
}
function getMemberStudyGroups($m_id){

    $groups = array();
    $conn = getDBConnection();
    $qg = mysql_query("SELECT * FROM study_group_members WHERE user_id='$m_id'");
    while($row = mysql_fetch_array($qg)){
        $groups[] = $row;
    }
    mysql_close($conn);
    return $groups;
}
function getMemberGroupMembers($member){
	$atfinder = strpos($member, "@");
	$school = substr($member, $atfinder+1, strlen($member));
	$groupmembers = array();
	$groups = getMemberStudyGroups($member);
	foreach($groups as $grpin=>$groupobj){
		$pstudygroup = loadObject($groupobj);
		$pcourse = $pstudygroup->getCourse();
		$pid = $pstudygroup->getID();
		
		$maingrppath = $_SERVER['DOCUMENT_ROOT']."/".$school."/courses/".$pcourse."/groups/".$pid.".studygroup";
		if(file_exists($maingrppath)){
			$studygroup = loadObject($maingrppath);
			$sgrpmems = $studygroup->getMembers();
			foreach($sgrpmems as $sgmin=> $member){
				$groupmembers[] = $member;
			}
		}
	}
	return $groupmembers;
}
function getMemberHomework($member){
	$atfinder = strpos($member, "@");
	$school = substr($member, $atfinder+1, strlen($member));
	$homework = glob($_SERVER['DOCUMENT_ROOT']."/".$school."/".$member."/homework/*.homework");
	return $homework;
}
function getMemberFiles($member){
	$atfinder = strpos($member, "@");
	$school = substr($member, $atfinder+1, strlen($member));
	$files = ScandirUnder($_SERVER['DOCUMENT_ROOT']."/".$school."/".$member."/files");
	return $files;
}
function getMemberInbox($member){
	$atfinder = strpos($member, "@");
	$school = substr($member, $atfinder+1, strlen($member));
	$inbox = glob($_SERVER['DOCUMENT_ROOT']."/".$school."/".$member."/messages/inbox/*.message");
	return $inbox;
}
function getMemberOutbox($member){
	$atfinder = strpos($member, "@");
	$school = substr($member, $atfinder+1, strlen($member));
	$inbox = glob($_SERVER['DOCUMENT_ROOT']."/".$school."/".$member."/messages/sent/*.message");
	return $outbox;
}

function uploadGroupLogo($to, $image, $tmp_image){
	$fileinfo = pathinfo($image);					
	$fileinfo['extension'] = strtolower($fileinfo['extension']);
	$thumbloc = $to;
	if(!file_exists($thumbloc)){
		fopen($thumbloc, 'w');
	}
	if($fileinfo['extension'] == "jpg" || $fileinfo['extension'] == "jpeg" || $fileinfo['extension'] == "gif" || $fileinfo['extension'] == "png" || $fileinfo['extension'] == "bmp"){
		if(filesize($tmp_image)< 5000000){
			if ( move_uploaded_file($tmp_image, $thumbloc ) ) {
				echo 'Logo successfully updated!';
			}
			else{
				echo 'Logo update failed. The image may have been too large. Redirecting to your desk...<meta http-equiv="refresh" content="2; url=studygroups.php"/>';
			}
		}//end if filesize lessthan 500kb
		else{
			echo 'Update failed. The image may have been too large. Please try again.';
		}
	}//end if type is image
	else{
		echo 'posted:';
		print_r($_POST);

		echo '<br>imgname:'.$image;

		echo "Invalid image format. Image must be in *.png, *.jpg, *.jpeg, *.bmp, or *.gif format.";
	}
}//end upload group logo

$loa = loadObject($a);
$lob = loadObject($b);
class MessageSort{
	private $a;
	private $b;
	
	public function sortByDateSentAsc($a, $b){
		if($GLOBALS['loa']->getDateSent() == $GLOBALS['lob']->getDateSent()){ return 0 ; } 
		return ($GLOBALS['loa']->getDateSent() < $GLOBALS['lob']->getDateSent()) ? -1 : 1;
	}
	public function sortByDateSentDesc($a, $b){
		if($GLOBALS['lob']->getDateSent() == $GLOBALS['loa']->getDateSent()){ return 0 ; } 
		return ($GLOBALS['lob']->getDateSent() < $GLOBALS['loa']->getDateSent()) ? -1 : 1;
	}
	public function sortByDateReceivedAsc($a, $b){
		if($GLOBALS['loa']->getDateReceived() == $GLOBALS['lob']->getDateReceived()){ return 0 ; } 
		return ($GLOBALS['loa']->getDateReceived() < $GLOBALS['lob']->getDateReceived()) ? -1 : 1;
	}
	public function sortByDateReceivedDesc($a, $b){
		if($GLOBALS['lob']->getDateReceived() == $GLOBALS['loa']->getDateReceived()){ return 0 ; } 
		return ($GLOBALS['lob']->getDateReceived() < $GLOBALS['loa']->getDateReceived()) ? -1 : 1;
	}
	public function sortByTitleAsc($a, $b){
		return strcmp($GLOBALS['loa']->getTitle(), $GLOBALS['lob']->getTitle());
	}
	public function sortByTitleDesc($a, $b){
		return strcmp($GLOBALS['lob']->getTitle(), $GLOBALS['loa']->getTitle());
	}
	public function sortByCourseAsc($a, $b){
		return strcmp($GLOBALS['loa']->getCourse(), $GLOBALS['lob']->getCourse());
	}
	public function sortByCourseDesc($a, $b){
		return strcmp($GLOBALS['lob']->getCourse(), $GLOBALS['loa']->getCourse());
	}
}

class FileSort{
	public function sortByDateCreatedAsc($a, $b){
	  if(filemtime($a) == filemtime($b)){ return 0 ; } 
	  return (filemtime($a) < filemtime($b)) ? -1 : 1;
	}
	public function sortByDateCreatedDesc($a, $b){
	  if(filemtime($b) == filemtime($a)){ return 0 ; } 
	  return (filemtime($b) < filemtime($a)) ? -1 : 1;
	}

	public function sortByFileSizeAsc($a, $b){
	  if(filesize($a) == filesize($b)){ return 0 ; } 
	  return (filesize($a) < filesize($b)) ? -1 : 1;
	}

	public function sortByFileSizeDesc($a, $b){
	  if(filesize($b) == filesize($a)){ return 0 ; } 
	  return (filesize($b) < filesize($a)) ? -1 : 1;
	}
}//end filesort class

class NoteSort{
	//private $loa = loadObject($a);
	//private $lob = loadObject($b);

	public function sortByTitleAsc($a, $b){
	$loa = loadObject($a);
	$lob = loadObject($b);
		return strcmp($loa->getTitle(), $lob->getTitle());
	}
	public function sortByTitleDesc($a, $b){
	$loa = loadObject($a);
	$lob = loadObject($b);
		return strcmp($lob->getTitle(), $loa->getTitle());
	}
	public function sortByDateCreatedAsc($a, $b){
	$loa = loadObject($a);
	$lob = loadObject($b);
	  if($loa->getDateTaken() == $lob->getDateTaken()){ return 0 ; } 
	  return ($loa->getDateTaken() < $lob->getDateTaken()) ? -1 : 1;
	}
	public function sortByDateCreatedDesc($a, $b){
	$loa = loadObject($a);
	$lob = loadObject($b);
	  if($lob->getDateTaken() == $loa->getDateTaken()){ return 0 ; } 
	  return ($lob->getDateTaken() < $loa->getDateTaken()) ? -1 : 1;
	}
	public function sortByLastEditedAsc($a, $b){
	$loa = loadObject($a);
	$lob = loadObject($b);
	  if($loa->getLastEdited() == $lob->getLastEdited()){ return 0 ; } 
	  return ($loa->getLastEdited() < $lob->getLastEdited()) ? -1 : 1;
	}
	public function sortByLastEditedDesc($a, $b){
	$loa = loadObject($a);
	$lob = loadObject($b);
	  if($lob->getLastEdited() == $loa->getLastEdited()){ return 0 ; } 
	  return ($lob->getLastEdited() < $loa->getLastEdited()) ? -1 : 1;
	}
	public function sortByCourseAsc($a, $b){
	$loa = loadObject($a);
	$lob = loadObject($b);
		return strcmp($loa->getCourse(), $lob->getCourse());
	}
	public function sortByCourseDesc($a, $b){
	$loa = loadObject($a);
	$lob = loadObject($b);
		return strcmp($lob->getCourse(), $loa->getCourse());
	}
}//end notesort class

function getIsMemberCourseTutor($course, $member){
    $conn = getDBConnection();
    $qt = mysql_query("SELECT * FROM tutored_courses WHERE schemail='$member' AND course_name='$course'");
    if(mysql_num_rows($qt) == 1){
        return 1;
    }
    else{
        return 0;
    }
}

function addMemberTutoredCourse($course, $years, $price, $member, $school){
    $conn = getDBConnection();
    $qins = mysql_query("INSERT INTO tutored_courses (schemail, course_name, years_tutored_course, tutor_rate, schooldomain) values                                   
                        ('".mysql_real_escape_string($member)."',
                        '".mysql_real_escape_string($course)."',
                        ".$years.",
                        '".$price."',
                        '".mysql_real_escape_string($school)."')");
    mysql_close($conn);
}
function updateMemberTutoredCourse($course, $years, $price, $member, $school){
    $conn = getDBConnection();
    $qt = mysql_query("SELECT * FROM tutored_courses WHERE course_name='$course' AND schemail='$school'");
    if(mysql_num_rows($qt) == 1){
        $qupt = mysql_query("UPDATE tutored_courses SET years_tutored_course='$years', tutor_rate='$price' WHERE schemail='$member' AND course_name='$course'");
    }//end if updating
    mysql_close($conn);
}

function getIsMemberRegisteredTutor($member){
	$conn = getDBConnection();
       $q_user = mysql_query("SELECT * FROM tutored_courses WHERE schemail='$member'");
	if(mysql_num_rows($q_user) >= 1){
		return 1;
             mysql_close($conn);
	}
    else{
        return 0;
    }
}

function getTutorsForCourse($course, $school){
    $conn = getDBConnection();
    $qc = mysql_query("SELECT * FROM tutored_courses WHERE course_name='$course' AND schooldomain='$school'");
    $data = array();
    while($row = mysql_fetch_array($qc)){
        $data[] = $row;
    }
    mysql_close($conn);
    return $data;
}

function getMemberYearsTutoringCourse($course, $tutor){
    $conn = getDBConnection();
    $qt = mysql_query("SELECT years_tutored_course FROM tutored_courses WHERE course_name='$course' AND schemail='$tutor'");
    if(mysql_num_rows($qt) >=1 ){
        $data = mysql_fetch_array($qt);
        return $data['years_tutored_course'];
    }
    mysql_close($conn);
}

function getMemberTutoredCourses($member){
	$conn = getDBConnection();
       $q_user = mysql_query("SELECT * FROM tutored_courses WHERE schemail='$member'");
    $data = array();

    while($row = mysql_fetch_array($q_user)){
        $data[] = $row;
    }
    mysql_close($conn);
    return $data;
}

function getMemberTutorRequestsForCourse($course, $member){
	$conn = getDBConnection();
       $q_user = mysql_query("SELECT request_for,request_for_course,schemail,date FROM tutor_requests WHERE request_for_course='$course' AND request_for = '$member'");
    $data = array();

    while($row = mysql_fetch_array($q_user)){
        $data[] = $row;
    }
    mysql_close($conn);
    return $data;
}

function getMemberTutorRatingsForCourse($course, $member){
	$conn = getDBConnection();
       $q_user = mysql_query("SELECT tutor FROM tutor_ratings WHERE course='$course' AND tutor = '$member'");
    $data = array();

    while($row = mysql_fetch_array($q_user)){
        $data[] = $row;
    }
    mysql_close($conn);
    return $data;
}
function getMemberTutorRateForCourse($course, $member){
	$conn = getDBConnection();
       $q_user = mysql_query("SELECT tutor_rate FROM tutored_courses WHERE course_name='$course' AND schemail = '$member'");
    if(mysql_num_rows($q_user) == 1){
        $result = mysql_fetch_array($q_user);
        return $result['tutor_rate'];
    }
    else{
        return 0;
    }
    mysql_close($conn);
}
function setMemberTutorRateForCourse($member, $course, $price){
	$conn = getDBConnection();
       $q_user = mysql_query("UPDATE tutored_courses SET tutor_rate = $price WHERE course_name='$course' AND schemail = '$member'");
      mysql_close($conn);
}
function getMemberAverageTutorRatingForCourse($course, $member){
    $count = count(getMemberTutorRatingsForCourse($course, $member));
    $ratings = getMemberTutorRatingsForCourse($course, $member);
    $rating_total = 0;
    foreach($ratings as $rating){
       $rating_total += $rating['rating'];
    }

    $r = ($count > 0) ? $rating_total/$count : 0;
    return $r;
}

function getMemberFileSortOrder($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT filesortorder FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$filesortorder = $data['filesortorder'];
		mysql_close($conn);

		return $filesortorder;
	}
}
function setMemberFileSortOrder($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET filesortorder = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberNoteSortOrder($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT notesortorder FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$notesortorder = $data['notesortorder'];
		mysql_close($conn);

		return $notesortorder;
	}
}
function setMemberNoteSortOrder($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET notesortorder = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberMessageSortOrder($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT messagesortorder FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$messagesortorder = $data['messagesortorder'];
		mysql_close($conn);

		return $messagesortorder;
	}
}
function getMemberHomeworkSortOrder($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT homeworksortorder FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$homeworksortorder = $data['homeworksortorder'];
		mysql_close($conn);

		return $homeworksortorder;
	}
}
function getMemberDiscussionSortOrder($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT discussionsortorder FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$discussionsortorder = $data['discussionsortorder'];
		mysql_close($conn);

		return $discussionsortorder;
	}
}
function getMemberStudyGroupSortOrder($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT studygroupsortorder FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$studygroupsortorder = $data['studygroupsortorder'];
		mysql_close($conn);

		return $studygroupsortorder;
	}
}
//make sure full file path is passed as a parameter
function getDisplayFileSize($file){
  if(file_exists($file)){
    $bytes = filesize($file);
    $kB = $bytes/1024;
    $MB = $kB/1024;
    $GB = $MB/1024;
		
    if($kB>0 && $kB<1000){
      $displayedfsize = $kB;
      $ext = "kB";
    }
    else if($MB>0 && $MB<1000){
      $displayedfsize = $MB;
      $ext = "MB";
    }
    else if($GB>0 && $GB<1000){
      $displayedfsize = $GB;
      $ext = "GB";
    }
    $decimalspot = strpos("$displayedfsize", ".");
    $decdig = substr("$displayedfsize", $decimalspot,3);
    $wholedig = substr("$displayedfsize", 0, $decimalspot);

    return $wholedig.$decdig.$ext;
  }//end if file exists
}

function doctorLink($link){
	$end = strpos($link, "/");
	$protocol = substr($link, 0, $end+1);

	if($protocol != "http:/" && $protocol != "https:/"){
		$link = "http://".$link;
	}
	return $link;
}

function arrayContainsObject($array){
	if(is_array($array)){
	$itcounter = 0;
	foreach($items as $itin=>$item){
		if(gettype($item) == "object"){
			$itcounter +=1;
		}//end if contains object
		
		if($itcounter >=1){
			return true;
		}//end if itcounter greater than1
		else{
			return "";
		}
	}//end foreach
	}//end if array
}
function contentFilter($content){
	$filteredContent = stripslashes(htmlspecialchars($content));
	return $filteredContent;
}
function sendMobileNotification($email, $mailsubject, $message){
	$headers = 'From: '.DOMAIN_NAME.' Mobile <noreply@'.SHORT_DOMAIN_URL.'>' . "\r\n" .
	'Reply-To: noreply@'.SHORT_DOMAIN_URL. "\r\n" .
	'X-Mailer: PHP/' . phpversion();
	mail($email, $mailsubject, $message, $headers);
}
function sendBatchEmailNotification($batch, $mailsubject, $message){
    if(is_array($batch)){
        foreach($batch as $email){
            $headers = 'From: '.DOMAIN_NAME.' <noreply@'.SHORT_DOMAIN_URL.'>' . "\r\n" .
            'Reply-To: noreply@'.SHORT_DOMAIN_URL. "\r\n" .
            'X-Mailer: PHP/' . phpversion();
            mail($email, $mailsubject, $message, $headers);
        }
    }
}
function sendEmailNotification($email, $mailsubject, $message){
	$headers = 'From: '.DOMAIN_NAME.' <noreply@'.SHORT_DOMAIN_URL.'>' . "\r\n" .
	'Reply-To: noreply@'.SHORT_DOMAIN_URL. "\r\n" .
	'X-Mailer: PHP/' . phpversion();
	mail($email, $mailsubject, $message, $headers);
}
function loadObject($objectFilePath){
	if(file_exists($objectFilePath)){
		$contents = file_get_contents($objectFilePath);
		$usableObject = unserialize($contents);
		return $usableObject;
	}
	else{
		return "";
	}
}

function limitStringLength($string, $maxlength){
	$condensedString = $string;
	if(strlen($string) > $maxlength){
		$condensedString = substr($string, 0, $maxlength)."...";
	}
	return $condensedString;
}

function getMemberDisplayPic($memberemail){
	$school = getMemberSchoolDomain($memberemail);
	$userprofilepic = $_SERVER['DOCUMENT_ROOT']."/".$school."/".$memberemail."/profilethumb.jpg";
	$defaultprofilepic = DOMAIN_URL."/defaultthumb.png";
	if (!file_exists($userprofilepic)){//if user uploaded profile pic
		$displaypic = $defaultprofilepic;
	}
	else{
		$profthumburl = DOMAIN_URL."/".$school."/".$memberemail."/profilethumb.jpg";
		$displaypic = $profthumburl;
	}
	return $displaypic;
}
*/
/*******start sql functions******************************************************************/
/*
function setMemberUsername($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET username = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberUsername($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT username FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$username = $data['username'];
		mysql_close($conn);

		return $username;
	}
}
function setMemberLastLogin($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET lastlogin = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberPassword($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT passwrd FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$password = $data['passwrd'];
		mysql_close($conn);

		return $password;
	}
}
function setMemberPassword($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET passwrd = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}
function getMemberCurrentCourse($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT currentcourse FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$currentcourse = $data['currentcourse'];
		mysql_close($conn);

		return $currentcourse;
	}
}
function setMemberCurrentCourse($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET currentcourse = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberCurrentCourseSection($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT currentcoursesection FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$currentcoursesection = $data['currentcoursesection'];
		mysql_close($conn);

		return $currentcoursesection;
	}
}
function setMemberCurrentCourseSection($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET currentcoursesection = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberCurrentGroupStudyCourse($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT currentgroupstudycourse FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$currentgroupstudycourse = $data['currentgroupstudycourse'];
		mysql_close($conn);

		return $currentgroupstudycourse;
	}
}
function setMemberCurrentGroupStudyCourse($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET currentgroupstudycourse = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberCurrentGroupStudyID($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT currentgroupstudyid FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$currentgroupstudyid = $data['currentgroupstudyid'];
		mysql_close($conn);

		return $currentgroupstudyid;
	}
}
function setMemberCurrentGroupStudyID($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET currentgroupstudyid = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getIsMemberRegistered($memberemail){
	$conn = getDBConnection();
	$q_user = mysql_query("SELECT * FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		mysql_close($conn);

		return true;
	}
	else{
		return false;
	}
}
function getMemberPronouns($memberemail){
    $pronouns = array("subjective"=>"","objective"=>"","possessive"=>"");
    $conn = getDBConnection();
    $qg = mysql_query("SELECT gndr FROM users WHERE schemail='$memberemail'");
    if(mysql_num_rows($qg) == "1"){
        $d = mysql_fetch_array($qg); 
    }
    switch(strtolower($d['gndr'])){
        case "male":
            $pronouns["subjective"] = "he";
            $pronouns["objective"] = "him";
            $pronouns["possessive"] = "his";
            break;
        case "female":
            $pronouns["subjective"] = "she";
            $pronouns["objective"] = "her";
            $pronouns["possessive"] = "her";
            break;
    }
    mysql_close($conn);
    return $pronouns;
}
function getMemberFullName($memberemail){
	$conn = getDBConnection();
	$q_user = mysql_query("SELECT fname,lname FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$firstname = $data['fname'];
		$lastname = $data['lname'];
		mysql_close($conn);

		return stripslashes($firstname)." ".stripslashes($lastname);
	}
}

function getMemberIsOnMailingList($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT mailinglist FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$mailinglist = $data['mailinglist'];
		mysql_close($conn);

		return $mailinglist;
	}
}
function setMemberIsOnMailingList($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET mailinglist = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberFirstName($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT fname FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$firstname = $data['fname'];
		mysql_close($conn);

		return stripslashes($firstname);
	}
}
function setMemberFirstName($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET fname = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberLastName($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT lname FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$lastname = $data['lname'];
		mysql_close($conn);

		return stripslashes($lastname);
	}
}
function setMemberLastName($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET lname = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberGender($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT gndr FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$gender = $data['gndr'];
		mysql_close($conn);

		return $gender;
	}
}
function setMemberGender($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET gndr = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberRole($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT role FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$role = $data['role'];
		mysql_close($conn);

		return $role;
	}
}
function setMemberRole($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET role = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberMDYBirthday($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT bmonth,bday,byear FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$birthmonth = $data['bmonth'];
		$birthday = $data['bday'];
		$birthyear = $data['byear'];
		mysql_close($conn);

		return $birthmonth." ".$birthday.", ".$birthyear;
	}
}

function getMemberBirthMonth($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT bmonth FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$birthmonth = $data['bmonth'];
		mysql_close($conn);

		return $birthmonth;
	}
}
function setMemberBirthMonth($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET bmonth = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberBirthDay($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT bday FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$birthday = $data['bday'];

		return $birthday;
	}
}
function setMemberBirthDay($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET bday = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberBirthYear($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT byear FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$birthyear = $data['byear'];
		mysql_close($conn);

		return $birthyear;
	}
}
function setMemberBirthYear($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET byear = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberSchool($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT school FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$school = $data['school'];
		mysql_close($conn);

		return $school;
	}
}
function setMemberSchool($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET school = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberSchoolDomain($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT schooldomain FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$schooldomain = $data['schooldomain'];
		mysql_close($conn);

		return $schooldomain;
	}
}
//change of schools...prob wont need this for a while-MJ
function setMemberSchoolDomain($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET schooldomain = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}
function getMemberMajor($memberemail){
	$conn = getDBConnection();
	$q_user = mysql_query("SELECT major FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$major = $data['major'];
		mysql_close($conn);
		return $major;
	}
}
function setMemberMajor($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET major = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}
function getMemberGradYear($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT grad_year FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$gradyear = $data['grad_year'];
		mysql_close($conn);

		return $gradyear;
	}
}
function setMemberGradYear($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET grad_year = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}
function getMemberIsConfirmed($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT confirmed FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$confirmed = $data['confirmed'];
		mysql_close($conn);

		return $confirmed;
	}
}
function setMemberIsConfirmed($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET confirmed = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberConfirmCode($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT emailconfirmcode FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$emailconfirmcode = $data['emailconfirmcode'];
		mysql_close($conn);

		return $emailconfirmcode;
	}
}

function getMemberMobileAddress($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT mobileaddress FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$mobileaddress = $data['mobileaddress'];
		mysql_close($conn);

		return $mobileaddress;
	}
}
function setMemberMobileAddress($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET mobileaddress = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberMobileNotesNotifications($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT mobilenotesnotifications FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$notifications = $data['mobilenotesnotifications'];
		mysql_close($conn);

		return $notifications;
	}
}
function setMemberMobileNotesNotifications($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET mobilenotesnotifications = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberMobileMessagesNotifications($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT mobilemessagesnotifications FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$notifications = $data['mobilemessagesnotifications'];
		mysql_close($conn);

		return $notifications;
	}
}
function setMemberMobileMessagesNotifications($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET mobilemessagesnotifications = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberMobileEventsNotifications($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT mobileeventsnotifications FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$notifications = $data['mobileeventsnotifications'];
		mysql_close($conn);

		return $notifications;
	}
}
function setMemberMobileEventsNotifications($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET mobileeventsnotifications = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberMobileHomeworkNotifications($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT mobilehomeworknotifications FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$notifications = $data['mobilehomeworknotifications'];
		mysql_close($conn);

		return $notifications;
	}
}
function setMemberMobileHomeworkNotifications($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET mobilehomeworknotifications = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberMobileGroupStudyNotifications($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT mobilegroupstudynotifications FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$notifications = $data['mobilegroupstudynotifications'];
		mysql_close($conn);

		return $notifications;
	}
}
function setMemberMobileGroupStudyNotifications($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET mobilegroupstudynotifications = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberMobileCoursesNotifications($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT mobilecoursesnotifications FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$notifications = $data['mobilecoursesnotifications'];
		mysql_close($conn);

		return $notifications;
	}
}
function setMemberMobileCoursesNotifications($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET mobilecoursesnotifications = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberMobileWeeklyScheduleNotifications($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT mobileweeklyschedulenotifications FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$notifications = $data['mobileweeklyschedulenotifications'];
		mysql_close($conn);

		return $notifications;
	}
}
function setMemberMobileWeeklyScheduleNotifications($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET mobileweeklyschedulenotifications = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}

function getMemberMobileTodoNotifications($memberemail){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$q_user = mysql_query("SELECT mobiletodonotifications FROM users WHERE schemail='$memberemail'");

	if(mysql_num_rows($q_user) == 1){
		$data = mysql_fetch_array($q_user);
		$notifications = $data['mobiletodonotifications'];
		mysql_close($conn);

		return $notifications;
	}
}
function setMemberMobileTodoNotifications($memberemail, $config){
	$conn = mysql_connect($GLOBALS['server'], $GLOBALS['dbuser'], $GLOBALS['dbpass']);
	$db = mysql_select_db($GLOBALS['dbname']);
	$config = mysql_real_escape_string($config);

	$q_user = mysql_query("UPDATE users SET mobiletodonotifications = '$config' WHERE schemail='$memberemail'");
	mysql_close($conn);
}*/
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

function ScandirUnder($chest){
	$treasure_array = array();
	if(is_dir($chest)){
	$treasures = scandir($chest);
	foreach($treasures as $treasureindex => $treasure){
		if($treasure == "." || $treasure == ".."){
		}//end if dots
		else{
			$treasurepath = $chest.'/'.$treasure;
			//print_r($treasure_array);
			$treasure_array[] = $treasurepath;
		}//end if treasure not dots
	}
	}
	return $treasure_array;
}

function returnCommaSeparatedString($array){
	$str = "";
	$counter = 0;
	if(is_array($array)){
	foreach($array as $index=> $item){
		$counter+=1;
		if($counter < count($array)){
			$str .= $item.", ";
		}//add comma after item until end
		if($counter == count($array)){
			$str .= $item;
		}//last item in array has been reached
	}}
	return $str;
}

function TwoLevelScanItem($chest){
	$chest = OneLevelScanItem($chest);
	$treasure_array = array();
	$treasures = scandir($chest);
	foreach($treasures as $treasureindex => $treasure){
		if($treasure == "." || $treasure == ".."){
		}//end if dots
		else{
			$treasurepath = $chest.'/'.$treasure;
			$treasure_array[] = $treasure;
		}//end if treasure not dots
	}
	return $treasure_array;
}

function convertMilHrs($hour){
	$amtomilhrs = array( "5"=> "500", "6" => "600", "7" => "700", "8" => "800", 
					"9" => "900", "10" => "1000", "11" => "1100","12" => "000");
	$regtomilhrsam = array_flip($amtomilhrs);
	if(in_array($hour, $amtomilhrs)){
		$realhour = intval($regtomilhrsam[$hour]);
	}
	$pmtomilhrs = array("12" => "1200", "1" => "1300","2" => "1400", 
			"3" => "1500", "4" => "1600", "5" => "1700", "6" => "1800", 
			"7" => "1900", "8" => "2000", "9" => "2100", "10" => "2200", 
			"11" => "2300");
	$regtomilhrspm = array_flip($pmtomilhrs);
	if(in_array($hour, $pmtomilhrs)){
		$realhour = intval($regtomilhrspm[$hour]);	
	}
	return $realhour;
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

function getFileName($filenameparam){
	$finfo = pathinfo($filenameparam);
	$fname = $finfo['filename'];
	return $fname;
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