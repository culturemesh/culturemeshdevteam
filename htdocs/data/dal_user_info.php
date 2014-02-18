<?php
/**
  * @operations - 
  * 	CREATE
  *         createInfo
  *	READ
  *	    getInfoByUser(uid)
  *	UPDATE
  *	    updateInfo
  *	DELETE
**/ 

include_once "zz341/fxn.php";
include_once "dal_user_info-dt.php";

class UserInfo
{
	////////////////////// CREATE OPERATIONS /////////////////////
	
	// called when user is created, bare bones
	public static function createInfo($uid, $con=false)
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
		
		if (!mysqli_query($con, "INSERT INTO user_info 
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
		public static function getInfoByUser($uid, $con=false)
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
			
			$info_dt = new UserInfoDT();
			
			if ($row = mysqli_fetch_array($result))
			{
				$info_dt->id = $row['id'];
				$info_dt->uid = $row['uid'];
				$info_dt->first_name = $row['first_name'];
				$info_dt->last_name = $row['last_name'];
				$info_dt->gender = $row['gender'];
				$info_dt->about_me = $row['about_me'];
			}
			
			return $info_dt;
		}
	}
	////////////////////// UPDATE OPERATIONS /////////////////////
	public static function updateInfo($info_dt, $con=false)
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
			SET first_name={$info_dt->first_name}, last_name={$info_dt->last_name},
			gender={$info_dt->gender}, about_me={$info_dt->about_me}"))
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
