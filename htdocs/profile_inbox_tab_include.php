<php
?>
<div id="inbox-post">
	<div class="event-host">
		<img src="images/blank_profile.png" width="72" height="72"/>
	</div>
	<div class="event-text">
		<form id="msg_compose_form" method="POST" action="start_conversation.php">
		    <input type="text" data-provide="typeahead" data-items="4" data-source="<?/*=getJSCompatibleArrayFromDBData(getMemberNames())*/?>" name="msg_to" placeholder="Email..." /> 
		    <textarea class="dashboard msg" name="msg_txt" placeholder="Write a message..."></textarea>
		    </br>
		    <a href="#<?=MSG_MODAL_PICTURE_ATTACH_ID?>"><i class="icon icon-camera"></i></a>
		    <a href="#<?=MSG_MODAL_VIDEO_ATTACH_ID?>"><i class="icon icon-facetime-video"></i></a>
		    <button id="message_post_btn" class="btn cm-button cm-button-small">Post</button>
		</form>
	</div>
</div>
<div class='clear'></div>
<div>
	<h3 class="dashboard">Your Conversations</h3>
	<ul class='dashboard item'>
		<?php
		$conversations = Conversation::getConversationsByUserId($_SESSION['uid'], $con);
		foreach($conversations as $conversation)
			HTMLBuilder::displayDashConversation($conversation);
		?>
	</ul>
</div>