<?php

/**
  * @operations - 
  * 	CREATE
  *         createPost
  *	READ
  *	    getAllPosts
  *	    getPostsByNetworkId
  *	    getPostsByUserId
  *	UPDATE
  *	    updatePost
  *	DELETE
  *         deletePost
  *	    deletePostByUser
  *	    deletePostByNetwork
**/ 

include_once("zz341/fxn.php");
include_once("dal_post-dt.php");
include_once("dal_query_handler.php");

class Post
{
	////////////////////// CREATE OPERATIONS ////////////////////////////////////////
	public static function createPost($post_dt, $con=NULL)
	{
		$query = <<<SQL
			INSERT INTO posts
			(id_user, id_network, post_date, post_text, post_class, post_original, vid_link, img_link) 
			VALUES ($post_dt->id_user, $post_dt->id_network, NOW(), 
			'$post_dt->post_text', '$post_dt->post_class', $post_dt->post_original, '$post_dt->vid_link', '$post_dt->img_link')
SQL;

		return QueryHandler::executeQuery($query, $con);
	}

	public static function createReply($text, $nid, $uid, $id_parent, $con=NULL)
	{
		$query = <<<SQL
			INSERT INTO post_replies
			(id_parent, id_user, id_network, reply_text) 
			VALUES ($id_parent, $uid, $nid, '$text')
SQL;

		return QueryHandler::executeQuery($query, $con);
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
	
	public static function getPostsByNetworkId($id, $con=NULL)
	{
		$query = <<<SQL
			SELECT p.*, u.email, u.username, u.first_name, u.last_name, u.img_link, reply_count 
			FROM posts p
			LEFT JOIN (SELECT id_parent, COUNT(id_parent) AS reply_count
					FROM post_replies
					GROUP BY id_parent) pr
			ON p.id = pr.id_parent
			JOIN (SELECT *
				FROM users) u
			ON p.id_user = u.id
			AND p.id_network=$id
			ORDER BY post_date DESC
SQL;
		// execute
		$result = QueryHandler::executeQuery($query, $con);

		// process posts
		$posts = array();
		while ($row = mysqli_fetch_array($result))
		{
			$post_dt = new PostDT();
			
			$post_dt->id = $row['id'];
			$post_dt->id_user = $row['id_user'];
			$post_dt->email = $row['email'];
			$post_dt->username = $row['username'];
			$post_dt->first_name = $row['first_name'];
			$post_dt->last_name = $row['last_name'];
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
		
		$result = mysqli_query($con,"SELECT * FROM posts p, users u WHERE p.id_user=u.id AND id_user={$id}");
		
		if (func_num_args() < 2)
			mysqli_close($con);
		
		$posts = array();
		
		while ($row = mysqli_fetch_array($result))
		{
			$post_dt = new PostDT();
			
			$post_dt->id = $row['id'];
			$post_dt->id_user = $id;
			$post_dt->first_name = $row['first_name'];
			$post_dt->last_name = $row['last_name'];
			$post_dt->username = $row['username'];
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
	
	public static function getPostById($id, $con=NULL) {
		$query = <<<SQL
			SELECT p.*, reply_count 
			FROM posts p
			LEFT JOIN (SELECT id_parent, COUNT(id_parent) AS reply_count
					FROM post_replies
					GROUP BY id_parent) pr
			ON p.id = pr.id_parent
			WHERE p.id=$id
			ORDER BY post_date DESC
SQL;

		$result = QueryHandler::executeQuery($query, $con);

		$post = new PostDT();

		$row = mysqli_fetch_array($result);

		$post->id = $row['id'];
		$post->id_user = $row['id_user'];
		$post->id_network = $row['id_network'];
		$post->post_date = $row['post_date'];
		$post->post_text = $row['post_text'];
		$post->post_class = $row['post_class'];
		$post->post_original = $row['post_original'];
		$post->reply_count = $row['reply_count'];
		$post->vid_link = $row['vid_link'];
		$post->img_link = $row['img_link'];

		return $post;
	}

	public static function getPostCount($id, $con=NULL)
	{
		
		$query = <<<SQL
			SELECT reply_count, COUNT(p.id_network) AS post_count
			FROM posts p
			LEFT JOIN (SELECT id_network, COUNT(id_network) AS reply_count
					FROM post_replies
					GROUP BY id_network) pr
			ON p.id_network = pr.id_network
			WHERE p.id_network=$id
SQL;
	//	$result = mysqli_query($con,"SELECT COUNT(id_network) as post_count FROM posts WHERE id_network={$id}");
		$result = QueryHandler::executeQuery($query, $con);
		
		while ($row = mysqli_fetch_array($result)) {
			$post_count = $row['post_count'];
			$reply_count = $row['reply_count'];
		}
		
		return $post_count + $reply_count;
	}

	public static function getRepliesByParentId($id, $con=NULL)
	{
		$query = <<<SQL
			SELECT p.*, u.email, u.username, u.first_name, u.last_name, u.img_link
			FROM post_replies p, users u
			WHERE p.id_user=u.id
			AND p.id_parent=$id
SQL;

		$result = QueryHandler::executeQuery($query, $con);

		$posts = array();
		
		while ($row = mysqli_fetch_array($result))
		{
			$post_dt = new PostDT();
			
			$post_dt->id = $row['id'];
			$post_dt->id_user = $row['id_user'];
			$post_dt->id_parent = $row['id_parent'];
			$post_dt->first_name = $row['first_name'];
			$post_dt->last_name = $row['last_name'];
			$post_dt->username = $row['username'];
			$post_dt->email = $row['email'];
			$post_dt->id_network = $row['id_network'];
			$post_dt->reply_date = $row['reply_date'];
			$post_dt->reply_text = $row['reply_text'];
			$post_dt->vid_link = $row['vid_link'];
			$post_dt->img_link = $row['img_link'];
			
			array_push($posts, $post_dt);
		}
		
		return $posts;
	}

	public static function getImmediateReply($text, $nid, $uid, $id_parent, $con=NULL) {
		$query = <<<SQL
			SELECT p.*, u.email, u.username, u.first_name, u.last_name, u.img_link
			FROM post_replies p, users u
			WHERE p.id_user=u.id
			AND p.reply_text='$text'
			AND p.id_network=$nid
			AND p.id_user=$uid
			AND p.id_parent=$id_parent
			ORDER BY reply_date DESC
SQL;

		$result = QueryHandler::executeQuery($query, $con);

		$row = mysqli_fetch_array($result);

		$post_dt = new PostDT();
		
		$post_dt->id = $row['id'];
		$post_dt->id_user = $row['id_user'];
		$post_dt->id_parent = $row['id_parent'];
		$post_dt->first_name = $row['first_name'];
		$post_dt->last_name = $row['last_name'];
		$post_dt->username = $row['username'];
		$post_dt->email = $row['email'];
		$post_dt->id_network = $row['id_network'];
		$post_dt->reply_date = $row['reply_date'];
		$post_dt->reply_text = $row['reply_text'];
		$post_dt->vid_link = $row['vid_link'];
		$post_dt->img_link = $row['img_link'];
			
		return $post_dt;
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

	public static function wipePost($pid, $con=NULL)
	{
		$query = <<<SQL
			UPDATE posts
			SET post_text = NULL
			WHERE id=$pid
SQL;
		return QueryHandler::executeQuery($query, $con);
	}
	
	////////////////////// DELETE OPERATIONS /////////////////////
	public static function deletePost($id, $con=NULL)
	{
		$query = <<<SQL
			DELETE FROM posts 
			WHERE id=$id
SQL;

		return QueryHandler::executeQuery($query, $con);
	}

	public static function deleteReply($id, $con=NULL)
	{
		$query = <<<SQL
			DELETE FROM post_replies 
			WHERE id=$id
SQL;

		return QueryHandler::executeQuery($query, $con);
	}

	public static function deleteRepliesByParentId($pid, $con)
	{
		$query = <<<SQL
			DELETE FROM post_replies
			WHERE id_parent=$pid
SQL;
		return QueryHandler::executeQuery($query, $con);
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
