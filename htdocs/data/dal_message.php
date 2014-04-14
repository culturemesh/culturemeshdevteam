<?php
ini_set('display_errors', 1);

/**
  * @operations - 
  * 	CREATE
  *         createMessage
  *	READ
  *	    getMessagesByUser
  *	    getMessagesByConversation
  *	UPDATE
  *	DELETE
  *         deleteMessage
**/ 

include_once("zz341/fxn.php");

include_once("dal_user.php");
include_once("dal_message-dt.php");

class Message
{
	////////////////////// CREATE OPERATIONS /////////////////////
	public static function createMessage($message_dt, $con=false)
	{
		if (!$con)
		{ 
			$con = getDBConnection();
			$must_close = true;
		}
		
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: ";
		}
		
		if (!mysqli_query($con, "INSERT INTO messages 
			(id_conversation, id_sender, id_recipient, message_text, send_date) 
			VALUES ({$message_dt->id_conversation}, {$message_dt->id_sender}, 
			{$message_dt->id_recipient}, {$message_dt->message_text}, NOW())"))
		{
			echo "Error message: " . $con->error;
			$error = true;
		}
		
		if ($must_close)
		{
			mysqli_close($con);
		}
		
		if ($error) return -1;
		else return 1;
	}
	////////////////////// READ OPERATIONS /////////////////////
	public static function getMessagesByConversation($id_conversation, $con=false)
	{
		if (!$con)
		{ 
			$con = getDBConnection();
			$must_close = true;
		}
		
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: ";
		}
		
		if (!$results = mysqli_query($con, "
			SELECT *
			FROM messages 
			WHERE id_conversation={$id_conversation}"))
		{
			echo "Error message: " . $con->error;
		}
		
		if ($must_close)
		{
			mysqli_close($con);
		}
		
		$messages = array();
		
		while ($row = mysqli_fetch_array($results))
		{
			$message = new MessageDT();
			$message->id = $row['id'];
			$message->id_conversation = $row['id_conversation'];
			$message->id_sender = $row['id_sender'];
			$message->id_recipient = $row['id_recipient'];
			$message->message_text = $row['message_text'];
			$message->send_date = $row['send_date'];
			
			array_push($messages, $message);
		}
		
		return $messages;
	}
	////////////////////// UPDATE OPERATIONS /////////////////////
	////////////////////// DELETE OPERATIONS /////////////////////
}
?>
