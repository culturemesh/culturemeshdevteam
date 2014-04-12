<?php

/**
  * @operations - 
  * 	CREATE
  *         createPost
  *	READ
  *	    getAllPosts
  *	UPDATE
  *	    updatePost
  *	DELETE
  *         deletePost
  *	    deletePostByUser
  *	    deletePostByNetwork
**/ 

include_once("zz341/fxn.php");
include_once("dal_post-dt.php");

class Post
{
	////////////////////// CREATE OPERATIONS ////////////////////////////////////////
	public static function createPost($post_dt)
	{
		if (func_num_args() == 2)
		{ $con = func_get_arg(1); }
		else
		{ $con = getDBConnection();}
		//$con = func_get_arg(1);
		
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: ";
		}
		
		echo "INSERT INTO posts
			(id_user, id_network, post_date, post_text, post_class, post_original, vid_link, img_link) 
			VALUES (". $post_dt->id_user . ", ". $post_dt->id_network . ", NOW(), 
			'". $post_dt->post_text ."', '". $post_dt->post_class ."', ". $post_dt->post_original .", '" . $post_dt->vid_link ."', '". $post_dt->img_link ."')";

		if (!mysqli_query($con, "INSERT INTO posts
			(id_user, id_network, post_date, post_text, post_class, post_original, vid_link, img_link) 
			VALUES (". $post_dt->id_user . ", ". $post_dt->id_network . ", NOW(), 
			'". $post_dt->post_text ."', '". $post_dt->post_class ."', ". $post_dt->post_original .", '" . $post_dt->vid_link ."', '". $post_dt->img_link ."')"))
		{
			echo "Error Message: " . $con->error;
		}
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
	
	////////////////////// READ OPERATIONS //////////////////////////////////////////////
	public static function getAllPosts()
	{
		
		if (func_num_args() == 1)
		{ $con = func_get_arg(0); }
		else
		{ $con = getDBConnection();}
		
		// Check connection
		if (mysqli_connect_errno())
		{
		  	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$result = mysqli_query($con,"SELECT * FROM posts");
		
		if (func_num_args() < 1)
			mysqli_close($con);
		
		return $result;
	}
	
	public static function getPostsByNetworkId($id)
	{
		if (func_num_args() == 2)
		{ $con = func_get_arg(1); }
		else
		{ $con = getDBConnection();}
		
		// Check connection
		if (mysqli_connect_errno())
		{
		  	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$result = mysqli_query($con,
			"SELECT p.*, u.email 
			FROM posts p, users u 
			WHERE p.id_user=u.id 
			AND id_network={$id}
			ORDER BY post_date DESC");
		
		$posts = array();
		while ($row = mysqli_fetch_array($result))
		{
			$post_dt = new PostDT();
			
			$post_dt->id = $row['id'];
			$post_dt->id_user = $row['id_user'];
			$post_dt->email = $row['email'];
			$post_dt->id_network = $id;
			$post_dt->post_date = $row['post_date'];
			$post_dt->post_text = $row['post_text'];
			$post_dt->post_class = $row['post_class'];
			$post_dt->post_original = $row['post_original'];
			$post_dt->reply_count = $row['reply_count'];
			$post_dt->vid_link = $row['vid_link'];
			$post_dt->img_link = $row['img_link'];
			
			array_push($posts, $post_dt);
		}
		
		if (func_num_args() < 2)
			mysqli_close($con);
		
		return $posts;
	}
	
	public static function getPostsByUserId($id)
	{
		if (func_num_args() == 2)
		{ $con = func_get_arg(1); }
		else
		{ $con = getDBConnection();}
		
		// Check connection
		if (mysqli_connect_errno())
		{
		  	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$result = mysqli_query($con,"SELECT * FROM posts WHERE id_user={$id}");
		
		if (func_num_args() < 2)
			mysqli_close($con);
		
		$posts = array();
		
		while ($row = mysqli_fetch_array($result))
		{
			$post_dt = new PostDT();
			
			$post_dt->id = $row['id'];
			$post_dt->id_user = $id;
			$post_dt->email = $row['email'];
			$post_dt->id_network = $row['id_network'];
			$post_dt->post_date = $row['post_date'];
			$post_dt->post_text = $row['post_text'];
			$post_dt->vid_link = $row['vid_link'];
			$post_dt->img_link = $row['img_link'];
			
			array_push($posts, $post_dt);
		}
		
		return $posts;
	}
	
	public static function getPostCount($id)
	{
		
		if (func_num_args() == 2)
		{ $con = func_get_arg(1); }
		else
		{ $con = getDBConnection();}
		
		// Check connection
		if (mysqli_connect_errno())
		{
		  	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$result = mysqli_query($con,"SELECT COUNT(id_network) as post_count FROM posts WHERE id_network={$id}");
		
		while ($row = mysqli_fetch_array($result))
			$count = $row['post_count'];
		
		if (func_num_args() < 2)
			mysqli_close($con);
		
		return $count;
	}
	////////////////////// UPDATE OPERATIONS /////////////////////
	public static function updatePost($post_dt)
	{
		if (func_num_args() == 2)
		{ $con = func_get_arg(1); }
		else
		{ $con = getDBConnection();}
		//$con = func_get_arg(1);
		
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: ";
		}
		
		if (!mysqli_query($con, "UPDATE posts
			SET post_date= NOW(), post_text='". $post_dt->post_text . "', 
			vid_link='". $post_dt->vid_link ."', img_link='". $post_dt->img_link .
			"' WHERE id=". $post_dt->id))
		{
			echo "Error Message: " . $con->error;
		}
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
	
	////////////////////// DELETE OPERATIONS /////////////////////
	public static function deletePost($post_dt)
	{
		if (func_num_args() == 2)
		{ $con = func_get_arg(1); }
		else
		{ $con = getDBConnection();}
		//$con = func_get_arg(1);
		
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: ";
		}
		
		if (!mysqli_query($con, "DELETE FROM posts 
			WHERE id=". $post_dt->id))
		{
			echo "Error Message: " . $con->error;
		}
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
	
	public static function deletePostsByUser($id)
	{
		if (func_num_args() == 2)
		{ $con = func_get_arg(1); }
		else
		{ $con = getDBConnection();}
		//$con = func_get_arg(1);
		
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: ";
		}
		
		if (!mysqli_query($con, "DELETE FROM posts 
			WHERE id_user=". $id))
		{
			echo "Error Message: " . $con->error;
		}
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
	
	public static function deletePostsByNetwork($id)
	{
		if (func_num_args() == 2)
		{ $con = func_get_arg(1); }
		else
		{ $con = getDBConnection();}
		//$con = func_get_arg(1);
		
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: ";
		}
		
		if (!mysqli_query($con, "DELETE FROM posts 
			WHERE id_network=". $id))
		{
			echo "Error Message: " . $con->error;
		}
		
		if (func_num_args() < 2)
		{ mysqli_close($con); }
	}
}
?>
