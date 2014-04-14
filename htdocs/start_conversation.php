<?php
// if the post variables aren't set, exit
if (!(isset($_POST['msg_to']) && isset($_POST['msg_txt'])))
	exit("You don't belong here.");

ini_set('display_errors', true);

include("zz341/fxn.php");
include_once("data/dal_user.php");
include_once("data/dal_conversation.php");
include_once("data/dal_message.php");
session_name("myDiaspora");
session_start();

$msg_to_c = mysql_escape_string($_POST['msg_to']);
$msg_txt_c = mysql_escape_string($_POST['msg_txt']);

$con = getDBConnection();

$recipient = User::getUserByEmail($msg_to_c, $con);

$user = User::getUserByEmail($_SESSION['uid'], $con);

if ($user->id == $recipient->id)
{
	mysqli_close($con);
	exit("Can't start a conversation with yourself.");
}

if (Conversation::createConversation($_SESSION['uid'], $recipient->id, $con))
{
	
	$message_dt = new MessageDT();
	$message_dt->id_conversation = Conversation::getIdByUsers($_SESSION['uid'], $recipient->id, $con);
	$message_dt->id_sender = $_SESSION['uid'];
	$message_dt->id_recipient = $recipient->id;
	$message_dt->message_text = $msg_txt_c;
	if(Message::createMessage($message_dt, $con))
	{
		mysqli_close($con);
		header("Location: profile_edit.php?cr_con=success");
	}
	else
	{
		mysqli_close($con);
		header("Location: profile_edit.php?cr_con=error_onmsg");
	}
}
else
{
	mysqli_close();
	exit("Couldn't create conversation. I DON'T KNOW WHY!");	
}
?>
