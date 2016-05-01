<?php

	include("environment.php");
	$cm = new Environment();

	session_name($cm->session_name);
	session_start();

	// Basic test for mobile device
	$mobile_detect = new \misc\MobileDetect();

	$cm->enableDatabase($dal, $do2db);

	$team_members = $do2db->execute($dal, NULL, 'getTeamMembers');
	$cm->closeConnection();

	//
	////////////// RENDER TEAM MEMBERS ////////////
	//

	// mustache components
	$m_comp = new \misc\MustacheComponent();

	// Width calculations
	//
	$team_members_count = count($team_members);
	$row_length = 4;

	$ul_count = ceil( $team_members_count / 4 );
	$pic_length = 205;
	$item_width = $pic_length + 6;
	$full_row_width = $item_width * $row_length;

	$team_html = array();

	for($i = 0; $i < $ul_count; $i++) {
	
		// count row offset
		$start_index = ($i * $row_length);
		$row_list = $team_members->slice($start_index, $row_length, True);

		// get row width
		$row_width = count($row_list) * $item_width;

		$tmp = file_get_contents($cm->template_dir . $cm->ds . 'about_team-member_ul.html');
		$row_html = $row_list->getHTML('about', array(
			'cm' => $cm,
			'mustache' => $m_comp,
			'list_template' => $tmp,
			'list_vars' => array('row_width' => $row_width)
			)
		);

		array_push($team_html, $row_html);
	}

	//
	///////////// HANDLE EMAILS
	//
	$success = False;
	$failure = False; // requires two because of templating

        if(isset($_POST['contact_name']) && ($_POST['contact_body'])
		&& isset($_POST['contact_email'])){

		$contact_us = new \api\ContactUsEmail($cm, $m_comp, 'ken@culturemesh.com', array(
			'name' => $_POST['contact_name'],
			'email' => $_POST['contact_email'],
			'message' => $_POST['contact_body']
		));

		$success = $contact_us->send();
		if (!$success)
		  $failure = True;

        }

	//
	// LOAD THE PAGE
	//
	if (isset($_SESSION['uid']))
		$logged_in = true;
	else
		$logged_in = false;

	$page_loader = new \misc\PageLoader($cm, $mobile_detect);
	echo $page_loader->generate('templates' . $cm->ds .'about.html', array(
		'vars' => $cm->getVars(),
		'logged_in' => $logged_in,
		'team_html' => $team_html,
		'success' => $success,
		'failure' => $failure
	));

	/*
		//// TEAM IMAGES

	    <?php
		$pic_length = 205;
		$team_members = getRowsQuery("SELECT * FROM internal_team");
		$team_members_count = count($team_members);
		$row_length = 4;

		$ul_count = $team_members_count / 4;
		$item_width = $pic_length + 6;
		
	    ?>
		<?php for($i = 0; $i < $team_members_count;):?>
			<?php
			$quotient = $i / $row_length;

			if ($quotient === 0) {
				$row_item_count = $row_length;
			}
			else {
				$row_item_count = $team_members_count - $i;
			}	

			$row_width = $row_item_count * $item_width;
			?>
			<div id="team-row-container">
			<ul class="img-strip-ul team" style="width:<?php echo $row_width;?>px">
			<?php for($j = 0; $j < $row_length && $i < $team_members_count; $j++, $i++):?>
				<li class="team-member">
					<div class="team-member">
					<img src="<?php echo $team_members[$i]['thumb_url'];?>" title="<?php echo $team_members[$i]['name'];?>" alt="<?php echo $team_members[$i]['name'];?>" class="team_thumb" /></br>
					<p class="team team-name"><?php echo $team_members[$i]['name']; ?></p>
					<p class="team team-job-title"><?php echo $team_members[$i]['job_title']; ?></p>
					</div>
				</li>
			<?php endfor;?>
	    		</ul>
			</div>
		<?php endfor; ?>

			////////// EMAIL
    <?php
        if(isset($_POST['contact_name']) && ($_POST['contact_body'])
		&& isset($_POST['contact_email'])){
		if (CMEmail::sendContactUsMsg($_POST['contact_name'], $_POST['contact_email'], $_POST['contact_body']))
            		echo '<span class="label label-success">Thanks! Our team is looking forward to reading what you had to say!</span>';
		else echo '<span class="label label-success">Sorry, your email didn\'t get sent. Try again later. We want to hear what you have to say!</span>';
        }
	else {
		//echo '<span class="label label-success">Please fill out all fields.</span>';
	}
    ?>
	 */
?>
