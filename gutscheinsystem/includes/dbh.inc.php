<?php
$serverName = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "gutscheine";

// Create connection
$mysqli = new mysqli($serverName, $dbUsername, $dbPassword, $dbName);
// Check connection
if ($mysqli->connect_error) {
  die("Connection failed: " . $mysqli->connect_error);
}

