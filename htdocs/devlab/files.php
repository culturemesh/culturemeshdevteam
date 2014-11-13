<?php

echo json_encode(array(
	'success' => true,
	'file-count' => count($_FILES['fileupload']['name']),
));
?>
