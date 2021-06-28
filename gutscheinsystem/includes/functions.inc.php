<?php

function getNewGutscheinNr($mysqli){
	$query = "SELECT MAX(number) AS max FROM `gutscheine_sp`;";
	$query_result = mysqli_query($mysqli, $query);
	
	while($row = mysqli_fetch_assoc($query_result)){
		return $row['max'] + 1;
	}
}

function makeGutschein($mysqli, $number, $euro, $bonnr){	
	$sql = "SELECT * FROM gutscheine_sp WHERE number=?";
	$stmt = mysqli_stmt_init($mysqli);
	if(!mysqli_stmt_prepare($stmt, $sql)){
		header("location: ../index.php?error=stmtfailed");
		exit();
	}
	mysqli_stmt_bind_param($stmt, "i", $number);
	mysqli_stmt_execute($stmt);

	$resultData = mysqli_stmt_get_result($stmt);
	if($row = mysqli_fetch_assoc($resultData)){
		header("location: ../index.php?error=gutscheinnummerblocked");
	}else{
		$date = date("d.m.Y");
		$sql = "INSERT INTO gutscheine_sp (number, wert, wertGes, bonNr, datum) VALUES (?,?,?,?,?);";
		$stmt = mysqli_stmt_init($mysqli);
		if(!mysqli_stmt_prepare($stmt, $sql)){
			header("location: ../index.php?error=stmtfailed");
			exit();
		}
		
		mysqli_stmt_bind_param($stmt, "issss",$number, $euro, $euro, $bonnr, $date);
		mysqli_stmt_execute($stmt);
		
		mysqli_stmt_close($stmt);
		
		header("location: ../index.php?error=gutscheinerstellt&number=$number&wert=$euro");
		exit();
	}
}

function cashGutschein($mysqli, $number, $rechnung, $bonNrCash){
	$sql = "SELECT * FROM gutscheine_sp WHERE number=?";
	$stmt = mysqli_stmt_init($mysqli);
	if(!mysqli_stmt_prepare($stmt, $sql)){
		header("location: ../index.php?error=stmtfailed");
		exit();
	}
	mysqli_stmt_bind_param($stmt, "i", $number);
	mysqli_stmt_execute($stmt);

	$resultData = mysqli_stmt_get_result($stmt);
	
	//Wenn Gutschein Nummer existiert dann:
	if($row = mysqli_fetch_assoc($resultData)){
		$wertAlt = $row['wert'];
		$saldo = $rechnung - $wertAlt;
		$date = date("d.m.Y");
		if($saldo>=0){
			//Gutschein komplett aufgebraucht
			$zero = 0;
			$sql = "UPDATE gutscheine_sp SET wert=?, lastUse=?, lastUseBon=? WHERE number=?;";
			$stmt = mysqli_stmt_init($mysqli);
			if(!mysqli_stmt_prepare($stmt, $sql)){
				header("location: ../index.php?error=stmtfailed");
				exit();
			}
			mysqli_stmt_bind_param($stmt, "issi", $zero, $date, $bonNrCash, $number);
			mysqli_stmt_execute($stmt);
			header("location: ../index.php?error=none&saldo=$saldo&gutscheinerstellen=0&wertAlt=$wertAlt&rechnung=$rechnung");
			exit();
		}else if($saldo < 0){
			//Gutschein hat noch Restwert
			$saldoNew = $saldo*(-1);
			
			$sql = "UPDATE gutscheine_sp SET wert=?, lastUse=?, lastUseBon=? WHERE number=?;";
			$stmt = mysqli_stmt_init($mysqli);
			if(!mysqli_stmt_prepare($stmt, $sql)){
				header("location: ../index.php?error=stmtfailed");
				exit();
			}
			mysqli_stmt_bind_param($stmt, "sssi", $saldoNew, $date, $bonNrCash, $number);
			mysqli_stmt_execute($stmt);
			header("location: ../index.php?error=none&saldo=$saldoNew&number=$number&gutscheinerstellen=1&wertAlt=$wertAlt&rechnung=$rechnung");
			exit();
		}
	}
}

//Ermittelt alle Gutscheine in Datenbank gutscheine_sp
function getGutscheine($mysqli){
	$table="<table><tbody>";
	$sql = "SELECT * FROM gutscheine_sp;";
	$result = $mysqli->query($sql);

	if($result->num_rows > 0){
			$table.="<tr><th class='border-right-silver'>Gutschein-Nummer</th>";
			$table.="<th>Gutschienwert aktuell</th>";
			$table.="<th>Gutscheinwert bei Erstellung</th>";
			$table.="<th>Bon Nummer</th>";
			$table.="<th class='border-right-silver'>Datum der Erstellung</th>";
			$table.="<th>Zuletzt verwendet</th>";
			$table.="<th>Letzte Verwendung Bon</th></tr>";
		while($row = $result->fetch_assoc()) {
				$table.="<tr class='zeile nr".$row["number"]."'><td class='border-right-silver'>".$row["number"]."</td>";
				$table.="<td>".$row["wert"]."€</td>";
				$table.="<td>".$row["wertGes"]."€</td>";
				$table.="<td>".$row["bonNr"]."</td>";
				$table.="<td class='border-right-silver'>".$row["datum"]."</td>";
				$table.="<td>".$row["lastUse"]."</td>";
				$table.="<td>".$row["lastUseBon"]."</td>";
				$table.="</tr>";
		}
		$table.="</tbody></table>";
	}
	return $table;
}