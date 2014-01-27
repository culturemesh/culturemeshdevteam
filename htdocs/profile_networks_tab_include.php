<?php
	include_once 'data/dal_network.php';
	include_once 'html_builder.php';
?>
<div>
	<h3>YOUR NETWORKS</h3>
	<?php
	$networks = NetworkRegistration::getNetworksByUserId($_SESSION['uid']);
	foreach($networks as $network)
	{
		HTMLBuilder::displayNetwork($network);
	}
	?>
</div>
