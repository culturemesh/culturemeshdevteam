<?php

ini_set('display_errors', 1);

/**
  * @operations - 
  * 	CREATE
  *         createConversation
  *	READ
  *	    getConversationsByUser
  *	UPDATE
  *	DELETE
  *         deleteConversation
**/ 

include_once("zz341/fxn.php");

include_once("dal_user.php");
include_once("dal_conversation-dt.php");

class Conversation
{
	////////////////////// CREATE OPERATIONS /////////////////////
	public static function createConversation($uid_1, $uid_2, $con=false)
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
		
		if (!mysqli_query($con, "INSERT INTO conversations 
			(id_user1, id_user2, start_date) 
			VALUES ({$uid_1}, {$uid_2}, NOW())"))
		{
			echo "Error message: " . $con->error;
			return -1;
			
			if ($must_close)
			{
				mysqli_close($con);
			}
		}
		else
		{
			if ($must_close)
			{
				mysqli_close($con);
			}
			return 1;
		}
	}
	////////////////////// READ OPERATIONS ///////////////////////
	public static function getConversationsByUserId($uid, $con=false)
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
		
		if(!$results = mysqli_query($con, "SELECT * 
			FROM conversations 
			WHERE id_user1={$uid} OR id_user2={$uid}"))
		{
			echo "Error message" . $con->error;
			
			if ($must_close)
			{ mysqli_close($con); }
			
			return -1;
		}
		else
		{
			if ($must_close)
			{ mysqli_close($con); }
			
			$conversations = array();
			
			while ($row = mysqli_fetch_array($results))
			{
				
				$conversation = new ConversationDT();
				$conversation->id = $row['id'];
				$conversation->id_user1 = $row['id_user1'];
				$conversation->id_user2 = $row['id_user2'];
				$conversation->start_date = $row['start_date'];
				if ($uid == $row['id_user1'])
				{
					$conversation->id_user1_dt = NULL;
					$conversation->id_user2_dt = User::getUserById($conversation->id_user2);
				}
				if ($uid == $row['id_user2'])
				{
					$conversation->id_user2_dt = NULL;
					$conversation->id_user1_dt = User::getUserById($conversation->id_user1);
				}
				array_push($conversations, $conversation);
			}
			
			return $conversations;
		}
	}
	
	public static function getIdByUsers($uid_1, $uid_2, $con=false)
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
		
		if(!$results = mysqli_query($con, "SELECT id
			FROM conversations 
			WHERE id_user1={$uid_1} AND id_user2={$uid_2}"))
		{
			echo "Error message" . $con->error;
			
			if ($must_close)
			{
				mysqli_close($con);
			}
			
			return -1;
		}
		else
		{
			if ($must_close)
			{
				mysqli_close($con);
			}
			
			if ($row = mysqli_fetch_array($results))
			{
				return $row['id'];
			}
		}
	}
	////////////////////// UPDATE OPERATIONS /////////////////////
	////////////////////// DELETE OPERATIONS /////////////////////
	public static function deleteConversation($id, $con=false)
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
		
		if (!mysqli_query($con, "DELETE FROM conversations 
			WHERE id={$id}"))
		{
			echo "Error message: " . $con->error;
		}
		
		if ($must_close)
		{
			mysqli_close($con);
		}
	}
}
?>
