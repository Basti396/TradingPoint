<?php
// Create connection
$mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');
// Check connection
if ($mysqli->connect_error) {
  die("Connection failed: " . $mysqli->connect_error);
}
 ?>
