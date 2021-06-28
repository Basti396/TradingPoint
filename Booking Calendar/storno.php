<?php

include_once 'conn.php';

if(isset($_POST['submit'])){
		$email = mysqli_real_escape_string($mysqli, $_POST['email']);
		
		$stmt = $mysqli->prepare("DELETE FROM bookings_rv WHERE email = ?");
		$stmt->bind_param('s', $email);
		$stmt->execute();
		$stmt->close();
		
		mysqli_close($mysqli);
		header('Location: calendar.php'); //If book.php is your main page where you list your all records
	}

?>
