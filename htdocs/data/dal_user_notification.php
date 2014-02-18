<?php
/**
  * @operations - 
  * 	CREATE
  *         createNotification
  *	READ
  *	    getNotificationsByUser(uid)
  *	UPDATE
  *	    updateNotification
  *	DELETE
**/ 

include_once "zz341/fxn.php";
include_once "dal_user_notification-dt.php";

class UserNotification
{
	////////////////////// CREATE OPERATIONS /////////////////////
	
	// called when user is created, bare bones
	public static function createNotification($uid, $con=false)
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
		
		if (!mysqli_query($con, "INSERT INTO user_notifications 
			(uid) 
			VALUES ({$uid}"))
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
	////////////////////// READ OPERATIONS /////////////////////
		public static function getNotificationsByUser($uid, $con=false)
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
		
		if (!$result = mysqli_query($con, "SELECT *
			FROM user_notifications 
			WHERE uid={$uid}"))
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
			$not_dt = new UserNotificationDT();
			
			if ($row = mysql_fetch_array($result))
			{
				$not_dt->id = $row['id'];
				$not_dt->uid = $row['uid'];
				$not_dt->events_upcoming = $row['events_upcoming'];
				$not_dt->events_interested_in = $row['events_interested_in'];
				$not_dt->company_news = $row['company_news'];
				$not_dt->network_activity = $row['network_activity'];
			}
			return $not_dt;
		}
	}
	////////////////////// UPDATE OPERATIONS /////////////////////
	public static function updateNotification($not_dt, $con=false)
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
		
		if (!mysqli_query($con, "UPDATE user_notifications 
			SET events_upcoming={$not_dt->events_upcoming}, events_interested_in={$not_dt->events_interested_in},
			company_news={$not_dt->company_news}, network_activity={$not_dt->network_activity}
			WHERE uid={$not_dt->uid}"))
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
	////////////////////// DELETE OPERATIONS /////////////////////
}
?>
