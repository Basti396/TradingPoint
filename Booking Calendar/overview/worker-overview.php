<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Create connection
$mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');
// Check connection
if ($mysqli->connect_error) {
	die("Connection failed: " . $mysqli->connect_error);
}

	$sql = "SELECT id, date, telefon, timeslot, name, email, street, location,taskOption FROM bookings_rv ORDER BY timeslot, name";
	$result = $mysqli->query($sql);
	$datetoday = date('Y-m-d');
	$kundenCounter = 0;
	$table="";

	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			if($datetoday == $row["date"]){
				$id = $row["id"];
				$name = $row["name"];
				$date = $row["date"];
				$timeslot = $row["timeslot"];
				$email = $row["email"];
				$telefon = $row["telefon"];
				$grund = $row["taskOption"];
				if ($grund == "s"){
					$grund="Shisha-Kauf";
				}else{
					$grund="Tabak-Kauf";
				}
				//Um später (im body) Tabelle mit einzelnen Termin zu kreiren
				//$kundenCounter++;
				$table.= "<tr><td>$id</td>";
				$table.= "<td>$name</td>";
				$table.= "<td>$date</td>";
				$table.= "<td>$timeslot</td>";
				$table.= "<td>$grund</td>";
				$table.= "<td>$email</td>";
				$table.= "<td>$telefon</td>";
				$table.= "<td><a href='../delete.php?id=".$id."'>Termin stornieren/löschen</a></td></tr>";
			}
		}
	}


	$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" contend="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="main.css" media="screen" />
 <meta http-equiv="refresh" content="300" />
</head>

<style>
td,th{
	padding:10px !important;
}
table, th, td {
  border: 1px solid black;
}
table {
  width:100%;
}
</style>

<body>
 <div class="container">
  <div class="row">
   <div class="col-md-12">
<h1><center>Ravensburg</center></h1>
		<table id="changes">
		<tbody>
			<tr>
			  <th>ID</th>
			  <th>Name</th>
			  <th>Datum</th>
			  <th>Zeitraum</th>
			  <th>Grund</th>
			  <th>E-Mail</th>
			  <th>Telefon</th>
			  <th>hat Storniert/ kam nicht
			</tr>
			<?php
				echo $table;
			?>
		</tbody>
	</table>

    </div>
   </div>
  </div>
 </div>
</body>
</html>
