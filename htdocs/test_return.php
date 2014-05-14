<?php
	if (isset($_POST['stuff']))
		echo json_encode(array('response' => $_POST['stuff']));
	else
		echo json_encode(array('response' => 'no data'));
?>
