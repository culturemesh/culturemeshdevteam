<?php
if ($_GET['randomId'] != "7VNabcGMrl_fVMe6JoDhrDXWPnE8o8PLNDH8dodtbUCiccaBbzYAtfh0mUJfIhHJ") {
    echo "Access Denied";
    exit();
}

// display the HTML code:
echo stripslashes($_POST['wproPreviewHTML']);

?>  
