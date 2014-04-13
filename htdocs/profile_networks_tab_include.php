<?php
	//include_once 'data/dal_network.php';
	//include_once 'html_builder.php';
?>
<div>
	<h5>Your Networks</h5>
	<?php
	$networks = NetworkRegistration::getNetworksByUserId($_SESSION['uid'], $con);
	foreach($networks as $network)
	{
		HTMLBuilder::displayDashNetwork($network);
	}
	?>
</div>
