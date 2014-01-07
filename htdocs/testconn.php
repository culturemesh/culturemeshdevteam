<?php
$mysqli = new mysqli("69.195.79.120", "culturp7", "GoRoop2013!","culturp7_ktc");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
echo $mysqli->host_info . "\n";
?>

