<?php
	ini_set("display_errors", true);
	//var_dump($HTTP_RAW_POST_DATA);
	if (count($_POST) < 1)
	{
		// use HTTP_RAW_POST_DATA
		$data = json_decode($HTTP_RAW_POST_DATA, true);
		var_dump($data);
		echo $data['key'];
	}
	else
	{
		echo var_dump($_POST);
	}
/*
	if (isset($_POST['stuff']))
		echo json_encode(array('response' => $_POST['stuff']));
	else
		echo json_encode(array('response' => 'no data'));
 */
?>
