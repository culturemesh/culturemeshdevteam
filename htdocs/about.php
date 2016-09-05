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

	$searchbar_template = file_get_contents('templates' . $cm->ds . 'searchbar.html');
	$sb_alt_font = $m_comp->render($searchbar_template, array('alt-font' => True, 'alt-color' => True, 'network' => True, 'vars'=>$cm->getVars()));

	// Width calculations
	//
	$team_members_count = count($team_members);

	$tmp = file_get_contents($cm->template_dir . $cm->ds . 'about_team-member_ul.html');
	$team_html = $team_members->getHTML('about', array(
		'cm' => $cm,
		'mustache' => $m_comp,
		'list_template' => $tmp,
		'list_vars' => array('row_width' => $row_width)
		)
	);

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

	// USER STUFF
	$site_user = NULL;
	$logged_in = false;
	$member = false;

	if (isset($_SESSION['uid'])) {

		$logged_in = true;

		// check if user is registered
		// if so, get user info
		$site_user = \dobj\User::createFromId($_SESSION['uid'], $dal, $do2db)->prepare($cm);

		// see if user is registered
		// in network
		$guest = false;
	}

	//
	// LOAD THE PAGE
	//

	$page_loader = new \misc\PageLoader($cm, $mobile_detect);
	echo $page_loader->generate('templates' . $cm->ds .'about.html', array(
		'vars' => $cm->getVars(),
		'site_user' => $site_user,
		'logged_in' => $logged_in,
		'team_html' => $team_html,
		'searchbars' => array(
			'alt-font' => $sb_alt_font
		),
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
