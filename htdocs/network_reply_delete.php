<?php
$response = array(
	'error' => NULL,
	'status' => NULL);

// if pid is set, we can continue
if (isset($_POST['rid']) && isset($_POST['nid']) && isset($_POST['pid'])) {

	include('environment.php');
	$cm = new \Environment();


	$reply = new \dobj\Reply();
	$reply->id = (int) $_POST['rid'];

	$dal = new \dal\DAL($cm->getConnection());
	$dal->loadFiles();
	$do2db = new \dal\Do2Db();

	$success = $reply->delete($dal, $do2db);

	if ($success) {

		$post = new \dobj\Post();
		$post->id = (int) $_POST['pid'];
		$post->getReplies($dal, $do2db);

		if(count($post->replies) == 0) {
			$post = \dobj\Post::createFromId((int) $_POST['pid'], $dal, $do2db);

			if ($post->post_text == NULL) {
				$success = $post->delete();

				if ($success) {

					$cm->closeConnection();

					if (isset($_POST['NOJS']))
						header("Location: network/{$_POST['nid']}?dr=true&dp=true");
					else {
						// ajax
						$response['error'] = 0;
						$response['status'] = 'postdelete';
						echo json_encode($response);
					}
				}
				// fail
				else {

					$cm->closeConnection();

					if (isset($_POST['NOJS']))
						header("Location: network/{$_POST['nid']}/?dr=true&dp=false");
					else {
						// ajax
						$response['error'] = 'Final delete failed';
						echo json_encode($response);
					}
				}
			}
			else {
				$cm->closeConnection();

				if (isset($_POST['NOJS']))
					header("Location: network/{$_POST['nid']}?dr=true&dp=false");
				else {
					// ajax
					$response['error'] = 0;
					echo json_encode($response);
				}
			}
		}
		else {
			// close db connection
			$cm->closeConnection();

			if (isset($_POST['NOJS']))
				header("Location: network/{$_POST['nid']}?dr=true");
			else {
				// ajax
				$response['error'] = 0;
				echo json_encode($response);
			}
		}
	}
	else {
		if (isset($_POST['NOJS']))
			header("Location: network/{$_POST['nid']}/?dr=false");
		else {
			// ajax
			$response['error'] = 'Delete was unsuccessful';
			echo json_encode($response);
		}
	}
	/*
	include_once("data/dal_post.php");
	include_once("data/dal_query_handler.php");

	// stuff
	$pid = $_POST['pid'];

	// get con
	$con = QueryHandler::getDBConnection();

	if(Post::deleteReply($_POST['rid'], $con)) {

		// check parent to see if it's got replies left
		$replies = Post::getRepliesByParentId($pid, $con);

		// delete if no replies are left
		if (count($replies) == 0) {
			
			// check if post is null
			$post = Post::getPostById($pid, $con);

			// 	delete if true
			if ($post->post_text == NULL) {
				// delete success
				if (Post::deletePost($pid, $con)) {
					mysqli_close($con);
					if (isset($_POST['NOJS']))
						header("Location: network.php?id={$_POST['nid']}&dr=true&dp=true");
					else {
						// ajax
						$response['error'] = 0;
						$response['status'] = 'postdelete';
						echo json_encode($response);
					}
				}
				// fail
				else {
					mysqli_close($con);
					if (isset($_POST['NOJS']))
						header("Location: network.php?id={$_POST['nid']}&dr=true&dp=false");
					else {
						// ajax
						$response['error'] = 'Final delete failed';
						echo json_encode($response);
					}

				}
			}
			else {
				mysqli_close($con);
				if (isset($_POST['NOJS']))
					header("Location: network.php?id={$_POST['nid']}&dr=true&dp=false");
				else {
					// ajax
					$response['error'] = 0;
					echo json_encode($response);
				}
			}
		}
		else {
			// close db connection
			mysqli_close($con);
			if (isset($_POST['NOJS']))
				header("Location: network.php?id={$_POST['nid']}&dr=true");
			else {
				// ajax
				$response['error'] = 0;
				echo json_encode($response);
			}
		}
	}
	else {
		if (isset($_POST['NOJS']))
			header("Location: network.php?id={$_POST['nid']}&dr=false");
		else {
			// ajax
			$response['error'] = 'Delete was unsuccessful';
			echo json_encode($response);
		}
	}
	 */
}
// else, nothing to do
else {
	if (isset($_POST['NOJS']))
		header("Location: index.php");
	else {
		// ajax
		$response['error'] = 'Not enough information';
		echo json_encode($response);
	}
}
?>
