<?php
include("data/network.php");

if isset($_GET["initial"])
{
	return searchbarInitData()
}

function searchBarInitData()
{
	return json_encode($data);
}
?>
