<?php

$id = $_GET['id'];

include_once 'conn.php';

// sql template to delete a record where ? is a placeholder
$sql = "DELETE FROM bookings_rv WHERE id = ?;";

//Create prepared statement
$stmt = mysqli_stmt_init($mysqli);

//Prepare the prepared statement
if(!mysqli_stmt_prepare($stmt, $sql)){
	echo "sql statement failed";
}else{
	//Bind parameters to the placeholder "?"
	mysqli_stmt_bind_param($stmt, "i", $id);
	//Run parameters inside database
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);

	mysqli_close($mysqli);
    header('Location: overview/worker-overview.php'); //If book.php is your main page where you list your all records
}

?>
