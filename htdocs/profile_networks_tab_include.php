<?php
	//include_once 'data/dal_network.php';
	//include_once 'html_builder.php';
?>
<div>
	<h5>Your Networks</h5>
	<?php
	foreach($yn_networks as $network)
	{
		HTMLBuilder::displayDashNetwork($network);
	}
	?>
</div>
