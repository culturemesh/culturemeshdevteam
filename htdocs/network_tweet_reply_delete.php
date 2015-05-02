<?php
$response = array(
	'error' => NULL,
	'status' => NULL);

// if tid is set, we can continue
if (isset($_POST['rid']) && isset($_POST['nid']) && isset($_POST['tid'])) {

	include('environment.php');
	$cm = new \Environment();

	$reply = new \dobj\TweetReply();
	$reply->id = (int) $_POST['rid'];


	$dal = new \dal\DAL($cm->getConnection());
	$dal->loadFiles();
	$do2db = new \dal\Do2Db();

	$network = \dobj\Network::createFromId((int) $_POST['nid'], $dal, $do2db);
	$success = $reply->delete($dal, $do2db);

	if ($success) {

		$tweet = new \dobj\Tweet();
		$tweet->id = (int) $_POST['tid'];
		$tweet->getReplies($dal, $do2db);

		if(count($tweet->replies) == 0) {

			$tweet = \dobj\Tweet::createFromId((int) $_POST['tid'], $dal, $do2db);
			$success = $tweet->delete($dal, $do2db);
			$network->decrementTweetCount($dal, $do2db);

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
